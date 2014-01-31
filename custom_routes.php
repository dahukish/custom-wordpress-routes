<?php
/*
  Plugin Name: Custom Routes
  Description: WPMU custom routes handling with controllers
  Author: Stephen Hukish
  Version: 1.0
 */

// Our plugin
define( 'CDS_PLUGIN_BASE', dirname(__FILE__));

//setup custom doctor search plugin root
if (is_multisite()) {
    define('CDS_PLUGIN_URL', network_site_url('/wp-content/mu-plugins/custom_routes'));
} else {
    define('CDS_PLUGIN_URL', content_url('/mu-plugins/custom_routes'));
}

require_once(dirname(__FILE__)."/custom_routes/bootstrap.php");
