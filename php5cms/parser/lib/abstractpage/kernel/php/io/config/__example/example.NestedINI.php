<?php

require( '../../../../../prepend.php' );

using( 'io.config..NestedINI' );


$ini = new NestedINI();
$ini->setScriptFile( "example.txt" );
$settings = $ini->parse();

if ( !PEAR::isError( $settings ) )
{
	echo "<b>sectionname:</b> " .						$settings["section.name"]								. "<br>";
	echo "<b>page id:</b> " .							$settings["section.page.id"]							. "<br>";
	echo "<b>page header:</b> " .						$settings["section.page.header"]						. "<br>";
	echo "<b>page CSS color:</b> " .					$settings["section.page.css.color"]						. "<br>";
	echo "<b>page CSS font-family(default):</b> " .		$settings["section.page.css.font.family.default"]		. "<br>";
	echo "<b>page CSS font-family(alternative):</b> " .	$settings["section.page.css.font.family.alternative"]	. "<br>";
	echo "<b>page CSS font-size:</b> " .				$settings["section.page.css.font.size"]					. "<br>";
	echo "<b>page CSS font-weight:</b> " .				$settings["section.page.css.font.weight"];
}
else 
{
	echo "Error while parsing script.";
}

?>
