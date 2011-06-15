<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * OmniLog Entry datatype.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Omnilog
 */

require_once PATH_THIRD .'omnilog/helpers/EI_number_helper' .EXT;

class Omnilog_entry {

    const NOTICE    = 1;
    const WARNING   = 2;
    const ERROR     = 3;

    private $_addon_class;
    private $_addon_name;
    private $_date;
    private $_message;
    private $_type;
    

    /* --------------------------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------------------------ */

    /**
     * Constructor.
     *
     * @access  public
     * @param   array        $props        An associative array of instance properties.
     * @return  void
     */
    public function __construct(Array $props = array())
    {
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
     * Returns the add-on class.
     *
     * @access  public
     * @return  string
     */
    public function get_addon_class()
    {
        return $this->_addon_class;
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
     * Returns the entry type.
     *
     * @access  public
     * @return  int
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
     * @return  bool
     */
    public function is_populated()
    {
        return $this->get_addon_class() != ''
            && $this->get_addon_name()  != ''
            && $this->get_date()        != 0
            && $this->get_message()     != ''
            && $this->get_type()        != 0;
    }


    /**
     * Resets the instance properties.
     *
     * @access  public
     * @return  Omnilog_entry
     */
    public function reset()
    {
        $this->_addon_class = '';
        $this->_addon_name  = '';
        $this->_date        = 0;
        $this->_message     = '';
        $this->_type        = 0;

        return $this;
    }


    /**
     * Sets the add-on class.
     *
     * @access  public
     * @param   string        $addon_class        The add-on class.
     * @return  string
     */
    public function set_addon_class($addon_class)
    {
        if (is_string($addon_class))
        {
            $this->_addon_class = $addon_class;
        }

        return $this->get_addon_class();
    }


    /**
     * Sets the add-on name.
     *
     * @access    public
     * @param    string        $addon_name        The add-on name.
     * @return    string
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
     * Sets the entry date.
     *
     * @access  public
     * @param   int|string        $date         The entry date, in UNIX time form.
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
     * Sets the entry type.
     *
     * @access  public
     * @param   int        $type        The entry type.
     * @return  int
     */
    public function set_type($type)
    {
        // Additional valid_int check required in case somebody passes in an object.
        if (valid_int($type)
            && in_array($type, array(self::NOTICE, self::WARNING, self::ERROR)))
        {
            $this->_type = $type;
        }

        return $this->get_type();
    }
    
    
    /**
     * Returns the instance as an associative array.
     *
     * @access  public
     * @return  array
     */
    public function to_array()
    {
        return array(
            'addon_class'   => $this->get_addon_class(),
            'addon_name'    => $this->get_addon_name(),
            'date'          => $this->get_date(),
            'message'       => $this->get_message(),
            'type'          => $this->get_type()
        );
    }


}


/* End of file      : omnilog_entry.php */
/* File location    : third_party/omnilog/classes/omnilog_entry.php */
