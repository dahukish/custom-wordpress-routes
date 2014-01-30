<?php
global $post;
use Helper\Templates\Locations;
use	Utility\Data\LocationQuery;
$locations = new Locations($post->ID);
$locations->fetchLocations(new LocationQuery());
?>
<div class="inside test">
<table class="form-table" id="ecpt_metabox_locations">
    <tbody>
        <?php do { ?>
        <tr id="ecpt_field_1" class="ecpt_field_type_repeatable ui-sortable" data-scope="location_<?=$locations->current_count()?>">
            <th style="width:20%">
                <label for="ecpt_education">Address <?=$locations->current_count()?></label>
            </th>
            <td>
                <div class="ecpt_repeatable_wrapper">
                    <label for="<?=$locations->unique_field('dcbs_street')?>">Street</label>
                    <input type="text" class="ecpt_repeatable_field" name="<?=$locations->unique_field('dcbs_street')?>" id="<?=$locations->unique_field('dcbs_street')?>" value="<?=$locations->value('dcbs_street')?>" size="30" style="width:80%">
                    <label for="<?=$locations->unique_field('dcbs_city')?>">City</label>
                    <input type="text" class="ecpt_repeatable_field" name="<?=$locations->unique_field('dcbs_city')?>" id="<?=$locations->unique_field('dcbs_city')?>" value="<?=$locations->value('dcbs_city')?>" size="30" style="width:80%">
                    <label for="<?=$locations->unique_field('dcbs_state')?>">State</label>
                    <input type="text" class="ecpt_repeatable_field" name="<?=$locations->unique_field('dcbs_state')?>" id="<?=$locations->unique_field('dcbs_state')?>" value="<?=$locations->value('dcbs_state')?>" size="30" style="width:80%">
                    <label for="<?=$locations->unique_field('dcbs_zipcode')?>">Zipcode</label>
                    <input type="text" class="ecpt_repeatable_field" name="<?=$locations->unique_field('dcbs_zipcode')?>" id="<?=$locations->unique_field('dcbs_zipcode')?>" value="<?=$locations->value('dcbs_zipcode')?>" size="30" style="width:80%">
                    <a href="#" class="ecpt_remove_repeatable button-secondary">x</a>
                 </div>
                <button class="ecpt_add_new_field button-secondary">Add New</button>
            </td>
        </tr>
        <?php $locations->nextLocation(); ?>
        <?php } while ($locations->hasLocations()); ?>
    </tbody>
</table>
</div>
