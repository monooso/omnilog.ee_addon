<?php if ( ! defined('BASEPATH')) exit('Direct script access not permitted.');

/**
 * OmniLog Entry datatype.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once PATH_THIRD .'omnilog/helpers/EI_number_helper' .EXT;

class Omnilog_entry {

  const NOTICE    = 'notice';
  const WARNING   = 'warning';
  const ERROR     = 'error';

  private $EE;
  private $_addon_name;
  private $_admin_emails;
  private $_date;
  private $_log_entry_id;
  private $_message;
  private $_extended_data;
  private $_notify_admin;
  private $_type;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   array     $props      Associative array of instance properties.
   * @return  void
   */
  public function __construct(Array $props = array())
  {
    $this->EE =& get_instance();
    $this->EE->load->library('email');

    $this->reset();

    foreach ($props AS $prop_name => $prop_value)
    {
      $method_name = 'set_' .$prop_name;

      if (method_exists($this, $method_name))
      {
        $this->$method_name($prop_value);
      }
    }
  }


  /**
   * Adds an email address to the 'admin emails' array.
   *
   * @access  public
   * @param   string      $email      The email address.
   * @return  array
   */
  public function add_admin_email($email)
  {
    if ($this->EE->email->valid_email($email))
    {
      $this->_admin_emails[] = $email;
    }

    return $this->get_admin_emails();
  }


  /**
   * Returns the add-on name.
   *
   * @access    public
   * @return    string
   */
  public function get_addon_name()
  {
    return $this->_addon_name;
  }


  /**
   * Returns an array of admin email addresses.
   *
   * @access  public
   * @return  array
   */
  public function get_admin_emails()
  {
    return $this->_admin_emails;
  }


  /**
   * Returns the entry date, in UNIX time form.
   *
   * @access    public
   * @return    int
   */
  public function get_date()
  {
    return $this->_date;
  }


  /**
   * Returns the log entry ID.
   *
   * @access  public
   * @return  int
   */
  public function get_log_entry_id()
  {
    return $this->_log_entry_id;
  }


  /**
   * Returns whether the site administrator should be notified of this entry.
   *
   * @access  public
   * @return  bool
   */
  public function get_notify_admin()
  {
    return $this->_notify_admin;
  }


  /**
   * Returns the message.
   *
   * @access  public
   * @return  string
   */
  public function get_message()
  {
    return $this->_message;
  }

  /**
   * Returns the extended data.
   *
   * @access  public
   * @return  string
   */
  public function get_extended_data()
  {
    return $this->_extended_data;
  }


  /**
   * Returns the entry type.
   *
   * @access  public
   * @return  string
   */
  public function get_type()
  {
    return $this->_type;
  }


  /**
   * Returns a boolean value indicating whether all of the instance properties
   * have been set to valid values.
   *
   * @access  public
   * @param   bool    $require_entry_id     Check for a valid log_entry_id?
   * @return  bool
   */
  public function is_populated($require_entry_id = FALSE)
  {
    $valid = $this->get_addon_name() != ''
      && $this->get_date()    != 0
      && $this->get_message() != ''
      && $this->get_type()    != '';

    return $require_entry_id === TRUE
      ? $valid && ($this->get_log_entry_id() != 0)
      : $valid;
  }


  /**
   * Resets the instance properties.
   *
   * @access  public
   * @return  Omnilog_entry
   */
  public function reset()
  {
    $this->_addon_name      = '';
    $this->_admin_emails    = array();
    $this->_date            = 0;
    $this->_log_entry_id    = 0;
    $this->_message         = '';
    $this->_extended_data   = '';
    $this->_notify_admin    = FALSE;
    $this->_type            = self::NOTICE;

    return $this;
  }


  /**
   * Sets the add-on name.
   *
   * @access  public
   * @param   string        $addon_name        The add-on name.
   * @return  string
   */
  public function set_addon_name($addon_name)
  {
    if (is_string($addon_name))
    {
      $this->_addon_name = $addon_name;
    }

    return $this->get_addon_name();
  }


  /**
   * Sets the admin email addresses.
   *
   * @access  public
   * @param   array       $emails     An array of email addresses.
   * @return  array
   */
  public function set_admin_emails(Array $emails = array())
  {
    $this->_admin_emails = array();

    foreach ($emails AS $email)
    {
      $this->add_admin_email($email);
    }

    return $this->get_admin_emails();
  }


  /**
   * Sets the entry date.
   *
   * @access  public
   * @param   int|string    $date     The entry date, in UNIX time form.
   * @return  int
   */
  public function set_date($date)
  {
    // It's reasonable to assume the nobody will be logging messages
    // from the 70s, or from the future.
    if (valid_int($date, 0, time()))
    {
      $this->_date = intval($date);
    }

    return $this->get_date();
  }


  /**
   * Sets the log entry ID.
   *
   * @access  public
   * @param   int|string      $log_entry_id        The log entry ID.
   * @return  int
   */
  public function set_log_entry_id($log_entry_id)
  {
    if (valid_int($log_entry_id, 1))
    {
      $this->_log_entry_id = $log_entry_id;
    }

    return $this->get_log_entry_id();
  }


  /**
   * Sets whether the site administrator should be notified of this entry.
   *
   * @access  public
   * @param   bool    $notify_admin        Should the site admin be notified?
   * @return  bool
   */
  public function set_notify_admin($notify_admin)
  {
    if (is_bool($notify_admin))
    {
      $this->_notify_admin = (bool) $notify_admin;
    }

    return $this->get_notify_admin();
  }


  /**
   * Sets the message.
   *
   * @access  public
   * @param   string        $message        The message.
   * @return  string
   */
  public function set_message($message)
  {
    if (is_string($message))
    {
      $this->_message = $message;
    }

    return $this->get_message();
  }


  /**
   * Sets the extended data.
   *
   * @access  public
   * @param   string        $extended_data  The extended data.
   * @return  string
   */
  public function set_extended_data($extended_data)
  {
    if (is_string($extended_data))
    {
      $this->_extended_data = $extended_data;
    }

    return $this->get_extended_data();
  }


  /**
   * Sets the entry type.
   *
   * @access  public
   * @param   string      $type        The entry type.
   * @return  string
   */
  public function set_type($type)
  {
    if (in_array($type, array(self::NOTICE, self::WARNING, self::ERROR)))
    {
      $this->_type = $type;
    }

    return $this->get_type();
  }


  /**
   * Returns the instance as an associative array.
   *
   * @access  public
   * @param   bool    $include_entry_id     Should we include the log_entry_id?
   * @return  array
   */
  public function to_array($include_entry_id = FALSE)
  {
    $return = array(
      'addon_name'    => $this->get_addon_name(),
      'admin_emails'  => $this->get_admin_emails(),
      'date'          => $this->get_date(),
      'message'       => $this->get_message(),
      'extended_data' => $this->get_extended_data(),
      'notify_admin'  => $this->get_notify_admin(),
      'type'          => $this->get_type()
    );

    if ($include_entry_id === TRUE)
    {
      $return['log_entry_id'] = $this->get_log_entry_id();
    }

    return $return;
  }


}


/* End of file      : omnilog_entry.php */
/* File location    : third_party/omnilog/classes/omnilog_entry.php */
