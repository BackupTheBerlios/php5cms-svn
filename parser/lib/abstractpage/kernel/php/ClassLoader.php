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


$GLOBALS["CLASSLOADER_CLASSES"]  = array();
$GLOBALS["CLASSLOADER_PACKAGES"] = array();


define( "CLASSLOADER_EXTENSION", ".php" );

if ( !defined( "AP_PACKAGES_PATH" ) )
	define( "AP_PACKAGES_PATH", "" );


/**
 * @package Abstractpage
 */
 
class ClassLoader
{	
    /**
	 * Importing a unique class or an entire package.
	 *
	 * @param	string	a class name or a package name
	 * @return  void
	 * @access  public static
	 */
	function import( $package )
	{
		// already registered?
		if ( ClassLoader::isRegistered( $package ) )
			return true;
		
		$packagename = ClassLoader::_extractPackageName( $package );
		$classname   = ClassLoader::_extractClassName( $package );
		$path        = ClassLoader::_convertToPath( $package );
		
		if ( ClassLoader::isPackage( $package ) )
		{
			if ( isset( $path ) && is_dir( $path ) )
			{
				ClassLoader::register( $package );
				$handle = opendir( $path );
				
				while ( $file = readdir( $handle ) )
				{
					if ( ( $file !== '.' ) && ( $file !== '..' ) && ( strpos( $file, CLASSLOADER_EXTENSION ) !== false ) )
					{
						$array = explode( '.', $file );
						ClassLoader::import( $packagename . "." . $array[0] );
					}
				}
				
				closedir( $handle );
				return true;
			}
			else
			{
				return false;
			}
		}
		else if ( ClassLoader::isClass( $package ) )
		{
			if ( isset( $path ) && is_file( $path . CLASSLOADER_EXTENSION ) )
			{
				/* prevent us from recursion */
				require_once $path . CLASSLOADER_EXTENSION;

				if ( class_exists( $classname ) )
				{
					ClassLoader::register( $packagename, $classname );
					return true;
				}
			}
			
			return false;
		}
		
		return false;
	}
	
	function create( $class, $parameters = null )
	{
		$classname = ClassLoader::_extractClassName( $class );
		
		if ( ClassLoader::isClass( $class ) || !ClassLoader::isRegistered( $class ) )
		{
			$path = ClassLoader::_convertToPath( $class );
			
			if ( file_exists( $path ) )
				ClassLoader::import( $class );
			else
				return false;
		}
		
		$params = '';
		
		if ( $parameters !== null && is_array( $parameters ) )
		{
			$plen = count( $parameters );
			
			if ( $plen > 0 )
			{
				for ( $i = 0; $i < $plen; $i++ )
					$params .= '$parameters[' . $i . ']';
					
				if ( $i < ( $plen - 1 ) )
					$params .= ', ';
			}
		}
		
		$obj = null;
		eval( '$obj =& new ' . $classname . '(' . $params . ');' );
		
		return $obj;
	}
	
	/**
	 * Try to register the class/package.
	 *
	 * @param	string		a class name or a package name
	 * @return  boolean 	TRUE if the class has been registered
	 * @access  public static
	 */
	function register( $class_package, $class_class = null )
	{
		if ( isset( $class_class ) )
			$class_package .= '.' . $class_class;
			
		$class_package = preg_replace( '/([À-Ý]|[A-Z])/e', 'chr(ord(\'\\1\')+32)', $class_package );

		// class or package?
		if ( ClassLoader::isClass( $class_package ) )
		{
			$extractedClassName   = ClassLoader::_extractClassName( $class_package );
			$extractedPackageName = ClassLoader::_extractPackageName( $class_package );
			
			$GLOBALS["CLASSLOADER_CLASSES"][$extractedClassName] = $extractedPackageName;
		}
		else if ( ClassLoader::isPackage( $class_package ) )
		{
			$GLOBALS["CLASSLOADER_PACKAGES"][$class_package] = $class_package;
		}
		
		return false;
	}
	
