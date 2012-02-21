<?php if ( ! defined('BASEPATH')) exit('Direct script access not permitted.');

/**
 * OmniLog module.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

class Omnilog {

  private $EE;
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
    $this->EE =& get_instance();
    $this->EE->load->model('omnilog_model');
    $this->_model = $this->EE->omnilog_model;
  }


}


/* End of file      : mod.omnilog.php */
/* File location    : third_party/omnilog/mod.omnilog.php */
