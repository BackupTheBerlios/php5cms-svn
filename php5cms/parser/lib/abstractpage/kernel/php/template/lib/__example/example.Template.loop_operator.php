<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


$tpl= "
 <ul>
  <tpl:loop obj='col'>
  <li><tpl:_></tpl:_>
 </tpl:loop>
</ul>
";

$my_array = array( 'Item1', 'Item2', 'Item3' );
$template = new Template( $tpl );
$template->set( 'col', $my_array );
$page = $template->parse();

print( $page );

?>
