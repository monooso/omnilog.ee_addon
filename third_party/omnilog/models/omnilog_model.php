<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * OmniLog model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 * @version         1.1.0
 */

require_once PATH_THIRD .'omnilog/classes/omnilog_entry' .EXT;

class Omnilog_model extends CI_Model {

    private $_ee;
    private $_namespace;
    private $_package_name;
    private $_package_version;
    private $_site_id;


    /* --------------------------------------------------------------
     * PRIVATE METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Returns a references to the package cache. Should be called
     * as follows: $cache =& $this->_get_package_cache();
     *
     * @access  private
     * @return  array
     */
    private function &_get_package_cache()
    {
        return $this->_ee->session->cache[$this->_namespace][$this->_package_name];
    }


    /**
     * Performs the necessary updates when upgrading to v1.1.0.
     *
     * @access  private
     * @return  void
     */
    private function _update_module_to_version_110()
    {
        $this->_ee->load->dbforge();

        $this->_ee->dbforge->add_column(
            'omnilog_entries',
            array('admin_emails' => array('type' => 'MEDIUMTEXT'))
        );
    }


    /* --------------------------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   string      $package_name       The package name. Used during testing.
     * @param   string      $package_version    The package version. Used during testing.
     * @param   string      $namespace          The global session namespace. Used during testing.
     * @return  void
     */
    public function __construct($package_name = '', $package_version = '', $namespace = '')
    {
        parent::__construct();

        $this->_ee              =& get_instance();
        $this->_namespace       = $namespace        ? strtolower($namespace)    : 'experience';
        $this->_package_name    = $package_name     ? strtolower($package_name) : 'omnilog';
        $this->_package_version = $package_version  ? $package_version          : '1.1.0';

        // Initialise the add-on cache.
        if ( ! array_key_exists($this->_namespace, $this->_ee->session->cache))
        {
            $this->_ee->session->cache[$this->_namespace] = array();
        }

        if ( ! array_key_exists($this->_package_name, $this->_ee->session->cache[$this->_namespace]))
        {
            $this->_ee->session->cache[$this->_namespace][$this->_package_name] = array();
        }
    }


    /**
     * Returns the log entries. By default, only the log entries for
     * the current site are returned.
     *
     * @access  public
     * @param   int|string      $site_id        Get the log entries for the specified site ID.
     * @param   int             $limit          The maximum number of log entries to retrieve.
     * @return  array
     */
    public function get_log_entries($site_id = NULL, $limit = NULL)
    {
        if ( ! valid_int($site_id, 1))
        {
            $site_id = $this->_ee->config->item('site_id');
        }

        $db = $this->_ee->db;
        $db->select('addon_name, admin_emails, date, log_entry_id, message, notify_admin, type')
            ->from('omnilog_entries')
            ->where(array('site_id' => $site_id))
            ->order_by('log_entry_id', 'desc');

        if (valid_int($limit, 1))
        {
            $db->limit($limit);
        }

        $db_result  = $db->get();
        $entries    = array();

        foreach ($db_result->result_array() AS $db_row)
        {
            $db_row['admin_emails'] = explode('|', $db_row['admin_emails']);
            $db_row['notify_admin'] = (strtolower($db_row['notify_admin']) === 'y');
            $entries[]              = new Omnilog_entry($db_row);
        }

        return $entries;
    }


    /**
     * Returns the package name.
     *
     * @access  public
     * @return  string
     */
    public function get_package_name()
    {
        return $this->_package_name;
    }


    /**
     * Returns the package theme folder URL, appending a forward slash if required.
     *
     * @access    public
     * @return    string
     */
    public function get_package_theme_url()
    {
        $theme_url = $this->_ee->config->item('theme_folder_url');
        $theme_url .= substr($theme_url, -1) == '/' ? 'third_party/' : '/third_party/';

        return $theme_url .$this->get_package_name() .'/';
    }


