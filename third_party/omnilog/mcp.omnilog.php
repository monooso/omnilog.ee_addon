<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog module control panel.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

class Omnilog_mcp {

  private $EE;
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
    $this->EE =& get_instance();

    $this->EE->load->model('omnilog_model');
    $this->_model = $this->EE->omnilog_model;

    $this->_base_qs = 'C=addons_modules' .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

    $this->_base_url    = BASE .AMP .$this->_base_qs;
    $this->_theme_url   = $this->_model->get_package_theme_url();

    $common_css = $this->_theme_url .'common/css/cp.css';
    $theme_css  = $this->EE->config->item('cp_theme') == 'corporate'
      ? $this->_theme_url .'corporate/css/cp.css'
      : $this->_theme_url .'default/css/cp.css';

    $this->EE->cp->add_to_head('<link rel="stylesheet" href="'
      .$common_css .'" />');

    $this->EE->cp->add_to_head('<link rel="stylesheet" href="'
      .$theme_css .'" />');

    $this->EE->cp->set_breadcrumb($this->_base_url,
      $this->EE->lang->line('omnilog_module_name'));

    $this->EE->cp->set_right_nav(array(
      'nav_log'   => $this->_base_url .AMP .'method=log',
      'nav_demo'  => $this->_base_url .AMP .'method=demo',
      'nav_reset' => $this->_base_url .AMP .'method=clear_log'
    ));
  }


  /**
   * Clears all the log entries for the current site.
   *
   * @access  public
   * @return  string
   */
  public function clear_log()
  {
    $this->_model->clear_log();
    return $this->log();
  }


  /**
   * OmniLog 'demo' homepage.
   *
   * @access  public
   * @return  string
   */
  public function demo()
  {
    $this->EE->load->library('javascript');
    $this->EE->cp->add_to_foot('<script src="'
      .$this->_theme_url .'common/js/cp.js"></script>');

    $vars = array(
      'cp_page_title'   => $this->EE->lang->line('hd_demo'),
      'run_demo_url'    => $this->_base_url .AMP .'method=run_demo',
      'webmaster_email' => $this->EE->config->item('webmaster_email')
    );
    
    return $this->EE->load->view('demo', $vars, TRUE);
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
    $this->EE->load->helper('form');

    $log_count  = $this->_model->get_log_entries_count();
    $log_limit  = $this->_model->get_default_log_limit();

    /**
     * Retrieve the matching log entries:
     * - Filtered by entry type.
     * - Filtered by add-on.
     * - Paginated.
     */

    $in = $this->EE->input;

    $post_addon = $in->post('filter_addon', TRUE);
    $post_type  = $in->post('filter_type', TRUE);

    $addon_filter = ($post_addon && $post_addon != 'null')
      ? urldecode($post_addon) : NULL;

    $type_filter = ($post_type && $post_type != 'null')
      ? urldecode($post_type) : NULL;

    $log_start = ($addon_filter OR $type_filter)
      ? 0 : (valid_int($in->get('start'), 0) ? (int) $in->get('start') : 0);

    $log_entries = $this->_model->get_log_entries(NULL, $log_limit, $log_start,
      $addon_filter, $type_filter);


    // Prepare the log pagination navigation.
    $next_url = ($log_start + $log_limit) < $log_count
      ? $this->_base_url .AMP .'start=' .($log_start + $log_limit)
      : '';

    $previous_url = $log_start > 0
      ? $this->_base_url .AMP .'start=' .(max(($log_start - $log_limit), 0))
      : '';


    // Prepare the log filter options.
    $addons = $this->_model->get_addons_with_an_omnilog_entry();
    $types  = $this->_model->get_types_with_an_omnilog_entry();

    $view_addons = array(
      'null' => $this->EE->lang->line('lbl_filter_by_addon'));

    foreach ($addons AS $addon)
    {
      $view_addons[urlencode($addon)] = $addon;
    }

    $view_types = array('null' => $this->EE->lang->line('lbl_filter_by_type'));

    foreach ($types AS $type)
    {
      $view_types[urlencode($type)] = $this->EE->lang->line('lbl_type_' .$type);
    }


    // Prepare the view variables.
    $vars = array(
      'addon_filter'    => $post_addon,
      'cp_page_title'   => $this->EE->lang->line('hd_log'),
      'filter_addons'   => $view_addons,
      'filter_types'    => $view_types,
      'form_action'     => $this->_base_qs,
      'log_entries'     => $log_entries,
      'next_url'        => $next_url,
      'previous_url'    => $previous_url,
      'type_filter'     => $post_type,
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

    return $this->EE->load->view('log', $vars, TRUE);
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
      $this->EE->session->set_flashdata('message_failure',
        $this->EE->lang->line('demo_flashdata_missing_omnilogger')
      );

      $this->EE->functions->redirect($redirect_url);
    }

    include_once PATH_THIRD .'omnilog/classes/omnilogger' .EXT;
    $this->EE->load->helper('email');
    
    $demo   = $this->EE->input->get('demo');
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
        $emails = array($this->EE->config->item('webmaster_email'));
        $notify = TRUE;
        $type   = Omnilog_entry::ERROR;
        break;

      case 'notify_custom':
        $emails     = array();
        $raw_emails = explode(',', urldecode($this->EE->input->get('email')));

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
      $this->EE->session->set_flashdata('message_failure',
        $this->EE->lang->line('demo_flashdata_unknown_demo')
      );

      $this->EE->functions->redirect($redirect_url);
    }

    $omnilog_entry = new Omnilog_entry(array(
      'addon_name'    => 'OmniLog Demo',
      'admin_emails'  => $emails,
      'date'          => time(),
      'message'       => $this->EE->lang->line('demo_message'),
      'extended_data' => $this->EE->lang->line('demo_extended_data'),
      'notify_admin'  => $notify,
      'type'          => $type
    ));

    if ( ! Omnilogger::log($omnilog_entry))
    {
      $this->EE->session->set_flashdata('message_failure',
        $this->EE->lang->line('demo_flashdata_failure')
      );
    }
    else
    {
      $this->EE->session->set_flashdata('message_success',
        $this->EE->lang->line('demo_flashdata_success')
      );
    }

    $this->EE->functions->redirect($redirect_url);
  }


}


/* End of file      : mcp.omnilog.php */
/* File location    : third_party/omnilog/mcp.omnilog.php */
