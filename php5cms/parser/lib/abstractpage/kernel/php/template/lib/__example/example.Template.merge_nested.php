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
$parent->child = new SampleObjectClass( 'Jake', 3 );

$template = new Template( 'tpl/merge_nested.tpl' );
$template->set( 'item', $parent );
$page = $template->parse();

print( $page );

?>