    /**
     * Returns the package version.
     *
     * @access  public
     * @return  string
     */
    public function get_package_version()
    {
        return $this->_package_version;
    }


    /**
     * Returns the site ID.
     *
     * @access  public
     * @return  int
     */
    public function get_site_id()
    {
        if ( ! $this->_site_id)
        {
            $this->_site_id = intval($this->_ee->config->item('site_id'));
        }

        return $this->_site_id;
    }


    /**
     * Installs the module.
     *
     * @access  public
     * @return  bool
     */
    public function install_module()
    {
        $this->install_module_register();
        $this->install_module_actions();
        $this->install_module_entries_table();

        return TRUE;
    }


    /**
     * Register the module actions in the database.
     *
     * @access  public
     * @return  void
     */
    public function install_module_actions()
    {
        
        $this->_ee->db->insert('actions', array(
            'class'     => ucfirst($this->get_package_name()),
            'method'    => ''
        ));
        
    }


    /**
     * Creates the OmniLog entries table.
     *
     * @access  public
     * @return  void
     */
    public function install_module_entries_table()
    {
        $this->_ee->load->dbforge();

        $this->_ee->dbforge->add_field(array(
            'log_entry_id' => array(
                'auto_increment'    => TRUE,
                'constraint'        => 10,
                'type'              => 'INT',
                'unsigned'          => TRUE
            ),
            'site_id' => array(
                'constraint'        => 5,
                'type'              => 'INT',
                'unsigned'          => TRUE
            ),
            'addon_name' => array(
                'constraint'        => 50,
                'type'              => 'VARCHAR'
            ),
            'admin_emails' => array(
                'type'              => 'MEDIUMTEXT'
            ),
            'date' => array(
                'constraint'        => 10,
                'type'              => 'INT',
                'unsigned'          => TRUE
            ),
            'notify_admin' => array(
                'constraint'        => 1,
                'default'           => 'n',
                'type'              => 'CHAR'
            ),
            'type' => array(
                'constraint'        => 10,
                'type'              => 'VARCHAR'
            ),
            'message' => array(
                'type'              => 'TEXT'
            )
        ));

        $this->_ee->dbforge->add_key('log_entry_id', TRUE);
        $this->_ee->dbforge->create_table('omnilog_entries', TRUE);
    }


    /**
     * Registers the module in the database.
     *
     * @access  public
     * @return  void
     */
    public function install_module_register()
    {
        $this->_ee->db->insert('modules', array(
            'has_cp_backend'        => 'y',
            'has_publish_fields'    => 'n',
            'module_name'           => ucfirst($this->get_package_name()),
            'module_version'        => $this->get_package_version()
        ));
    }


    /**
     * Notifies the site administrator (via email) of the supplied OmniLog Entry.
     *
     * @access  public
     * @param   Omnilog_entry        $entry        The log entry.
     * @return  void
     */
    public function notify_site_admin_of_log_entry(Omnilog_entry $entry)
    {
        $this->_ee->load->helper('text');
        $this->_ee->load->library('email');

        $email  = $this->_ee->email;
        $lang   = $this->_ee->lang;

        if ( ! $entry->is_populated())
        {
            throw new Exception($lang->line('exception__notify_admin__missing_data'));
        }

        $webmaster_email = $this->_ee->config->item('webmaster_email');

        if ($email->valid_email($webmaster_email) !== TRUE)
        {
            throw new Exception($lang->line('exception__notify_admin__invalid_webmaster_email'));
        }

        $webmaster_name = ($webmaster_name = $this->_ee->config->item('webmaster_name'))
            ? $webmaster_name
            : '';

        switch ($entry->get_type())
        {
            case Omnilog_entry::NOTICE:
                $lang_entry_type = $lang->line('email_entry_type_notice');
                break;

            case Omnilog_entry::WARNING:
                $lang_entry_type = $lang->line('email_entry_type_warning');
                break;

            case Omnilog_entry::ERROR:
                $lang_entry_type = $lang->line('email_entry_type_error');
                break;

            default:
                $lang_entry_type = $lang->line('email_entry_type_unknown');
                break;
        }

        $subject = ($site_name = $this->_ee->config->item('site_name'))
            ? $lang->line('email_subject') .' (' .$site_name .')'
            : $lang->line('email_subject');

        $message = $lang->line('email_preamble') .NL .NL;
        $message .= $lang->line('email_addon_name') .NL .$entry->get_addon_name() .NL .NL;
        $message .= $lang->line('email_log_date') .NL .date('r', $entry->get_date()) .NL .NL;
        $message .= $lang->line('email_entry_type') .NL .$lang_entry_type .NL .NL;
        $message .= $lang->line('email_log_message') .NL .$entry->get_message() .NL .NL;
        $message .= $lang->line('email_cp_url') .NL .$this->_ee->config->item('cp_url') .NL .NL;
        $message .= $lang->line('email_postscript');
        $message = entities_to_ascii($message);

        $email->from($webmaster_email, $webmaster_name);
        $email->to($webmaster_email);
        $email->subject($subject);
        $email->message($message);

        if ($email->send() !== TRUE)
        {
            throw new Exception($lang->line('exception__notify_admin__email_not_sent'));
        }

    }


