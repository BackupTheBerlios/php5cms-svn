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
+----------------------------------------------------------------------+
*/


/**
 * ASCIIArt - Class to convert Bitmap-Images into nice ASCII Texts in HTML format.
 * It provides 3 different render modes and support for JPG, PNG and GIF 
 * (if your PHP supports it, too).
 *
 * Requirements:
 * PHP4 with GD support for the desired image format
 *
 * @package image
 */
  
class ASCIIArt extends PEAR
{
    /**
	 * The replace characters from dark to light used by render modes 0,1 
	 * (current version can handle 9 variations).
	 *
	 * @var      array
	 * @access   public
	 */
    var $replace_characters = array(
		1 => "W",
        2 => "@",
        3 => "#",
        4 => "*",
        5 => "+",
        6 => ":",
        7 => ".",
        8 => ",",
        9 => "&nbsp;"
    );

    /**
     * Possible image types.
     *
     * @var      array
     * @access   public
     */
    var $image_types = array(
        1 => "GIF",
        2 => "JPEG",
        3 => "PNG"
    );

    /**
     * Image file handler.
     *
     * @var      string
     * @access   public
     */
    var $image = null;
    
    /**
     * Image file height.
     *
     * @var      integer
     * @access   public
     */
    var $image_height = 0;
    
    /**
     * Image file height.
     *
     * @var      integer
     * @access   public
     */
    var $image_width = 0;
    
    /**
     * Container for the rendered HTML/ASCII image.
     *
     * @var      string
     * @access   public
     */
    var $image_html = null;
   
    /**
     * CSS for the HTML Image Output.
     *
     * @var      string
     * @access   public
     */
    var $image_css = "
        color           : #000000;
        background-color: #FFFFFF;
        font-size       : 8px;
        font-family     : \"Courier New\", Courier, mono;
        line-height     : 5px;
        letter-spacing  : -1px;
    ";
    
    /** 
     * Var to remember the last font tag.
     *
     * @var      array
     * @access   private
     */
    var $last_rgb = array();
    
