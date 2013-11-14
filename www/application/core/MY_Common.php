<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('get_context'))
{
    /**
     * 
     * @param boolean $enable_guessing
     * @return string
     */
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
        if (class_exists('Dev_Controller', FALSE))
        {
            return 'dev';
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
	function show_service_error($dbgmsg=NULL, $status_code=SERVICE_STATUS_ERROR, $http_code=NULL)
	{                   
            if ($http_code === NULL)
            {
                switch($status_code)
                {
                    case SERVICE_STATUS_SERVER_ERROR:
                    case SERVICE_STATUS_TEMP_UNAVAILABLE:
                        $http_code = HTTP_SERVER_ERROR;
                        break;

                    default:
                        $http_code = HTTP_CLIENT_ERROR;
                        break;
                }
            }
            
            $_error =& load_class('Exceptions', 'core');
            echo $_error->show_service_error($dbgmsg, $status_code, $http_code);
            
            if (ENVIRONMENT != 'development')
            {
                switch($status_code)
                {
                    case SERVICE_STATUS_SERVER_ERROR:
                    case SERVICE_STATUS_TEMP_UNAVAILABLE:
                        notify_admin('error',$dbgmsg);
                        break;
                }
            }
            exit;
	}
}

if ( ! function_exists('notify_admin'))
{
    function notify_admin($type='info',$subject, $message=NULL)
    {
        $ci =& get_instance();
        
        $ci->load->config('appserver');
        $ci->load->library('email');
        
        $contacts = $ci->config->item('contacts','appserver');
        
        
        $ci->email->clear();
        $ci->email->set_mailtype('html');
        $ci->email->subject("[{$contacts['notify']['subject']}] {$type}");
        $ci->email->to($contacts['notify']['email']);
        $ci->email->from($contacts['sender']['email'],$contacts['sender']['name']);
        
        $vars['reason'] = $subject;
        if (is_array($message))
        {
            $vars['message'] = print_r($message, true);
        }
        else
        {
            $vars['message'] = $message;
        }
        $vars['environment'] = $GLOBALS;
        $body = $ci->load->view('emails/notify_admin',$vars, TRUE);
        
        $ci->email->message($body);
        $ci->email->send();
    }
}


//if (! function_exists('run_worker'))
//{
//    function run_worker($name, $method='run', $directory='workers', $path_to_loader=NULL)
//    {
//            if ($path_to_loader === NULL)
//            {
//                $loader = realpath(SELF);
//            }
//            else
//            {
//                $loader = $path_to_loader;
//            }
//            
//            $cmd = "php \"{$loader}\" {$directory} \"{$name}\" \"{$method}\"";
//            
//            $descriptorspec = array(
//                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
//                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
//                2 => array("pipe", "w") // stderr is a file to write to
//            );
//            $pipesarray = array();
//            
//            
//            $process = proc_open($cmd, $descriptorspec, $pipesarray);
//            
//            if ($process === FALSE)
//            {
//                log_message('info', "Could not run worker {$name}");
//                return -1;
//            }
//            else
//            {
//                log_message('info', "Could  run worker {$name}");
//                return proc_close($process);
//                //@todo error handling of temrination status
//            }
//    }
//}
//
//if (!function_exists('run_worker_on_shutdown'))
//{
//    function run_worker_on_shutdown($name,$method='run',$directory='workers')
//    {
//        $path_to_loader = realpath(SELF);
//        
//        register_shutdown_function('run_worker', $name, $method, $directory,$path_to_loader);
//    }
//}
//
//if (! function_exists('run_worker_delayed'))
//{
//    function run_worker_delayed($name,$method='run',$directory='workers',$in_minutes=1,$in_hours=0,$in_days=0)
//    {
//        $ci = get_instance();
//        $ci->load->model('cronjob_model');
//        
//        if ($directory != '')
//        {
//            $directory = '/'.$directory;
//        }
//        
//        $command = "{$directory}/{$name}/{$method}";
//        $ci->cronjob_model->schedule_once_in($command,NULL,0,$in_minutes,$in_hours,$in_days);
//        $ci->cronjob_model->commit_to_cron();
//    }
//}
//

