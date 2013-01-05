<?php 

/**
 * Homepage template
 *
 */

$headline = "United States Skyscrapers";
$browserTitle = $headline; 
$content = '';

// display a random photo from this page
if($photo = $page->images->getRandom()) {
	$photo = $photo->size(640, 300); 
	$content .= "<p><img src='{$photo->url}' alt='{$photo->description}' /></p>";
}

// intro copy 
$content .= $page->body; 

// generate a list of featured skyscrapers. 
$skyscrapers = $page->skyscrapers->find("limit=3, sort=random");
$content .= "\n<h3>Featured Skyscrapers</h3>" . renderSkyscraperList($skyscrapers, false);

// provide a list of all cities where they can start browsing skyscrapers
$content .= "\n<h3>Skyscrapers by City</h3>" . $pages->get("/cities/")->children()->render();

