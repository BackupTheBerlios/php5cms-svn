<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


$tpl = "
<tpl:bool2string param='sayyes'></tpl:bool2string>
<tpl:bool2string param='sayno'></tpl:bool2string>
";

$template = new Template( $tpl );
$template->set( 'sayyes',  true );
$template->set( 'sayno',  false );
$page = $template->parse();

print( $page );

?>
