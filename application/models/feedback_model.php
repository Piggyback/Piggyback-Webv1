<?php

class Feedback_Model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /* restful api */
    
    public function add_feedback($comment,$uid,$date) {
        $data = array(
            'referenceNum' => NULL,
            'comment' => $comment,
            'uid' => $uid,
            'date' => $date
        );
        
        $this->db->insert('Feedback',$data);
    }
    
}
?>