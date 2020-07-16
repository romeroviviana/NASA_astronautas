<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

$option1 = "nasa_agradecimiento";
$option1 = "nasa_logo";
$option1 = "nasa_introduccion";
 
// For site options in Multisite

delete_option( $option1 ); 
delete_option( $option2 ); 
delete_option( $option3 ); 

global $wpdb;
$tabla_aspirantes = $wpdb->prefix . 'nasa_form';
$wpdb->query("DROP TABLE IF EXISTS {$tabla_aspirantes}");



 