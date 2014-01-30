<?php
/*
  Plugin Name: Custom Doctors Search
  Description: WPMU custom search plugin
  Author: Stephen Hukish	
  Version: 1.0
 */

// Our plugin
define( 'CDS_PLUGIN_BASE', dirname(__FILE__));

//setup custom doctor search plugin root
if(is_multisite()) {
	define('CDS_PLUGIN_URL', network_site_url('/wp-content/mu-plugins/doctor_search'));
} else {
	define('CDS_PLUGIN_URL', content_url('/mu-plugins/doctor_search'));
}

require_once(dirname(__FILE__)."/doctor_search/bootstrap.php");