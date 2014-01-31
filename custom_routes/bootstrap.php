<?php

#load vendor stuff
require_once(CDS_PLUGIN_BASE.'/custom_routes/vendor/pimple/lib/Pimple.php');

# load eveything we need for the plugin
#
# TODO autoload
#
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Input/Form.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Helper/Validation.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Helper/Html/Form.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Routes/Router.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Utility/Server/Request.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Utility/Server/Response.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Utility/Service/DI.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Helper/Templates/Locations.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Utility/Data/LocationQueryInterface.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Utility/Data/LocationQuery.php');
require_once(CDS_PLUGIN_BASE.'/custom_routes/Classes/Metabox/Locations.php');

add_action('send_headers', array('\Routes\Router','loadRoutes'));

#custom taxomony type

// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_doctor_taxonomy', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_doctor_taxonomy()
{

    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => __( 'Regions' ),
        'singular_name'     => __( 'Region' ),
        'search_items'      => __( 'Search Regions' ),
        'all_items'         => __( 'All Regions' ),
        'parent_item'       => __( 'Parent Region' ),
        'parent_item_colon' => __( 'Parent Region:' ),
        'edit_item'         => __( 'Edit Region' ),
        'update_item'       => __( 'Update Region' ),
        'add_new_item'      => __( 'Add New Region' ),
        'new_item_name'     => __( 'New Region Name' ),
        'menu_name'         => __( 'Regions' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        // 'rewrite'           => array( 'slug' => 'regions' ),
    );

    register_taxonomy( 'regions', array( 'doctors' ), $args );
}

# add metabox
add_action( 'add_meta_boxes', 'add_location_metaboxes' );

function add_location_metaboxes()
{
  add_meta_box('wpt_office_locations', 'Office Location(s)', array('\Metabox\Locations','getLocationHtml'), 'doctors', 'normal', 'default');
}

# add custom dbTable logic
global $dcs_db_version;
$dcs_db_version = "1.0";

function dcsdb_install()
{
  global $wpdb;
  global $dcs_db_version;

   # MU Plugins don't get activation hooks so this is a hack
   if (get_option( "dcsdb_table_created" ) === false) {

       $table_name = $wpdb->prefix . "locations";

       $sql = "CREATE TABLE $table_name (
          id mediumint(11) NOT NULL AUTO_INCREMENT,
          post_id mediumint(11) 	DEFAULT 0  NOT NULL,
          street1 VARCHAR(255) 	DEFAULT '' NOT NULL,
          street2 VARCHAR(255) 	DEFAULT '' NOT NULL,
          city VARCHAR(128)		DEFAULT '' NOT NULL,
          state VARCHAR(32)		DEFAULT '' NOT NULL,
          zipcode VARCHAR(32)	DEFAULT '' NOT NULL,
          latitude FLOAT(11,11) DEFAULT 0  NOT NULL,
          longitude FLOAT(11,11) DEFAULT 0 NOT NULL,
          UNIQUE KEY id (id)
            );";

       require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
       dbDelta( $sql );

       update_option( "dcs_db_version", $dcs_db_version );
       update_option( "dcsdb_table_created", true);
   }

}

# call the database setup
dcsdb_install();

// let's start by enqueuing our styles correctly
function dcs_admin_styles()
{
    wp_register_style( 'dcs_admin_stylesheet', CDS_PLUGIN_URL.'/styles/doctor_admin.css');
    wp_enqueue_style( 'dcs_admin_stylesheet' );

    // wp_register_script( 'dcs_admin_ajax', CDS_PLUGIN_URL.'/scripts/ajax.js');
    wp_register_script( 'dcs_admin_js', CDS_PLUGIN_URL.'/scripts/dcs_admin.js');
    wp_enqueue_script( 'jquery' );
    // wp_enqueue_script( 'dcs_admin_ajax', array('jquery'));
    wp_enqueue_script( 'dcs_admin_js', array('jquery'));
}
add_action( 'admin_enqueue_scripts', 'dcs_admin_styles' );
