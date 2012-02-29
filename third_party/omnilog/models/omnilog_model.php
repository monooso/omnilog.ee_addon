<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 * @version         1.5.0
 */

require_once dirname(__FILE__) .'/../classes/omnilog_entry.php';

class Omnilog_model extends CI_Model {

  private $EE;
  private $_log_limit;
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
    return $this->EE->session->cache[$this->_namespace][$this->_package_name];
  }


  /**
   * Performs the necessary updates when upgrading to v1.1.0.
   *
   * @access  private
   * @return  void
   */
  private function _update_package_to_version_110()
  {
    $this->EE->load->dbforge();

    $this->EE->dbforge->add_column(
      'omnilog_entries',
      array('admin_emails' => array('type' => 'MEDIUMTEXT'))
    );
  }


  /**
   * Performs the necessary updates when upgrading to v1.2.2.
   *
   * @access  private
   * @return  void
   */
  private function _update_package_to_version_122()
  {
    $this->EE->load->dbforge();

    $this->EE->dbforge->add_column(
      'omnilog_entries',
      array('extended_data' => array('type' => 'TEXT'))
    );

    // Add an index to the OmniLog entries table.
    $this->EE->db->query('CREATE INDEX key_addon_name
      ON `exp_omnilog_entries` (`addon_name`)');
  }


  /**
   * Performs the necessary updates when upgrading to v1.4.1
   *
   * @access  private
   * @return  void
   */
  private function _update_package_to_version_141()
  {
    $this->EE->db->delete('actions',
      array('class' => ucfirst($this->get_package_name())));
  }


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   string    $package_name       Package name. Used for testing.
   * @param   string    $package_version    Package version. Used for testing.
   * @param   string    $namespace          Session namespace. Used for testing.
   * @return  void
   */
  public function __construct(
    $package_name = '',
    $package_version = '',
    $namespace = ''
  )
  {
    parent::__construct();
    $this->EE =& get_instance();

    $this->_namespace = $namespace ? strtolower($namespace) : 'experience';

    $this->_package_name = $package_name ? strtolower($package_name)
      : 'omnilog';

    $this->_package_version = $package_version ? $package_version : '1.5.0';

    $this->_log_limit = 50;

    // Initialise the add-on cache.
    if ( ! array_key_exists($this->_namespace, $this->EE->session->cache))
    {
        $this->EE->session->cache[$this->_namespace] = array();
    }

    if ( ! array_key_exists($this->_package_name,
      $this->EE->session->cache[$this->_namespace])
    )
    {
      $this->EE->session->cache[$this->_namespace][$this->_package_name]
        = array();
    }
  }


  /**
   * Clears all the log entries for the specified site.
   *
   * @access  public
   * @param   int|string    $site_id    The site ID.
   * @return  void
   */
  public function clear_log($site_id = NULL)
  {
    $site_id = valid_int($site_id, 1)
      ? (int) $site_id : $this->get_site_id();

    $this->EE->db->delete('omnilog_entries', array('site_id' => $site_id));
    return TRUE;
  }


  /**
   * Returns an array of add-ons with one or more log entries in the OmniLog 
   * table.
   *
   * @access  public
   * @param   int|string    $site_id    The site ID. Defaults to current site.
   * @return  array
   */
  public function get_addons_with_an_omnilog_entry($site_id = NULL)
  {
    // Ensure we have a valid site ID.
    $site_id = valid_int($site_id, 1)
      ? (int) $site_id : $this->get_site_id();

    $db_result = $this->EE->db
      ->select('addon_name')
      ->group_by('addon_name')
      ->order_by('addon_name', 'asc')
      ->get_where('omnilog_entries', array('site_id' => $site_id));

    $addons = array();

    foreach ($db_result->result() AS $db_addon)
    {
      $addons[] = $db_addon->addon_name;
    }

    return $addons;
  }


  /**
   * Returns the default log 'limit'. That is, the number of log entries
   * returned when calling the get_log_entries method.
   *
   * @access  public
   * @return  int
   */
  public function get_default_log_limit()
  {
    return $this->_log_limit;
  }


  /**
   * Returns the installed package version.
   *
   * @access  public
   * @return  string
   */
  public function get_installed_version()
  {
    $db = $this->EE->db;

    $db_result = $db
      ->select('module_version')
      ->get_where(
        'modules',
        array('module_name' => $this->get_package_name()),
        1
      );

    return $db_result->num_rows() === 1
      ? $db_result->row()->module_version
      : '';
  }


  /**
   * Returns the total number of log entries for the specified site.
   *
   * @access  public
   * @param   int|string    $site_id    The site ID. Defaults is current site.
   * @return  int
   */
  public function get_log_entries_count($site_id = NULL)
  {
    $site_id = valid_int($site_id, 1)
      ? (int) $site_id : $this->get_site_id();

    return $this->EE->db
      ->where(array('site_id' => $site_id))
      ->count_all_results('omnilog_entries');
  }


  /**
   * Returns the log entries. By default, only the log entries for
   * the current site are returned.
   *
   * @access  public
   * @param   int|string  $site_id        Restrict to the specified site ID.
   * @param   int         $limit          Maximum number of entries to retrieve.
   * @param   int         $offset         The number of entries to skip.
   * @param   string      $addon_filter   Restrict to the specifed add-on.
   * @param   string      $type_filter    Restrict to the specified entry type.
   * @return  array
   */
  public function get_log_entries($site_id = NULL, $limit = NULL,
    $offset = NULL, $addon_filter = NULL, $type_filter = NULL
  )
  {
    // Ensure we have valid arguments.
    $site_id = valid_int($site_id, 1)
      ? (int) $site_id : $this->get_site_id();

    $limit = valid_int($limit, 1)
      ? (int) $limit : $this->get_default_log_limit();

    $offset = valid_int($offset, 0) ? (int) $offset : 0;

    $db = $this->EE->db;

    $db
      ->select('addon_name, admin_emails, date, log_entry_id, message,
        extended_data, notify_admin, type')
      ->where('site_id', $site_id);

    // Filter by add-on.
    if ($addon_filter)
    {
      $db->where('addon_name', $addon_filter);
    }

    // Filter by entry type.
    if (is_string($type_filter))
    {
      $db->where('type', $type_filter);
    }

    // Run the query.
    $db_result = $db
      ->order_by('log_entry_id', 'desc')
      ->get('omnilog_entries', $limit, $offset);

    $entries = array();

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
   * Returns the package theme folder URL. Appends a forward slash if required.
   *
   * @access    public
   * @return    string
   */
  public function get_package_theme_url()
  {
    $theme_url = $this->EE->config->item('theme_folder_url');

    $theme_url .= substr($theme_url, -1) == '/'
      ? 'third_party/'
      : '/third_party/';

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
      $this->_site_id = intval($this->EE->config->item('site_id'));
    }

    return $this->_site_id;
  }


  /**
   * Returns an array of entry types with one or more entries in the OmniLog 
   * table.
   *
   * @access  public
   * @param   int|string    $site_id    The site ID. Defaults to current site.
   * @return  array
   */
  public function get_types_with_an_omnilog_entry($site_id = NULL)
  {
    // Ensure we have a valid site ID.
    $site_id = valid_int($site_id, 1)
      ? (int) $site_id : $this->get_site_id();

    $db_types = $this->EE->db
      ->select('type')
      ->group_by('type')
      ->order_by('type', 'asc')
      ->get_where('omnilog_entries', array('site_id' => $site_id));

    $types = array();

    foreach ($db_types->result() AS $db_type)
    {
      $types[] = $db_type->type;
    }

    return $types;
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
    $this->install_module_entries_table();
    return TRUE;
  }


  /**
   * Creates the OmniLog entries table.
   *
   * @access  public
   * @return  void
   */
  public function install_module_entries_table()
  {
    $this->EE->load->dbforge();

    $this->EE->dbforge->add_field(array(
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
      ),
      'extended_data' => array(
        'type'              => 'TEXT'
      )
    ));

    $this->EE->dbforge->add_key('log_entry_id', TRUE);
    $this->EE->dbforge->add_key('addon_name');
    $this->EE->dbforge->create_table('omnilog_entries', TRUE);
  }


  /**
   * Registers the module in the database.
   *
   * @access  public
   * @return  void
   */
  public function install_module_register()
  {
    $this->EE->db->insert('modules', array(
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
   * @param   Omnilog_entry     $entry      The log entry.
   * @return  void
   */
  public function notify_site_admin_of_log_entry(Omnilog_entry $entry)
  {
    $this->EE->load->helper('text');
    $this->EE->load->library('email');

    $email  = $this->EE->email;
    $lang   = $this->EE->lang;

    $lang->loadfile('omnilog');

    if ( ! $entry->is_populated())
    {
      throw new Exception($lang->line('exception__notify_admin__missing_data'));
    }

    $webmaster_email = $this->EE->config->item('webmaster_email');

    if ($email->valid_email($webmaster_email) !== TRUE)
    {
      throw new Exception(
        $lang->line('exception__notify_admin__invalid_webmaster_email'));
    }

    $webmaster_name = ($webmaster_name = $this->EE->config->item('webmaster_name'))
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

    $subject = ($site_name = $this->EE->config->item('site_name'))
      ? $lang->line('email_subject') .' (' .$site_name .')'
      : $lang->line('email_subject');

    $admin_emails = ($admin_emails = $entry->get_admin_emails())
      ? $admin_emails
      : array($webmaster_email);

    $message = $lang->line('email_preamble') .NL .NL;
    $message .= $lang->line('email_addon_name') .NL
      .$entry->get_addon_name() .NL .NL;

    $message .= $lang->line('email_log_date') .NL
      .date('r', $entry->get_date()) .NL .NL;

    $message .= $lang->line('email_entry_type') .NL
      .$lang_entry_type .NL .NL;

    $message .= $lang->line('email_log_message') .NL
      .$entry->get_message() .NL .NL;

    $message .= $lang->line('email_log_extended_data') .NL
      .$entry->get_extended_data() .NL .NL;

    $message .= $lang->line('email_cp_url') .NL
      .$this->EE->config->item('cp_url') .NL .NL;

    $message .= $lang->line('email_postscript');
    $message = entities_to_ascii($message);

    $email->from($webmaster_email, $webmaster_name);
    $email->to($admin_emails);
    $email->subject($subject);
    $email->message($message);

    if ($email->send() !== TRUE)
    {
      throw new Exception(
        $lang->line('exception__notify_admin__email_not_sent'));
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

    if ( ! $this->EE->db->table_exists('omnilog_entries'))
    {
      throw new Exception(
        $this->EE->lang->line('exception__save_entry__not_installed'));
    }

    if ( ! $entry->is_populated())
    {
      throw new Exception(
        $this->EE->lang->line('exception__save_entry__missing_data'));
    }

    $insert_data = array_merge(
      $entry->to_array(),
      array(
        'notify_admin'  => ($entry->get_notify_admin() === TRUE) ? 'y' : 'n',
        'site_id'       => $this->get_site_id()
      )
    );

    $insert_data['admin_emails'] = implode($insert_data['admin_emails'], '|');

    $this->EE->db->insert('omnilog_entries', $insert_data);

    if ( ! $insert_id = $this->EE->db->insert_id())
    {
      throw new Exception(
        $this->EE->lang->line('exception__save_entry__not_saved'));
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
    $db_module = $this->EE->db
      ->select('module_id')
      ->get_where('modules', array('module_name' => $module_name), 1);

    if ($db_module->num_rows() !== 1)
    {
      return FALSE;
    }

    $this->EE->db->delete('module_member_groups',
      array('module_id' => $db_module->row()->module_id));

    $this->EE->db->delete('modules', array('module_name' => $module_name));
    $this->EE->db->delete('actions', array('class' => $module_name));

    // Drop the log entries table.
    $this->EE->load->dbforge();
    $this->EE->dbforge->drop_table('omnilog_entries');

    return TRUE;
  }


  /**
   * Updates the module.
   *
   * @access  public
   * @param   string    $installed_version    The installed version.
   * @param   bool      $force                Forcibly update the module version?
   * @return  bool
   */
  public function update_package($installed_version = '', $force = FALSE)
  {
    if ( ! $installed_version OR version_compare(
      $installed_version, $this->get_package_version(), '>=')
    )
    {
      return FALSE;
    }

    if (version_compare($installed_version, '1.1.0', '<'))
    {
      $this->_update_package_to_version_110();
    }

    if (version_compare($installed_version, '1.2.2', '<'))
    {
      $this->_update_package_to_version_122();
    }

    if (version_compare($installed_version, '1.4.1', '<'))
    {
      $this->_update_package_to_version_141();
    }

    // Forcibly update the module version number?
    if ($force === TRUE)
    {
      $this->EE->db->update(
        'modules',
        array('module_version' => $this->get_package_version()),
        array('module_name' => $this->get_package_name())
      );
    }

    return TRUE;
  }


}


/* End of file      : omnilog_model.php */
/* File location    : third_party/omnilog/models/omnilog_model.php */
