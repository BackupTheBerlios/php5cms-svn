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
$CharacterInfo  = $swf->DefineCircleSolid( 17, 8000, 6000, 4000, 300, true, true, 255, 0, 0, 200, true, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefinePolygonSolid( 5, 3000, 6500, 2500, 300, true, true, 0, 0, 0, 0, true, 0, 0, 255, 200 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineTriangleSolid( 3000, 8000, 8000, 11500, 13000, 8000, 300, false, true, 0, 0, 0, 0, true, 255, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineRectangleSolid( 10000, 6500, 15500, 10000, 300, true, true, 0, 0, 0, 128, true, 0, 255, 0, 128 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
$CharacterInfo  = $swf->DefineArcClosedSolid( 17, 0, 1.5 * pi(), 11000, 3000, 2500, 300, true, true, 0, 255, 0, 200, true, 0, 0, 0, 0 );
$CharacterDepth = $swf->EasyPlaceObject( $CharacterInfo["CharacterID"] );
// end frame # 00000 

$swf->EndFrame();
$swf->EndMovie();

print $swf->GetMovie();
exit;

?>
