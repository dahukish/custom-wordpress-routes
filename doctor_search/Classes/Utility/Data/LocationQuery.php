<?php namespace Utility\Data;

class LocationQuery implements LocationQueryInterface
{
    public function __construct() {}

    public function getItemByPostID($post_id)
    {
        global $wpdb;

        $sql = "SELECT * FROM wp_locations WHERE post_id=%d";

        $sql = $wpdb->prepare($sql, $post_id);

        $res = $wpdb->get_results($sql);

        if(!empty($res)) return $res;

        return false;
    }
}
