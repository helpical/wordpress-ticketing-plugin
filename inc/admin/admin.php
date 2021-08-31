<?php
add_filter("plugin_action_links_" . HELPICAL_BASENAME, function ($links) {
    $links[] = '<a href="admin.php?page=helpical_settings">' . __('Settings', 'helpical') . '</a>';
    return $links;
});

add_action('activated_plugin', function ($plugin) {
    if ($plugin == HELPICAL_BASENAME)
        exit(wp_redirect(admin_url('admin.php?page=helpical_settings')));
});

add_action('admin_menu', function () {
    add_menu_page(__('Helpical', 'helpical'), __('Helpical', 'helpical'), 'manage_options', 'helpical_settings', 'heplical_settings_callback', plugins_url('assets/images/logo-menu.png', HELPICAL_FILE), 81);
}, 11);

add_action('admin_init', function () {
    add_settings_section('helpical_settings_tools', __('Helpical settings', 'helpical'), '', 'helpical_settings');
    add_settings_section('helpical_settings_shortcode', __('Shortcode settings', 'helpical'), '', 'helpical_settings');

    register_setting('helpical_settings', 'helpical_settings_tools_api_key', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'show_in_rest'      => true
    ]);
    register_setting('helpical_settings', 'helpical_settings_tools_api_url', [
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'show_in_rest'      => true
    ]);
    register_setting('helpical_settings', 'helpical_settings_shortcode_color', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_hex_color',
        'show_in_rest'      => true
    ]);
    register_setting('helpical_settings', 'helpical_settings_realtime', [
        'type'              => 'boolean',
        'sanitize_callback' => function ($input) {
            if ($input)
                return true;
            return false;
        },
        'show_in_rest'      => true
    ]);

    add_settings_field('helpical_settings_tools_api_key', __('API secret key', 'helpical'), 'helpical_settings_tools_api_key_callback', 'helpical_settings', 'helpical_settings_tools');
    add_settings_field('helpical_settings_tools_api_url', __('API URL', 'helpical'), 'helpical_settings_tools_api_url_callback', 'helpical_settings', 'helpical_settings_tools');
    add_settings_field('helpical_settings_shortcode_color', __('Color', 'helpical'), 'helpical_settings_shortcode_color_callback', 'helpical_settings', 'helpical_settings_shortcode');
    add_settings_field('helpical_settings_realtime', __('Real-time configuration synchronization', 'helpical'), 'helpical_settings_realtime_callback', 'helpical_settings', 'helpical_settings_shortcode');
});

function heplical_settings_callback()
{
    $helpical_api = helpical_get_api();
    if ($helpical_api != false) {
        $is_active = helpical_refresh();
        if ($is_active == 'false') {
            $message = '<div class="notice notice-error"><p>' . __('There are some problems on your helpical system please check the parameters or contact helpical support', 'helpical') . '</p></div>';
        } else {
            $message = '<div class="notice notice-success"><p>' . sprintf(__('To use ticketing Helpical plugin, you should put %s shortcode where ever you want', 'helpical'), '<span style="color: red">[helpical container=true(false)]</span>') . '</p></div>';
        }
    }
    $plugin_version = get_plugin_data(HELPICAL_FILE)['Version'];
    $base_version = wp_remote_get('http://plugin.helpical.com/json.php', [
        'headers' => [
            'content-type'  => 'application/json'
        ]
    ]);
    if (!is_wp_error($base_version) || $base_version['response']['code'] == 200)
        $base_version = isset(json_decode($base_version['body'])->wordpress) ? json_decode($base_version['body'])->wordpress : '1.0.0';
    else
        $base_version = '1.0.0';
    echo '<div style="margin: 10px 20px 0 2px;">
    <div style="display:flex; margin-left:-8px">
    <img src="' . plugins_url('assets/images/logo.png', HELPICAL_FILE) . '" width="48" height="48">
    <h1>';
    _e('Helpical Ticketing System Wordpress Plugin', 'helpical');
    echo ' (v' . $plugin_version . ')' . '</h1></div>';
    settings_errors();
    echo $message;
    if (version_compare($plugin_version, $base_version) == -1)
        echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf(__("A new version of helpical plugin has been released. Find the latest version %s here %s", 'helpical'), '<a href="https://plugin.helpical.com">', '</a>') . '</p></div>';
    if (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']))
        echo '<div class="notice notice-error is-dismissible"><p>' . __("Your current communication with Helpical API is unsecure as there is not any active SSL on your Wordpress website.", 'helpical') . '</p></div>';
    if (isset($_GET['updated'])) {
        remove_query_arg('updated');
        echo '<div class="notice notice-success is-dismissible"><p>' . __("Settings updated", 'helpical') . '</p></div>';
    }
    echo '<form method="POST" action="options.php">';
    settings_fields('helpical_settings');
    do_settings_sections('helpical_settings');
    echo '<div style="display:flex">
        <input type="submit" name="submit" id="submit" value="' . __('Save changes', 'helpical') . '" class="button button-primary">
        <a href="' . add_query_arg('updated', '1') . '" class="button button-secondary" style="margin: 0 10px">' . __('Update settings', 'helpical') . '</a>
    </div>';
    echo ' </form></div>';
}

function helpical_settings_tools_api_key_callback()
{
    echo '<input type="text" class="regular-text" name="helpical_settings_tools_api_key" value="' .  esc_attr(get_option('helpical_settings_tools_api_key')) . '">';
    echo '<p class="description">' . __('Your generated API secret key', 'helpical') . '</p>';
}

function helpical_settings_tools_api_url_callback()
{
    echo '<input type="url" class="regular-text" name="helpical_settings_tools_api_url" value="' . esc_attr(get_option('helpical_settings_tools_api_url')) . '">';
    echo '<p class="description">' . __('Full URL of your ticketing system', 'helpical') . '</p>';
}

function helpical_settings_shortcode_color_callback()
{
    echo '<input type="color" class="" name="helpical_settings_shortcode_color" value="' . esc_attr(get_option('helpical_settings_shortcode_color')) . '">';
    echo '<p class="description">' . __('Main color of your theme', 'helpical') . '</p>';
}

function helpical_settings_realtime_callback()
{
    $realtime = esc_attr(get_option('helpical_settings_realtime', false));
    echo '<input type="checkbox" class="" name="helpical_settings_realtime" value="1"';
    echo (!$realtime) ? '' : ' checked';
    echo '>';
    echo '<p class="description">' . __('Real-time synchronization make your Wordpress website synced with Helpical Ticketing System configurations. This option needs more requests to Helpical API which burns your rate limits, and may decrease the time for operation as they will require real-time configuration data', 'helpical') . '</p>';
    if (!$realtime)
        echo '<p class="description" style="color: green">* ' . __('Click update button to sync configurations as what is defined in Helpical', 'helpical') . '</p>';
}
