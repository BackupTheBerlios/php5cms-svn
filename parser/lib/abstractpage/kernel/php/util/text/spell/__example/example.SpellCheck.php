<?php 

require( '../../../../../../prepend.php' );

using( 'util.text.spell.SpellCheck' );


$spell_chk = new SpellCheck("en", "zend-john"); 
$spell_chk->add('ttest'); 

$mystr = "This is a ttest of a mispellled word"; 
$words = split( "[^[:alpha:]']+", $mystr ); 

foreach ( $words as $val )
{ 
	if ( $spell_chk->check( $val ) )
	{ 
		echo "The word '$val' is spelled correctly<BR>"; 
	}
	else
	{ 
		echo "The word '$val' was not spelled correctly<BR>"; 
		echo "Possible correct spellings are: "; 

		foreach( $spell_chk->suggest($val) as $suggestion ) 
			echo ' ' . $suggestion; 

		echo "<BR>"; 
	} 
} 

$spell_chk->close(); 

?>
