<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


$tpl = "
<tpl:showif obj='myobj'>
  this will we shown if myobj is evaluated to true
</tpl:showif>

<tpl:showif obj='notshown'>
  this won't be shown
</tpl:showif>
";

$template = new Template( $tpl );
$template->set( 'myobj', 1 );
$template->set( 'notshown', false );
$page = $template->parse();

print( $page );

?>
