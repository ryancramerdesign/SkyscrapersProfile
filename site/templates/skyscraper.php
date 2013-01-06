<?php 

/**
 * The Skyscraper template displays a single skyscraper with a table of stats, photos, description and map
 *
 * Because the skyscraper template has distinct areas to render, we have split each into a separate
 * function to aid readability. See the bottom fo this file where the results are compiled into 
 * the $content variable. 
 *
 */

/**
 * Find skyscrapers that are related to the given skyscraper
 *
 * Currently, this just finds skyscrapers that mention the given one in their bodycopy
 *
 * Note that because the site-profile version of this site contains Latin text rather than 
 * real text, this function won't ever find anything except on the live processwire.com/skyscrapers/ site.
 *
 * @return PageArray
 *
 */
function findRelatedSkyscrapers($page) {
	$str = wire('sanitizer')->selectorValue($page->title); 
	return wire('pages')->find("template=skyscraper, body*=$str, id!=$page->id"); 
}

/**
 * Render all skyscraper images into a list of consistent width
 *
 */
function renderSkyscraperImages($page) {

	$out = "\n<ul class='skyscraper_images'>";

	if(count($page->images)) {
		foreach($page->images as $image) {
			$thumb = $image->width(300); 
			$description = $image->description ? "<span>{$image->description}</span>" : '';
			$out .= "\n\t<li>" . 
				"<p><a href='{$image->url}'>" . 
				"<img src='{$thumb->url}' alt='' width='$thumb->width' height='$thumb->height' /></a>" . 
				"$description</p></li>";
		}
	} else {
		$src = wire('config')->urls->templates . "styles/images/photo_placeholder.png";
		$out .= "\n\t<li><p><img src='$src' alt='' /><span>Photo Not Available</span></p></li>";
	}

	$out .= "\n</ul>";

	return $out; 
}

/**
 * Render the bodycopy area of the skyscraper
 *
 */
function renderSkyscraperBody($page) {

	// prepare the bodycopy, adding a wikipedia link for more info if available
	$out = 	"\n<div id='bodycopy'>" . 
		"\n\t<h3>About {$page->title}</h3>" . 
		"\n\t{$page->body}";

	// add a link to wikipedia for more information
	if($page->wikipedia_id) {
		$url = "http://en.wikipedia.org/wiki/index.html?curid={$page->wikipedia_id}";
		$out .= "<p><a target='_blank' href='$url'>Read More at Wikipedia</a></p>";
	}

	$out .= "\n<h3>See Also</h3>" . 
		"\n<ul class='page_list'>"; 

	// find potentially related skyscrapers by performing a text search.
	// this only works on processwire.com/skyscrapers/, since the site profile
	// has had all the real text replaced with latin placeholder text
	foreach(findRelatedSkyscrapers($page) as $item) {
		$out .= "\n\t<li><a href='{$item->url}'>{$item->title}, {$item->parent->title}</a></li>";
	}

	$out .= "\n\t<li><a href='../'>{$page->parent->title} Skyscrapers</a></li>"; 

	// make a list of architects for use in the 'see also' list, as well as the data table below
	$architects = '';
	if(count($page->architects)) {
		foreach($page->architects as $architect) {
			$architects .= "\n\t<li><a href='{$architect->url}'>{$architect->title}</a></li>";
			$out .= "\n\t<li><a href='{$architect->url}'>Skyscrapers by {$architect->title}</a></li>";
		}
		$architects = "\n<ul>$architects\n</ul>";
	}

	$out .= "\n</ul>" . 
		"\n</div>";

	return $out; 
}

/**
 * Make a basic data table with linked skyscraper stats
 *
 */
function renderSkyscraperData($page) {

	$searchUrl = wire('config')->urls->root . "search/";
	$na = "<span class='na'>n/a</span>";
	$architects = '';

	foreach($page->architects as $a) {
		$architects .= "\n\t<li><a href='{$a->url}'>{$a->title}</a></li>";
	}

	$out =	"\n<table class='skyscraper_data'>" . 
		"\n\t<tbody>" . 
		"\n\t<tr><th>Height</th><td>" . ($page->height ? "<a href='$searchUrl?height={$page->height}'>{$page->height} feet</a>" : $na) . "</td></tr>" . 
		"\n\t<tr><th>Floors</th><td>" . ($page->floors ? "<a href='$searchUrl?floors={$page->floors}'>{$page->floors}</a>" : $na) . "</td></tr>" . 
		"\n\t<tr><th>Year</th><td>" . ($page->year ? "<a href='$searchUrl?year={$page->year}'>{$page->year}</a>" : $na) . "</td></tr>" . 
		"\n\t<tr><th>Architects</th><td>" . ($architects ? "\n<ul>$architects</ul>" : $na) . "</td></tr>" . 
		"\n\t</tbody>" . 
		"\n</table>";

	return $out; 
}

/*************************************************************************************************
 * Compile the data into the final output 
 *
 */

$browserTitle = $page->title . ", " . $page->parent->title . " Skyscraper";
$content = renderSkyscraperImages($page) . renderSkyscraperData($page) . renderSkyscraperBody($page); 

