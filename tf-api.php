<?php
/*
 * Plugin Name: TripFriend API
 * Plugin URI: #
 * Description: RESTful API for TripFriend mobile app.
 * Author: David Sucharda
 * Version: 0.1
 * Author URI: http://idefixx.cz
 * License: GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('TF_API_DIR', dirname(__FILE__));
@include_once TF_API_DIR . "/controllers/api.php";
@include_once TF_API_DIR . "/controllers/respond.php";
@include_once TF_API_DIR . "/controllers/schedule.php";

function tf_init() {
    add_filter('rewrite_rules_array', 'tf_api_rewrites');
    
    $tf_api = new TF_API();
}

function tf_plugin_activation() {
    // Add the rewrite rule on activation
    global $wp_rewrite;
    add_filter('rewrite_rules_array', 'tf_api_rewrites');
    $wp_rewrite->flush_rules();
}

function tf_plugin_deactivation() {
    // Remove the rewrite rule on deactivation
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function tf_api_rewrites($wp_rules) {
  $tf_api_rules = array(
    "tf-api\$" => 'index.php?tf-api=all',
    "tf-api/friends\$" => 'index.php?tf-api=friends',
    "tf-api/friends-available\$" => 'index.php?tf-api=friends-available',
    "tf-api/schedule/(.+)\$" => 'index.php?tf-api=schedule&sch-method=$matches[1]'
  );
  return array_merge($tf_api_rules, $wp_rules);
}

add_action( 'init', 'tf_init' );
register_activation_hook( __FILE__, 'tf_plugin_activation' );
register_deactivation_hook( __FILE__, 'tf_plugin_deactivation' );
