<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


// this is the same template that the stored in the file
// tpl/merge.tpl. This shows that the template can
// be a string, instead a file

$tpl= "<tpl:merge obj='item'>
   Hello <tpl:name>show the name here</tpl:name>,<br>
   you have <b><tpl:years>show the years here</tpl:years></b> !
       </tpl:merge>
";

$item_array = array( 'name' => John, 'years' => 27 );
   
class SampleObjectClass
{ 
    function SampleObjectClass( $name = null, $years = 0 ) 
	{
		$this->name  = $name;
		$this->years = $years;
    }
}

$item_object = new SampleObjectClass( 'John', 27 );
$template = new Template( 'tpl/merge.tpl' );
$template->set( 'item', $item_array );
$page = $template->parse();
print( "Using $item_array" );
print( $page );

$template = new Template( $tpl );
$template->set( 'item', $item_object );
$page = $template->parse();

print( "" );
print( "Using $item_object" );
print( $page );

?>
