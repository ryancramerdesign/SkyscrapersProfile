<?php

/*
 * MAIN OUTPUT TEMPLATE FOR SKYSCRAPERS SITE EXAMPLE
 *
 * Copyright 2013 by Ryan Cramer
 * 
 * This file expects the following variables to be populated: 
 *
 * 	- $browserTitle: The text to place in the <title> tag. 
 * 	- $headline: The text to place in the <h1> tag. 
 * 	- $content: The text to place in the <div id='content'> tag, like the main body copy. 
 *
 */


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<title><?php echo $browserTitle; ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $config->urls->templates?>styles/main.css?v=3" />

	<?php 
	// if there is a CSS file having the same name as the template, then include it here
	if(is_file($config->paths->templates . "styles/{$page->template}.css")) {
		echo "\n\t<link rel='stylesheet' type='text/css' href='{$config->urls->templates}styles/{$page->template}.css' />"; 
	}
	?>

	<script type="text/javascript" src="<?php echo $config->urls->templates?>scripts/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 
	<script type="text/javascript" src="<?php echo $config->urls->templates?>scripts/main.js"></script>
	<script type="text/javascript" src="<?php echo $config->urls->templates?>scripts/RCDMap.js"></script>

	<?php 
	// if there is a javascript file having the same name as the template, then include it here
	if(is_file($config->paths->templates . "scripts/{$page->template}.js")) {
		echo "<script type='text/javascript' src='{$config->urls->templates}scripts/{$page->template}.js'></script>"; 
	}

	// check to see if we should include google analytics
	if($config->httpHost == 'processwire.com') include("./includes/google_analytics.php"); 

	?>

</head>

<body id="body_<?php echo $page->rootParent->name?>" class="template_<?php echo $page->template?>">

	<div class='container'>

		<div id='masthead'>

			<ul id='topnav'>
				<li><a id='topnav_home' href='<?php echo $config->urls->root; ?>'>Directory of United States Skyscrapers</a></li>
				<li><a id='topnav_cities' href='<?php echo $config->urls->root; ?>cities/'>Cities</a></li>
				<li><a id='topnav_architects' href='<?php echo $config->urls->root; ?>architects/'>Architects</a></li>
				<li><a id='topnav_about' href='<?php echo $config->urls->root; ?>about/'>About</a></li>
			</ul>

			<ul id='breadcrumb'><?php 
				// generate a breadcrumb list by iterating through the current page's parents
				foreach($page->parents() as $parent) {
					echo "<li><a href='{$parent->url}'>{$parent->title}</a> &gt; </li>"; 
				}
			?></ul>

			<h1><?php echo $headline ?></h1>

			<?php 
			// if the page has a parent, then make it a section label/link in the top right corner
			if($page->parent->id) echo "<p id='parent_label'><a href='{$page->parent->url}'>{$page->parent->title}</a>";
			?>

		</div>

		<div id="content">

			<?php 

			if($page->body_embed) echo $page->body_embed; 

			echo $content; 

			include("./includes/search_form.php"); 
			include("./includes/sidebar_links.php"); 

			// determine whether we are going to display a map on this page
			if(($page->map && $page->map->lat) || in_array($page->template->name, array('cities', 'home')) || count(mapSkyscrapers())) {
				include("./includes/map.php"); 
			}

			?>
			
		</div><!--/content-->
	</div><!--/container-->

	<div id='footer'>
		<div class='container'>
			<p id='copyright'>
	
				<?php if($config->httpHost == 'processwire.com'): ?>	

				<a href='/servint/'><img src='http://processwire.com/site/templates/styles/images/servint.gif' width='87' height='21' alt='ServInt' style='position: relative; bottom: -3px;' /></a>
				Hosted by <a href='/servint/'>ServInt</a> <br /><br />

				<?php endif; ?>

				&copy; <?php echo date("Y"); ?> Ryan Cramer Design, LLC 
				&bull; Data and photos from Wikipedia and Freebase
				&bull; Powered by <a href="http://processwire.com">ProcessWire Open Source CMS v<?php echo $config->version; ?></a>

				<?php if($user->isGuest()) echo "&bull; <a href='{$config->urls->admin}'>Login</a>"; ?>

			</p>
			<?php // if($config->debug) foreach($db->getQueryLog() as $n => $query) echo "<p>$n. $query</p>"; ?>

		</div>
	</div>

<?php 
// if this page is editable by the current user, then make an 'edit' link
if($page->editable()) echo "<a id='editpage' href='{$config->urls->admin}page/edit/?id={$page->id}'>Edit</a>"; 
?>

</body><!--RCD-->
</html>
