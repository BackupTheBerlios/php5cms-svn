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

// Structure
//
//  John(30) [ Jake(3) ]
//  Mike(32) [ Norma(14),Allan(21) ]
//  Bill(27) [ Cindy(12),Robert(11),Cloe(2) ]

$john   = new SampleObjectClass( 'John',   30 );
$jake   = new SampleObjectClass( 'Jake',    3 );
$john->childs = array( $jake );

$mike   = new SampleObjectClass( 'Mike',   32 );
$norma  = new SampleObjectClass( 'Norma',  14 );
$allan  = new SampleObjectClass( 'Allan',  21 );
$mike->childs = array( $norma, $allan );

$bill   = new SampleObjectClass( 'Bill',   27 );
$cindy  = new SampleObjectClass( 'Cindy',  12 );
$robert = new SampleObjectClass( 'Robert', 11 );
$cloe   = new SampleObjectClass( 'Cloe',    2 );
$bill->childs = array( $cindy, $robert, $cloe );

$family = array( $john, $mike, $bill );

$template = new Template( 'tpl/loop_nested.tpl' );
$template->set( 'item', $family );
$template->set( 'john_childs', $john_childs  );
$template->set( 'mike_childs', $mike->childs );
$template->set( 'bill_childs', $bill->childs );
$page = $template->parse();

print( $page );

?>
