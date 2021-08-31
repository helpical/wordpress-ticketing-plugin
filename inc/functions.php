<?php
if (get_option('helpical_is_active', 'false') != 'false') {
    add_action('rest_api_init', function () {
        $ticketApi = new TicketsRest();
        $ticketApi->register();
    });

    add_action('wp_enqueue_scripts', function () {
        wp_register_style('helpical-css', plugins_url('assets/css/style.css', HELPICAL_FILE));
        wp_register_script('helpical-js', plugins_url('assets/js/script.js', HELPICAL_FILE));
        $base = helpical_get_base();
        wp_localize_script('helpical-js', 'helpical', ['url' => get_rest_url(null, 'helpical'), 'wpnonce' => wp_create_nonce('wp_rest'), 'helpicalnonce' => wp_create_nonce('_helpical_tickets'), 'attachmentsFormat' => array_values($base['allowed_attachment_formats_array']), 'attachmentsSize' => $base['allowed_attachment_file_size_real']]);
        $color = get_option('helpical_settings_shortcode_color', '#0a87cf');
        wp_add_inline_style('helpical-css', ".--helpical{ --primary: $color; }");
    });

    add_action('init', function () {
        add_shortcode('helpical', function ($attr) {
            $container = (isset($attr['container']) && $attr['container'] == 'true') ? 'container' : 'container-fluid';
            $res = '<div class="--helpical"><div class="--helpical-' . $container . '">';
            $is_active = get_option('helpical_is_active', 'false');
            if (get_option('helpical_settings_realtime') == true)
                $is_active = helpical_refresh();
            wp_enqueue_style('helpical-css');
            wp_enqueue_script('helpical-js');
            if ($is_active === 'false') {
                $res .= '<div class="--helpical-alert --helpical-alert-danger --helpical-problem" role="alert">' . __("Sorry, there's a problem in ticketing system. please contact admin", 'helpical') . '</div>';
            } else {
                $res .= '<div class="helpical-loading"><div class="lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>';
                ob_start();
                require_once HELPICAL_DIR . '/inc/templates/shortcode.php';
                $res .= ob_get_clean();
            }
            $res .= '</div></div>';
            return $res;
        });
    });
}

function helpical_refresh()
{
    $helpical_api = helpical_get_api();
    if (!$helpical_api) {
        update_option('helpical_is_active', 'false');
        return 'false';
    }
    $base = wp_remote_get($helpical_api['api_url'] . '/api/v1/base-info', [
        'headers' => [
            'X-Api-Key'     => $helpical_api['api_key'],
            'content-type'  => 'application/json'
        ]
    ]);
    if (is_wp_error($base) || $base['response']['code'] != 200) {
        update_option('helpical_is_active', 'false');
        return 'false';
    }
    $base = json_decode($base['body']);
    $allowed_attachment_formats = $base->returned_values->allowed_attachment_formats;
    $allowed_attachment_file_size = $base->returned_values->allowed_attachment_file_size;
    $username_regex = $base->returned_values->username_regex;
    update_option('allowed_attachment_formats', explode(", ", $allowed_attachment_formats));
    update_option('allowed_attachment_file_size', $allowed_attachment_file_size);
    update_option('username_regex', $username_regex);

    $departments = wp_remote_get($helpical_api['api_url'] . '/api/v1/departments', [
        'headers' => [
            'X-Api-Key'     => $helpical_api['api_key'],
            'content-type'  => 'application/json'
        ]
    ]);
    if (is_wp_error($departments) || $departments['response']['code'] != 200) {
        update_option('helpical_is_active', 'false');
        return 'false';
    }
    $categories = wp_remote_get($helpical_api['api_url'] . '/api/v1/ticket/cats/', [
        'headers' => [
            'X-Api-Key'     => $helpical_api['api_key'],
            'content-type'  => 'application/json'
        ]
    ]);
    if (is_wp_error($categories) || $categories['response']['code'] != 200) {
        update_option('helpical_is_active', 'false');
        return 'false';
    }
    $departments = json_decode($departments['body'])->returned_values;
    $categories = json_decode($categories['body'])->returned_values;
    $array = [];
    if (!is_array($departments) || empty($departments)) {
        update_option('helpical_is_active', 'false');
        return 'false';
    }
    foreach ($departments as $department) {
        $cate = [];
        if ($department->disable != '1' && $department->customer_visible != 0) {
            foreach ($categories as $key => $category) {
                if ($category->department_id == $department->id && $category->disable == '0') {
                    array_push($cate, ['id' => $category->id, 'title' => $category->title]);
                    unset($categories[$key]);
                }
            }
            array_push($array, ['id' => $department->id, 'title' => $department->title, 'categories' => $cate]);
        }
    }
    update_option('helpical_categories', $array);
    update_option('helpical_is_active', 'true');
    return 'true';
}

function helpical_get_api()
{
    $api_key = esc_attr(get_option('helpical_settings_tools_api_key', ''));
    $api_url = esc_url_raw(get_option('helpical_settings_tools_api_url', ''));
    if (empty($api_key) || empty($api_url))
        return false;
    return compact('api_key', 'api_url');
}

