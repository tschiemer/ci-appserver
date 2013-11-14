<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Cronjob/CLI controller
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Worker_Controller extends CI_Controller {
    
    /**
     *
     * @var boolean
     */
    var $is_cli_request;
    
    public function __construct() {
        parent::__construct();
        
        $this->load->config('appserver',TRUE);
        
        $this->is_cli_request = $this->input->is_cli_request();
        
        // do not allow webbased access on production server
        if ( ! $this->is_cli_request and ENVIRONMENT == 'production')
        {
            show_404();
        }
    }
}



/* End of file Worker_Controller.php 
/* Location: ./application/core/Worker_Controller.php */