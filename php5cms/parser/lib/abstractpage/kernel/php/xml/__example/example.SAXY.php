<?php

require( '../../../../prepend.php' );

using( 'xml.SAXY' );


class SAXY_Test
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SAXY_Test()
	{
		$sp = new SAXY();
		$sp->xml_set_element_handler( array( &$this, "startElement" ), array( &$this, "endElement" ) );
		$sp->xml_set_character_data_handler( array( &$this, "charData" ) );
		$sp->parse( "<book><title id=\"67281\"><![CDATA[How to use SAXY]]></title><author>Markus Nix</author></book>" );
	}
	
	
	/**
	 * @access public
	 */
	function startElement( $parser, $name, $attributes ) 
	{
		echo( "<br /><b>Open tag:</b> " . $name  . "<br /><b>Attributes:</b> " . print_r( $attributes, true )  . "<br />" );
	}
 
 	/**
 	 * @access public
	 */
	function endElement( $parser, $name ) 
	{
		echo( "<br /><b>Close tag:</b> " . $name  . "<br />" );
 	}

	/**
	 * @access public
	 */
	function charData( $parser, $text ) 
	{
		echo( "<br /><b>Text node:</b> " . $text  . "<br />" );
	}
}

$st = new SAXY_Test(); 

?>
