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


using( 'template.atl.ATL_SourceLocator' );


/**
 * This class is used to resolve template source path outside the file system.
 *
 * Given a path, a template repository and a template caller path, one resolver
 * must decide whether or not it can retrieve the template source.
 *
 * @package template_atl
 */
 
class ATL_SourceResolver extends PEAR
{
    /**
     * Resolve a template source path.
     *
     * This method is invoked each time a template source has to be
     * located.
     *
     * This method must returns a ATL_SourceLocator object which
     * 'point' to the template source and is able to retrieve it.
     *
     * If the resolver does not handle this kind of path, it must return
     * 'false' so ATL will ask other resolvers.
     *
     * @param string $path       -- path to resolve
     *
     * @param string $repository -- templates repository if specified on
     *                              on template creation. 
     *
     * @param string $callerPath -- caller realpath when a template look
     *                              for an external template or macro,
     *                              this should be usefull for relative urls
     *
     * @return mixed
	 * @access public
     */
    function resolve( $path, $repository = false, $callerPath = false )
    {
        // first look at an absolute path
        $pathes = array( $path );
		
        // search in the caller directory
        if ( $callerPath )
            $pathes[] = dirname( $callerPath ) . DIRECTORY_SEPARATOR . $path;
        
        // search in the template repository
        if ( $repository )
            $pathes[] = $repository . DIRECTORY_SEPARATOR . $path;
        
        // search in the defined repository
        if ( defined( 'ATL_REPOSITORY' ) )
            $pathes[] = ATL_REPOSITORY . DIRECTORY_SEPARATOR . $path;
        
        foreach ( $pathes as $ftest ) 
		{
            if ( file_exists( $ftest ) ) 
			{
                $realpath = realpath( $ftest );
                $locator  = new ATL_SourceLocator( $realpath );
				
                return $locator;
            }
        }
		
        return false;
    }
} // END OF ATL_SourceResolver

?>
