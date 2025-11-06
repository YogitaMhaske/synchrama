<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    private $table = 'users';

    public function insert($data){
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_by_email($email){
        return $this->db->get_where($this->table, ['email' => $email])->row();
    }

    public function get_by_id($id){
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function update($id, $data){
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function count_dealers($zip = null){
        $this->db->where('type','dealer');
        if($zip){
            $this->db->like('zip', $zip);
        }
        return $this->db->count_all_results($this->table);
    }

    public function get_dealers($limit, $offset, $zip = null){
        $this->db->where('type','dealer');
        if($zip){
            $this->db->like('zip', $zip);
        }
        $this->db->order_by('created_at','DESC');
        $q = $this->db->get($this->table, $limit, $offset);
        return $q->result();
    }
}
