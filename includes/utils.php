<?php
// Utility functions can be added here

/**
 * Sanitizes input data.
 *
 * @param mixed $input The input to be sanitized.
 * @return mixed The sanitized input.
 */
function ai_autoblogger_sanitize($input) {
    if (is_string($input)) {
        // Sanitize text strings
        return sanitize_text_field($input);
    } elseif (is_array($input)) {
        // Recursively sanitize each element of the array
        return array_map('ai_autoblogger_sanitize', $input);
    } else {
        // Return the input as is for types that don't need sanitization
        return $input;
    }
}

/**
 * Formats and logs a message for debugging.
 *
 * @param string $message The message to log.
 */
function ai_autoblogger_debug_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
        error_log('[AI Autoblogger]: ' . $message);
    }
}

/**
 * Generates a title for auto-generated posts.
 *
 * @param string $content The content of the post.
 * @return string The generated title.
 */
function ai_autoblogger_generate_post_title($content) {
    // Extract the first sentence or a portion of the content as the title
    $endOfFirstSentence = strpos($content, '.') ?: strpos($content, ' ') ?: 50;
    $title = substr($content, 0, $endOfFirstSentence);

    // Truncate the title to a reasonable length if necessary
    $maxTitleLength = 60;
    if (strlen($title) > $maxTitleLength) {
        $title = substr($title, 0, $maxTitleLength) . '...';
    }

    // Sanitize and return the title
    return sanitize_text_field($title);
}
