<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Service/REST controller
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Service_Controller extends CI_Controller {
    
    /**
     *
     * @var integer
     */
    var $appversion = 0;
    
    /**
     *
     * @var array
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
        $this->load->helper('service');
        
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

//            var_dump($this->input->request_headers());
            
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
    
    public function _get_user_id()
    {
        $user_id = 1;
        
//        $user_id = $this->flexi_auth->get_user_id();
        
        return 1;
    }
    
    /**
     * 
     * @param string $output
     */
    public function _output($output)
    {
        
        switch($this->response->status['code'])
        {
            case SERVICE_STATUS_SERVER_ERROR:
            case SERVICE_STATUS_TEMP_UNAVAILABLE:
                set_status_header(HTTP_SERVER_ERROR);
                break;
            
            case SERVICE_STATUS_OK:
                set_status_header(HTTP_OK);
                break;
            
            default:
                set_status_header(HTTP_CLIENT_ERROR);
                break;
                
        }
        
        if (in_array(ENVIRONMENT, array('development','testing')))
 	{
            $this->response->status['is_logged_in'] = $this->flexi_auth->is_logged_in();
 	}
        echo $this->response->json();
    }
    
}



/* End of file Service_Controller.php 
/* Location: ./application/core/Service_Controller.php */