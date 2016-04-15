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
        
    }
    
}

?>
