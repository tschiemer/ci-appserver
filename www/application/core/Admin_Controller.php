<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Admin web-interface controller
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Admin_Controller extends CI_Controller {
    
    /**
     *
     * @var Admin_user
     */
    var $user;
    
    public function __construct($allowed_groups=array()) {
        parent::__construct();
        
        $this->load->library('flexi_auth');
        
        $allowed_groups = array_merge(array('Developer','Admin'),$allowed_groups);
        
        if (! $this->flexi_auth->in_group($allowed_groups))
        {
            redirect('user/login');
        }
        
        $this->user = $this->flexi_auth->get_user_by_id($this->flexi_auth->get_user_id())->row();
    }
}



/* End of file Admin_Controller.php 
/* Location: ./application/core/Admin_Controller.php */