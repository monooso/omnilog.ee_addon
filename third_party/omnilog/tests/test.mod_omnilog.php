<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * OmniLog module tests.
 *
 * @author        Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright     Experience Internet
 * @package       Omnilog
 */

require_once dirname(__FILE__) .'/../mcp.omnilog.php';
require_once dirname(__FILE__) .'/../models/omnilog_model.php';

class Test_omnilog extends Testee_unit_test_case {

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
        $this->_subject = new Omnilog();
    }


}


/* End of file      : test.mod_omnilog.php */
/* File location    : third_party/omnilog/tests/test.mod_omnilog.php */
