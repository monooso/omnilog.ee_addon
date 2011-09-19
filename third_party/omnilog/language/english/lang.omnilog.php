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
 * DEMO
 * ------------------------------------------------------------ */
'demo_flashdata_failure'            => 'The demo failed due to an unknown error.',
'demo_flashdata_missing_omnilogger' => 'The OmniLogger class could not be found.',
'demo_flashdata_success'            => 'The demo was successful.',
'demo_flashdata_unknown_demo'       => 'Unknown demo.',

'demo_log_error'            => 'Send an "error" to OmniLog.',
'demo_log_notice'           => 'Send a "notice" to OmniLog.',
'demo_log_warning'          => 'Send a "warning" to OmniLog.',
'demo_message'              => 'Demo message, sent by OmniLog.',
'demo_extended_data'        => 'Demo extended data, maybe a var dump or similar, sent by Omnilog Demo extended data, maybe a var dump or similar, sent by Omnilog Demo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by OmnilogDemo extended data, maybe a var dump or similar, sent by Omnilog.',
'demo_notify_standard'      => 'Send an "error" to OmniLog, and email the site administrator (%s).',
'demo_notify_custom'        => 'Send an "error" to OmniLog, and email custom recipients.',

'hd_demo'                   => 'OmniLog Demo',

'lbl_demo_email'            => 'Comma-separated list of email address(es)',
'lbl_demo_run'              => 'Run',

'thd_demo_title'            => 'Title',
'thd_demo_description'      => 'Description',
'thd_demo_custom_info'      => 'Custom Information',
'thd_demo_log_notice'       => 'Log Notice',
'thd_demo_log_warning'      => 'Log Warning',
'thd_demo_log_error'        => 'Log Error',
'thd_demo_notify_standard'  => 'Standard Notification',
'thd_demo_notify_custom'    => 'Custom Notification',

'td_view_extended'			=> 'Show',
'td_hide_extended'			=> 'Hide',


/* --------------------------------------------------------------
 * EMAIL
 * ------------------------------------------------------------ */
'email_addon_name'          => 'Add-on Name:',
'email_cp_url'              => 'Control Panel URL:',
'email_entry_type'          => 'Severity:',
'email_entry_type_error'    => 'Error',
'email_entry_type_notice'   => 'Notice',
'email_entry_type_warning'  => 'Warning',
'email_log_date'            => 'Log Entry Date:',
'email_log_message'         => 'Log Message:',
'email_postscript'          => '--- END OF MESSAGE ---',
'email_preamble'            => 'OmniLog was instructed to notify the site administrator of the following log entry.',
'email_subject'             => 'OmniLog Entry Notification',


/* --------------------------------------------------------------
 * EXCEPTIONS
 * ------------------------------------------------------------ */
'exception__notify_admin__email_not_sent'           => 'Unable to send administrator email.',
'exception__notify_admin__invalid_webmaster_email'  => 'Unable to send admin email due to invalid webmaster email address.',
'exception__notify_admin__missing_data'             => 'Unable to send admin email due to missing or invalid data.',

'exception__save_entry__missing_data'   => 'Unable to save entry to log due to missing or invalid data.',
'exception__save_entry__not_installed'  => 'The OmniLog module does not appear to be installed.',
'exception__save_entry__not_saved'      => 'A database error occurred whilst attempting to save the log entry.',


/* --------------------------------------------------------------
 * LOG
 * ------------------------------------------------------------ */
'hd_log'            => 'OmniLog',

'lbl_no'            => 'No',
'lbl_type_notice'   => 'Notice',
'lbl_type_warning'  => 'Warning',
'lbl_type_error'    => 'Error',
'lbl_yes'           => 'Yes',

'thd_addon'         => 'Add-on',
'thd_date'          => 'Date',
'thd_type'          => 'Entry Type',
'thd_message'       => 'Message',
'thd_extended_data'	=> 'Extended Data',
'thd_notify_admin'  => 'Notify Admin?',


/* --------------------------------------------------------------
 * NAVIGATION
 * ------------------------------------------------------------ */
'nav_log'           => 'Log',
'nav_demo'          => 'Demo',


// All done.
'' => ''

);

/* End of file      : lang.omnilog.php */
/* File location    : third_party/omnilog/language/english/lang.omnilog.php */