	/**
	 * Check if the class/package is already registered.
	 *
	 * @param   string		a class name or a package name
	 * @return  boolean		TRUE if the class/package is already registered
	 * @access  public
	 */
	function isRegistered( $class_package, $class_class = null )
	{
		if ( isset( $class_class ) || ClassLoader::isClass( $class_package ) )
		{
			if ( !isset( $class_class ) )
				$class_class = ClassLoader::_extractClassName( $class_package );
				
			$class_class = preg_replace( '/([À-Ý]|[A-Z])/e', 'chr(ord(\'\\1\')+32)', $class_class );
			return isset( $GLOBALS["CLASSLOADER_CLASSES"][$class_class] );
		}
		else
		{
			$class_package = preg_replace( '/([À-Ý]|[A-Z])/e', 'chr(ord(\'\\1\')+32)', $class_package );
			return isset( $GLOBALS["CLASSLOADER_PACKAGES"][$class_package] );
		}
	}
	
	 /**
	  *	Check if the parameter string is a package definition.
	  *
	  *	@param	 string		a class name or a package name
	  *	@return  boolean	TRUE if it's a package definition
	  *	@access  public static
	  */
	function isPackage( $package )
	{
		return ( 
			( $package[strlen( $package ) - 1] === '*' ) &&
			( $package[strlen( $package ) - 2] === '.' ) );
	}
	
	/**
	 * Check if the parameter string is a class definition.
	 *
	 * @param	string		a class name or a package name
	 * @return  boolean		TRUE if it's a package definition
	 * @access  public static
	 */
	function isClass( $package )
	{
		return isset( $package )? 
			$package[strlen( $package ) - 1] !== '*' : 
			false;
	}
	
	
	// private methods
	
	/**
	 * Try to extract a class name from a package call.
	 *
	 * @param   string		a class name or a package name
	 * @return  string		the extracted class name if extraction is a success or null if fail to extract
	 * @access  public static
	 */
	function _extractClassName( $package )
	{
		$array = explode( '.', $package );
		return $array[count( $array ) - 1];
	}	
	
	/**
	 * Try to extract a package name from a package call.
	 *
	 * @param   string		a class name or a package name
	 * @return  string		the extracted package name if extraction is a success or NULL if fail to extract
	 * @access  public static
	 */
	function _extractPackageName( $package )
	{
		if ( ClassLoader::isPackage( $package ) )
		{
			return substr( $package, 0, strlen( $package ) - 2 );
		}
		else
		{
			$pos = strrpos( $package, '.' );
			
			if ( $pos > 0 )
				return substr( $package, 0, $pos );
			else
				return $package;
		}
		
		return null;
	}
	
	/**
     * Convert to real path.
     */ 
	function _convertToPath( $package_name, $class_name = null )
	{
		$package_name = preg_replace( '/(\*?)$/', '', $package_name );
		$package_name = strtr( $package_name, '*.', ' /' );
		$package_name = str_replace( str_repeat( DIRECTORY_SEPARATOR, 2 ), '.' . DIRECTORY_SEPARATOR, $package_name );
		$package_name = AP_PACKAGES_PATH . $package_name;
		
		if ( isset( $class_name ) )
			$package_name .= DIRECTORY_SEPARATOR . $class_name . CLASSLOADER_EXTENSION;
		
		if ( stristr( getenv( "OS" ), "Windows" ) )
			$package_name = str_replace( "/", "\\", $package_name );
		
		return $package_name;
	}
} // END OF ClassLoader


/**
 * @access public
 */
function using( $class = "" )
{
	if ( !is_array( $class ) )
		$class = preg_split( '/,/', preg_replace( '/\s{1,}/', '', $class ) );

	$len = count( $class );
	
	for ( $i = 0; $i < $len; $i++ )
		ClassLoader::import( $class[$i] );
}

/**
 * @access public
 */
function class_registered( $package )
{
	return ClassLoader::isRegistered( $package );
}

/**
 * @access public
 */
function create_class( $class, $params )
{
	return ClassLoader::create( $class, $params );
}

?>
