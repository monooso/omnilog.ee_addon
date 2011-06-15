<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * OmniLog model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 * @version         0.1.0
 */

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
        $this->_package_version = $package_version  ? $package_version          : '0.1.0';
 

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

        return TRUE;
    }


}


/* End of file      : omnilog_model.php */
/* File location    : third_party/omnilog/models/omnilog_model.php */
