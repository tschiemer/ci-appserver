<?php

/**
 * @todo if called directly or not from CLI abort
 */


class Crontab_Manager {
    
    var $_file_loaded = FALSE;
    
    var $_jobs_changed = FALSE;
    
    var $_my_jobs = array();
    var $_other_jobs = array();
    
    var $_last_job_id = NULL;
    
    /**
     * 
     * @param boolean $reload
     * @return array|boolean FALSE on fail
     */
    public function list_jobs()
    {
        if ($this->_file_loaded === FALSE)
        {
            // read actual file
            $this->_file_loaded = $this->read_file();
            
            // on failure abort
            if ($this->_file_loaded === FALSE)
            {
                show_error('could not read crontab file');
            }
            
            $this->_my_jobs = array();
            
            /**
             * @todo parse jobs
             */
            foreach($this->_file as $job)
            {
                if (preg_match('/().(--once?)(--ci-cronjob-id)=(:alpha)', $subject, $matches))
                {
                    $this->_my_jobs[$job_id] = array(
                        'cmd'   => '',
                        'when'  => array(
                            'second' => 0
                            // etc
                        ),
                        'once'  => FALSE
                    );
                }
                else
                {
                    
                }
            }
        }
        
        return $this->_my_jobs;
    }
    
    /**
     * 
     * @param string $cmd
     * @param type $when
     * @param type $options
     * @return string
     */
    public function add_job($cmd, $when, $options = array())
    {
        /**
         * @todo sanitize cmd
         */
        
        $is_once = (isset($options['once']) and $options['once']);
        
        if (! empty($options['job_id']))
        {
            /**
             * @todo sanitize
             */
            $job_id = $options['job_id'];
        }
        else
        {
            /**
             * @todo randomize id
             */
            $job_id = 'random';
        }
        
        if (is_string($when) and strpos($when,' '))
        {
            $when = explode(' ',$when);
            
        }
        if (is_array($when))
        {
            $when = array_combine(array(),array_values($when));
        }
        
        
        $this->_last_job_id = $job_id;
        
        $this->_jobs_changed = TRUE;
        
        return $this;
    }
    
    public function last_job_id()
    {
        return $this->_last_job_id;
    }
    
    /**
     * 
     * @param string $job_id
     */
    public function remove_job($job_id)
    {
        if (isset($this->_my_jobs[$job_id]))
        {
            unset($this->_my_jobs[$job_id]);
            $this->_jobs_changed = TRUE;
        }
        
        return $this;
    }
    
    /**
     * 
     * @param integer $seconds
     * @param integer $minutes
     * @param integer $hours
     * @param integer $days
     * @return string
     */
    public function when_from_now($seconds = 0, $minutes = 0, $hours = 0, $days = 0)
    {
        return '';
    }
    
    public function commit()
    {
        if ($this->_jobs_changed)
        {
            $my_jobs = array();
            foreach($this->_my_jobs as $job_id => $job)
            {
                // when php __FILE__ --ci-cronjob-id=ID [--once] cmd
                
                if (is_array($job['when']))
                {
                    $when = implode(' ', $job['when']);
                }
                else
                {
                    $when = $job['when'];
                }
                
                $id_str = '--ci-cronjob-id='.$job_id;
                
                if ($job['once'])
                {
                    $once = ' --once '; // please not the whitespaces
                }
                else
                {
                    $once = '';
                }
                
                $cmd = '"'.$job['cmd'].'"';
                
                $my_jobs[] =  $when . ' php ' . __FILE__ . ' ' . $id_str . $once . $cmd;
            }
            $all_jobs = array_merge($my_jobs, $this->_other_jobs);
            
            $this->write_file($all_jobs);
            
            $this->_jobs_changed = FALSE;
        }
    }
    
    /**
     * @return array|null
     */
    public function read_file()
    {
        return array();
    }
    
    /**
     * 
     * @param array|string $cronjobs
     * @return boolean
     */
    public function write_file($cronjobs)
    {
        if (is_array($cronjobs))
        {
            $cronjobs = implode("\n",$cronjobs);
        }
        
        return TRUE;
    }
    
    /**
     * 
     */
    public function clear_file()
    {
        
    }
    
}


if (false and $is_cli)
{
    /**
     * @todo clear file
     */
}