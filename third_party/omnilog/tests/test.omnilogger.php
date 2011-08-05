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

    private $_log_entry_props;
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
        $this->_ee->omnilog_model   = $this->_get_mock('model');
        $this->_model               = $this->_ee->omnilog_model;
        $this->_log_entry_props     = array(
            'addon_name'    => 'Example Add-on',
            'date'          => time() - 100,
            'message'       => 'Example log entry.',
            'notify_admin'  => FALSE,
            'type'          => Omnilog_entry::NOTICE
        );
    }


    public function test__log__success()
    {
        $log_entry = new Omnilog_entry($this->_log_entry_props);

        $saved_entry_props = array_merge($this->_log_entry_props, array('log_entry_id' => 10));
        $saved_entry = new Omnilog_entry($saved_entry_props);

        /**
         * This is driving me spare. If we ask SimpleTest to test that
         * $log_entry was passed to the `save_entry_to_log` method, it
         * throws out of memory errors.
         *
         * It never used to, and I have no idea what the root cause of
         * the problem is. Suffice to say that I don't have time to
         * debug my bloody testing framework.
         *
         * This rant applies to all the tests in this class.
         */

        // $this->_model->expectOnce('save_entry_to_log', array($log_entry));

        $this->_model->expectOnce('save_entry_to_log', array('*'));
        $this->_model->expectNever('notify_site_admin_of_log_entry');
        $this->_model->setReturnValue('save_entry_to_log', $saved_entry);

        $this->assertIdentical(TRUE, Omnilogger::log($log_entry));
    }


    public function test__log__failure()
    {
        $exception_message = 'Exception';
        $log_entry = new Omnilog_entry($this->_log_entry_props);

        // $this->_model->expectOnce('save_entry_to_log', array($log_entry));

        $this->_model->expectOnce('save_entry_to_log', array('*'));
        $this->_model->expectNever('notify_site_admin_of_log_entry');

        $this->_model->throwOn('save_entry_to_log', new Exception($exception_message));

        $this->assertIdentical(FALSE, Omnilogger::log($log_entry));
    }


    public function test__log__success_with_admin_notification()
    {
        $log_entry = new Omnilog_entry($this->_log_entry_props);
        $log_entry->set_notify_admin(TRUE);

        $saved_entry_props = array_merge($log_entry->to_array(), array('log_entry_id' => 10));
        $saved_entry = new Omnilog_entry($saved_entry_props);

        // $this->_model->expectOnce('save_entry_to_log', array($log_entry));
        $this->_model->expectOnce('save_entry_to_log', array('*'));
        $this->_model->setReturnValue('save_entry_to_log', $saved_entry);

        // $this->_model->expectOnce('notify_site_admin_of_log_entry', array($saved_entry));
        $this->_model->expectOnce('notify_site_admin_of_log_entry', array('*'));

        $this->assertIdentical(TRUE, Omnilogger::log($log_entry));
    }


    public function test__log__failure_with_admin_notification()
    {
        $exception_message = 'Exception';
        $log_entry = new Omnilog_entry($this->_log_entry_props);
        $log_entry->set_notify_admin(TRUE);

        // $this->_model->expectOnce('save_entry_to_log', array($log_entry));
        $this->_model->expectOnce('save_entry_to_log', array('*'));
        $this->_model->expectNever('notify_site_admin_of_log_entry');
        $this->_model->throwOn('save_entry_to_log', new Exception($exception_message));

        $this->assertIdentical(FALSE, Omnilogger::log($log_entry));
    }


    public function test__log__notify_admin_failure()
    {
        $exception_message = 'Exception';
        $log_entry = new Omnilog_entry($this->_log_entry_props);
        $log_entry->set_notify_admin(TRUE);

        $saved_entry_props = array_merge($log_entry->to_array(), array('log_entry_id' => 10));
        $saved_entry = new Omnilog_entry($saved_entry_props);

        // $this->_model->expectOnce('save_entry_to_log', array($log_entry));
        $this->_model->expectOnce('save_entry_to_log', array('*'));
        $this->_model->setReturnValue('save_entry_to_log', $saved_entry);

        // $this->_model->expectOnce('notify_site_admin_of_log_entry', array($saved_entry));
        $this->_model->expectOnce('notify_site_admin_of_log_entry', array('*'));
        $this->_model->throwOn('notify_site_admin_of_log_entry', new Exception($exception_message));

        $this->assertIdentical(FALSE, Omnilogger::log($log_entry));
    }


}


/* End of file      : test.omnilogger.php */
/* File location    : third_party/omnilog/tests/test.omnilogger.php */
