<?php
class TF_API_All_Response {  
    
    function __construct() {
        $template_friends = TF_API_DIR . "/template/friends-response.php";
        if (file_exists($template_friends)) {
            require_once $template_friends;
        } 
        $this->template->friends = new TF_API_Friends_Response();
    }
    
    function show_all_data() {        
        $response = array();
        $response["config"] = $this->get_basic_config();
        $response["friends"] = $this->template->friends->get_friends_data();
        
        return json_encode($response);
    }
    
    function get_basic_config() {
        global $birchschedule;
        
        $return = array();
        $return['locations'] = $this->get_locations();
        $return['languages'] = $this->get_languages();
        $return['time_spans'] = $birchschedule->model->get_time_spans();
        $return['preferences'] = $this->get_preferences();
        $return['start_time'] = $birchschedule->model->get_start_time();
        $return['end_time'] = $birchschedule->model->get_end_time();
        
        return $return;
    }
    
    function get_locations() {
        global $birchschedule;
        
        $b_locations = $birchschedule->model->get_locations_map();
        foreach ($b_locations as $id => $location) {
            $locations[$id] = $location['_birs_location_name'];
        }
        return $locations;
    }
    
    function get_languages() {
        global $birchschedule;
        
        print_r($b_languages);
        
        $r_languages = array(); 
        $b_languages = $birchschedule->model->get_locations_services_map();
        foreach ($b_languages as $l_id => $languages) {
            foreach($languages as $id => $language) {
                if( !array_key_exists( $id, $r_languages ) ) {
                    $r_languages[$id] = $language;
                }
            }
        }
        
        return $r_languages;
    }
    
    function get_preferences() {
        global $birchschedule;
        
        $b_preferences = $birchschedule->model->get_preferences_data();
        foreach ($b_preferences as $id => $preference) {
            $r_preferences[] = $preference['display'];
        }
        return $r_preferences;
    }
}
?>