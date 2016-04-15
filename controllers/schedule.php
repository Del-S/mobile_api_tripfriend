<?php
class TF_Schedule_Controller {

    function __construct() {
    }

    function respond($method) {
        if( method_exists($this, $method) ) {
            return $this->$method();
        } else { 
            return false; 
        }
    }
    
    public function create_schedule() {
        return "asvsvsv";
    }

    public function update_schedule() {

    }

    public function disable_schedule() {

    }
}

?>
