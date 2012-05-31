<?php
require(APPPATH.'libraries/REST_Controller.php');


class Searchapi extends REST_Controller
{
    /**
     * returns foursquare search results for given location and query
     */
    function search_get()
    {
        $location = $this->get('location');
        $query = $this->get('query');
        // TODO: add cases for if no location or no query
        if (!$location || !$query) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('search_vendors_model');
            $results = $this->search_vendors_model->search_vendors($location,$query);
            if ($results) {
                $this->response($results, 200);
            } else {
                $this->response(NULL, 404);
            }
        }
    }
}

?>
