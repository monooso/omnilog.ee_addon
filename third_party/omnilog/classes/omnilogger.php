<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * OmniLogger class.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once PATH_THIRD .'omnilog/classes/omnilog_entry' .EXT;

class Omnilogger {
    
    /* --------------------------------------------------------------
     * STATIC METHODS
     * ------------------------------------------------------------ */

    /**
     * Adds an entry to the log.
     *
     * @access  public
	 * @param	Omnilog_entry		$entry				The log entry.
     * @return  bool
     */
    public static function log(Omnilog_entry $entry)
    {
        $ee =& get_instance();
        $ee->load->add_package_path(PATH_THIRD .'omnilog/');
        $ee->load->model('omnilog_model');

        try
        {
            $saved_entry = $ee->omnilog_model->save_entry_to_log($entry);

            if ($entry->get_notify_admin() === TRUE)
            {
                $ee->omnilog_model->notify_site_admin_of_log_entry($saved_entry);
            }

            return TRUE;
        }
        catch (Exception $e)
        {
            // Don't try to log the error using OmniLog, it could result in an infinite loop.
            return FALSE;
        }
    }

    
}


/* End of file      : omnilogger.php */
/* File location    : third_party/omnilog/classes/omnilogger.php */
