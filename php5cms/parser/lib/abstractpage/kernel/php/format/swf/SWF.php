<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: ??                                                           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package format_swf
 */
 
class SWF extends PEAR
{
	/**
	 * @access public
	 */
	var $Shape = array();
	
	/**
	 * @access public
	 */
	var $SWFVersion = 0;
	
	/**
	 * @access public
	 */
	var $SWFVersionLowerLimit = 1;
	
	/**
	 * @access public
	 */
	var $SWFVersionUpperLimit = 6;
	
	/**
	 * @access public
	 */
	var $MovieData = "";
	
	/**
	 * @access public
	 */
	var $FrameCounter = 0;
	
	/**
	 * @access public
	 */
	var $CharacterDepth = 0;
	
	/**
	 * @access public
	 */
	var $Bitmaps = array();
	
	/**
	 * @access public
	 */
	var $FrameRate = 12.5;
	
	/**
	 * @access public
	 */
	var $FrameSize = array(
		"Xmin" => 0, 
		"Xmax" => 11000, 
		"Ymin" => 0, 
		"Ymax" => 8000
	);

	/**
	 * @access public
	 */
	var $BackgroundColor = array(
		"R" => 0, 
		"G" => 0, 
		"B" => 0
	);

	/**
	 * The theoretical limit is 65535, but older versions of
	 * Flash Player cannot display layers above 16000
	 * @access public
	 */
	var $LayerLimit = 16000;

	/**
	 * @access public
	 */
	var $FrameNumberLimit = 16000;
	
	/**
	 * @access public
	 */
	var $CharacterIDLimit = 65535;
	
	/**
	 * @access public
	 */
	var $CharacterID = 0;


	/**
	 * Sets the SWF file version number to $version and
	 * returns that version.
	 *
	 * NOTE: call this function right before the call
	 *       to EndMovie() to override changes made by
	 *       calls to AutoSetSWFVersion().
	 */
	function SetSWFVersion( $version )
	{
		if ( ( $version < $this->SWFVersionLowerLimit ) || ( $version > $this->SWFVersionUpperLimit ) )
			return PEAR::raiseError( "SetSWFVersion argument (version) out of range." );
		else
			$this->SWFVersion = (int)$version;
		
		return $this->SWFVersion;
	} 
	
	/**
	 * Returns the SWF file version number set by
	 * AutoSetSWFVersion() and SetSWFVersion().  
	 * Does not change the current SWF file version number.
	 */
	function GetSWFVersion()
	{
		return $this->SWFVersion;
	} 
	
	/**
	 * array SetFrameSize(integer Xmax, integer Ymax)
	 *
	 * Sets the Flash movie frame size and returns the
	 * array holding the new frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSize( $Xmax, $Ymax )
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = (int)$Xmax;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = (int)$Ymax;
		
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABButton1()
	 *
	 * Sets the Flash movie frame size to IAB Button 1
	 * and returns the array holding the new frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABButton1()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 120 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 90 * 20;
	
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABButton2()
	 *
	 * Sets the Flash movie frame size to IAB Button 2
	 * and returns the array holding the new frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABButton2()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 120 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 60 * 20;
		
		return $this->FrameSize;
	} 

	/**
	 * array SetFrameSizeIABFullBanner()
	 *
	 * Sets the Flash movie frame size to IAB Full
	 * Banner and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABFullBanner()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 468 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 60 * 20;
	
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABHalfBanner()
	 *
	 * Sets the Flash movie frame size to IAB Half
	 * Banner and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABHalfBanner()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 234 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 60 * 20;
		
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABLargeRectangle()
	 *
	 * Sets the Flash movie frame size to IAB Large
	 * Rectangle and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABLargeRectangle()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 336 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 280 * 20;
		
		return $this->FrameSize;
	}
	
	/** 
	 * array SetFrameSizeIABMediumRectangle()
	 *
	 * Sets the Flash movie frame size to IAB Medium 
	 * Rectangle and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABMediumRectangle()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 300 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 250 * 20;
		
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABMicroBar()
	 *
	 * Sets the Flash movie frame size to IAB Micro 
	 * Bar and returns the array holding the new frame
	 * size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABMicroBar()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 88 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 31 * 20;
		
		return $this->FrameSize;
	}
	
	/**
	 * array SetFrameSizeIABRectangle()
	 *
	 * Sets the Flash movie frame size to IAB Rectangle 
	 * and returns the array holding the new frame
	 * size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABRectangle()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 180 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 150 * 20;
		
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABSkyscraper()
	 *
	 * Sets the Flash movie frame size to IAB
	 * Skyscraper and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABSkyscraper()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 120 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 600 * 20;
		
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABSquareButton()
	 *
	 * Sets the Flash movie frame size to IAB Square
	 * Button and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABSquareButton()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 125 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 125 * 20;
	
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABSquarePopUp()
	 *
	 * Sets the Flash movie frame size to IAB Square
	 * Pop-up and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABSquarePopUp()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 250 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 250 * 20;
		
		return $this->FrameSize;
	} 
	
	/** 
	 * array SetFrameSizeIABVerticalBanner()
	 *
	 * Sets the Flash movie frame size to IAB Vertical
	 * Banner and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABVerticalBanner()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 120 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 240 * 20;
		
		return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABVerticalRectangle()
	 *
	 * Sets the Flash movie frame size to IAB Vertical
	 * Rectangle and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABVerticalRectangle()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 240 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 400 * 20;
				return $this->FrameSize;
	} 
	
	/**
	 * array SetFrameSizeIABWideSkyscraper()
	 *
	 * Sets the Flash movie frame size to IAB Wide
	 * Skyscraper and returns the array holding the new
	 * frame size. 
	 *
	 * NOTE: must be called before the call to
	 *       EndMovie().
	 */
	function SetFrameSizeIABWideSkyscraper()
	{
		$this->FrameSize["Xmin"] = 0;
		$this->FrameSize["Xmax"] = 160 * 20;
		$this->FrameSize["Ymin"] = 0;
		$this->FrameSize["Ymax"] = 600 * 20;
		
		return $this->FrameSize;
	} 
	
	/**
	 * array GetFrameSize()
	 *
	 * Returns the array that stores the Flash movie
	 * frame size, without changing it.
	 */
	function GetFrameSize()
	{
		return $this->FrameSize;
	} 
	
	/**
	 * float SetFrameRate(float framerate) 
	 *
	 * Sets the Flash movie frame rate and returns it.
	 *
	 * NOTE: must be called before the call to  
	 *       EndMovie(), even better before 
	 *       BeginMovie().
	 */
	function SetFrameRate( $framerate )
	{
		$lower_limit = 0.01;
		$upper_limit = 120;
		
		if ( ( $framerate < $lower_limit ) || ( $framerate > $upper_limit ) ) 
			return PEAR::raiseError( "SetFrameRate argument (framerate) out of range." );
		else
			$this->FrameRate = $framerate;
	
		return $this->FrameRate;
	} 
	
	/**
	 * float GetFrameRate()
	 *
	 * Returns the Flash movie frame rate without 
	 * changing it.
	 */
	function GetFrameRate()
	{
		return $this->FrameRate;
	} 
	
	/**
	 * array SetBackgroundColor(integer R, integer G, integer B) 
	 *
	 * Sets the background color of the movie and 
	 * returns the array which stores it.
	 *
	 * NOTE: must be called before the call to  
	 *       EndMovie().
	 */
	function SetBackgroundColor( $R, $G, $B )
	{
		$lower_limit = 0;
		$upper_limit = 255;
		
		if ( ( $R < $lower_limit ) || ( $R > $upper_limit ) || ( $G < $lower_limit ) || ( $G > $upper_limit ) || ( $B < $lower_limit ) || ( $B > $upper_limit ) ) 
		{
			return PEAR::raiseError( "SetBackgroundColor arguments out of range." );
		} 
		else 
		{
			$this->BackgroundColor["R"] = $R;
			$this->BackgroundColor["G"] = $G;
			$this->BackgroundColor["B"] = $B;
		}
		
		return $this->BackgroundColor;
	} 
	
	/**
	 * array GetBackgroundColor()
	 *
	 * Returns the Flash movie background color without
	 * changing it.
	 */
	function GetBackgroundColor()
	{
		return $this->BackgroundColor;
	} 
	
	/**
	 * integer NextLayer()
	 *
	 * Returns the number of the next available layer.
	 */
	function NextLayer()
	{
		$this->CharacterDepth++;
		
		if ( $this->CharacterDepth > $this->LayerLimit )
			return PEAR::raiseError( "NextLayer: layer limit exceeded." );
		
		return $this->CharacterDepth;
	}
	
	/**
	 * integer PlaceObject(integer CharacterID, integer CharacterDepth, string MATRIX,string CXFORM)
	 * 
	 * Adds the given character on the specified layer
	 * and returns the index number of that layer.
	 */
	function PlaceObject( $CharacterID, $CharacterDepth, $MATRIX, $CXFORM )
	{		
		// check layer depth
		if ( $CharacterDepth > $this->LayerLimit )
			return PEAR::raiseError( "PlaceObject: layer limit exceeded." );
		
		$this->packPlaceObjectTag( $CharacterID, $CharacterDepth, $MATRIX, $CXFORM );
		return $CharacterDepth;
	}
	
	/**
	 * integer EasyPlaceObject(integer CharacterID)
	 *
	 * Automatically adds the given character on the
	 * specified layer and returns the index number of
	 * that layer.
	 */
	function EasyPlaceObject( $CharacterID )
	{
		$CharacterDepth = $this->NextLayer();
		
		if ( PEAR::isError( $CharacterDepth ) )
			return $CharacterDepth;
			
		// check layer depth
		if ( $CharacterDepth > $this->LayerLimit )
			return PEAR::raiseError( "EasyPlaceObject: layer limit exceeded." );
		
		$this->packPlaceObjectTag( $CharacterID, $CharacterDepth, $this->DefineMATRIX( false, null, null, false, null, null, 0, 0 ), "" );
		return $CharacterDepth;
	}
	
	/**
	 * integer AdvancedPlaceObject(integer CharacterID,integer CharacterDepth, string MATRIX,string CXFORM, integer Ratio, string Name,string ClipActions)
	 *
	 * Adds the given object to the Display List or
	 * modifies an object on the given layer.
	 */
	function AdvancedPlaceObject( $CharacterID, $CharacterDepth, $MATRIX, $CXFORM, $Ratio, $Name, $ClipActions )
	{		
		// check layer depth
		if ( $CharacterDepth > $this->LayerLimit )
			return PEAR::raiseError( "AdvancedPlaceObject: layer limit exceeded." );
		
		$this->packPlaceObject2Tag( false, true, $CharacterID, $CharacterDepth, $MATRIX, $CXFORM, $Ratio, $Name, $ClipActions);
		return $CharacterDepth;
	}
	
	/**
	 * integer AdvancedModifyObject(integer CharacterID,integer CharacterDepth, string MATRIX,string CXFORM, integer Ratio, string Name,string ClipActions)
	 *
	 * Adds the given object to the Display List or
	 * modifies an object on the given layer.
	 */
	function AdvancedModifyObject( $CharacterDepth, $MATRIX, $CXFORM, $Ratio, $Name, $ClipActions )
	{		
		// check layer depth
		if ( $CharacterDepth > $this->LayerLimit )
			return PEAR::raiseError( "AdvancedModifyObject: layer limit exceeded.");
		
		$this->packPlaceObject2Tag( true, false, null, $CharacterDepth, $MATRIX, $CXFORM, $Ratio, $Name, $ClipActions );
		return $CharacterDepth;
	}
	
	/**
	 * integer AdvancedReplaceObject(integer CharacterID,integer CharacterDepth, string MATRIX,string CXFORM, integer Ratio, string Name,string ClipActions)
	 *
	 * Adds the given object to the Display List or
	 * modifies an object on the given layer.
	 */
	function AdvancedReplaceObject( $CharacterID, $CharacterDepth, $MATRIX, $CXFORM, $Ratio, $Name, $ClipActions )
	{		
		// check layer depth
		if ( $CharacterDepth > $this->LayerLimit )
			return PEAR::raiseError( "AdvancedReplaceObject: layer limit exceeded." );
		
		$this->packPlaceObject2Tag( true, true, $CharacterID, $CharacterDepth, $MATRIX, $CXFORM, $Ratio, $Name, $ClipActions );
		return $CharacterDepth;
	}
	
	/**
	 * integer RemoveObjectFromLayer(integer CharacterID,integer CharacterDepth)
	 *
	 * Removes the given character from the specified layer.
	 */
	function RemoveObjectFromLayer( $CharacterID, $CharacterDepth )
	{
		$this->packRemoveObjectTag( $CharacterID, $CharacterDepth );
	}
	
	/**
	 * integer RemoveFromLayer(integer CharacterDepth)
	 *
	 * Removes a character from the specified layer.
	 */
	function RemoveFromLayer( $CharacterDepth )
	{
		$this->packRemoveObject2Tag( $CharacterDepth );
	}
	
	/**
	 * integer EndFrame()
	 *
	 * Marks the end of a frame in a Flash movie and 
	 * returns that frame's number.
	 */
	function EndFrame()
	{
		$this->packShowFrameTag();
		$this->FrameCounter += 1;
		
		// The real limit is 65535 frames, but older 
		// versions of Flash Player only display the first
		// 16000 frames.
		if ( $this->FrameCounter > $this->FrameNumberLimit )
			return PEAR::raiseError( "EndFrame: Too many frames!" );
		
		return $this->FrameCounter;
	} 
	
	/**
	 * null BeginMovie()
	 * 
	 * Begins a Flash movie with the 
	 * SetBackgroundColor tag.
	 *
	 * NOTE: This must be done at the very beginning
	 *       of a movie. 
	 */
	function BeginMovie()
	{
		$this->packSetBackgroundColorTag( (int)$this->BackgroundColor["R"], (int)$this->BackgroundColor["G"], (int)$this->BackgroundColor["B"] );
	}
	
	/**
	 * null EndMovie()
	 *
	 * Marks the end of a Flash movie.
	 *
	 * NOTE: No other calls should be made after this
	 *       point, except for GetMovie().
	 */
	function EndMovie()
	{
		$this->packEndTag();
		return $this->packMacromediaFlashSWFHeader();
	}
	
	/**
	 * string GetMovie()
	 *
	 * Returns a complete Flash movie bytestream, ready
	 * to be sent to the requesting client.
	 *
	 * NOTE: Must be called after EndMovie().
	 */
	function GetMovie()
	{
		return $this->MovieData;
	} 
	
	/**
	 * string DefineMATRIX(boolean HasScale,
	 *           float ScaleX, float ScaleY, boolean HasRotate,
	 *           float RotateSkew0, float RotateSkew1,
	 *           integer TranslateX, integer TranslateY)
	 *
	 * Wrapper for packMATRIX().
	 */
	function DefineMATRIX( $HasScale, $ScaleX, $ScaleY, $HasRotate, $RotateSkew0, $RotateSkew1, $TranslateX, $TranslateY )  
	{
		$matrix = $this->packMATRIX( $HasScale, $ScaleX, $ScaleY, $HasRotate, $RotateSkew0, $RotateSkew1, $TranslateX, -$TranslateY ); 
			
		// Return the SWF matrix atom string.
		return $matrix;
	}
	
	/**
	 * array DefineGradient(integer Ratio, boolean AlphaFlag, integer R, integer G, integer B, integer A)
	 *
	 * Defines a gradient entry used to build a 
	 * gradient definition.
	 */
	function DefineGradient( $Ratio, $AlphaFlag, $R, $G, $B, $A )
	{		
		if ( $AlphaFlag ) 
		{
			$gradient = array(
				"Ratio" => $Ratio, 
				"R"     => $R, 
				"G"     => $G, 
				"B"     => $B, 
				"A"     => $A
			);
		} 
		else 
		{
			$gradient = array(
				"Ratio" => $Ratio, 
				"R"     => $R, 
				"G"     => $G, 
				"B"     => $B
			);
		}
		
		// return the SWF gradient point atom string.
		return $gradient;
	}
		
	
	// Basic shapes without fills
	
	/**
	 * integer DefineStraightLine(integer X1,integer Y1, integer X2, integer Y2,integer Width, boolean AlphaFlag, integer R,integer G, integer B, integer A)
	 *
	 * Defines a straight line.
	 */
	function DefineStraightLine( $X1, $Y1, $X2, $Y2, $Width, $AlphaFlag, $R, $G, $B, $A )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check for Character ID limit overflow.
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineStraightLine: character limit exceeded." );
		
		// define fill styles (none are used in this case)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one, in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		
		// select line style and move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );		
		
		// compute displacements along the x and y axes
		$DeltaX = $X2 - $X1;
		$DeltaY = $Y2 - $Y1;
		
