<?php
require(APPPATH.'libraries/REST_Controller.php');


class Userapi extends REST_Controller
{
    /**
     * returns all lists for id
     */
    function user_get()
    {
        $fbid = $this->get('fbid');
        if (!$fbid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('user_model');
            $user = $this->user_model->get_user($fbid);
            if ($user) {
                $this->response($user, 200);
            } else {
                $this->response(array('error' => 'User could not be found'), 404);
            }
        }
    }
}
?>
