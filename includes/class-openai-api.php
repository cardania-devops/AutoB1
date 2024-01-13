<?php
class OpenAI_API {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function send_request($data) {
        $api_url = 'https://api.openai.com/v1/chat/completions'; // Chat completions endpoint for GPT-4

        $args = array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body' => json_encode(array(
                'model' => 'gpt-4-1106-preview', // GPT-4 model
                'messages' => $data['messages'], // Ensure 'messages' is passed correctly
                'max_tokens' => 800
            )),
            'method' => 'POST',
            'data_format' => 'body',
            'timeout' => 90 // Increased timeout duration
        );

        $response = wp_remote_post($api_url, $args);
        
        if (is_wp_error($response)) {
            error_log('OpenAI API Request Error: ' . $response->get_error_message());
            return array('error' => $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded_body = json_decode($body, true);

        if ($response_code !== 200 || isset($decoded_body['error'])) {
            error_log('OpenAI API Response Error: ' . $body);
            return array('error' => 'API error or invalid response');
        }

        return $decoded_body;
    }
}
