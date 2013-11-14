<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Developer helper controller (only available in development, and testing environment)
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Dev_Controller extends CI_Controller {
    
    var $msg_list = array();
    
    public function __construct() {
        parent::__construct();
        
        if (! in_array(ENVIRONMENT,array('testing','development')))
        {
            show_404();
        }
        
        $this->load->helper('url');
        $this->load->helper('service');
    }
    
    public function _error($msg,$code=200)
    {
        show_error("Status {$code}: $msg");
    }
    
}



/* End of file Public_Controller.php 
/* Location: ./application/core/Public_Controller.php */