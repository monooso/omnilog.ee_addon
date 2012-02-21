<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog model tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once dirname(__FILE__) .'/../models/omnilog_model.php';

// Helpers (not mocked).
require_once BASEPATH .'helpers/text_helper.php';
require_once APPPATH .'helpers/EE_text_helper.php';

class Test_omnilog_model extends Testee_unit_test_case {

  private $_package_name;
  private $_package_version;
  private $_site_id;
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

      $this->_package_name    = 'example_package';
      $this->_package_version = '1.0.0';

      $this->_site_id = 10;
      $this->_ee->config->setReturnValue('item', $this->_site_id, array('site_id'));

      $this->_subject = new Omnilog_model($this->_package_name, $this->_package_version);
  }


  public function test__constructor__package_name_and_version()
  {
      $package_name       = 'Example_package';
      $package_version    = '1.0.0';

      $subject = new Omnilog_model($package_name, $package_version);
      $this->assertIdentical(strtolower($package_name), $subject->get_package_name());
      $this->assertIdentical($package_version, $subject->get_package_version());
  }


  public function test__get_installed_version__success()
  {
      $db         = $this->_ee->db;
      $version    = '1.0.0';
      $db_result  = $this->_get_mock('db_query');
      $db_row     = new StdClass();

      $db_row->module_version = $version;

      $db->expectOnce('select', array('module_version'));
      $db->expectOnce('get_where', array('modules', array('module_name' => $this->_package_name), 1));

      $db->setReturnReference('get_where', $db_result);

      $db_result->expectOnce('num_rows');
      $db_result->setReturnValue('num_rows', 1);

      $db_result->expectOnce('row');
      $db_result->setReturnValue('row', $db_row);
  
      $this->assertIdentical($version, $this->_subject->get_installed_version());
  }


  public function test__get_installed_version__not_installed()
  {
      $db         = $this->_ee->db;
      $db_result  = $this->_get_mock('db_query');

      $db->expectOnce('select', array('module_version'));
      $db->expectOnce('get_where', array('modules', array('module_name' => $this->_package_name), 1));

      $db->setReturnReference('get_where', $db_result);
      $db_result->expectOnce('num_rows');
      $db_result->setReturnValue('num_rows', 0);
      $db_result->expectNever('row');
  
      $this->assertIdentical('', $this->_subject->get_installed_version());
  }


  public function test__get_log_entries__success_default_site_id()
  {
    $db     = $this->_ee->db;
    $limit  = $this->_subject->get_default_log_limit();

    $select_fields = 'addon_name, admin_emails, date, log_entry_id, message,
      extended_data, notify_admin, type';

    $db->expectOnce('select',
      array(new EqualWithoutWhitespaceExpectation($select_fields)));

    $db->expectOnce('where', array(array('site_id' => $this->_site_id)));
    $db->expectOnce('order_by', array('log_entry_id', 'desc'));
    $db->expectOnce('get', array('omnilog_entries', $limit, 0));

    $db_result = $this->_get_mock('db_query');
    $db_rows    = array(
      array(
        'addon_name'    => 'Example A',
        'admin_emails'  => 'adam@ants.com|bob@dylan.com',
        'date'          => time() - 5000,
        'log_entry_id'  => '10',
        'message'       => 'Example message A-A',
        'extended_data' => 'Example extended data A-A',
        'notify_admin'  => 'n',
        'type'          => Omnilog_entry::NOTICE
      ),
      array(
        'addon_name'    => 'Example A',
        'admin_emails'  => '',
        'date'          => time() - 4000,
        'log_entry_id'  => '20',
        'message'       => 'Example message A-B',
        'extended_data' => 'Example extended data A-B',
        'notify_admin'  => 'y',
        'type'          => Omnilog_entry::ERROR
      ),
      array(
        'addon_name'    => 'Example B',
        'admin_emails'  => 'chas@dave.com|eric@roberts.com|dead@weather.com',
        'date'          => time() - 3000,
        'log_entry_id'  => '30',
        'message'       => 'Example message B-A',
        'extended_data' => 'Example extended data B-A',
        'notify_admin'  => 'n',
        'type'          => Omnilog_entry::WARNING
      )
    );

    $db->setReturnReference('get', $db_result);
    $db_result->expectOnce('result_array');
    $db_result->setReturnValue('result_array', $db_rows);

    $expected_result = array();
    foreach ($db_rows AS $db_row)
    {
      $db_row['admin_emails'] = explode('|', $db_row['admin_emails']);
      $db_row['notify_admin'] = (strtolower($db_row['notify_admin']) === 'y');
      $expected_result[]      = new Omnilog_entry($db_row);
    }

    $actual_result = $this->_subject->get_log_entries();

    $this->assertIdentical(count($expected_result), count($actual_result));
    for ($count = 0, $length = count($expected_result); $count < $length; $count++)
    {
      $this->assertIdentical(
        $expected_result[$count]->to_array(TRUE),
        $actual_result[$count]->to_array(TRUE)
      );
    }
  }


  public function test__get_log_entries__success_custom_site_id()
  {
    $db       = $this->_ee->db;
    $site_id  = 999;
    $limit    = $this->_subject->get_default_log_limit();

    $select_fields = 'addon_name, admin_emails, date, log_entry_id, message,
      extended_data, notify_admin, type';

    $db->expectOnce('select',
      array(new EqualWithoutWhitespaceExpectation($select_fields)));

    $db->expectOnce('where', array(array('site_id' => $site_id)));
    $db->expectOnce('order_by', array('log_entry_id', 'desc'));
    $db->expectOnce('get', array('omnilog_entries', $limit, 0));

    $db_result = $this->_get_mock('db_query');
    $db_rows = array(
      array(
        'addon_name'    => 'Example A',
        'admin_emails'  => '',
        'date'          => time() - 3000,
        'log_entry_id'  => '10',
        'message'       => 'Example message A-A',
        'extended_data' => 'Example extended data A-A',
        'notify_admin'  => 'n',
        'type'          => Omnilog_entry::WARNING
      )
    );

    $db->setReturnReference('get', $db_result);
    $db_result->expectOnce('result_array');
    $db_result->setReturnValue('result_array', $db_rows);

    $expected_result = array();
    foreach ($db_rows AS $db_row)
    {
      $db_row['admin_emails'] = explode('|', $db_row['admin_emails']);
      $db_row['notify_admin'] = (strtolower($db_row['notify_admin']) === 'y');
      $expected_result[]      = new Omnilog_entry($db_row);
    }

    $actual_result = $this->_subject->get_log_entries($site_id);

    $this->assertIdentical(count($expected_result), count($actual_result));
    for ($count = 0, $length = count($expected_result); $count < $length; $count++)
    {
      $this->assertIdentical(
        $expected_result[$count]->to_array(TRUE),
        $actual_result[$count]->to_array(TRUE)
      );
    }
  }


  public function test__get_log_entries__no_entries()
  {
    $db = $this->_ee->db;
    $db_result = $this->_get_mock('db_query');

    $db->setReturnReference('get', $db_result);
    $db_result->expectOnce('result_array');
    $db_result->setReturnValue('result_array', array());

    $this->assertIdentical(array(), $this->_subject->get_log_entries());
  }


  public function test__get_log_entries__success_with_limit()
  {
    $db     = $this->_ee->db;
    $limit  = 10;

    $select_fields = 'addon_name, admin_emails, date, log_entry_id, message,
      extended_data, notify_admin, type';

    $db->expectOnce('select',
      array(new EqualWithoutWhitespaceExpectation($select_fields)));

    $db->expectOnce('where', array(array('site_id' => $this->_site_id)));
    $db->expectOnce('order_by', array('log_entry_id', 'desc'));
    $db->expectOnce('get', array('omnilog_entries', $limit, 0));

    $db_result = $this->_get_mock('db_query');
    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', array());

    $this->assertIdentical(array(),
      $this->_subject->get_log_entries(NULL, $limit));
  }


  public function test__get_log_entries__works_with_custom_offset()
  {
    $db     = $this->_ee->db;
    $limit  = $this->_subject->get_default_log_limit();
    $offset = 100;

    $select_fields = 'addon_name, admin_emails, date, log_entry_id, message,
      extended_data, notify_admin, type';

    $db->expectOnce('select',
      array(new EqualWithoutWhitespaceExpectation($select_fields)));

    $db->expectOnce('where', array(array('site_id' => $this->_site_id)));
    $db->expectOnce('order_by', array('log_entry_id', 'desc'));
    $db->expectOnce('get', array('omnilog_entries', $limit, $offset));

    $db_result = $this->_get_mock('db_query');
    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('result_array', array());

    $this->assertIdentical(array(),
      $this->_subject->get_log_entries(NULL, NULL, $offset));
  }


  public function test__get_site_id__success()
  {
    $this->_ee->config->expectOnce('item', array('site_id'));
    $this->assertIdentical(intval($this->_site_id), $this->_subject->get_site_id());
  }


  public function test__install_module_actions__success()
  {
      $query_data = array(
          array('class' => ucfirst($this->_package_name), 'method' => '')
      );

      $query_count = count($query_data);
      $this->_ee->db->expectCallCount('insert', $query_count);

      for ($count = 0; $count < $query_count; $count++)
      {
          $this->_ee->db->expectAt($count, 'insert', array('actions', $query_data[$count]));
      }

      $this->_subject->install_module_actions();
  }


  public function test__install_module_entries_table__success()
  {
      $dbforge = $this->_ee->dbforge;

      $fields = array(
          'log_entry_id' => array(
              'auto_increment'    => TRUE,
              'constraint'        => 10,
              'type'              => 'INT',
              'unsigned'          => TRUE
          ),
          'site_id' => array(
              'constraint'        => 5,
              'type'              => 'INT',
              'unsigned'          => TRUE
          ),
          'addon_name' => array(
              'constraint'        => 50,
              'type'              => 'VARCHAR'
          ),
          'admin_emails' => array(
              'type'              => 'MEDIUMTEXT',
          ),
          'date' => array(
              'constraint'        => 10,
              'type'              => 'INT',
              'unsigned'          => TRUE
          ),
          'notify_admin' => array(
              'constraint'        => 1,
              'default'           => 'n',
              'type'              => 'CHAR'
          ),
          'type' => array(
              'constraint'        => 10,
              'type'              => 'VARCHAR'
          ),
          'message' => array(
              'type'              => 'TEXT'
          ),
          'extended_data' => array(
              'type'              => 'TEXT'
          )
      );
  
      $dbforge->expectOnce('add_field', array($fields));

      $dbforge->expectCallCount('add_key', 2);
      $dbforge->expectAt(0, 'add_key', array('log_entry_id', TRUE));
      $dbforge->expectAt(1, 'add_key', array('addon_name'));

      $dbforge->expectOnce('create_table', array('omnilog_entries', TRUE));

      $this->_subject->install_module_entries_table();
  }


  public function test__install_module_register__success()
  {
      $query_data = array(
          'has_cp_backend'        => 'y',
          'has_publish_fields'    => 'n',
          'module_name'           => ucfirst($this->_package_name),
          'module_version'        => $this->_package_version
      );

      $this->_ee->db->expectOnce('insert', array('modules', $query_data));
      $this->_subject->install_module_register();
  }


  public function test__notify_site_admin_of_log_entry__success()
  {
      $config = $this->_ee->config;
      $email  = $this->_ee->email;
      $lang   = $this->_ee->lang;

      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::ERROR
      );

      $entry = new Omnilog_entry($entry_data);

      $cp_url         = 'http://example.com/system/index.php';
      $site_name      = 'Example Website';
      $webmaster_email = 'webmaster@example.com';
      $webmaster_name = 'Lord Vancellator';

      $lang_subject       = 'Subject';
      $lang_addon_name    = 'Add-on Name:';
      $lang_cp_url        = 'Control Panel URL:';
      $lang_log_date      = 'Date Logged:';
      $lang_log_message   = 'Log Message:';
      $lang_log_extended  = 'Extended Data:';
      $lang_entry_type    = 'Severity:';
      $lang_error         = 'Error';
      $lang_preamble      = 'The bit before the details.';
      $lang_postscript    = '-- End of message --';

      $subject            = $lang_subject .' (' .$site_name .')';
      $addon_name         = $lang_addon_name .NL .$entry_data['addon_name'];
      $log_cp_url         = $lang_cp_url .NL .$cp_url;
      $log_date           = $lang_log_date .NL .date('r', $entry_data['date']);
      $log_message        = $lang_log_message .NL .$entry_data['message'];
      $log_extended_data  = $lang_log_extended .NL .$entry_data['extended_data'];
      $entry_type         = $lang_entry_type .NL .$lang_error;

      $message = $lang_preamble
          .NL .NL
          .$addon_name .NL .NL
          .$log_date .NL .NL
          .$entry_type .NL .NL
          .$log_message .NL .NL
          .$log_extended_data .NL .NL
          .$log_cp_url .NL .NL
          .$lang_postscript;

      $message = entities_to_ascii($message);

      $config->expectCallCount('item', 4);
      $config->setReturnValue('item', $cp_url, array('cp_url'));
      $config->setReturnValue('item', $site_name, array('site_name'));
      $config->setReturnValue('item', $webmaster_email, array('webmaster_email'));
      $config->setReturnValue('item', $webmaster_name, array('webmaster_name'));

      $email->expectOnce('valid_email', array($webmaster_email));
      $email->setReturnValue('valid_email', TRUE);
      $email->expectOnce('from', array($webmaster_email, $webmaster_name));
      $email->expectOnce('to', array(array($webmaster_email)));
      $email->expectOnce('subject', array($subject));
      $email->expectOnce('message', array($message));
      $email->expectOnce('send');
      $email->setReturnValue('send', TRUE);

      $lang->setReturnValue('line', $lang_subject, array('email_subject'));
      $lang->setReturnValue('line', $lang_addon_name, array('email_addon_name'));
      $lang->setReturnValue('line', $lang_cp_url, array('email_cp_url'));
      $lang->setReturnValue('line', $lang_log_date, array('email_log_date'));
      $lang->setReturnValue('line', $lang_log_message, array('email_log_message'));
      $lang->setReturnValue('line', $lang_log_extended, array('email_log_extended_data'));
      $lang->setReturnValue('line', $lang_entry_type, array('email_entry_type'));
      $lang->setReturnValue('line', $lang_error, array('email_entry_type_error'));
      $lang->setReturnValue('line', $lang_preamble, array('email_preamble'));
      $lang->setReturnValue('line', $lang_postscript, array('email_postscript'));

      $this->_subject->notify_site_admin_of_log_entry($entry);
  }


  public function test__notify_site_admin_of_log_entry__custom_email_success()
  {
      $config = $this->_ee->config;
      $email  = $this->_ee->email;
      $lang   = $this->_ee->lang;

      // Must be set before we create the Omnilog_entry.
      $email->setReturnValue('valid_email', TRUE);

      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'admin_emails'  => array('adam@adamson.com', 'bob@bobson.com'),
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::ERROR
      );

      $entry = new Omnilog_entry($entry_data);

      $cp_url         = 'http://example.com/system/index.php';
      $site_name      = 'Example Website';
      $webmaster_email = 'webmaster@example.com';
      $webmaster_name = 'Lord Vancellator';

      $lang_subject       = 'Subject';
      $lang_addon_name    = 'Add-on Name:';
      $lang_cp_url        = 'Control Panel URL:';
      $lang_log_date      = 'Date Logged:';
      $lang_log_message   = 'Log Message:';
      $lang_log_extended  = 'Log Extended Data:';
      $lang_entry_type    = 'Severity:';
      $lang_error         = 'Error';
      $lang_preamble      = 'The bit before the details.';
      $lang_postscript    = '-- End of message --';

      $subject            = $lang_subject .' (' .$site_name .')';
      $addon_name         = $lang_addon_name .NL .$entry_data['addon_name'];
      $log_cp_url         = $lang_cp_url .NL .$cp_url;
      $log_date           = $lang_log_date .NL .date('r', $entry_data['date']);
      $log_message        = $lang_log_message .NL .$entry_data['message'];
      $log_extended_data  = $lang_log_extended .NL .$entry_data['extended_data'];
      $entry_type         = $lang_entry_type .NL .$lang_error;

      $message = $lang_preamble
          .NL .NL
          .$addon_name .NL .NL
          .$log_date .NL .NL
          .$entry_type .NL .NL
          .$log_message .NL .NL
          .$log_extended_data .NL .NL
          .$log_cp_url .NL .NL
          .$lang_postscript;

      $message = entities_to_ascii($message);

      $config->expectCallCount('item', 4);
      $config->setReturnValue('item', $cp_url, array('cp_url'));
      $config->setReturnValue('item', $site_name, array('site_name'));
      $config->setReturnValue('item', $webmaster_email, array('webmaster_email'));
      $config->setReturnValue('item', $webmaster_name, array('webmaster_name'));

      $email->expectOnce('from', array($webmaster_email, $webmaster_name));
      $email->expectOnce('to', array($entry_data['admin_emails']));
      $email->expectOnce('subject', array($subject));
      $email->expectOnce('message', array($message));
      $email->expectOnce('send');
      $email->setReturnValue('send', TRUE);

      $lang->setReturnValue('line', $lang_subject, array('email_subject'));
      $lang->setReturnValue('line', $lang_addon_name, array('email_addon_name'));
      $lang->setReturnValue('line', $lang_cp_url, array('email_cp_url'));
      $lang->setReturnValue('line', $lang_log_date, array('email_log_date'));
      $lang->setReturnValue('line', $lang_log_message, array('email_log_message'));
      $lang->setReturnValue('line', $lang_log_extended, array('email_log_extended_data'));
      $lang->setReturnValue('line', $lang_entry_type, array('email_entry_type'));
      $lang->setReturnValue('line', $lang_error, array('email_entry_type_error'));
      $lang->setReturnValue('line', $lang_preamble, array('email_preamble'));
      $lang->setReturnValue('line', $lang_postscript, array('email_postscript'));

      $this->_subject->notify_site_admin_of_log_entry($entry);
  }


  public function test__notify_site_admin_of_log_entry__success_no_webmaster_name()
  {
      $config = $this->_ee->config;
      $email  = $this->_ee->email;

      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::ERROR
      );

      $entry = new Omnilog_entry($entry_data);

      $webmaster_email = 'webmaster@example.com';

      $config->setReturnValue('item', $webmaster_email, array('webmaster_email'));
      $email->expectOnce('valid_email', array($webmaster_email));
      $email->setReturnValue('valid_email', TRUE);
      $email->expectOnce('from', array($webmaster_email, ''));
      $email->expectOnce('to', array(array($webmaster_email)));
      $email->expectOnce('subject');
      $email->expectOnce('message');
      $email->expectOnce('send');
      $email->setReturnValue('send', TRUE);

      $this->_subject->notify_site_admin_of_log_entry($entry);
  }


  public function test__notify_site_admin_of_log_entry__success_no_site_name()
  {
      $config = $this->_ee->config;
      $email  = $this->_ee->email;
      $lang   = $this->_ee->lang;

      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::ERROR
      );

      $entry = new Omnilog_entry($entry_data);

      $webmaster_email = 'webmaster@example.com';
      $webmaster_name = 'Lord Vancellator';
      $lang_subject   = 'Subject';

      $config->setReturnValue('item', $webmaster_email, array('webmaster_email'));
      $config->setReturnValue('item', $webmaster_name, array('webmaster_name'));

      $email->expectOnce('valid_email', array($webmaster_email));
      $email->setReturnValue('valid_email', TRUE);
      $email->expectOnce('from', array($webmaster_email, $webmaster_name));
      $email->expectOnce('to', array(array($webmaster_email)));
      $email->expectOnce('subject', array($lang_subject));
      $email->expectOnce('message');
      $email->expectOnce('send');
      $email->setReturnValue('send', TRUE);
      $lang->setReturnValue('line', $lang_subject, array('email_subject'));

      $this->_subject->notify_site_admin_of_log_entry($entry);
  }


  public function test__notify_site_admin_of_log_entry__missing_log_data()
  {
      $config = $this->_ee->config;
      $email  = $this->_ee->email;
      $lang   = $this->_ee->lang;
  
      $entry_data = array(
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::ERROR
      );

      $entry = new Omnilog_entry($entry_data);

      $config->expectNever('item');
      $email->expectNever('valid_email');
      $email->expectNever('from');
      $email->expectNever('to');
      $email->expectNever('subject');
      $email->expectNever('message');
      $email->expectNever('send');

      $error_message = 'Error';
      $lang->setReturnValue('line', $error_message, array('exception__notify_admin__missing_data'));

      $this->expectException(new Exception($error_message));
      $this->_subject->notify_site_admin_of_log_entry($entry);
  }


  public function test__notify_site_admin_of_log_entry__invalid_webmaster_email()
  {
      $config = $this->_ee->config;
      $email  = $this->_ee->email;
      $lang   = $this->_ee->lang;
  
      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::ERROR
      );

      $entry = new Omnilog_entry($entry_data);

      $webmaster_email = 'invalid';
      $config->expectOnce('item', array('webmaster_email'));
      $config->setReturnValue('item', $webmaster_email);

      $email->expectOnce('valid_email', array($webmaster_email));
      $email->setReturnValue('valid_email', FALSE);

      $email->expectNever('from');
      $email->expectNever('to');
      $email->expectNever('subject');
      $email->expectNever('message');
      $email->expectNever('send');

      $error_message = 'Error';
      $lang->setReturnValue('line', $error_message, array('exception__notify_admin__invalid_webmaster_email'));

      $this->expectException(new Exception($error_message));
      $this->_subject->notify_site_admin_of_log_entry($entry);
  }


  public function test__notify_site_admin_of_log_entry__email_not_sent()
  {
      $config = $this->_ee->config;
      $email  = $this->_ee->email;
      $lang   = $this->_ee->lang;
  
      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::ERROR
      );

      $entry = new Omnilog_entry($entry_data);

      $webmaster_email = 'webmaster@example.com';
      $config->setReturnValue('item', $webmaster_email);
      $email->setReturnValue('valid_email', TRUE);

      $email->expectOnce('from');
      $email->expectOnce('to');
      $email->expectOnce('subject');
      $email->expectOnce('message');
      $email->expectOnce('send');
      $email->setReturnValue('send', FALSE);

      $error_message = 'Error';
      $lang->setReturnValue('line', $error_message, array('exception__notify_admin__email_not_sent'));

      $this->expectException(new Exception($error_message));
      $this->_subject->notify_site_admin_of_log_entry($entry);
  }


  public function test__save_entry_to_log__success()
  {
      $db     = $this->_ee->db;
      $email  = $this->_ee->email;

      // Ensures that the emails are added to the Omnilog_entry.
      $email->setReturnValue('valid_email', TRUE);

      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'admin_emails'  => array('adam@ants.com', 'bob@dylan.com'),
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'notify_admin'  => FALSE,
          'type'          => Omnilog_entry::NOTICE
      );

      $insert_data = array(
          'addon_name'    => 'Example Add-on',
          'admin_emails'  => 'adam@ants.com|bob@dylan.com',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'notify_admin'  => 'n',
          'type'          => Omnilog_entry::NOTICE,
          'site_id'       => $this->_site_id
      );

      $entry          = new Omnilog_entry($entry_data);
      $insert_id      = 10;

      $db->expectOnce('table_exists', array('omnilog_entries'));
      $db->setReturnValue('table_exists', TRUE);

      $db->expectOnce('insert', array('omnilog_entries', $insert_data));
      $db->setReturnValue('insert_id', $insert_id);

      $expected_props = array_merge($entry_data, array('log_entry_id' => $insert_id));
      $expected_result = new Omnilog_entry($expected_props);
      $actual_result  = $this->_subject->save_entry_to_log($entry);
  
      $this->assertIdentical($expected_result->to_array(TRUE), $actual_result->to_array(TRUE));
  }


  public function test__save_entry_to_log__success_with_notify_admin()
  {
      $db = $this->_ee->db;
      $email  = $this->_ee->email;

      // Ensures that the emails are added to the Omnilog_entry.
      $email->setReturnValue('valid_email', TRUE);

      $entry_data = array(
          'addon_name'    => 'Example Add-on',
          'admin_emails'  => array('adam@ants.com', 'bob@dylan.com'),
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'notify_admin'  => TRUE,
          'type'          => Omnilog_entry::ERROR
      );

      $insert_data = array(
          'addon_name'    => 'Example Add-on',
          'admin_emails'  => 'adam@ants.com|bob@dylan.com',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'notify_admin'  => 'y',
          'type'          => Omnilog_entry::ERROR,
          'site_id'       => $this->_site_id
      );

      $entry          = new Omnilog_entry($entry_data);
      $insert_id      = 10;

      $db->expectOnce('table_exists', array('omnilog_entries'));
      $db->setReturnValue('table_exists', TRUE);

      $db->expectOnce('insert', array('omnilog_entries', $insert_data));
      $db->setReturnValue('insert_id', $insert_id);

      $expected_props = array_merge($entry_data, array('log_entry_id' => $insert_id));
      $expected_result = new Omnilog_entry($expected_props);
      $actual_result  = $this->_subject->save_entry_to_log($entry);
  
      $this->assertIdentical($expected_result->to_array(TRUE), $actual_result->to_array(TRUE));
  }


  public function test__save_entry_to_log__not_installed()
  {
      $exception_message = 'Exception';
      $this->_ee->lang->setReturnValue('line', $exception_message);

      $this->_ee->db->expectOnce('table_exists', array('omnilog_entries'));
      $this->_ee->db->setReturnValue('table_exists', FALSE);

      $this->_ee->db->expectNever('insert');
      $this->expectException(new Exception($exception_message));

      $this->_subject->save_entry_to_log(new Omnilog_entry());
  }


  public function test__save_entry_to_log__missing_entry_data()
  {
      $exception_message = 'Exception';
      $this->_ee->lang->setReturnValue('line', $exception_message);

      $this->_ee->db->expectOnce('table_exists', array('omnilog_entries'));
      $this->_ee->db->setReturnValue('table_exists', TRUE);

      $this->_ee->db->expectNever('insert');
      $this->expectException(new Exception($exception_message));

      $this->_subject->save_entry_to_log(new Omnilog_entry());
  }


  public function test__save_entry_to_log__no_insert_id()
  {
      $entry_props = array(
          'addon_name'    => 'Example Add-on',
          'date'          => time() - 100,
          'message'       => 'Example OmniLog entry.',
          'extended_data' => 'Example OmniLog extended data.',
          'type'          => Omnilog_entry::NOTICE
      );

      $entry = new Omnilog_entry($entry_props);

      $exception_message = 'Exception';
      $this->_ee->lang->setReturnValue('line', $exception_message);

      $this->_ee->db->expectOnce('table_exists', array('omnilog_entries'));
      $this->_ee->db->setReturnValue('table_exists', TRUE);

      $this->_ee->db->expectOnce('insert');
      $this->_ee->db->expectOnce('insert_id');
      $this->_ee->db->setReturnValue('insert_id', 0);

      $this->expectException(new Exception($exception_message));
      $this->_subject->save_entry_to_log($entry);
  }


  public function test__uninstall_module__success()
  {
      $dbforge                    = $this->_ee->dbforge;
      $db_module_result           = $this->_get_mock('db_query');
      $db_module_row              = new StdClass();
      $db_module_row->module_id   = '10';
      $module_name                = ucfirst($this->_package_name);

      $this->_ee->db->expectOnce('select', array('module_id'));
      $this->_ee->db->expectOnce('get_where', array('modules', array('module_name' => $module_name), 1));

      $this->_ee->db->expectCallCount('delete', 3);
      $this->_ee->db->expectAt(0, 'delete', array('module_member_groups', array('module_id' => $db_module_row->module_id)));
      $this->_ee->db->expectAt(1, 'delete', array('modules', array('module_name' => $module_name)));
      $this->_ee->db->expectAt(2, 'delete', array('actions', array('class' => $module_name)));

      $dbforge->expectOnce('drop_table', array('omnilog_entries'));

      $this->_ee->db->setReturnReference('get_where', $db_module_result);
      $db_module_result->setReturnValue('num_rows', 1);
      $db_module_result->setReturnValue('row', $db_module_row);

      $this->assertIdentical(TRUE, $this->_subject->uninstall_module());
  }


  public function test__uninstall_module__module_not_found()
  {
      $db_module_result = $this->_get_mock('db_query');

      $this->_ee->db->expectOnce('select');
      $this->_ee->db->expectOnce('get_where');
      $this->_ee->db->expectNever('delete');

      $this->_ee->db->setReturnReference('get_where', $db_module_result);
      $db_module_result->setReturnValue('num_rows', 0);

      $this->assertIdentical(FALSE, $this->_subject->uninstall_module());
  }


  public function test__update_package__no_update_required()
  {
      $installed_version = $this->_package_version;
      $this->assertIdentical(FALSE, $this->_subject->update_package($installed_version));
  }


  public function test__update_package__update_required()
  {
      /**
       * Arbitrarily high numbers, so no
       * update scripts are triggered.
       */

      $installed_version  = '10.0.0';
      $package_version    = '10.0.1';
      $package_name       = 'example_package';
      $subject            = new Omnilog_model($package_name, $package_version);

      $this->assertIdentical(TRUE, $subject->update_package($installed_version));
  }


  public function test__update_package__update_required_force_version_bump()
  {
      /**
       * Arbitrarily high numbers, so no
       * update scripts are triggered.
       */

      $installed_version  = '10.0.0';
      $package_version    = '10.0.1';
      $package_name       = 'example_package';
      $subject            = new Omnilog_model($package_name, $package_version);

      $this->_ee->db->expectOnce('update', array(
          'modules',
          array('module_version' => $package_version),
          array('module_name' => $this->_package_name)
      ));

      $this->assertIdentical(TRUE, $subject->update_package($installed_version, TRUE));
  }


  public function test__update_package__no_installed_version()
  {
      $installed_version = '';
      $this->assertIdentical(FALSE, $this->_subject->update_package($installed_version));
  }


  public function test__update_package__update_to_version_110()
  {
      $installed_version  = '1.0.0';
      $package_version    = '1.1.0';
      $package_name       = 'example_package';
      $subject            = new Omnilog_model($package_name, $package_version);

      $column = array(
          'admin_emails' => array('type' => 'MEDIUMTEXT')
      );

      $this->_ee->dbforge->expectAtLeastOnce('add_column');

      $this->_ee->dbforge->expectAt(0,
        'add_column',
        array('omnilog_entries', $column)
      );

      $this->assertIdentical(TRUE, $subject->update_package($installed_version));
  }


  public function test__update_package__update_to_version_122()
  {
      $installed_version  = '1.2.1';
      $package_version    = '1.2.2';
      $package_name       = 'example_package';
      $subject            = new Omnilog_model($package_name, $package_version);

      $column = array(
        'extended_data' => array('type' => 'TEXT')
      );

      $this->_ee->dbforge->expectAtLeastOnce('add_column');

      $this->_ee->dbforge->expectAt(0,
        'add_column',
        array('omnilog_entries', $column)
      );

      $sql = 'CREATE INDEX key_addon_name
        ON `exp_omnilog_entries` (`addon_name`)';

      $this->_ee->db->expectAtLeastOnce('query');
      $this->_ee->db->expectAt(0, 'query', array(
        new EqualWithoutWhitespaceExpectation($sql))
      );

      $this->assertIdentical(TRUE, $subject->update_package($installed_version));
  }


}


/* End of file      : test.omnilog_model.php */
/* File location    : third_party/omnilog/tests/test.omnilog_model.php */
