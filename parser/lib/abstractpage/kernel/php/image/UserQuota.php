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
|Authors: Markus Nix <mnix@docuverse.de>                               |
|         Andrew Collington <php@amnuts.com>                           |
+----------------------------------------------------------------------+
*/


/**
 * This is a class to allow a graphical or text display of a user's quota.
 * The quota and how much has been used is passed to the class and the output
 * is display as either a graphic or as text.  The graphic is a pie chart and
 * can be displayed in a few different ways.
 * 
 * Usage:
 * 
 * $uq = new UserQuota(24000000, 5087643);
 * $uq->setOutputImage();
 * $uq->setImageProperties( 130, true, true, false, 'DDDDDD', 'ED1C24', 'FFFFFF', '555555' );
 * $uq->displayQuota();
 *
 * @package image
 */

class UserQuota extends PEAR
{
	/**
	 * output type
	 * 
	 * @access public
	 */
    var $qType;
	
	/**
	 * quota value
	 * 
	 * @access public
	 */
    var $qQuota;
	
	/**
	 * used value
	 * 
	 * @access public
	 */
    var $qUsed;
	
	/**
	 * input user name
	 * 
	 * @access public
	 */
    var $qUser;

	/**
	 * width of pie chart
	 * 
	 * @access public
	 */
    var $gWidth;
	
	/**
	 * height of pie chart (determined by width)
	 * 
	 * @access public
	 */
    var $gHeight;
	
	/**
	 * do we want a 3D look or not
	 * 
	 * @access public
	 */
    var $g3DHeight;
	
	/**
	 * display the legend or not
	 * 
	 * @access public
	 */
    var $gLegend;
	
	/**
	 * colour of quota
	 * 
	 * @access public
	 */
    var $gQuotaColour;
	
	/**
	 * colour of used space
	 * 
	 * @access public
	 */
    var $gUsedColour;
	
	/**
	 * colour of the background
	 * 
	 * @access public
	 */
    var $gBackColour;
	
	/**
	 * colour of the legend text
	 * 
	 * @access public
	 */
    var $gTextColour;
	
	/**
	 * centre the 'used' wedge to bottom of pie
	 * 
	 * @access public
	 */
    var $gCentreUsed;


    /**
	 * Constructor
	 *
     * $quota is the total quota the user has supplied as just a number
     * $used is how much the user has taken up supplied as just a number
     * $username, if supplied, will be displayed on the image legend or in the text output
	 *
	 * @access public
     */
    function UserQuota( $quota, $used, $username = '' )
    {
        $this->qUser = $username;
		
        $this->setOutputImage();
        $this->setImageProperties();

        $this->qQuota   = $quota;
        $this->qUsed    = $used;
        $this->oPercent = ( $this->qUsed / ( !$this->qQuota? 1 : $this->qQuota ) ) * 100;
        $this->qUser    = $username;
		
        if ( $this->oPercent > 100 )
            $this->oPercent = 100;
    }
    
	
    /**
	 * Set the class to output only in text mode.
	 *
	 * @access public
	 */
    function setOutputText()
    {
        $this->qType = 0;
    }
    
	/**
	 * Set the class to output in graphical mode.
	 *
	 * @access public
	 */
    function setOutputImage()
    {
        $this->qType = 1;
    }
    
    /**
     * Determine the image properties for the class.
     *
     * $width is the width of the graph in pixels, supplied as an int
     * $threeD is a boolean value.  true shows graph in faux 3D and false shows graph flat
     * $legend is a boolean value.  true shows legend and false does not
     * $centre is a boolean value.  true shows used wedge centered in chart and false defaults to the right
     *
     * all colours are supplied as HTML hex values
     *
     * $qc is the quota colour
     * $uc is the used colour
     * $bc is the background colour
     * $tc is the legend text colour
     *
	 * @access public
	 */
    function setImageProperties( $width = 150, $threeD = true, $legend = true, $centre = true, $qc = 'DDDDDD', $uc = 'ED1C24', $bc = 'FFFFFF', $tc = '000000' )
    {
        $this->gWidth = ( !$width? 150 : $width );
		
        if ( $threeD )
        {
            $this->gHeight   = $width / 2;
            $this->g3DHeight = $width / 10;
        }
        else
        {
            $this->gHeight   = $width;
            $this->g3DHeight = 0;
        }
		
        $this->gLegend      = ( $legend == true? true : false );
        $this->gCentreUsed  = ( $centre == true? true : false );
		
        $this->gQuotaColour = $this->_htmlHexToBinArray( $qc );
        $this->gUsedColour  = $this->_htmlHexToBinArray( $uc );
        $this->gBackColour  = $this->_htmlHexToBinArray( $bc );
        $this->gTextColour  = $this->_htmlHexToBinArray( $tc );
    }

