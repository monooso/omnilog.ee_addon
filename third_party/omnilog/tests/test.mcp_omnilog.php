<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog module control panel tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once dirname(__FILE__) .'/../mcp.omnilog.php';
require_once dirname(__FILE__) .'/../classes/omnilog_entry.php';
require_once dirname(__FILE__) .'/../models/omnilog_model.php';

class Test_omnilog_mcp extends Testee_unit_test_case {

  private $_model;
  private $_subject;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function setUp()
  {
    parent::setUp();

    Mock::generate('Omnilog_model', get_class($this) .'_mock_model');

    $this->EE->omnilog_model = $this->_get_mock('model');
    $this->_model   = $this->EE->omnilog_model;
    $this->_subject = new Omnilog_mcp();
  }


  public function test__log__works_with_no_previous_page_and_next_page()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $log_start    = 0;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->EE->input->expectOnce('get', array('start'));
    $this->EE->input->returns('get', FALSE, array('start'));

    $this->_model->expectOnce('get_default_log_limit');
    $this->_model->returns('get_default_log_limit', $log_limit);

    $this->_model->expectOnce('get_log_entries_count');
    $this->_model->returns('get_log_entries_count', $log_limit * 3);

    // Filters.
    $this->EE->input->returns('post', FALSE);
    $this->_model->returns('get_addons_with_an_omnilog_entry', array());
    $this->_model->returns('get_types_with_an_omnilog_entry', array());

    // Retrieve the log entries.
    $this->_model->expectOnce('get_log_entries', array(
      NULL, $log_limit, $log_start, NULL, NULL));
    
    $this->_model->returns('get_log_entries', $log_entries);

    // Construct the view variables.
    $this->EE->config->returns('item', $webmaster, array('webmaster_email'));

    $lang_addon_prompt  = 'Filter by add-on';
    $lang_type_prompt   = 'Filter by entry type';

    $this->EE->lang->returns('line', $lang_addon_prompt,
      array('lbl_filter_by_addon'));

    $this->EE->lang->returns('line', $lang_type_prompt,
      array('lbl_filter_by_type'));

    $view_addons  = array('null' => $lang_addon_prompt);
    $view_types   = array('null' => $lang_type_prompt);

    $this->EE->lang->returns('line', $page_title, array('hd_log'));

    $form_action = 'C=addons_modules'
      .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

    $view_vars = array(
      'addon_filter'      => FALSE,
      'cp_page_title'     => $page_title,
      'filter_addons'     => $view_addons,
      'filter_types'      => $view_types,
      'form_action'       => $form_action,
      'log_entries'       => $log_entries,
      'next_url'          => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=' .$log_limit,
      'previous_url'      => '',
      'type_filter'       => FALSE,
      'webmaster_email'   => $webmaster
    );

