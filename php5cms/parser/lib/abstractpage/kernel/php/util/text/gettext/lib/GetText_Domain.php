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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Class representing a domain file for a specified language.
 *
 * @package util_text_gettext_lib
 */
 
class GetText_Domain extends PEAR
{
	/**
	 * @access public
	 */
    var $name;
	
	/**
	 * @access public
	 */
    var $path;

	/**
	 * @access private
	 */
    var $_keys = array();
	
	
	/**
	 * @access public
	 */	
    function hasKey( $key )
    {
        return array_key_exists( $key, $this->_keys );
    }

	/**
	 * @access public
	 */
    function get( $key )
    {
        return $this->_keys[$key];
    }
} // END OF GetText_Domain

?>
