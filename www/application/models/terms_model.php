<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Terms & conditions model
 * 
 * @copyright (c) 2013, Philip Tschiemer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 * @package ci-appserver
 * @link https://github.com/tschiemer/ci-appserver 
 */
class Terms_model extends CI_Model {
    
    var $tbl_terms = 'terms_and_conditions';
    var $tbl_accept = 'customer_terms_acceptance';
    
    var $tbl_types = array(
        'terms_version' => 'int',
        'terms_issue_date'=> 'int',
        'terms_is_latest' => 'bool'
    );
    
    
    public function get_terms_by_version($terms_version)
    {
        $terms = $this->db->where('terms_version',$terms_version)
                            ->get($this->tbl_terms)
                            ->row();
        if (!empty($terms))
        {
            $terms = cast_objfields($terms, $this->tbl_types);
        }
        
        return $terms;
    }
    
    public function get_latest_terms()
    {
        $terms = $this->db->where('terms_is_latest',1)
                            ->get($this->tbl_terms)
                            ->row();
        if (!empty($terms))
        {
            $terms = cast_objfields($terms, $this->tbl_types);
        }
        
        return $terms;
    }
    
    public function get_all_terms()
    {
        $query = $this->db->get($this->tbl_terms);
        
        $list = array();
        foreach($query->result() as $terms)
        {
            $list = cast_objfields($terms, $this->tbl_types);
        }
        return $list;
    }
    
    public function set_latest_terms($terms_version)
    {
        $this->db->trans_start();
        
        $this->db->where('terms_is_latest',1)
                 ->set('terms_is_latest',0)
                 ->update($this->tbl_terms);
        
        $this->db->where('terms_version',$terms_version)
                 ->set('terms_is_latest',1)
                 ->update($this->tbl_terms);
                
        $this->db->trans_complete();
        
        return (bool)$this->db->trans_status();
    }
    
    public function insert_terms($terms)
    {
        $this->db->set($terms)
                 ->insert($this->tbl_terms);
                
        return $this->db-insert_id();
    }
    
    public function update_terms($terms_version,$terms)
    {
        $this->db->where('terms_version',$terms_version)
                 ->set($terms)
                 ->update($this->tbl_terms);
        
        return $this->db->affected_rows();
    }
    
    public function delete_terms($terms_version)
    {
        $this->db->where('terms_version',$terms)
                 ->delete($this->tbl_terms);
        
        return $this->db->affected->rows();
    }
    
    
    public function last_accepted_terms($user_id)
    {
        $query = $this->db->select('terms_version')
                 ->where('customer_id',$user_id)
                 ->order_by('terms_version','desc')
                 ->limit(1)
                 ->get($this->tbl_accept);
        
        $version = 0;
        if ($query->num_rows() > 0)
        {
            $version = $query->row()->terms_version;
        }
        return $version;
    }
    
    public function accept_terms($user_id,$terms_version)
    {
        $this->db->set('customer_id',$user_id)
                 ->set('terms_version',$terms_version)
                 ->set('terms_acceptance_date',date('Y-m-d H:i:s',time()))
                 ->insert($this->tbl_accept);
        
        return $this->db->affected_rows();
    }
    
}


/* End of file terms_model.php */
/* Location: ./application/models/terms_model.php */