    $this->EE->load->expectOnce('view', array('log', $view_vars, TRUE));
    $this->_subject->log();
  }


  public function test__log__works_with_previous_page_and_no_next_page()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $log_start    = 50;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->EE->input->returns('get', $log_start, array('start'));

    $this->_model->returns('get_default_log_limit', $log_limit);
    $this->_model->returns('get_log_entries_count', $log_limit * 1.5);

    // Filters.
    $this->EE->input->returns('post', FALSE);
    $this->_model->returns('get_addons_with_an_omnilog_entry', array());
    $this->_model->returns('get_types_with_an_omnilog_entry', array());

    // Retrieve the log entries.
    $this->_model->expectOnce('get_log_entries', array(
      NULL, $log_limit, $log_start, NULL, NULL));
    
    $this->_model->returns('get_log_entries', $log_entries);

    // Costruct the view variables.
    $this->EE->config->returns('item', $webmaster, array('webmaster_email'));
    $this->EE->lang->returns('line', $page_title, array('hd_log'));

    $lang_addon_prompt  = 'Filter by add-on';
    $lang_type_prompt   = 'Filter by entry type';

    $this->EE->lang->returns('line', $lang_addon_prompt,
      array('lbl_filter_by_addon'));

    $this->EE->lang->returns('line', $lang_type_prompt,
      array('lbl_filter_by_type'));

    $view_addons  = array('null' => $lang_addon_prompt);
    $view_types   = array('null' => $lang_type_prompt);

    $form_action = 'C=addons_modules'
      .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

    $view_vars = array(
      'addon_filter'      => FALSE,
      'cp_page_title'     => $page_title,
      'filter_addons'     => $view_addons,
      'filter_types'      => $view_types,
      'form_action'       => $form_action,
      'log_entries'       => $log_entries,
      'next_url'          => '',
      'previous_url'      => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=0',
      'type_filter'       => FALSE,
      'webmaster_email'   => $webmaster
    );

    $this->EE->load->expectOnce('view', array('log', $view_vars, TRUE));
    $this->_subject->log();
  }


  public function test__log__works_with_previous_page_and_next_page()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $log_start    = 50;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->EE->input->returns('get', $log_start, array('start'));

    $this->_model->returns('get_default_log_limit', $log_limit);
    $this->_model->returns('get_log_entries_count', $log_limit * 3);

    // Filters.
    $this->EE->input->returns('post', FALSE);
    $this->_model->returns('get_addons_with_an_omnilog_entry', array());
    $this->_model->returns('get_types_with_an_omnilog_entry', array());

    // Retrieve the log entries.
    $this->_model->expectOnce('get_log_entries', array(
      NULL, $log_limit, $log_start, NULL, NULL));

    $this->_model->returns('get_log_entries', $log_entries);

    // Costruct the view variables.
    $this->EE->config->returns('item', $webmaster, array('webmaster_email'));
    $this->EE->lang->returns('line', $page_title, array('hd_log'));

    $lang_addon_prompt  = 'Filter by add-on';
    $lang_type_prompt   = 'Filter by entry type';

    $this->EE->lang->returns('line', $lang_addon_prompt,
      array('lbl_filter_by_addon'));

    $this->EE->lang->returns('line', $lang_type_prompt,
      array('lbl_filter_by_type'));

    $view_addons  = array('null' => $lang_addon_prompt);
    $view_types   = array('null' => $lang_type_prompt);

    $form_action = 'C=addons_modules'
      .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

    $view_vars = array(
      'addon_filter'      => FALSE,
      'cp_page_title'     => $page_title,
      'filter_addons'     => $view_addons,
      'filter_types'      => $view_types,
      'form_action'       => $form_action,
      'log_entries'       => $log_entries,
      'next_url'          => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=' .($log_start + $log_limit),
      'previous_url'      => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=0',
      'type_filter'       => FALSE,
      'webmaster_email'   => $webmaster
    );

    $this->EE->load->expectOnce('view', array('log', $view_vars, TRUE));
    $this->_subject->log();
  }


  public function test__log__works_with_addon_filter()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $log_start    = 0;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->EE->input->returns('get', FALSE, array('start'));
    $this->_model->returns('get_default_log_limit', $log_limit);
    $this->_model->returns('get_log_entries_count', $log_limit * 3);

    // Filters.
    $addons = array('Crumbly', 'NSM Better Meta', 'Playa');

    $this->EE->input->returns('post', 'NSM+Better+Meta',
      array('filter_addon', TRUE));

    $this->EE->input->returns('post', FALSE);

    $this->_model->returns('get_addons_with_an_omnilog_entry', $addons);
    $this->_model->returns('get_types_with_an_omnilog_entry', array());

    // Retrieve the log entries.
    $this->_model->expectOnce('get_log_entries', array(
      NULL, $log_limit, $log_start, 'NSM Better Meta', NULL));
    
    $this->_model->returns('get_log_entries', $log_entries);

    // Construct the view variables.
    $this->EE->config->returns('item', $webmaster, array('webmaster_email'));

    $lang_addon_prompt  = 'Filter by add-on';
    $lang_type_prompt   = 'Filter by entry type';

    $this->EE->lang->returns('line', $lang_addon_prompt,
      array('lbl_filter_by_addon'));

    $this->EE->lang->returns('line', $lang_type_prompt,
      array('lbl_filter_by_type'));

    $this->EE->lang->returns('line', $page_title, array('hd_log'));

    $view_addons = array(
      'null'            => $lang_addon_prompt,
      'Crumbly'         => 'Crumbly',
      'NSM+Better+Meta' => 'NSM Better Meta',
      'Playa'           => 'Playa'
    );

    $view_types = array('null' => $lang_type_prompt);

    $form_action = 'C=addons_modules'
      .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

    $view_vars = array(
      'addon_filter'      => 'NSM+Better+Meta',
      'cp_page_title'     => $page_title,
      'filter_addons'     => $view_addons,
      'filter_types'      => $view_types,
      'form_action'       => $form_action,
      'log_entries'       => $log_entries,
      'next_url'          => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=' .$log_limit,
      'previous_url'      => '',
      'type_filter'       => FALSE,
      'webmaster_email'   => $webmaster
    );

    $this->EE->load->expectOnce('view', array('log', $view_vars, TRUE));
    $this->_subject->log();
  }


  public function test__log__works_with_type_filter()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $log_start    = 0;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->EE->input->returns('get', FALSE, array('start'));
    $this->_model->returns('get_default_log_limit', $log_limit);
    $this->_model->returns('get_log_entries_count', $log_limit * 3);

    // Filters.
    $types = array('error', 'notice');

    $this->EE->input->returns('post', 'notice', array('filter_type', TRUE));
    $this->EE->input->returns('post', FALSE);

    $this->_model->returns('get_addons_with_an_omnilog_entry', array());
    $this->_model->returns('get_types_with_an_omnilog_entry', $types);

    // Retrieve the log entries.
    $this->_model->expectOnce('get_log_entries', array(
      NULL, $log_limit, $log_start, NULL, 'notice'));
    
    $this->_model->returns('get_log_entries', $log_entries);

    // Construct the view variables.
    $this->EE->config->returns('item', $webmaster, array('webmaster_email'));

    $lang_addon_prompt  = 'Filter by add-on';
    $lang_type_error    = 'This is the error label';
    $lang_type_notice   = 'This is the notice label';
    $lang_type_prompt   = 'Filter by entry type';

    $this->EE->lang->returns('line', $lang_addon_prompt,
      array('lbl_filter_by_addon'));

    $this->EE->lang->returns('line', $lang_type_error, array('lbl_type_error'));

    $this->EE->lang->returns('line', $lang_type_notice,
      array('lbl_type_notice'));

    $this->EE->lang->returns('line', $lang_type_prompt,
      array('lbl_filter_by_type'));

    $this->EE->lang->returns('line', $page_title, array('hd_log'));

    $view_addons = array('null' => $lang_addon_prompt);

    $view_types = array(
      'null'    => $lang_type_prompt,
      'error'   => $lang_type_error,
      'notice'  => $lang_type_notice
    );

    $form_action = 'C=addons_modules'
      .AMP .'M=show_module_cp'
      .AMP .'module=omnilog';

    $view_vars = array(
      'addon_filter'      => FALSE,
      'cp_page_title'     => $page_title,
      'filter_addons'     => $view_addons,
      'filter_types'      => $view_types,
      'form_action'       => $form_action,
      'log_entries'       => $log_entries,
      'next_url'          => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=' .$log_limit,
      'previous_url'      => '',
      'type_filter'       => 'notice',
      'webmaster_email'   => $webmaster
    );

    $this->EE->load->expectOnce('view', array('log', $view_vars, TRUE));
    $this->_subject->log();
  }
  


}


/* End of file      : test.mcp_omnilog.php */
/* File location    : third_party/omnilog/tests/test.mcp_omnilog.php */
