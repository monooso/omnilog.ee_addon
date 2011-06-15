<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog module control panel.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

class Omnilog_mcp {

    private $_ee;
    private $_model;
    private $_theme_url;
    
    
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

        // Load the model.
        $this->_ee->load->model('omnilog_model');
        $this->_model = $this->_ee->omnilog_model;

        // Basic stuff required by every view.
        $this->_base_qs     = 'C=addons_modules' .AMP .'M=show_module_cp' .AMP .'module=omnilog';
        $this->_base_url    = BASE .AMP .$this->_base_qs;
        $this->_theme_url   = $this->_model->get_package_theme_url();
        
        $this->_ee->load->helper('form');
        $this->_ee->load->library('table');

        $this->_ee->cp->set_breadcrumb($this->_base_url, $this->_ee->lang->line('omnilog_module_name'));
        $this->_ee->cp->add_to_foot('<script type="text/javascript" src="' .$this->_theme_url .'js/cp.js"></script>');
        $this->_ee->javascript->compile();

        $this->_ee->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' .$this->_theme_url .'css/cp.css" />');

        $nav_array = array(
            'nav_settings'      => $this->_base_url .AMP .'method=settings',
            'nav_error_log'     => $this->_base_url .AMP .'method=error_log'
        );

        $this->_ee->cp->set_right_nav($nav_array);
    }


    /**
     * Module index page.
     *
     * @access  public
     * @return  string
     */
    public function index()
    {
        return $this->settings();
    }


    /**
     * Error log.
     *
     * @access  public
     * @return  string
     */
    public function error_log()
    {
        $vars = array(
            'cp_page_title' => $this->_ee->lang->line('hd_error_log')
        );
        
        return $this->_ee->load->view('error_log', $vars, TRUE);
    }


    /**
     * Saves the settings.
     *
     * @access  public
     * @return  void
     */
    public function save_settings()
    {
        $lang = $this->_ee->lang;
        $sess = $this->_ee->session;

        $this->_model->save_module_settings()
            ? $sess->set_flashdata('message_success', $lang->line('flashdata__settings_saved'))
            : $sess->set_flashdata('message_failure', $lang->line('flashdata__settings_not_saved'));

        $this->_ee->functions->redirect($this->_base_url .AMP .'method=settings');
    }


    /**
     * Settings.
     *
     * @access  public
     * @return  string
     */
    public function settings()
    {
        $vars = array(
            'form_action'       => $this->_base_qs .AMP .'method=save_settings',
            'cp_page_title'     => $this->_ee->lang->line('hd_settings')
        );
        
        return $this->_ee->load->view('settings', $vars, TRUE);
    }

}


/* End of file      : mcp.omnilog.php */
/* File location    : third_party/omnilog/mcp.omnilog.php */
