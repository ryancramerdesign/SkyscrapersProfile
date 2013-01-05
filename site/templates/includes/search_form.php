
<form id='skyscraper_search' method='get' action='<?php echo $config->urls->root?>search/'>

	<h3>Skyscraper Search</h3>

	<p>
	<label for='search_keywords'>Keywords</label>
	<input type='text' name='keywords' id='search_keywords' value='<?php if($input->whitelist->keywords) echo $sanitizer->entities($input->whitelist->keywords); ?>' />
	</p>

	<p>
	<label for='search_city'>City</label>
	<select id='search_city' name='city'>
		<option value=''>Any</option><?php 
		// generate the city options, checking the whitelist to see if any are already selected
		foreach($pages->get("/cities/")->children() as $city) {
			$selected = $city->name == $input->whitelist->city ? " selected='selected' " : ''; 
			echo "<option$selected value='{$city->name}'>{$city->title}</option>"; 
		}
		?>

	</select>
	</p>

	<p>
	<label for='search_height'>Height</label>
	<select id='search_height' name='height'>
		<option value=''>Any</option><?php 
		// generate a range of heights, checking our whitelist to see if any are already selected
		foreach(array('0-250', '250-500', '500-750', '750-1000', '1000+') as $range) {
			$selected = $range == $input->whitelist->height ? " selected='selected'" : '';
			echo "<option$selected value='$range'>$range ft.</option>";
		}
		?>

	</select>
	</p>

	<p>
	<label for='search_floors'>Floors</label>
	<select id='search_floors' name='floors'>
		<option value=''>Any</option><?php
		// generate our range of floors, checking to see if any are already selected
		foreach(array('1-20', '20-40', '40-60', '60-80', '80+') as $range) {
			$selected = $range == $input->whitelist->floors ? " selected='selected'" : '';
			echo "<option$selected value='$range'>$range floors</option>";
		}
		?>

	</select>
	</p>

	<p>
	<label for='search_year'>Year</label>
	<select id='search_year' name='year'>
		<option value=''>Any</option><?php
		// generate a range of years by decade, checking to see if any are selected
		for($year = 1850; $year <= 2010; $year += 10){
			$endYear = $year+9; 
			$range = "$year-$endYear";
			$selected = $input->whitelist->year == $range ? " selected='selected'" : '';
			echo "<option$selected value='$range'>{$year}s</option>";
		}
		?>

	</select>
	</p>

	<p><input type='submit' id='search_submit' name='submit' value='Search' /></p>

</form>

