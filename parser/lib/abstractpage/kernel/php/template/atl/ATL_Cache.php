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
 * Interface to ATL cache system.
 *
 * Implement this interface and use ATL_Template::setCacheManager() method
 * to intercept macro and template execution with your own cache system.
 *
 * The aim of this system is to fine grain the caching of ATL results
 * allowing the php coder to use whatever cache system he prefers with a 
 * good granularity as he can cache result of specific templates execution, 
 * and specific macros calls.
 *
 * @package template_atl
 */
 
class ATL_Cache extends PEAR
{
    /**
     * Called each time a template has to be executed.
     *
     * This method must return the cache value or the template execution 
     * return.
     *
     * function template(&$tpl, $path, &$context)
     * {
     *     // return cache if exists
     *     // else realy process template
     *     $res = $tpl->_process();
     *     // cache the result if needed
     *     // and return it
     *     return $res;
     * }
     *
     * @param  ATL_Template      the template that must be cached/executed
     * @param  string $path      the template path
     * @param  ATL_Context $ctx  the execution context
     * @return string
	 * @access public
     */
    function template( &$tpl, $path, &$context )
    {
        return $tpl->_process();
    }
    
    /**
     * Called each time a macro needs to be executed.
     *
     * This method allow cache on macro result. It must return the cache value
     * or at least the macro execution result.
     *
     * function macro(&$macro, $file, $name, &$context)
     * {
     *     // return cache if exists
     *     // else really process macro
     *     $res = $macro->_process();
     *     // cache the result if needed
     *     // and return it
     *     return $res
     * }
     * 
     *
     * @param  ATL_Macro    the macro to executed
     * @param  string $file the macro source file
     * @param  string $name the macro name
     * @param  ATL_Context $context the current execution context
     * @return string
	 * @access public
     */
    function macro( &$macro, $file, $name, &$context )
    {
        return $macro->_process();
    }
} // END OF ATL_Cache

?>
