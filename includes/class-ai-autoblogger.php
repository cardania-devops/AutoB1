<?php
require_once plugin_dir_path(__FILE__) . 'ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'class-ai-autoblogger-admin.php';
require_once plugin_dir_path(__FILE__) . 'class-db-handler.php';
require_once plugin_dir_path(__FILE__) . 'class-logger.php';
require_once plugin_dir_path(__FILE__) . 'class-openai-api.php';
require_once plugin_dir_path(__FILE__) . 'class-post-generator.php';
require_once plugin_dir_path(__FILE__) . 'class-settings-manager.php';
require_once plugin_dir_path(__FILE__) . 'utils.php';


class AIAutoblogger {


    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        if (!wp_next_scheduled('ai_autoblogger_cron_hook')) {
            wp_schedule_event(time(), 'hourly', 'ai_autoblogger_cron_hook');
        }
        add_action('ai_autoblogger_cron_hook', array($this, 'auto_generate_post'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'AI Autoblogger Settings',
            'AI Autoblogger',
            'manage_options',
            'ai-autoblogger',
            array($this, 'display_settings_page')
        );
        add_action('admin_init', array($this, 'handle_post_generation'));
    }

    public function display_settings_page() {
        require_once plugin_dir_path( __FILE__ ) . '../admin/settings-page.php';
    }

    public function register_settings() {
        register_setting('ai-autoblogger', 'ai_autoblogger_options');

        add_settings_section(
            'ai_autoblogger_main',
            'AI Autoblogger Settings',
            array($this, 'ai_autoblogger_settings_section_callback'),
            'ai-autoblogger'
        );

        // API Key field
        add_settings_field(
            'ai_autoblogger_api_key',
            'API Key',
            array($this, 'ai_autoblogger_api_key_render'),
            'ai-autoblogger',
            'ai_autoblogger_main'
        );

        // Post Frequency field
        add_settings_field(
            'ai_autoblogger_post_frequency',
            'Post Frequency',
            array($this, 'ai_autoblogger_post_frequency_render'),
            'ai-autoblogger',
            'ai_autoblogger_main'
        );

        // Content Tone field
        add_settings_field(
            'ai_autoblogger_content_tone',
            'Content Tone',
            array($this, 'ai_autoblogger_content_tone_render'),
            'ai-autoblogger',
            'ai_autoblogger_main'
        );

        // Tags field
        add_settings_field(
            'ai_autoblogger_tags',
            'Tags',
            array($this, 'ai_autoblogger_tags_render'),
            'ai-autoblogger',
            'ai_autoblogger_main'
        );

        // Automatic Post Generation field
        add_settings_field(
            'ai_autoblogger_auto_post',
            'Automatic Post Generation',
            array($this, 'ai_autoblogger_auto_post_render'),
            'ai-autoblogger',
            'ai_autoblogger_main'
        );
    }

    public function ai_autoblogger_settings_section_callback() {
        echo '<p>Enter your settings below:</p>';
    }

    // Rendering functions for each setting field
    // Rendering function for the API Key
    public function ai_autoblogger_api_key_render() {
        $options = get_option('ai_autoblogger_options');
        ?>
        <input type='text' name='ai_autoblogger_options[api_key]' value='<?php echo isset($options['api_key']) ? esc_attr($options['api_key']) : ''; ?>'>
        <?php
    }

    // Rendering function for Post Frequency
    public function ai_autoblogger_post_frequency_render() {
        $options = get_option('ai_autoblogger_options');
        ?>
        <select name='ai_autoblogger_options[post_frequency]'>
            <option value='hourly' <?php selected(isset($options['post_frequency']) && $options['post_frequency'] === 'hourly'); ?>>Hourly</option>
            <option value='daily' <?php selected(isset($options['post_frequency']) && $options['post_frequency'] === 'daily'); ?>>Daily</option>
            <option value='weekly' <?php selected(isset($options['post_frequency']) && $options['post_frequency'] === 'weekly'); ?>>Weekly</option>
        </select>
        <?php
    }

    // Rendering function for Content Tone
    public function ai_autoblogger_content_tone_render() {
        $options = get_option('ai_autoblogger_options');
        ?>
        <input type='text' name='ai_autoblogger_options[content_tone]' value='<?php echo isset($options['content_tone']) ? esc_attr($options['content_tone']) : ''; ?>'>
        <?php
    }

    // Rendering function for Tags
    public function ai_autoblogger_tags_render() {
        $options = get_option('ai_autoblogger_options');
        ?>
        <input type='text' name='ai_autoblogger_options[tags]' value='<?php echo isset($options['tags']) ? esc_attr($options['tags']) : ''; ?>'>
        <?php
    }

    public function ai_autoblogger_auto_post_render() {
        $options = get_option('ai_autoblogger_options');
        ?>
        <input type='checkbox' name='ai_autoblogger_options[auto_post]' <?php checked(isset($options['auto_post']) && $options['auto_post'] === 'yes'); ?> value='yes'>
        Automatically generate posts
        <?php
    }

    public function handle_post_generation() {
        if (isset($_POST['action']) && $_POST['action'] == 'generate_post') {
            $options = get_option('ai_autoblogger_options');
            $api_key = $options['api_key'] ?? '';
            $openai_api = new OpenAI_API($api_key);
            $post_generator = new Post_Generator($openai_api);
            $post_generator->generate_and_publish_post();
        }
    }
    
    public function auto_generate_post() {
        $options = get_option('ai_autoblogger_options');
        if (isset($options['auto_post']) && $options['auto_post'] == 'yes') {
            $api_key = $options['api_key'] ?? '';
            $openai_api = new OpenAI_API($api_key);
            $post_generator = new Post_Generator($openai_api);
            $post_generator->generate_and_publish_post();
        }
    }
    
}
