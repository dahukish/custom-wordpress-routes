<form method="post" action="/doctors/find-a-physician" charset="utf-8" class="doctors-search">
	<fieldset>
		<label for="city">Search By Location</label>
		<input type="text" id="city" name="city" value="" placeholder="City, State OR Zipcode" />
	</fieldset>
	<fieldset>
		<label for="region">Regions Served</label>
		<select id="region" name="region" >
			<option value="">--select a region--</option>
			<?php 

			$regions = get_terms('regions');
			
			if(isset($regions)&!empty($regions)) {
				foreach ($regions as $region) {
			?>
				<option value="/<?php echo $region->slug; ?>-providers/doctors"><?php echo $region->name; ?></option>
			<?php 
				}
			}
			?>
		</select>
	</fieldset>	
	<fieldset>
		<input type="submit" value="Search" />
		<a class="all-doctors" href="/doctors/all">View all doctors</a>
	</fieldset>
</form>