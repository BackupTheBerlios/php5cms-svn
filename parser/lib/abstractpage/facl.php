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
 * PHP FUNCTION ACL, ACCESS CONTROL LISTS FOR PHP OBJECTS
 *
 * Prepend this file (in your php.ini) to control your clients access to
 * execute defined functions, undefined includes and forbidden variables.
 */

$basedir = "/path/to/your/wwwroot"; /* without "/" at the and */

if ( !isset( $_facl_func_deny ) || $_facl_func_deny[0] != '__FACL_HEADER_SIGN' )
{
	$_facl_func_deny = array(
		'__FACL_HEADER_SIGN',
		'_variable_callback',   	// don't allow facl _variable_callback
		'_function_callback',   	// and _function_callback()
		'mysql(.*)',            	// don't allow mysql* functions!
		'fopen(.*)',            	// dont' allow fopen function!
		'system',               	// and so on...
		'exec',
		'mail',
		'file(.*)',
		'var_dump(.*)',
		'print_r(.*)',
		'getenv(.*)',
		'setenv(.*)',
	);
}

if ( !isset( $_facl_var_deny ) || $_facl_var_deny[0] != '__FACL_HEADER_SIGN' )
{
    $_facl_var_deny = array(
		'__FACL_HEADER_SIGN',
		'_facl_parseable_content',
		'GLOBALS',					// DON'T ALLOW ACCESS TO $GLOBALS 
		'SCRIPT_NAME',				// DON'T ALLOW ACCESS TO $SCRIPT_NAME
		'basedir',					// and so on...
	);
}

if ( !isset( $_facl_include_allow ) || $_facl_include_allow[0] != '__FACL_HEADER_SIGN' )
{
    $_facl_include_allow = array(
		'__FACL_HEADER_SIGN',
		'libs/lib-global.php', 		// __ALLOW__ ACCESS TO $basedir/libs/lib-global.php !!!
	);
}

$buffer = file( "$basedir$SCRIPT_NAME" );

function _function_callback( $matches )
{
    global $_facl_func_deny;
    global $line, $buff;

    $val   = $matches[1];
    $param = $matches[2];

    for ( $i = 1; $i < sizeof( $_facl_func_deny ); $i++ )
	{
		if ( eregi( $_facl_func_deny[$i], $val ) )
	    	return ( $buff = "echo \"<b>ERROR on Line $line at Position ".strpos($buff,$val).": Function &quot;$val&quot; not accessable!</b><br>\";\n" );
	}
	
    return $buff;
}

function _variable_callback( $matches )
{
    global $_facl_var_deny;
    global $line, $buff;

    $val = $matches[1];

    for ( $i = 1; $i < sizeof( $_facl_var_deny ); $i++ )
	{
		if ( eregi( $_facl_var_deny[$i], $val ) )
	    	return ( $buff="echo \"<b>ERROR on Line $line at Position ".strpos($buff,$val).": Variable &quot;\\$".$_facl_var_deny[$i]."&quot; could not be used!</b><br>\";\n" );
	}
	
    return $buff;
}

function _include_callback( $matches )
{
    global $_facl_include_allow;
    global $line, $buff, $SCRIPT_NAME;

    $val   = $matches[4];
    $whole = $matches[0];

    $sn_ar = explode( "/", $SCRIPT_NAME );
    $sn_sz = sizeof( $sn_ar );
    $vl_ar = explode( "/", $val );
    $vl_sz = sizeof( $vl_ar );

    for ( $i = ( $s = ( $vl_sz - $sn_sz ) ); $i < sizeof( $vl_ar ); $i++ )
    {
		if ( $i != $s )
	    	$path .= "/";
	
		$path .= $vl_ar[$i];
    }

    if ( !in_array( $path, $_facl_include_allow ) )
		return ( $buff="echo \"<b>ERROR on Line $line at Position ".strpos($buff,$whole).": Access to Include/Require $val forbidden by rule!!</b><br>\";\n" );

    return $buff;
}

$_facl_parseable_content = "";
$_php_in = 0;

while ( list( $line, $buff ) = each( $buffer ) )
{
    // are we in php code? "<?(.*)?>"
    if ( preg_match( '/' . preg_quote( "<?" ) . '/', $buff ) == 1 )        
		$_php_in = 1;
    elseif ( preg_match( '/' . preg_quote( "?>" ) . '/', $buff ) == 1 )  
		$_php_in = 0;

    if ( $_php_in )
    {
		$tmp = trim( $buff );
		
		if ( $tmp[0] == '#' )
	    	continue;
			
		preg_replace_callback( '/([_A-Za-z0-9]{1,})\((.*)\)/',        '_function_callback', $buff );
		preg_replace_callback( '/\$([_A-Za-z0-9]{1,})/',              '_variable_callback', $buff );
		preg_replace_callback( '/(include|require(.*))(.+)\'(.*)\'/', '_include_callback',  $buff );
    }

    $_facl_parseable_content .= $buff;
}

// cleanup
unset( $buff );
unset( $buffer );
unset( $line );
unset( $_facl_func_deny );
unset( $_facl_var_deny );
unset( $_facl_include_allo );
unset( $tmp );
unset( $_php_in );


// NOW EVAL THE REST OF USERS CODE!

// (eval outputs content!)
eval( '?>' . $_facl_parseable_content . '<?' );

// EXIT THIS CODE IN ALL CASES !!
exit;
// }

?>
