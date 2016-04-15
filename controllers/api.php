<?php
  
class TF_API {  
    
    function __construct() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if (is_plugin_active('birchschedule/birchschedule.php')) { 
            add_action('template_redirect', array(&$this, 'template_redirect'));
            add_filter('query_vars', array(&$this, 'query_vars'));
        }
    }
  
    function template_redirect() {
        if ($method == '404') {
            status_header( 404 );
            get_template_part( 404 ); 
            exit();
        }
        
        $controller_name = $this->get_controller();
        if( ( $controller_name != false ) && ( class_exists($controller_name) ) && ( $this->method != false ) ) {
            $this->controller = new $controller_name();
            $response = $this->controller->respond($this->method);
            
            if($response == false) {
                status_header( 404 );
                echo 'Function returned wrong response!';
            } else {
                echo $response;
            }
            exit();
        } else {
            status_header( 404 );
            get_template_part( 404 ); 
            exit();
        }
    }
    
    function get_controller() {
        $this->method = false;
        $tf_api = $this->get('tf-api');
        if (empty($tf_api)) {
            return false;
        }
        
        $method_array = array( 
            "all" => array( "controller" => "TF_Respond_Controller", "method" => "all_response" ),
            "friends" => array( "controller" => "TF_Respond_Controller", "method" => "friends_response" ),
            "schedule" => array( "controller" => "TF_Schedule_Controller", "method" => $this->get_schedule_method() ) 
        );        
        $this->method = $method_array[$tf_api]["method"];
        return $method_array[$tf_api]["controller"];
    }
    
    function get($key) {
        $wp_query_var = get_query_var($key);
        $query_var = (isset($_REQUEST[$key])) ? $_REQUEST[$key] : null;
        if (!empty($wp_query_var)) {
            return $wp_query_var;
        } else if (!empty($query_var)) {
            return $query_var;
        }
        return null;
    }
    
    function get_schedule_method() {
        $schedule_method = $this->get('sch-method');
        if (empty($schedule_method)) {
            return false;
        }
        
        $method_array = array( 
            "create" => "create_schedule",
            "update" => "update_schedule",
            "disable" => "disable_schedule"
        );
        return $method_array[$schedule_method];
    }
    
    function query_vars($wp_vars) {
        $wp_vars[] = 'tf-api';
        $wp_vars[] = 'sch-method';
        return $wp_vars;
    }

    
}
  ?>