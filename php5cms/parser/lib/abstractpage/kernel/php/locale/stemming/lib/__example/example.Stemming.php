<?php

require( '../../../../../../prepend.php' );

using( 'locale.stemming.lib.Stemming' );
using( 'locale.stopwords.lib.StopWords' );


$orig = "The head of the Central Intelligence Agency today told the panel investigating the September 11, 2001, terror attacks that the United States was in effect, unprotected on that day. George Tenet told the panel that intelligence officials were taking steps to counter the threat posed by Osama bin Laden's terrorist network, but he acknowledged mistakes were made.";

$words = &StopWords::factory( "en" );
$wo_stopwords = $words->removeStopWords( $orig );

$stem =& Stemming::factory( "porter" );

echo( "<pre>\n" );
echo( $stem->stem( "connection" ) . "\n\n\n" );
echo( "Original: " . $orig . "\n\n" );
echo( "Removed Stopwords: " . $wo_stopwords . "\n\n" );
echo( "Stemmed: "  . $stem->stemText( $wo_stopwords ) );
echo( "</pre>" );

?>
