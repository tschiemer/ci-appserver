<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Public web-frontend controller
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Public_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->load->helper('url');
    }
    
}



/* End of file Public_Controller.php 
/* Location: ./application/core/Public_Controller.php */