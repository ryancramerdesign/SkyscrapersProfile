<?php 

/**
 * Architect Template: Display the skyscrapers associated with an architect
 *
 */

$browserTitle = $page->title . " Skyscrapers";
$headline = $page->title; 
$content = renderSkyscraperList(findSkyscrapers("architects=$page")); 

