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
        $this->_ee->cp->set_breadcrumb($this->_base_url, $this->_ee->lang->line('omnilog_module_name'));
    }


    /**
     * Module index page.
     *
     * @access  public
     * @return  string
     */
    public function index()
    {
        return $this->log();
    }


    /**
     * Log.
     *
     * @access  public
     * @return  string
     */
    public function log()
    {
        $vars = array(
            'cp_page_title' => $this->_ee->lang->line('hd_log'),
            'log_entries'   => $this->_model->get_log_entries()
        );
        
        return $this->_ee->load->view('log', $vars, TRUE);
    }


}


/* End of file      : mcp.omnilog.php */
/* File location    : third_party/omnilog/mcp.omnilog.php */
