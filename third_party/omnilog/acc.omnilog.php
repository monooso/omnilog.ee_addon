<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog accessory.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

class Omnilog_acc {

  private $EE;
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
    $this->EE =& get_instance();

    // Tell EE where to look.
    $this->EE->lang->loadfile('omnilog');
    $this->EE->load->add_package_path(PATH_THIRD .'omnilog/');

    // Load the model.
    $this->EE->load->model('omnilog_model');
    $this->_model = $this->EE->omnilog_model;

    // Update the package, if required.
    $this->_model->update_package($this->_model->get_installed_version(), TRUE);

    // Instance properties.
    $this->description  = $this->EE->lang->line('omnilog_module_description');
    $this->id           = $this->_model->get_package_name();
    $this->name         = $this->EE->lang->line('omnilog_module_name');
    $this->sections     = array();
    $this->version      = $this->_model->get_package_version();

    // Basic stuff required by every view.
    $this->_base_qs = 'C=addons_modules' .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

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
    $theme_css  = $this->EE->config->item('cp_theme') == 'corporate'
      ? $this->_theme_url .'corporate/css/cp.css'
      : $this->_theme_url .'default/css/cp.css';

    $this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'
      .$common_css .'" />');

    $this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'
      .$theme_css .'" />');

    $vars = array(
      'log_entries'     => $this->_model->get_log_entries(NULL, 10),
      'webmaster_email' => $this->EE->config->item('webmaster_email')
    );

    // Language strings required by JS.
    $this->EE->load->library('javascript');

    $this->EE->javascript->set_global('omnilog.lang', array(
      'lblShow' => $this->EE->lang->line('lbl_show'),
      'lblHide' => $this->EE->lang->line('lbl_hide')
    ));

    $this->EE->javascript->compile();

    $this->EE->cp->add_to_foot('<script type="text/javascript" src="'
      .$this->_theme_url .'common/js/cp.js"></script>');

    $this->sections[$this->EE->lang->line('hd_log')]
      = $this->EE->load->view('accessory', $vars, TRUE);
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
