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
 * Interface for source filter.
 *
 * This interface may be used to implement input / output filters.
 *
 * If the template intermediate php code is up to date, pre filters won't be
 * used on it.
 *
 * Output filters are only called on main template result.
 *
 * class MyFilter extends ATL_Filter
 * {
 *     function filter(&$tpl, $data, $mode) 
 *     {
 *         // just to present $mode usage for input/output filters
 *         if ( $mode == ATL_FILTER_POST )
 *             return PEAR::raiseError( "MyFilter mustn't be used as a pre-filter' );
 *         
 *         // remove html comments from template source
 *         return preg_replace('/(<\!--.*?-->)/sm', '', $data);
 *     }
 * }
 *
 * $tpl = ATL_Template( 'mytemplate.html' );
 * $tpl->addInputFilter( new MyFilter() );
 * echo $tpl->execute();
 *
 * @package template_atl
 */

define( 'ATL_FILTER_PRE',  1 );
define( 'ATL_FILTER_POST', 2 );


class ATL_Filter extends PEAR
{
    /**
     * Filter some template source string.
     *
     * @param  ATL_Template $tpl 
     *         The template which invoked this filter.
     *        
     * @param  string $data         
     *         Data to filter.
     *
     * @param  int $mode
     *         ATL_FILTER_PRE | ATL_FILTER_POST depending if this filter
     *         is registered as a pre-filter or as a post-filter.
	 *
	 * @access public
     *        
     */
    function filter( &$tpl, $data, $mode )
    {
        return $data;
    }
} // END OF ATL_Filter

?>
