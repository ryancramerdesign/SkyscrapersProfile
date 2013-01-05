<?php

/***************************************************************************************
 * SHARED SKYSCRAPER FUNCTIONS
 *
 * The following functions find and render skyscrapers are are defined here so that
 * they can be used by multiple template files. 
 *
 */

/**
 * Returns an array of valid skyscraper sort properties
 *
 * The keys for the array are the field names
 * The values for the array are the printable labels
 *
 * @return array
 *
 */
function getValidSkyscraperSorts() {
	return array(
		// field => label
		'images' => 'Images', 
		'title' => 'Title', 
		'parent' => 'City', 
		'height' => 'Height', 
		'floors' => 'Floors', 
		'year' => 'Year',
		); 
}

/**
 * Find Skyscraper pages using criteria from the given selector string.
 *
 * Serves as a front-end to $pages->find(), filling in some of the redundant
 * functionality used by multiple template files.
 *
 * @param string $selector
 * @return PageArray
 *
 */
function findSkyscrapers($selector) {

	$validSorts = getValidSkyscraperSorts();

	// check if there is a valid 'sort' var in the GET variables
	$sort = wire('sanitizer')->name(wire('input')->get->sort);

	// if no valid sort, then use 'title' as a default
	if(!$sort || !isset($validSorts[ltrim($sort, '-')])) $sort = 'title';

	// whitelist the sort value so that it is retained in pagination
	if($sort != 'title') wire('input')->whitelist('sort', $sort); 

	// expand on the provided selector to limit it to 10 sorted skyscrapers
	$selector = "template=skyscraper, limit=10, " . trim($selector, ", ");

	// check if there are any keyword searches in the selector by looking for the presence of 
	// ~= operator. if present, then omit the 'sort' param, since ProcessWire sorts by 
	// relevance when no sort specified.
	if(strpos($selector, "~=") === false) $selector .= ", sort=$sort";

	// now call upon ProcessWire to find the skyscrapers for us
	$skyscrapers = wire('pages')->find($selector); 

	// save skyscrapers for possible display in a map
	mapSkyscrapers($skyscrapers); 

	// set this runtime variable to the page so we can show the user what selector was used
	// to find the skyscrapers. the renderSkyscraperList function looks for it. 
	wire('page')->set('skyscraper_selector', $selector);

	return $skyscrapers; 
}

/**
 * Serves as a place to store and retrieve loaded skyscrapers that will be displayed in a google map.
 *
 * To add skyscrapers, pass in a PageArray of them. 
 * To retrieve skyscreapers, pass in nothing and retrieve the returned value.
 *
 * @param null|PageArray $items Skyscraper pages to store
 * @return PageArray All Skyscraper pages stored so far
 *
 */
function mapSkyscrapers($items = null) {
	static $skyscrapers = null; 
	if(is_null($skyscrapers)) $skyscrapers = new PageArray();
	if(!is_null($items) && $items instanceof PageArray) $skyscrapers->add($items);
	return $skyscrapers; 
}

/**
 * Render the <thead> portion of a Skyscraper list table
 *
 * @internal
 *
 */
function renderSkyscraperListHeader($showCity = true) {

	// get the 'sort' property, if it's been used
	$sort = wire('input')->whitelist('sort');
	if(!$sort) $sort = 'title';

	// query string that will be used to retain GET variables in table header sort links
	$queryString = '';

	// make a query string from variables that have been stuffed into $input->whitelist
	// to use with the table header sort links
	foreach(wire('input')->whitelist as $key => $value) {
		if($key == 'sort') continue; 
		$queryString .= "&amp;$key=" . urlencode($value); 
	}

	$out = "\n\t<thead>\n\t<tr>";

	// build the table header with sort links
	foreach(getValidSkyscraperSorts() as $key => $value) {

		// don't show a city header if we're already in a city
		if($value == 'City' && !$showCity) continue; 

		// check if they want to reverse the sort
		if($key == $sort) {
			$key = "-$sort";
			$value = "<strong>$value &raquo;</strong>";

		} else if("-$key" == $sort) {
			$key = ltrim($sort, '-'); 
			$value = "<strong>$value &laquo;</strong>";
		}

		$out .= "<th><a href='./?sort=$key$queryString'>$value</a></th>";
	}

	$out .= "\n\t</tr>\n\t</thead>";
	return $out; 
}

/** 
 * Get a generated table/listing of skyscrapers found from the given selector.
 *
 * @param string $selector Selector to find skyscrapers
 * @param string $showPagination Set to false to disable pagination links (default is true)
 * @return string The content to be placed in the template
 *
 */
