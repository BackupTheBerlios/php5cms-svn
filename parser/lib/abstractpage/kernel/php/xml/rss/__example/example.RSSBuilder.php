<?php

require( '../../../../../prepend.php' );

using( 'xml.rss.RSSBuilder' );


// create the object - remember, not all attibutes are supported by every rss version. just hand over an empty string if you don't need a specific attribute
$encoding    = 'ISO-8859-1';
$about       = 'http://www.docuverse.de/';
$title       = 'Docuverse.de News';
$description = 'non existing news about my homepage';
$image_link  = 'http://www.docuverse.de/small_logo.png';
$category    = 'PHP Development'; 							// (only rss 2.0)
$cache       = (int)60; 									// in minutes (only rss 2.0)
$rssfile     = new RSSBuilder( $encoding, $about, $title, $description, $image_link, $category, $cache );

// if you want you can add additional Dublic Core data to the basic rss file (if rss version supports it)
$publisher   = 'Markus'; 									// person, an organization, or a service
$creator     = 'Markus'; 									// person, an organization, or a service
$date        = date( 'Y-m-d\TH:i:sO' );
$language    = 'en';
$rights      = 'Copyright Docuverse.de';
$coverage    = 'unknown'; 									// spatial location , temporal period or jurisdiction
$contributor = 'Markus'; 									// person, an organization, or a service
$rssfile->addDCdata( $publisher, $creator, $date, $language,	$rights, $coverage, $contributor );

// if you want you can add additional Syndication data to the basic rss file (if rss version supports it)
$period      = 'daily'; 									// hourly / daily / weekly / ...
$frequency   = (int)1; 										// every X hours/days/...
$base        = date( 'Y-m-d\TH:i:sO' );
$rssfile->addSYdata( $period, $frequency, $base );

// data for a single RSS item
$about       = $link = 'http://www.docuverse.de/sometext.php?somevariable=somevalue';
$title       = 'A fake news headline';
$description = 'some abstract text about the fake news';
$subject     = 'technology'; 								// optional DC value
$date        = date( 'Y-m-d\TH:i:sO' ); 					// optional DC value
$author      = 'Docuverse.de'; 								// author of item
$comments    = 'http://www.docuverse.de/sometext.php'; 		// url to comment page rss 2.0 value
$image       = 'http://www.docuverse.de/small_logo2.png'; 	// optional mod_im value for dispaying a different pic for every item
$rssfile->addItem( $about, $title, $link, $description, $subject, $date, $author, $comments, $image );

// add as much items as you want ...
$version = '2.0'; // 0.91 / 1.0 / 2.0
$rssfile->outputRSS( $version );

// if you don't want to directly output the content, but instead work with the string (for example write it to a cache file) use
// $rssfile->getRSSOutput($version);

?>
