<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog accessory.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

class Omnilog_acc {

    private $_ee;
    private $_model;
    private $_theme_url;

    public $description;
    public $id;
    public $name;
    public $sections;
    public $version;
    
    
    /* --------------------------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Constructor.
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        $this->_ee =& get_instance();

        // Tell EE where to look.
        $this->_ee->lang->loadfile('omnilog');
        $this->_ee->load->add_package_path(PATH_THIRD .'omnilog/');

        // Load the model.
        $this->_ee->load->model('omnilog_model');
        $this->_model = $this->_ee->omnilog_model;

        // Instance properties.
        $this->description  = $this->_ee->lang->line('omnilog_module_description');
        $this->id           = $this->_model->get_package_name();
        $this->name         = $this->_ee->lang->line('omnilog_module_name');
        $this->sections     = array();
        $this->version      = $this->_model->get_package_version();

        // Basic stuff required by every view.
        $this->_base_qs     = 'C=addons_modules' .AMP .'M=show_module_cp' .AMP .'module=omnilog';
        $this->_base_url    = BASE .AMP .$this->_base_qs;
        $this->_theme_url   = $this->_model->get_package_theme_url();
    }


    /**
     * Sets the accessory sections.
     *
     * @access  public
     * @return  void
     */
    public function set_sections()
    {
        $common_css = $this->_theme_url .'common/css/cp.css';
        $theme_css  = $this->_ee->config->item('cp_theme') == 'corporate'
            ? $this->_theme_url .'corporate/css/cp.css'
            : $this->_theme_url .'default/css/cp.css';

        $this->_ee->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' .$common_css .'" />');
        $this->_ee->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' .$theme_css .'" />');

        $vars = array(
            'log_entries'       => $this->_model->get_log_entries(NULL, 10),
            'webmaster_email'   => $this->_ee->config->item('webmaster_email')
        );

        $this->sections[$this->_ee->lang->line('hd_log')] = $this->_ee->load->view('accessory', $vars, TRUE);
    }


    /**
     * Required to automatically update the version number.
     *
     * @access  public
     * @return  bool
     */
    public function update()
    {
        return TRUE;
    }


}


/* End of file      : mcp.omnilog.php */
/* File location    : third_party/omnilog/mcp.omnilog.php */
