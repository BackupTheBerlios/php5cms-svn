<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>BitField Example</title>

</head>

<body>

<?php

require( '../../../../prepend.php' );

using( 'util.BitField' );


$bits = new BitField;

$bits->SetBit( BITFIELD_1, 1 );
$bits->SetBit( BITFIELD_2, 0 );
$bits->SetBit( BITFIELD_3, 0 );
$bits->FlipBit( BITFIELD_3 );

echo $bits->QueryBit( BITFIELD_1 ) . ",";
echo $bits->QueryBit( BITFIELD_2 ) . ",";
echo $bits->QueryBit( BITFIELD_3 );

?>

</body>
</html>
