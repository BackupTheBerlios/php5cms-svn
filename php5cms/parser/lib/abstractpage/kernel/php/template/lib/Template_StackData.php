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
|Authors: Juan M. Casillas <assman@jmcresearch.com>                    |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Class to hold the tag information.
 *
 * @package template_lib
 */

class Template_StackData
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function Template_StackData( $a, $b, $c, $d, $name, $args )
	{
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
		$this->d = $d;
		
		$this->name      = $name;
		$this->args      = $args;
		$this->open      = true;
		$this->replaceby = -1;
		$this->included  = array();
	}
} // END OF Template_StackData

?>
