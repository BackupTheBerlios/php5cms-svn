<?php

require( '../../../../../prepend.php' );

using( 'image.verification.Captcha' );


$config = array(
	'chars'			=> 5,
	'minsize'		=> 27,
	'maxsize'		=> 41,
	'noise'			=> true,
	'websafecolors'	=> true,
	'refreshlink'	=> true,
	'maxtry'		=> 3
);


$captcha =& new Captcha( $config );

switch ( $captcha->validateSubmit() )
{
	// was submitted and has valid keys
	case 1:
		// PUT IN ALL YOUR STUFF HERE
		echo "<p><br>Congratulation. You will get the resource now.";
		echo "<br><br><a href=\"".$_SERVER['PHP_SELF']."\">New DEMO</a></p>";
		
		break;

	// was submitted with no matching keys, but has not reached the maximum try's
	case 2:
		echo $captcha->displayForm();
		break;


	// was submitted, has bad keys and also reached the maximum try's
	case 3:
		/*
		if ( !headers_sent() && isset( $captcha->badguys_url) ) 
			header('location: '.$captcha->badguys_url);
		*/
		
		echo "<p><br>Reached the maximum try's of " . $captcha->maxtry . " without success!";
		echo "<br><br><a href=\"" . $_SERVER['PHP_SELF'] . "\">New DEMO</a></p>";
		
		break;

	// was not submitted, first entry
	default:
		echo $captcha->displayForm();
		break;
}

?>
