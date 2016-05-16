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

    public function show_by_email_schedule() {
        $json = file_get_contents('php://input');
        $obj = json_decode($json);
        
        $email = $obj->{'email'};
        $time_start = $obj->{'time_start'};
        $time_end = $obj->{'time_end'};
        //test purposes
        $email = "david@sucharda.cz";
        $time_start = time() - 1000 * 60 * 60;
        $time_end = time() + 1000 * 60 * 60;
        
        if(!empty($email) && $email != "") {
            global $birchschedule;
            
            $criteria = array("client_email" => $email, "start" => $time_start, "end" => $time_end);
            $appointments = $birchschedule->model->booking->query_appointments( $criteria,
                    array(
                        'appointment_keys' => array(
                            '_birs_appointment_location', '_birs_appointment_service', '_birs_appointment_staff',
                            '_birs_appointment_timespan', '_birs_appointment_timestamp',
                            
                            '_birs_appointment_duration', '_birs_appointment_service',
                            '_birs_appointment_preference'
                        ),
                        'appointment1on1_keys' => array('_birs_appointment_pickup_location', '_birs_appointment_notes'), 
                        'client_keys' => array( 'post_title', '_birs_client_group', '_birs_client_phone', '_birs_client_email' )
                    ) );
            
            $return["schedules"] = array();
            $template = TF_API_DIR . "/template/friends-response.php";
            if (file_exists($template)) {
                require_once $template;
            } 
            $template_instance = new TF_API_Friends_Response();
            foreach( $appointments as $k => $appointment ) {
                $appointment_data["location_id"] = $appointment['_birs_appointment_location'];
                $appointment_data["service_id"] = $appointment['_birs_appointment_service'];
                $appointment_data["staff_id"] = $appointment['_birs_appointment_staff'];
                $appointment_data["timespan"] = $appointment['_birs_appointment_timespan'];
                
                foreach($appointment['appointment1on1s'] as $id => $val) {
                    $appointment_data["group"] = $val['_birs_client_group'];
                
                    $name_surname = $val['_birs_client_name'];
                    $last_word_start = strrpos($name_surname, ' ') + 1;
                    $name = substr($name_surname, 0, - $last_word_start);
                    $surname = substr($name_surname, $last_word_start);
                    
                    $appointment_data["name"] = $name;
                    $appointment_data["surname"] = $surname;
                    $appointment_data["email"] = $email;
                    $appointment_data["phone"] = $val['_birs_client_phone'];
                    $appointment_data["pickup_location"] = $val['_birs_appointment_pickup_location'];
                    $appointment_data["notes"] = $val['_birs_appointment_notes'];
                }
                    
                $date = date('m/d/Y', $appointment['_birs_appointment_timestamp']);
                $appointment_data["date"] = $date;
                $hour = date('H', $appointment['_birs_appointment_timestamp']);
                $minute = date('i', $appointment['_birs_appointment_timestamp']);
                $appointment_data["time"] = $hour . ":" . $minute;
                    
                if(empty($appointment['_birs_appointment_preference']) || ($appointment['_birs_appointment_preference'] == "")) { $appointment['_birs_appointment_preference'] = array(); }
                $appointment_data["preferences"] = $appointment['_birs_appointment_preference'];
                $time_minutes = ($hour * 60) + $minute;
                $available_friends = json_decode($template_instance->show_friends_avaliable($appointment_data["location_id"], $appointment_data["service_id"], $appointment_data["date"], $time_minutes, $appointment_data["timespan"]), true);
                if(empty($available_friends) || ($available_friends == "")) { $available_friends = array(); }
                $appointment_data = array_merge($appointment_data, $available_friends);
                
                $return["schedules"][$k] = $appointment_data;
            }
            
            return json_encode($return);
        } else {
            return json_encode(array("status" => "error", "error" => "Email is not set."));
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
