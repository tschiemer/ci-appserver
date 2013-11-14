<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

    function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        switch(get_context())
        {
            case 'service':
                if (is_array($message))
                {
                    $message = implode("\n",$message);
                }
                show_service_error($message, SERVICE_STATUS_SERVER_ERROR, $status_code);
                break;
            
            case 'worker':
                if (is_cli_request())
                {
                    if (in_array(ENVIRONMENT,array('testing','production')))
                    {
                        notify_admin('error', 'CLI Error', $message);
                        log_message('error', 'CLI Error: '.$message);
                        exit;
                    }
                    else
                    {
                        log_message('error', 'CLI Error: '. $message);
                        exit;
                    }
                }
                else
                {
                    return parent::show_error($heading, $message, $template, $status_code);
                }
            
            default:
            case 'dev':
            case 'admin':
            case 'public':
                
                return parent::show_error($heading, $message, $template, $status_code);
                
        }
    }
    
    public function show_php_error($severity, $message, $filepath, $line)
    {
        switch(get_context())
        {
            case 'service':
                if (is_array($message))
                {
                    $message = implode("\n",$message);
                }
                show_service_error("PHP Error: {$message} in {$filepath}, line {$line}",SERVICE_STATUS_SERVER_ERROR);
                break;
            
            case 'worker':
                if (is_cli_request())
                {
                    if (in_array(ENVIRONMENT,array('testing','production')))
                    {
                        notify_admin('error', 'CLI PHP Error', $message);
                        log_message('error', 'CLI Error: '.$message, TRUE);
                        exit;
                    }
                    else
                    {
                        log_message('error', 'CLI PHP Error: '. $message);
                        exit;
                    }
                }
                else
                {
                    parent::show_php_error($severity, $message, $filepath, $line);
                }
                break;
            
            default:
            case 'admin':
            case 'public':
            case 'dev':
                parent::show_php_error($severity, $message, $filepath, $line);
        }
    }
    
    function show_404($page = '', $log_error = TRUE)
    {
        // By default we log this, but allow a dev to skip it
        if ($log_error)
        {
                log_message('error', '404 Page Not Found --> '.$page);
        }
        
        switch(get_context())
        {
            case 'service':
                if (is_array($page))
                {
                    $page = implode("\n",$page);
                }
                show_service_error("404 Not found: {$page}",SERVICE_STATUS_NOT_FOUND);
                break;
            
            case 'worker':
                if (is_cli_request())
                {
                    if (in_array(ENVIRONMENT,array('testing','production')))
                    {
                        notify_admin('error', 'CLI 404 Error', $message);
                        log_message('error', 'CLI 404 Error: '.$message);
                        exit;
                    }
                    else
                    {
                        log_message('error', 'CLI 404 Error: '. $message);
                        exit;
                    }
                }
                else
                {
                    parent::show_php_error('bad', "Trying to run invalid worker: {$page}", 0, 0);
                }
                break;
            
            default:
            case 'admin':
            case 'public':
            case 'dev':
                $heading = "404 Page Not Found";
                $message = "The page you requested was not found.";
                echo $this->show_error($heading, $message, 'error_404', 404);
                exit;
        }

            

    }
    
    public function show_service_error($dbgmsg='',$status_code=SERVICE_STATUS_ERROR, $http_code=200)
    {   
        set_status_header($http_code);
        header('Content-Type: application/json');
        
        $response = new stdClass();
        
        $response->status['code']     = $status_code;
        
        switch(ENVIRONMENT)
        {
            case 'development':
            case 'testing':
                $response->status['dbgmsg'] = $dbgmsg;
                break;
            
            case 'production':
                // do nothing
        }
        
        if (ob_get_level() > $this->ob_level + 1)
        {
                ob_end_flush();
        }
        ob_start();
        echo json_encode($response);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    
}

/* End of file MY_Exceptions.php 
/* Location: ./application/core/MY_Exceptions.php */