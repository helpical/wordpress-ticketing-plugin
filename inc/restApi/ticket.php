<?php

class TicketsRest
{
    private $namespace;
    public function __construct()
    {
        $this->namespace = 'helpical';
    }

    public function register()
    {
        register_rest_route(
            $this->namespace,
            '/getTickets',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'getTickets'],
                    'permission_callback' => [$this, 'ticketPermision'],
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/getTicket',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this, 'getTicket'],
                    'permission_callback' => [$this, 'ticketPermision'],
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/newTicket',
            [
                [
                    'methods'             => 'POST',
                    'callback'            => [$this, 'newTicket'],
                    'permission_callback' => [$this, 'ticketPermision'],
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/replyTicket',
            [
                [
                    'methods'             => 'POST',
                    'callback'            => [$this, 'replyTicket'],
                    'permission_callback' => [$this, 'ticketPermision'],
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/closeTicket',
            [
                [
                    'methods'             => 'PUT',
                    'callback'            => [$this, 'closeTicket'],
                    'permission_callback' => [$this, 'ticketPermision'],
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/satisfactionTicket',
            [
                [
                    'methods'             => 'PUT',
                    'callback'            => [$this, 'satisfactionTicket'],
                    'permission_callback' => [$this, 'ticketPermision']
                ]
            ]
        );
    }

    public function ticketPermision($request)
    {
        if (isset($request['nonce']) && wp_verify_nonce($request['nonce'], '_helpical_tickets'))
            return true;
        return new WP_REST_Response(['status' => 'No No No!', 'msg' => 'Idiot Hacker']);
    }

    public function getTickets($request)
    {
        $user_id = helpical_get_user();
        if (!$user_id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('You should sign into your account at first', 'helpical')]);
        $helpical_api = helpical_get_api();
        $response = wp_remote_get($helpical_api['api_url'] . '/api/v1/ticket/customer-tickets/' . $user_id, [
            'headers' => [
                'X-Api-Key'     => $helpical_api['api_key'],
                'content-type'  => 'application/json'
            ],
            'body'  => [
                'order_type'  => 'desc'
            ]
        ]);
        if (is_wp_error($response))
            return new WP_REST_Response(['status' => 'error', 'msg' => __("Sorry, there's a problem in ticketing system. please contact admin", 'helpical')]);
        $tickets = [];
        $ids = [];
        if ($response['response']['code'] == 200) {
            $ids = array_column(json_decode($response['body'], true)['returned_values'], 'ticket_id');
            $tickets = json_decode($response['body'])->returned_values;
        }
        set_query_var('tickets', $tickets);
        set_query_var('user_id', $user_id);
        ob_start();
        require_once(HELPICAL_DIR . '/inc/templates/tickets.php');
        return new WP_REST_Response(['status' => 'success', 'data' => ob_get_clean(), 'ids' => $ids], 200);
    }

    public function getTicket($request)
    {
        $id = intval($request['id']);
        if (!$id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Please fill all required fields', 'helpical')], 200);
        $user_id = helpical_get_user();
        if (!$user_id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('You should sign into your account at first', 'helpical')]);
        $helpical_api = helpical_get_api();
        $response = wp_remote_get($helpical_api['api_url'] . '/api/v1/ticket/customer-ticket/' . $id . '/' . $user_id, [
            'headers' => [
                'X-Api-Key'     => $helpical_api['api_key'],
                'content-type'  => 'application/json'
            ],
            'body'  => [
                'order_type'    => 'desc'
            ]
        ]);
        if (is_wp_error($response))
            return new WP_REST_Response(['status' => 'error', 'msg' => __("Sorry, there's a problem in ticketing system. please contact admin", 'helpical')]);
        $ticket = '';
        if ($response['response']['code'] == 200)
            $ticket = json_decode($response['body'])->returned_values[0];
        set_query_var('ticket', $ticket);
        ob_start();
        require_once(HELPICAL_DIR . '/inc/templates/ticket.php');
        return new WP_REST_Response(['status' => 'success', 'data' => ob_get_clean()], 200);
    }

    public function newTicket($request)
    {
        $ticket_cat = intval($request['ticket_cat']);
        $target_department_id = intval($request['target_department_id']);
        $importance = strip_tags($request['importance']);
        $subject = sanitize_text_field($request['subject']);
        $message = sanitize_textarea_field($request['message']);
        if (!$ticket_cat || !$target_department_id || !$importance || !$subject || !$message)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Please fill all required fields', 'helpical')], 200);
        $user_id = helpical_get_user();
        if (!$user_id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('You should sign into your account at first', 'helpical')]);
        $helpical_api = helpical_get_api();
        $count = 0;
        if (isset($_FILES['attachments'])) {
            $count = count($_FILES['attachments']['name']);
            $base_info = helpical_get_base();
            if ($count > 0) {
                $error = $this->validFiles($_FILES['attazchments'], $count, $base_info);
                if ($error == 1)
                    return new WP_REST_Response(['status' => 'attachment_error', 'msg' => sprintf(__('Allowed formats for attachment files are %s', 'helpical'), $base_info['allowed_attachment_formats_srting'])], 200);
                else if ($error == 0)
                    return new WP_REST_Response(['status' => 'attachment_error', 'msg' => sprintf(__('Maximum size for each attachment is %s', 'helpical'), $base_info['allowed_attachment_file_size'])], 200);
            }
        }
        $response = wp_remote_post($helpical_api['api_url'] . '/api/v1/ticket/customer-new/', [
            'headers' => [
                'X-Api-Key'     => $helpical_api['api_key'],
                'content-type'  => 'application/json'
            ],
            'body'  => json_encode([
                'customer_id'           => $user_id,
                'ticket_cat'            => $ticket_cat,
                'target_department_id'  => $target_department_id,
                'importance'            => $importance,
                'subject'               => $subject,
                'message'               => $message
            ])
        ]);
        if (is_wp_error($response))
            return new WP_REST_Response(['status' => 'error', 'msg' => __("Sorry, there's a problem in ticketing system. please contact admin", 'helpical')]);
        else if ($response['response']['code'] != 201)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Operation failed', 'helpical')], 200);
        $ticket_id = json_decode($response['body'])->returned_values[0]->ticket_id;
        $content_id = json_decode($response['body'])->returned_values[0]->content_id;
        if ($count > 0)
            return $this->sendAttachment($helpical_api, $count, $content_id, $ticket_id);
        return new WP_REST_Response(['status' => 'success', 'data' => ['ticket_id' => $ticket_id]], 200);
    }

    public function replyTicket($request)
    {
        $ticket_id = intval($request['ticket_id']);
        $message = sanitize_textarea_field($request['message']);
        if (!$ticket_id || !$message)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Please fill all required fields', 'helpical')], 200);
        $user_id = helpical_get_user();
        if (!$user_id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('You should sign into your account at first', 'helpical')]);
        $helpical_api = helpical_get_api();
        $count = 0;
        if (isset($_FILES['attachments'])) {
            $count = count($_FILES['attachments']['name']);
            $base_info = helpical_get_base();
            if ($count > 0) {
                $error = $this->validFiles($_FILES['attachments'], $count, $base_info);
                if ($error == 1)
                    return new WP_REST_Response(['status' => 'attachment_error', 'msg' => sprintf(__('Allowed formats for attachment files are %s', 'helpical'), $base_info['allowed_attachment_formats_srting'])], 200);
                else if ($error == 0)
                    return new WP_REST_Response(['status' => 'attachment_error', 'msg' => sprintf(__('Maximum size for each attachment is %s', 'helpical'), $base_info['allowed_attachment_file_size'])], 200);
            }
        }
        $response = wp_remote_request($helpical_api['api_url'] . '/api/v1/ticket/customer-reply/', [
            'method'            => "PUT",
            'headers' => [
                'X-Api-Key'     => $helpical_api['api_key'],
                'content-type'  => 'application/json'
            ],
            'body'  => json_encode([
                'ticket_id'             => $ticket_id,
                'customer_id'           => $user_id,
                'message'               => $message,
            ])
        ]);
        if (is_wp_error($response))
            return new WP_REST_Response(['status' => 'error', 'msg' => __("Sorry, there's a problem in ticketing system. please contact admin", 'helpical')]);
        else if ($response['response']['code'] != 202) {
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Operation failed', 'helpical')], 200);
        }
        $content_id = json_decode($response['body'])->returned_values[0]->content_id;
        if ($count > 0)
            return $this->sendAttachment($helpical_api, $count, $content_id, $ticket_id);
        return new WP_REST_Response(['status' => 'success', 'data' => ['ticket_id' => $ticket_id]], 200);
    }

    public function closeTicket($request)
    {
        $ticket_id = intval($request['ticket_id']);
        if (!$ticket_id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Please fill all required fields', 'helpical')], 200);
        $user_id = helpical_get_user();
        if (!$user_id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('You should sign into your account at first', 'helpical')]);
        $helpical_api = helpical_get_api();
        $response = wp_remote_request($helpical_api['api_url'] . '/api/v1/ticket/customer-close/', [
            'method'    => 'PUT',
            'headers'   => [
                'X-Api-Key'     => $helpical_api['api_key'],
                'content-type'  => 'application/json'
            ],
            'body'      => json_encode([
                'ticket_id'         => $ticket_id,
                'customer_id'       => $user_id
            ])
        ]);
        if (is_wp_error($response))
            return new WP_REST_Response(['status' => 'error', 'msg' => __("Sorry, there's a problem in ticketing system. please contact admin", 'helpical')]);
        else if ($response['response']['code'] != 202) {
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Operation failed', 'helpical')], 200);
        }
        return new WP_REST_Response(['status' => 'success', 'data' => ['ticket_id' => $ticket_id]], 200);
    }

    public function satisfactionTicket($request)
    {
        $ticket_id = intval($request['ticket_id']);
        $satisfaction = intval($request['satisfaction']);
        if (!$ticket_id || !$satisfaction)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Please fill all required fields', 'helpical')], 200);
        $user_id = helpical_get_user();
        if (!$user_id)
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('You should sign into your account at first', 'helpical')]);
        $helpical_api = helpical_get_api();
        $response = wp_remote_request($helpical_api['api_url'] . '/api/v1/ticket/satisfaction/', [
            'method'    => 'PUT',
            'headers'   => [
                'X-Api-Key'     => $helpical_api['api_key'],
                'content-type'  => 'application/json'
            ],
            'body'      => json_encode([
                'id'                => $ticket_id,
                'customer_id'       => $user_id,
                'rate'              => $satisfaction
            ])
        ]);
        if (is_wp_error($response))
            return new WP_REST_Response(['status' => 'error', 'msg' => __("Sorry, there's a problem in ticketing system. please contact admin", 'helpical')]);
        else if ($response['response']['code'] != 202) {
            return new WP_REST_Response(['status' => 'failed', 'msg' => __('Operation failed', 'helpical')], 200);
        }
        return new WP_REST_Response(['status' => 'success', 'data' => ['msg' => helpical_satisfactio_text($satisfaction)]], 200);
    }

