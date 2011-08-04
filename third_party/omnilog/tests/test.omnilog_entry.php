<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * OmniLog Entry tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once PATH_THIRD .'omnilog/classes/omnilog_entry.php';

class Test_omnilog_entry extends Testee_unit_test_case {

    private $_props;
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

        $this->_props = array(
            'addon_name'    => 'Example Add-on',
            'admin_emails'  => array('adam@ants.com', 'bob@dylan.com'),
            'date'          => time() - 1000,
            'log_entry_id'  => 100,
            'message'       => 'Example log entry.',
            'notify_admin'  => TRUE,
            'type'          => Omnilog_entry::WARNING
        );

        $this->_ee->email->setReturnValue('valid_email', TRUE);
        $this->_subject = new Omnilog_entry($this->_props);
    }


    public function test__constructor__success()
    {
        foreach ($this->_props AS $prop_name => $prop_value)
        {
            $method_name = 'get_' .$prop_name;
            $this->assertIdentical($prop_value, $this->_subject->$method_name());
        }
    }


    public function test__admin_emails__no_admin_emails()
    {
        unset($this->_props['admin_emails']);
        $subject = new Omnilog_entry($this->_props);

        $this->assertIdentical(array(), $subject->get_admin_emails());
    }


    public function test__constructor__unknown_property()
    {
        // If this doesn't throw an error, it's worked.
        $props = array_merge($this->_props, array('dummy_property' => 'invalid'));
        new Omnilog_entry($props);
    }


    public function test__is_populated__populated_without_entry_id()
    {
        unset($this->_props['log_entry_id']);
        $subject = new Omnilog_entry($this->_props);
        $this->assertIdentical(TRUE, $subject->is_populated());
    }


    public function test__is_populated__populated_with_entry_id()
    {
        $this->assertIdentical(TRUE, $this->_subject->is_populated(TRUE));
    }


    public function test__is_populated__not_populated_without_entry_id()
    {
        unset($this->_props['log_entry_id']);

        // Add-on name.
        $props = array_merge($this->_props, array('addon_name' => ''));
        $subject = new Omnilog_entry($props);
        $this->assertIdentical(FALSE, $subject->is_populated());

        // Date.
        $props = array_merge($this->_props, array('date' => 0));
        $subject = new Omnilog_entry($props);
        $this->assertIdentical(FALSE, $subject->is_populated());

        // Message.
        $props = array_merge($this->_props, array('message' => ''));
        $subject = new Omnilog_entry($props);
        $this->assertIdentical(FALSE, $subject->is_populated());

        // Type.
        $props = array_merge($this->_props, array('type' => 0));
        $subject = new Omnilog_entry($props);
        $this->assertIdentical(FALSE, $subject->is_populated());
    }


    public function test__is_populated__not_populated_with_entry_id()
    {
        unset($this->_props['log_entry_id']);
        $subject = new Omnilog_entry($this->_props);
        $this->assertIdentical(FALSE, $subject->is_populated(TRUE));
    }


    public function test__reset__success()
    {
        $result = $this->_subject->reset();

        $this->assertIdentical('', $result->get_addon_name());
        $this->assertIdentical(array(), $result->get_admin_emails());
        $this->assertIdentical(0, $result->get_date());
        $this->assertIdentical(0, $result->get_log_entry_id());
        $this->assertIdentical('', $result->get_message());
        $this->assertIdentical(FALSE, $result->get_notify_admin());
        $this->assertIdentical(Omnilog_entry::NOTICE, $result->get_type());
    }


    public function test__set_addon_name__invalid_values()
    {
        $this->assertIdentical($this->_props['addon_name'], $this->_subject->set_addon_name(123));
        $this->assertIdentical($this->_props['addon_name'], $this->_subject->set_addon_name(FALSE));
        $this->assertIdentical($this->_props['addon_name'], $this->_subject->set_addon_name(new StdClass()));
        $this->assertIdentical($this->_props['addon_name'], $this->_subject->set_addon_name(NULL));
    }


    public function test__set_admin_emails__invalid_emails()
    {
        $invalid_email  = 'bob';
        $valid_email    = 'bob@bob.com';

        $this->_ee->email->setReturnValueAt(2, 'valid_email', FALSE);
        $this->_ee->email->setReturnValueAt(3, 'valid_email', TRUE);

        $this->assertIdentical(array($valid_email), $this->_subject->set_admin_emails(array($invalid_email, $valid_email)));
    }


    public function test__set_date__invalid_values()
    {
        $this->assertIdentical($this->_props['date'], $this->_subject->set_date('Invalid'));
        $this->assertIdentical($this->_props['date'], $this->_subject->set_date(FALSE));
        $this->assertIdentical($this->_props['date'], $this->_subject->set_date(new StdClass()));
        $this->assertIdentical($this->_props['date'], $this->_subject->set_date(NULL));
        $this->assertIdentical($this->_props['date'], $this->_subject->set_date(time() + 100));
        $this->assertIdentical($this->_props['date'], $this->_subject->set_date(-100));
    }


    public function test__set_log_entry_id__invalid_values()
    {
        $this->assertIdentical($this->_props['log_entry_id'], $this->_subject->set_log_entry_id('Invalid'));
        $this->assertIdentical($this->_props['log_entry_id'], $this->_subject->set_log_entry_id(FALSE));
        $this->assertIdentical($this->_props['log_entry_id'], $this->_subject->set_log_entry_id(new StdClass()));
        $this->assertIdentical($this->_props['log_entry_id'], $this->_subject->set_log_entry_id(NULL));
        $this->assertIdentical($this->_props['log_entry_id'], $this->_subject->set_log_entry_id(0));
    }


    public function test__set_message__invalid_values()
    {
        $this->assertIdentical($this->_props['message'], $this->_subject->set_message(123));
        $this->assertIdentical($this->_props['message'], $this->_subject->set_message(FALSE));
        $this->assertIdentical($this->_props['message'], $this->_subject->set_message(new StdClass()));
        $this->assertIdentical($this->_props['message'], $this->_subject->set_message(NULL));
    }


    public function test__set_notify_admin__invalid_values()
    {
        $this->assertIdentical($this->_props['notify_admin'], $this->_subject->set_notify_admin(123));
        $this->assertIdentical($this->_props['notify_admin'], $this->_subject->set_notify_admin('Invalid'));
        $this->assertIdentical($this->_props['notify_admin'], $this->_subject->set_notify_admin(new StdClass()));
        $this->assertIdentical($this->_props['notify_admin'], $this->_subject->set_notify_admin(NULL));
    }


    public function test__set_type__invalid_values()
    {
        $this->assertIdentical($this->_props['type'], $this->_subject->set_type('Invalid'));
        $this->assertIdentical($this->_props['type'], $this->_subject->set_type(FALSE));
        $this->assertIdentical($this->_props['type'], $this->_subject->set_type(new StdClass()));
        $this->assertIdentical($this->_props['type'], $this->_subject->set_type(NULL));
        $this->assertIdentical($this->_props['type'], $this->_subject->set_type(20));
    }


    public function test__to_array__success_without_entry_id()
    {
        unset($this->_props['log_entry_id']);
        $this->assertIdentical($this->_props, $this->_subject->to_array());
    }


    public function test__to_array__success_with_entry_id()
    {
        $result = $this->_subject->to_array(TRUE);
        ksort($result);

        $this->assertIdentical($this->_props, $result);
    }


}


/* End of file      : test.omnilog_entry.php */
/* File location    : third_party/omnilog/tests/test.omnilog_entry.php */
