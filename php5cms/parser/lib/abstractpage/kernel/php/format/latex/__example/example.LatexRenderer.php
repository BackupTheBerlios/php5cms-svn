<?php

require( '../../../../../prepend.php' );

using( 'format.latex.LatexRenderer' );


// adjust this to match your system configuration    
$picture_cache_path       = "/var/www/latexrender/pictures";	
$picture_cache_httpd_path = "/img";


$latex = new LatexRenderer( $picture_cache_path, $picture_cache_httpd_path );

echo "<html><title>LatexRenderer Example</title><body><h3>Latex Render Demo</h3>";
echo "<form method='post'>";
echo "<textarea name='latex_formula' rows=10 cols=50>";

if ( isset( $_POST['latex_formula'] ) )
  	echo stripslashes( $_POST['latex_formula'] );
else
  	echo "\frac {43}{12} \sqrt {43}";

echo "</textarea>";
echo "<br><br><input type='submit' value='Render Formula'>";
echo "</form>";

if ( isset( $_POST['latex_formula'] ) ) 
{
  	$url = $latex->getFormulaURL( stripslashes( $_POST['latex_formula'] ) );
	
   	if ( $url != false )
    	echo "<u>Formula:</u><br><br><img src='" . $url . "'>";
  	else
    	echo "unparseable or potentially dangerous formula!";
}

echo "</body></html>";

?>
