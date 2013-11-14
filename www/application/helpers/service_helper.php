<?php

/**
 * Service request helper object
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Service_Request implements Countable, ArrayAccess, IteratorAggregate {
    
    /**
     *
     * @var mixed
     */
    var $json_error;
    
    /**
     *
     * @var string
     */
    var $json_type;
    
    
    public function __construct($json_string)
    {
        $obj = json_decode($json_string);
        $this->json_error = json_last_error();
        
        if ($obj instanceof stdClass)
        {
            $this->json_type = 'object';
        }
        elseif (is_array($obj))
        {
            $this->json_type = 'array';
            $this->array = $obj;
        }
        elseif ($obj === NULL)
        {
            $this->json_type = 'null';
        }
        elseif ($obj === TRUE)
        {
            $this->json_type = 'true';
        }
        elseif ($obj === FALSE)
        {
            $this->json_type = 'false';
        }
        
        if (!$this->has_errors() and $this->is_object())
        {
            if (isset($obj->json_error))
            {
                unset($obj->json_error);
            }
            if (isset($obj->json_type))
            {
                unset($obj->json_type);
            }
            foreach($obj as $key => $value)
            {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_object()
    {
        return $this->json_type == 'object';
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_array()
    {
        return $this->json_type == 'array';
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_null()
    {
        return $this->json_type == 'null';
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_false()
    {
        return $this->json_type == 'false';
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_true()
    {
        return $this->json_type == 'true';
    }
    
    /**
     * 
     * @return boolean
     */
    public function has_errors()
    {
        return ! ($this->json_error == JSON_ERROR_NONE);
    }
    
    /**
     * 
     * @return integer
     */
    public function get_error()
    {
        return $this->json_error;
    }
    
    /**
     * 
     * @return string
     */
    public function get_error_msg()
    {
        switch($this->json_error)
        {
            case JSON_ERROR_NONE:
                return 'JSON request: No error.';

            case JSON_ERROR_DEPTH:
                return 'JSON request: The maximum stack depth has been exceeded.';

            case JSON_ERROR_STATE_MISMATCH:
                return 'JSON request: Invalid or malformed JSON.';

            case JSON_ERROR_CTRL_CHAR:
                return 'JSON request: Control character error, possibly incorrectly encoded.';

            case JSON_ERROR_SYNTAX:
                return 'JSON request: Syntax error.';

            case JSON_ERROR_UTF8:
                return 'JSON request: Malformed UTF-8 characters, possibly incorrectly encoded.';
                
            default:
                return 'JSON request: Unknown error.';

        }
    }
    
    /**
     * 
     * @param string $field
     * @param mixed $requirements
     * @return boolean
     */
    public function validate($field, $requirements=NULL)
    {   
        if (is_callable($requirements))
        {
            $array = array($requirements);
            $requirements = $array;
        }
        elseif (is_string($requirements))
        {
            $array = explode(',',$requirements);
            $requirements = $array;
        }
        
        foreach($requirements as $test)
        {
            if (is_callable($test))
            {
                $valid = call_user_func($test, $this->$field);
            }
            elseif ($test == 'isset')
            {
                $valid = isset($this->$field);
            }
            elseif ($test == 'empty')
            {
                $valid = empty($this->$field);
            }
            if (! $valid)
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    /**
     * 
     * @param array $rule_list
     * @return boolean
     */
    public function validate_all($rule_list)
    {
        foreach($rule_list as $field => $requirements)
        {
            $valid = $this->validate($field, $requirements);
            
            if ( ! $valid )
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    
    
    /**
     * Countable
     * @return boolean
     */
    public function count()
    {
        return count($this->array);
    }
    
    /**
     * 
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists( $offset )
    {
        return array_key_exists($offset, $this->array);
    }
    
    /**
     * 
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet( $offset )
    {
        return $this->array[$offset];
    }
    
    /**
     * 
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet( $offset , $value )
    {
        $this->array[$offset] = $value;
    }
    
    /**
     * 
     * @param mixed $offset
     */
    public function offsetUnset( $offset )
    {
        unset($this->array[$offset]);
    }

    /**
     * 
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }
}

if ( ! function_exists('get_service_request'))
{
    /**
     * Service request getter
     * 
     * @copyright (c) 2013, Philip Tschiemer
     * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
     * @package ci-appserver
     * @link https://github.com/tschiemer/ci-appserver 
     * @return \Service_Request
     */
    function get_service_request()
    {
        $input = file_get_contents('php://input');
        
        return new Service_Request($input);
    }
}

/**
 * Service request helper object
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Service_Response {
    
    /**
     *
     * @var array 
     */
    var $status;
    
    public function __construct($status_code = SERVICE_STATUS_OK) {
        $this->status = array(
            'code'  => $status_code
        );
        $this->set_dbgmsg('OK');
    }
    
    /**
     * 
     * @param integer $status_code
     * @param string $dbgmsg
     */
    public function set_status($status_code, $dbgmsg=NULL)
    {
        $this->status['code'] = $status_code;
        $this->set_dbgmsg($dbgmsg);
    }
    
    /**
     * 
     * @param type $msg
     */
    public function set_dbgmsg($msg = NULL)
    {
        switch(ENVIRONMENT)
        {
            case 'development':
            case 'testing':
                if ($msg === NULL)
                {
                    unset($this->status['dbgmsg']);
                }
                else
                {
                    $this->status['dbgmsg'] = $msg;
                }
        }
    }
    
    /**
     * 
     * @return string
     */
    public function json()
    {
        return json_encode($this);
    }
    
}