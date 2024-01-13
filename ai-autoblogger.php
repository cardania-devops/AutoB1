<?php
/**
 * Plugin Name: AI Autoblogger
 * Description: Automatically generates blog posts using OpenAI.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: ai-autoblogger
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include the main AI Autoblogger class from the 'includes' folder.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-ai-autoblogger.php';

// Begins execution of the plugin by instantiating the main class.
$plugin = new AIAutoblogger();
