<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * OmniLogger tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once PATH_THIRD .'omnilog/tests/mocks/mock.omnilog_model' .EXT;
require_once PATH_THIRD .'omnilog/classes/omnilogger' .EXT;

class Test_omnilogger extends Testee_unit_test_case {

	private $_log_entry;
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
        $this->_ee->omnilog_model	= $this->_get_mock('model');
        $this->_model				= $this->_ee->omnilog_model;
		$this->_log_entry			= new Omnilog_entry(array(
			'addon_class'	=> 'Example_addon',
			'addon_name'	=> 'Example Add-on',
			'date'			=> time() - 100,
			'message'		=> 'Example log entry.',
			'type'			=> Omnilog_entry::NOTICE
		));
    }


    public function test__log__success()
    {
		$log_entry_id = 10;
        $this->_model->expectOnce('save_entry_to_log', array($this->_log_entry));
		$this->_model->expectNever('notify_site_admin_of_log_entry');
		$this->_model->setReturnValue('save_entry_to_log', $log_entry_id);
        $this->assertIdentical(TRUE, Omnilogger::log($this->_log_entry));
    }


	public function test__log__failure()
	{
        $this->_model->expectOnce('save_entry_to_log', array($this->_log_entry));
		$this->_model->expectNever('notify_site_admin_of_log_entry');
		$this->_model->setReturnValue('save_entry_to_log', FALSE);
        $this->assertIdentical(FALSE, Omnilogger::log($this->_log_entry));
	}


	public function test__log__success_with_admin_notification()
	{
		$log_entry_id = 10;
        $this->_model->expectOnce('save_entry_to_log', array($this->_log_entry));
		$this->_model->setReturnValue('save_entry_to_log', $log_entry_id);

		$this->_model->expectOnce('notify_site_admin_of_log_entry', array($log_entry_id));
		$this->_model->setReturnValue('notify_site_admin_of_log_entry', TRUE);

        $this->assertIdentical(TRUE, Omnilogger::log($this->_log_entry, TRUE));
	}


	public function test__log__failure_with_admin_notification()
	{
		$log_entry_id = 10;
        $this->_model->expectOnce('save_entry_to_log', array($this->_log_entry));
		$this->_model->setReturnValue('save_entry_to_log', $log_entry_id);

		$this->_model->expectOnce('notify_site_admin_of_log_entry', array($log_entry_id));
		$this->_model->setReturnValue('notify_site_admin_of_log_entry', FALSE);

        $this->assertIdentical(FALSE, Omnilogger::log($this->_log_entry, TRUE));
	}


}


/* End of file      : test.omnilogger.php */
/* File location    : third_party/omnilog/tests/test.omnilogger.php */
