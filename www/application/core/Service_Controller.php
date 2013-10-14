<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


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
     * 
     * @return \Service_Request
     */
    function get_service_request()
    {
        $input = file_get_contents('php://input');
        
        return new Service_Request($input);
    }
}

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

if ( !function_exists('serve_error'))
{
    function service_error($dbgmsg = NULL, $status_code = SERVICE_STATUS_ERROR)
    {
        $ci =& get_instance();
        $ci->response->set_status($status_code,$dbgmsg);
    }
}

class Service_Controller extends CI_Controller {
    
    /**
     *
     * @var integer
     */
    var $appversion = 0;
    
    /**
     *
     * @var string
     */
    var $language = NULL;
    
    /**
     *
     * @var Service_Response
     */
    var $response;
    
    public function __construct() {
        parent::__construct();
        
        $this->load->config('appserver',TRUE);
        
        /**
         * 
         */
        
        $languages = $this->config->item('languages','appserver');
        
        if (is_array($languages))
        {
            $lang = $this->input->get_request_header('Accept-language');
            $lang = Locale::acceptFromHttp($lang);
            $lang = Locale::getPrimaryLanguage($lang);
            
            if ( ! isset($languages[$lang]))
            {
                $lang = $languages['default'];
            }
            
            $this->language = array(
                'iso'       => $lang,
                'local'     => $languages[$lang]
            );
            
            $this->output->set_header('Content-Language: '.$this->language['iso']);
        }
        
        
        /**
         * 
         */
        
        $version_header = $this->config->item('http-version-header','appserver');
        
        if (isset($version_header))
        {
            $appversion = $this->input->get_request_header($version_header);
            
            if ($appversion === NULL or intval($appversion) != $appversion)
            {
                show_service_error('Missing the version header - are you actually an app?',SERVICE_STATUS_ERROR);
            }
            else
            {
                $this->appversion = intval($appversion);
            }
            
            $minversion = $this->config->item('require-app-version','appserver');
            if ($this->appversion < $minversion)
            {
                show_service_error('App version is too old, please update.',SERVICE_STATUS_UPDATE_REQUIRED);
            }
        }
        
        /**
         * Load user authentication
         */
        $auth_library = $this->config->item('auth-library','appserver');
        if ( ! empty($auth_library))
        {
            $this->load->library($auth_library['library'],$auth_library['params'],$auth_library['object_name']);
        }
        
        // Set response content type to JSON
        $this->output->set_content_type('application/json');
        
        // Initialize the response object.
        $this->response = new Service_Response();
    }
    
    /**
     * Helper
     * @param string $dbgmsg
     * @param integer $status_code
     */
    public function _error($dbgmsg=NULL,$status_code=SERVICE_STATUS_ERROR)
    {
        $this->response->set_status($status_code,$dbgmsg);
    }
    
    /**
     * 
     */
    public function _require_authorization()
    {
        /**
         * @todo customize to your own requirements
         */
        
        // If not customized,  
//        show_service_error(SERVICE_STATUS_NOT_AUTHORIZED, 'not authorized: please customize your authorization process in the service controller');
        
        
        $is_authorized = TRUE;
        
//        $is_authorized = $this->flexi_auth->is_logged_in();
//        $is_authorized = $this->flexi_auth->in_group('AppUser');
//        $is_authorized = $this->flexi_auth->is_privileged('can_use_app');
        
        if (!$is_authorized)
        {
            show_service_error(SERVICE_STATUS_NOT_AUTHORIZED, 'not authorized');
        }
    }
    
    /**
     * 
     * @param string $output
     */
    public function _output($output)
    {
        echo $this->response->json();
    }
    
}



/* End of file Service_Controller.php 
/* Location: ./application/core/Service_Controller.php */