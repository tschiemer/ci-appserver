<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Public_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->load->helper('url');
    }
    
}



/* End of file Public_Controller.php 
/* Location: ./application/core/Public_Controller.php */