    /** 
     * Var to remember the font tag state.
     *
     * @var      boolean
     * @access   private
     */
    var $font_tag_open = false;
  
  
    /**
	 * Constructor
     * Loads the bitmap image and sets file handler, width and height properties.
     *
     * If filename begins with "http://" (not case sensitive), an HTTP 1.0 connection 
     * is opened to the specified server, the page is requested using the HTTP GET method.
     * If filename begins with "ftp://" (not case sensitive), an ftp connection to the 
     * specified server is opened.
     * If the server does not support passive mode ftp, this will fail.
     * If filename is one of "php://stdin", "php://stdout", or "php://stderr",
     * the corresponding stdio stream will be opened.
     * If filename begins with anything else, the file will be opened from the filesystem.
     *
     * @param    string  $image  Filename of the bitmap Image
     * @param    string  $tmp    Directory for temporary files, will fallback to system default if necessary
     * @access   public
     * @see  $image, $image_height, $image_width
     */
    function ASCIIArt( $image, $tmp_dir = "/tmp" )
    {
        if ( $input = @fopen( $image, "rb" ) ) 
		{
    		// Read image data.
            $image_data = fread( $input, 2048576 );
    		fclose( $input );
    		
    		// Create temporary file.
    		if ( !$tmp_filename = tempnam( $tmp_dir, "BAA" ) )
			{
				$this = new PEAR_Error( "Cannot create tempfile " . $tmp_filename );
				return;
			}

    		// tempnam created a file already. Remove it for now.
			if ( !unlink( $tmp_filename ) )
			{
				$this = new PEAR_Error( "Cannot remove " . $tmp_filename );
				return;
			}
    		
    		// Open temporary file.
    		if ( !$output = fopen( $tmp_filename, "wb" ) )
			{
				$this = new PEAR_Error( "Cannot open " . $tmp_filename . " for writing" );
				return;
			}
    		
    		// Write image data to temporary file.
    		if ( !fwrite( $output, $image_data ) )
			{
				$this = new PEAR_Error( "Cannot write to " . $tmp_filename );
				return;
			}
    		
    		// Close temporary file.
    		if ( !fclose( $output ) )
			{
				$this = new PEAR_Error( "Cannot close " . $tmp_filename );
				return;
			}
        } 
		else 
		{
			$this = new PEAR_Error( "Cannot access " . $image . " for reading (does not exist or has wrong permissions)." );
			return;
		}

        // Create Image from file by type, get and set size.
        list( $width, $height, $type ) = getimagesize( $tmp_filename );
		
        switch ( $type ) 
		{
            case 1:
			
			case 2:
            
			case 3:
                $imagefunction = "imagecreatefrom" . strtolower( $this->image_types[$type] );
                
				if ( !function_exists( $imagefunction ) || !$this->image = $imagefunction( $tmp_filename ) ) 
				{
                    // Remove temporary file.
            		if ( !unlink( $tmp_filename ) )
					{
						$this = new PEAR_Error( "Cannot remove " . $tmp_filename );
						return;
					}
            		
					$this = new PEAR_Error( "This PHP version cannot create image from " . $this->image_types[$type] );
					return;
                }
                
                $this->image_height = $height;
                $this->image_width  = $width;
                
                // Remove temporary file.
        		if ( !unlink( $tmp_filename ) )
				{
					$this = new PEAR_Error( "Cannot remove " . $tmp_filename );
					return;
				}

                break;
            
			default:
                // Remove temporary file.
        		if ( !unlink( $tmp_filename ) )
        		{
					$this = new PEAR_Error( "Cannot remove " . $tmp_filename );
					return;
				}
        		
				$this = new PEAR_Error( "Cannot determine image type of " . $image . "." );
				return;
        }        
    }
	
    
    /**
     * Formatting the HTML Image using CSS.
     * Tip: Use width-fixed fonts such as Courier only.
     *
     * @param    string    $css    CSS Content
     * @access   public
     * @see      $image_css
     */
    function setImageCSS( $css )
    {
        $this->image_css = $css;
    }

    /**
    * Renders the image into HTML.
    *
    * The following modes are implemented: 
    * 1 = black/white using $replaceCharacters by brightness, 
    * 2 = colourized using $replaceCharacters by brightness, 
    * 3 = colourized using a fixed character definded by $fixedChar. 
    * A resolution of 1 means that every pixel is being replaced, 
    * whereas 5 for example means a scanned block of 5 pixel height and width, 
    * resulting in less data to replace.
    *
    * @param    integer $mode       Current version can handle mode 1, 2 or 3
    * @param    integer $resolution Resolution for scanning the bitmap.
    * @param    string  $fixed_char Needed for mode 3
    * @param    boolean $flip_h     Flip output horizontally?
    * @param    boolean $flip_v     Flip output vertically?
    * @access   public
    * @see  getHTMLImage(), $replace_characters
    */
    function renderHTMLImage( $mode = 1, $resolution = 2, $fixed_char = "@", $flip_h = false, $flip_v = false )
    {
        // Minimum value for $resolution is 1.
        if ( $resolution < 1 )
            $resolution = 1;
        
        // Different loops for flipping
        // (btw.: Can someone give me a hint how to implement 
        // dynamic operators or the like to get rid of those endless cases?)
        if ( !$flip_h && !$flip_v ) 
		{
            // Y-Axis
            for ( $y = 0; $y < $this->image_height; $y += $resolution )
            {
                // X-Axis
                for ( $x = 0; $x < $this->image_width; $x += $resolution )
                    $this->renderPixel( $mode, $x, $y, $fixed_char );
                
                $this->image_html .= "<br>\n";
            }
        }
        else if ( $flip_h && !$flip_v ) 
		{
            // Y-Axis
            for ( $y = 0; $y < $this->image_height; $y += $resolution )
            {
                // X-Axis
                for ( $x = $this->image_width; $x > 0; $x -= $resolution )
                    $this->renderPixel( $mode, $x, $y, $fixed_char );
                
                $this->image_html .= "<br>\n";
            }
        }
        else if ( !$flip_h && $flip_v ) 
		{
            // Y-Axis
            for ( $y = $this->image_height; $y > 0; $y -= $resolution )
            {
                // X-Axis
                for ( $x = 0; $x < $this->image_width; $x += $resolution )
                    $this->renderPixel( $mode, $x, $y, $fixed_char );
                
                $this->image_html .= "<br>\n";
            }
        }
        else if ( $flip_h && $flip_v ) 
		{
            // Y-Axis
            for ( $y = $this->image_height; $y > 0; $y -= $resolution )
            {
                // X-Axis
                for ( $x = $this->image_width; $x > 0; $x -= $resolution )
                    $this->renderPixel( $mode, $x, $y, $fixed_char );
                
                $this->image_html .= "<br>\n";
            }
        }
		
        if ( $this->font_tag_open )
            $this->image_html .= "</font>\n";
    }

