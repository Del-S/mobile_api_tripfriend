<?php
class TF_API_Friends_Response {  
    
    function __construct() {
    }
    
    function show_friends_data() {        
        $response = array();
        $response["friends"] = $this->get_friends_data();
        
        return json_encode($response);
    }
        
    function show_friends_avaliable($location_id, $service_id, $date, $time, $timespan) {
        global $birchschedule, $birchpress;
        
        $date = $birchpress->util->get_wp_datetime(
			array(
				'date' => $date,
				'time' => 0
			)
		);
        
        $staff_options = $birchschedule->model->schedule->get_avaliable_staff_by_time( $location_id, $service_id, $date, $time, $timespan );
        foreach( $staff_options as $id => $staff ) {
            $friends[] = $id;
        }
        return json_encode(array('available_friends' => $friends));
    }
    
    function get_friends_data() {
        global $birchschedule;
        
        $friends = array();
        $all_staff = $birchschedule->model->query( array('post_type' => 'birs_staff'), array( 'base_keys' => array( 'post_title', 'post_content' ), 'meta_keys' => array( '_thumbnail_id', 'lang_img' ) ) );
        foreach( $all_staff as $id => $staff ) {
            $friend['id'] = $id;
            $friend['name'] = $staff['post_title'];
            
            $image_id = $staff['_thumbnail_id'];
            $friend['image'] = wp_get_attachment_url( $image_id );
            //$friend['description'] = $staff['post_content'];
            
            $languages = str_replace ( '"', "" , $staff['lang_img'] );
            $languages = explode(";", $languages);
            $friend['languages'] = $languages;
            
            $friend['update_time'] = date("m/d/y"); 
            $friend['up_to_date'] = true;
            
            $friends[$id] = $friend;
        }
        
        return $friends;
    }
}
?>
