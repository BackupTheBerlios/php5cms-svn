<?php

require( '../../../../../../prepend.php' );

using( 'format.xls.workbook.XLSWorkbook' );


$workbook = new XLSWorkbook( "-" );
$workbook->headeringExcel( 'test.xls' );

// Creating the first worksheet
$worksheet1 =& $workbook->addworksheet( 'First One' );
$worksheet1->set_column( 1, 1, 40 );
$worksheet1->set_row( 1, 20 );
$worksheet1->write_string( 1, 1, "This worksheet's name is " . $worksheet1->get_name() );
$worksheet1->write_number( 3, 0, 11 );
$worksheet1->write_string( 3, 1, "by four is" );
$worksheet1->write_formula( 3, 2, "=A4 * ( 2 + 2 )" );
 
// Creating the second worksheet
$worksheet2 =& $workbook->addworksheet();
 
$formatot =& $workbook->addformat();
$formatot->set_size( 10 );
$formatot->set_align( 'center' );
$formatot->set_color( 'white' );
$formatot->set_pattern();
$formatot->set_bg_color( 'magenta' );
 
$worksheet2->set_column( 0, 0, 15 );
$worksheet2->set_column( 1, 2, 30 );
$worksheet2->set_column( 3, 3, 20 );
 
$worksheet2->write_string( 1, 0, "Id", $formatot );
$worksheet2->write_string( 1, 1, "Name", $formatot );
$worksheet2->write_string( 1, 2, "Adress", $formatot );
$worksheet2->write_string( 1, 3, "Phone Number", $formatot );
 
$worksheet2->write_string( 3, 0, "22222222-2" );
$worksheet2->write_string( 3, 1, "John Smith" );
$worksheet2->write_string( 3, 2, "Main Street 100" );
$worksheet2->write_string( 3, 3, "02-5551234" );
 
$workbook->close();

?>
