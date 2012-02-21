<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog module update tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once dirname(__FILE__) .'/../upd.omnilog.php';
require_once dirname(__FILE__) .'/../classes/omnilog_entry.php';
require_once dirname(__FILE__) .'/../models/omnilog_model.php';

class Test_omnilog_upd extends Testee_unit_test_case {

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

    $this->_ee->omnilog_model = $this->_get_mock('model');
    $this->_model   = $this->_ee->omnilog_model;
    $this->_subject = new Omnilog_upd();
  }


  public function test__install__success()
  {
    $this->_model->expectOnce('install_module');
    $this->_model->setReturnValue('install_module', 'wibble');
    $this->assertIdentical('wibble', $this->_subject->install());
  }


  public function test__uninstall__success()
  {
    $this->_model->expectOnce('uninstall_module');
    $this->_model->setReturnValue('uninstall_module', 'wibble');
    $this->assertIdentical('wibble', $this->_subject->uninstall());
  }


  public function test__update__success()
  {
    $installed_version  = '1.0.0';
    $return_value       = 'Huzzah!';    // Should just be passed along.

    $this->_model->expectOnce('update_package', array($installed_version));
    $this->_model->setReturnValue('update_package', $return_value);

    $this->assertIdentical($return_value,
      $this->_subject->update($installed_version));
  }


}


/* End of file      : test.upd_omnilog.php */
/* File location    : third_party/omnilog/tests/test.upd_omnilog.php */
