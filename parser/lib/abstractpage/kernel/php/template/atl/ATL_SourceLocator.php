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
 * Interface for template source locators.
 *
 * A locator is a way to retrieve a ATL template source otherwhere than in
 * the file system.
 *
 * The default behavour of this class is to act like a file system locator.
 *
 * @package template_atl
 */
 
class ATL_SourceLocator extends PEAR
{
	/**
	 * @access public
	 */
    var $path;


	/**
	 * Constructor
	 *
	 * @access public
	 */    
    function ATL_SourceLocator( $path )
    {
        $this->path = $path;
    }
    
	
    /**
     * Returns an absolute path to this resource.
     * 
     * The result of this method is used to generate some unique
     * md5 value which represent the template php function name
     * and the intermediate php file name.
     *
     * @return string
	 * @access public
     */
    function realPath()
    {
        return $this->path;
    }

    /**
     * Return source last modified date in a filemtime format.
     *
     * The result is compared to php intermediate mtime to decide
     * weither or not to re-parse the template source.
     *
     * @return int
	 * @access public
     */
    function lastModified()
    {
        return filemtime( $this->path );
    }

    /**
     * Return the template source.
     *
     * This method is invoqued if the template has to be parsed.
     *
     * @return string
	 * @access public
     */
    function data()
    {
        return join( '', file( $this->path ) );
    }
} // END OF ATL_SourceLocator

?>
