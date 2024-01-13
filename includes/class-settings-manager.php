<?php
class Settings_Manager {
    private $option_name = 'ai_autoblogger_options';

    /**
     * Retrieve settings from the database.
     * 
     * @return array The settings array.
     */
    public function get_settings() {
        return get_option($this->option_name, []);
    }

    /**
     * Save settings to the database.
     * 
     * @param array $settings The settings array to save.
     * @return bool True if option value has changed, false if not or if update failed.
     */
    public function save_settings($settings) {
        return update_option($this->option_name, $settings);
    }
}
