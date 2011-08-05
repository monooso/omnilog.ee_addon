<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * Mock OmniLog model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

class Mock_omnilog_model {

    public function get_log_entries($site_id = NULL, $limit = NULL) {}
    public function get_package_name() {}
    public function get_package_theme_url() {}
    public function get_package_version() {}
    public function get_site_id() {}
    public function install_module() {}
    public function install_module_actions() {}
    public function install_module_register() {}
	public function notify_site_admin_of_log_entry(Omnilog_entry $entry) {}
    public function save_entry_to_log(Omnilog_entry $entry) {}
    public function uninstall_module() {}
    public function update_package($installed_version = '') {}

}


/* End of file		: mock.omnilog_model.php */
/* File location	: third_party/omnilog/tests/mocks/mock.omnilog_model.php */
