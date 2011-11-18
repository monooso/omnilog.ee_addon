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

    $this->_ee->load->model('omnilog_model');
    $this->_model = $this->_ee->omnilog_model;

    $this->_base_qs = 'C=addons_modules' .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

    $this->_base_url    = BASE .AMP .$this->_base_qs;
    $this->_theme_url   = $this->_model->get_package_theme_url();

    $common_css = $this->_theme_url .'common/css/cp.css';
    $theme_css  = $this->_ee->config->item('cp_theme') == 'corporate'
      ? $this->_theme_url .'corporate/css/cp.css'
      : $this->_theme_url .'default/css/cp.css';

    $this->_ee->cp->add_to_head('<link rel="stylesheet" href="'
      .$common_css .'" />');

    $this->_ee->cp->add_to_head('<link rel="stylesheet" href="'
      .$theme_css .'" />');

    $this->_ee->cp->set_breadcrumb($this->_base_url,
      $this->_ee->lang->line('omnilog_module_name'));

    $this->_ee->cp->set_right_nav(array(
      'nav_log'   => $this->_base_url .AMP .'method=log',
      'nav_demo'  => $this->_base_url .AMP .'method=demo'
    ));
  }


  /**
   * OmniLog 'demo' homepage.
   *
   * @access  public
   * @return  string
   */
  public function demo()
  {
    $this->_ee->load->library('javascript');
    $this->_ee->cp->add_to_foot('<script src="'
      .$this->_theme_url .'common/js/cp.js"></script>');

    $vars = array(
      'cp_page_title'   => $this->_ee->lang->line('hd_demo'),
      'run_demo_url'    => $this->_base_url .AMP .'method=run_demo',
      'webmaster_email' => $this->_ee->config->item('webmaster_email')
    );
    
    return $this->_ee->load->view('demo', $vars, TRUE);
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
    $log_count  = $this->_model->get_log_entries_count();
    $log_limit  = $this->_model->get_default_log_limit();

    $log_start  = valid_int($this->_ee->input->get('start'), 0)
      ? (int) $this->_ee->input->get('start') : 0;

    $log_entries = $this->_model->get_log_entries(NULL, $log_limit, $log_start);

    $next_url = ($log_start + $log_limit) < $log_count
      ? $this->_base_url .AMP .'start=' .($log_start + $log_limit)
      : '';

    $previous_url = $log_start > 0
      ? $this->_base_url .AMP .'start=' .(max(($log_start - $log_limit), 0))
      : '';

    $vars = array(
      'cp_page_title'   => $this->_ee->lang->line('hd_log'),
      'log_entries'     => $log_entries,
      'next_url'        => $next_url,
      'previous_url'    => $previous_url,
      'webmaster_email' => $this->_ee->config->item('webmaster_email')
    );

    // Language strings required by JS.
    $this->_ee->load->library('javascript');

    $this->_ee->javascript->set_global('omnilog.lang', array(
      'lblShow' => $this->_ee->lang->line('lbl_show'),
      'lblHide' => $this->_ee->lang->line('lbl_hide')
    ));

    $this->_ee->javascript->compile();
    
    $this->_ee->cp->add_to_foot('<script type="text/javascript" src="'
      .$this->_theme_url .'common/js/cp.js"></script>');

    return $this->_ee->load->view('log', $vars, TRUE);
  }


  /**
   * Runs a demo.
   *
   * @access  public
   * @return  void
   */
  public function run_demo()
  {
    $redirect_url = $this->_base_url .AMP .'method=demo';

    if ( ! file_exists(PATH_THIRD .'omnilog/classes/omnilogger' .EXT))
    {
      $this->_ee->session->set_flashdata('message_failure',
        $this->_ee->lang->line('demo_flashdata_missing_omnilogger')
      );

      $this->_ee->functions->redirect($redirect_url);
    }

    include_once PATH_THIRD .'omnilog/classes/omnilogger' .EXT;
    $this->_ee->load->helper('email');
    
    $demo   = $this->_ee->input->get('demo');
    $emails = array();

    switch ($demo)
    {
      case 'log_error':
        $notify = FALSE;
        $type   = Omnilog_entry::ERROR;
        break;

      case 'log_notice':
        $notify = FALSE;
        $type   = Omnilog_entry::NOTICE;
        break;

      case 'log_warning':
        $notify = FALSE;
        $type   = Omnilog_entry::WARNING;
        break;

      case 'notify_standard':
        $emails = array($this->_ee->config->item('webmaster_email'));
        $notify = TRUE;
        $type   = Omnilog_entry::ERROR;
        break;

      case 'notify_custom':
        $emails     = array();
        $raw_emails = explode(',', urldecode($this->_ee->input->get('email')));

        foreach ($raw_emails AS $email)
        {
          $emails[] = trim($email);
        }

        $notify = TRUE;
        $type   = Omnilog_entry::ERROR;
        break;

      default:
        $notify = FALSE;
        $type   = FALSE;
        break;
    }

    if ( ! $type)
    {
      $this->_ee->session->set_flashdata('message_failure',
        $this->_ee->lang->line('demo_flashdata_unknown_demo')
      );

      $this->_ee->functions->redirect($redirect_url);
    }

    $omnilog_entry = new Omnilog_entry(array(
      'addon_name'    => 'OmniLog Demo',
      'admin_emails'  => $emails,
      'date'          => time(),
      'message'       => $this->_ee->lang->line('demo_message'),
      'extended_data' => $this->_ee->lang->line('demo_extended_data'),
      'notify_admin'  => $notify,
      'type'          => $type
    ));

    if ( ! Omnilogger::log($omnilog_entry))
    {
      $this->_ee->session->set_flashdata('message_failure',
        $this->_ee->lang->line('demo_flashdata_failure')
      );
    }
    else
    {
      $this->_ee->session->set_flashdata('message_success',
        $this->_ee->lang->line('demo_flashdata_success')
      );
    }

    $this->_ee->functions->redirect($redirect_url);
  }


}


/* End of file      : mcp.omnilog.php */
/* File location    : third_party/omnilog/mcp.omnilog.php */
