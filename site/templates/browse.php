<?php

/**
 * Browse Template: used to browse cities or architects
 *
 * This template does nothing more than list the page's children, and paginate them after 100
 *
 */

$headline = "Skyscraper " . $page->title; 
$browserTitle = $headline;
$content = $page->children("limit=100")->render();

/*
 * Note that the render() method above is from the MarkupPageArray plugin module.
 * It does nothing more than iterate through the provided pages and make an unordered list, 
 * optionally with pagination. 
 *
 * While that render() method may be convenient, you could do something similar here in the
 * template like this: 
 *
 * foreach($page->children() as $child) 
 * 	echo "<li><a href='{$child->url}'>{$child->title}</a></li>"; 
 *
 */