    /**
     * Saves the supplied OmniLog Entry to the database.
     *
     * @access  public
     * @param   Omnilog_entry       $entry          The entry to save.
     * @return  Omnilog_entry
     */
    public function save_entry_to_log(Omnilog_entry $entry)
    {
        /**
         * This method could conceivably be called when the module is
         * not installed, but the Omnilogger class is present.
         */

        if ( ! $this->_ee->db->table_exists('omnilog_entries'))
        {
            throw new Exception($this->_ee->lang->line('exception__save_entry__not_installed'));
        }

        if ( ! $entry->is_populated())
        {
            throw new Exception($this->_ee->lang->line('exception__save_entry__missing_data'));
        }

        $insert_data = array_merge(
            $entry->to_array(),
            array(
                'notify_admin'  => ($entry->get_notify_admin() === TRUE) ? 'y' : 'n',
                'site_id'       => $this->get_site_id()
            )
        );

        $insert_data['admin_emails'] = implode($insert_data['admin_emails'], '|');

        $this->_ee->db->insert('omnilog_entries', $insert_data);

        if ( ! $insert_id = $this->_ee->db->insert_id())
        {
            throw new Exception($this->_ee->lang->line('exception__save_entry__not_saved'));
        }

        $entry->set_log_entry_id($insert_id);
        return $entry;
    }


    /**
     * Uninstalls the module.
     *
     * @access  public
     * @return  bool
     */
    public function uninstall_module()
    {
        $module_name = ucfirst($this->get_package_name());

        // Retrieve the module information.
        $db_module = $this->_ee->db
            ->select('module_id')
            ->get_where('modules', array('module_name' => $module_name), 1);

        if ($db_module->num_rows() !== 1)
        {
            return FALSE;
        }

        $this->_ee->db->delete('module_member_groups', array('module_id' => $db_module->row()->module_id));
        $this->_ee->db->delete('modules', array('module_name' => $module_name));
        $this->_ee->db->delete('actions', array('class' => $module_name));

        // Drop the log entries table.
        $this->_ee->load->dbforge();
        $this->_ee->dbforge->drop_table('omnilog_entries');

        return TRUE;
    }


    /**
     * Updates the module.
     *
     * @access  public
     * @param   string        $installed_version        The installed version.
     * @return  bool
     */
    public function update_module($installed_version = '')
    {
        if (version_compare($installed_version, $this->get_package_version(), '>='))
        {
            return FALSE;
        }

        if (version_compare($installed_version, '1.1.0', '<'))
        {
            $this->_update_module_to_version_110();
        }

        return TRUE;
    }


}


/* End of file      : omnilog_model.php */
/* File location    : third_party/omnilog/models/omnilog_model.php */
