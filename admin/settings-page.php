<?php
// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Fetch saved options
$options = get_option('ai_autoblogger_options');
?>
<div class="wrap">
    <h1><?= esc_html(get_admin_page_title()); ?></h1>

    <!-- Settings form -->
    <form action="options.php" method="post">
    <?php
    settings_fields('ai-autoblogger');
    do_settings_sections('ai-autoblogger');
    submit_button();
    ?>
</form>

    <!-- Manual post generation form -->
    <form action="<?php echo esc_url(admin_url('admin.php?page=ai-autoblogger')); ?>" method="post">
        <?php wp_nonce_field('ai_autoblogger_generate_post'); ?>
        <input type="hidden" name="action" value="generate_post">
        <?php submit_button('Generate Post Now'); ?>
    </form>
</div>
