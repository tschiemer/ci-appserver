<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


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
        
        
    }
}



/* End of file Worker_Controller.php 
/* Location: ./application/core/Worker_Controller.php */