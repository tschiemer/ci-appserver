<?php

class Testing extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        
        if ( ! in_array(ENVIRONMENT, array('development','testing')))
        {
            show_404();
        }
    }
    
    public function index()
    {
        $this->output->set_output("Hello Foobar.\n");
    }
    
}