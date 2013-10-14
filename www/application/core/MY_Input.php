<?php

class MY_Input extends CI_Input {
    
    public function method()
    {
        return $this->server('method');
    }
    
    public function service($format='json')
    {
        $input = file_get_contents('php://input');
        
    }
}