<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * OmniLog module.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

class Omnilog {
    
    private $_ee;
    private $_model;
    public $return_data = '';
    
    
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
        $this->_ee->load->model('omnilog_model');
        $this->_model = $this->_ee->omnilog_model;
    }


}


/* End of file      : mod.omnilog.php */
/* File location    : third_party/omnilog/mod.omnilog.php */
