<?php
class TF_Respond_Controller {

    function __construct() {
    }

    function respond($method) {
        if( method_exists($this, $method) ) {
            return $this->$method();
        } else { 
            return false; 
        }
    }
    
    function all_response() {
        $template = TF_API_DIR . "/template/all-response.php";
        if (file_exists($template)) {
            require_once $template;
        } 
        $template_instance = new TF_API_All_Response();
        return $template_instance->show_all_data();
    }
        
    function friends_response() {
        $template = TF_API_DIR . "/template/friends-response.php";
        if (file_exists($template)) {
            require_once $template;
        } 
        $template_instance = new TF_API_Friends_Response();
        return $template_instance->show_friends_data();
    }
    
    function available_friends_response() {
        $template = TF_API_DIR . "/template/friends-response.php";
        if (file_exists($template)) {
            require_once $template;
        } 
        $template_instance = new TF_API_Friends_Response();
        
        if(!empty($_POST)) {
            $location_id = $_POST['location_id'];  // int id
            $service_id = $_POST['service_id'];    // int id
            $date = $_POST['date'];                // MM/DD/YYYY
            $time = $_POST['time'];                // int minutes
            $timespan = $_POST['timespan'];        // int minutes

            if( (!empty($location_id)) && (!empty($service_id)) && (!empty($date)) && (!empty($time)) && (!empty($timespan))) {
                return $template_instance->show_friends_avaliable($location_id, $service_id, $date, $time, $timespan);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
}

?>
