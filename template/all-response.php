<?php
class TF_API_All_Response {  
    
    function __construct() {
    }
    
    function show_all_data() {
        global $birchschedule, $birchpress;
        
        $response = array();
        
        $b_preferences = $birchschedule->model->get_preferences_data();
        foreach ($b_preferences as $id => $preference) {
            $r_preferences[] = $preference['display'];
        }
        $response['preferences'] = $r_preferences;
        return json_encode($response);
    }
}
?>