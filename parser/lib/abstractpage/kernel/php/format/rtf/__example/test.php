<?php

require( '../../../../../prepend.php' );

using( 'format.rtf.RTFGenerator' );


$file_rtf = "result.rtf"; // attachment file name 

header( "Content-type: application/octet-stream" );
header( "Content-Disposition: attachment; filename=$file_rtf" );

if ( $html_text != "" )
{
	$rtf = new RTFGenerator();
	
	// give the tagged text
	$rtf->parse_HTML( $html_text );
	
	// receive RTF file
	$fin = $rtf->get_rtf();
	
	echo $fin;
}

?>
