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


using( 'xml.dom.lib.Document' );


/**
 * Base class for all Dom applications.
 *
 * @package xml_dom_lib
 */

class Dom extends Document
{
	/**
	 * @access public
	 */
	var $path = null;
	
	/**
	 * @access public
	 */
	var $file = null;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Dom()
	{
		$this->Document();
	}
	
	
	/**
	 * @access public
	 */
	function load( $file = '', $path = './' )
	{
		if ( is_dir( $path ) )
		{
			$this->path = $path;
			
			if ( is_readable( $file ) )
			{
				$this->file = $file;
				$result = $this->createFromFile( $this->path . $this->file );
				
				return $result? true : false;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @access public
	 */
	function save()
	{
		$xml = $this->toString();
		
		// TODO
	}
} // END OF Dom

?>
