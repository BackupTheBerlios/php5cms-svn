<?php 

require( '../../../../../prepend.php' );

using( 'format.xls.ExcelUtil' );


// To display the contents directly in a MIME compatible browser  
// add the following lines on TOP of your PHP file: 

header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D,d M YH:i:s" ) . " GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header( 'Content-type: application/x-msexcel' ); 
header( "Content-Disposition: attachment; filename=EmplList.xls" );  
header( "Content-Description: PHP/INTERBASE Generated Data" ); 

// the next lines demonstrate the generation of the Excel stream 

echo( ExcelUtil::bof() );

// write a label in A1, use for dates too 
echo( ExcelUtil::writeLabel( 0, 0, "This is a label" ) );

// write a number B1 
echo( ExcelUtil::writeNumber( 0, 1,9999 ) );

echo( ExcelUtil::eof() );

?>