    protected function sendAttachment($helpical_api, $count, $content_id, $ticket_id)
    {
        $ch = curl_init();
        $postfields = [
            'content_id'    => $content_id
        ];
        for ($i = 0; $i < $count; $i++) {
            $postfields['attachment[' . $i . ']'] = new \CurlFile($_FILES['attachments']['tmp_name'][$i], $_FILES['attachments']['type'][$i], $_FILES['attachments']['name'][$i]);
        }
        $options = [
            CURLOPT_URL => $helpical_api['api_url'] . '/api/v1/ticket/attachment/',
            CURLOPT_HEADER => true,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => [
                "Content-Type:multipart/form-data",
                'X-Api-Key:' . $helpical_api['api_key']
            ],
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_RETURNTRANSFER => true
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);
        curl_close($ch);
        return (!$res && curl_getinfo($ch, CURLINFO_HTTP_CODE) == 201) ? new WP_REST_Response(['status' => 'failed', 'msg' => __('Operation failed', 'helpical')], 200) : new WP_REST_Response(['status' => 'success', 'data' => ['ticket_id' => $ticket_id]], 200);
    }

    protected function validFiles($attachments, $count, $base_info)
    {
        for ($i = 0; $i < $count; $i++) {
            if (!array_search($attachments['type'][$i], $base_info['allowed_attachment_formats_array']))
                return 0;
            if ($attachments['size'][$i] > intval($base_info['allowed_attachment_file_size_real']))
                return 1;
        }
        return 2;
    }
}
