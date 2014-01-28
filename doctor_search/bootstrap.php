<?php

#load vendor stuff
require_once(CDS_PLUGIN_BASE.'/doctor_search/vendor/pimple/lib/Pimple.php');

# load eveything we need for the plugin
#
# TODO autoload
#
require_once(CDS_PLUGIN_BASE.'/doctor_search/classes/input/form.php');
require_once(CDS_PLUGIN_BASE.'/doctor_search/classes/routes/router.php');
require_once(CDS_PLUGIN_BASE.'/doctor_search/classes/utility/server/request.php');
require_once(CDS_PLUGIN_BASE.'/doctor_search/classes/utility/server/response.php');
require_once(CDS_PLUGIN_BASE.'/doctor_search/classes/utility/service/di.php');
require_once(CDS_PLUGIN_BASE.'/doctor_search/classes/metabox/locations.php');

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
     #add_meta_box('wpt_office_locations', 'Office Location(s)', array('\Metabox\MetaboxLocations','getLocationHtml'), 'doctors', 'normal', 'default');
}

# add custom dbTable logic

global $dcs_db_version;
$dcs_db_version = "1.0";

function dcsdb_install()
{
   global $wpdb;
   global $dcs_db_version;;

   $table_name = $wpdb->prefix . "locations";

   $sql = "CREATE TABLE $table_name (
      id mediumint(11) NOT NULL AUTO_INCREMENT,
      post_id mediumint(11) 	DEFAULT 0  NOT NULL,
      street1 VARCHAR(255) 	DEFAULT '' NOT NULL,
      street2 VARCHAR(255) 	DEFAULT '' NOT NULL,
      city VARCHAR(128)		DEFAULT '' NOT NULL,
      state VARCHAR(32)		DEFAULT '' NOT NULL,
      zipcode VARCHAR(32)	DEFAULT '' NOT NULL,
      UNIQUE KEY id (id)
        );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );

   add_option( "dcs_db_version", $dcs_db_version );
}
