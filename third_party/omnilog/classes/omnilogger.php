<?php if ( ! defined('BASEPATH')) exit('Direct script access not permitted.');

/**
 * OmniLogger class.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once dirname(__FILE__) .'/../classes/omnilog_entry.php';

class Omnilogger {

  /* --------------------------------------------------------------
   * STATIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Adds an entry to the log.
   *
   * @access  public
   * @param   Omnilog_entry     $entry      The log entry.
   * @return  bool
   */
  public static function log(Omnilog_entry $entry)
  {
    $EE =& get_instance();
    $EE->load->add_package_path(PATH_THIRD .'omnilog/');
    $EE->load->model('omnilog_model');

    try
    {
      $saved_entry = $EE->omnilog_model->save_entry_to_log($entry);

      if ($entry->get_notify_admin() === TRUE)
      {
        $EE->omnilog_model->notify_site_admin_of_log_entry($saved_entry);
      }

      return TRUE;
    }
    catch (Exception $e)
    {
      // Don't try to log the error using OmniLog. Remember Inception.
      return FALSE;
    }
  }


}


/* End of file      : omnilogger.php */
/* File location    : third_party/omnilog/classes/omnilogger.php */