function renderSkyscraperList(PageArray $skyscrapers, $showHeader = true) {

	if(!count($skyscrapers)) return "<h3>No skyscrapers found.</h3>";

	// we don't show the city if they are already on a city page
	$showCity = wire('page')->template != 'city'; 

	// get the markup for any pagination links 
	// note: these are provided by the MarkupPagerNav and MarkupPageArray modules, 
	// modules that are already installed by default. 
	$pagerLinks = $showHeader ? $skyscrapers->renderPager() : ''; 	

	$out = 	$pagerLinks . "\n<table class='list_skyscrapers'>";
	if($showHeader) $out .= renderSkyscraperListHeader($showCity);
	$out .= "\n\t<tbody>";

	// build the table body
	foreach($skyscrapers as $skyscraper) {
		$out .= renderSkyscraperItem($skyscraper, $showCity); 
	}
		
	$out .= "\n\t</tbody>" . 
		"\n</table>" . $pagerLinks;

	// if we stuffed a variable called skyscraper_selector into the page,
	// tell them what the selector was, for demonstration purposes
	$selector = wire('page')->skyscraper_selector; 
	if($selector) {
		$out .= "\n\n<p class='selector_note'>The selector used to find the pages shown above is:<br />" . 
			"<code>" . makePrettySelector($selector) . "</code></p>\n";
	}

	return $out; 
}


/**
 * Generate the markup for a single skyscraper item in a skyscraper list
 *
 * This is primarily used by the render() method. 
 *
 * @param Page $skyscraper The Skyscraper to render
 * @param bool $showCity Should the city name be shown?
 * @return string
 *
 */
function renderSkyscraperItem(Page $skyscraper, $showCity = true) {

	// we keep track of the number of items we've already rendered	
	// so that we can alternate placeholder images from row to row
	static $cnt = 0;
	$cnt++;

	// make a thumbnail if the first skyscraper image
	if(count($skyscraper->images)) {
		// our thumbnail is 100px wide with proportional height
		$thumb = $skyscraper->images->first()->width(100); 
		$img = "<img src='{$thumb->url}' alt='{$skyscraper->title} photo' />";

	} else {
		// skyscraper has no images, so we'll show a placeholder instead
		$class = 'placeholder';
		if($cnt % 2 == 0) $class .= " placeholder2"; // for alternate version of placeholder image
		$img = "<span class='$class'>Image Not Available</span>";
	}

	// make a truncated version of the bodycopy with max 500 characters
	if($skyscraper->body) {
		$summary = strip_tags($skyscraper->body); 
		if(strlen($summary) > 500) { 
			$summary = substr($summary, 0, 500); // display no more than 500 chars
			$summary = substr($summary, 0, strrpos($summary, ". ")+1); // and truncate to last sentence
		} 
		$summary = trim($summary); 
	} else $summary = '';

	// what we show when a field is blank
	$na = "<span class='na'>n/a</span>";

	// start a table row for the output markup
	$out = 	"\n\t<tr class='skyscraper_details'>" . 
		"\n\t\t<td rowspan='2' class='skyscraper_image'><a href='{$skyscraper->url}'>$img</a></td>" . 
		"\n\t\t<td><a href='{$skyscraper->url}'>{$skyscraper->title}</a></td>"; 

	if($showCity) {
		// display the city's abbreviation, or title if there is no abbreviation
		$out .= "\n\t\t<td>" . $skyscraper->parent->get("abbreviation|title") . "</td>";
	}

	// finish the table row of output markup
	$out .= "\n\t\t<td>" . ($skyscraper->height ? number_format($skyscraper->height) . " ft." : $na) . "</td>" . 
		"\n\t\t<td>" . ($skyscraper->floors ? $skyscraper->floors : $na) . "</td>" . 
		"\n\t\t<td>" . ($skyscraper->year ? $skyscraper->year : $na) . "</td>" . 
		"\n\t</tr>" . 
		"\n\t<tr class='skyscraper_summary'>" .
		"\n\t\t<td colspan='5'><p>$summary</p></td>" . 
		"\n\t</tr>";

	return $out; 
}


/**
 * Make the selector better for display readability
 *
 * Since we're displaying the selector to screen for demonstration purposes, this method optimizes the 
 * selector is the most readable fashion and removes any parts that aren't necessary
 *
 * This is not something you would bother with on a site that wasn't demonstrating a CMS. :) 
 *
 */
function makePrettySelector($selector) {
	if(preg_match('/(architects|parent)=(\d+)/', $selector, $matches)) {
		if($page = wire('pages')->get($matches[2])) 
			$selector = str_replace($matches[0], "$matches[1]={$page->path}", $selector); 
		if($matches[1] == 'parent') $selector = str_replace("template=skyscraper, ", "", $selector); // template not necessary here
	}
	return $selector; 
}

