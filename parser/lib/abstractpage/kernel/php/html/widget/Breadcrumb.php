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
 * Breadcrumb class
 *
 * Show the directories and their links in path form
 * Home > Firstdir > Seconddir > Etc > filename.php
 *
 *
 * Examples
 *
 * $breadcrumb = new Breadcrumb( basename( __FILE__ ) );
 * $breadcrumb->homepage  ='homepage'; // sets the home directory name
 * $breadcrumb->dirformat ='ucfirst';  // Show the directory in this style
 * $breadcrumb->symbol    = ' || ';    // set the separator between directories
 * $breadcrumb->showfile  = true;      // shows the file name in the path
 * $breadcrumb->special   = 'elmer';   // special directory formatting
 * echo "<p>".$breadcrumb->get()."</p>";
 *
 * $breadcrumb = new Breadcrumb( basename( __FILE__ ) );
 * echo "<p>".$breadcrumb->get()."</p>";
 *
 *
 * Properties
 *
 * homepage    sets the name of the home directory, leave empty if you do
 *             not want the home directory to show (DEFAULT = 'home')
 *
 * dirformat   can be of type:
 *
 *             ucfirst = upper case first letter
 *             uppercase = all uppercase
 *             lowercase = all lowercase
 *             none = show directories as they are named in path structure (DEFAULT)
 * 
 * symbol      set the separator between directories (DEFAULT = ' &gt; ')
 * 
 * showfile    shows the file name in the path (DEFAULT = TRUE)
 *
 * special     special directory formatting
 *
 *             elmer   = elmer fudd translation
 *             hacker  = hacker speach translation
 *             pig     = pig latin translation
 *             reverse = Reverses the text so it is backwards
 *             none    = no special formatting (DEFAULT)
 *
 * @package html_widget
 */

class Breadcrumb extends PEAR
{
	// directory structure
  	var $scriptArray = '';
  
  	// filename
  	var $fileName = '';
  
  	// homepage name
  	var $homepage = 'home';
  
  	// directory type formatting
  	var $dirformat = '';
  
  	// symbol to use between directories
  	var $symbol = ' &gt; ';
  
  	// show the filename with the path
  	var $showfile = true;
  
  	// special directory text style
  	var $special = '';
  
  
  	/**
  	 * Constructor
  	 */
  	function Breadcrumb( $path = __FILE__ ) 
	{
    	// Creates an array of Directory Structure.
    	$this->scriptArray = explode( "/", $path );
    
		// Pops the filename off the end and throws it into it's own variable.
    	$this->fileName = array_pop( $this->scriptArray );
  	}
 
 
	/**
	 * Converts a string to an array.
	 */
 	function str_split( $string ) 
 	{
    	for ( $i = 0; $i < strlen( $string ); $i++ ) 
			$array[] = $string{$i};
    
		return $array;
  	}
  
  	/**
  	 * Convert string into language specified.
  	 */
  	function specialLang( $string, $lang ) 
	{
    	// parse directory special text style
    	switch ( $lang ) 
		{
      		case 'elmer': 
				$string = str_replace( 'l', 'w', $string );
                $string = str_replace( 'r', 'w', $string );
                
				break;
      
	  		case 'hacker': 
				$string = strtoupper( $string );
				$string = str_replace( 'A', '&#52;',             $string );
				$string = str_replace( 'C', '&#40;',             $string );
				$string = str_replace( 'D', '&#68;',             $string );
				$string = str_replace( 'E', '&#51;',             $string );
				$string = str_replace( 'F', '&#112;&#104;',      $string );
				$string = str_replace( 'G', '&#54;',             $string );
				$string = str_replace( 'H', '&#125;&#123;',      $string );
				$string = str_replace( 'I', '&#49;',             $string );
				$string = str_replace( 'M', '&#124;&#86;&#124;', $string );
				$string = str_replace( 'N', '&#124;&#92;&#124;', $string );
				$string = str_replace( 'O', '&#48;',             $string );
				$string = str_replace( 'R', '&#82;',             $string );
				$string = str_replace( 'S', '&#53;',             $string );
				$string = str_replace( 'T', '&#55;',             $string );
				
				break;
				
      		case 'pig': 
				$vowels      = array( 'a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U' );
               	$string      = $this->str_split( $string );
               	$firstLetter = array_shift( $string );
               	$string      = implode( '', $string );
				$string      = ( in_array( $firstLetter, $vowels ) )? $firstLetter . $string . 'yay' : $string . $firstLetter . 'ay';
              
			  	break;
      
	  		case 'reverse': 
				$string = strrev( $string );
           		break;
    	}
    
		return $string;
	}
  
  
  	/**
  	 * Convert string into specified format.
  	 */
  	function dirFormat( $string, $format ) 
	{
    	// parse directory text style
      	switch ( $format ) 
		{
        	case 'ucfirst': 
				$string = ucfirst( $string ); 
				break;
        	
			case 'uppercase': 
				$string = strtoupper( $string ); 
				break;
        	
			case 'lowercase': 
				$string = strtolower( $string ); 
				break;
        	
			default: 
				$string = $string;
      	}
    
		return $string;
  	}
  
  	function get() 
	{
  		// Either set the home element or pop the first empty array off the beginning.
    	if ( $this->homepage != '' ) 
			$this->scriptArray[0] = $this->homepage;
    	else 
			$tmp = array_shift( $this->scriptArray );
    
    	if ( $this->homepage == '' ) 
			$dir = '/';
    
		$fileName = $this->fileName;
    
    	// Build Path Structure.
    	for ( $i = 0; $i < count( $this->scriptArray ); $i++ ) 
		{
      		$dirName = $this->scriptArray[$i];
      		
			// append the current directory
      		$dir .= ( $i == 0 && $this->homepage != '' )? '/' : $dirName . "/";
      
      		// parse directory special text style
      		$dirName = $this->specialLang( $dirName, $this->special );

      		// convert string into specified format
      		$dirName = $this->dirFormat( $dirName, $this->dirformat );
      
      		// create link
      		$breadcrumb[] = "<a href='$dir'>$dirName</a>";
    	}
    
    	// parse filename special text style
    	$fileName = $this->specialLang( $fileName, $this->special );
    
		// convert string into specified format
    	$fileName = $this->dirFormat( $fileName, $this->dirformat );
    
    	// web server path
    	return ( $this->showfile == true )? implode( $this->symbol,$breadcrumb ) . $this->symbol . $fileName : implode( $this->symbol, $breadcrumb );
  	}
} // END OF Breadcrumb

?>
