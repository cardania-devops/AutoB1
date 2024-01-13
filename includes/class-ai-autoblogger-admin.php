<?php
/**
 * The admin-specific functionality of the plugin.
 */

class AIAutoblogger_Admin {

    /**
     * Register the settings page for the plugin.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
    }

    /**
     * Add settings page to the WordPress admin menu.
     */
    public function add_settings_page() {
        add_options_page(
            'AI Autoblogger Settings',
            'AI Autoblogger',
            'manage_options',
            'ai-autoblogger',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display the settings page content.
     */
    public function display_settings_page() {
        // Include the settings page view
        include_once plugin_dir_path(__FILE__) . 'views/settings-page.php';
    }

    // Additional methods for handling settings...
}
