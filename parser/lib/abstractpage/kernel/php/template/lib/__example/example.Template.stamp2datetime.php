<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


$tpl= "
<tpl:stamp2datetime param='mystamp'></tpl:stamp2datetime>
";

$template = new Template( $tpl );
$template->set( 'mystamp', time() );
$page = $template->parse();

print( $page );

?>
