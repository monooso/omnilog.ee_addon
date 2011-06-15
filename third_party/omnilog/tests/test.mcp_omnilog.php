<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * OmniLog module control panel tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once PATH_THIRD .'omnilog/mcp.omnilog' .EXT;
require_once PATH_THIRD .'omnilog/tests/mocks/mock.omnilog_model' .EXT;

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
    

}


/* End of file      : test.mcp_omnilog.php */
/* File location    : third_party/omnilog/tests/test.mcp_omnilog.php */
