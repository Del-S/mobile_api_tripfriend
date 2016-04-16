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
        global $birchschedule;

        $ns = $birchschedule->view->bookingform;
        $appointment_id = 0;
        $errors = $ns->validate_booking_info();
        if ( $errors ) {
            return json_encode(array("error" => $errors));
        } else {
            $appointment1on1_id = $ns->schedule();
            $success = $ns->get_success_message( $appointment1on1_id );
            $ns->send_confirm_email( $appointment1on1_id );  // Del_S send confirm email
            return json_encode(array("success" => $success));
            //$birchschedule->view->render_ajax_success_message( $success );
        }
    }

    public function update_schedule() {
        // TODO: update info :)
    }

    public function change_status_schedule() {
        $schedule_id = $_POST['schedule_id'];
        $status = $_POST['status'];
        if( ( isset($schedule_id) ) && ( !empty($schedule_id) ) && ( isset($status) ) && ( !empty($status) ) ) {
            if(get_post_status($schedule_id) != $status) {
                wp_update_post( array('ID' => $schedule_id, 'post_status' => $status), true ); 
                if (is_wp_error($schedule_id)) { 
                    $return = array("error" => "Error during saving.");
                }
                $return = array("success" => "Schedule successfully disabled/enabled.");
            } else {
                $return = array("error" => "Already disabled/enabled.");
            }
        } else {
            $return = array("error" => "Missing schedule ID or status.");
        }
        
        return json_encode($return);
    }
}

?>
