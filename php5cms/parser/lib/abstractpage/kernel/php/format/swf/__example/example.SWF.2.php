<?php

require( '../../../../../prepend.php' );

using( 'format.swf.SWF' );


header( "Content-Type: application/x-shockwave-flash" );

$swf = new SWF;

// set the global parameters of your movie
// frame size is in twips (1 twip = 20 pixels)
$swf->SetSWFVersion( 5 );
$swf->SetFrameSize( 16000, 12000 ); // = 800 x 20, 600 x 20
$swf->SetFrameRate( 24.0 ); 
$swf->SetBackgroundColor( 255, 255, 255 );

// lights! camera! action!
$swf->BeginMovie();

// frame # 00000
$CharacterInfo  = $swf->DefineStraightLine( 2000, 8000, 4000, 10000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineStraightLine( 2000, 10000, 4000, 8000, 300, true, 255, 0, 0, 128 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineTriangle( 6000, 8000, 8000, 10000, 10000, 8000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineRectangle( 12000, 8000, 14000, 10000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefinePolygon( 5, 3000, 6000, 1000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineBezierQuad( 17, 6000, 5000, 8000, 9000, 10000, 5000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineBezierCubic( 17, 12000, 5000, 12000, 7000, 14000, 5000, 14000, 7000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineCurvedLine( 2000, 2000, 3000, 6000, 4000, 2000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineCircle( 17, 6500, 3000, 1000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineArc( 17, 10000, 3000, 0, 1.5 * pi(), 1000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineArcClosed( 17, 13000, 3000, 0, 1.5 * pi(), 1000, 300, false, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
// end frame # 00000 

$swf->EndFrame();
$swf->EndMovie();

print $swf->GetMovie();
exit;
 
?>
