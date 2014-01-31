<?php namespace Metabox;

class Locations
{
    public function getLocationHtml()
    {
        load_template(CDS_PLUGIN_BASE.'/doctor_search/templates/partials/metaboxes/locations.php');
    }

    public function getStates()
    {
        return array(
            'MD'=>'Maryland',
            'NY'=>'New York',
            'DC'=>'District of Columbia',
            'VA'=>'Virginia'
            );
    }
}
