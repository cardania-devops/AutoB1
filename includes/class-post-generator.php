<?php
/**
 * Class for handling post generation using OpenAI.
 */

class Post_Generator {
    private $openai_api;

    public function __construct($openai_api) {
        $this->openai_api = $openai_api;
    }

    public function generate_and_publish_post() {
        $options = get_option('ai_autoblogger_options');
        $promptData = $this->create_prompt($options);
        $response = $this->openai_api->send_request($promptData);

        if (isset($response['error']) || empty($response['choices'][0]['text'])) {
            error_log('OpenAI API error or empty response');
            return false;
        }

        // Parse the response
        $responseText = $response['choices'][0]['text'];
        $parts = explode("Recommended Categories:", $responseText);
        $content = trim($parts[0]); // Blog post content

        // Extract the title from the content
        $titleEnd = strpos($content, "\n");
        $post_title = substr($content, 0, $titleEnd);
        $post_title = trim(str_replace('Title:', '', $post_title)); // Remove 'Title:' if present

        // Extract the post body without the title
        $post_content = substr($content, $titleEnd);

        $categories = $tags = [];
        if (isset($parts[1])) {
            $categoriesAndTags = explode("Recommended Tags:", $parts[1]);
            $categoriesList = isset($categoriesAndTags[0]) ? trim($categoriesAndTags[0]) : '';
            $tagsList = isset($categoriesAndTags[1]) ? trim($categoriesAndTags[1]) : '';
        
            $categories = $categoriesList ? array_map('trim', explode(",", $categoriesList)) : [];
            $tags = $tagsList ? array_map('trim', explode(",", $tagsList)) : [];
        }

        $this->create_wordpress_post($post_content, $post_title, $options, $categories, $tags);
        return true;
    }

    private function create_prompt($options) {
        $topic = $options['tags'] ?? 'general';
        $tone = $options['content_tone'] ?? 'neutral';

        $messages = [];
        $messages[] = ['role' => 'system', 'content' => 'You are an AI assistant tasked with writing an engaging, effective blog post.'];
        $messages[] = ['role' => 'user', 'content' => "Write an engaging blog post with a succinct title about {$topic} in a {$tone} tone. Include relevant external URLs for further reading. At the very end, include a succinct list of categories and tags which are relevant to the post."];
    
        return ['messages' => $messages];
    }

    private function create_wordpress_post($content, $title, $options, $categories, $tags) {
        $post_data = [
            'post_title'    => $title ?: 'Auto Generated Post',
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => get_current_user_id(),
            'post_category' => $this->convert_categories_to_ids($categories),
            'tags_input'    => $tags,
        ];

        $result = wp_insert_post($post_data);
        if (is_wp_error($result)) {
            error_log('Error creating post: ' . $result->get_error_message());
        }
    }

    private function convert_categories_to_ids($categories) {
        $category_ids = [];
    
        foreach ($categories as $category_name) {
            $category_slug = sanitize_title($category_name);
            $category_obj = get_category_by_slug($category_slug);
            if (!$category_obj) {
                $category_id = wp_create_category($category_name);
            } else {
                $category_id = $category_obj->term_id;
            }
    
            $category_ids[] = $category_id;
        }
    
        return $category_ids;
    }
}
