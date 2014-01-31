<?php
global $post;
use Helper\Templates\Locations;
use	Utility\Data\LocationQuery;
use Helper\Html\Form;
use Metabox\Locations as LocationsViewHelper;
$locations = new Locations($post->ID);
$locations->fetchLocations(new LocationQuery());
?>
<div class="inside">
<table class="" id="ecpt_metabox_locations">
    <tbody data-scope="location-table">
        <?php do { ?>
        <tr id="ecpt_field_1" class="" data-scope="location_item" data-id="<?=$locations->value('ID', 0)?>">
            <th style="width:20%">
                <label>Address <?=$locations->current_count()?></label>
            </th>
            <td>
                <div class="ecpt_repeatable_wrapper">
                    <label for="<?=$locations->unique_field('dcbs_street')?>">Street</label>
                    <input type="text" class="" name="<?=$locations->unique_field('dcbs_street')?>" id="<?=$locations->unique_field('dcbs_street')?>" value="<?=$locations->value('dcbs_street')?>" size="30" style="width:80%">
                    <label for="<?=$locations->unique_field('dcbs_city')?>">City</label>
                    <input type="text" class="" name="<?=$locations->unique_field('dcbs_city')?>" id="<?=$locations->unique_field('dcbs_city')?>" value="<?=$locations->value('dcbs_city')?>" size="30" style="width:80%">
                    <label for="<?=$locations->unique_field('dcbs_state')?>">State</label>
                    <?php echo Form::select($locations->unique_field('dcbs_state'), LocationsViewHelper::getStates(), $locations->value('dcbs_state'), array()); ?>
                    <label for="<?=$locations->unique_field('dcbs_zipcode')?>">Zipcode</label>
                    <input type="text" class="" name="<?=$locations->unique_field('dcbs_zipcode')?>" id="<?=$locations->unique_field('dcbs_zipcode')?>" value="<?=$locations->value('dcbs_zipcode')?>" size="30" style="width:80%">
                    <a href="/wp-admin/doctor-locations/remove" class="button-secondary" data-action="remove-location">x</a>
                    <a href="/wp-admin/doctor-locations/save" class="button-secondary" data-action="save-location">Save Location</a>
                 </div>

            </td>
        </tr>
        <?php $locations->nextLocation(); ?>
        <?php } while ($locations->hasLocations()); ?>
    </tbody>
</table>
<a href="/wp-admin/doctor-locations/add" class="button-primary" data-action="add-location">Add New</a>
</div>