    /**
	 * Show the quota output.
	 *
	 * @access public
	 */
    function displayQuota()
    {
        ( $this->qType == 0 )? $this->_displayText() : $this->_displayImage();
    }
    
    
	// private methods

    /**
	 * onvert HTML hex value into integer array.
	 *
	 * @access private
     */
    function _htmlHexToBinArray( $hex )
    {
        for ( $i = 0; $i < 3; $i++ )
        {
            $foo = substr( $hex, 2 * $i, 2 ); 
            $rgb[$i] = 16 * hexdec( substr( $foo, 0, 1 ) ) + hexdec( substr( $foo, 1, 1 ) ); 
        }
		
        return $rgb;
    }

	/**
	 * Output the quota text.
	 *
	 * @access private
     */
    function _displayText()
    {
        if ( $this->qUser )
		{
			echo 'The quota for ', $this->qUser;
		}
        else
		{
			echo 'Your quota';
        	echo ' is ', $this->_formatSize( $this->qQuota ), ' and ', 
            	( $this->qUser? 'they' : 'you' ), ' have used ',
            	$this->_formatSize( $this->qUsed ), ' (',
            	number_format( $this->oPercent, 2 ), '%) of it.';
		}
    }

	/**
	 * Output the quota graph.
	 *
	 * @access private
     */
    function _displayImage()
    {
        // the graph variables
        $sStart = $this->g3DHeight * 2;
        $wStart = $this->gWidth    * 2;
        $hStart = $this->gHeight   * 2;
        
		if ( $this->qUsed >= $this->qQuota ) 
			$usedPercent = 359;
        else 
			$usedPercent = $this->oPercent * 3.6;

        // work out where the 'used' wedge will be located
        if ( $this->gCentreUsed )
        {
            $sWedge = (int)( 90 - ( $usedPercent / 2 ) );
            
			if ( $sWedge < 0 ) 
				$sWedge += 360;
            
			$mWedge = (int)( 90 + ( $usedPercent / 2 ) );
            $mWedge = ( $mWedge == 90? 91 : $mWedge );
            $eWedge = (int)$sWedge;
        }
        else
        {
            $sWedge = 0;
            $mWedge = (int)( $usedPercent? $usedPercent : 1 );
            $eWedge = 360;
        }

        // setup image and main colours
        $im = @imagecreatetruecolor( $wStart, $hStart + $sStart );
		
        if ( $im )
        {
            $cBg    = imagecolorallocate( $im, $this->gBackColour[0],  $this->gBackColour[1],  $this->gBackColour[2]  );
            $cUsed  = imagecolorallocate( $im, $this->gUsedColour[0],  $this->gUsedColour[1],  $this->gUsedColour[2]  );
            $cQuota = imagecolorallocate( $im, $this->gQuotaColour[0], $this->gQuotaColour[1], $this->gQuotaColour[2] );

            imagefill( $im, 0, 0, $cBg );

            // work out 3D look if needs be
            if ( $this->g3DHeight )
            {
                // process colours
                $qDarkArray = $this->gQuotaColour;
                
				for ( $i = 0; $i < 3; $i++ )
                    ( $qDarkArray[$i] > 99 )? $qDarkArray[$i] -= 100 : $qDarkArray[$i] = 0;
				
                $uDarkArray = $this->gUsedColour;

                for ( $i = 0; $i < 3; $i++ )
                    ( $uDarkArray[$i] > 99 )? $uDarkArray[$i] -= 100 : $uDarkArray[$i] = 0;
                
                $cQuotaDark = imagecolorallocate( $im, $qDarkArray[0], $qDarkArray[1], $qDarkArray[2] );
                $cUsedDark  = imagecolorallocate( $im, $uDarkArray[0], $uDarkArray[1], $uDarkArray[2] );

                // add 3D look
                $shadow_start = ( $hStart / 2 ) + $sStart;
                $shadow_end   = $hStart / 2;

                for ( $i = $shadow_start; $i>$shadow_end; $i--)
                {
                    imagefilledarc( $im, ( $wStart / 2 ), $i, $wStart, $hStart, $sWedge, $mWedge, $cUsedDark,  IMG_ARC_PIE );
                    imagefilledarc( $im, ( $wStart / 2 ), $i, $wStart, $hStart, $mWedge, $eWedge, $cQuotaDark, IMG_ARC_PIE );
                }
            }

            // now do the top of the graph
            imagefilledarc( $im, ( $wStart / 2 ), ( $hStart / 2 ), $wStart, $hStart, $sWedge, $mWedge, $cUsed,  IMG_ARC_PIE );
            imagefilledarc( $im, ( $wStart / 2 ), ( $hStart / 2 ), $wStart, $hStart, $mWedge, $eWedge, $cQuota, IMG_ARC_PIE );

            // now create a legend image if needs be
            if ( $this->gLegend )
            {
                // the legend variables
                $lHeight = 0;
				$lWidth  = 0;
                $spacer  = 10;

                // build quota strings
                $qText[0] = 'Quota: ' . $this->_formatSize( $this->qQuota );
				
                if ( $this->qUser ) 
					$qText[1] = '       (' . $this->qUser . ')';
                else 
					$qText[1] = '';
                
				$uText[0] = 'Used : '  . $this->_formatSize( $this->qUsed );
                $uText[1] = '       (' . number_format( $this->oPercent, 2 ) . '%)';

                // space + line + spacer + line
                $lHeight = ( imagefontheight( 2 ) * ( $qText[1] == ''? 3 : 4 ) ) + $spacer;

                // get biggest string length and add spacer to it - legend block is size of font height (square)
                $qMax   = ( strlen( $qText[0] ) > strlen( $qText[1] ) )? strlen( $qText[0] ) : strlen( $qText[1] );
                $uMax   = ( strlen( $uText[0] ) > strlen( $uText[1] ) )? strlen( $uText[0] ) : strlen( $uText[1] );
                $tMax   = ( $qMax > $uMax? $qMax : $uMax);
                $lWidth = ( $tMax * imagefontwidth( 2 ) ) + $spacer + imagefontheight( 2 );

                // now create the image
                $lim = imagecreatetruecolor( $lWidth, $lHeight );
                ImageFill( $lim, 0, 0, $cBg );
				
                $cText = imagecolorallocate( $lim, $this->gTextColour[0], $this->gTextColour[1], $this->gTextColour[2] );
                $lx    = 0;
                $ly    = 0;

                // write out the 'quota' legend
                imagefilledrectangle( $lim, $lx, $ly, ( $lx + imagefontheight( 2 ) ), ( $ly + imagefontheight( 2 ) ), $cQuota );
                imagestring( $lim, 2, ( $lx + imagefontheight( 2 ) + $spacer ), $ly, $qText[0], $cText );
				
                if ( $qText[1] != '' )
                {
                    $ly += imagefontheight( 2 );
                    imagestring( $lim, 2, ( $lx + imagefontheight( 2 ) + $spacer ), $ly, $qText[1], $cText );
                }

                $ly += ( $spacer + imagefontheight( 2 ) );

                // write out the 'used' legend
                imagefilledrectangle( $lim, $lx, $ly, ( $lx + imagefontheight( 2 ) ), ( $ly + imagefontheight( 2 ) ), $cUsed );
                imagestring( $lim, 2, ( $lx + imagefontheight( 2 ) + $spacer ), $ly, $uText[0], $cText );
                $ly += imagefontheight( 2 );
                imagestring( $lim, 2, ( $lx + imagefontheight( 2 ) + $spacer ), $ly, $uText[1], $cText );

                // now merge the two images into the final one

                // anti-aliasing look
                $gsx   = imagesx( $im  );
                $gsy   = imagesy( $im  );
                $lsx   = imagesx( $lim );
                $lsy   = imagesy( $lim );
                $gnx   = ( $gsx >> 1 );
                $gny   = ( $gsy >> 1 );
                $fx    = ( $gnx > $lsx )? $gnx : $lsx;
                $fy    = $gny + $lsy + ( $spacer * 2 );
				$final = imagecreatetruecolor( $fx, $fy );
                
				imagefill( $final, 0, 0, $cBg );
                imagecopyresampled( $final, $im,  ( ( $fx / 2 ) - ( $gnx / 2 ) ), 0, 0, 0, $gnx, $gny, $gsx, $gsy );
                imagecopyresampled( $final, $lim, ( ( $fx / 2 ) - ( $lsx / 2 ) ), $gny + ( $spacer * 2 ), 0, 0, $lsx, $lsy, $lsx, $lsy );
                imagedestroy( $lim );
            }
            else
            {
                // we do not have a legend, so just reample graph
                $sx    = imagesx( $im ); 
                $sy    = imagesy( $im ); 
                $nx    = ( $sx >> 1 ); 
                $ny    = ( $sy >> 1 );
                $final = imagecreatetruecolor( $nx, $ny );
				
                imagecopyresampled( $final, $im, 0, 0, 0, 0, $nx, $ny, $sx, $sy );
            }

            // flush image
            header( "Content-type: image/jpeg" );
            imagejpeg( $final, null, 100 );
 
            imagedestroy( $im );
            imagedestroy( $final );
        }
    }

    /**
	 * Make the size of the quota values more human readable.
	 *
	 * @access private
	 */
    function _formatSize( $size = 0 )
    {
        if ( $size >= 1073741824 ) 
			$size = round( $size / 1073741824 * 100 ) / 100 . " Gb";
        else if ( $size >= 1048576 ) 
			$size = round( $size / 1048576 * 100 ) / 100 . " Mb";
        else if ( $size >= 1024 ) 
			$size = round( $size / 1024 * 100 ) / 100 . " kb";
        else 
			$size = $size . " bytes";
        
		return $size;
    }
} // END OF UserQuota

?>
