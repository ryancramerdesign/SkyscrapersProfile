<?php 

/**
 * City Template: Display all the skyscrapers in a given city
 *
 * This just lists the current page's children, which are assumed to be skyscrapers
 *
 */

$browserTitle = "Skyscrapers in " . $page->title; 
$headline = $page->title . " Skyscrapers";
$content = renderSkyscraperList(findSkyscrapers("parent=$page")); 