		// test if the line is general, horizontal or 
		// vertical and use the appropriate straight edge 
		// record
		if ( ( $DeltaX == 0 ) && ( $DeltaY != 0 ) ) 
			$EdgeRecords = $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX, $DeltaY );
		else if ( ( $DeltaX != 0 ) && ( $DeltaY == 0 ) ) 
			$EdgeRecords = $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX, $DeltaY );
		else 
			$EdgeRecords = $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2 ) - $margin;
		$X_max  = max( $X1, $X2 ) + $margin;
		$Y_min  = min( $Y1, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $Y2 ) + $margin;
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag ) 
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle);
		else 
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
	
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineTriangle(integer X1, integer Y1, 
	 *             integer X2, integer Y2, integer X3, 
	 *             integer Y3, integer Width, boolean AlphaFlag, 
	 *             integer R, integer G, integer B, integer A)
	 *
	 * Defines a triangle without fill.
	 */
	function DefineTriangle( $X1, $Y1, $X2, $Y2, $X3, $Y3, $Width, $AlphaFlag, $R, $G, $B, $A )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineTriangle: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		$Y3 = $this->FrameSize["Ymax"] - $Y3;
		
		// select line style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );		
		
		// compute deltas for triangle edges.
		$DeltaX1 = $X2 - $X1;
		$DeltaY1 = $Y2 - $Y1;
		$DeltaX2 = $X3 - $X2;
		$DeltaY2 = $Y3 - $Y2;
		$DeltaX3 = $X1 - $X3;
		$DeltaY3 = $Y1 - $Y3;
		
		// test if one or two of the triangle's edges are 
		// general, horizontal or vertical and use the 
		// appropriate straight edge record
		if ( ( $DeltaX1 == 0 ) && ( $DeltaY1 != 0 ) ) 
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX1, $DeltaY1 );
		else if ( ( $DeltaX1 != 0 ) && ( $DeltaY1 == 0 ) ) 
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX1, $DeltaY1 );
		else 
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX1, $DeltaY1 );
		
		if ( ( $DeltaX2 == 0 ) && ( $DeltaY2 != 0 ) ) 
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX2, $DeltaY2 );
		else if ( ( $DeltaX2 != 0 ) && ( $DeltaY2 == 0 ) ) 
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX2, $DeltaY2 );
		else 
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX2, $DeltaY2 );
		
		if ( ( $DeltaX3 == 0 ) && ( $DeltaY3 != 0 ) ) 
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX3, $DeltaY3 );
		else if ( ( $DeltaX3 != 0 ) && ( $DeltaY3 == 0 ) ) 
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX3, $DeltaY3 );
		else 
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX3, $DeltaY3 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds.
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2, $X3 ) - $margin;
		$X_max  = max( $X1, $X2, $X3 ) + $margin;
		$Y_min  = min( $Y1, $Y2, $Y3 ) - $margin;
		$Y_max  = max( $Y1, $Y2, $Y3 ) + $margin;
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
		
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineRectangle(integer X1, integer Y1, 
	 *          integer X2, integer Y2, integer Width,
	 *          boolean AlphaFlag, integer R, integer G,
	 *          integer B, integer A)
	 *
	 * Defines a rectangle without fill.
	 */
	function DefineRectangle( $X1, $Y1, $X2, $Y2, $Width, $AlphaFlag, $R, $G, $B, $A )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineRectangle: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		
		// select line style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );		
		
		// compute deltas
		$DeltaX = $X2 - $X1;
		$DeltaY = $Y2 - $Y1;
		
		// add vertical and horizontal straight edges
		$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, 0, $DeltaY  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX, 0  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, 0, -$DeltaY );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, -$DeltaX, 0 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2 ) - $margin;
		$X_max  = max( $X1, $X2 ) + $margin;
		$Y_min  = min( $Y1, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $Y2 ) + $margin;
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
		
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/** 
	 * integer DefinePolygon(integer Segments,
	 *         integer X1, integer Y1, integer Radius,
	 *         integer Width, boolean AlphaFlag, integer R,
	 *         integer G, integer B, integer A)
	 *
	 * Defines a regular polygon without fill.
	 */
	function DefinePolygon( $Segments, $X1, $Y1, $Radius, $Width, $AlphaFlag, $R, $G, $B, $A )
	{		
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefinePolygon: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1;
		$Y2 = $Y1 - $Radius;
		
		// select line style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X2, $Y2, 0, 1, 0, 0, 1, "", "", 0, 0 );		
		
		// compute step angle
		$step = 2 * pi() / $Segments;
		for ( $n = 0; $n < $Segments; $n++ ) 
		{	
			// compute x and y deltas
			$angle  = -( pi() / 2 ) + $step * $n;
			$X3     = $X1 + $Radius * cos( $angle );
			$Y3     = $Y1 + $Radius * sin( $angle );
			$X4     = $X1 + $Radius * cos( $angle + $step );
			$Y4     = $Y1 + $Radius * sin( $angle + $step );
			$DeltaX = round( $X4 - $X3 );
			$DeltaY = round( $Y4 - $Y3 );
			
			// add a straight edge
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineBezierQuad(integer Accuracy,
	 *				integer X1, integer Y1, integer ControlX,
	 *       		integer ControlY, integer X2, integer Y2,
	 *       		integer Width, boolean AlphaFlag, integer R,
	 *            	integer G, integer B, integer A)
	 *
	 * Defines a 2nd degree Bezier curve using straight
	 * line segments.
	 */
	function DefineBezierQuad( $Accuracy, $X1, $Y1, $ControlX, $ControlY, $X2, $Y2, $Width, $AlphaFlag, $R, $G, $B, $A )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineBezierQuad: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$ControlY = $this->FrameSize["Ymax"] - $ControlY;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		
		// select line style
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );		
		
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{ 	
			// compute values of u
			$us = $n / $Accuracy;
			$uf = ( $n + 1 ) / $Accuracy;
			
			// compute control and anchor deltas
			$QX1s = $X1 * pow( ( 1 - $us ), 2 );
			$QY1s = $Y1 * pow( ( 1 - $us ), 2 );
			$QXCs = $ControlX * 2 * $us * ( 1 - $us );
			$QYCs = $ControlY * 2 * $us * ( 1 - $us );
			$QX2s = $X2 * pow( $us, 2 );
			$QY2s = $Y2 * pow( $us, 2 );
			$QXs  = $QX1s + $QXCs + $QX2s;
			$QYs  = $QY1s + $QYCs + $QY2s;
			$QX1f = $X1 * pow( ( 1 - $uf ), 2 );
			$QY1f = $Y1 * pow( ( 1 - $uf ), 2 );
			$QXCf = $ControlX * 2 * $uf * ( 1 - $uf );
			$QYCf = $ControlY * 2 * $uf * ( 1 - $uf );
			$QX2f = $X2 * pow( $uf, 2 );
			$QY2f = $Y2 * pow( $uf, 2 );
			$QXf  = $QX1f + $QXCf + $QX2f;
			$QYf  = $QY1f + $QYCf + $QY2f;
			
			$AnchorDeltaX = round( $QXf - $QXs );
			$AnchorDeltaY = round( $QYf - $QYs );
			
			// add a straight edge
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// mark the end of the shape
		$EndShape .= $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $ControlX, $X2 ) - $margin;
		$X_max  = max( $X1, $ControlX, $X2 ) + $margin;
		$Y_min  = min( $Y1, $ControlY, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $ControlY, $Y2 ) + $margin;
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag($this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag($this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
		
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineBezierCubic(integer Accuracy, 
	 *      		integer X1, integer Y1, integer ControlX1,
	 *           	integer ControlY1, integer ControlX2,
	 *      		integer ControlY2, integer X2, integer Y2,
	 *    			integer Width, boolean AlphaFlag, integer R,
	 *            	integer G, integer B, integer A)
	 *
	 * Defines a 3rd degree Bezier curve using 
	 * straight line segments.
	 */
	function DefineBezierCubic( $Accuracy, $X1, $Y1, $ControlX1, $ControlY1, $ControlX2, $ControlY2, $X2, $Y2, $Width, $AlphaFlag, $R, $G, $B, $A )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineBezierCubic: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1        = $this->FrameSize["Ymax"] - $Y1;
		$ControlY1 = $this->FrameSize["Ymax"] - $ControlY1;
		$ControlY2 = $this->FrameSize["Ymax"] - $ControlY2;
		$Y2        = $this->FrameSize["Ymax"] - $Y2;
		
		// select line style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );
		
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{ 	
			// compute values of u
			$us = $n / $Accuracy;
			$uf = ( $n + 1 ) / $Accuracy;
			
			// compute control and anchor deltas
			$QX1s   = $X1 * pow( ( 1 - $us ), 3 );
			$QY1s   = $Y1 * pow( ( 1 - $us ), 3 );
			$QXC1s  = $ControlX1 * 3 * $us * pow( ( 1 - $us ), 2 );
			$QYC1s  = $ControlY1 * 3 * $us * pow( ( 1 - $us ), 2 );
			$QXC2s  = $ControlX2 * 3 * pow( $us, 2 ) * ( 1 - $us );
			$QYC2s  = $ControlY2 * 3 * pow( $us, 2 ) * ( 1 - $us );
			$QX2s   = $X2 * pow( $us, 3 );
			$QY2s   = $Y2 * pow( $us, 3 );
			$QXs    = $QX1s + $QXC1s + $QXC2s + $QX2s;
			$QYs    = $QY1s + $QYC1s + $QYC2s + $QY2s;
			$QX1f   = $X1 * pow( ( 1 - $uf ), 3 );
			$QY1f   = $Y1 * pow( ( 1 - $uf ), 3 );
			$QXC1f  = $ControlX1 * 3 * $uf * pow( ( 1 - $uf ), 2 );
			$QYC1f  = $ControlY1 * 3 * $uf * pow( ( 1 - $uf ), 2 );
			$QXC2f  = $ControlX2 * 3 * pow( $uf, 2 ) * ( 1 - $uf );
			$QYC2f  = $ControlY2 * 3 * pow( $uf, 2 ) * ( 1 - $uf );
			$QX2f   = $X2 * pow( $uf, 3 );
			$QY2f   = $Y2 * pow( $uf, 3 );
			$QXf    = $QX1f + $QXC1f + $QXC2f + $QX2f;
			$QYf    = $QY1f + $QYC1f + $QYC2f + $QY2f;
			$DeltaX = round( $QXf - $QXs );
			$DeltaY = round( $QYf - $QYs );
			
			// add a straight edge
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		}
		
		// mark the end of the shape
		$EndShape .= $this->packENDSHAPERECORD();
		
		// compute shape bounds.
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $ControlX1, $ControlX2, $X2 ) - $margin;
		$X_max  = max( $X1, $ControlX1, $ControlX2, $X2 ) + $margin;
		$Y_min  = min( $Y1, $ControlY1, $ControlY2, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $ControlY1, $ControlY2, $Y2 ) + $margin;
	 	
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag ) 
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else 
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}

	/**
	 * integer DefineCurvedLine(integer X1, 
	 *				integer Y1, integer ControlX,
	 *				integer ControlY, integer X2, integer Y2,
	 * 				integer Width, boolean AlphaFlag, integer R,
	 *				integer G, integer B, integer A)
	 *
	 * Defines a 2nd degree Bezier curve using 
	 * the CURVEDEDGERECORD structure.
	 */
	function DefineCurvedLine( $X1, $Y1, $ControlX, $ControlY, $X2, $Y2, $Width, $AlphaFlag, $R, $G, $B, $A )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineCurvedLine: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$ControlY = $this->FrameSize["Ymax"] - $ControlY;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		
		// select line style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );
		
		// compute control and anchor deltas
		$ControlDeltaX = $ControlX - $X1;
		$ControlDeltaY = $ControlY - $Y1;
		$AnchorDeltaX  = $X2 - $ControlX;
		$AnchorDeltaY  = $Y2 - $ControlY;
		
		// define a curved edge
		$EdgeRecords = $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		
		// mark the end of the shape
		$EndShape .= $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $ControlX, $X2 ) - $margin;
		$X_max  = max( $X1, $ControlX, $X2 ) + $margin;
		$Y_min  = min( $Y1, $ControlY, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $ControlY, $Y2 ) + $margin;
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
	
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/** 
	 * integer DefineCircle(integer Accuracy, 
	 *				integer X1, integer Y1, integer Radius,
	 *				integer Accuracy, integer Width,
	 *				boolean AlphaFlag, integer R, integer G,
	 *				integer B, integer A)
	 *
	 * Defines a circle without fill.
	 */
	function DefineCircle( $Accuracy, $X1, $Y1, $Radius, $Width, $AlphaFlag, $R, $G, $B, $A )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineCircle: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1 + $Radius;
		$Y2 = $Y1;
		
		// select line style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X2, $Y2, 0, 1, 0, 0, 1, "", "", 0, 0 );
		
		// compute angles and radii
		$Alpha = 2 * pi() / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineArc(integer Accuracy,
	 *				integer X1, integer Y1, float Angle1,
	 * 				float Angle2, integer Radius, integer Width,
	 *				boolean AlphaFlag, integer R, integer G,
	 *				integer B, integer A)
	 *
	 * Defines an arc.
	 */
	function DefineArc( $Accuracy, $X1, $Y1, $Angle1, $Angle2, $Radius, $Width, $AlphaFlag, $R, $G, $B, $A )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineArc: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X1 = $X1 + round( $Radius * cos( $Angle1 ) );
		$Y1 = $Y1 + round( $Radius * sin( $Angle1 ) );
		
		// select line style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );		
		
		// compute angles and radii
		$Alpha = ( $Angle2 - $Angle1 ) / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos($Beta); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineArcClosed(integer Accuracy,
	 *           	integer X1, integer Y1, float Angle1,
	 *    			float Angle2, integer Radius, integer Width,
	 *        		boolean AlphaFlag, integer R, integer G,
	 *				integer B, integer A)
	 *
	 * Defines a closed arc.
	 */
	function DefineArcClosed( $Accuracy, $X1, $Y1, $Angle1, $Angle2, $Radius, $Width, $AlphaFlag, $R, $G, $B, $A )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineArcClosed: character limit exceeded." );
		
		// define fill styles (none are used here)
		$FillStyle = "";
		$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
		
		// define line styles (just one in this case)
		$LineStyle = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
		$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		
		// select line style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, 1, 0, 0, 1, $X1, $Y1, 0, 1, 0, 0, 1, "", "", 0, 0 );		
		
		// compute fist straight line deltas
		$DeltaX = round( $Radius * cos( $Angle1 ) );
		$DeltaY = round( $Radius * sin( $Angle1 ) );
		
		// add fist straight line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// compute angles and radii
		$Alpha = ( $Angle2 - $Angle1 ) / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// compute second line deltas
		$DeltaX = -round( $Radius * cos( $Angle2 ) );
		$DeltaY = -round( $Radius * sin( $Angle2 ) );
		
		// add second line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 0, 1, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
	
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	

	// Basic shapes with solid fills 
	
	/**
	 * integer DefineTriangleSolid(integer X1,
	 *             	integer Y1, integer X2, integer Y2,
	 *          	integer X3, integer Y3, integer Width,
	 *            	boolean AlphaFlag, boolean EdgeFlag,
	 *     			integer R, integer G, integer B, integer A,
	 *           	boolean FillFlag, integer FillR,
	 *     			integer FillG, integer FillB, integer FillA)
	 *
	 * Defines a triangle with the solid fill.
	 */
	function DefineTriangleSolid( $X1, $Y1, $X2, $Y2, $X3, $Y3, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $FillFlag, $FillR, $FillG, $FillB, $FillA )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineTriangleSolid: character limit exceeded." );
		
		if ( $FillFlag )
		{	
			// define fill styles (just one in this case)
			$FillStyle      = $this->packFILLSTYLE( 0x00, $FillR, $FillG, $FillB, $AlphaFlag, $FillA, "", "", "", "" );
			$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
			$nFillBits      = 1;
			$FillStyleIndex = 1;
		} 
		else 
		{	
			// define fill styles (none are used here)
			$FillStyle      = "";
			$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
			$nFillBits      = 0;
			$FillStyleIndex = 0;
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		$Y3 = $this->FrameSize["Ymax"] - $Y3;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, $FillFlag, 0, 1, $X1, $Y1, $nFillBits, $nLineBits, 0, $FillStyleIndex, $LineStyleIndex, "", "", 0, 0 );		
		
		// compute deltas for triangle edges
		$DeltaX1 = $X2 - $X1;
		$DeltaY1 = $Y2 - $Y1;
		$DeltaX2 = $X3 - $X2;
		$DeltaY2 = $Y3 - $Y2;
		$DeltaX3 = $X1 - $X3;
		$DeltaY3 = $Y1 - $Y3;
		
		// test if one or two of the triangle's edges are 
		// general, horizontal or vertical and use the 
		// appropriate straight edge record
		if ( ( $DeltaX1 == 0 ) && ( $DeltaY1 != 0 ) )
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX1, $DeltaY1 );
		else if ( ( $DeltaX1 != 0 ) && ( $DeltaY1 == 0 ) ) 
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX1, $DeltaY1 );
		else
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX1, $DeltaY1 );

		if ( ( $DeltaX2 == 0 ) && ( $DeltaY2 != 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX2, $DeltaY2 );
		else if ( ( $DeltaX2 != 0 ) && ( $DeltaY2 == 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX2, $DeltaY2 );
		else
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX2, $DeltaY2 );

		if ( ( $DeltaX3 == 0 ) && ( $DeltaY3 != 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX3, $DeltaY3 );
		else if ( ( $DeltaX3 != 0 ) && ( $DeltaY3 == 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX3, $DeltaY3 );
		else
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX3, $DeltaY3 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2, $X3 ) - $margin;
		$X_max  = max( $X1, $X2, $X3 ) + $margin;
		$Y_min  = min( $Y1, $Y2, $Y3 ) - $margin;
		$Y_max  = max( $Y1, $Y2, $Y3 ) + $margin;
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, $nFillBits, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
	
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineRectangleSolid(integer X1,
	 *				integer Y1, integer X2, integer Y2,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, boolean FillFlag, 
	 *				integer FillR, integer FillG, integer FillB,
	 *				integer FillA)
	 *
	 * Defines a rectangle with the solid fill.
	 */
	function DefineRectangleSolid( $X1, $Y1, $X2, $Y2, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $FillFlag, $FillR, $FillG, $FillB, $FillA )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineRectangleSolid: character limit exceeded." );
		
		if ( $FillFlag ) 
		{	
			// define fill styles (just one in this case)
			$FillStyle      = $this->packFILLSTYLE( 0x00, $FillR, $FillG, $FillB, $AlphaFlag, $FillA, "", "", "", "" );
			$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
			$nFillBits      = 1;
			$FillStyleIndex = 1;
		} 
		else 
		{	
			// define fill styles (none are used here)
			$FillStyle      = "";
			$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
			$nFillBits      = 0;
			$FillStyleIndex = 0;
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinate.
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, $FillFlag, 0, 1, $X1, $Y1, $nFillBits, $nLineBits, 0, $FillStyleIndex, $LineStyleIndex, "", "", 0, 0 );
		
		// compute deltas
		$DeltaX = $X2 - $X1;
		$DeltaY = $Y2 - $Y1;
		
		// add vertical and horizontal straight edges
		$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, 0, $DeltaY  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX, 0  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, 0, -$DeltaY );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, -$DeltaX, 0 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2 ) - $margin;
		$X_max  = max( $X1, $X2 ) + $margin;
		$Y_min  = min( $Y1, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $Y2 ) + $margin;
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, $nFillBits, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
		
		// return the CharacterInfo array for this shape.
		return $CharacterInfo;
	}
	
	/**
	 * integer DefinePolygonSolid(integer Segments,
	 *         		integer X1, integer Y1, integer Radius,
	 *           	integer Width, boolean AlphaFlag,
	 *         		boolean EdgeFlag, integer R, integer G,
	 *         		integer B, integer A, boolean FillFlag,
	 *    			integer FillR, integer FillG, integer FillB,
	 *				integer FillA)
	 *
	 * Draws a polygon with the solid fill.
	 */
	function DefinePolygonSolid( $Segments, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $FillFlag, $FillR, $FillG, $FillB, $FillA )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefinePolygonSolid: character limit exceeded." );
		
		if ( $FillFlag ) 
		{	
			// define fill styles (just one in this case)
			$FillStyle      = $this->packFILLSTYLE( 0x00, $FillR, $FillG, $FillB, $AlphaFlag, $FillA, "", "", "", "" );
			$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
			$nFillBits      = 1;
			$FillStyleIndex = 1;
		} 
		else 
		{	
			// define fill styles (none are used here)
			$FillStyle      = "";
			$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
			$nFillBits      = 0;
			$FillStyleIndex = 0;
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1;
		$Y2 = $Y1 - $Radius;
		
		// select line and fill style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, $FillFlag, 0, 1, $X2, $Y2, $nFillBits, $nLineBits, 0, $FillStyleIndex, $LineStyleIndex, "", "", 0, 0 );
		
		// compute step angle
		$step = 2 * pi() / $Segments;

		for ( $n = 0; $n < $Segments; $n++ ) 
		{	
			// compute x and y deltas
			$angle  = -( pi() / 2 ) + $step * $n;
			$X3     = $X1 + $Radius * cos( $angle );
			$Y3     = $Y1 + $Radius * sin( $angle );
			$X4     = $X1 + $Radius * cos( $angle + $step );
			$Y4     = $Y1 + $Radius * sin( $angle + $step );
			$DeltaX = round( $X4 - $X3 );
			$DeltaY = round( $Y4 - $Y3 );
			
			// add a straight edge
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, $nFillBits, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineCircleSolid(integer Accuracy,
	 *				integer X1, integer Y1, integer Radius,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, boolean FillFlag,
	 *				integer FillR, integer FillG, integer FillB,
	 *				integer FillA)
	 *
	 * Defines a circle with the solid fill.
	 */
	function DefineCircleSolid( $Accuracy, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $FillFlag, $FillR, $FillG, $FillB, $FillA )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineCircleSolid: character limit exceeded." );
		
		if ( $FillFlag ) 
		{	
			// define fill styles (just one in this case)
			$FillStyle      = $this->packFILLSTYLE( 0x00, $FillR, $FillG, $FillB, $AlphaFlag, $FillA, "", "", "", "" );
			$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
			$nFillBits      = 1;
			$FillStyleIndex = 1;
		} 
		else 
		{	
			// define fill styles (none are used here)
			$FillStyle      = "";
			$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
			$nFillBits      = 0;
			$FillStyleIndex = 0;
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1 + $Radius;
		$Y2 = $Y1;
		
		// select line and fill style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, $FillFlag, 0, 1, $X2, $Y2, $nFillBits, $nLineBits, 0, $FillStyleIndex, $LineStyleIndex, "", "", 0, 0 );
		
		// compute angles and radii
		$Alpha = 2 * pi() / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, $nFillBits, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
	
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineArcClosedSolid(integer Accuracy,
	 *				float Angle1, float Angle2, integer X1,
	 *				integer Y1, integer Radius, integer Width,
	 *				boolean AlphaFlag, boolean EdgeFlag,
	 *				integer R, integer G, integer B, integer A,
	 *				boolean FillFlag, integer FillR,
	 *				integer FillG, integer FillB, integer FillA)
	 *
	 * Draws a closed arc with the solid fill.
	 */
	function DefineArcClosedSolid( $Accuracy, $Angle1, $Angle2, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $FillFlag, $FillR, $FillG, $FillB, $FillA )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineArcClosedSolid: character limit exceeded." );
		
		if ( $FillFlag ) 
		{	
			// define fill styles (just one in this case)
			$FillStyle      = $this->packFILLSTYLE( 0x00, $FillR, $FillG, $FillB, $AlphaFlag, $FillA, "", "", "", "" );
			$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
			$nFillBits      = 1;
			$FillStyleIndex = 1;
		} 
		else 
		{	
			// define fill styles (none are used here)
			$FillStyle      = "";
			$FillStyleArray = $this->packFILLSTYLEARRAY( 0, $FillStyle ); 
			$nFillBits      = 0;
			$FillStyleIndex = 0;
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, $FillFlag, 0, 1, $X1, $Y1, $nFillBits, $nLineBits, 0, $FillStyleIndex, $LineStyleIndex, "", "", 0, 0 );
		
		// compute fist straight line deltas
		$DeltaX = round( $Radius * cos( $Angle1 ) );
		$DeltaY = round( $Radius * sin( $Angle1 ) );
		
		// add fist straight line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// compute angles and radii
		$Alpha = ( $Angle2 - $Angle1 ) / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// compute second line deltas
		$DeltaX = -round( $Radius * cos( $Angle2 ) );
		$DeltaY = -round( $Radius * sin( $Angle2 ) );
		
		// add second line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape and add it to the main movie bytestream
		$ShapeRecords   = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, $nFillBits, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	

	// Basic shapes with gradient fills 
	
	/**
	 * integer DefineTriangleGradient(integer X1,
	 *				integer Y1, integer X2, integer Y2,
	 *				integer X3, integer Y3, integer Width,
	 *				boolean AlphaFlag, boolean EdgeFlag,
	 *				integer R, integer G, integer B, integer A,
	 *				string GradientType,
	 *				array GradientDefinition, boolean AutoFit,
	 *				string GradientMatrix)
	 *
	 * Defines a triangle with the gradient fill.
	 */
	function DefineTriangleGradient( $X1, $Y1, $X2, $Y2, $X3, $Y3, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $GradientType, $GradientDefinition, $AutoFitFlag, $GradientMatrix )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineTriangleGradient: character limit exceeded." );
		
		// check the number of gradient entries
		$m = count($GradientDefinition);
		
		if ( $m < 1 )
			return PEAR::raiseError( "Gradient too short." );
		
		if ( $m > 8 )
			return PEAR::raiseError( "Gradient too long." );
		
		// check for alpha in gradient definition
		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			
			if ( array_key_exists( "A", $GradientRecord ) ) 
			{
				$A = $GradientRecord["A"];
				$AlphaFlag = true;
			}
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		$Y3 = $this->FrameSize["Ymax"] - $Y3;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X1, $Y1, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute deltas for triangle edges
		$DeltaX1 = $X2 - $X1;
		$DeltaY1 = $Y2 - $Y1;
		$DeltaX2 = $X3 - $X2;
		$DeltaY2 = $Y3 - $Y2;
		$DeltaX3 = $X1 - $X3;
		$DeltaY3 = $Y1 - $Y3;
		
		// test if one or two of the triangle's edges are 
		// general, horizontal or vertical and use the 
		// appropriate straight edge record
		if ( ( $DeltaX1 == 0 ) && ( $DeltaY1 != 0 ) )
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX1, $DeltaY1 );
		else if ( ( $DeltaX1 != 0 ) && ( $DeltaY1 == 0 ) )
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX1, $DeltaY1 );
		else
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX1, $DeltaY1 );
		
		if ( ( $DeltaX2 == 0 ) && ( $DeltaY2 != 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX2, $DeltaY2 );
		else if ( ( $DeltaX2 != 0 ) && ( $DeltaY2 == 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX2, $DeltaY2 );
		else
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX2, $DeltaY2 );
		
		if ( ( $DeltaX3 == 0 ) && ( $DeltaY3 != 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX3, $DeltaY3 );
		else if ( ( $DeltaX3 != 0 ) && ( $DeltaY3 == 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX3, $DeltaY3 );
		else
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX3, $DeltaY3 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2, $X3 ) - $margin;
		$X_max  = max( $X1, $X2, $X3 ) + $margin;
		$Y_min  = min( $Y1, $Y2, $Y3 ) - $margin;
		$Y_max  = max( $Y1, $Y2, $Y3 ) + $margin;
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );

		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			$Ratio = $GradientRecord["Ratio"];
			$R = $GradientRecord["R"];
			$G = $GradientRecord["G"];
			$B = $GradientRecord["B"];
			
			if ( array_key_exists( "A", $GradientRecord ) )
				$A = $GradientRecord["A"];
			else
				$A = 255;
	
			$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
		}
		
		$Gradient = $this->packGRADIENT( $Gradient, $AlphaFlag );
		
		// check if the gradient is to be fitted automatically
		if ( $AutoFitFlag )
			$GradientMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / 32768, ( $Y_max - $Y_min ) / 32768, false, 0, 0, $X_min + ( $X_max - $X_min ) / 2, $Y_min + ( $Y_max - $Y_min ) / 2 ); 
		
		if ( $GradientType == "l" )
			$GradType = 0x10;
		else if ( $GradientType == "r" )
			$GradType = 0x12;
		else
			return PEAR::raiseError( "Wrong gradient type." );
		
		$FillStyle = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineRectangleGradient(integer X1,
	 *				integer Y1, integer X2, integer Y2,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, string GradientType,
	 *				array GradientDefinition, boolean AutoFitFlag,
	 *				string GradientMatrix)
	 *
	 * Defines a rectangle with the gradient fill.
	 */
	function DefineRectangleGradient( $X1, $Y1, $X2, $Y2, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $GradientType, $GradientDefinition, $AutoFitFlag, $GradientMatrix )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineRectangleGradient: character limit exceeded." );

		// check the number of gradient entries
		$m = count( $GradientDefinition );
		
		if ( $m < 1 )
			return PEAR::raiseError( "Gradient too short." );
		
		if ( $m > 8 )
			return PEAR::raiseError( "Gradient too long." );
		
		// check for alpha in gradient definition
		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			
			if ( array_key_exists( "A", $GradientRecord ) ) 
			{
				$A = $GradientRecord["A"];
				$AlphaFlag = true;
			}
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X1, $Y1, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute deltas
		$DeltaX = $X2 - $X1;
		$DeltaY = $Y2 - $Y1;
		
		// add vertical and horizontal straight edges
		$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, 0, $DeltaY  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX, 0  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, 0, -$DeltaY );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, -$DeltaX, 0 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2 ) - $margin;
		$X_max  = max( $X1, $X2 ) + $margin;
		$Y_min  = min( $Y1, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $Y2 ) + $margin;
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );

		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			$Ratio = $GradientRecord["Ratio"];
			$R = $GradientRecord["R"];
			$G = $GradientRecord["G"];
			$B = $GradientRecord["B"];
			
			if ( array_key_exists( "A", $GradientRecord ) )
				$A = $GradientRecord["A"];
			else
				$A = 255;
	
			$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
		}
		
		$Gradient = $this->packGRADIENT( $Gradient, $AlphaFlag );
		
		// check if the gradient is to be fitted automatically
		if ( $AutoFitFlag )
			$GradientMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / 32768, ( $Y_max - $Y_min ) / 32768, false, 0, 0, $X_min + ( $X_max - $X_min ) / 2, $Y_min + ( $Y_max - $Y_min ) / 2 ); 
		
		if ( $GradientType == "l" )
			$GradType = 0x10;
		else if ( $GradientType == "r" )
			$GradType = 0x12;
		else
			return PEAR::raiseError( "Wrong gradient type." );
		
		$FillStyle = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag ) 
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT($X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefinePolygonGradient(integer Segments,
	 *         		integer X1, integer Y1, integer Radius,
	 *           	integer Width, boolean AlphaFlag,
	 *        	 	boolean EdgeFlag, integer R, integer G,
	 *      		integer B, integer A, string GradientType,
	 *  			array GradientDefinition, boolean AutoFitFlag,
	 *				string GradientMatrix)
	 *
	 * Defines a regular polygon with the gradient fill.
	 */
	function DefinePolygonGradient( $Segments, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $GradientType, $GradientDefinition, $AutoFitFlag, $GradientMatrix )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefinePolygonGradient: character limit exceeded." );
		
		// check the number of gradient entries
		$m = count($GradientDefinition);

		if ( $m < 1 )
			return PEAR::raiseError( "Gradient too short.");
		
		if ( $m > 8 )
			return PEAR::raiseError( "Gradient too long." );
		
		// check for alpha in gradient definition
		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			
			if ( array_key_exists( "A", $GradientRecord ) ) 
			{
				$A = $GradientRecord["A"];
				$AlphaFlag = True;
			}
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1;
		$Y2 = $Y1 - $Radius;
		
		// select line and fill style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X2, $Y2, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute step angle
		$step = 2 * pi() / $Segments;

		for ( $n = 0; $n < $Segments; $n++ ) 
		{	
			// compute x and y deltas
			$angle  = -( pi() / 2 ) + $step * $n;
			$X3     = $X1 + $Radius * cos( $angle );
			$Y3     = $Y1 + $Radius * sin( $angle );
			$X4     = $X1 + $Radius * cos( $angle + $step );
			$Y4     = $Y1 + $Radius * sin( $angle + $step );
			$DeltaX = round( $X4 - $X3 );
			$DeltaY = round( $Y4 - $Y3 );
			
			// add a straight edge
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds.
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );

		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			$Ratio = $GradientRecord["Ratio"];
			$R = $GradientRecord["R"];
			$G = $GradientRecord["G"];
			$B = $GradientRecord["B"];
			
			if ( array_key_exists( "A", $GradientRecord ) )
				$A = $GradientRecord["A"];
			else
				$A = 255;
	
			$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
		}
		
		$Gradient = $this->packGRADIENT( $Gradient, $AlphaFlag );
		
		// check if the gradient is to be fitted automatically
		if ( $AutoFitFlag )
			$GradientMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / 32768, ( $Y_max - $Y_min ) / 32768, false, 0, 0, $X_min + ( $X_max - $X_min ) / 2, $Y_min + ( $Y_max - $Y_min ) / 2 );
		
		if ( $GradientType == "l" )
			$GradType = 0x10;
		else if ( $GradientType == "r" ) 
			$GradType = 0x12;
		else
			return PEAR::raiseError( "Wrong gradient type." );
		
		$FillStyle = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 *  integer DefineCircleGradient(integer Accuracy,
	 *				integer X1, integer Y1, integer Radius,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, string GradientType,
	 *				array GradientDefinition, boolean AutoFitFlag,
	 *				string GradientMatrix)
	 *
	 * Defines a circle with the gradient fill.
	 */
	function DefineCircleGradient( $Accuracy, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $GradientType, $GradientDefinition, $AutoFitFlag, $GradientMatrix )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineCircleGradient: character limit exceeded.");
		
		// check the number of gradient entries
		$m = count( $GradientDefinition );
		
		if ( $m < 1 )
			return PEAR::raiseError( "Gradient too short." );
		
		if ( $m > 8 )
			return PEAR::raiseError( "Gradient too long." );
		
		// check for alpha in gradient definition
		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			
			if ( array_key_exists( "A", $GradientRecord ) ) 
			{
				$A = $GradientRecord["A"];
				$AlphaFlag = true;
			}
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1 + $Radius;
		$Y2 = $Y1;
		
		// select line and fill style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X2, $Y2, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute angles and radii
		$Alpha = 2 * pi() / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );

		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			$Ratio = $GradientRecord["Ratio"];
			$R = $GradientRecord["R"];
			$G = $GradientRecord["G"];
			$B = $GradientRecord["B"];
			
			if ( array_key_exists( "A", $GradientRecord ) )
				$A = $GradientRecord["A"];
			else
				$A = 255;
	
			$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
		}
		
		$Gradient = $this->packGRADIENT( $Gradient, $AlphaFlag );
		
		// check if the gradient is to be fitted automatically
		if ( $AutoFitFlag )
			$GradientMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / 32768, ( $Y_max - $Y_min ) / 32768, false, 0, 0, $X_min + ( $X_max - $X_min ) / 2, $Y_min + ( $Y_max - $Y_min ) / 2 ); 
		
		if ( $GradientType == "l" )
			$GradType = 0x10;
		else if ( $GradientType == "r" )
			$GradType = 0x12;
		else
			return PEAR::raiseError( "Wrong gradient type.");
		
		$FillStyle = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineArcClosedGradient(
	 *   			integer Accuracy, float Angle1, float Angle2,
	 *         		integer X1, integer Y1, integer Radius,
	 *           	integer Width, boolean AlphaFlag,
	 *         		boolean EdgeFlag, integer R, integer G,
	 *      		integer B, integer A, string GradientType,
	 * 				array GradientDefinition, boolean AutoFitFlag,
	 *				string GradientMatrix)
	 *
	 * Defines a closed arc with the gradient fill.
	 */
	function DefineArcClosedGradient( $Accuracy, $Angle1, $Angle2, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $GradientType, $GradientDefinition, $AutoFitFlag, $GradientMatrix )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineArcClosedGradient: character limit exceeded." );
		
		// check the number of gradient entries
		$m = count( $GradientDefinition );
		
		if ( $m < 1 )
			return PEAR::raiseError( "Gradient too short." );
		
		if ( $m > 8 )
			return PEAR::raiseError( "Gradient too long." );
		
		// check for alpha in gradient definition
		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			
			if ( array_key_exists( "A", $GradientRecord ) ) 
			{
				$A = $GradientRecord["A"];
				$AlphaFlag = true;
			}
		}
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X1, $Y1, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute fist straight line deltas
		$DeltaX = round( $Radius * cos( $Angle1 ) );
		$DeltaY = round( $Radius * sin( $Angle1 ) );
		
		// add fist straight line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// compute angles and radii
		$Alpha = ( $Angle2 - $Angle1 ) / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// compute second line deltas
		$DeltaX = -round( $Radius * cos( $Angle2 ) );
		$DeltaY = -round( $Radius * sin( $Angle2 ) );
		
		// add second line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );

		for ( $n = 1; $n <= $m; $n++ ) 
		{
			$GradientRecord = $GradientDefinition[$n - 1];
			$Ratio = $GradientRecord["Ratio"];
			$R = $GradientRecord["R"];
			$G = $GradientRecord["G"];
			$B = $GradientRecord["B"];
			
			if ( array_key_exists( "A", $GradientRecord ) ) 
				$A = $GradientRecord["A"];
			else 
				$A = 255;
			
			$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
		}
		
		$Gradient = $this->packGRADIENT( $Gradient, $AlphaFlag );
		
		// check if the gradient is to be fitted automatically
		if ( $AutoFitFlag )
			$GradientMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / 32768, ( $Y_max - $Y_min ) / 32768, false, 0, 0, $X_min + ( $X_max - $X_min ) / 2, $Y_min + ( $Y_max - $Y_min ) / 2 ); 
		
		if ( $GradientType == "l" ) 
			$GradType = 0x10;
		else if ( $GradientType == "r" ) 
			$GradType = 0x12;
		else 
			return PEAR::raiseError( "Wrong gradient type." );
		
		$FillStyle = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $GradType, "", "", "", $AlphaFlag, "", $GradientMatrix, $Gradient, "", "" );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	
	// basic shapes with bitmap fills
	
	/**
	 * null DefineBitmapJPEGTables(string file)
	 * Defines the JPEGTables tag.
	 */
	function DefineBitmapJPEGTables( $file )
	{
		// create the JPEG encoding tables
		$BitmapJPEG = $this->parseJPEGfile( $file );
		$this->packJPEGTablesTag( $BitmapJPEG["JPEGEncoding"] );
	}
	
	/**
	 * null DefineBitmapJPEGImage(string file)
	 * Defines the DefineBits tag.
	 */
	function DefineBitmapJPEGImage($file)
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineBitmapJPEGImage: character limit exceeded." );
		
		$BitmapJPEG = $this->parseJPEGfile( $file );
		$this->packDefineBitsTag( $this->CharacterID, $BitmapJPEG["JPEGImage"] );
		
		$this->Bitmaps[$this->CharacterID] = array(
			"width"  => $BitmapJPEG["width"], 
			"height" => $BitmapJPEG["height"]
		);

		// return the CharacterID of this bitmap
		return $this->CharacterID;
	}
	
	/**
	 * null DefineBitmapJPEG(string file)
	 * Defines the DefineBitsJPEG2 tag.
	 */
	function DefineBitmapJPEG( $file )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineBitmapJPEG: character limit exceeded." );
		
		$BitmapJPEG = $this->parseJPEGfile( $file );
		$this->packDefineBitsJPEG2Tag( $this->CharacterID, $BitmapJPEG["JPEGEncoding"], $BitmapJPEG["JPEGImage"] );
		
		$this->Bitmaps[$this->CharacterID] = array(
			"width"  => $BitmapJPEG["width"], 
			"height" => $BitmapJPEG["height"]
		);

		// return the CharacterID of this bitmap
		return $this->CharacterID;
	}
	
	/**
	 * null DefineBitmapJPEGAlpha(string file, string fileAlpha)
	 * Defines the DefineBitsJPEG3 tag.
	 */
	function DefineBitmapJPEGAlpha( $fileJPEG, $fileAlpha )
	{	
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineBitmapJPEGAlpha: character limit exceeded." );
		
		$BitmapJPEG = $this->parseJPEGfile( $fileJPEG );
		$BitmapTIFF = $this->parseTIFFfile( $fileAlpha, null );
		
		if ( PEAR::isError( $BitmapTIFF ) )
			return $BitmapTIFF;
			
		$this->packDefineBitsJPEG3Tag( $this->CharacterID, $BitmapJPEG["JPEGEncoding"], $BitmapJPEG["JPEGImage"], $BitmapTIFF["alphadata"] );
		
		$this->Bitmaps[$this->CharacterID] = array(
			"width"  => $BitmapJPEG["width"], 
			"height" => $BitmapJPEG["height"], 
			"Alpha"  => true 
		);

		// return the CharacterID of this bitmap
		return $this->CharacterID;
	}
	
	/**
	 * null DefineBitmapTIFF(string file)
	 * Defines the DefineBitsLossless tag and returns its ID.
	 */
	function DefineBitmapTIFF( $file )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineBitmapTIFF: character limit exceeded." );
		
		$BitmapTIFF = $this->parseTIFFfile( $file, null );
		
		if ( PEAR::isError( $BitmapTIFF ) )
			return $BitmapTIFF;
			
		$res = $this->packDefineBitsLosslessTag( $this->CharacterID, $BitmapTIFF["format"], $BitmapTIFF["width"], $BitmapTIFF["height"], $BitmapTIFF["colortablesize"], $BitmapTIFF["zlibbitmapdata"] );
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		$this->Bitmaps[$this->CharacterID] = array( 
			"width"  => $BitmapTIFF["width"], 
			"height" => $BitmapTIFF["height"] 
		);
	
		// return the CharacterID of this bitmap
		return $this->CharacterID;
	}
	
	/**
	 * null DefineBitmapTIFFAlpha(string TIFFfile, array AlphaPalette)
	 * Defines the DefineBitsLossless2 tag and returns its ID.
	 */
	function DefineBitmapTIFFAlpha( $TIFFFile, $AlphaPalette )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineBitmapTIFFAlpha: character limit exceeded." );
		
		// read image and alpha bitmaps
		$BitmapTIFF = $this->parseTIFFfile( $TIFFFile, $AlphaPalette );
		
		if ( PEAR::isError( $BitmapTIFF ) )
			return $BitmapTIFF;
			
		$colormap   = $BitmapTIFF["colortable"];
		
		if ( strlen( $colormap ) < 6 )
			return PEAR::raiseError( "DefineBitmapTIFFAlpha: color map must have at least 2 colors." );
		
		$colormap = str_pad( $colormap, 1024, chr( 0 ) );
		$res = $this->packDefineBitsLossless2Tag( $this->CharacterID, $BitmapTIFF["format"], $BitmapTIFF["width"], $BitmapTIFF["height"], $BitmapTIFF["colortablesize"], $BitmapTIFF["zlibbitmapdata"] );
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		$this->Bitmaps[$this->CharacterID] = array( 
			"width"  => $BitmapTIFF["width"], 
			"height" => $BitmapTIFF["height"], 
			"Alpha"  => true
		);
		
		// return the CharacterID of this bitmap
		return $this->CharacterID;
	}
	
	/**
	 * integer DefineTriangleBitmap(integer X1,
	 *				integer Y1, integer X2, integer Y2,
	 *				integer X3, integer Y3, integer Width,
	 *				boolean AlphaFlag, boolean EdgeFlag,
	 *				integer R, integer G, integer B, integer A,
	 *				string BitmapType, integer BitmapID,
	 *				boolean AutoFitFlag, string BitmapMatrix)
	 *
	 * Defines a triangle with the bitmap fill.
	 */
	function DefineTriangleBitmap( $X1, $Y1, $X2, $Y2, $X3, $Y3, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $BitmapType, $BitmapID, $AutoFitFlag, $BitmapMatrix )
	{		
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineTriangleBitmap: character limit exceeded." );
		
		// check if the bitmap has alpha channel information
		if ( array_key_exists( "Alpha", $this->Bitmaps[$BitmapID] ) )
			$AlphaFlag = true;
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		$Y3 = $this->FrameSize["Ymax"] - $Y3;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X1, $Y1, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );		
		
		// compute deltas for triangle edges
		$DeltaX1 = $X2 - $X1;
		$DeltaY1 = $Y2 - $Y1;
		$DeltaX2 = $X3 - $X2;
		$DeltaY2 = $Y3 - $Y2;
		$DeltaX3 = $X1 - $X3;
		$DeltaY3 = $Y1 - $Y3;
		
		// test if one or two of the triangle's edges are 
		// general, horizontal or vertical and use the 
		// appropriate straight edge record
		if ( ( $DeltaX1 == 0 ) && ( $DeltaY1 != 0 ) )
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX1, $DeltaY1 );
		else if ( ( $DeltaX1 != 0 ) && ( $DeltaY1 == 0 ) )
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX1, $DeltaY1 );
		else
			$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX1, $DeltaY1 );
		
		if ( ( $DeltaX2 == 0 ) && ( $DeltaY2 != 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX2, $DeltaY2 );
		else if ( ( $DeltaX2 != 0 ) && ( $DeltaY2 == 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX2, $DeltaY2 );
		else
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX2, $DeltaY2 );
		
		if ( ( $DeltaX3 == 0 ) && ( $DeltaY3 != 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, $DeltaX3, $DeltaY3 );
		else if ( ( $DeltaX3 != 0 ) && ( $DeltaY3 == 0 ) )
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX3, $DeltaY3 );
		else
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX3, $DeltaY3 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2, $X3 ) - $margin;
		$X_max  = max( $X1, $X2, $X3 ) + $margin;
		$Y_min  = min( $Y1, $Y2, $Y3 ) - $margin;
		$Y_max  = max( $Y1, $Y2, $Y3 ) + $margin;
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		
		// check if the bitmap is to be fitted automatically
		if ( $AutoFitFlag )
			$BitmapMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / $this->Bitmaps[$BitmapID]["width"], ( $Y_max - $Y_min ) / $this->Bitmaps[$BitmapID]["height"], false, 0, 0, $X_min, $Y_min ); 
		
		if ( $BitmapType == "c" )
			$BType = 0x41;
		else if ( $BitmapType == "t" )
			$BType = 0x40;
		else
			return PEAR::raiseError( "Wrong bitmap type." );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $BType, "", "", "", $AlphaFlag, "", "", "", $BitmapID, $BitmapMatrix );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineRectangleBitmap(integer X1,
	 *				integer Y1, integer X2, integer Y2,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, string BitmapType,
	 *				integer BitmapID, boolean AutoFitFlag, 
	 *				string BitmapMatrix)
	 *
	 * Defines a rectangle with the bitmap fill.
	 */
	function DefineRectangleBitmap( $X1, $Y1, $X2, $Y2, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $BitmapType, $BitmapID, $AutoFitFlag, $BitmapMatrix )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineRectangleBitmap: character limit exceeded." );
		
		// check if the bitmap has alpha channel information
		if ( array_key_exists( "Alpha", $this->Bitmaps[$BitmapID] ) )
			$AlphaFlag = true;
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$Y2 = $this->FrameSize["Ymax"] - $Y2;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X1, $Y1, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute deltas
		$DeltaX = $X2 - $X1;
		$DeltaY = $Y2 - $Y1;
		
		// add vertical and horizontal straight edges
		$EdgeRecords  = $this->packSTRAIGHTEDGERECORD( 0, 1, 0, $DeltaY  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, $DeltaX, 0  );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 1, 0, -$DeltaY );
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 0, 0, -$DeltaX, 0 );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = min( $X1, $X2 ) - $margin;
		$X_max  = max( $X1, $X2 ) + $margin;
		$Y_min  = min( $Y1, $Y2 ) - $margin;
		$Y_max  = max( $Y1, $Y2 ) + $margin;
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		
		// check if the bitmap is to be fitted automatically
		if ( $AutoFitFlag )
			$BitmapMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / $this->Bitmaps[$BitmapID]["width"], ( $Y_max - $Y_min ) / $this->Bitmaps[$BitmapID]["height"], false, 0, 0, $X_min, $Y_min ); 
		
		if ( $BitmapType == "c" )
			$BType = 0x41;
		else if ( $BitmapType == "t" )
			$BType = 0x40;
		else
			return PEAR::raiseError( "Wrong bitmap type." );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $BType, "", "", "", $AlphaFlag, "", "", "", $BitmapID, $BitmapMatrix );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefinePolygonBitmap(integer Segments,
	 *				integer X1, integer Y1, integer Radius,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, string BitmapType,
	 *				integer BitmapID, boolean AutoFitFlag,
	 *				string BitmapMatrix)
	 *				integer FillG, integer FillB, integer FillA)
	 *
	 * Defines a regular polygon with the bitmap fill.
	 */
	function DefinePolygonBitmap( $Segments, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $BitmapType, $BitmapID, $AutoFitFlag, $BitmapMatrix )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefinePolygonBitmap: character limit exceeded." );
		
		// check if the bitmap has alpha channel information
		if ( array_key_exists( "Alpha", $this->Bitmaps[$BitmapID] ) )
			$AlphaFlag = true;
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1;
		$Y2 = $Y1 - $Radius;
		
		// select line and fill style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X2, $Y2, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute step angle
		$step = 2 * pi() / $Segments;
		
		for ( $n = 0; $n < $Segments; $n++ ) 
		{	
			// compute x and y deltas
			$angle  = -( pi() / 2 ) + $step * $n;
			$X3     = $X1 + $Radius * cos( $angle );
			$Y3     = $Y1 + $Radius * sin( $angle );
			$X4     = $X1 + $Radius * cos( $angle + $step );
			$Y4     = $Y1 + $Radius * sin( $angle + $step );
			$DeltaX = round( $X4 - $X3 );
			$DeltaY = round( $Y4 - $Y3 );
			
			// add a straight edge
			$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		
		// check if the bitmap is to be fitted automatically
		if ( $AutoFitFlag )
			$BitmapMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / $this->Bitmaps[$BitmapID]["width"], ( $Y_max - $Y_min ) / $this->Bitmaps[$BitmapID]["height"], false, 0, 0, $X_min, $Y_min ); 
		
		if ( $BitmapType == "c" ) 
			$BType = 0x41;
		else if ( $BitmapType == "t" )
			$BType = 0x40;
		else
			return PEAR::raiseError( "Wrong bitmap type." );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $BType, "", "", "", $AlphaFlag, "", "", "", $BitmapID, $BitmapMatrix );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/**
	 * integer DefineCircleBitmap(integer Accuracy,
	 *				integer X1, integer Y1, integer Radius,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, string BitmapType,
	 *				integer BitmapID, boolean AutoFitFlag,
	 *				string BitmapMatrix)
	 *
	 * Defines a circle with the bitmap fill.
	 */
	function DefineCircleBitmap( $Accuracy, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $BitmapType, $BitmapID, $AutoFitFlag, $BitmapMatrix )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineCircleBitmap: character limit exceeded." );
		
		// check if the bitmap has alpha channel information
		if ( array_key_exists( "Alpha", $this->Bitmaps[$BitmapID] ) )
			$AlphaFlag = true;
		
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		$X2 = $X1 + $Radius;
		$Y2 = $Y1;
		
		// select line and fill style, move pen to X2, Y2
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X2, $Y2, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute angles and radii
		$Alpha = 2 * pi() / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		
		// check if the bitmap is to be fitted automatically
		if ( $AutoFitFlag )
			$BitmapMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / $this->Bitmaps[$BitmapID]["width"], ( $Y_max - $Y_min ) / $this->Bitmaps[$BitmapID]["height"], false, 0, 0, $X_min, $Y_min ); 
		
		if ( $BitmapType == "c" )
			$BType = 0x41;
		else if ( $BitmapType == "t" )
			$BType = 0x40;
		else
			return PEAR::raiseError( "Wrong bitmap type." );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $BType, "", "", "", $AlphaFlag, "", "", "", $BitmapID, $BitmapMatrix );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	/** 
	* integer DefineArcClosedBitmap(integer Accuracy,
	 *				integer X1, integer Y1, integer Radius,
	 *				integer Width, boolean AlphaFlag,
	 *				boolean EdgeFlag, integer R, integer G,
	 *				integer B, integer A, string BitmapType,
	 *				integer BitmapID, boolean AutoFitFlag,
	 *				string BitmapMatrix)
	 *
	 * Defines a closed arc with the bitmap fill.
	 */
	function DefineArcClosedBitmap( $Accuracy, $Angle1, $Angle2, $X1, $Y1, $Radius, $Width, $AlphaFlag, $EdgeFlag, $R, $G, $B, $A, $BitmapType, $BitmapID, $AutoFitFlag, $BitmapMatrix )
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "DefineArcClosedBitmap: character limit exceeded." );
		
		// check if the bitmap has alpha channel information
		if ( array_key_exists( "Alpha", $this->Bitmaps[$BitmapID] ) )
			$AlphaFlag = true;
		
		// check for alpha in bitmap definition
		if ( $EdgeFlag ) 
		{	
			// define line styles (just one in this case)
			$LineStyle      = $this->packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A );
			$LineStyleArray = $this->packLINESTYLEARRAY( 1, $LineStyle );
			$nLineBits      = 1;
			$LineStyleIndex = 1;
		} 
		else 
		{	
			// define line styles (none in this case)
			$LineStyle      = "";
			$LineStyleArray = $this->packLINESTYLEARRAY( 0, $LineStyle );
			$nLineBits      = 0;
			$LineStyleIndex = 0;
		}
		
		// translate coordinates
		$Y1 = $this->FrameSize["Ymax"] - $Y1;
		
		// select line and fill style, move pen to X1, Y1
		$StyleChangeRecord = $this->packSTYLECHANGERECORD( 0, $EdgeFlag, 1, 0, 1, $X1, $Y1, 1, $nLineBits, 0, 1, $LineStyleIndex, "", "", 0, 0 );
		
		// compute fist straight line deltas
		$DeltaX = round( $Radius * cos( $Angle1 ) );
		$DeltaY = round( $Radius * sin( $Angle1 ) );
		
		// add fist straight line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// compute angles and radii
		$Alpha = ( $Angle2 - $Angle1 ) / $Accuracy;
		$Beta  = $Alpha / 2;
		$ControlRadius = $Radius / cos( $Beta ); 
		
		// reset anchor deltas
		$AnchorDeltaX = 0;
		$AnchorDeltaY = 0;
		
		// compute anchor and control point deltas
		for ( $n = 0; $n < $Accuracy; $n++ ) 
		{
			$step = $n * $Alpha;
			$X3   = round( $Radius * cos( $Angle1 + $step ) );
			$Y3   = round( $Radius * sin( $Angle1 + $step ) );
			$X4   = round( $ControlRadius * cos( $Angle1 + $Beta + $step ) );
			$Y4   = round( $ControlRadius * sin( $Angle1 + $Beta + $step ) );
			$X5   = round( $Radius * cos( $Angle1 + $step + $Alpha ) );
			$Y5   = round( $Radius * sin( $Angle1 + $step + $Alpha ) );
			
			$ControlDeltaX = $X4 - $X3;
			$ControlDeltaY = $Y4 - $Y3;
			$AnchorDeltaX  = $X5 - $X4;
			$AnchorDeltaY  = $Y5 - $Y4;
			
			// add a curved line
			$EdgeRecords .= $this->packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY );
		}
		
		// compute second line deltas
		$DeltaX = -round( $Radius * cos( $Angle2 ) );
		$DeltaY = -round( $Radius * sin( $Angle2 ) );
		
		// add second line
		$EdgeRecords .= $this->packSTRAIGHTEDGERECORD( 1, 0, $DeltaX, $DeltaY );
		
		// mark the end of the shape
		$EndShape = $this->packENDSHAPERECORD();
		
		// compute shape bounds
		$margin = round( $Width / 2 );
		$X_min  = $X1 - ( $Radius + $margin );
		$X_max  = $X1 + ( $Radius + $margin );
		$Y_min  = $Y1 - ( $Radius + $margin );
		$Y_max  = $Y1 + ( $Radius + $margin );
		
		// pack shape records
		$ShapeRecords = $this->packBitValues( $StyleChangeRecord["Bitstream"] . $EdgeRecords . $EndShape );
		
		// check if the bitmap is to be fitted automatically
		if ( $AutoFitFlag )
			$BitmapMatrix = $this->packMATRIX( true, ( $X_max - $X_min ) / $this->Bitmaps[$BitmapID]["width"], ( $Y_max - $Y_min ) / $this->Bitmaps[$BitmapID]["height"], false, 0, 0, $X_min, $Y_min ); 
		
		if ( $BitmapType == "c" )
			$BType = 0x41;
		else if ( $BitmapType == "t" )
			$BType = 0x40;
		else
			return PEAR::raiseError( "Wrong bitmap type." );
		
		// define fill styles (just one in this case)
		$FillStyle      = $this->packFILLSTYLE( $BType, "", "", "", $AlphaFlag, "", "", "", $BitmapID, $BitmapMatrix );
		$FillStyleArray = $this->packFILLSTYLEARRAY( 1, $FillStyle ); 
		$ShapeWithStyle = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, 1, $nLineBits, $ShapeRecords );
		
		// test if AlphaFlag is set and use the appropriate shape tag
		if ( $AlphaFlag )
			$this->packDefineShape3Tag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		else
			$this->packDefineShapeTag( $this->CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $ShapeWithStyle );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $this->CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	
	
	// freeform shape methods

	/**
	 * integer BeginShape()
	 * Returns a Character ID for the new shape.
	 */
	function BeginShape()
	{
		// increment the Character ID counter
		++$this->CharacterID;
		
		// check character ID limit
		if ( $this->CharacterID > $this->CharacterIDLimit )
			return PEAR::raiseError( "BeginShape: character limit exceeded." );

		// return the CharacterInfo array for this shape
		return $this->CharacterID;
	}
	
	/**
	 * array DefineSolidLine(integer Width, 
	 *				boolean AlphaFlag, integer R, integer G,
	 *				integer B, integer Alpha)
	 *
	 * Creates a solid line style definition and returns it.
	 */
	function DefineSolidLine( $Width, $AlphaFlag, $R, $G, $B, $Alpha )
	{
		if ( $AlphaFlag )
			$Style = array( "Width" => $Width, "R" => $R, "G" => $G, "B" => $B, "A" => $Alpha );
		else
			$Style = array( "Width" => $Width, "R" => $R, "G" => $G, "B" => $B );
				
		// return the solid line style
		return $Style;
	}
	
	/**
	 * array DefineSolidFill(boolean AlphaFlag, 
	 *				integer R, integer G, integer B, integer Alpha)
	 *
	 * Creates a solid fill style definition and 
	 * returns it.
	 */
	function DefineSolidFill( $AlphaFlag, $R, $G, $B, $Alpha )
	{
		if ( $AlphaFlag )
			$fill = array( "Type" => "solid", "R" => $R, "G" => $G, "B" => $B, "A" => $Alpha );
		else
			$fill = array( "Type" => "solid", "R" => $R, "G" => $G, "B" => $B );

		return $fill;
	}
		
	/**
	 * null SelectLineStyle(integer CharacterID,array Line)
	 * 
	 * Selects a line style.
	 */
	function SelectLineStyle( $CharacterID, $Line )
	{
		if ( $Line == 0 ) 
		{
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "line", "Definition" => "zero", "Alpha" => false );
		} 
		else 
		{
			foreach ( $Line as $element ) 
				$Line["string"] .= ":" . sprintf( "%s", $element );
			
			if ( array_key_exists( "A", $Line ) )
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "line", "Definition" => $Line, "Alpha" => true );
			else 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "line", "Definition" => $Line, "Alpha" => false );
		}
	}
	
	/**
	 * null SelectFill0Style(integer CharacterID,array Fill, string FillType, string Matrix)
	 *
	 * Selects a Fill0 style.
	 */
	function SelectFill0Style( $CharacterID, $Fill, $FillType, $Matrix )
	{
		if ( $Fill == 0 ) 
		{
			$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill0", "Type" => null, "Matrix" => $Matrix, "Definition" => "zero", "Alpha" => false );
		} 
		else if ( ( $FillType == "c" ) || ( $FillType == "t" ) ) 
		{
			$FillB["ID"] = $Fill;
			$FillB["string"] .= sprintf( "%s", $Fill );
			
			if ( $this->Bitmaps[$Fill]["Alpha"] == true ) 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill0", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $FillB, "Alpha" => true );
			else 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill0", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $FillB, "Alpha" => false );
		} 
		else 
		{
			foreach ( $Fill as $element ) 
				$Fill["string"] .= sprintf( "%s", $element );
			
			if ( array_key_exists( "Alpha", $Fill ) || array_key_exists( "A", $Fill ) )
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill0", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $Fill, "Alpha" => true );
			else 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill0", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $Fill, "Alpha" => false );
		}
	}
	
	/**
	 * null SelectFill1Style(integer CharacterID,array Fill, string FillType, string Matrix)
	 *
	 * Selects a Fill1 style.
	 */
	function SelectFill1Style( $CharacterID, $Fill, $FillType, $Matrix )
	{
		if ( $Fill == 0 ) 
		{
			$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill1", "Type" => null, "Matrix" => $Matrix, "Definition" => "zero", "Alpha" => false );
		} 
		else if ( ( $FillType == "c" ) || ( $FillType == "t" ) ) 
		{
			$FillB["ID"] = $Fill;
			$FillB["string"] .= sprintf( "%s", $Fill );
			
			if ( $this->Bitmaps[$Fill]["Alpha"] == true ) 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill0", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $FillB, "Alpha" => true );
			else 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill0", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $FillB, "Alpha" => false );
		} 
		else 
		{
			foreach ( $Fill as $element ) 
				$Fill["string"] .= sprintf( "%s", $element );
			
			if ( array_key_exists( "Alpha", $Fill ) || array_key_exists( "A", $Fill ) ) 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill1", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $Fill, "Alpha" => true );
			else 
				$this->Shapes[$CharacterID][] = array( "Record" => "changestyle", "Style" => "fill1", "Type" => $FillType, "Matrix" => $Matrix, "Definition" => $Fill, "Alpha" => false );
		}
	}
	
	/**
	 * null MoveTo(integer CharacterID, integer X, integer Y)
	 *
	 * Moves pen to a new location.
	 */
	function MoveTo( $CharacterID, $X, $Y )
	{
		$this->Shapes[$CharacterID][] = array( "Record" => "moveto", "X" => $X, "Y" => $Y );
	}
	
	/**
	 * null LineTo(integer CharacterID, integer X, integer Y)
	 *
	 * Draws a straight line to X, Y.
	 */
	function LineTo( $CharacterID, $X, $Y )
	{
		$this->Shapes[$CharacterID][] = array( "Record" => "edge", "Type" => "lineto", "AnchorX" => $X, "AnchorY" => $Y );
	}
	
	/**
	 * null CurveTo(integer CharacterID,
	 *				integer ControlX, integer ControlY, 
	 *				integer AnchorX, integer AnchorY)
	 *
	 * Draws a 2nd degree Bezier curve.
	 */
	function CurveTo( $CharacterID, $ControlX, $ControlY, $AnchorX, $AnchorY )
	{
		$this->Shapes[$CharacterID][] = array(
			"Record"   => "edge", 
			"Type"     => "curveto", 
			"ControlX" => $ControlX, 
			"ControlY" => $ControlY, 
			"AnchorX"  => $AnchorX, 
			"AnchorY"  => $AnchorY
		);
	}
	
	/**
	 * null EndShape(integer CharacterID)
	 *
	 * Packs shape and creates appropriate shape tag.
	 */
	function EndShape( $CharacterID )
	{
		$AlphaFlag = false;
		
		// detect alpha
		$limit = sizeof( $this->Shapes[$CharacterID] );

		foreach ( $this->Shapes[$CharacterID] as $Record ) 
		{
			if ( $Record["Alpha"] )
				$AlphaFlag = true;
		}
		
		// sort styles
		$FillStyles = array();
		$LineStyles = array();

		foreach ( $this->Shapes[$CharacterID] as $Record ) 
		{
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill0" ) && ( $Record["Definition"] != 0 ) )
				$FillStyles[] = $Record;
	
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill1" ) && ( $Record["Definition"] != 0 ) )
				$FillStyles[] = $Record;
	
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "line"  ) && ( $Record["Definition"] != 0 ) )
				$LineStyles[] = $Record;
		}
		
		// remove duplicate styles
		$limitf = sizeof( $FillStyles );
		$limitl = sizeof( $LineStyles );

		if ( $limitf > 0 ) 
		{
			$t = 0;
			$tmpFillStyles   = array();
			$tmpFillStyles[] = $FillStyles[0];
			
			for ( $counter = 0; $counter < $limitf; $counter++ ) 
			{
				$limittmp = sizeof( $tmpFillStyles );
				
				for ( $countertmp = 0; $countertmp < $limittmp; $countertmp++ ) 
				{
					if ( $FillStyles[$counter]["Definition"]["string"] == $tmpFillStyles[$countertmp]["Definition"]["string"] ) 
						$t = 1;
				}
				
				if ( $t == 0 ) 
					$tmpFillStyles[] = $FillStyles[$counter];
				else 
					$t = 0;
			}
		} 
		
		$FillStyles = $tmpFillStyles;
		
		if ( $limitl > 0 ) 
		{
			$t = 0;
			$tmpLineStyles   = array();
			$tmpLineStyles[] = $LineStyles[0];
			
			for ( $counter = 0; $counter < $limitl; $counter++ ) 
			{
				$limittmp = sizeof( $tmpLineStyles );
				
				for ( $countertmp = 0; $countertmp < $limittmp; $countertmp++ ) 
				{
					if ( $LineStyles[$counter]["Definition"]["string"] == $tmpLineStyles[$countertmp]["Definition"]["string"] ) 
						$t = 1;
				}
				
				if ( $t == 0 ) 
					$tmpLineStyles[] = $LineStyles[$counter];
				else 
					$t = 0;
			}
		} 
		
		$LineStyles = $tmpLineStyles;
		
		// check the number of styles used in this shape
		$limitf = sizeof( $FillStyles );
		$limitl = sizeof( $LineStyles );
		
		if ( $limitf > 255 )
			$LongShapeTag = true;
		
		if ( $limitl > 255 )
			$LongShapeTag = true;
		
		$upperstylelimit = pow( 2, 15 ) - 1;

		if ( $limitf > $upperstylelimit )
			return PEAR::raiseError( "EndShape: too many fill styles in this shape." );
		
		if ( $limitl > $upperstylelimit )
			return PEAR::raiseError( "EndShape: too many line styles in this shape." );
		
		// assign numbers to styles
		$Edges = array();
		
		for ( $counter = 0; $counter < $limit; $counter++ ) 
		{
			if ( ( $this->Shapes[$CharacterID][$counter]["Record"] == "changestyle" ) && ( $this->Shapes[$CharacterID][$counter]["Style"] == "fill0" ) ) 
			{ 
				$limitf = sizeof( $FillStyles );
				
				for ( $counterf = 0; $counterf < $limitf; $counterf++ ) 
				{
					if ( $this->Shapes[$CharacterID][$counter]["Definition"] == "zero" ) 
					{
						$this->Shapes[$CharacterID][$counter]["StyleID"] = 0; 
						break;
					}
					
					if ( $this->Shapes[$CharacterID][$counter]["Definition"]["string"] == $FillStyles[$counterf]["Definition"]["string"] )
					{
						$this->Shapes[$CharacterID][$counter]["StyleID"] = $counterf + 1; 
						break;
					}
				}
				
				continue;
			}
			
			if ( ( $this->Shapes[$CharacterID][$counter]["Record"] == "changestyle" ) && ( $this->Shapes[$CharacterID][$counter]["Style"] == "fill1" ) ) 
			{ 
				$limitf = sizeof( $FillStyles );
				
				for ( $counterf = 0; $counterf < $limitf; $counterf++ ) 
				{
					if ( $this->Shapes[$CharacterID][$counter]["Definition"] == "zero" ) 
					{
						$this->Shapes[$CharacterID][$counter]["StyleID"] = 0; 
						break;
					}
					
					if ( $this->Shapes[$CharacterID][$counter]["Definition"]["string"] == $FillStyles[$counterf]["Definition"]["string"] ) 
					{
						$this->Shapes[$CharacterID][$counter]["StyleID"] = $counterf + 1; 
						break;
					}
				}
				
				continue;
			}
			
			if ( ( $this->Shapes[$CharacterID][$counter]["Record"] == "changestyle" ) && ( $this->Shapes[$CharacterID][$counter]["Style"] == "line" ) ) 
			{ 
				$limitl = sizeof( $LineStyles );
				
				for ( $counterl = 0; $counterl < $limitl; $counterl++ ) 
				{
					if ( $this->Shapes[$CharacterID][$counter]["Definition"] == "zero" ) 
					{
						$this->Shapes[$CharacterID][$counter]["StyleID"] = 0; 
						break;
					}
					
					if ( $this->Shapes[$CharacterID][$counter]["Definition"]["string"] == $LineStyles[$counterl]["Definition"]["string"] ) 
					{
						$this->Shapes[$CharacterID][$counter]["StyleID"] = $counterl + 1; 
						continue;
					}
				}
				
				continue;
			}
		}
		
		// pack fill styles
		$Fills      = "";
		$Lines      = "";
		$FillStyle0 = 0;
		$FillStyle1 = 0;
		$LineStyle  = 0;
		
		if ( $limitf > 0 ) 
		{
			for ( $counter = 0; $counter < $limitf; $counter++ ) 
			{	
				// solid fill style?
				if ( $FillStyles[$counter]["Type"] == "solid" ) 
				{
					if ( $AlphaFlag )
						$Fills .= $this->packFILLSTYLE( 0x00, $FillStyles[$counter]["Definition"]["R"], $FillStyles[$counter]["Definition"]["G"], $FillStyles[$counter]["Definition"]["B"], $AlphaFlag, $FillStyles[$counter]["Definition"]["A"], null, null, null, null );
					else 
						$Fills .= $this->packFILLSTYLE( 0x00, $FillStyles[$counter]["Definition"]["R"], $FillStyles[$counter]["Definition"]["G"], $FillStyles[$counter]["Definition"]["B"], $AlphaFlag, null, null, null, null, null );
				}	
				
				// gradient fill style?
				if ( ( $FillStyles[$counter]["Type"] == "l" ) || ( $FillStyles[$counter]["Type"] == "r" ) ) 
				{
					if ( $FillStyles[$counter]["Type"] == "l" ) 
						$GradType = 0x10;
				
					if ( $FillStyles[$counter]["Type"] == "r" )
						$GradType = 0x12;
			
					$limitg = sizeof( $FillStyles[$counter]["Definition"] );
	
					for ( $counterg = 0; $counterg <= $limitg; $counterg++ ) 
					{
						if ( is_array( $FillStyles[$counter]["Definition"][$counterg] ) ) 
						{
							$GradientRecord = $FillStyles[$counter]["Definition"][$counterg];
							$Ratio = $GradientRecord["Ratio"];
							$R = $GradientRecord["R"];
							$G = $GradientRecord["G"];
							$B = $GradientRecord["B"];
			
							if ( array_key_exists( "A", $GradientRecord ) ) 
								$A = $GradientRecord["A"];
							else 
								$A = 255;
			
							$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
						}
					}
					
					$Gradient  = $this->packGRADIENT( $Gradient, $AlphaFlag );
					$Fills    .= $this->packFILLSTYLE( $GradType, null, null, null, $AlphaFlag, null, $FillStyles[$counter]["Matrix"], $Gradient, null, null );
				}	
				
				// bitmap fill style?
				if ( ( $FillStyles[$counter]["Type"] == "c" ) || ( $FillStyles[$counter]["Type"] == "t" ) ) 
				{
					if ( $FillStyles[$counter]["Type"] == "c" ) 
						$BType = 0x41;
					
					if ( $FillStyles[$counter]["Type"] == "t" ) 
						$BType = 0x40;
					
					$Fills .= $this->packFILLSTYLE( $BType, null, null, null, $AlphaFlag, null, null, null, $FillStyles[$counter]["Definition"]["ID"], $FillStyles[$counter]["Matrix"] );
				}	
			}
		}
		
		$FillStyleArray = $this->packFILLSTYLEARRAY( $limitf, $Fills ); 
		
		// pack line styles
		$Width_max = 0;
		
		if ( $limitl > 0 ) 
		{
			for ( $counter = 0; $counter < $limitl; $counter++ ) 
			{
				if ( $AlphaFlag ) 
					$Lines .= $this->packLINESTYLE( $LineStyles[$counter]["Definition"]["Width"], $LineStyles[$counter]["Definition"]["R"], $LineStyles[$counter]["Definition"]["G"], $LineStyles[$counter]["Definition"]["B"], $AlphaFlag, $LineStyles[$counter]["Definition"]["A"] );
				else 
					$Lines .= $this->packLINESTYLE( $LineStyles[$counter]["Definition"]["Width"], $LineStyles[$counter]["Definition"]["R"], $LineStyles[$counter]["Definition"]["G"], $LineStyles[$counter]["Definition"]["B"], $AlphaFlag, null );
				
				if ( $Width_max < $LineStyles[$counter]["Definition"]["Width"] ) 
					$Width_max = $LineStyles[$counter]["Definition"]["Width"];
			}
		}
		
		$LineStyleArray = $this->packLINESTYLEARRAY( $limitl, $Lines );
		
		// pack shape records
		$StateFillStyle0 = false;
		$StateStyle1     = false;
		$StateLineStyle  = false;
		$FillStyle0      = 0;
		$FillStyle1      = 0;
		$LineStyle       = 0;
		$X_min           = 2147483647;
		$X_max           = -2147483647;
		$Y_min           = 2147483647;
		$Y_max           = -2147483647;
		$X_tmp           = 0;
		$Y_tmp           = 0;

		reset( $this->Shapes[$CharacterID] );
		foreach ( $this->Shapes[$CharacterID] as $Record ) 
		{
			if ( $Record["Record"] == "moveto" ) 
			{
				$MoveToX = $Record["X"];
				$MoveToY = $this->FrameSize["Ymax"] - $Record["Y"];
				$X_tmp   = $MoveToX;
				$Y_tmp   = $MoveToY;
				
				if ( $MoveToX < $X_min ) 
				{
					$X_min = $MoveToX;
					$X_tmp = $MoveToX;
				}
				
				if ( $MoveToX > $X_max ) 
				{
					$X_max = $MoveToX;
					$X_tmp = $MoveToX;
				}
				
				if ( $MoveToY < $Y_min ) 
				{
					$Y_min = $MoveToY;
					$Y_tmp = $MoveToY;
				}
				
				if ( $MoveToY > $Y_max ) 
				{
					$Y_max = $MoveToY;
					$Y_tmp = $MoveToY;
				}
				
				$StateMoveTo     = true;
				$ChangeStyleFlag = true;
				
				continue;
			}
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill0" ) ) 
			{
				$FillStyle0      = $Record["StyleID"];
				$StateFillStyle0 = true;
				$ChangeStyleFlag = true;

				continue;
			}
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill1" ) ) 
			{
				$FillStyle1      = $Record["StyleID"];
				$StateFillStyle1 = true;
				$ChangeStyleFlag = true;
		
				continue;
			}
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "line" ) ) 
			{
				$LineStyle       = (int)$Record["StyleID"];
				$StateLineStyle  = true;
				$ChangeStyleFlag = true;
				
				continue;
			}
			
			if ( ( $Record["Record"] == "edge" ) && ( $Record["Type"] == "lineto" ) ) 
			{
				if ( $ChangeStyleFlag ) 
				{
					if ( $limitf > 0 ) 
						$nFillBits = ceil( log( $limitf + 1 ) / log( 2 ) );
					else if ( $limitf == 0 )
						$nFillBits = 0;
					
					if ( $limitl > 0 ) 
						$nLineBits = ceil( log( $limitl + 1 ) / log( 2 ) );
					else if ( $limitl == 0 ) 
						$nLineBits = 0;
					
					$StyleChangeRecord = $this->packSTYLECHANGERECORD( false, $StateLineStyle, $StateFillStyle1, $StateFillStyle0, $StateMoveTo, $MoveToX, $MoveToY, $nFillBits, $nLineBits, $FillStyle0, $FillStyle1, $LineStyle, null, null, null, null );
					$Shape .= $StyleChangeRecord["Bitstream"];		
					
					$StateMoveTo     = false;
					$StateFillStyle0 = false;
					$StateFillStyle1 = false;
					$StateLineStyle  = false;
					$ChangeStyleFlag = false;
				}
				
				if ( $Record["AnchorX"] == 0 ) 
					$Shape .= $this->packSTRAIGHTEDGERECORD( 0, 1, $Record["AnchorX"], -$Record["AnchorY"] );
				
				if ( $Record["AnchorY"] == 0 ) 
					$Shape .= $this->packSTRAIGHTEDGERECORD( 0, 0, $Record["AnchorX"], -$Record["AnchorY"] );
				
				if ( ( $Record["AnchorX"] != 0 ) && ( $Record["AnchorY"] != 0 ) ) 
					$Shape .= $this->packSTRAIGHTEDGERECORD( 1, 0, $Record["AnchorX"], -$Record["AnchorY"] );
				
				$X_tmp += $Record["AnchorX"]; 
				$Y_tmp -= $Record["AnchorY"]; 
				
				if ( $X_tmp < $X_min ) 
					$X_min = $X_tmp;
				
				if ( $X_tmp > $X_max ) 
					$X_max = $X_tmp;
				
				if ( $Y_tmp < $Y_min ) 
					$Y_min = $Y_tmp;
				
				if ( $Y_tmp > $Y_max ) 
					$Y_max = $Y_tmp;
				
				continue;
			}
			
			if ( ( $Record["Record"] == "edge" ) && ( $Record["Type"] == "curveto" ) ) 
			{
				if ( $ChangeStyleFlag ) 
				{
					if ( $limitf > 1 ) 
						$nFillBits = ceil( log( $limitf ) / log( 2 ) );
					else if ( $limitf == 1 ) 
						$nFillBits = 1;
					else if ( $limitf == 0 ) 
						$nFillBits = 0;
					
					if ( $limitl > 1 ) 
						$nLineBits = ceil( log( $limitl ) / log( 2 ) );
					else if ( $limitl == 1 ) 
						$nLineBits = 1;
					else if ( $limitf == 0 ) 
						$nFillBits = 0;
					
					$StyleChangeRecord = $this->packSTYLECHANGERECORD( false, $StateLineStyle, $StateFillStyle1, $StateFillStyle0, $StateMoveTo, $MoveToX, $MoveToY, $nFillBits, $nLineBits, $FillStyle0, $FillStyle1, $LineStyle, null, null, null, null );
					$Shape .= $StyleChangeRecord["Bitstream"];		
					
					$StateMoveTo     = false;
					$StateFillStyle0 = false;
					$StateFillStyle1 = false;
					$StateLineStyle  = false;
					$ChangeStyleFlag = false;
				}
				
				$Shape .= $this->packCURVEDEDGERECORD( $Record["ControlX"], -$Record["ControlY"], $Record["AnchorX"], -$Record["AnchorY"] );
				$X_tmp += $Record["AnchorX"]; 
				$Y_tmp += $Record["AnchorY"];
				
				if ( $X_tmp < $X_min ) 
					$X_min = $X_tmp;
				
				if ( $X_tmp > $X_max ) 
					$X_max = $X_tmp;
				
				if ( $Y_tmp < $Y_min ) 
					$Y_min = $Y_tmp;
				
				if ( $Y_tmp > $Y_max ) 
					$Y_max = $Y_tmp;
				
				$X_tmp += $Record["ControlX"]; 
				$Y_tmp += $Record["ControlY"]; 
				
				if ( $X_tmp < $X_min ) 
					$X_min = $X_tmp;
				
				if ( $X_tmp > $X_max ) 
					$X_max = $X_tmp;
				
				if ( $Y_tmp < $Y_min ) 
					$Y_min = $Y_tmp;
				
				if ( $Y_tmp > $Y_max ) 
					$Y_max = $Y_tmp;
				
				continue;
			}
		}
		
		$Shape .= $this->packENDSHAPERECORD();
		$Shape  = $this->packBitValues( $Shape );
		$X_min -= round( $Width_max / 2 );
		$X_max += round( $Width_max / 2 );
		$Y_min -= round( $Width_max / 2 );
		$Y_max += round( $Width_max / 2 );
		$Shape  = $this->packSHAPEWITHSTYLE( $FillStyleArray, $LineStyleArray, $limitf, $limitl, $Shape );
		
		if ( $AlphaFlag ) 
		{
			$this->packDefineShape3Tag( $CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $Shape );
		} 
		else 
		{
			if ( $LongShapeTag ) 
				$this->packDefineShape2Tag( $CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $Shape );
			else 
				$this->packDefineShapeTag( $CharacterID, $this->packRECT( $X_min, $X_max, $Y_min, $Y_max ), $Shape );
		}
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );
			
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}
	

	// freeform morph shape methods

	/**
	 * string BeginMorphShape(integer FromCharacterID)
	 *
	 * Returns a Character ID for the new morph shape.
	 */
	function BeginMorphShape( $FromCharacterID )
	{
		$CharacterID = "t $FromCharacterID";

		// return the CharacterInfo array for this shape
		return $CharacterID;
	}
	
	/**
	 * null EndMorphShape(integer FromCharacterID,string ToCharacterID)
	 *
	 * Packs shape and creates appropriate shape tag.
	 */
	function EndMorphShape( $FromCharacterID, $ToCharacterID )
	{
		$AlphaFlag = true;
		
		// sort -from- styles
		$f_FillStyles = array();
		$f_LineStyles = array();

		foreach( $this->Shapes[$FromCharacterID] as $Record ) 
		{
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill0" ) && ( $Record["Definition"] != 0 ) ) 
				$f_FillStyles[] = $Record;
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill1" ) && ( $Record["Definition"] != 0 ) ) 
				$f_FillStyles[] = $Record;
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "line"  ) && ( $Record["Definition"] != 0 ) ) 
				$f_LineStyles[] = $Record;
		}
		
		// sort -to- styles
		$t_FillStyles = array();
		$t_LineStyles = array();

		foreach ( $this->Shapes[$ToCharacterID] as $Record ) 
		{
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill0" ) && ( $Record["Definition"] != 0 ) ) 
				$t_FillStyles[] = $Record;
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill1" ) && ( $Record["Definition"] != 0 ) ) 
				$t_FillStyles[] = $Record;
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "line"  ) && ( $Record["Definition"] != 0 ) ) 
				$t_LineStyles[] = $Record;
		}
		
		$f_limitf = sizeof( $f_FillStyles );
		$f_limitl = sizeof( $f_LineStyles );
		$t_limitf = sizeof( $t_FillStyles );
		$t_limitl = sizeof( $t_LineStyles );
		
		if ( $f_limitf != $t_limitf )
			return PEAR::raiseError( "EndMorphShape: the number of fill styles in -from- and -to- shapes is not equal." );
		
		if ( $f_limitl != $t_limitl )
			return PEAR::raiseError( "EndMorphShape: the number of line styles in -from- and -to- shapes is not equal." );
		
		// remove duplicate -from- styles
		if ( $f_limitf > 0 ) 
		{
			$t = 0;
			$tmpFillStyles   = array();
			$tmpFillStyles[] = $f_FillStyles[0];
			
			for ( $counter = 0; $counter < $f_limitf; $counter++ ) 
			{
				$limittmp = sizeof( $tmpFillStyles );
				
				for ( $countertmp = 0; $countertmp < $limittmp; $countertmp++ ) 
				{
					if ( $f_FillStyles[$counter]["Definition"]["string"] == $tmpFillStyles[$countertmp]["Definition"]["string"] ) 
						$t = 1;
				}
				
				if ( $t == 0 ) 
					$tmpFillStyles[] = $f_FillStyles[$counter];
				else 
					$t = 0;
			}
		} 
		
		$f_FillStyles = $tmpFillStyles;
		
		if ( $f_limitl > 0 ) 
		{
			$t = 0;
			$tmpLineStyles   = array();
			$tmpLineStyles[] = $f_LineStyles[0];

			for ( $counter = 0; $counter < $f_limitl; $counter++ ) 
			{
				$limittmp = sizeof( $tmpLineStyles );
				
				for ( $countertmp = 0; $countertmp < $limittmp; $countertmp++ ) 
				{
					if ( $f_LineStyles[$counter]["Definition"]["string"] == $tmpLineStyles[$countertmp]["Definition"]["string"] ) 
						$t = 1;
				}
				
				if ( $t == 0 ) 
					$tmpLineStyles[] = $f_LineStyles[$counter];
				else 
					$t = 0;
			}
		} 
		
		$f_LineStyles = $tmpLineStyles;
		
		// remove duplicate -to- styles
		if ( $t_limitf > 0 ) 
		{
			$t = 0;
			$tmpFillStyles   = array();
			$tmpFillStyles[] = $t_FillStyles[0];

			for ( $counter = 0; $counter < $t_limitf; $counter++ ) 
			{
				$limittmp = sizeof( $tmpFillStyles );
				
				for ( $countertmp = 0; $countertmp < $limittmp; $countertmp++ ) 
				{
					if ( $t_FillStyles[$counter]["Definition"]["string"] == $tmpFillStyles[$countertmp]["Definition"]["string"] ) 
						$t = 1;
				}
				
				if ( $t == 0 ) 
					$tmpFillStyles[] = $t_FillStyles[$counter];
				else 
					$t = 0;
			}
		}
		
		$t_FillStyles = $tmpFillStyles;
		
		if ( $t_limitl > 0 ) 
		{
			$t = 0;
			$tmpLineStyles   = array();
			$tmpLineStyles[] = $t_LineStyles[0];
			
			for ( $counter = 0; $counter < $t_limitl; $counter++ ) 
			{
				$limittmp = sizeof( $tmpLineStyles );
				
				for ( $countertmp = 0; $countertmp < $limittmp; $countertmp++ ) 
				{
					if ( $t_LineStyles[$counter]["Definition"]["string"] == $tmpLineStyles[$countertmp]["Definition"]["string"] ) 
						$t = 1;
				}
				
				if ( $t == 0 ) 
					$tmpLineStyles[] = $t_LineStyles[$counter];
				else 
					$t = 0;
			}
		} 
		
		$t_LineStyles = $tmpLineStyles;
		$f_limitf = sizeof( $f_FillStyles );
		$f_limitl = sizeof( $f_LineStyles );
		$t_limitf = sizeof( $t_FillStyles );
		$t_limitl = sizeof( $t_LineStyles );
		
		if ( $f_limitf != $t_limitf ) 
			return PEAR::raiseError( "EndMorphShape: the reduced number of fill styles in -from- and -to- shapes is not equal." );
		
		if ( $f_limitl != $t_limitl )
			return PEAR::raiseError( "EndMorphShape: the reduced number of line styles in -from- and -to- shapes is not equal." );
		
		$upperstylelimit = pow( 2, 15 ) - 1;
		
		// check the number of styles used in the -from- shape
		if ( $f_limitf > 255 )
			$LongShapeTag = true;
		
		if ( $f_limitl > 255 )
			$LongShapeTag = true;
		
		if ( $f_limitf > $upperstylelimit )
			return PEAR::raiseError( "EndMorphShape: too many fill styles in this shape." );
		
		if ( $f_limitl > $upperstylelimit )
			return PEAR::raiseError( "EndMorphShape: too many line styles in this shape." );
		
		// check the number of styles used in the -to- shape
		if ( $t_limitf > 255 )
			$LongShapeTag = true;
		
		if ( $t_limitl > 255 )
			$LongShapeTag = true;
		
		if ( $t_limitf > $upperstylelimit )
			return PEAR::raiseError( "EndMorphShape: too many fill styles in this shape." );
		
		if ( $t_limitl > $upperstylelimit )
			return PEAR::raiseError( "EndMorphShape: too many line styles in this shape." );
		
		// assign numbers to styles
		$limit = sizeof( $this->Shapes[$FromCharacterID] );
		
		for ( $counter = 0; $counter < $limit; $counter++ ) 
		{
			if ( ( $this->Shapes[$FromCharacterID][$counter]["Record"] == "changestyle" ) && ( $this->Shapes[$FromCharacterID][$counter]["Style"] == "fill0" ) ) 
			{ 
				$limitf = sizeof( $f_FillStyles );
				
				for ( $counterf = 0; $counterf < $limitf; $counterf++ ) 
				{
					if ( $this->Shapes[$FromCharacterID][$counter]["Definition"] == "zero" ) 
					{
						$this->Shapes[$FromCharacterID][$counter]["StyleID"] = 0; 
						$this->Shapes[$ToCharacterID][$counter]["StyleID"]   = 0; 
		
						break;
					}
					
					if ( $this->Shapes[$FromCharacterID][$counter]["Definition"]["string"] == $f_FillStyles[$counterf]["Definition"]["string"] ) 
					{
						$this->Shapes[$FromCharacterID][$counter]["StyleID"] = $counterf + 1; 
						$this->Shapes[$ToCharacterID][$counter]["StyleID"]   = $counterf + 1; 
		
						break;
					}
				}
				
				continue;
			}
			
			if ( ( $this->Shapes[$FromCharacterID][$counter]["Record"] == "changestyle" ) && ( $this->Shapes[$FromCharacterID][$counter]["Style"] == "fill1" ) ) 
			{ 
				$limitf = sizeof( $f_FillStyles );
				
				for ( $counterf = 0; $counterf < $limitf; $counterf++ ) 
				{
					if ( $this->Shapes[$FromCharacterID][$counter]["Definition"] == "zero" ) 
					{
						$this->Shapes[$FromCharacterID][$counter]["StyleID"] = 0; 
						$this->Shapes[$ToCharacterID][$counter]["StyleID"]   = 0; 
		
						break;
					}
					
					if ( $this->Shapes[$FromCharacterID][$counter]["Definition"]["string"] == $f_FillStyles[$counterf]["Definition"]["string"] ) 
					{
						$this->Shapes[$FromCharacterID][$counter]["StyleID"] = $counterf + 1; 
						$this->Shapes[$ToCharacterID][$counter]["StyleID"]   = $counterf + 1; 
		
						break;
					}
				}
				
				continue;
			}
			
			if ( ( $this->Shapes[$FromCharacterID][$counter]["Record"] == "changestyle" ) && ( $this->Shapes[$FromCharacterID][$counter]["Style"] == "line" ) ) 
			{ 
				$limitl = sizeof( $f_LineStyles );
				
				for ( $counterl = 0; $counterl < $limitl; $counterl++ ) 
				{
					if ( $this->Shapes[$FromCharacterID][$counter]["Definition"] == "zero" ) 
					{
						$this->Shapes[$FromCharacterID][$counter]["StyleID"] = 0; 
						$this->Shapes[$ToCharacterID][$counter]["StyleID"]   = 0; 
		
						break;
					}

					if ( $this->Shapes[$FromCharacterID][$counter]["Definition"]["string"] == $f_LineStyles[$counterl]["Definition"]["string"] ) 
					{
						$this->Shapes[$FromCharacterID][$counter]["StyleID"] = $counterl + 1; 
						$this->Shapes[$ToCharacterID][$counter]["StyleID"]   = $counterl + 1; 
		
						continue;
					}
				}
				
				continue;
			}
		}
		
		// pack fill styles
		$Fills      = "";
		$Lines      = "";
		$FillStyle0 = 0;
		$FillStyle1 = 0;
		$LineStyle  = 0;
		
		if ( $f_limitf > 0 ) 
		{
			for ( $counter = 0; $counter < $f_limitf; $counter++ ) 
			{	
				// solid fill style?
				if ( $f_FillStyles[$counter]["Type"] == "solid" ) 
				{
					if ( $t_FillStyles[$counter]["Type"] != "solid" ) 
						return PEAR::raiseError( "EndMorphShape: -to- fill style not solid." );
					
					if ( array_key_exists( "A", $f_FillStyles[$counter]["Definition"] ) ) 
						$Fills .= $this->packFILLSTYLE( 0x00, $f_FillStyles[$counter]["Definition"]["R"], $f_FillStyles[$counter]["Definition"]["G"], $f_FillStyles[$counter]["Definition"]["B"], $AlphaFlag, $f_FillStyles[$counter]["Definition"]["A"], null, null, null, null );
					else 
						$Fills .= $this->packFILLSTYLE( 0x00, $f_FillStyles[$counter]["Definition"]["R"], $f_FillStyles[$counter]["Definition"]["G"], $f_FillStyles[$counter]["Definition"]["B"], $AlphaFlag, 255, null, null, null, null );
					
					if ( array_key_exists( "A", $t_FillStyles[$counter]["Definition"] ) ) 
						$Fills .= substr( $this->packFILLSTYLE( 0x00, $t_FillStyles[$counter]["Definition"]["R"], $t_FillStyles[$counter]["Definition"]["G"], $t_FillStyles[$counter]["Definition"]["B"], $AlphaFlag, $t_FillStyles[$counter]["Definition"]["A"], null, null, null, null ), 1 );
					else 
						$Fills .= substr( $this->packFILLSTYLE( 0x00, $t_FillStyles[$counter]["Definition"]["R"], $t_FillStyles[$counter]["Definition"]["G"], $t_FillStyles[$counter]["Definition"]["B"], $AlphaFlag, 255, null, null, null, null ), 1 );
				}	
				
				// gradient fill style?
				if ( ( $f_FillStyles[$counter]["Type"] == "l" ) || ( $f_FillStyles[$counter]["Type"] == "r" ) ) 
				{
					if ( !( ( $t_FillStyles[$counter]["Type"] == "l" ) || ( $t_FillStyles[$counter]["Type"] == "r" ) ) ) 
						return PEAR::raiseError( "EndMorphShape: -to- fill style not a gradient." );
					
					if ( $f_FillStyles[$counter]["Type"] == "l" ) 
					{
						if ( $t_FillStyles[$counter]["Type"] != "l" ) 
							return PEAR::raiseError( "EndMorphShape: -to- gradient not linear." );
		
						$GradType = 0x10;
					}
				
					if ( $f_FillStyles[$counter]["Type"] == "r" ) 
					{
						if ( $t_FillStyles[$counter]["Type"] != "r" ) 
							return PEAR::raiseError( "EndMorphShape: -to- gradient not radial." );
		
						$GradType = 0x12;
					}
			
					$limitg = sizeof( $f_FillStyles[$counter]["Definition"] );
	
					for ( $counterg = 0; $counterg <= $limitg; $counterg++ ) 
					{
						$GradientRecord = $f_FillStyles[$counter]["Definition"][$counterg];
						$Ratio = $GradientRecord["Ratio"];
						$R = $GradientRecord["R"];
						$G = $GradientRecord["G"];
						$B = $GradientRecord["B"];
		
						if ( array_key_exists( "A", $GradientRecord ) ) 
							$A = $GradientRecord["A"];
						else 
							$A = 255;
		
						$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
					}
					
					$Gradient  = $this->packGRADIENT( $Gradient, $AlphaFlag );
					$Fills    .= $this->packFILLSTYLE( $GradType, null, null, null, $AlphaFlag, null, $f_FillStyles[$counter]["Matrix"], $Gradient, null, null );
					
					for ( $counterg = 0; $counterg <= $limitg; $counterg++ ) 
					{
						$GradientRecord = $t_FillStyles[$counter]["Definition"][$counterg];
						$Ratio = $GradientRecord["Ratio"];
						$R = $GradientRecord["R"];
						$G = $GradientRecord["G"];
						$B = $GradientRecord["B"];
		
						if ( array_key_exists( "A", $GradientRecord ) ) 
							$A = $GradientRecord["A"];
						else 
							$A = 255;
						
						$Gradient .= $this->packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A );
					}
					
					$Gradient  = $this->packGRADIENT( $Gradient, $AlphaFlag );
					$Fills    .= substr( $this->packFILLSTYLE( $GradType, null, null, null, $AlphaFlag, null, $t_FillStyles[$counter]["Matrix"], $Gradient, null, null), 1 );
				}	
				
				// bitmap fill style?
				if ( ( $f_FillStyles[$counter]["Type"] == "c" ) || ( $f_FillStyles[$counter]["Type"] == "t" ) ) 
				{
					if ( !( ( $t_FillStyles[$counter]["Type"] == "c" ) || ( $t_FillStyles[$counter]["Type"] == "t" ) ) ) 
						return PEAR::raiseError( "EndMorphShape: -to- fill style not a bitmap." );
					
					if ( $f_FillStyles[$counter]["Type"] == "c" ) 
					{
						if ( $t_FillStyles[$counter]["Type"] != "c" ) 
							return PEAR::raiseError( "EndMorphShape: -to- bitmap not clipped.");

						$BType = 0x41;
					}
					
					if ( $f_FillStyles[$counter]["Type"] == "t" ) 
					{
						if ( $t_FillStyles[$counter]["Type"] != "t" ) 
							return PEAR::raiseError( "EndMorphShape: -to- bitmap not tiled.");
					
						$BType = 0x40;
					}
					
					$Fills .= $this->packFILLSTYLE( $BType, null, null, null, $AlphaFlag, null, null, null, $f_FillStyles[$counter]["Definition"]["ID"], $f_FillStyles[$counter]["Matrix"] );
					$Fills .= substr( $this->packFILLSTYLE( $BType, null, null, null, $AlphaFlag, null, null, null, $t_FillStyles[$counter]["Definition"]["ID"], $t_FillStyles[$counter]["Matrix"] ), 2 );
				}	
			}
		}
		
		$FillStyleArray = $this->packFILLSTYLEARRAY( $f_limitf, $Fills ); 
		
		// pack line styles
		$f_Width_max = 0;
		$t_Width_max = 0;
		
		if ( $f_limitl > 0 ) 
		{
			for ( $counter = 0; $counter < $f_limitl; $counter++ ) 
			{
				if ( ( array_key_exists( "A", $f_LineStyles[$counter]["Definition"] ) ) || ( array_key_exists( "A", $t_LineStyles[$counter]["Definition"] ) ) ) 
				{
					$f_tmpLines  = $this->packLINESTYLE( $f_LineStyles[$counter]["Definition"]["Width"], $f_LineStyles[$counter]["Definition"]["R"], $f_LineStyles[$counter]["Definition"]["G"], $f_LineStyles[$counter]["Definition"]["B"], $AlphaFlag, $f_LineStyles[$counter]["Definition"]["A"] );
					$t_tmpLines  = $this->packLINESTYLE( $t_LineStyles[$counter]["Definition"]["Width"], $t_LineStyles[$counter]["Definition"]["R"], $t_LineStyles[$counter]["Definition"]["G"], $t_LineStyles[$counter]["Definition"]["B"], $AlphaFlag, 255 );
					$Lines      .= substr( $f_tmpLines, 0, 2 ) . substr( $t_tmpLines, 0, 2 ) . substr( $f_tmpLines, 2 ) . substr( $t_tmpLines, 2 ); 
				} 
				else 
				{
					$f_tmpLines  = $this->packLINESTYLE( $f_LineStyles[$counter]["Definition"]["Width"], $f_LineStyles[$counter]["Definition"]["R"], $f_LineStyles[$counter]["Definition"]["G"], $f_LineStyles[$counter]["Definition"]["B"], $AlphaFlag, $f_LineStyles[$counter]["Definition"]["A"] );
					$t_tmpLines  = $this->packLINESTYLE( $t_LineStyles[$counter]["Definition"]["Width"], $t_LineStyles[$counter]["Definition"]["R"], $t_LineStyles[$counter]["Definition"]["G"], $t_LineStyles[$counter]["Definition"]["B"], $AlphaFlag, 255 );
					$Lines      .= substr( $f_tmpLines, 0, 2 ) . substr( $t_tmpLines, 0, 2 ) . substr( $f_tmpLines, 2 ) . substr( $t_tmpLines, 2 ); 
				}
				
				if ( $f_Width_max < $f_LineStyles[$counter]["Definition"]["Width"] ) 
					$f_Width_max = $f_LineStyles[$counter]["Definition"]["Width"];
				
				if ( $t_Width_max < $t_LineStyles[$counter]["Definition"]["Width"] ) 
					$t_Width_max = $t_LineStyles[$counter]["Definition"]["Width"];
			}
		}
		
		$LineStyleArray = $this->packLINESTYLEARRAY( $f_limitl, $Lines );
		
		// pack -from- shape records
		$StateFillStyle0 = false;
		$StateStyle1     = false;
		$StateLineStyle  = false;
		$FillStyle0      = 0;
		$FillStyle1      = 0;
		$LineStyle       = 0;
		$X_min           = 2147483647;
		$X_max           = -2147483647;
		$Y_min           = 2147483647;
		$Y_max           = -2147483647;
		$X_tmp           = 0;
		$Y_tmp           = 0;
		
		reset( $this->Shapes[$FromCharacterID] );
		foreach ( $this->Shapes[$FromCharacterID] as $Record ) 
		{
			if ( $Record["Record"] == "moveto" ) 
			{
				$MoveToX = $Record["X"];
				$MoveToY = $this->FrameSize["Ymax"] - $Record["Y"];
				$X_tmp   = $MoveToX;
				$Y_tmp   = $MoveToY;
				
				if ( $MoveToX < $X_min ) 
				{
					$X_min = $MoveToX;
					$X_tmp = $MoveToX;
				}
				
				if ( $MoveToX > $X_max ) 
				{
					$X_max = $MoveToX;
					$X_tmp = $MoveToX;
				}
				
				if ( $MoveToY < $Y_min ) 
				{
					$Y_min = $MoveToY;
					$Y_tmp = $MoveToY;
				}
				
				if ( $MoveToY > $Y_max ) 
				{
					$Y_max = $MoveToY;
					$Y_tmp = $MoveToY;
				}
				
				$StateMoveTo     = true;
				$ChangeStyleFlag = true;

				continue;
			}
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill0" ) ) 
			{
				$FillStyle0      = (int)$Record["StyleID"];
				$StateFillStyle0 = true;
				$ChangeStyleFlag = true;

				continue;
			}
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "fill1" ) ) 
			{
				$FillStyle1      = (int) $Record["StyleID"];
				$StateFillStyle1 = true;
				$ChangeStyleFlag = true;

				continue;
			}
			
			if ( ( $Record["Record"] == "changestyle" ) && ( $Record["Style"] == "line" ) ) 
			{
				$LineStyle       = (int)$Record["StyleID"];
				$StateLineStyle  = true;
				$ChangeStyleFlag = true;
				
				continue;
			}
			
			if ( ( $Record["Record"] == "edge" ) && ( $Record["Type"] == "lineto" ) ) 
			{
				if ( $ChangeStyleFlag ) 
				{
					if ( $f_limitf > 0 ) 
						$nFillBits = ceil( log( $f_limitf + 1 ) / log( 2 ) );
					else if ( $f_limitf == 0 ) 
						$nFillBits = 1;
					
					if ( $f_limitl > 0 ) 
						$nLineBits = ceil( log( $f_limitl + 1 ) / log( 2 ) );
					else if ( $f_limitl == 0 ) 
						$nLineBits = 1;

					$StyleChangeRecord = $this->packSTYLECHANGERECORD( false, $StateLineStyle, $StateFillStyle1, $StateFillStyle0, $StateMoveTo, $MoveToX, $MoveToY, $nFillBits, $nLineBits, $FillStyle0, $FillStyle1, $LineStyle, null, null, null, null );
					$f_Shape .= $StyleChangeRecord["Bitstream"];		
					
					$StateMoveTo     = false;
					$StateFillStyle0 = false;
					$StateFillStyle1 = false;
					$StateLineStyle  = false;
					$ChangeStyleFlag = false;
				}
				
				if ( $Record["AnchorX"] == 0 ) 
					$f_Shape .= $this->packSTRAIGHTEDGERECORD( 0, 1, $Record["AnchorX"], -$Record["AnchorY"] );
				
				if ( $Record["AnchorY"] == 0 ) 
					$f_Shape .= $this->packSTRAIGHTEDGERECORD( 0, 0, $Record["AnchorX"], -$Record["AnchorY"] );
				
				if ( ( $Record["AnchorX"] != 0 ) && ( $Record["AnchorY"] != 0 ) ) 
					$f_Shape .= $this->packSTRAIGHTEDGERECORD( 1, 0, $Record["AnchorX"], -$Record["AnchorY"] );
				
				$X_tmp += $Record["AnchorX"]; 
				$Y_tmp -= $Record["AnchorY"]; 
				
				if ( $X_tmp < $X_min ) 
					$X_min = $X_tmp;
				
				if ( $X_tmp > $X_max ) 
					$X_max = $X_tmp;
				
				if ( $Y_tmp < $Y_min ) 
					$Y_min = $Y_tmp;

				if ( $Y_tmp > $Y_max ) 
					$Y_max = $Y_tmp;
				
				continue;
			}
			
			if ( ( $Record["Record"] == "edge" ) && ( $Record["Type"] == "curveto" ) ) 
			{
				if ( $ChangeStyleFlag ) 
				{
					if ( $f_limitf > 1 ) 
						$nFillBits = ceil( log( $f_limitf ) / log( 2 ) );
					else if ( $f_limitf == 1 ) 
						$nFillBits = 1;
					else if ( $f_limitf == 0 ) 
						$nFillBits = 0;
					
					if ( $f_limitl > 1 ) 
						$nLineBits = ceil( log( $f_limitl ) / log( 2 ) );
					else if ( $f_limitl == 1 ) 
						$nLineBits = 1;
					else if ( $f_limitf == 0 ) 
						$nFillBits = 0;
					
					$StyleChangeRecord = $this->packSTYLECHANGERECORD( false, $StateLineStyle, $StateFillStyle1, $StateFillStyle0, $StateMoveTo, $MoveToX, $MoveToY, $nFillBits, $nLineBits, $FillStyle0, $FillStyle1, $LineStyle, null, null, null, null );
					$f_Shape .= $StyleChangeRecord["Bitstream"];		
					
					$StateMoveTo     = false;
					$StateFillStyle0 = false;
					$StateFillStyle1 = false;
					$StateLineStyle  = false;
					$ChangeStyleFlag = false;
				}
				
				$f_Shape .= $this->packCURVEDEDGERECORD( $Record["ControlX"], -$Record["ControlY"], $Record["AnchorX"], -$Record["AnchorY"] );
				$X_tmp   += $Record["AnchorX"]; 
				$Y_tmp   += $Record["AnchorY"];
	
				if ( $X_tmp < $X_min ) 
					$X_min = $X_tmp;
				
				if ( $X_tmp > $X_max ) 
					$X_max = $X_tmp;
				
				if ( $Y_tmp < $Y_min ) 
					$Y_min = $Y_tmp;
				
				if ( $Y_tmp > $Y_max ) 
					$Y_max = $Y_tmp;
				
				$X_tmp += $Record["ControlX"]; 
				$Y_tmp += $Record["ControlY"]; 
				
				if ( $X_tmp < $X_min ) 
					$X_min = $X_tmp;
				
				if ( $X_tmp > $X_max ) 
					$X_max = $X_tmp;
			
				if ( $Y_tmp < $Y_min ) 
					$Y_min = $Y_tmp;
				
				if ( $Y_tmp > $Y_max ) 
					$Y_max = $Y_tmp;
				
				continue;
			}
		}
		
		$f_Shape .= $this->packENDSHAPERECORD();
		$f_Shape  = $this->packBitValues( $f_Shape );
		
		if ( $f_limitf > 0 ) 
			$nFillBits = ceil( log( $f_limitf + 1 ) / log( 2 ) );
		else if ( $f_limitf == 0 ) 
			$nFillBits = 0;
		
		if ( $f_limitl > 0 )
			$nLineBits = ceil( log( $f_limitl + 1 ) / log( 2 ) );
		else if ( $f_limitl == 0 ) 
			$nLineBits = 0;
		
		$f_Shape = $this->packSHAPE( $nFillBits, $nLineBits, $f_Shape );
		$f_X_min = $X_min - round( $f_Width_max / 2 );
		$f_X_max = $X_max + round( $f_Width_max / 2 );
		$f_Y_min = $Y_min - round( $f_Width_max / 2 );
		$f_Y_max = $Y_max + round( $f_Width_max / 2 );
		
		// pack -to- shape records
		$StateFillStyle0 = false;
		$StateStyle1     = false;
		$StateLineStyle  = false;
		$FillStyle0      = 0;
		$FillStyle1      = 0;
		$LineStyle       = 0;
		$X_min           = 2147483647;
		$X_max           = -2147483647;
		$Y_min           = 2147483647;
		$Y_max           = -2147483647;
		$X_tmp           = 0;
		$Y_tmp           = 0;
		
		reset( $this->Shapes[$ToCharacterID] );
		foreach ( $this->Shapes[$ToCharacterID] as $Record ) 
		{
			if ( $Record["Record"] == "moveto" ) 
			{
				$MoveToX = $Record["X"];
				$MoveToY = $this->FrameSize["Ymax"] - $Record["Y"];
				$X_tmp   = $MoveToX;
				$Y_tmp   = $MoveToY;
				
				if ( $MoveToX < $X_min ) 
				{
					$X_min = $MoveToX;
					$X_tmp = $MoveToX;
				}
				
				if ( $MoveToX > $X_max ) 
				{
					$X_max = $MoveToX;
					$X_tmp = $MoveToX;
				}
				
				if ( $MoveToY < $Y_min ) 
				{
					$Y_min = $MoveToY;
					$Y_tmp = $MoveToY;
				}
				
				if ( $MoveToY > $Y_max ) 
				{
					$Y_max = $MoveToY;
					$Y_tmp = $MoveToY;
				}
				
				$StateMoveTo     = true;
				$ChangeStyleFlag = false;

				continue;
			}
			
			if ( ( $Record["Record"] == "edge" ) && ( $Record["Type"] == "lineto" ) ) 
			{
				if ( $StateMoveTo ) 
				{
					$StyleChangeRecord = $this->packSTYLECHANGERECORD( false, false, false, false, $StateMoveTo, $MoveToX, $MoveToY, null, null, null, null, null, null, null, null, null );
					$t_Shape .= $StyleChangeRecord["Bitstream"];		
					$StateMoveTo = false;
				}
				
				if ( $Record["AnchorX"] == 0 )
					$t_Shape .= $this->packSTRAIGHTEDGERECORD( 0, 1, $Record["AnchorX"], -$Record["AnchorY"] );
		
				if ( $Record["AnchorY"] == 0 )
					$t_Shape .= $this->packSTRAIGHTEDGERECORD( 0, 0, $Record["AnchorX"], -$Record["AnchorY"] );
		
				if ( ( $Record["AnchorX"] != 0 ) && ( $Record["AnchorY"] != 0 ) )
					$t_Shape .= $this->packSTRAIGHTEDGERECORD( 1, 0, $Record["AnchorX"], -$Record["AnchorY"] );
		
				$X_tmp += $Record["AnchorX"]; 
				$Y_tmp -= $Record["AnchorY"]; 
		
				if ( $X_tmp < $X_min )
					$X_min = $X_tmp;
		
				if ( $X_tmp > $X_max )
					$X_max = $X_tmp;
		
				if ( $Y_tmp < $Y_min )
					$Y_min = $Y_tmp;
		
				if ( $Y_tmp > $Y_max )
					$Y_max = $Y_tmp;
		
				continue;
			}
			
			if ( ( $Record["Record"] == "edge" ) && ( $Record["Type"] == "curveto" ) ) 
			{
				if ( $StateMoveTo ) 
				{
					$StyleChangeRecord = $this->packSTYLECHANGERECORD( false, false, false, false, $StateMoveTo, $MoveToX, $MoveToY, null, null, null, null, null, null, null, null, null );
					$t_Shape .= $StyleChangeRecord["Bitstream"];		
					$StateMoveTo = false;
				}
				
				$t_Shape .= $this->packCURVEDEDGERECORD( $Record["ControlX"], -$Record["ControlY"], $Record["AnchorX"], -$Record["AnchorY"] );
				$X_tmp   += $Record["AnchorX"]; 
				$Y_tmp   += $Record["AnchorY"];
				
				if ( $X_tmp < $X_min )
					$X_min = $X_tmp;
		
				if ( $X_tmp > $X_max )
					$X_max = $X_tmp;
		
				if ( $Y_tmp < $Y_min )
					$Y_min = $Y_tmp;
		
				if ( $Y_tmp > $Y_max )
					$Y_max = $Y_tmp;
		
				$X_tmp += $Record["ControlX"]; 
				$Y_tmp += $Record["ControlY"]; 
				
				if ( $X_tmp < $X_min ) 
					$X_min = $X_tmp;
		
				if ( $X_tmp > $X_max )
					$X_max = $X_tmp;
		
				if ( $Y_tmp < $Y_min )
					$Y_min = $Y_tmp;
		
				if ( $Y_tmp > $Y_max ) 
					$Y_max = $Y_tmp;
		
				continue;
			}
		}
		
		$t_Shape .= $this->packENDSHAPERECORD();
		$t_Shape  = $this->packBitValues( $t_Shape );
		
		if ( $t_limitf > 0 ) 
			$nFillBits = ceil( log( $t_limitf + 1 ) / log( 2 ) );
		else if ( $t_limitf == 0 ) 
			$nFillBits = 0;
		
		if ( $t_limitl > 0 ) 
			$nLineBits = ceil( log( $t_limitl + 1 ) / log( 2 ) );
		else if ( $t_limitl == 0 ) 
			$nLineBits = 0;
		
		$t_Shape = $this->packSHAPE( $nFillBits, $nLineBits, $t_Shape );
		$t_X_min = $X_min - round( $t_Width_max / 2 );
		$t_X_max = $X_max + round( $t_Width_max / 2 );
		$t_Y_min = $Y_min - round( $t_Width_max / 2 );
		$t_Y_max = $Y_max + round( $t_Width_max / 2 );
		
		$this->packDefineMorphShapeTag( $FromCharacterID, $this->packRECT( $f_X_min, $f_X_max, $f_Y_min, $f_Y_max ), $this->packRECT( $t_X_min, $t_X_max, $t_Y_min, $t_Y_max ), $FillStyleArray, $LineStyleArray, $f_Shape, $t_Shape );
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $FromCharacterID, "f_X_min" => $f_X_min, "f_X_max" => $f_X_max, "f_Y_min" => $f_Y_min, "f_Y_max" => $f_Y_max, "t_X_min" => $t_X_min, "t_X_max" => $t_X_max, "t_Y_min" => $t_Y_min, "t_Y_max" => $t_Y_max );
	
		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}

	
	// line styles
	
	/**
	 * integer DashedLine(integer LineLength, 
	 *   			integer SpaceLength, integer X1, integer Y1,
	 *          	integer X2, integer Y2, integer Width, 
	 *           	array LineStyle, array FillStyle, 
	 *              string FillType, string FillMatrix)
	 *
	 * Cretes a dashed line with flat ends.
	 */
	function DashedLine( $LineLength, $SpaceLength, $X1, $Y1, $X2, $Y2, $Width, $LineStyle, $FillStyle, $FillType, $FillMatrix )
	{
		$CharacterID = $this->BeginShape();
		
		if ( PEAR::isError( $CharacterID ) )
			return $CharacterID;
			 
		$this->SelectLineStyle( $CharacterID, $LineStyle );
		$this->SelectFill1Style( $CharacterID, $FillStyle, $FillType, $FillMatrix );
		
		$totallength = sqrt( pow( $X2 - $X1, 2 ) + pow( $Y2 - $Y1, 2 ) );
		$tmplength   = $totallength;
		$line        = true;
		$store       = array();
		
		while ( true ) 
		{
			if ( $line ) 
			{
				if ( $tmplength >= $LineLength ) 
				{
					$store[]    = $LineLength;
					$tmplength -= $LineLength;
					$line       = false;
				} 
				else 
				{
					$store[] = round( $tmplength );
					break;
				}
			} 
			else 
			{
				if ( $tmplength >= $SpaceLength ) 
				{
					$store[]    = $SpaceLength;
					$tmplength -= $SpaceLength;
					$line       = true;
				} 
				else 
				{
					$store[] = round( $tmplength );
					break;
				}
			}
		}
		
		$Alpha  = asin( ( $Y2 - $Y1 ) / $totallength );
		$Width2 = $Width / 2;
		$XF     = $X1;
		$YF     = $Y1;
		$line   = true;
		$limit  = sizeof( $store );
		
		for ( $counter = 0; $counter < $limit; $counter++ ) 
		{
			$XDA = $Width * sin( $Alpha );
			$YDA = $Width * cos( $Alpha );
			$XDB = $store[$counter] * cos( $Alpha );
			$YDB = $store[$counter] * sin( $Alpha );
			
			if ( $line ) 
			{
				$this->MoveTo( $CharacterID,   $XF + 0.5 * $XDA, $YF - 0.5 * $YDA );
				$this->LineTo( $CharacterID, -$XDA,  $YDA );
				$this->LineTo( $CharacterID,  $XDB,  $YDB );
				$this->LineTo( $CharacterID,  $XDA, -$YDA );
				$this->LineTo( $CharacterID, -$XDB, -$YDB );

				$line = false;
			} 
			else 
			{
				$line = true;
			}
 
			$XF += $XDB;
			$YF += $YDB;
		}
		
		$res = $this->EndShape( $CharacterID );
		
		if ( PEAR::isError( $res ) )
			return $res;
		
		// create the CharacterInfo array for this shape
		$CharacterInfo = array( "CharacterID" => $CharacterID, "X_min" => $X_min, "X_max" => $X_max, "Y_min" => $Y_min, "Y_max" => $Y_max );

		// return the CharacterInfo array for this shape
		return $CharacterInfo;
	}


	// basic types

   	/**
	 * string packUI8(integer number)
	 *
	 * Converts the given 8-bit unsigned integer number into an SWF UI8 atom.
	 */
	function packUI8( $number )
	{
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packUI8 argument $number not a number." );
		
		$number      = (int)$number;
		$lower_limit = 0;
		$upper_limit = 255;
		
		if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
		
		$atom = chr( $number );
		return $atom;
	}

   	/**
	 * string packUI16(integer number)
	 *
	 * Converts the given 16-bit unsigned integer into an SWF UI16 atom.
	 */
	function packUI16( $number )
	{
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packUI16 argument not a number." );
        	
		$number      = (int)$number;
		$lower_limit = 0;
		$upper_limit = 65535;
		
		if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
		
		$number    = sprintf( "%04x", $number );
		$low_byte  = base_convert( substr( $number, 2, 2 ), 16, 10 );
		$high_byte = base_convert( substr( $number, 0, 2 ), 16, 10 );
		$atom      = chr( $low_byte ) . chr( $high_byte );
		
		return $atom;
	}

   	/**
	 * string packUI32(integer number)
	 *
	 * Converts the given unsigned integer into an SWF UI32 atom and returns it.
	 */
	function packUI32( $number )
	{	
		if ( !( is_integer( $number ) ) ) 
			return PEAR::raiseError( "packUI32 argument not an integer." );
        	
		$lower_limit = 0;
		$upper_limit = 2147483647; // the real limit is 4294967295 but PHP 4 cannot handle such large unsigned integers 
	  
	  	if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
        	
		$number               = sprintf( "%08x", $number );
		$low_byte_low_word    = base_convert( substr( $number, 6, 2 ), 16, 10 );
		$high_byte_low_word   = base_convert( substr( $number, 4, 2 ), 16, 10 ); 
		$low_byte_high_word   = base_convert( substr( $number, 2, 2 ), 16, 10 );
		$high_byte_high_word  = base_convert( substr( $number, 0, 2 ), 16, 10 );
		$atom                 = chr( $low_byte_low_word  ) . chr( $high_byte_low_word  );
		$atom                .= chr( $low_byte_high_word ) . chr( $high_byte_high_word );

		return $atom;
	}

   	/**
	 * string packSI8(integer number)
	 *
	 * Converts the given 8-bit signed integer into an SWF SI8 atom.
	 */
	function packSI8( $number )
	{
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packSI8 argument not a number." );
		
		$number      = (int)$number;
		$lower_limit = -127;
		$upper_limit = 127;
		
		if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
		
		if ( $number < 0 )
			$number = $upper_limit + 1 + abs( $number );
		
		$atom = chr( $number );
		return $atom;
	}

   	/**
	 * string packSI16(integer number)
	 *
	 * Converts the given 16-bit signed integer into an SWF SI16 atom.
	 */
	function packSI16( $number )
	{
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packSI16 argument not a number." );
		
		$number      = (int)$number;
		$lower_limit = -32767;
		$upper_limit = 32767;
		
		if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
		
		if ( $number < 0 )
			$number = $upper_limit + 1 + abs( $number );
		
		$number    = sprintf( "%04x", $number );
		$low_byte  = base_convert( substr( $number, 2, 2 ), 16, 10 );
		$high_byte = base_convert( substr( $number, 0, 2 ), 16, 10 );
		$atom      = chr( $low_byte ) . chr( $high_byte );
		
		return $atom;
	}

   	/**
	 * string packSI32(integer number)
	 *
	 * Converts the given 32-bit signed integer into an SWF SI32 atom.
	 */
	function packSI32( $number )
	{	
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packUI32 argument not a number." );
        	
		$lower_limit = -2147483647;
		$upper_limit = 2147483647;
		
		if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
		
		if ( $number < 0 )
			$number = $upper_limit + 1 + abs( $number );
		
		$number               = sprintf( "%08x", $number );
		$low_byte_low_word    = base_convert( substr( $number, 6, 2 ), 16, 10 );
		$high_byte_low_word   = base_convert( substr( $number, 4, 2 ), 16, 10 ); 
		$low_byte_high_word   = base_convert( substr( $number, 2, 2 ), 16, 10 );
		$high_byte_high_word  = base_convert( substr( $number, 0, 2 ), 16, 10 );
		$atom                 = chr( $low_byte_low_word  ) . chr( $high_byte_low_word  );
		$atom                .= chr( $low_byte_high_word ) . chr( $high_byte_high_word );
		        	
		return $atom;
	}

   	/**
	 * string packFIXED8(float number)
	 *
	 * Converts the given signed floating-point number into an SWF FIXED 8.8 atom.
	 */
	function packFIXED8( $number )
	{
		$lower_limit_high_byte = -127;
		$upper_limit_high_byte = 127;
		$lower_limit_low_byte  = 0;
		$upper_limit_low_byte  = 99;
		
	   	if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packFIXED8 argument not a number." );
        	
		$number    = round( $number, 2 );
		$high_byte = intval( $number );
		
		if ( $high_byte < $lower_limit_high_byte )
			$high_byte = $lower_limit_high_byte;
		
		if ( $high_byte > $upper_limit_high_byte )
			$high_byte = $upper_limit_high_byte;
		
		$low_byte = (int)( ( abs( $number ) - intval( abs( $number ) ) ) * 100 );
		$atom  = $this->packUI8( intval( $low_byte * 256 / 100 ) );
		$atom .= $this->packSI8( $high_byte );
				
		return $atom;
	}

   	/**
	 * string packFIXED16(float number)
	 *
	 * Converts the given signed floating-point number into an SWF FIXED 16.16 atom.
	 */
	function packFIXED16( $number )
	{
		$lower_limit_high_word = -32767;
		$upper_limit_high_word = 32767;
		$lower_limit_low_word  = 0;
		$upper_limit_low_word  = 9999;
		
	   	if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packFIXED16 argument not a number." );
        	
		$number    = round( $number, 4 );
		$high_word = intval( $number );
		
		if ( $high_word < $lower_limit_high_word )
			$high_word = $lower_limit_high_word;
		
		if ( $high_word > $upper_limit_high_word )
			$high_word = $upper_limit_high_word;
		
		$low_word = (int)( ( abs( $number ) - intval( abs( $number ) ) ) * 10000 );
		$atom  = $this->packUI16( intval( $low_word * 65536 / 10000 ) );
		$atom .= $this->packSI16( $high_word );
				
		return $atom;
	}

	/**
	 * string packUBchunk(integer number)
	 *
	 * Converts the given 31-bit unsigned integer number into an SWF UB atom.
	 */
	function packUBchunk( $number )
	{
		$lower_limit = 0;
		$upper_limit = 2147483647;
		
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packUBchunk argument not a number." );
		
		if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
		
		$atom = sprintf( "%b", $number );
		return $atom;
	}
	
	/**
	 * string packSBchunk(integer number)
	 *
	 * Converts the given 31-bit signed integer number into an SWF SB atom.
	 */
	function packSBchunk( $number )
	{
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packSBchunk argument not a number." );
		
		$number      = (int)$number;
		$lower_limit = -1073741823;
		$upper_limit = 1073741823;
		
		if ( $number < $lower_limit )
			$number = $lower_limit;
		
		if ( $number > $upper_limit )
			$number = $upper_limit;
		
		if ( $number < 0 ) 
		{
			if ( $number == -1 ) 
			{
				$atom = "11";
			} 
			else 
			{
				$atom = decbin( $number );
				$atom = substr( $atom, strpos( $atom, "10" ) );
			}
		} 
		else 
		{
			$atom = "0" . decbin( $number );
		}
		
		return $atom;
	}
	
	/** 
	 * string packFBchunk(float number)
	 *
	 * Converts the given signed floating-point number into an SWF FB atom.
	 */
	function packFBchunk( $number )
	{
		$lower_limit_high_word = -16383;
		$upper_limit_high_word = 16383;
		$lower_limit_low_word  = 0;
		$upper_limit_low_word  = 9999;
		
	 	if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packFBchunk argument not a number." );
        	
		$number    = round( $number, 4 );
		$high_word = intval( $number );
		$low_word  = (int)( ( abs( $number ) - intval( abs( $number ) ) ) * 10000 );
		
		if ( $high_word < $lower_limit_high_word )
			$high_word = $lower_limit_high_word;
		
		if ( $high_word > $upper_limit_high_word )
			$high_word = $upper_limit_high_word;
		
		if ( $low_word < $lower_limit_low_word )
			$low_word = $lower_limit_low_word;
		
		if ( $low_word > $upper_limit_low_word )
			$low_word = $upper_limit_low_word;
		
		if ( $number < 0 ) 
		{
			if ( $high_word == 0 ) 
				$high_word = "1";
			else 
				$high_word = "1" . substr( decbin( $high_word ), 18 );
		} 
		else 
		{
			if ( $high_word == 0 ) 
				$high_word = "0";
			else 
				$high_word = "0" . decbin( $high_word );
		}
		
		if ( $number < 0 ) 
		{
			if ( $low_word == 0 ) 
			{
				$low_word = "0000000000000000";
			} 
			else 
			{
				$low_word = ~$low_word;
				$low_word = substr( decbin( intval( $low_word * 65536 / 10000 ) ), 16 );
			}
		} 
		else 
		{
			if ( $low_word == 0 ) 
				$low_word = "0000000000000000";
			else 
				$low_word = sprintf( "%016s", decbin( intval( $low_word * 65536 / 10000 ) ) );
		}
		
		$atom = $high_word . $low_word;
		return $atom;
	}

	/**
	 * string packnBits(integer number, integer n)
	 *
	 * Converts the given unsigned integer number (the 
	 * length of the largest bit field) into an SWF n bits 
	 * long nBits atom.
	 */
	function packnBits( $number, $n )
	{
		if ( !( is_numeric( $number ) ) )
			return PEAR::raiseError( "packnBits argument (number) not a number." );
        	
		$number      = (int) $number;
		$lower_limit = 0;
		$upper_limit = 32;
		
		if ( ( $number < $lower_limit ) || ( $number > $upper_limit ) ) 
			return PEAR::raiseError( "packnBits argument (number) out of range." );
		
		if ( !( is_numeric( $n ) ) )
			return PEAR::raiseError( "packnBits argument (n) not a number." );
        	
		if ( $n < $lower_limit )
			return PEAR::raiseError( "packnBits argument (n) out of range." );
        	
		$n = (int)$n;
		
		if ( $number > ( pow( 2, $n ) - 1 ) )
			return PEAR::raiseError( "packnBits cannot pack ($number) in ($n) bits." );
		
		$atom = sprintf( "%b", $number );
		$atom = str_repeat( "0", ( $n - strlen( $atom ) ) ) . $atom;
		
		return $atom;
	}
	
	/**
	 * string packBitValues(string atoms)
	 *
	 * Converts the given string of SWF bit values.
	 * (atoms) into a byte-aligned stream. 
	 */
	function packBitValues( $atoms )
	{
		if ( !( is_string( $atoms ) ) )
			return PEAR::raiseError( "packBitValues argument not a string." );
		
		$atoms = $atoms . str_repeat( "0", (int)( ( ceil( strlen( $atoms ) / 8 ) ) * 8 - strlen( $atoms ) ) );
		$limit = ceil( strlen( $atoms ) / 8);
		$bytestream = "";
		
		for ( $n = 0; $n < $limit; $n++ ) 
		{
			$bytestream .= chr( base_convert( substr( $atoms, 0, 8 ), 2, 10 ) );
			$atoms = substr( $atoms, 8 );
		}
		
		return $bytestream;
	}
	
	/**
	 * string packSTRING(string text)
	 *
	 * Converts the given text string into an SWF STRING atom. 
	 */
	function packSTRING( $text )
	{
		if ( !( is_string( $text ) ) )
			return PEAR::raiseError( "packSTRING argument not a string." );
		
		$atom = strtr( $text, chr( 0 ), "" ) . chr( 0 );
		return $atom;
	}
	
	/**
	 * string packRECT(integer Xmin, integer Xmax, integer Ymin, integer Ymax)
	 * 
	 * Returns an SWF RECT bit value (atom) string.
	 */
	function packRECT( $Xmin, $Xmax, $Ymin, $Ymax )
	{
		if ( ! ( ( $Xmin == 0 ) && ( $Xmax == 0 ) && ( $Ymin == 0 ) && ( $Ymax == 0 ) ) ) 
		{
			$Xmin  = $this->packSBchunk( $Xmin ); 
			$Xmax  = $this->packSBchunk( $Xmax ); 
			$Ymin  = $this->packSBchunk( $Ymin ); 
			$Ymax  = $this->packSBchunk( $Ymax );
			$nBits = (int)max( strlen( $Xmin ), strlen( $Xmax ), strlen( $Ymin ), strlen( $Ymax ) );
			$Xmin  = str_repeat( substr( $Xmin, 0, 1 ), $nBits - strlen( $Xmin ) ) . $Xmin;
			$Xmax  = str_repeat( substr( $Xmax, 0, 1 ), $nBits - strlen( $Xmax ) ) . $Xmax;
			$Ymin  = str_repeat( substr( $Ymin, 0, 1 ), $nBits - strlen( $Ymin ) ) . $Ymin;
			$Ymax  = str_repeat( substr( $Ymax, 0, 1 ), $nBits - strlen( $Ymax ) ) . $Ymax;
			$atom  = $this->packnBits( $nBits, 5 ) . $Xmin . $Xmax . $Ymin . $Ymax;
		} 
		else 
		{
			$atom = "00000";
		}
		
		$atom = $this->packBitValues( $atom );
		return $atom;
	}
	
	/**
	 * string packRGB(integer R, integer G, integer B)
	 *
	 * Returns an SWF RGB atom string.
	 */
	function packRGB( $R, $G, $B )
	{
		$atom  = $this->packUI8( $R );
		$atom .= $this->packUI8( $G );
		$atom .= $this->packUI8( $B );
		
		return $atom;
	}
	
	/**
	 * string packRGBA(integer R, integer G, integer B, integer A)
	 *
	 * Returns an SWF RGBA atom string.
	 */
	function packRGBA( $R, $G, $B, $A )
	{
		$atom  = $this->packUI8( $R );
		$atom .= $this->packUI8( $G );
		$atom .= $this->packUI8( $B );
		$atom .= $this->packUI8( $A );
		
		return $atom;
	}
	
	/**
	 * string packMATRIX(boolean HasScale, 
	 *				float ScaleX, float ScaleY,boolean HasRotate, 
	 *				float RotateSkew0, float RotateSkew1,
	 *				integer TranslateX, integer TranslateY)
	 * 
	 * Returns an SWF MATRIX atom string.
	 */
	function packMATRIX( $HasScale, $ScaleX, $ScaleY, $HasRotate, $RotateSkew0, $RotateSkew1, $TranslateX, $TranslateY )
	{
		$atom = "";
		
		if ( $HasScale ) 
		{
			$ScaleX     = $this->packFBchunk( $ScaleX );
			$ScaleY     = $this->packFBchunk( $ScaleY );
			$nScaleBits = (int)max( strlen( $ScaleX ), strlen( $ScaleY ) );
			$ScaleX     = str_repeat( substr( $ScaleX, 0, 1 ), ( $nScaleBits - strlen( $ScaleX ) ) ) . $ScaleX;
			$ScaleY     = str_repeat( substr( $ScaleY, 0, 1 ), ( $nScaleBits - strlen( $ScaleY ) ) ) . $ScaleY;
			$atom       = "1" . $this->packnBits( $nScaleBits, 5 ) . $ScaleX . $ScaleY;
		} 
		else 
		{
			$atom = "0";
		}
		
		if ( $HasRotate ) 
		{
			$RotateSkew0  = $this->packFBchunk( $RotateSkew0 );
			$RotateSkew1  = $this->packFBchunk( $RotateSkew1 );
			$nRotateBits  = (int)max( strlen( $RotateSkew0 ), strlen( $RotateSkew1 ) );
			$RotateSkew0  = str_repeat( substr( $RotateSkew0, 0, 1 ), $nRotateBits - strlen( $RotateSkew0 ) ) . $RotateSkew0;
			$RotateSkew1  = str_repeat( substr( $RotateSkew1, 0, 1 ), $nRotateBits - strlen( $RotateSkew1 ) ) . $RotateSkew1;
			$atom        .= "1" . $this->packnBits( $nRotateBits, 5 ) . $RotateSkew0 . $RotateSkew1;
		} 
		else 
		{
			$atom .= "0";
		}
		
		if ( ( $TranslateX == 0 ) && ( $TranslateY == 0 ) ) 
		{
			$atom .= "00000";
		} 
		else 
		{
			$TranslateX      = $this->packSBchunk( $TranslateX ); 
			$TranslateY      = $this->packSBchunk( $TranslateY );
			$nTranslateBits  = (int)max( strlen( $TranslateX ), strlen( $TranslateY ) );
			$TranslateX      = str_repeat( substr( $TranslateX, 0, 1 ), $nTranslateBits - strlen( $TranslateX ) ) . $TranslateX;
			$TranslateY      = str_repeat( substr( $TranslateY, 0, 1 ), $nTranslateBits - strlen( $TranslateY ) ) . $TranslateY;
			$atom           .= $this->packnBits( $nTranslateBits, 5 ) . $TranslateX . $TranslateY;
		}
		
		$atom  = $this->packBitValues( $atom );
		return $atom;
	}
	
	/**
	 * string packCXFORM(boolean HasAddTerms, 
	 *				integer RedAddTerm, integer GreenAddTerm,
	 *				integer BlueAddTerm, boolean HasMultTerms, 
	 *				integer RedMultTerm, integer GreenMultTerm,
	 *				integer BlueMultTerm)
	 *
	 * Returns an SWF CXFORM atom string.
	 */
	function packCXFORM( $HasAddTerms, $RedAddTerm, $GreenAddTerm, $BlueAddTerm, $HasMultTerms, $RedMultTerm, $GreenMultTerm, $BlueMultTerm )
	{
		if ( $HasAddTerms ) 
		{
			$RedAddTerm   = $this->packSBchunk( $RedAddTerm   );
			$GreenAddTerm = $this->packSBchunk( $GreenAddTerm );
			$BlueAddTerm  = $this->packSBchunk( $BlueAddTerm  );

			$atom = "1";
		} 
		else 
		{
			$atom = "0";
		}
		
		if ( $HasMultTerms ) 
		{
			$RedMultTerm   = $this->packSBchunk( $RedMultTerm   );
			$GreenMultTerm = $this->packSBchunk( $GreenMultTerm );
			$BlueMultTerm  = $this->packSBchunk( $BlueMultTerm  );

			$atom .= "1";
		} 
		else 
		{
			$atom .= "0";
		}
		
		if ( !( ( $HasAddTerms == 0 ) && ( $HasMultTerms == 0 ) ) ) 
		{
			$nBits          = (int)max( strlen( $RedMultTerm ), strlen( $GreenMultTerm ), strlen( $BlueMultTerm ), strlen( $RedAddTerm ), strlen( $GreenAddTerm ), strlen( $BlueAddTerm ) );
			$RedAddTerm     = str_repeat( substr( $RedAddTerm,    0, 1 ), $nBits - strlen( $RedAddTerm    ) ) . $RedAddTerm;
			$GreenAddTerm   = str_repeat( substr( $GreenAddTerm,  0, 1 ), $nBits - strlen( $GreenAddTerm  ) ) . $GreenAddTerm;
			$BlueAddTerm    = str_repeat( substr( $BlueAddTerm,   0, 1 ), $nBits - strlen( $BlueAddTerm   ) ) . $BlueAddTerm;
			$RedMultTerm    = str_repeat( substr( $RedMultTerm,   0, 1 ), $nBits - strlen( $RedMultTerm   ) ) . $RedMultTerm;
			$GreenMultTerm  = str_repeat( substr( $GreenMultTerm, 0, 1 ), $nBits - strlen( $GreenMultTerm ) ) . $GreenMultTerm;
			$BlueMultTerm   = str_repeat( substr( $BlueMultTerm,  0, 1 ), $nBits - strlen( $BlueMultTerm  ) ) . $BlueMultTerm;
			$atom          .= $this->packnBits( $nBits, 5 );
			
			if ( $HasMultTerms )
				$atom .= $RedMultTerm . $GreenMultTerm . $BlueMultTerm;
	
			if ( $HasAddTerms )
				$atom .= $RedAddTerm . $GreenAddTerm . $BlueAddTerm;
		}
		
		$atom = $this->packBitValues( $atom );
		return $atom;
	}
	
	/**
	 * string packCXFORMWITHALPHA(boolean HasAddTerms, 
	 *				integer RedAddTerm, integer GreenAddTerm,
	 *				integer BlueAddTerm, integer AlphaAddTerm,
	 *				boolean HasMultTerms, integer RedMultTerm, 
	 *				integer GreenMultTerm, integer BlueMultTerm,
	 *				integer AlphaMultTerm)
	 *
	 * Returns an SWF CXFORMWITHALPHA atom string.
	 */
	function packCXFORMWITHALPHA( $HasAddTerms, $RedAddTerm, $GreenAddTerm, $BlueAddTerm, $AlphaAddTerm, $HasMultTerms, $RedMultTerm, $GreenMultTerm, $BlueMultTerm, $AlphaMultTerm )
	{
		if ( $HasAddTerms ) 
		{
			$RedAddTerm   = $this->packSBchunk( $RedAddTerm   );
			$GreenAddTerm = $this->packSBchunk( $GreenAddTerm );
			$BlueAddTerm  = $this->packSBchunk( $BlueAddTerm  );
			$AlphaAddTerm = $this->packSBchunk( $AlphaAddTerm );
			
			$atom = "1";
		} 
		else 
		{
			$atom = "0";
		}
		
		if ( $HasMultTerms ) 
		{
			$RedMultTerm   = $this->packSBchunk( $RedMultTerm   );
			$GreenMultTerm = $this->packSBchunk( $GreenMultTerm );
			$BlueMultTerm  = $this->packSBchunk( $BlueMultTerm  );
			$AlphaMultTerm = $this->packSBchunk( $AlphaMultTerm );

			$atom .= "1";
		} 
		else 
		{
			$atom .= "0";
		}
		
		if ( !( ( $HasAddTerms == 0 ) && ( $HasMultTerms == 0 ) ) ) 
		{
			$nBits          = (int)max( strlen( $RedMultTerm ), strlen( $GreenMultTerm ), strlen( $BlueMultTerm ), strlen( $AlphaMultTerm ), strlen( $RedAddTerm ), strlen( $GreenAddTerm ), strlen( $BlueAddTerm ), strlen( $AlphaAddTerm ) );
			$RedAddTerm     = str_repeat( substr( $RedAddTerm,    0, 1 ), $nBits - strlen( $RedAddTerm    ) ) . $RedAddTerm;
			$GreenAddTerm   = str_repeat( substr( $GreenAddTerm,  0, 1 ), $nBits - strlen( $GreenAddTerm  ) ) . $GreenAddTerm;
			$BlueAddTerm    = str_repeat( substr( $BlueAddTerm,   0, 1 ), $nBits - strlen( $BlueAddTerm   ) ) . $BlueAddTerm;
			$AlphaAddTerm   = str_repeat( substr( $AlphaAddTerm,  0, 1 ), $nBits - strlen( $AlphaAddTerm  ) ) . $AlphaAddTerm;
			$RedMultTerm    = str_repeat( substr( $RedMultTerm,   0, 1 ), $nBits - strlen( $RedMultTerm   ) ) . $RedMultTerm;
			$GreenMultTerm  = str_repeat( substr( $GreenMultTerm, 0, 1 ), $nBits - strlen( $GreenMultTerm ) ) . $GreenMultTerm;
			$BlueMultTerm   = str_repeat( substr( $BlueMultTerm,  0, 1 ), $nBits - strlen( $BlueMultTerm  ) ) . $BlueMultTerm;
			$AlphaMultTerm  = str_repeat( substr( $AlphaMultTerm, 0, 1 ), $nBits - strlen( $AlphaMultTerm ) ) . $AlphaMultTerm;
			$atom          .= $this->packnBits( $nBits, 5 );
			
			if ( $HasMultTerms == "1" )
				$atom .= $RedMultTerm . $GreenMultTerm . $BlueMultTerm . $AlphaMultTerm;
	
			if ( $HasAddTerms == "1" )
				$atom .= $RedAddTerm . $GreenAddTerm . $BlueAddTerm . $AlphaAddTerm;
		}
		
		$atom = $this->packBitValues( $atom );
		return $atom;
	}

	
	// compound data types
	
	/**
	 * string packZLIBBITMAPDATA(string ColorTableRGB, string BitmapPixelData)
	 *
	 * Returns an SWF ZLIBBITMAPDATA string.
	 */
	function packZLIBBITMAPDATA( $ColorTableRGB, $BitmapPixelData )
	{
		$atom = $ColorTableRGB . $BitmapPixelData;
		return $atom;
	}
	
	/**
	 * string packZLIBBITMAPDATA2(string ColorTableRGBA, string BitmapPixelData)
	 *
	 * Returns an SWF ZLIBBITMAPDATA2 string.
	 */
	function packZLIBBITMAPDATA2( $ColorTableRGBA, $BitmapPixelData )
	{
		$atom = $ColorTableRGBA . $BitmapPixelData;
		return $atom;
	}
	
	/**
	 * string packGRADRECORD(string ShapeTag, integer Ratio, integer R, integer G, integer B, boolean AlphaFlag, integer A )
	 *
	 * Returns an SWF GRADRECORD string.
	 */
	function packGRADRECORD( $Ratio, $R, $G, $B, $AlphaFlag, $A )
	{
		if ( $AlphaFlag )
			$atom = $this->packUI8( (int)$Ratio) . $this->packRGBA( (int)$R, (int)$G, (int)$B, (int)$A );
		else
			$atom = $this->packUI8( (int)$Ratio) . $this->packRGB( (int)$R, (int)$G, (int)$B );
				
		return $atom;
	}
	
	/**
	 * string packGRADIENT(string GradientRecords) 
	 *
	 * Returns an SWF GRADIENT string.
	 */
	function packGRADIENT( $GradientRecords, $AlphaFlag )
	{
		if ( $AlphaFlag )
			$atom = $this->packUI8(  (int)strlen( $GradientRecords ) / 5 ) . $GradientRecords;
		else
			$atom  = $this->packUI8( (int)strlen( $GradientRecords ) / 4 ) . $GradientRecords;
		
		return $atom;
	}
	
	/**
	 * string packFILLSTYLE(int FillStyleType, int R, 
	 *				int G, int B, int A, string GradientMatrix,
	 *				string Gradient, integer BitmapID,
	 *				string BitmapMatrix)
	 *
	 * Returns an SWF FILLSTYLE string.
	 */
	function packFILLSTYLE( $FillStyleType, $R, $G, $B, $AlphaFlag, $A, $GradientMatrix, $Gradient, $BitmapID, $BitmapMatrix )
	{
		switch ( $FillStyleType ) 
		{
			case 0x00:
				if ( $AlphaFlag ) 
				{
					if ( $A == "" ) 
						$A = 0xff;
					
					$atom .= $this->packRGBA( $R, $G, $B, $A );
				} 
				else 
				{
					$atom .= $this->packRGB( $R, $G, $B );
				}
				
				break;
			
			case 0x10:
				$atom .= $GradientMatrix . $Gradient;
				break;
			
			case 0x12:
				$atom .= $GradientMatrix . $Gradient;
				break;
			
			case 0x40:
				$atom .= $this->packUI16( $BitmapID ) . $BitmapMatrix;
				break;
			
			case 0x41:
				$atom .= $this->packUI16( $BitmapID ) . $BitmapMatrix;
				break;
			
			default:
				return PEAR::raiseError( "packFILLSTYLE unknown FillStyleType value." );
		}
		
		$atom  = $this->packUI8( $FillStyleType ) . $atom;
		return $atom;
	}
	
	/**
	 * string packFILLSTYLEARRAY(string ShapeTag, integer FillStyleCount, string FillStyles)
	 *
	 * Returns an SWF FILLSTYLEARRAY string.
	 */
	function packFILLSTYLEARRAY( $FillStyleCount, $FillStyles )
	{
		if ( $FillStyleCount < 0xff ) 
			$atom = $this->packUI8( $FillStyleCount );
		else
			$atom = chr( 0xff ) . $this->packUI16( $FillStyleCount );
		
		$atom .= $FillStyles; 
		return $atom;
	}
 
	/**
	 * string packLINESTYLE(integer Width, integer R, integer G, integer B, integer A)
	 *
	 * Returns an SWF LINESTYLE string.
	 */
	function packLINESTYLE( $Width, $R, $G, $B, $AlphaFlag, $A )
	{
		$atom  = $this->packUI16( $Width );

		if ( $AlphaFlag ) 
		{
			if ( $A == "" ) 
				$A = 0xff;
			
			$atom  .= $this->packRGBA( $R, $G, $B, $A );
		} 
		else 
		{
			$atom  .= $this->packRGB( $R, $G, $B );
		}
		
		return $atom;
	}
	
	/**
	 * string packLINESTYLEARRAY(int LineStyleCount, string LineStyles)
	 *
	 * Returns an SWF LINESTYLEARRAY string.
	 */
	function packLINESTYLEARRAY( $LineStyleCount, $LineStyles )
	{
		if ( $LineStyleCount < 0xff ) 
			$atom  = $this->packUI8( $LineStyleCount );
		else 
			$atom .= char( 0xff ) . $this->packUI16( $LineStyleCount ); // char ???
		
		$atom .= $LineStyles; 
		return $atom;
	}
 
	/**
	 * string packCURVEDEDGERECORD(
	 *				integer ControlDeltaX, integer ControlDeltaY,
	 *				integer AnchorDeltaX, integer AnchorDeltaY)
	 *
	 * Returns an SWF CURVEDEDGERECORD string.
	 */
	function packCURVEDEDGERECORD( $ControlDeltaX, $ControlDeltaY, $AnchorDeltaX, $AnchorDeltaY )
	{
		$TypeFlag      = "1";
		$StraightEdge  = "0";
		$ControlDeltaX = $this->packSBchunk( $ControlDeltaX ); 
		$ControlDeltaY = $this->packSBchunk( $ControlDeltaY );
		$AnchorDeltaX  = $this->packSBchunk( $AnchorDeltaX  ); 
		$AnchorDeltaY  = $this->packSBchunk( $AnchorDeltaY  );
		$NumBits       = (int)max( strlen( $ControlDeltaX ), strlen( $ControlDeltaY ), strlen( $AnchorDeltaX ), strlen( $AnchorDeltaY ) );
		$nBits         = $this->packnBits( $NumBits - 2, 4 );
		$ControlDeltaX = str_repeat( substr( $ControlDeltaX, 0, 1 ), ( $NumBits - strlen( $ControlDeltaX ) ) ) . $ControlDeltaX;
		$ControlDeltaY = str_repeat( substr( $ControlDeltaY, 0, 1 ), ( $NumBits - strlen( $ControlDeltaY ) ) ) . $ControlDeltaY;
		$AnchorDeltaX  = str_repeat( substr( $AnchorDeltaX,  0, 1 ), ( $NumBits - strlen( $AnchorDeltaX  ) ) ) . $AnchorDeltaX;
		$AnchorDeltaY  = str_repeat( substr( $AnchorDeltaY,  0, 1 ), ( $NumBits - strlen( $AnchorDeltaY  ) ) ) . $AnchorDeltaY;
		$atom          = $TypeFlag . $StraightEdge . $nBits . $ControlDeltaX . $ControlDeltaY . $AnchorDeltaX  . $AnchorDeltaY;
				
		return $atom;
	}
	
	/**
	 * string packSTRAIGHTEDGERECORD(
	 * 	 			boolean GeneralLineFlag, boolean VertLineFlag,
	 *				integer DeltaX, integer DeltaY)
	 *
	 * Returns an SWF STRAIGHTEDGERECORD string.
	 */
	function packSTRAIGHTEDGERECORD( $GeneralLineFlag, $VertLineFlag, $DeltaX, $DeltaY )
	{
		$TypeFlag = "1";
		$StraightEdge = "1";
		
		if ( ( $DeltaX == 0 ) && ( $DeltaY == 0 ) ) 
		{
			$atom = sprintf( "%1d", $TypeFlag ) . sprintf( "%1d", $StraightEdge ) . "0" ;
		} 
		else 
		{
			$DeltaX  = $this->packSBchunk( $DeltaX ); 
			$DeltaY  = $this->packSBchunk( $DeltaY );
			$NumBits = (int)max( strlen( $DeltaX ), strlen( $DeltaY ) );
			$nBits   = $this->packnBits( ( $NumBits - 2 ), 4 );
			$DeltaX  = str_repeat( substr( $DeltaX, 0, 1 ), ( $NumBits - strlen( $DeltaX ) ) ) . $DeltaX;
			$DeltaY  = str_repeat( substr( $DeltaY, 0, 1 ), ( $NumBits - strlen( $DeltaY ) ) ) . $DeltaY;
			$atom    = sprintf( "%1d", $TypeFlag ) . sprintf( "%1d", $StraightEdge ) . $nBits . sprintf( "%1d", $GeneralLineFlag );
			
			if ( $GeneralLineFlag ) 
			{
				$atom .= $DeltaX . $DeltaY;
			} 
			else 
			{
				if ( $VertLineFlag )
					$atom .= sprintf( "%1d", $VertLineFlag ) . $DeltaY; 
				else
					$atom .= sprintf( "%1d", $VertLineFlag ) . $DeltaX; 
			}
		}
				
		return $atom;
	}
	
	/**
	 * string packSTYLECHANGERECORD(
	 *				integer StateNewStyles,
	 *				integer StateLineStyle, 
	 *				integer StateFillStyle1,
	 *				integer StateFillStyle0, integer StateMoveTo,
	 *				integer MoveDeltaX, integer MoveDeltaY, 
	 *				integer nFillBits, integer nLineBits, 
	 *				integer FillStyle0, integer FillStyle1,
	 *				integer LineStyle, string FillStyles,
	 *				string LineStyles, integer NumFillBits,
	 *				integer NumLineBits)
	 *
	 * Returns an SWF STYLECHANGERECORD string.
	 */
	function packSTYLECHANGERECORD( $StateNewStyles, $StateLineStyle, $StateFillStyle1, $StateFillStyle0, $StateMoveTo, $MoveDeltaX, $MoveDeltaY, $nFillBits, $nLineBits, $FillStyle0, $FillStyle1, $LineStyle, $FillStyles, $LineStyles, $NumFillBits, $NumLineBits )
	{
		$atom = array(
			"Bitstream"  => "", 
			"Bytestream" => ""
		);
		
		$atom["Bitstream"] .= "0";
		$atom["Bitstream"] .= sprintf( "%1d", $StateNewStyles  );
		$atom["Bitstream"] .= sprintf( "%1d", $StateLineStyle  );
		$atom["Bitstream"] .= sprintf( "%1d", $StateFillStyle1 );
		$atom["Bitstream"] .= sprintf( "%1d", $StateFillStyle0 );
		$atom["Bitstream"] .= sprintf( "%1d", $StateMoveTo     );

		if ( $StateMoveTo == 1 ) 
		{
			$MoveDeltaX = $this->packSBchunk( $MoveDeltaX ); 
			$MoveDeltaY = $this->packSBchunk( $MoveDeltaY );
			$MoveBits   = (int)max( strlen( $MoveDeltaX ), strlen( $MoveDeltaY ) );
			$nMoveBits  = $this->packnBits( $MoveBits, 5 );
			$MoveDeltaX = str_repeat( substr( $MoveDeltaX, 0, 1 ), ( $MoveBits - strlen( $MoveDeltaX ) ) ) . $MoveDeltaX;
			$MoveDeltaY = str_repeat( substr( $MoveDeltaY, 0, 1 ), ( $MoveBits - strlen( $MoveDeltaY ) ) ) . $MoveDeltaY;
			$atom["Bitstream"] .= $nMoveBits . $MoveDeltaX . $MoveDeltaY;
		}
		
		if ( $StateFillStyle0 ) 
			$atom["Bitstream"] .= $this->packnBits( $FillStyle0, $nFillBits );
		
		if ( $StateFillStyle1 )
			$atom["Bitstream"] .= $this->packnBits( $FillStyle1, $nFillBits );
		
		if ( $StateLineStyle )
			$atom["Bitstream"] .= $this->packnBits( $LineStyle, $nLineBits );
		
		if ( $StateNewStyles ) 
			$atom["Bytestream"] = $FillStyles . $LineStyles . $this->packUI8( (int)( $this->packnBits( $NumFillBits, 4 ) . $this->packnBits( $NumLineBits, 4 ) ) );
				
		return $atom;
	}
	
	/**
	 * string packENDSHAPERECORD()
	 *
	 * Returns an SWF ENDSHAPERECORD string.
	 */
	function packENDSHAPERECORD()
	{
		$TypeFlag   = "0";
		$EndOfShape = "00000";
		$atom       = $TypeFlag . $EndOfShape;
				
		return $atom;
	}
	
	/**
	 * string packSHAPEWITHSTYLE(string FillStyles, 
	 *         		string LineStyles, integer NumFillBits, 
	 *				integer NumLineBits, string ShapeRecords)
	 * 
	 * Returns an SWF SHAPEWITHSTYLE string.
	 */
	function packSHAPEWITHSTYLE( $FillStyles, $LineStyles, $NumFillBits, $NumLineBits, $ShapeRecords )
	{
		$lower_limit = 0;
		$upper_limit = 15;
		
		if ( ( $NumFillBits < $lower_limit ) || ( $NumFillBits > $upper_limit ) )
			return PEAR::raiseError( "packSHAPEWITHSTYLE argument (NumFillBits) out of range." );
		
		if ( ( $NumLineBits < $lower_limit ) || ( $NumLineBits > $upper_limit ) )
			return PEAR::raiseError( "packSHAPEWITHSTYLE argument (NumLineBits) out of range." );
		
		$atom  = $FillStyles;
		$atom .= $LineStyles;
		$NumFillBits = $this->packnBits( $NumFillBits, 4 );
		$NumLineBits = $this->packnBits( $NumLineBits, 4 );
		$atom .= $this->packBitValues( $NumFillBits . $NumLineBits );
		$atom .= $ShapeRecords;
				
		return $atom;
	}
	
	/**
	 * string packSHAPE(integer NumFillBits, integer NumLineBits, string ShapeRecords)
	 *
	 * Returns an SWF SHAPE string.
	 */
	function packSHAPE( $NumFillBits, $NumLineBits, $ShapeRecords )
	{
		$lower_limit = 0;
		$upper_limit = 15;
		
		if ( ( $NumFillBits < $lower_limit ) || ( $NumFillBits > $upper_limit ) )
			return PEAR::raiseError( "packSHAPE argument (NumFillBits) out of range." );
		
		if ( ( $NumLineBits < $lower_limit) || ($NumLineBits > $upper_limit ) )
			return PEAR::raiseError( "packSHAPE argument (NumLineBits) out of range." );
		
		$atom  = $this->packnBits( $NumFillBits, 4 );
		$atom .= $this->packnBits( $NumLineBits, 4 );
		$atom  = $this->packBitValues( $atom );
		$atom .= $ShapeRecords;
		
		return $atom;
	}
	
	/**
	 * string packASSET(integer Tag, string Label)
	 *
	 * Returns an ASSET string used by ExportAssets
	 * and ImportAssets tags.
	 */
	function packASSET( $Tag, $Label )
	{
		$atom  = $this->packUI16( $Tag );
		$atom .= $this->packSTRING( $Label );
		
		return $atom;
	}
	
	/**
	 * array parseJPEGfile(string filename)
	 *
	 * Returns an array that holds the given JPEG file
	 * broken up into chunks.
	 */
	function parseJPEGfile( $filename )
	{
		$SOI  = chr( 0xff ) . chr( 0xd8 );
		$APP0 = chr( 0xff ) . chr( 0xe0 );
		$DQT  = chr( 0xff ) . chr( 0xdb );
		$SOF0 = chr( 0xff ) . chr( 0xc0 );
		$SOF1 = chr( 0xff ) . chr( 0xc1 );
		$SOF2 = chr( 0xff ) . chr( 0xc2 );
		$DHT  = chr( 0xff ) . chr( 0xc4 );
		$DRI  = chr( 0xff ) . chr( 0xdd );
		$SOS  = chr( 0xff ) . chr( 0xda );
		$EOI  = chr( 0xff ) . chr( 0xd9 );
		$COM  = chr( 0xff ) . chr( 0xfe );
		
		$filearray  = array( "JPEGEncoding" => "", "JPEGImage" => "" );
		$filehandle = fopen( $filename, "r" );
		
		if ( $filehandle == false )
			return PEAR::raiseError( "parseJPEGfile cannot open file." );
		
		$jpeg = fread( $filehandle, filesize( $filename ) );
		fclose( $filehandle );
		$marker = strpos( $jpeg, $SOI );
		$jpeg   = substr( $jpeg, $marker );
		$loop   = true;
		
		while ( $loop == true ) 
		{
			if ( strlen( $jpeg ) == 0 )
				$loop = false;
			
			switch ( substr( $jpeg, 0, 2 ) ) 
			{
				case $SOI:
					$filearray["JPEGEncoding"] = $SOI;
					$filearray["JPEGImage"] = $SOI;
					$jpeg = substr( $jpeg, 2 );
					
					break;
				
				case $APP0:
					$blocklength = ord( substr( $jpeg, 2, 1 ) ) * 256 + ord( substr( $jpeg, 3, 1 ) );	
					$filearray["JPEGImage"] .= substr( $jpeg, 0, $blocklength + 2 );
					$jpeg = substr( $jpeg, $blocklength + 2 );

					break;

				case $DQT:
					$blocklength = ord( substr( $jpeg, 2, 1 ) ) * 256 + ord( substr( $jpeg, 3, 1 ) );	
					$filearray["JPEGEncoding"] .= substr( $jpeg, 0, $blocklength + 2 );
					$jpeg = substr( $jpeg, $blocklength + 2 );

					break;

				case $SOF0:

				case $SOF1:

				case $SOF2:
					$blocklength = ord( substr( $jpeg, 2, 1 ) ) * 256 + ord( substr( $jpeg, 3, 1 ) );	
					$filearray["JPEGImage"] .= substr( $jpeg, 0, $blocklength + 2 );
					$filearray["width"]  = ord( substr( $jpeg, 7, 1 ) ) * 256 + ord( substr( $jpeg, 8, 1 ) );	
					$filearray["height"] = ord( substr( $jpeg, 5, 1 ) ) * 256 + ord( substr( $jpeg, 6, 1 ) );	
					$jpeg = substr( $jpeg, $blocklength + 2 );

					break;

				case $DHT:
					$blocklength = ord( substr( $jpeg, 2, 1 ) ) * 256 + ord( substr( $jpeg, 3, 1 ) );	
					$filearray["JPEGEncoding"] .= substr( $jpeg, 0, $blocklength + 2 );
					$jpeg = substr( $jpeg, $blocklength + 2 );
					
					break;
				
				case $DRI:
					$blocklength = ord( substr( $jpeg, 2, 1 ) ) * 256 + ord( substr( $jpeg, 3, 1 ) );	
					$filearray["JPEGImage"] .= substr( $jpeg, 0, $blocklength + 2 );
					$jpeg = substr( $jpeg, $blocklength + 2 );

					break;

				case $COM:
					$blocklength = ord( substr( $jpeg, 2, 1 ) ) * 256 + ord( substr( $jpeg, 3, 1 ) );	
					$jpeg = substr( $jpeg, $blocklength + 2 );

					break;

				case $EOI:
					$filearray["JPEGEncoding"] .= $EOI;
					$filearray["JPEGImage"] .= $EOI;
					$loop = false;

					break;

				default:
					if ( substr( $jpeg, 0, 2 ) == $SOS ) 
					{
						$blocklength = ord( substr( $jpeg, 2, 1 ) ) * 256 + ord( substr( $jpeg, 3, 1 ) );	
						$filearray["JPEGImage"] .= substr( $jpeg, 0, $blocklength + 2 );
						$jpeg = substr( $jpeg, $blocklength + 2 );
						$marker = strpos( $jpeg, chr( 255 ) );
						$filearray["JPEGImage"] .= substr( $jpeg, 0, $marker );
						$jpeg = substr( $jpeg, $marker );
						$foundsos = true;
					} 
					else 
					{
						if ( $foundsos ) 
						{
							$filearray["JPEGImage"] .= substr( $jpeg, 0, 1 );
							$jpeg   = substr( $jpeg, 1 );
							$marker = strpos( $jpeg, chr( 255 ) );
							$filearray["JPEGImage"] .= substr( $jpeg, 0, $marker );
							$jpeg = substr( $jpeg, $marker );
						} 
						else 
						{
							return PEAR::raiseError( "parseJPEGfile error parsing JPEG file file." );
						}
					}
			}
		}
		
		return $filearray;
	}
	
	/**
	 * Returns an array that holds the given JPEG file broken up into chunks.
	 */
	function parseTIFFfile( $filename, $AlphaPalette )
	{
		$II = chr( 0x49 ) . chr( 0x49 );
		$MM = chr( 0x4d ) . chr( 0x4d );
		
		$TIFFNewSubfileType              = array( "II" => chr( 0xfe ), chr( 0x00 ), "MM" => chr( 0x00 ), chr( 0xfe ) );
		$TIFFSubfileType                 = array( "II" => chr( 0xff ), chr( 0x00 ), "MM" => chr( 0x00 ), chr( 0xff ) );
		$TIFFImageWidth                  = array( "II" => chr( 0x00 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x00 ) );
		$TIFFImageLength                 = array( "II" => chr( 0x01 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x01 ) );
		$TIFFBitsPerSample               = array( "II" => chr( 0x02 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x02 ) );
		$TIFFCompression                 = array( "II" => chr( 0x03 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x03 ) );
		$TIFFPhotometricInterpretation   = array( "II" => chr( 0x06 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x06 ) );
		$TIFFThresholding                = array( "II" => chr( 0x07 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x07 ) );
		$TIFFCellWidth                   = array( "II" => chr( 0x08 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x08 ) );
		$TIFFCellLength                  = array( "II" => chr( 0x09 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x09 ) );
		$TIFFFillOrder                   = array( "II" => chr( 0x0a ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x0a ) );
		$TIFFDocumentName                = array( "II" => chr( 0x0d ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x0d ) );
		$TIFFImageDescription            = array( "II" => chr( 0x0e ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x0e ) );
		$TIFFMake                        = array( "II" => chr( 0x0f ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x0f ) );
		$TIFFModel                       = array( "II" => chr( 0x10 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x10 ) );
		$TIFFStripOffsets                = array( "II" => chr( 0x11 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x11 ) );
		$TIFFOrientation                 = array( "II" => chr( 0x12 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x12 ) );
		$TIFFSamplesPerPixel             = array( "II" => chr( 0x15 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x15 ) );
		$TIFFRowsPerStrip                = array( "II" => chr( 0x16 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x16 ) );
		$TIFFStripByteCounts             = array( "II" => chr( 0x17 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x17 ) );
		$TIFFMinSampleValue              = array( "II" => chr( 0x18 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x18 ) );
		$TIFFMaxSampleValue              = array( "II" => chr( 0x19 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x19 ) );
		$TIFFXResolution                 = array( "II" => chr( 0x1a ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x1a ) );
		$TIFFYResolution                 = array( "II" => chr( 0x1b ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x1b ) );
		$TIFFPlanarConfiguration         = array( "II" => chr( 0x1c ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x1c ) );
		$TIFFPageName                    = array( "II" => chr( 0x1d ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x1d ) );
		$TIFFXPosition                   = array( "II" => chr( 0x1e ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x1e ) );
		$TIFFYPosition                   = array( "II" => chr( 0x1f ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x1f ) );
		$TIFFFreeOffsets                 = array( "II" => chr( 0x20 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x20 ) );
		$TIFFFreeByteCounts              = array( "II" => chr( 0x21 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x21 ) );
		$TIFFGrayResponseUnit            = array( "II" => chr( 0x22 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x22 ) );
		$TIFFGrayResponseCurve           = array( "II" => chr( 0x23 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x23 ) );
		$TIFFT4Options                   = array( "II" => chr( 0x24 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x24 ) );
		$TIFFT6Options                   = array( "II" => chr( 0x25 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x25 ) );
		$TIFFResolutionUnit              = array( "II" => chr( 0x28 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x28 ) );
		$TIFFPageNumber                  = array( "II" => chr( 0x29 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x29 ) );
		$TIFFTransferFunction            = array( "II" => chr( 0x2d ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x2d ) );
		$TIFFSoftware                    = array( "II" => chr( 0x31 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x31 ) );
		$TIFFDateTime                    = array( "II" => chr( 0x32 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x32 ) );
		$TIFFArtist                      = array( "II" => chr( 0x3b ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x3b ) );
		$TIFFHostComputer                = array( "II" => chr( 0x3c ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x3c ) );
		$TIFFPredictor                   = array( "II" => chr( 0x3d ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x3d ) );
		$TIFFWhitePoint                  = array( "II" => chr( 0x3e ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x3e ) );
		$TIFFPrimaryChromaticities       = array( "II" => chr( 0x3f ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x3f ) );
		$TIFFColorMap                    = array( "II" => chr( 0x40 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x40 ) );
		$TIFFHalftoneHints               = array( "II" => chr( 0x41 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x41 ) );
		$TIFFTileWidth                   = array( "II" => chr( 0x42 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x42 ) );
		$TIFFTileLength                  = array( "II" => chr( 0x43 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x43 ) );
		$TIFFTileOffsets                 = array( "II" => chr( 0x44 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x44 ) );
		$TIFFTileByteCounts              = array( "II" => chr( 0x45 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x45 ) );
		$TIFFInkSet                      = array( "II" => chr( 0x4c ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x4c ) );
		$TIFFInkNames                    = array( "II" => chr( 0x4d ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x4d ) );
		$TIFFNumberOfInks                = array( "II" => chr( 0x4e ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x4e ) );
		$TIFFDotRange                    = array( "II" => chr( 0x50 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x50 ) );
		$TIFFTargetPrinter               = array( "II" => chr( 0x51 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x51 ) );
		$TIFFExtraSamples                = array( "II" => chr( 0x52 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x52 ) );
		$TIFFSampleFormat                = array( "II" => chr( 0x53 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x53 ) );
		$TIFFSMinSampleValue             = array( "II" => chr( 0x54 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x54 ) );
		$TIFFSMaxSampleValue             = array( "II" => chr( 0x55 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x55 ) );
		$TIFFTransferRange               = array( "II" => chr( 0x56 ), chr( 0x01 ), "MM" => chr( 0x01 ), chr( 0x56 ) );
		$TIFFJPEGProc                    = array( "II" => chr( 0x00 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x00 ) );
		$TIFFJPEGInterchangeFormat       = array( "II" => chr( 0x01 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x01 ) );
		$TIFFJPEGInterchangeFormatLength = array( "II" => chr( 0x02 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x02 ) );
		$TIFFJPEGRestartInterval         = array( "II" => chr( 0x03 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x03 ) );
		$TIFFJPEGLosslessPredictors      = array( "II" => chr( 0x05 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x05 ) );
		$TIFFJPEGPointTransforms         = array( "II" => chr( 0x06 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x06 ) );
		$TIFFJPEGQTables                 = array( "II" => chr( 0x07 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x07 ) );
		$TIFFJPEGDCTables                = array( "II" => chr( 0x08 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x08 ) );
		$TIFFJPEGACTables                = array( "II" => chr( 0x09 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x09 ) );
		$TIFFYCbCrCoefficients           = array( "II" => chr( 0x11 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x11 ) );
		$TIFFYCbCrSubSampling            = array( "II" => chr( 0x12 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x12 ) );
		$TIFFYCbCrPositioning            = array( "II" => chr( 0x13 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x13 ) );
		$TIFFReferenceBlackWhite         = array( "II" => chr( 0x14 ), chr( 0x02 ), "MM" => chr( 0x02 ), chr( 0x14 ) );
		$TIFFCopyright                   = array( "II" => chr( 0x98 ), chr( 0x82 ), "MM" => chr( 0x82 ), chr( 0x98 ) );
		
		$TIFFfile   = array();
		$filehandle = fopen( $filename, "r" );
		
		if ( $filehandle == false )
			return PEAR::raiseError( "parseTIFFfile cannot open file." );
		
		$tiff = fread( $filehandle, filesize( $filename ) );
		fclose( $filehandle );
		$byteorder   = substr( $tiff, 0, 2 );
		$filetype    = substr( $tiff, 2, 2 );
		$ifdoffset   = substr( $tiff, 4, 4 );
		$valueoffset = substr( $tiff, 8, 4 );
		
		if ( $byteorder == $II ) 
		{
			if ( $filetype != chr( 0x2a ) . chr( 0x00 ) )
				return PEAR::raiseError( "parseTIFFfile -- not a TIFF file!" );
	
			$ifdoffset   = ( ord( substr( $ifdoffset,   3, 1 ) ) * 256 + ord( substr( $ifdoffset,   2, 1 ) ) ) * 65536 + ord( substr( $ifdoffset,   1, 1 ) ) * 256 + ord( substr( $ifdoffset,   0, 1 ) ); 
			$valueoffset = ( ord( substr( $valueoffset, 3, 1 ) ) * 256 + ord( substr( $valueoffset, 2, 1 ) ) ) * 65536 + ord( substr( $valueoffset, 1, 1 ) ) * 256 + ord( substr( $valueoffset, 0, 1 ) ); 
		}
		
		// unpack MM byte order TIFF
		if ( $byteorder == $MM )
			return PEAR::raiseError( "Cannot handle MM byte order in TIFF files, yet." );
		
		$ifdtags = substr( $tiff, $ifdoffset, 2 );
		
		// unpack II byte order TIFF
		if ( $byteorder == $II ) 
		{
			$ifdtags = ord( substr( $ifdtags, 1, 1 ) ) * 256 + ord( substr( $ifdtags, 0, 1 ) );
			
			for ( $n = 0; $n < $ifdtags; $n++ ) 
			{
				$tag       = substr( $tiff, $ifdoffset + 2 + $n * 12, 2 );
				$valuetype = substr( $tiff, $ifdoffset + 2 + $n * 12 + 2, 2 );
				$valuetype = ord( substr( $valuetype, 1, 1 ) ) * 256 + ord( substr( $valuetype, 0, 1 ) );
				
				switch ( $tag ) 
				{
					case $TIFFNewSubfileType["II"]:
						break;
					
					case $TIFFSubfileType["II"]:
						break;
					
					case $TIFFImageWidth["II"]:
						if ( $valuetype == 3 ) 
						{
							$imagewidth = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$imagewidth = ord( substr( $imagewidth, 1, 1 ) ) * 256 + ord( substr( $imagewidth, 0, 1 ) ); 
							$TIFFfile["ImageWidth"] = $imagewidth;
						} 
						else if ( $valuetype == 4 ) 
						{
							$imagewidth = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 4 );
							$imagewidth = ( ord( substr( $imagewidth, 3, 1 ) ) * 256 + ord( substr( $imagewidth, 2, 1 ) ) ) * 65536 + ord( substr( $imagewidth, 1, 1 ) ) * 256 + ord( substr( $imagewidth, 0, 1 ) ); 
							$TIFFfile["ImageWidth"] = $imagewidth;
						} 
						else 
						{
							return PEAR::raiseError( "ImageWidth tag: wrong data type." );
						}
		
						break;
					
					case $TIFFImageLength["II"]:
						if ( $valuetype == 3 ) 
						{
							$imagelength = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$imagelength = ord( substr( $imagelength, 1, 1 ) ) * 256 + ord( substr( $imagelength, 0, 1 ) ); 
							$TIFFfile["ImageLength"] = $imagelength;
						} 
						else if ( $valuetype == 4 ) 
						{
							$imagelength = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 4 );
							$imagelength = ( ord( substr( $imagelength, 3, 1 ) ) * 256 + ord( substr( $imagelength, 2, 1 ) ) ) * 65536 + ord( substr( $imagelength, 1, 1 ) ) * 256 + ord( substr( $imagelength, 0, 1 ) ); 
							$TIFFfile["ImageLength"] = $imagelength;
						} 
						else 
						{
							return PEAR::raiseError( "ImageLength tag: wrong data type." );
						}
	
						break;
					
					case $TIFFBitsPerSample["II"]:
						if ( $valuetype == 3 ) 
						{
							$nvalues = substr( $tiff, $ifdoffset + 2 + $n * 12 + 4, 4 );
							$nvalues = ( ord( substr( $nvalues, 3, 1 ) ) * 256 + ord( substr( $nvalues, 2, 1 ) ) ) * 65536 + ord( substr( $nvalues, 1, 1 ) ) * 256 + ord( substr( $nvalues, 0, 1 ) );
							$TIFFfile["BitsPerSample"] = array();
			
							if ( $nvalues == 1 ) 
							{
								$bitspersample = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
								$bitspersample = ord( substr( $bitspersample, 1, 1 ) ) * 256 + ord( substr( $bitspersample, 0, 1 ) );
								$TIFFfile["BitsPerSample"][0] = $bitspersample;
							} 
							else 
							{
								$voffset = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 4 );
								$voffset = ( ord( substr( $voffset, 3, 1 ) ) * 256 + ord( substr( $voffset, 2, 1 ) ) ) * 65536 + ord( substr( $voffset, 1, 1 ) ) * 256 + ord( substr( $voffset, 0, 1 ) );

								for ( $counter = 0; $counter < $nvalues; $counter++ ) 
								{
									$foffset = $voffset + 2 * $counter;
									$bitspersample = ord( substr( $tiff, $foffset + 1, 1 ) ) * 256 + ord( substr( $tiff, $foffset, 1 ) );
									$TIFFfile["BitsPerSample"][$counter] = $bitspersample;
								}
							}
						} 
						else 
						{
							return PEAR::raiseError( "BitsPerSample: wrong tag value type." );
						}

						break;
				
					case $TIFFCompression["II"]:
						if ( $valuetype == 3 ) 
						{
							$compression = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$compression = ord( substr( $compression, 1, 1 ) ) * 256 + ord( substr( $compression, 0, 1 ) ); 
				
							if ( $compression == 1 ) 
								$TIFFfile["Compression"] = $compression;
							else 
								return PEAR::raiseError( "Cannot handle this kind of compression yet.");
						} 
						else 
						{
							return PEAR::raiseError( "Compression tag: wrong data type." );
						}
							
						if ( $TIFFfile["Compression"] != 1 )
							return PEAR::raiseError( "Cannot Handle compressed TIFF files, sorry." );
		
						break;
						
					case $TIFFPhotometricInterpretation["II"]:
						if ( $valuetype == 3 ) 
						{
							$interpretation = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$interpretation = ord( substr( $interpretation, 1, 1 ) ) * 256 + ord( substr( $interpretation, 0, 1 ) ); 
							$TIFFfile["PhotometricInterpretation"] = $interpretation;
						} 
						else 
						{
							return PEAR::raiseError( "PhotometricInterpretation tag: wrong data type." );
						}
				
						break;
				
					case $TIFFThresholding["II"]:
						break;
					
					case $TIFFCellWidth["II"]:
						break;
					
					case $TIFFCellLength["II"]:
						break;
					
					case $TIFFFillOrder["II"]:
						if ( $valuetype == 3 ) 
						{
							$fillorder = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$fillorder = ord( substr( $fillorder, 1, 1 ) ) * 256 + ord( substr( $fillorder, 0, 1 ) );
							$TIFFfile["FillOrder"] = $fillorder;
						} 
						else 
						{
							return PEAR::raiseError( "FillOrder tag: wrong data type." );
						}
	
						break;
					
					case $TIFFDocumentName["II"]:
						break;
					
					case $TIFFImageDescription["II"]:
						break;
					
					case $TIFFMake["II"]:
						break;
					
					case $TIFFModel["II"]:
						break;
					
					case $TIFFStripOffsets["II"]:
						$nvalues = substr( $tiff, $ifdoffset + 2 + $n * 12 + 4, 4 );
						$nvalues = ( ord( substr( $nvalues, 3, 1 ) ) * 256 + ord( substr( $nvalues, 2, 1 ) ) ) * 65536 + ord( substr( $nvalues, 1, 1 ) ) * 256 + ord( substr( $nvalues, 0, 1 ) ); 
						$voffset = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 4 );
						$voffset = ( ord( substr( $voffset, 3, 1 ) ) * 256 + ord( substr( $voffset, 2, 1 ) ) ) * 65536 + ord( substr( $voffset, 1, 1 ) ) * 256 + ord( substr( $voffset, 0, 1 ) ); 
						$TIFFfile["StripOffsets"] = array();
		
						if ( $valuetype == 3 ) 
						{
							if ( $nvalues == 1 ) 
							{
								$TIFFfile["StripOffsets"][0] = $voffset;
							} 
							else 
							{
								for ( $counter = 0; $counter < $nvalues; $counter++ ) 
								{
									$foffset = $voffset + 2 * $counter;
									$stripoffsets = ord( substr( $tiff, $foffset + 1, 1 ) ) * 256 + ord( substr( $tiff, $foffset, 1 ) ); 
									$TIFFfile["StripOffsets"][$counter] = $stripoffsets;
								}
							}
						} 
						else if ( $valuetype == 4 ) 
						{ 
							if ( $nvalues == 1 ) 
							{
								$TIFFfile["StripOffsets"][0] = $voffset;
							} 
							else 
							{
								for ( $counter = 0; $counter < $nvalues; $counter++ ) 
								{
									$foffset = $voffset + 4 * $counter;
									$stripoffsets = ( ord( substr( $tiff, $foffset + 3, 1 ) ) * 256 + ord( substr( $tiff, $foffset + 2, 1 ) ) ) * 65536 + ord( substr( $tiff, $foffset + 1, 1 ) ) * 256 + ord( substr( $tiff, $foffset, 1 ) ); 
									$TIFFfile["StripOffsets"][$counter] = $stripoffsets;
								}
							}
						} 
						else 
						{
							return PEAR::raiseError( "StripOffsets: wrong tag value type." );
						}
	
						break;
					
					case $TIFFOrientation["II"]:
						if ( $valuetype == 3 ) 
						{
							$subfiletype = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$subfiletype = ord( substr( $subfiletype, 1, 1 ) ) * 256 + ord( substr( $subfiletype, 0, 1 ) ); 
							$TIFFfile["Orientation"] = $subfiletype;
						} 
						else 
						{
							return PEAR::raiseError( "Orientation tag: wrong data type." );
						}
					
						break;
				
					case $TIFFSamplesPerPixel["II"]:
						if ( $valuetype == 3 ) 
						{
							$samplesperpixel = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$samplesperpixel = ord( substr( $samplesperpixel, 1, 1 ) ) * 256 + ord( substr( $samplesperpixel, 0, 1 ) ); 
							$TIFFfile["SamplesPerPixel"] = $samplesperpixel;
						} 
						else 
						{
							return PEAR::raiseError( "SamplesPerPixel tag: wrong data type." );
						}
					
						break;
					
					case $TIFFRowsPerStrip["II"]:
						if ( $valuetype == 3 ) 
						{
							$rowsperstrip = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$rowsperstrip = ord( substr( $rowsperstrip, 1, 1 ) ) * 256 + ord( substr( $rowsperstrip, 0, 1 ) ); 
							$TIFFfile["RowsPerStrip"] = $rowsperstrip;
						} 
						else if ( $valuetype == 4 ) 
						{
							$rowsperstrip = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 4 );
							$rowsperstrip = ( ord( substr( $rowsperstrip, 3, 1 ) ) * 256 + ord( substr( $rowsperstrip, 2, 1 ) ) ) * 65536 + ord( substr( $rowsperstrip, 1, 1 ) ) * 256 + ord( substr( $rowsperstrip, 0, 1 ) ); 
							$TIFFfile["RowsPerStrip"] = $rowsperstrip;
						} 
						else 
						{
							return PEAR::raiseError( "RowsPerStrip tag: wrong data type." );
						}
					
						break;
				
					case $TIFFStripByteCounts["II"]:
						$nvalues = substr( $tiff, $ifdoffset + 2 + $n * 12 + 4, 4 );
						$nvalues = ( ord( substr( $nvalues, 3, 1 ) ) * 256 + ord( substr( $nvalues, 2, 1 ) ) ) * 65536 + ord( substr( $nvalues, 1, 1 ) ) * 256 + ord( substr( $nvalues, 0, 1 ) ); 
						$voffset = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 4 );
						$voffset = ( ord( substr( $voffset, 3, 1 ) ) * 256 + ord( substr( $voffset, 2, 1 ) ) ) * 65536 + ord( substr( $voffset, 1, 1 ) ) * 256 + ord( substr( $voffset, 0, 1 ) ); 
						$TIFFfile["StripByteCounts"] = array();

						if ( $valuetype == 3 ) 
						{
							if ( $nvalues == 1 ) 
							{
								$TIFFfile["StripByteCounts"][0] = $voffset;
							} 
							else 
							{
								for ( $counter = 0; $counter < $nvalues; $counter++ ) 
								{
									$foffset = $voffset + 2 * $counter;
									$stripbytecounts = ord( substr( $tiff, $foffset + 1, 1 ) ) * 256 + ord( substr( $tiff, $foffset, 1 ) ); 
									$TIFFfile["StripByteCounts"][$counter] = $stripbytecounts;
								}
							}
						} 
						else if ( $valuetype == 4 ) 
						{ 
							if ( $nvalues == 1 ) 
							{
								$TIFFfile["StripByteCounts"][0] = $voffset;
							} 
							else 
							{
								for ( $counter = 0; $counter < $nvalues; $counter++ ) 
								{
									$foffset = $voffset + 4 * $counter;
									$stripbytecounts = ( ord( substr( $tiff, $foffset + 3, 1 ) ) * 256 + ord( substr( $tiff, $foffset + 2, 1 ) ) ) * 65536 + ord( substr( $tiff, $foffset + 1, 1 ) ) * 256 + ord( substr( $tiff, $foffset, 1 ) ); 
									$TIFFfile["StripByteCounts"][$counter] = $stripbytecounts;
								}
							}
						} 
						else 
						{
							return PEAR::raiseError( "StripByteCounts: wrong tag value type." );
						}
	
						break;
				
					case $TIFFMinSampleValue["II"]:
						break;
					
					case $TIFFMaxSampleValue["II"]:
						break;
					
					case $TIFFXResolution["II"]:
						break;
					
					case $TIFFYResolution["II"]:
						break;
					
					case $TIFFPlanarConfiguration["II"]:
						if ( $valuetype == 3 ) 
						{
							$planarconfiguration = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$planarconfiguration = ord( substr( $planarconfiguration, 1, 1 ) ) * 256 + ord( substr( $planarconfiguration, 0, 1 ) ); 
							$TIFFfile["PlanarConfiguration"] = $planarconfiguration;
						} 
						else 
						{
							return PEAR::raiseError( "PlanarConfiguration tag: wrong data type." );
						}

						break;
				
					case $TIFFPageName["II"]:
						break;
					
					case $TIFFXPosition["II"]:
						break;
					
					case $TIFFYPosition["II"]:
						break;
					
					case $TIFFFreeOffsets["II"]:
						break;
					
					case $TIFFFreeByteCounts["II"]:
						break;
					
					case $TIFFGrayResponseUnit["II"]:
						break;
					
					case $TIFFGrayResponseCurve["II"]:
						break;
					
					case $TIFFT4Options["II"]:
						break;
					
					case $TIFFT6Options["II"]:
						break;
				
					case $TIFFResolutionUnit["II"]:
						break;
				
					case $TIFFPageNumber["II"]:
						break;
				
					case $TIFFTransferFunction["II"]:
						break;
					
					case $TIFFSoftware["II"]:
						break;
				
					case $TIFFDateTime["II"]:
						break;
				
					case $TIFFArtist["II"]:
						break;
					
					case $TIFFHostComputer["II"]:
						break;
				
					case $TIFFPredictor["II"]:
						break;
					
					case $TIFFWhitePoint["II"]:
						break;
					
					case $TIFFPrimaryChromaticities["II"]:
						break;
					
					case $TIFFColorMap["II"]:
						if ( $valuetype == 3 ) 
						{
							$nvalues = substr( $tiff, $ifdoffset + 2 + $n * 12 + 4, 4 );
							$nvalues = ord( substr( $nvalues, 1, 1 ) ) * 256 + ord( substr( $nvalues, 0, 1 ) ); 
							$voffset = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 4 );
							$voffset = ( ord( substr( $voffset, 3, 1 ) ) * 256 + ord( substr( $voffset, 2, 1 ) ) ) * 65536 + ord( substr( $voffset, 1, 1 ) ) * 256 + ord( substr( $voffset, 0, 1 ) ); 
							$TIFFfile["ColorMap"] = substr( $tiff, $voffset, 2 * $nvalues );
						} 
						else 
						{
							return PEAR::raiseError( "ColorMap: wrong tag value type." );
						}
					
						break;
					
					case $TIFFHalftoneHints["II"]:
						break;
					
					case $TIFFTileWidth["II"]:
						break;
				
					case $TIFFTileLength["II"]:
						break;
				
					case $TIFFTileOffsets["II"]:
						break;
					
					case $TIFFTileByteCounts["II"]:
						break;
				
					case $TIFFInkSet["II"]:
						break;
				
					case $TIFFInkNames["II"]:
						break;
				
					case $TIFFNumberOfInks["II"]:
						break;
				
					case $TIFFExtraSamples["II"]:
						if ( $valuetype == 3 ) 
						{
							$extrasamples = substr( $tiff, $ifdoffset + 2 + $n * 12 + 8, 2 );
							$extrasamples = ord( substr( $extrasamples, 1, 1 ) ) * 256 + ord( substr( $extrasamples, 0, 1 ) ); 
							$TIFFfile["ExtraSamples"] = $extrasamples;
						} 
						else 
						{
							return PEAR::raiseError( "ExtraSamples: wrong data type." );
						}
				
						break;
				
					case $TIFFSampleFormat["II"]:
						break;
					
					case $TIFFSMinSampleValue["II"]:
						break;
				
					case $TIFFSMaxSampleValue["II"]:
						break;
					
					case $TIFFTransferRange["II"]:
						break;
				
					case $TIFFJPEGProc["II"]:
						break;
				
					case $TIFFJPEGInterchangeFormat["II"]:
						break;
				
					case $TIFFJPEGInterchangeFormatLength["II"]:
						break;
				
					case $TIFFJPEGRestartInterval["II"]:
						break;
				
					case $TIFFJPEGLosslessPredictors["II"]:
						break;
				
					case $TIFFJPEGPointTransforms["II"]:
						break;
				
					case $TIFFJPEGQTables["II"]:
						break;
				
					case $TIFFJPEGDCTables["II"]:
						break;
				
					case $TIFFJPEGACTables["II"]:
						break;

					case $TIFFYCbCrCoefficients["II"]:
						break;
				
					case $TIFFYCbCrSubSampling["II"]:
						break;
				
					case $TIFFYCbCrPositioning["II"]:
						break;
				
					case $TIFFReferenceBlackWhite["II"]:
						break;
				
					case $TIFFCopyright["II"]:
						break;
				}
			} 
		}
		
		// process TIFF data
		$bitmap = array();
		switch ( $TIFFfile["PhotometricInterpretation"] ) 
		{	
			// Bilevel - WhiteIsZero
			case 0:
				if ( !( in_array( "BitsPerSample", $TIFFfile ) ) ) 
				{
				} 
				
				// build image stream
				$imagestream = "";
				$nstrips = count( $TIFFfile["StripOffsets"] );
				
				for ( $counter = 0; $counter < $nstrips; $counter++ ) 
					$imagestream .= substr( $tiff, $TIFFfile["StripOffsets"][$counter], $TIFFfile["StripByteCounts"][$counter] ); 
				
				$newimagestream = "";
				$padcount = $TIFFfile["ImageWidth"] % 4;
				
				if ( ( $padcount ) == 0 ) 
					$padding = "";
				else 
					$padding = str_repeat( chr( 0 ), 4 - $padcount );
				
				for ( $counter = 0; $counter < $TIFFfile["ImageLength"]; $counter++ ) 
					$newimagestream .= substr( $imagestream, $counter * $TIFFfile["ImageWidth"], $TIFFfile["ImageWidth"] ) . $padding;
				
				// create palettes
				$swfcolormap = "";
				
				if  ( $AlphaPalette == null ) 
				{
					for ( $counter = 255; $counter >= 0; $counter-- ) 
					{
						$r = chr( $counter );
						$g = chr( $counter );
						$b = chr( $counter );
						$swfcolormap .= $r . $g . $b;
					}
					
					$bitmap["colortablesize"]  = ( strlen( $swfcolormap ) / 3 ) - 1;
				} 
				else 
				{
					$a = array();
					
					for ( $counter = 0; $counter < 256; $counter++ ) 
						$a[$counter] = chr( 255 );
					
					$limita = sizeof( $AlphaPalette );

					reset( $AlphaPalette );
					for ( $counter = 0; $counter < $limita; $counter++ ) 
					{
						$tmp = each( $AlphaPalette );
						$a[$tmp["key"]] = chr( $tmp["value"] );
					}
					
					for ( $counter = 255; $counter >= 0; $counter-- ) 
					{
						$r = chr( $counter );
						$g = chr( $counter );
						$b = chr( $counter );
						$swfcolormap .= $r . $g . $b . $a[$counter];
					}
					
					$bitmap["colortablesize"]  = ( strlen( $swfcolormap ) / 4 ) - 1;
				}
				
				$zlibbitmapdata = gzcompress( $swfcolormap . $newimagestream, 9 );
				$alphadata      = gzcompress( $imagestream, 9 );

				break;
			
			// Bilevel - BlackIsZero
			case 1:				
				// build image stream 
				$imagestream = "";
				$nstrips = count( $TIFFfile["StripOffsets"] );
				
				for ( $counter = 0; $counter < $nstrips; $counter++ )
					$imagestream .= substr( $tiff, $TIFFfile["StripOffsets"][$counter], $TIFFfile["StripByteCounts"][$counter] ); 
		
				$newimagestream = "";
				$padcount = $TIFFfile["ImageWidth"] % 4;
		
				if ( ( $padcount ) == 0 ) 
					$padding = "";
				else 
					$padding = str_repeat( chr( 0 ), 4 - $padcount );
				
				for ( $counter = 0; $counter < $TIFFfile["ImageLength"]; $counter++ )
					$newimagestream .= substr( $imagestream, $counter * $TIFFfile["ImageWidth"], $TIFFfile["ImageWidth"] ) . $padding;
				
				// create palette
				$swfcolormap = "";

				if ( $AlphaPalette == null ) 
				{
					for ( $counter = 0; $counter < 256; $counter++ ) 
					{
						$r = chr( $counter );
						$g = chr( $counter );
						$b = chr( $counter );
						$swfcolormap .= $r . $g . $b;
					}
					
					$bitmap["colortablesize"]  = ( strlen( $swfcolormap ) / 3 ) - 1;
				} 
				else 
				{
					$a = array();
					
					for ( $counter = 0; $counter < 256; $counter++ ) 
						$a[$counter] = chr( 255 );
					
					$limita = sizeof( $AlphaPalette );
					
					reset( $AlphaPalette );
					for ( $counter = 0; $counter < $limita; $counter++ ) 
					{
						$tmp = each( $AlphaPalette );
						$a[$tmp["key"]] = chr( $tmp["value"] );
					}
					
					for ( $counter = 0; $counter < 256; $counter++ ) 
					{
						$r = chr( $counter );
						$g = chr( $counter );
						$b = chr( $counter );
						$swfcolormap .= $r . $g . $b . $a[$counter];
					}
					
					$bitmap["colortablesize"]  = ( strlen( $swfcolormap ) / 4 ) - 1;
				}
				
				$zlibbitmapdata = gzcompress( $swfcolormap . $newimagestream, 9 );
				$alphadata      = gzcompress( $imagestream, 9 );
				
				break;
			
			// RGB
			case 2:
				return PEAR::raiseError( "Cannot handle full-color images, use JPEG instead." );
				break;
			
			// Palette
			case 3:				
				// build image stream
				$imagestream = "";
				$nstrips     = count( $TIFFfile["StripOffsets"] );
				
				for ( $counter = 0; $counter < $nstrips; $counter++ )
					$imagestream .= substr( $tiff, $TIFFfile["StripOffsets"][$counter], $TIFFfile["StripByteCounts"][$counter] ); 
		
				$newimagestream = "";
				$padcount = $TIFFfile["ImageWidth"] % 4;
				
				if ( ( $padcount ) == 0 ) 
					$padding = "";
				else 
					$padding = str_repeat( chr( 0 ), 4 - $padcount );
				
				for ( $counter = 0; $counter < $TIFFfile["ImageLength"]; $counter++ )
					$newimagestream .= substr( $imagestream, $counter * $TIFFfile["ImageWidth"], $TIFFfile["ImageWidth"] ) . $padding;
				
				// reconfigure palette
				$newcolormap = "";
				$limit = strlen( $TIFFfile["ColorMap"] );
				
				for ( $counter = 0; $counter < $limit; $counter++ )
					$newcolormap .= substr( $TIFFfile["ColorMap"], 2 * $counter, 1 );
		
				$swfcolormap = "";
				$limit = strlen( $newcolormap ) / 3;

				if ( $AlphaPalette == null ) 
				{
					for ( $counter = 0; $counter < $limit; $counter++ ) 
					{
						$r = substr( $newcolormap, $counter, 1 );
						$g = substr( $newcolormap, $counter + $limit, 1 );
						$b = substr( $newcolormap, $counter + $limit, 1 );
						$swfcolormap .= $r . $g . $b;
					}
					
					$bitmap["colortablesize"]  = ( strlen( $swfcolormap ) / 3 ) - 1;
				} 
				else 
				{
					$a = array();
					
					for ( $counter = 0; $counter < 256; $counter++ ) 
						$a[$counter] = chr( 255 );
					
					$limita = sizeof( $AlphaPalette );
					
					reset( $AlphaPalette );
					for ( $counter = 0; $counter < $limita; $counter++ ) 
					{
						$tmp = each( $AlphaPalette );
						$a[$tmp["key"]] = chr( $tmp["value"] );
					}
					
					for ( $counter = 0; $counter < 256; $counter++ ) 
					{
						$r = substr( $newcolormap, $counter, 1 );
						$g = substr( $newcolormap, $counter + $limit, 1 );
						$b = substr( $newcolormap, $counter + $limit, 1 );
						$swfcolormap .= $r . $g . $b . $a[$counter];
					}
					
					$bitmap["colortablesize"]  = ( strlen( $swfcolormap ) / 4 ) - 1;
				}
				
				$zlibbitmapdata = gzcompress( $swfcolormap . $newimagestream, 9 );
				$alphadata      = gzcompress( $imagestream, 9 );

				break;
			
			// Transparency mask
			case 4:
				return PEAR::raiseError( "Cannot handle images with transparency masks, use RGB + Alpha encoding." );
				break;
		}
		
		$bitmap["format"]          = 3;
		$bitmap["width"]           = $TIFFfile["ImageWidth"];
		$bitmap["height"]          = $TIFFfile["ImageLength"];
		$bitmap["colortable"]      = $swfcolormap;
		$bitmap["newimagestream"]  = $newimagestream;
		$bitmap["zlibbitmapdata"]  = $zlibbitmapdata;
		$bitmap["alphadata"]       = $alphadata;
		
		return $bitmap;
	}
	
	function parseTrueTypefile( $filename )
	{
		$TTfile = array();
		$filehandle = fopen( $filename, "r" );
		
		if ( $filehandle == false )
			return PEAR::raiseError( "parseTrueTypefile cannot open font file." );
		
		$tt = fread( $filehandle, filesize( $filename ) );
		fclose( $filehandle );
		
		// offset subtable
		$ScalerType = substr( $tt, 0, 4 );
		
		if ( ( $ScalerType == "true" ) || ( $ScalerType == chr( 0x00 ) . chr( 0x01 ) . chr( 0x00 ) . chr( 0x00 ) ) ) 
		{
			$TTfile["OffsetSubtable"] = array();
			$TTfile["OffsetSubtable"]["ScalerType"]    = $ScalerType;
			$TTfile["OffsetSubtable"]["numTables"]     = ord( substr( $tt,  4, 1 ) ) * 256 + ord( substr( $tt,  5, 1 ) ); 
			$TTfile["OffsetSubtable"]["searchRange"]   = ord( substr( $tt,  6, 1 ) ) * 256 + ord( substr( $tt,  7, 1 ) ); 
			$TTfile["OffsetSubtable"]["entrySelector"] = ord( substr( $tt,  8, 1 ) ) * 256 + ord( substr( $tt,  9, 1 ) ); 
			$TTfile["OffsetSubtable"]["rangeShift"]    = ord( substr( $tt, 10, 1 ) ) * 256 + ord( substr( $tt, 11, 1 ) ); 
		} 
		else 
		{
			return PEAR::raiseError( "parseTrueTypefile: Not a TrueType font." );
		}
		
		for ( $counter = 0; $counter < $TTfile["OffsetSubtable"]["numTables"]; $counter++ ) 
		{	
			// read tag name
			$tmp = 12 + 16 * $counter;
			$tag = substr( $tt, $tmp, 4 );
			
			$TTfile[$tag] = array();
			$TTfile[$tag]["checksum"] = ( ord( substr( $tt, $tmp +  4, 1 ) ) * 256 + ord( substr( $tt, $tmp +  5, 1 ) ) ) * 65536 + ord( substr( $tt, $tmp +  6, 1 ) ) * 256 + ord( substr( $tt, $tmp +  7, 1 ) ); 
			$TTfile[$tag]["offset"]   = ( ord( substr( $tt, $tmp +  8, 1 ) ) * 256 + ord( substr( $tt, $tmp +  9, 1 ) ) ) * 65536 + ord( substr( $tt, $tmp + 10, 1 ) ) * 256 + ord( substr( $tt, $tmp + 11, 1 ) ); 
			$TTfile[$tag]["length"]   = ( ord( substr( $tt, $tmp + 12, 1 ) ) * 256 + ord( substr( $tt, $tmp + 13, 1 ) ) ) * 65536 + ord( substr( $tt, $tmp + 14, 1 ) ) * 256 + ord( substr( $tt, $tmp + 15, 1 ) ); 
		}
		
		// decode "cmap" table
		if ( $TTfile["cmap"]["offset"] != null ) 
		{
			$tmp = $TTfile["cmap"]["offset"];
			$TTfile["cmap"]["version"]    = ord( substr( $tt, $tmp, 1 ) )     * 256 + ord( substr( $tt, $tmp + 1, 1 ) );
			$TTfile["cmap"]["nSubtables"] = ord( substr( $tt, $tmp + 2, 1 ) ) * 256 + ord( substr( $tt, $tmp + 3, 1 ) );
			$tmpa = $tmp + 4;

			for ( $counter = 0; $counter < $TTfile["cmap"]["nSubtables"]; $counter++ ) 
			{
				$TTfile["cmap"]["subtables"][$counter]["platformID"]         =   ord( substr( $tt, $tmpa, 1 ) )     * 256 + ord( substr( $tt, $tmpa + 1, 1 ) ); 
				$TTfile["cmap"]["subtables"][$counter]["platformSpecificID"] =   ord( substr( $tt, $tmpa + 2, 1 ) ) * 256 + ord( substr( $tt, $tmpa + 3, 1 ) ); 
				$TTfile["cmap"]["subtables"][$counter]["offset"]             = ( ord( substr( $tt, $tmpa + 4, 1 ) ) * 256 + ord( substr( $tt, $tmpa + 5, 1 ) ) ) * 65536 + ord( substr( $tt, $tmpa + 6, 1 ) ) * 256 + ord( substr( $tt, $tmpa + 7, 1 ) ); 
				$tmpa += 8;
			}
			
			for ( $counter = 0; $counter < $TTfile["cmap"]["nSubtables"]; $counter++ ) 
			{
				$tmpa = $tmp + $TTfile["cmap"]["subtables"][$counter]["offset"];
				$TTfile["cmap"]["subtables"][$counter]["format"]   = ord( substr( $tt, $tmpa, 1 ) )     * 256 + ord( substr( $tt, $tmpa + 1, 1 ) ); 
				$TTfile["cmap"]["subtables"][$counter]["length"]   = ord( substr( $tt, $tmpa + 2, 1 ) ) * 256 + ord( substr( $tt, $tmpa + 3, 1 ) ); 
				$TTfile["cmap"]["subtables"][$counter]["language"] = ord( substr( $tt, $tmpa + 4, 1 ) ) * 256 + ord( substr( $tt, $tmpa + 5, 1 ) ); 

				if ( $TTfile["cmap"]["subtables"][$counter]["format"] == 0 ) 
				{
					for ( $countert = 0; $countert < 256; $countert++ ) 
						$TTfile["cmap"]["subtables"][$counter]["glyphIndexArray"][$countert] = ord( substr( $tt, $tmpa + 6 + $countert, 1 ) );
				}
				
				if ( $TTfile["cmap"]["subtables"][$counter]["format"] == 2 ) 
				{
					for ( $countert = 0; $countert < 256; $countert++ ) 
						$TTfile["cmap"]["subtables"][$counter]["subHeaderKeys"][$countert] = ord( substr( $tt, $tmpa + 6 + $countert, 1 ) );
				}
			}
		} 
		else 
		{
			return PEAR::raiseError( "parseTrueTypefile: no cmap table... cannot create proper font mappings." );
		}
		
		// decode "name" table
		if ( $TTfile["name"]["offset"] != null ) 
		{
			$tmp = $TTfile["name"]["offset"];
			$TTfile["name"]["format"]       = ord( substr( $tt, $tmp, 1 ) )     * 256 + ord( substr( $tt, $tmp + 1, 1 ) );
			$TTfile["name"]["count"]        = ord( substr( $tt, $tmp + 2, 1 ) ) * 256 + ord( substr( $tt, $tmp + 3, 1 ) );
			$TTfile["name"]["stringOffset"] = ord( substr( $tt, $tmp + 4, 1 ) ) * 256 + ord( substr( $tt, $tmp + 5, 1 ) );
			
			for ( $counter = 0; $counter < $TTfile["name"]["count"]; $counter++ ) 
			{
				$tmpa = $tmp + 6 + $counter * 12;
				$TTfile["name"]["records"][$counter]["platformID"]         = ord( substr( $tt, $tmpa, 1 ) )      * 256 + ord( substr( $tt, $tmpa +  1, 1 ) ); 
				$TTfile["name"]["records"][$counter]["platformSpecificID"] = ord( substr( $tt, $tmpa +  2, 1 ) ) * 256 + ord( substr( $tt, $tmpa +  3, 1 ) );
				$TTfile["name"]["records"][$counter]["languageID"]         = ord( substr( $tt, $tmpa +  4, 1 ) ) * 256 + ord( substr( $tt, $tmpa +  5, 1 ) );
				$TTfile["name"]["records"][$counter]["nameID"]             = ord( substr( $tt, $tmpa +  6, 1 ) ) * 256 + ord( substr( $tt, $tmpa +  7, 1 ) );
				$TTfile["name"]["records"][$counter]["length"]             = ord( substr( $tt, $tmpa +  8, 1 ) ) * 256 + ord( substr( $tt, $tmpa +  9, 1 ) );
				$TTfile["name"]["records"][$counter]["offset"]             = ord( substr( $tt, $tmpa + 10, 1 ) ) * 256 + ord( substr( $tt, $tmpa + 11, 1 ) );
				$TTfile["name"]["records"]["namestring"][$counter]         = substr( $tt, $tmp + $TTfile["name"]["records"][$counter]["offset"], $TTfile["name"]["records"][$counter]["length"] );
			}
		} 
		else 
		{
			return PEAR::raiseError( "parseTrueTypefile: no name table... cannot create proper font name entry." );
		}
		
		// decode "post" table
		if ( $TTfile["post"]["offset"] != null ) 
		{
			$tmp = $TTfile["post"]["offset"];
			$TTfile["post"]["format"] = ord( substr( $tt, $tmp, 1 ) ) * 256 + ord( substr( $tt, $tmp + 1, 1 ) ) + ( ord( substr( $tt, $tmp + 2, 1 ) ) * 256 + ord( substr( $tt, $tmp + 3, 1 ) ) ) / 100000;
			$f_int = ord( substr( $tt, $tmp + 4, 1 ) ) * 256 + ord( substr( $tt, $tmp + 5, 1 ) );
			$f_fra = round( ( ord( substr( $tt, $tmp + 6, 1 ) ) * 256 + ord( substr( $tt, $tmp + 7, 1 ) ) ) / 100000, 4 );
			
			if ( $f_int > 32767 ) 
			{
				$f_int = -( $f_int - 32768 );
				$f_fra = -$f_fra;
			}
			
			$TTfile["post"]["italicAngle"] = $f_int + $f_fra; 
			$f_int = ord( substr( $tt, $tmp + 8, 1 ) ) * 256 + ord( substr( $tt, $tmp + 9, 1 ) );
			
			if ( $f_int > 32767 )
				$f_int = -( $f_int - 32768 );
	
			$TTfile["post"]["underlinePosition"] = $f_int; 
			$f_int = ord( substr( $tt, $tmp + 10, 1 ) ) * 256 + ord( substr( $tt, $tmp + 11, 1 ) );
			
			if ( $f_int > 32767 )
				$f_int = -( $f_int - 32768 );
	
			$TTfile["post"]["underlineThickness"] = $f_int; 
			$f_int = ord( substr( $tt, $tmp + 12, 1 ) ) * 256 + ord( substr( $tt, $tmp + 13, 1 ) );
			$TTfile["post"]["isFixedPitch"] = $f_int; 
			$f_int = ord( substr( $tt, $tmp + 14, 1 ) ) * 256 + ord( substr( $tt, $tmp + 15, 1 ) );
			$TTfile["post"]["reserved"]     = $f_int; 
			$TTfile["post"]["minMemType42"] = ( ord( substr( $tt, $tmp + 16, 1 ) ) * 256 + ord( substr( $tt, $tmp + 17, 1 ) ) ) * 65536 + ord( substr( $tt, $tmp + 18, 1 ) ) * 256 + ord( substr( $tt, $tmp + 19, 1 ) ); 
			$TTfile["post"]["maxMemType42"] = ( ord( substr( $tt, $tmp + 20, 1 ) ) * 256 + ord( substr( $tt, $tmp + 21, 1 ) ) ) * 65536 + ord( substr( $tt, $tmp + 22, 1 ) ) * 256 + ord( substr( $tt, $tmp + 23, 1 ) ); 
			$TTfile["post"]["minMemType1"]  = ( ord( substr( $tt, $tmp + 24, 1 ) ) * 256 + ord( substr( $tt, $tmp + 25, 1 ) ) ) * 65536 + ord( substr( $tt, $tmp + 26, 1 ) ) * 256 + ord( substr( $tt, $tmp + 27, 1 ) ); 
			$TTfile["post"]["maxMemType1"]  = ( ord( substr( $tt, $tmp + 28, 1 ) ) * 256 + ord( substr( $tt, $tmp + 29, 1 ) ) ) * 65536 + ord( substr( $tt, $tmp + 30, 1 ) ) * 256 + ord( substr( $tt, $tmp + 31, 1 ) ); 
			
			if ( $TTfile["post"]["format"] = 2 ) 
			{
				$tmp += 32;
				$nGlyphs = ord( substr( $tt, $tmp, 1 ) ) * 256 + ord( substr( $tt, $tmp + 1, 1 ) );
				$TTfile["post"]["subtable"]["nGlyphs"] = $nGlyphs;
				$tmp += 2;
				
				for ( $counter = 0; $counter < $nGlyphs; $counter++ ) 
				{
					$TTfile["post"]["subtable"]["GlyphIDs"][$counter] = ord( substr( $tt, $tmp, 1 ) ) * 256 + ord( substr( $tt, $tmp + 1, 1 ) );
					$tmp += 2;
				}
				
				for ( $counter = 0; $counter < $nGlyphs; $counter++ ) 
				{
					$TTfile["post"]["subtable"]["GlyphNames"][$counter] = substr( $tt, $tmp + 1, ord( substr( $tt, $tmp, 1 ) ) );
					$tmp += 1 + strlen( $TTfile["post"]["subtable"]["GlyphNames"][$counter] );
				}
			}
			
			if ( $TTfile["post"]["format"] = 2.5 ) 
			{
				$tmp += 32;
				$nGlyphs = ord( substr( $tt, $tmp, 1 ) ) * 256 + ord( substr( $tt, $tmp + 1, 1 ) );
				$TTfile["post"]["subtable"]["nGlyphs"] = $nGlyphs;
				$tmp += 2;
				
				for ( $counter = 0; $counter < $nGlyphs; $counter++ ) 
				{
					$toff = ord( substr( $tt, $tmp, 1 ) );

					if ( $toff > 127 ) 
						$toff = -($toff - 128);
					
					$TTfile["post"]["subtable"]["offset"][$counter] = $toff;
					$tmp += 1;
				}
				
				for ( $counter = 0; $counter < $nGlyphs; $counter++ ) 
				{
					$TTfile["post"]["subtable"]["GlyphNames"][$counter] = substr( $tt, $tmp + 1, ord( substr( $tt, $tmp, 1 ) ) );
					$tmp += 1 + strlen( $TTfile["post"]["subtable"]["GlyphNames"][$counter] );
				}
			}
		} 
		else 
		{
			return PEAR::raiseError( "parseTrueTypefile: no post table... cannot create proper TrueType to PostScript table." );
		}

		if ( in_array( "glyf", $TTfile ) ) 
		{
		} 
		else 
		{
			return PEAR::raiseError( "parseTrueTypefile: no glyph table... cannot create glyph outlines." );
		}
		
		// Return value???
	}
	
	function packSOUNDINFO( $SyncFlags, $HasEnvelope, $HasLoops, $HasOutPoint, $HasInPoint, $InPoint, $OutPoint, $LoopCount, $nEnvelopePoints, $Envelope ) 
	{
		$SOUNDINFO  = $this->packnBits( $SyncFlags, 4 );
		$SOUNDINFO .= $HasEnvelope;
		$SOUNDINFO .= $HasLoops;
		$SOUNDINFO .= $HasOutPoint;
		$SOUNDINFO .= $HasInPoint;
		$SOUNDINFO  = $this->packBitValues( $SOUNDINFO );
		
		if ( $HasInPoint )
			$SOUNDINFO .= $this->packUI32( $InPoint );
		
		if ( $HasOutPoint )
			$SOUNDINFO .= $this->packUI32( $OutPoint );
		
		if ( $HasLoops )
			$SOUNDINFO .= $this->packUI16( $LoopCount );
		
		if ( $HasEnvelope ) 
		{
			$SOUNDINFO .= $this->packUI8( $nEnvelopePoints );
			$SOUNDINFO .= $Envelope;
		}
		
		return $SOUNDINFO;
	}
	
	function packSOUNDENVELOPE( $Mark44, $Level0, $Level1 )
	{
		$SOUNDENVELOPE  = $this->packUI32( $Mark44 );
		$SOUNDENVELOPE .= $this->packUI16( $Level0 );
		$SOUNDENVELOPE .= $this->packUI16( $Level1 );
		
		return $SOUNDENVELOPE;
	}
	
	function packADPCMSOUNDDATA()
	{
	}
	
	function packADPCMPACKET16STEREO()
	{
	}
	
	function packADPCMCODEDATA()
	{
	}
	
	function packMP3FRAME()
	{
	}
	
	function packMP3SOUNDDATA()
	{
	}
	
	function packMP3STREAMSOUNDDATA()
	{
	}
	
	function packACTIONRECORD()
	{
	}
	
	function packActionGotoFrame( $Frame )
	{
		$ActionID        = $this->packUI8( 0x81 );
		$ActionLength    = $this->packUI16( 2 );
		$Frame           = $this->packUI16( $Frame );
		$ActionGotoFrame = $ActionID . $ActionLength . $Frame;
		
		return $ActionGotoFrame;
	}
	
	function packActionGetURL( $URLString, $TargetString )
	{
		$ActionID     = $this->packUI8( 0x83 );
		$URLString    = $this->packSTRING( $URLString );
		$TargetString = $this->packSTRING( $TargetString );
		$ActionLength = $this->packUI16( strlen( $URLString . $TargetString ) );
		$ActionGetURL = $ActionID . $ActionLength . $URLString . $TargetString;
				
		return $ActionGetURL;
	}
	
	function packActionNextFrame()
	{
		$ActionID = $this->packUI8( 0x04 );
		$ActionNextFrame = $ActionID;
				
		return $ActionNextFrame;
	}
	
	function packActionPreviousFrame()
	{
		$ActionID = $this->packUI8( 0x05 );
		$ActionPrevFrame = $ActionID;
				
		return $ActionPrevFrame;
	}
	
	function packActionPlay()
	{
		$ActionID = $this->packUI8( 0x06 );
		$ActionPlay = $ActionID;
				
		return $ActionPlay;
	}
	
	function packActionStop()
	{
		$ActionID   = $this->packUI8( 0x07 );
		$ActionStop = $ActionID;
				
		return $ActionStop;
	}
	
	function packActionToggleQuality()
	{
		$ActionID = $this->packUI8( 0x08 );
		$ActionToggleQuality = $ActionID;
				
		return $ActionToggleQuality;
	}
	
	function packActionStopSounds()
	{
		$ActionID = $this->packUI8( 0x09 );
		$ActionStopSounds = $ActionID;
		
		return $ActionStopSounds;
	}
	
	function packActionWaitForFrame( $Frame, $SkipCount )
	{
		$ActionID           = $this->packUI8( 0x8A );
		$ActionLength       = $this->packUI16( 3 );
		$Frame              = $this->packUI16( $Frame );
		$SkipCount          = $this->packUI8( $SkipCount );
		$ActionWaitForFrame = $ActionID . $ActionLength . $Frame . $SkipCount;
				
		return $ActionWaitForFrame;
	}
	
	function packActionSetTarget( $Target )
	{
		$ActionID        = $this->packUI8( 0x8B );
		$Target          = $this->packSTRING( $Target );
		$ActionLength    = $this->packUI16( $Target );
		$ActionSetTarget = $ActionID . $ActionLength . $Target;
				
		return $ActionSetTarget;
	}
	
	function packActionGoToLabel( $Label )
	{
		$ActionID        = $this->packUI8( 0x8B );
		$Label           = $this->packSTRING( $Label );
		$ActionLength    = $this->packUI16( $Label );
		$ActionGotoLabel = $ActionID . $ActionLength . $Label;
				
		return $ActionGotoLabel;
	}
	
	function packActionPush( $Type, $Value )
	{
		$ActionID = $this->packUI8( 0x96 );
		
		if ( $Type == 0 ) 
		{
			$Type  = $this->packUI8( $Type );
			$Value = $this->packSTRING( $Value );
		} 
		else if ( $Type == 1 ) 
		{
			$Type  = $this->packUI8( $Type );
			$Value = $this->packFLOAT( $Value );
		}
		
		$ActionPush = $ActionID . $Type . $Value;
		return $ActionPush;
	}
	
	function packActionPop()
	{
		$ActionID  = $this->packUI8( 0x17 );
		$ActionPop = $ActionID;
				
		return $ActionPop;
	}
	
	function packActionAdd()
	{
		$ActionID  = $this->packUI8( 0x0A );
		$ActionAdd = $ActionID;
				
		return $ActionAdd;
	}
	
	function packActionSubtract()
	{
		$ActionID = $this->packUI8( 0x0B );
		$ActionSubtract = $ActionID;
				
		return $ActionSubtract;
	}
	
	function packActionMultiply()
	{
		$ActionID = $this->packUI8( 0x0C );
		$ActionMultiply = $ActionID;
				
		return $ActionMultiply;
	}
	
	function packActionDivide()
	{
		$ActionID = $this->packUI8( 0x0D );
		$ActionDivide = $ActionID;
				
		return $ActionDivide;
	}
	
	function packActionEquals()
	{
		$ActionID = $this->packUI8( 0x0E );
		$ActionEquals = $ActionID;
				
		return $ActionEquals;
	}
	
	function packActionLess()
	{
		$ActionID = $this->packUI8( 0x0F );
		$ActionLess = $ActionID;
			
		return $ActionLess;
	}
	
	function packActionAnd()
	{
		$ActionID  = $this->packUI8( 0x10 );
		$ActionAnd = $ActionID;
				
		return $ActionAnd;
	}
	
	function packActionOr()
	{
		$ActionID = $this->packUI8( 0x11 );
		$ActionOr = $ActionID;
				
		return $ActionOr;
	}
	
	function packActionNot()
	{
		$ActionID  = $this->packUI8( 0x12 );
		$ActionNot = $ActionID;
				
		return $ActionNot;
	}
	
	function packActionStringEquals()
	{
		$ActionID = $this->packUI8( 0x13 );
		$ActionStringEquals = $ActionID;
				
		return $ActionStringEquals;
	}
	
	function packActionStringLength()
	{
		$ActionID = $this->packUI8( 0x14 );
		$ActionStringLength = $ActionID;
		
		return $ActionStringLength;
	}
	
	function packActionStringAdd()
	{
		$ActionID = $this->packUI8( 0x21 );
		$ActionStringAdd = $ActionID;
				
		return $ActionStringAdd;
	}
	
	function packActionStringExtract()
	{
		$ActionID = $this->packUI8( 0x15 );
		$ActionStringExtract = $ActionID;
				
		return $ActionStringExtract;
	}
	
	function packActionStringLess()
	{
		$ActionID = $this->packUI8( 0x29 );
		$ActionStringLess = $ActionID;
			
		return $ActionStringLess;
	}
	
	function packActionMBStringLength()
	{
		$ActionID = $this->packUI8( 0x31 );
		$ActionStringLength = $ActionID;
			
		return $ActionStringLength;
	}
	
	function packActionMBStringExtract()
	{
		$ActionID = $this->packUI8( 0x35 );
		$ActionStringExtract = $ActionID;
			
		return $ActionStringExtract;
	}
	
	function packActionToInteger()
	{
		$ActionID = $this->packUI8( 0x18 );
		$ActionToInteger = $ActionID;
			
		return $ActionToInteger;
	}
	
	function packActionCharToAscii()
	{
		$ActionID = $this->packUI8( 0x32 );
		$ActionCharToASCII = $ActionID;
				
		return $ActionCharToASCII;
	}
	
	function packActionAsciiToChar()
	{
		$ActionID = $this->packUI8( 0x33 );
		$ActionASCIIToChar = $ActionID;
			
		return $ActionASCIIToChar;
	}
	
	function packActionMBCharToAscii()
	{
		$ActionID = $this->packUI8( 0x36 );
		$ActionMBCharToASCII = $ActionID;
				
		return $ActionMBCharToASCII;
	}
	
	function packActionMBAsciiToChar()
	{
		$ActionID = $this->packUI8( 0x37 );
		$ActionMBASCIIToChar = $ActionID;
				
		return $ActionMBASCIIToChar;
	}
	
	function packActionJump( $Offset )
	{
		$ActionID   = $this->packUI8( 0x99 );
		$Offset     = $this->packSI16( $Offset );
		$ActionJump = $ActionID . $Offset;
				
		return $ActionJump;
	}
	
	function packActionIf()
	{
		$ActionID = $this->packUI8( 0x9A );
		$Offset   = $this->packSI16( $Offset );
		$ActionIf = $ActionID . $Offset;
			
		return $ActionIf;
	}
	
	function packActionCall()
	{
		$ActionID   = $this->packUI8( 0x37 );
		$ActionCall = $ActionID;
			
		return $ActionCall;
	}
	
	function packActionGetVariables()
	{
	}
	
	function packActionSetVariables()
	{
	}
	
	function packActionGetURL2()
	{
	}
	
	function packActionGotoFrame2()
	{
	}
	
	function packActionSetTarget2()
	{
	}
	
	function packActionGetProperty()
	{
	}
	
	function packActionSetProperty()
	{
	}
	
	function packActionCloneSprite()
	{
	}
	
	function packActionRemoveSprite()
	{
	}
	
	function packActionStartDrag()
	{
	}
	
	function packActionEndDrag()
	{
	}
	
	function packWaitForFrame2()
	{
	}
	
	function packActionTrace()
	{
	}
	
	function packActionGetTime()
	{
	}
	
	function packActionRandomNumber()
	{
	}
	
	function packActionCallFunction()
	{
	}
	
	function packActionCallMethod()
	{
	}
	
	function packActionConstantPool()
	{
	}
	
	function packActionDefineFunction()
	{
	}
	
	function packActionDefineLocal()
	{
	}
	
	function packActionDefineLocal2()
	{
	}
	
	function packActionDelete()
	{
	}
	
	function packActionDelete2()
	{
	}
	
	function packActionEnumerate()
	{
	}
	
	function packActionEquals2()
	{
	}
	
	function packActionGetMember()
	{
	}
	
	function packActionInitArray()
	{
	}
	
	function packActionInitObject()
	{
	}
	
	function packActionNewMethod()
	{
	}
	
	function packActionNewObject()
	{
	}
	
	function packActionSetMember()
	{
	}
	
	function packActionTargetPath()
	{
	}
	
	function packActionWith()
	{
	}
	
	function packActionToNumber()
	{
	}
	
	function packActionToString()
	{
	}
	
	function packActionTypeOf()
	{
	}
	
	function packActionAdd2()
	{
	}
	
	function packActionLess2()
	{
	}
	
	function packActionModulo()
	{
	}
	
	function packActionBitAnd()
	{
	}
	
	function packActionBitLShift()
	{
	}
	
	function packActionBitOr()
	{
	}
	
	function packActionBitRShift()
	{
	}
	
	function packActionBitURShift()
	{
	}
	
	function packActionBitXor()
	{
	}
	
	function packActionDecrement()
	{
	}
	
	function packActionIncrement()
	{
	}
	
	function packActionPushDuplicate()
	{
	}
	
	function packActionReturn()
	{
	}
	
	function packActionStackSwap()
	{
	}
	
	function packActionStoreRegister()
	{
	}
	
	function packBUTTONRECORD()
	{
	}
	
	function packDefineButtonxform()
	{
	}
	
	function packDefineButtonSound()
	{
	}
	

	// tags
	
	/**
	 * null AutoSetSWFVersion(integer version)
	 *
	 * Sets the SWF file version number to version.
	 * NOTE: don't call this function directly.
	 */ 
	function AutoSetSWFVersion( $version )
	{
		if ( $this->SWFVersion < $version )
			$this->SWFVersion = (int) $version;
	} 
	
	/**
	 * string packRECORDHEADER(integer TagID, integer TagLength)
	 *
	 * Returns the SWF RECORDHEADER string.
	 */
	function packRECORDHEADER( $TagID, $TagLength )
	{
		$lower_limit           = 0;
		$upper_short_tag_limit = 62;
		$upper_long_tag_limit  = 2147483647;
        
		if ( !( is_integer( $TagLength ) ) )
       		return PEAR::raiseError( "packRECORDHEADER argument (TagLength) not an integer." );
        	
        if ( $TagLength < $lower_limit )
         	return PEAR::raiseError( "packRECORDHEADER argument (TagLength) negative." );
        	
        if ( $TagLength > $upper_short_tag_limit ) 
		{
			if ( $TagLength > $upper_long_tag_limit ) 
			{
                return PEAR::raiseError( "packRECORDHEADER argument (TagLength) out of range." );
			} 
			else 
			{
				$atom  = $TagID << 6;
				$atom += 0x3f;
				$atom  = $this->packUI16( $atom );
				$atom .= $this->packUI32( $TagLength );
			}
        } 
		else 
		{
			$atom  = $TagID << 6;
			$atom += $TagLength;
			$atom  = $this->packUI16( $atom );
		}
	
		return $atom;
	}
	
	/**
	 * string packEndTag()
	 *
	 * Returns an SWF End Tag string.
	 * TagID: 0
	 */ 
	function packEndTag()
	{
		$TagID = 0;
		$TagLength = 0;
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength );
	}
	
	/**
	 * string packShowFrameTag() 
	 *
	 * Returns an SWF ShowFrameTag string.
	 * TagID: 1 
	 */
	function packShowFrameTag()
	{
		$TagID = 1;
		$TagLength = 0;
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength );
	}
	
	/**
	 * null packDefineShapeTag(integer ShapeID, string ShapeBounds, string SHAPEWITHSTYLE) 
	 *
	 * Returns an SWF DefineShapeTag string.
	 * TagID: 2 
	 */
	function packDefineShapeTag( $ShapeID, $ShapeBounds, $SHAPEWITHSTYLE )
	{
		$TagID = 2;
		$DefineShapeTag = $this->packUI16( $ShapeID ) . $ShapeBounds . $SHAPEWITHSTYLE;
		$TagLength = strlen( $DefineShapeTag );
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $DefineShapeTag;
	}
	
	/**
	 * string packPlaceObjectTag(integer CharacterID, integer Depth, string MATRIX, string CXFORM) 
	 * 
	 * Rturn an SWF PlaceObject tag string.
	 * TagID: 4
	 */
	function packPlaceObjectTag( $CharacterID, $Depth, $MATRIX, $CXFORM )
	{
		$TagID = 4;
		$CharacterID = $this->packUI16( $CharacterID );
		$Depth       = $this->packUI16( $Depth );
		$TagLength   = strlen( $CharacterID . $Depth . $MATRIX . $CXFORM );
	
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $CharacterID . $Depth . $MATRIX . $CXFORM;
	}
	
	/**
	 * string packRemoveObjectTag(integer CharacterID, integer Depth)
	 *
	 * Returns an SWF RemoveObject tag string.
	 * TagID: 5 
	 */
	function packRemoveObjectTag( $CharacterID, $Depth )
	{
		$TagID = 5;
		$CharacterID = $this->packUI16( $CharacterID );
		$Depth       = $this->packUI16( (int)$Depth  );
		$TagLength   = strlen( $CharacterID . $Depth );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $CharacterID . $Depth;
	}
	
	/**
	 * string packDefineBitsTag(integer BitmapID, string BitmapJPEGImage)
	 *
	 * Return an SWF DefineBits tag string.
	 * TagID: 6
	 */
	function packDefineBitsTag( $CharacterID, $BitmapJPEGImage )
	{
		$TagID = 6;
		$BitmapID  = $this->packUI16( $CharacterID );
		$TagLength = strlen( $BitmapID . $BitmapJPEGImage );
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $BitmapID . $BitmapJPEGImage;
	}
	
	function packDefineButtonTag()
	{
	}
	
	function packDefineButton2Tag()
	{
	}
	
	/**
	 * string packJPEGTablesTag(string BitmapJPEGEncoding)
	 *
	 * Returns an SWF JPEGTablesTag string.
	 * TagID: 8 
	 */
	function packJPEGTablesTag( $BitmapJPEGEncoding )
	{
		$TagID = 8;
		$TagLength = strlen( $BitmapJPEGEncoding );
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $BitmapJPEGEncoding;
	}
	
	/**
	 * string packSetBackgroundColorTag(integer R, integer G, integer B)
	 *
	 * Return an SWF SetBackgroundColorTag string.
	 * TagID: 9
	 */
	function packSetBackgroundColorTag( $R, $G, $B )
	{
		$TagID = 9;
		$RGB   = $this->packRGB( $R, $G, $B );
		$TagLength = strlen( $RGB );
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $RGB;
	}
	
	function packDoActionTag()
	{
	}
	
	/**
	 * string packDefineSoundTag(integer CharacterID, 
	 *				integer SoundFormat, integer SoundRate,
	 *				integer SoundSize, integer SoundType, 
	 *				integer SoundSampleCount, string SoundFile)
	 *
	 * Returns an SWFDefineSoundTag string.
	 * TagID: 14 
	 */
	function packDefineSoundTag( $CharacterID, $SoundFormat, $SoundRate, $SoundSize, $SoundType, $SoundSampleCount, $SoundFile )
	{
		$TagID = 14;

		$DefineSoundTag  = $this->packnBits( $SoundFormat, 4 );
		$DefineSoundTag .= $this->packnBits( $SoundRate, 2 );
		$DefineSoundTag .= $this->packnBits( $SoundSize, 1 );
		$DefineSoundTag .= $this->packnBits( $SoundType, 1 );
		$DefineSoundTag  = $this->packBitValues( $DefineSoundTag );
		$DefineSoundTag  = $this->packUI16( $CharacterID) . $DefineSoundTag;
		$DefineSoundTag .= $this->packUI32( $SoundSampleCount );
		
		$file_handle = fopen( $SoundFile, "r" );
		$file = fread( $file_handle, filesize( $SoundFile ) );
		fclose( $file_handle );
		
		if ( $SoundFormat == 2 )
			$DefineSoundTag .= $this->packUI16( 10 ) . $file;
		
		$TagLength = strlen( $DefineSoundTag );
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $DefineSoundTag;
	}
	
	/**
	 * string packStartSoundTag(integer CharacterID, string SOUNDINFO)
	 *
	 * Returns an SWFDefineSoundTag string.
	 * TagID: 15 
	 */
	function packStartSoundTag( $CharacterID, $SOUNDINFO )
	{
		$TagID = 15;
		$StartSoundTag = $this->packUI16( $CharacterID ) . $SOUNDINFO;
		$TagLength     = strlen( $StartSoundTag );
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $StartSoundTag;
	}
	
	function packSoundStreadmingHeadTag()
	{
	}
	
	function packSoundStreamingHead2Tag()
	{
	}
	
	function packSoundStreamBlockTag()
	{
	}
	
	/**
	 * string packDefineBitsLosslessTag(
	 *				integer BitmapID, integer BitmapID,
	 *				integer BitmapFormat, integer BitmapWidth,
	 *				integer BitmapHeight, 
	 *				integer BitmapColorTableSize,
	 *				string ZlibBitmapData)
	 *
	 * Return an SWF DefineBitsLossless tag string.
	 * TagID: 20 
	 */
	function packDefineBitsLosslessTag( $BitmapID, $BitmapFormat, $BitmapWidth, $BitmapHeight, $BitmapColorTableSize, $ZlibBitmapData )
	{
		$TagID = 20;
		$BitmapID     = $this->packUI16( $BitmapID );
		$BitmapWidth  = $this->packUI16( $BitmapWidth );
		$BitmapHeight = $this->packUI16( $BitmapHeight );
	
		switch ( $BitmapFormat ) 
		{
			case 3:
				$BitmapColorTableSize = $this->packUI8( $BitmapColorTableSize ); 
				break;
	
			case 4:
				$BitmapColorTableSize = $this->packUI16( $BitmapColorTableSize ); 
				break;
				
			case 5:
				$BitmapColorTableSize = $this->packUI32( $BitmapColorTableSize ); 
				break;
				
			default:
				return PEAR::raiseError( "packDefineBitsLosslessTag illegal argument (BitmapFormat)." );
		}
		
		$BitmapFormat = $this->packUI8( $BitmapFormat );
		$TagLength    = strlen( $BitmapID . $BitmapFormat . $BitmapWidth . $BitmapHeight . $BitmapColorTableSize . $ZlibBitmapData );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $BitmapID . $BitmapFormat . $BitmapWidth . $BitmapHeight . $BitmapColorTableSize . $ZlibBitmapData;
	}
	
	/**
	 * string packDefineBitsJPEG2Tag(integer BitmapID, 
	 *				string BitmapJPEGEncoding,
	 *				string BitmapJPEGImage)
	 *
	 * Return an SWF DefineBitsJPEG2 tag string.
	 * TagID: 21 
	 */
	function packDefineBitsJPEG2Tag( $BitmapID, $BitmapJPEGEncoding, $BitmapJPEGImage )
	{
		$TagID     = 21;
		$BitmapID  = $this->packUI16( $BitmapID );
		$TagLength = strlen( $BitmapID . $BitmapJPEGEncoding . $BitmapJPEGImage );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $BitmapID . $BitmapJPEGEncoding . $BitmapJPEGImage;
	}
	
	/**
	 * null packDefineShapeTag2(integer ShapeID, 
	 *				string ShapeBounds, string SHAPEWITHSTYLE)
	 *
	 * Returns an SWF DefineShapeTag string.
	 * TagID: 22 
	 */
	function packDefineShape2Tag( $ShapeID, $ShapeBounds, $SHAPEWITHSTYLE )
	{
		$TagID = 22;
		$DefineShapeTag = $this->packUI16( $ShapeID ) . $ShapeBounds . $SHAPEWITHSTYLE;
		$TagLength      = strlen( $DefineShapeTag );
		
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $DefineShapeTag;
	}
	
	/**
	 * string packProtectTag(string Password)
	 *
	 * Returns an SWF Protect tag string.
	 * TagID: 24
	 */
	function packProtectTag( $Password )
	{
		$TagID = 24;
		
		if ( !( $Password == "" ) )
			$Password = $this->packSTRING( bin2hex( mhash( MHASH_MD5, $Password ) ) );
		
		$TagLength = strlen( $Password );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $Label;
	}
	
	/**
	 * string packPlaceObject2Tag(integer CharacterID,
	 *				integer Depth, string MATRIX, string CXFORM,
	 *				string MATRIX, string CXFORM, integer Ratio,
	 *				string Name, string ClipActions) 
	 *
	 * Return an SWF PlaceObject2 tag string.
	 * TagID: 26 
	 */
	function packPlaceObject2Tag($PlaceFlagMove, $PlaceFlagHasCharacter, $CharacterID, $Depth, $MATRIX, $CXFORM, $Ratio, $Name, $ClipActions)
	{
		$TagID = 26;
	
		$PlaceFlagHasClipActions    = "0";
		$PlaceFlagReserved          = "0";
		$PlaceFlagHasName           = "0";
		$PlaceFlagHasRatio          = "0";
		$PlaceFlagHasColorTransform = "0";
		$PlaceFlagHasMatrix         = "0";
	
		$payload = "";
	
		if ( $PlaceFlagMove )
			$PlaceFlagMove = "1";
		else
			$PlaceFlagMove = "0";

		if ( ( $PlaceFlagHasCharacter ) && ( $CharacterID != null ) ) 
		{
			$PlaceFlagHasCharacter = "1";
			$payload .= $this->packUI16( $CharacterID );
		} 
		else 
		{
			$PlaceFlagHasCharacter = "0";
		}
		
		if ( !( $MATRIX == "" ) ) 
		{
			$PlaceFlagHasMatrix = "1";
			$payload .= $MATRIX;
		}
		
		if ( !( $CXFORM == "" ) ) 
		{
			$PlaceFlagHasColorTransform = "1";
			$payload .= $CXFORM;
		}
		
		if ( !( $Ratio == null ) ) 
		{
			$PlaceFlagHasRatio = "1";
			$payload .= $this->packUI16( $Ratio );
		}
		
		if ( !( $Name == null ) ) 
		{
			$PlaceFlagHasName = "1";
			$payload .= $this->packSTRING( $Name );
		}
		
		if ( !( $ClipActions == null ) ) 
		{
			$PlaceFlagHasClipActions = "1";
			$payload .= $ClipActions;
		}
		
		$PlaceFlags = $PlaceFlagHasClipActions . $PlaceFlagReserved . $PlaceFlagHasName . $PlaceFlagHasRatio . $PlaceFlagHasColorTransform . $PlaceFlagHasMatrix . $PlaceFlagHasCharacter . $PlaceFlagMove;
		$payload    = $this->packBitValues( $PlaceFlags ) . $this->packUI16( $Depth ). $payload;
		$TagLength  = strlen( $payload );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $payload;
	}
	
	/**
	 * string packRemoveObject2Tag(integer Depth)
	 *
	 * Returns an SWF RemoveObject2 tag string.
	 * TagID: 28 
	 */
	function packRemoveObject2Tag( $Depth )
	{
		$TagID = 28;
		$Depth = $this->packUI16( $Depth );
		$TagLength = strlen( $Depth );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $Depth;
	}
	
	/**
	 * null packDefineShapeTag3(integer ShapeID, string ShapeBounds, string SHAPEWITHSTYLE) 
	 *
	 * Returns an SWF DefineShapeTag string.
	 * TagID: 32 
	 */
	function packDefineShape3Tag( $ShapeID, $ShapeBounds, $SHAPEWITHSTYLE )
	{
		$TagID = 32;
		$DefineShapeTag = $this->packUI16( $ShapeID ) . $ShapeBounds . $SHAPEWITHSTYLE;
		$TagLength = strlen( $DefineShapeTag );
		$this->AutoSetSWFVersion( 1 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $DefineShapeTag;
	}
	
	/**
	 * string packDefineBitsJPEG3Tag(integer BitmapID, 
	 *				string BitmapJPEGEncoding, 
	 *				string BitmapJPEGImage, string BitmapAlphaData)
	 *
	 * Return an SWF DefineBitsJPEG3 tag string.
	 * TagID: 35 
	 */
	function packDefineBitsJPEG3Tag( $BitmapID, $BitmapJPEGEncoding, $BitmapJPEGImage, $BitmapAlphaData )
	{
		$TagID = 35;
		$BitmapID  = $this->packUI16( $BitmapID );
		$Offset    = $this->packUI32( strlen( $BitmapJPEGEncoding . $BitmapJPEGImage ) );
		$TagLength = strlen( $BitmapID . $Offset . $BitmapJPEGEncoding . $BitmapJPEGImage . $BitmapAlphaData );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $BitmapID . $Offset . $BitmapJPEGEncoding . $BitmapJPEGImage . $BitmapAlphaData;
	}
	
	/**
	 * string packDefineBitsLossless2Tag(
	 *				integer BitmapID, integer BitmapID,
	 *				integer BitmapFormat, integer BitmapWidth,
	 *				integer BitmapHeight, 
	 *				integer BitmapColorTableSize,
	 *				string ZlibBitmapData)
	 *
	 * Return an SWF DefineBitsLossless2 tag string.
	 * TagID: 36 
	 */
	function packDefineBitsLossless2Tag( $BitmapID, $BitmapFormat, $BitmapWidth, $BitmapHeight, $BitmapColorTableSize, $ZlibBitmapData2 )
	{
		$TagID = 36;
		
		$BitmapID     = $this->packUI16( $BitmapID );
		$BitmapWidth  = $this->packUI16( $BitmapWidth );
		$BitmapHeight = $this->packUI16( $BitmapHeight );
	
		switch ( $BitmapFormat ) 
		{
			case 3:
				$BitmapColorTableSize = $this->packUI8( $BitmapColorTableSize ); 
				break;
	
			case 4:
				$BitmapColorTableSize = $this->packUI16( $BitmapColorTableSize ); 
				break;
			case 5:
				$BitmapColorTableSize = $this->packUI32( $BitmapColorTableSize ); 
				break;
				
			default:
				return PEAR::raiseError( "packDefineBitsLosslessTag illegal argument (BitmapFormat)." );
		}
		
		$BitmapFormat = $this->packUI8( $BitmapFormat );
		$TagLength    = strlen( $BitmapID . $BitmapFormat . $BitmapWidth . $BitmapHeight . $BitmapColorTableSize . $ZlibBitmapData2 );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $BitmapID . $BitmapFormat . $BitmapWidth . $BitmapHeight . $BitmapColorTableSize . $ZlibBitmapData2;
	}
	
	/**
	 * string packFrameLabelTag(string Label)
	 *
	 * Returns an SWF FrameLabel tag string.
	 * TagID: 43
	 */
	function packFrameLabelTag( $Label )
	{
		$TagID = 43;
		$Label = $this->packSTRING( $Label );
		$TagLength = strlen( $Label );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $Label;
	}
	
	/**
	 * null packDefineMorphShapeTag(integer ShapeID, 
	 *				string FromShapeBounds, stringToShapeBounds,
	 *				string MorphFillStyles,
	 *				string MorphLineStyles, string FromShape,
	 *				string ToShape) 
	 *
	 * Returns an SWF DefineMorphShapeTag string.
	 * TagID: 46 
	 */
	function packDefineMorphShapeTag( $ShapeID, $FromShapeBounds, $ToShapeBounds, $MorphFillStyles, $MorphLineStyles, $FromShape, $ToShape )
	{
		$TagID = 46;
		$DefineMorphShapeTag = $this->packUI16( $ShapeID ) . $FromShapeBounds . $ToShapeBounds . $this->packUI32( strlen( $MorphFillStyles . $MorphLineStyles . $FromShape ) ) . $MorphFillStyles . $MorphLineStyles . $FromShape . $ToShape;
		$TagLength = strlen( $DefineMorphShapeTag );
		$this->AutoSetSWFVersion( 5 );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $DefineMorphShapeTag;
	}
	
	/**
	 * string packExportAssetsTag(string AssetList)
	 *
	 * Returns an SWF EnableDebugger tag string.
	 * TagID: 56
	 */
	function packExportAssetsTag( $AssetList )
	{
		$TagID = 56;
		$AssetCount = substr_count( $AssetList, chr( 0 ) );
		$TagLength  = strlen( $AssetCount . $AssetList );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $AssetCount . $AssetList;
	}
	
	/**
	 * string packImportAssetsTag(string URL, string AssetList)
	 *
	 * Returns an SWF EnableDebugger tag string.
	 * TagID: 57
	 */
	function packImportAssetsTag( $URL, $AssetList )
	{
		$TagID = 57;
		$URL   = $this->packSTRING( $URL );
		$AssetCount = substr_count( $AssetList, chr( 0 ) ) - 1;
		$TagLength  = strlen( $URL, $AssetCount . $AssetList );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $URL . $AssetCount . $AssetList;
	}
	
	/**
	 * string packEnableDebuggerTag(string Password)
	 *
	 * Returns an SWF EnableDebugger tag string.
	 * TagID: 58
	 */
	function packtectEnableDebuggerTag( $Password )
	{
		$TagID = 58;
		
		if ( !($Password == "" ) )
			$Password = $this->packSTRING( bin2hex( mhash( MHASH_MD5, $Password ) ) );
		
		$TagLength = strlen( $Password  );
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $Label;
	}
	
	/**
	 * string packDefineBitsPtrTag(integer Pointer)
	 *
	 * Returns an SWF DefineBitsPtr tag string.
	 * TagID:1023 
	 */
	function packDefineBitsPtrTag( $Pointer )
	{
		$TagID     = 1023;
		$TagLength = strlen( $this->packUI32( $Pointer ) );
	
		$this->MovieData .= $this->packRECORDHEADER( $TagID, $TagLength ) . $Label;
	}
	
	/**
	 * string packMacromediaFlashSWFHeader()
	 *
	 * Returns the Macromedia Flash SWF Header string.
	 */
	function packMacromediaFlashSWFHeader()
	{
		$HeaderLength = 21;
		$atom  = "FWS";
		$atom .= $this->packUI8( (int)$this->SWFVersion );
		$atom .= $this->packUI32( $HeaderLength + strlen( $this->MovieData ) );
		$Xmin  = (int)$this->FrameSize["Xmin"]; 
		$Xmax  = (int)$this->FrameSize["Xmax"]; 
		$Ymin  = (int)$this->FrameSize["Ymin"]; 
		$Ymax  = (int)$this->FrameSize["Ymax"];

		if ( min( $Xmax, $Ymax ) < 360 )
			return PEAR::raiseError( "packMacromediaFlashSWFHeader movie frame too small." );

		if ( max( $Xmax, $Ymax ) > 57600 )
			return PEAR::raiseError( "packMacromediaFlashSWFHeader movie frame too large." );
		
		$Xmin   = $this->packUBchunk( $Xmin ); 
		$Xmax   = $this->packUBchunk( $Xmax ); 
		$Ymin   = $this->packUBchunk( $Ymin ); 
		$Ymax   = $this->packUBchunk( $Ymax );
		$nBits  = 16;
		$Xmin   = str_repeat( "0", ( $nBits - strlen( $Xmin ) ) ) . $Xmin;
		$Xmax   = str_repeat( "0", ( $nBits - strlen( $Xmax ) ) ) . $Xmax;
		$Ymin   = str_repeat( "0", ( $nBits - strlen( $Ymin ) ) ) . $Ymin;
		$Ymax   = str_repeat( "0", ( $nBits - strlen( $Ymax ) ) ) . $Ymax;
		$RECT   = $this->packnBits( $nBits, 5 ) . $Xmin . $Xmax . $Ymin . $Ymax;
		$atom  .= $this->packBitValues( $RECT );
		$atom  .= $this->packFIXED8( (float)$this->FrameRate );
		$atom  .= $this->packUI16( (int)$this->FrameCounter );
		
		$this->MovieData = $atom . $this->MovieData;
	}
} // END OF SWF

?>
