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


$guy1 = new SampleObjectClass( 'John', 30 );
$guy2 = new SampleObjectClass( 'Jake', 32 );
$guy3 = new SampleObjectClass( 'Bill', 27 );


$guy_collection = array( $guy1, $guy2, $guy3 );
$template = new Template( 'tpl/loop.tpl' );
$template->set( 'item', $guy_collection );
$page = $template->parse();

print( $page );

?>
