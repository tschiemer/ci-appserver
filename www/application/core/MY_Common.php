<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * @param boolean $enable_guessing
 * @return string
 */
if ( ! function_exists('get_context'))
{
    function get_context($enable_guessing = TRUE)
    {
        if (class_exists('Service_Controller', FALSE))
        {
            return 'service';
        }
        if (class_exists('Public_Controller',FALSE))
        {
            return 'public';
        }
        if (class_exists('Admin_Controller',FALSE))
        {
            return 'admin';
        }
        if (class_exists('Worker_Controller',FALSE))
        {
            return 'worker';
        }
        
        if ($enable_guessing)
        {
            /**
             * @todo try to guess context from request uri
             */
        }
        
        return 'unknown';
        
    }
    
}

if ( ! function_exists('is_cli_request'))
{
    function is_cli_request()
    {
        $_input =& load_class('Input', 'core');
        
        return $_input->is_cli_request();
    }
}


if ( ! function_exists('show_service_error'))
{
	function show_service_error($dbgmsg=NULL, $status_code=SERVICE_STATUS_ERROR, $http_code=200)
	{                   
            $_error =& load_class('Exceptions', 'core');
            echo $_error->show_service_error($dbgmsg, $status_code, $http_code);
            
            exit;
	}
}

