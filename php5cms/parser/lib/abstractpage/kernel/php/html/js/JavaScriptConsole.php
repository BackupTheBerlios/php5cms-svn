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
 * Class to open a debugging console.
 *
 * Usage:
 * 
 * print "<html><head><title>Testing console...</title></head><body>";
 *
 * print "Output on console 1, no output on console 2";
 *
 * $console_1 =& new console_class("console1","Console 1");
 * $console_2 =& new console_class("console2","Console 2");
 *
 * $console_1->output("Testtext");
 *
 * print $console_1->injection();
 * print $console_2->injection();
 *
 * print "</body></html>";
 *
 * @package html_js
 */
 
class JavaScriptConsole extends PEAR
{
 	/** 
     * The content buffer for this console window.
	 * @var string  
	 */
   	var $content;
   
   	/** 
     * The (javascript) name of the window.
	 * @var string  
	 */
	var $window;
   
   	/** 
	 * The title of the window.
	 * @var string  
	 */
	 var $title;

	 
	/**
	 * Constructor
	 *
	 * @access public
	 * @param string The name of the console window to be used
	 * @param string The title of the window
	 */
	function JavaScriptConsole( $window, $title )
	{
	   	$this->content = "";
		$this->window  = $window;
		$this->title   = $title;
	}
	
	 
  	/**
  	 * Function to return the Java-Script injection required to generate the console.
	 * 
  	 * @access public
  	 * @return string The injection as html-ready text or "" if the console was not used 
  	 */
	function injection()
	{
		if ( $this->is_used() )
			$result = $this->_get_injection();
		else
			$result = "";
	
		$content = "";
		return $result;
	}
	 
  	/**
  	 * Query function to determine whether the console was used.
	 * 
  	 * @access public
  	 * @return bool Whether the console was used
  	 */
	function is_used()
	{
		return ( strlen( $this->content ) > 1 );
	}
	 
  	/**
  	 * Output text to the console.
	 * 
  	 * @access public
  	 * @param string The text to be written
   	 */
	function output( $text )
	{
		$this->content .= " $this->window.document.writeln('" . $text . "');\r\n";
	}


	// private methods
		 	 
  	/**
  	 * Function constructing the injection text.
	 * 
  	 * @access private
  	 * @return string The injection text
  	 */
	function _get_injection()
	{  
	    return "\n<script type=\"text/javascript\" language=\"JavaScript\">\n"
			. "<!--\n"
			. " $this->window = window.open(\"\", \"" . $this->window . "Window\",\"resizable=yes,scrollbars=yes,directories=no,location=no,menubar=no,status=no,toolbar=no\");\n"
			. " $this->window.document.open();"
			. " $this->window.document.writeln(\"<html><head><title>Console: $this->title</title></head><body>\");"
			. $this->content 
			. " $this->window.document.writeln(\"</body></html>\");\n"
			. " $this->window.document.close();\n"
			. " //--></script>\n";
	}
} // END OF JavaScriptConsole
 
?>
