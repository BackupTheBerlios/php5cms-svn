<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


$tpl = "
<tpl:stamp2date param='mystamp'></tpl:stamp2date>
";

$template = new Template( $tpl );
$template->set( 'mystamp', time() );
$page = $template->parse();

print( $page );

?>
