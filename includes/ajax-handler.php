<?php
// Handle AJAX requests here
add_action('wp_ajax_generate_post', 'handle_generate_post_ajax');

function handle_generate_post_ajax() {
    check_ajax_referer('ai_autoblogger_generate_post');

    $openai_api = new OpenAI_API(); // Ensure this is correctly instantiated
    $post_generator = new Post_Generator($openai_api);
    $result = $post_generator->generate_and_publish_post();

    if ($result === false) {
        wp_send_json_error('Failed to generate post');
    } else {
        wp_send_json_success('Post generated successfully');
    }

    wp_die(); // Terminate AJAX request
}
