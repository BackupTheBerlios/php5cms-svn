<?php

require( '../../../../../prepend.php' );

using( 'peer.xmlrpc.BasicXMLRPCServer' );


function addTwo( $params )
{
	return $params[0] + $params[1];
}


$methodName = "";
$params = Array();
$j = 0;

$fList = Array(
	"examples.addTwo" => "addTwo"
);

BasicXMLRPCServer::receive();

?>
