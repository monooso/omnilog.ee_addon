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
	 * @param	bool				$notify_admin		Should we notify the site administrator of this entry?
     * @return  bool
     */
    public static function log(Omnilog_entry $entry, $notify_admin = FALSE)
    {
        $ee =& get_instance();
        $ee->load->model('omnilog_model');

        $model = $ee->omnilog_model;
		
		if ( ! $entry_id = $model->save_entry_to_log($entry))
		{
			return FALSE;
		}

		return $notify_admin === TRUE
			? $model->notify_site_admin_of_log_entry($entry_id)
			: TRUE;
    }

    
}


/* End of file      : omnilogger.php */
/* File location    : third_party/omnilog/classes/omnilogger.php */
