<?php

require( '../../../../prepend.php' );

using( 'image.ASCIIArt' );


ob_start( "ob_gzhandler" );

echo "<html><head><body><div align=\"center\">";

if ( !empty( $_POST["image"] ) ) 
{
    $pic = new ASCIIArt( $_POST["image"] );
    
    if ( $_POST["flip_h"] )
        $flip_h = true;
    else
        $flip_h = false;
    
    if ( $_POST["flip_v"] )
        $flip_v = true;
    else
        $flip_v = false;
    
    $pic->setImageCSS("
        color           : ".$_POST["color"].";
        background-color: #FFFFFF;
        font-size       : ".$_POST["font-size"]."px;
        font-family     : \"Courier New\", Courier, mono;
        line-height     : ".$_POST["line-height"]."px;
        letter-spacing  : ".$_POST["letter-spacing"]."px;
    ");
    
    $pic->renderHTMLImage( $_POST["mode"], $_POST["resolution"], $_POST["fixed_char"], $flip_h, $flip_v );

    echo $pic->getHTMLImage();
    echo "<br><p style=\"font-family: Verdana, sans-serif; font-size: 11px;\">Original Image:<br><img src=\"".$_POST["image"]."\" height=\"".$pic->image_height."\" width=\"".$pic->image_width."\"></p>";
} 
else 
{
    echo "<p>No image location specified.</p>";
}

echo "</div></body></head></html>";
ob_end_flush();

?>
