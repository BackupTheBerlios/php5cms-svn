<?php

require( '../../../../../../prepend.php' );

using( 'security.crypt.lib.Crypting' );


function writeBoolean( $xVariable )
{
	if ( gettype( $xVariable ) == 'boolean' )
		return $xVariable? 'TRUE' : 'FALSE';
	else
		return $xVariable;
}

function out()
{
	$aArguments = func_get_args();
	$aArguments = array_Map( 'writeBoolean', $aArguments );
	$aArguments = array_Map( 'nl2br', $aArguments );
	
	echo implode( '&nbsp;|&nbsp;', $aArguments ) . "\n";
}


echo "<pre>\n";

$cRSA = Crypting::factory( 'rsa' );
$cRSA->createPrimes( '9876543210', 40 );
$cRSA->calcKeys();
out( $cRSA->p, $cRSA->q, $cRSA->n );
$v = $cRSA->testKeys();
out( $v );

out( 'EnCrypt - DeCrypt' );

$m = 'WhatsUp@ThisTime';
out( $m );

$c = $cRSA->encrypt( $m );
out( $c );

$m = $cRSA->decrypt( $c );
out( $m );

out( 'Signature - Verification' );

$m = 'WhatsUp@ThisTime';
out( $m );

$s = $cRSA->signature( $m );
out( $s );

$v = $cRSA->verification( $m, $s );
out( $v );

echo "</pre>\n";

?>
