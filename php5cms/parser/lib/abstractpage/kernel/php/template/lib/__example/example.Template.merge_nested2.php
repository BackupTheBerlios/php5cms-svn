<?php

require( '../../../../../prepend.php' );

using( 'template.lib.Template' );


class SampleObjectClass
{ 
    function SampleObjectClass( $name = null, $years = 0 )
	{
		$this->name  = $name;
		$this->years = $years;
    }
}


$parent = new SampleObjectClass( 'John', 27 );
$child  = new SampleObjectClass( 'Jake',  3 );

$template = new Template( 'tpl/merge_nested2.tpl' );
$template->set( 'parent', $parent );
$template->set( 'child',  $child );
$page = $template->parse();

print( $page );

?>
