<?php

header("Content-Type: text/plain");


require( '../../../../../../prepend.php' );

using( 'util.registry.lib.Registry' );
using( 'util.registry.lib.RegistryStorage' );


$key = 'foo';
$val = 'bar';

$rs  = &RegistryStorage::factory( 'memory' );
$reg = &Registry::getInstance( $rs );

$reg->put( $key, $val );
echo $reg->get( $key );

?>
