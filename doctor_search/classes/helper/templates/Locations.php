<?php namespace Helper\Templates;

class Locations
{
    public $post_id = null;
    public $locations = null;
    public $item_count = 1;
    public $current_location = null;

    public function __construct($post_id)
    {
        $this->post_id = $post_id;
        $this->locations = array();
    }

    public function fetchLocations(\Utility\Data\LocationQueryInterface $loc_interface)
    {
        if (($res = $loc_interface->getItemByPostID($this->post_id)) !== false) {
            $this->locations = $res;
        }

        return $this;
    }

    public function nextLocation()
    {
        $this->current_location = array_shift($this->locations);
        $this->item_count++;
    }

    public function hasLocations()
    {
        return !empty($this->locations);
    }

    public function current_count()
    {
        return $this->item_count;
    }

    public function unique_field($fieldName)
    {
        return $fieldName.'_'.$this->item_count;
    }

    public function value($fieldName)
    {
        return  $fieldName;
    }

}
