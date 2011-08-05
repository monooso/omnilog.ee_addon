<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * OmniLog module control panel tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once PATH_THIRD .'omnilog/mcp.omnilog.php';
require_once PATH_THIRD .'omnilog/classes/omnilog_entry.php';
require_once PATH_THIRD .'omnilog/tests/mocks/mock.omnilog_model.php';

class Test_omnilog_mcp extends Testee_unit_test_case {
    
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
        $this->_ee->omnilog_model = $this->_get_mock('model');
        $this->_model   = $this->_ee->omnilog_model;
        $this->_subject = new Omnilog_mcp();
    }


    public function test__log__success()
    {
        $log_entries = array('a', 'b', 'c');
        $page_title = 'Example Page Title';
        $webmaster  = 'webmaster@website.com';

        $view       = 'log';
        $view_vars  = array(
            'cp_page_title'     => $page_title,
            'log_entries'       => $log_entries,
            'webmaster_email'   => $webmaster
        );

        $this->_ee->lang->expectAtLeastOnce('line', array('hd_log'));
        $this->_ee->lang->setReturnValue('line', $page_title);

        $this->_ee->config->expectOnce('item', array('webmaster_email'));
        $this->_ee->config->setReturnValue('item', $webmaster, array('webmaster_email'));

        $this->_model->expectOnce('get_log_entries');
        $this->_model->setReturnValue('get_log_entries', $log_entries);

        $this->_ee->load->expectOnce('view', array($view, $view_vars, TRUE));
        $this->_subject->log();
    }

}


/* End of file      : test.mcp_omnilog.php */
/* File location    : third_party/omnilog/tests/test.mcp_omnilog.php */
