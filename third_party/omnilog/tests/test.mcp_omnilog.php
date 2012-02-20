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
require_once dirname(__FILE__) .'/../tests/mocks/mock.omnilog_model.php';

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
      
      Mock::generate('Mock_omnilog_model', get_class($this) .'_mock_model');
      $this->_ee->omnilog_model = $this->_get_mock('model');
      $this->_model   = $this->_ee->omnilog_model;
      $this->_subject = new Omnilog_mcp();
  }


  public function test__log__works_with_no_previous_page_and_next_page()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->_ee->input->expectOnce('get', array('start'));
    $this->_ee->input->setReturnValue('get', FALSE, array('start'));

    $this->_model->expectOnce('get_default_log_limit');
    $this->_model->setReturnValue('get_default_log_limit', $log_limit);

    $this->_model->expectOnce('get_log_entries_count');
    $this->_model->setReturnValue('get_log_entries_count', $log_limit * 3);

    $view       = 'log';
    $view_vars  = array(
      'cp_page_title'     => $page_title,
      'log_entries'       => $log_entries,
      'next_url'          => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=' .$log_limit,
      'previous_url'      => '',
      'webmaster_email'   => $webmaster
    );

    $this->_ee->lang->expectAtLeastOnce('line');
    $this->_ee->lang->setReturnValue('line', $page_title, array('hd_log'));

    $this->_ee->config->setReturnValue('item',
      $webmaster, array('webmaster_email'));

    $this->_model->expectOnce('get_log_entries');
    $this->_model->setReturnValue('get_log_entries', $log_entries);

    $this->_ee->load->expectOnce('view', array($view, $view_vars, TRUE));
    $this->_subject->log();
  }


  public function test__log__works_with_previous_page_and_no_next_page()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $log_start    = 50;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->_ee->input->expectAtLeastOnce('get', array('start'));
    $this->_ee->input->setReturnValue('get', $log_start, array('start'));

    $this->_model->expectOnce('get_default_log_limit');
    $this->_model->setReturnValue('get_default_log_limit', $log_limit);

    $this->_model->expectOnce('get_log_entries_count');
    $this->_model->setReturnValue('get_log_entries_count', $log_limit * 1.5);

    $view       = 'log';
    $view_vars  = array(
      'cp_page_title'     => $page_title,
      'log_entries'       => $log_entries,
      'next_url'          => '',
      'previous_url'      => BASE .AMP
        .'C=addons_modules' .AMP
        .'M=show_module_cp' .AMP
        .'module=omnilog' .AMP
        .'start=0',
      'webmaster_email'   => $webmaster
    );

    $this->_ee->lang->expectAtLeastOnce('line');
    $this->_ee->lang->setReturnValue('line', $page_title, array('hd_log'));

    $this->_ee->config->setReturnValue('item',
      $webmaster, array('webmaster_email'));

    $this->_model->expectOnce('get_log_entries');
    $this->_model->setReturnValue('get_log_entries', $log_entries);

    $this->_ee->load->expectOnce('view', array($view, $view_vars, TRUE));
    $this->_subject->log();
  }


  public function test__log__works_with_previous_page_and_next_page()
  {
    $log_entries  = array('a', 'b', 'c');
    $log_limit    = 50;
    $log_start    = 50;
    $page_title   = 'Example Page Title';
    $webmaster    = 'webmaster@website.com';

    $this->_ee->input->expectAtLeastOnce('get', array('start'));
    $this->_ee->input->setReturnValue('get', $log_start, array('start'));

    $this->_model->expectOnce('get_default_log_limit');
    $this->_model->setReturnValue('get_default_log_limit', $log_limit);

    $this->_model->expectOnce('get_log_entries_count');
    $this->_model->setReturnValue('get_log_entries_count', $log_limit * 3);

    $view       = 'log';
    $view_vars  = array(
      'cp_page_title'     => $page_title,
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
      'webmaster_email'   => $webmaster
    );

    $this->_ee->lang->expectAtLeastOnce('line');
    $this->_ee->lang->setReturnValue('line', $page_title, array('hd_log'));

    $this->_ee->config->setReturnValue('item',
      $webmaster, array('webmaster_email'));

    $this->_model->expectOnce('get_log_entries');
    $this->_model->setReturnValue('get_log_entries', $log_entries);

    $this->_ee->load->expectOnce('view', array($view, $view_vars, TRUE));
    $this->_subject->log();
  }


}


/* End of file      : test.mcp_omnilog.php */
/* File location    : third_party/omnilog/tests/test.mcp_omnilog.php */
