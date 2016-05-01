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

        $json = file_get_contents('php://input');
        $obj = json_decode($json);
        
        $_POST['birs_appointment_location'] = $obj->{'location_id'};
        $_POST['birs_appointment_service'] = $obj->{'service_id'};
        $_POST['birs_appointment_staff'] = $obj->{'staff_id'};
        $_POST['birs_appointment_date'] = $obj->{'date'};
        $_POST['birs_appointment_time'] = $obj->{'time'};
        $_POST['birs_appointment_timespan'] = $obj->{'timespan'};
        $preferences = $obj->{'preferences'};
        $preferences = explode(",", $preferences);
        $_POST['birs_appointment_preference'] = $preferences;
        
        $_POST['birs_appointment_pickup_location'] = $obj->{'pickup_location'};
        $_POST['birs_appointment_notes'] = $obj->{'notes'};

        $_POST['birs_client_fields'] = array('_birs_client_name_first', '_birs_client_name_last', '_birs_client_email', '_birs_client_email', '_birs_client_phone', '_birs_client_group');
        $_POST['birs_client_name_first'] = $obj->{'name'};
        $_POST['birs_client_name_last'] = $obj->{'surname'};
        $_POST['birs_client_email'] = $obj->{'email'};
        $_POST['birs_client_phone'] = $obj->{'phone'};
        $_POST['birs_client_group'] = $obj->{'group'};
            
        $ns = $birchschedule->view->bookingform;
        $appointment_id = 0;
        $errors = $ns->validate_booking_info();
        if ( $errors ) {
            return json_encode(array("status" => "error", "error" => $errors));
        } else {
            $appointment1on1_id = $ns->schedule();
            $success = $ns->get_success_message( $appointment1on1_id );
            $ns->send_confirm_email( $appointment1on1_id );  // Del_S send confirm email
            return json_encode(array("status" => "success", "success" => $success));
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