function helpical_get_base($seperator = ', ')
{
    $allowed_attachment_formats = get_option('allowed_attachment_formats', '');
    $allowed_attachment_formats_srting = implode($seperator, $allowed_attachment_formats);
    $allowed_attachment_formats_array = helpical_get_formats($allowed_attachment_formats);
    $allowed_attachment_file_size = get_option('allowed_attachment_file_size', '');
    $username_regex = get_option('username_regex', '');
    $allowed_attachment_file_size_real = helpical_get_real_size($allowed_attachment_file_size);
    if (empty($allowed_attachment_formats || empty($allowed_attachment_file_size)))
        return false;
    return compact('allowed_attachment_formats_srting', 'allowed_attachment_formats_array', 'allowed_attachment_file_size', 'allowed_attachment_file_size_real', 'username_regex');
}

function helpical_get_formats($formats)
{
    $mimeTypes = [
        'jpg'   => 'image/jpeg',
        'gif'   => 'image/gif',
        'png'   => 'image/png',
        'pdf'   => 'application/pdf',
        'zip'   => 'application/zip',
        'rar'   => 'application/vnd.rar',
        'doc'   => 'application/msword',
        'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls'   => 'application/vnd.ms-excel',
        'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
    $res = [];
    foreach ($formats as $format)
        if (array_key_exists($format, $mimeTypes))
            $res[$format] = $mimeTypes[$format];
    return $res;
}

function helpical_get_user()
{
    $helpical_api = helpical_get_api();
    $user_id = get_current_user_id();
    if (!$user_id || !$helpical_api)
        return false;
    $user_id2 = get_user_meta($user_id, 'helpical_user_id', true);
    if ($user_id2 == '') {
        return helpical_create_user($helpical_api, $user_id);
    } else {
        $response = wp_remote_get($helpical_api['api_url'] . '/api/v1/customer/' . $user_id2, [
            'headers' => [
                'X-Api-Key'     => $helpical_api['api_key'],
                'content-type'  => 'application/json'
            ],
        ]);
        if (is_wp_error($response))
            return false;
        if ($response['response']['code'] == 200)
            return $user_id2;
        else if ($response['response']['code'] == 404) {
            return helpical_create_user($helpical_api, $user_id);
        }
        return false;
    }
}

function helpical_create_user($helpical_api, $user_id)
{
    $user = wp_get_current_user();
    $username_regex = get_option('username_regex', '');
    $username = ($user->user_login) ? $user->user_login : false;
    if (!$username && !empty($username_regex) && !preg_match($username_regex, $username))
        $username = false;
    $email = ($user->user_email && is_email($user->user_email)) ? $user->user_email : false;
    if (!$username && !$email)
        return false;
    $body = [
        'fname'     => ($user->first_name) ? $user->first_name : 'undefined',
        'lname'     => ($user->last_name) ? $user->last_name : 'undefined',
        'org_id'    => 1,
        'password'  => '123456'
    ];
    if ($email != false)
        $body['email'] = $email;
    if ($username != false)
        $body['username'] = $username;
    $response = wp_remote_post($helpical_api['api_url'] . '/api/v1/customer/', [
        'headers' => [
            'X-Api-Key'     => $helpical_api['api_key'],
            'content-type'  => 'application/json'
        ],
        'body'  => json_encode($body)
    ]);
    if (is_wp_error($response))
        return false;
    if ($response['response']['code'] != 201)
        return false;
    $response = json_decode($response['body']);
    $user_id2 = $response->returned_values[0]->id;
    update_user_meta($user_id, 'helpical_user_id', $user_id2);
    return $user_id2;
}

function getStatus($status)
{
    switch ($status) {
        case 'o':
            $res = __('Open', 'helpical');
            break;
        case 'p':
            $res = __('Pending', 'helpical');
            break;
        case 'd':
            $res = __('Attendee answered', 'helpical');
            break;
        case 'a':
            $res = __('Owner answered', 'helpical');
            break;
        default:
            $res = __('Closed', 'helpical');
    }
    return $res;
}

function helpical_get_real_size($size)
{
    if (strpos($size, 'KB') > 0)
        return intval(str_replace('KB', '', $size)) * 1024;
    return intval(str_replace('MB', '', $size)) * 1048576;
}

function helpical_satisfactio_text($satisfaction)
{
    switch ($satisfaction) {
        case 0:
            return __('Please help us to imporove our service quality by telling us your satisfaction level.', 'helpical');
            break;
        case 1:
            return __('We will try to enhance your satisfaction in the next tickets.', 'helpical');
            break;
        case 2:
            return __('We are so sorry because we could not enhance your satisfaction.', 'helpical');
            break;
        default:
            return __('We are happy because we could enhance your satisfaction.', 'helpical');
    }
}

add_action('plugins_loaded', function () {
    if (is_admin())
        load_textdomain('helpical', HELPICAL_DIR . '/languages/' . determine_locale() . '.mo');
    else
        load_textdomain('helpical', HELPICAL_DIR . '/languages/fa_IR.mo');
}, 5);
