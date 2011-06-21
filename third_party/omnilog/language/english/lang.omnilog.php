<?php 

/**
 * OmniLog language strings.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

$lang = array(

/* --------------------------------------------------------------
 * REQUIRED
 * ------------------------------------------------------------ */
'omnilog_module_name'        => 'OmniLog',
'omnilog_module_description' => 'Installation-wide message log, which any add-on can use to log notices, warnings, and errors.',


/* --------------------------------------------------------------
 * EXCEPTIONS
 * ------------------------------------------------------------ */
'exception__notify_admin__email_not_sent'           => 'Unable to send administrator email.',
'exception__notify_admin__invalid_webmaster_email'  => 'Unable to send admin email due to invalid webmaster email address.',
'exception__notify_admin__missing_data'             => 'Unable to send admin email due to missing or invalid data.',

'exception__save_entry__missing_data'   => 'Unable to save entry to log due to missing or invalid data.',
'exception__save_entry__not_saved'      => 'A database error occurred whilst attempting to save the log entry.',


/* --------------------------------------------------------------
 * LOG
 * ------------------------------------------------------------ */
'hd_log'            => 'Log',

'lbl_no'            => 'No',
'lbl_type_notice'   => 'Notice',
'lbl_type_warning'  => 'Warning',
'lbl_type_error'    => 'Error',
'lbl_yes'           => 'Yes',

'thd_addon'         => 'Add-on',
'thd_date'          => 'Date',
'thd_type'          => 'Entry Type',
'thd_message'       => 'Message',
'thd_notify_admin'  => 'Notify Admin?',

// All done.
'' => ''

);

/* End of file      : lang.omnilog.php */
/* File location    : third_party/omnilog/language/english/lang.omnilog.php */