    /**
     * Renders the current pixel.
     *
     * @param    integer $mode       Current version can handle mode 1, 2 or 3
     * @param    integer $x          X Position of the image
     * @param    integer $y          Y Position of the image
     * @param    string  $fixed_char Needed for mode 3
     * @access   public
     * @see  renderHTMLImage(), $image_html
     */
    function renderPixel( $mode, $x, $y, $fixed_char )
    {
        // RGB Value of current pixel (Array)
        $rgb = imagecolorsforindex( $this->image, imagecolorat( $this->image, $x, $y ) );
        
        // Replace by mode
        switch ( $mode ) 
		{
            case 1:
				// Rounded Brightness
				$brightness = $rgb["red"] + $rgb["green"] + $rgb["blue"];
        
				// Choose replacing character
				$replace_character_id = round( $brightness / 100 ) + 1;
                
				$this->image_html .= $this->replace_characters[$replace_character_id];
                break;
				
            case 2:
				// Rounded Brightness
				$brightness = $rgb["red"] + $rgb["green"] + $rgb["blue"];
        
				// Choose replacing character
				$replace_character_id = round( $brightness / 100 ) + 1;

				if ( $this->last_rgb == $rgb ) 
				{
					$this->image_html .= $this->replace_characters[$replace_character_id];
				} 
				else 
				{
					if ( $this->font_tag_open )
                        $this->image_html .= "</font>";
                    
					$this->image_html    .= "<font color=\"#".$this->_rgb2hex($rgb)."\">".$this->replace_characters[$replace_character_id];
					$this->font_tag_open  = true;
				}
				
				break;
			
			case 3:
				if ( $this->last_rgb == $rgb ) 
				{
					$this->image_html .= $fixed_char;
				} 
				else 
				{
					if ( $this->font_tag_open )
						$this->image_html .= "</font>";
						
					$this->image_html    .= "<font color=\"#".$this->_rgb2hex($rgb)."\">".$fixed_char;
					$this->font_tag_open  = true;
                }
				
				break;
		}
		
		$this->last_rgb = $rgb;
	}
    
    /**
     * Returns the rendered HTML image and CSS.
     *
     * @return   string  The rendered HTML image and CSS
     * @access   public
     */
    function getHTMLImage()
    {
        if ( $this->image_html ) 
		{
            return "<style type=\"text/css\">"
                   .".asciiimage{"
                   .$this->image_css
                   ."}</style>"
                   ."<span class=\"asciiimage\">"
                   .$this->image_html
                   ."</span>";
        } 
		else 
		{
            return "";
        }
    }
	
	
	// private methods
	
    /**
    * Returns the hex string of a rgb array.
    *
    * Example:
    * $rbg = array("red" -> 255, "green" -> 255, "blue" -> 255); 
    * rgb2hex($rgb) will return "FFFFFF"
    *
    * @param    array   $rgb    An array of red, green and blue values
    * @return   string  The hex values as one string
    * @access   private
    */
    function _rgb2hex( $rgb )
    {
        return sprintf( "%02X%02X%02X", $rgb["red"], $rgb["green"], $rgb["blue"] );
    }
} // END OF ASCIIArt

?>
