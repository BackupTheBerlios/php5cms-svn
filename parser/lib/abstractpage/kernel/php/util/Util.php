<?php

/*
+----------------------------------------------------------------------+
| AbstractPage - Web Content Management Solution                       |
+----------------------------------------------------------------------+
| Copyright (c) 2001-2003 Docuverse                                    |
+----------------------------------------------------------------------+
| This source file is subject to the Docuverse license,                |
| that is bundled with this package in the file LICENSE, and is        |
| available at http://www.docuverse.de/license/                        |
| If you did not receive a copy of the Docuverse license and are       |
| unable to obtain it through the world-wide-web, please send a note   |
| to license@docuverse.de so we can mail you a copy immediately.       |
+----------------------------------------------------------------------+
| Author: Chuck Hagenbuch <chuck@horde.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Static utility functions.
 *
 * @package util
 */

class Util
{
    /**
     * Removes given elements at request shutdown.
     *
     * If called with a filename will delete that file at request shutdown; if
     * called with a directory will remove that directory and all files in that
     * directory at request shutdown.
     *
     * If called with no arguments, return all elements to be deleted (this
     * should only be done by Util::_deleteAtShutdown).
     *
     * The first time it is called, it initializes the array and registers
     * Util::_deleteAtShutdown() as a shutdown function - no need to do so
     * manually.
     *
     * The second parameter allows the unregistering of previously registered
     * elements.
     *
     * @access public
     *
     * @param optional string $filename   The filename to be deleted at the end
     *                                    of the request.
     * @param optional boolean $register  If true, then register the element for
     *                                    deletion, otherwise, unregister it.
     * @param optional boolean $secure    If deleting file, should we securely
     *                                    delete the file?
     */
    function deleteAtShutdown( $filename = false, $register = true, $secure = false )
    {
        static $dirs, $files, $securedel;

        /* Initialization of variables and shutdown functions. */
        if ( is_null( $dirs ) )
		{
          	$dirs      = array();
            $files     = array();
            $securedel = array();
            
			register_shutdown_function( array( 'Util', '_deleteAtShutdown' ) );
        }

        if ( $filename ) 
		{
            if ( $register ) 
			{
                if ( @is_dir( $filename ) )
                    $dirs[$filename] = true;
                else
                    $files[$filename] = true;
                
                if ( $secure )
                    $securedel[$filename] = true;
            } 
			else 
			{
                unset( $dirs[$filename] );
                unset( $files[$filename] );
                unset( $securedel[$filename] );
            }
        } 
		else 
		{
            return array(
				$dirs, 
				$files, 
				$securedel
			);
        }
    }

    /**
     * Buffer the output from a function call, like readfile() or
     * highlight_string(), that prints the output directly, so that
     * instead it can be returned as a string and used.
     *
     * @access public
     *
     * @param string $function        The function to run.
     * @param optional mixed $arg1    First argument to $function().
     * @param optional mixed $arg2    Second argument to $function().
     * @param optional mixed $arg...  ...
     * @param optional mixed $argN    Nth argument to $function().
     *
     * @return string  The output of the function.
     */
    function bufferOutput()
    {
        if ( func_num_args() == 0 )
            return false;
        
        $eval = false;
        $args = func_get_args();
        $function = array_shift( $args );
        
		if ( is_array( $function ) ) 
		{
            if ( !method_exists( $function[0], $function[1] ) )
                return false;
        } 
		else if ( ( $function == 'include'      ) ||
                  ( $function == 'include_once' ) ||
                  ( $function == 'require'      ) ||
                  ( $function == 'require_once' ) ) 
		{
            $eval = true;
        } 
		else if ( !function_exists( $function ) && ( $function != 'eval' ) ) 
		{
            return false;
        }

        ob_start();

        if ( $eval ) 
            eval( $function . " '" . implode( ',', $args ) . "';" );
		else if ( $function == 'eval' ) 
            eval( $args[0] );
		else 
            call_user_func_array( $function, $args );
		
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
	
    /**
     * Return a hidden form input containing the session name and id.
     *
     * @access public
     */
    function formInput()
    {
		return '<input type="hidden" name="' . session_name() . '" value="' . session_id() . '" />';
    }
	
    /**
     * Print a hidden form input containing the session name and id.
     *
     * @access public
     */
    function pformInput()
    {
        echo Util::formInput();
    }
	
	/**
	 * Get linefeed for OS.
	 *
	 * @access public
	 */
	function getLinefeedForOS()
	{
		if ( stristr( getenv( "OS" ), "Windows" ) )
			$eol = "\r\n";
		else if ( stristr( getenv( "OS" ), "Darwin" ) )
			$eol = "\r";
		else
			$eol = "\n";
			
		return $eol;
	}
	
    /**
     * If magic_quotes_gpc is in use, run stripslashes() on $var.
     *
     * @access public
     *
     * @param  string $var  The string to un-quote, if necessary.
     *
     * @return string       $var, minus any magic quotes.
     */
    function dispelMagicQuotes( &$var )
    {
        static $magic_quotes;

        if ( !isset( $magic_quotes ) )
            $magic_quotes = get_magic_quotes_gpc();

        if ( $magic_quotes ) 
		{
            if ( !is_array( $var ) )
                $var = stripslashes( $var );
            else
                array_walk( $var, array( 'Util', 'dispelMagicQuotes' ) );
        }

        return $var;
    }

    /**
     * Get a form variable from GET or POST data, stripped of magic
     * quotes if necessary. If the variable is somehow set in both the
     * GET data and the POST data, the value from the POST data will
     * be returned and the GET value will be ignored.
     *
     * @access public
     *
     * @param string $var       The name of the form variable to look for.
     * @param string $default   (optional) The value to return if the
     *                          variable is not there.
     *
     * @return string     The cleaned form variable, or $default.
     */
    function getFormData( $var, $default = null )
    {
        return ( $val = Util::getPost( $var ) ) !== null? $val : Util::getGet( $var, $default );
    }

    /**
     * Get a form variable from GET data, stripped of magic quotes if
     * necessary. This function will NOT return a POST variable.
     *
     * @access public
     *
     * @param string $var       The name of the form variable to look for.
     * @param string $default   (optional) The value to return if the
     *                          variable is not there.
     *
     * @return string     The cleaned form variable, or $default.
     */
    function getGet( $var, $default = null )
    {
        return ( array_key_exists( $var, $_GET ) )? Util::dispelMagicQuotes( $_GET[$var] ) : $default;
    }

    /**
     * Get a form variable from POST data, stripped of magic quotes if
     * necessary. This function will NOT return a GET variable.
     *
     * @access public
     *
     * @param string $var       The name of the form variable to look for.
     * @param string $default   (optional) The value to return if the
     *                          variable is not there.
     *
     * @return string     The cleaned form variable, or $default.
     */
    function getPost($var, $default = null)
    {
        return (array_key_exists($var, $_POST))
            ? Util::dispelMagicQuotes($_POST[$var])
            : $default;
    }
	
    /**
     * Check to see if a value has been set by the script and not by GET, POST,
     * or cookie input. The value being checked MUST be in the global scope.
     *
     * @param string $varname  The variable name to check.
     *
     * @return mixed  Null if the var is in user input, the variable value otherwise.
     */
    function nonInputVar( $varname )
    {
        if ( isset( $_GET[$varname]    ) ||
             isset( $_POST[$varname]   ) ||
             isset( $_COOKIE[$varname] ) ) 
		{
            return null;
        } 
		else 
		{
            return isset( $GLOBALS[$varname] )? $GLOBALS[$varname] : null;
        }
    }
	
    /**
     * Determine the location of the system temporary directory.
     * If a specific setting cannot be found, it defaults to /tmp
     *
     * @access public
     *
     * @return string   A directory name which can be used for temp files.
     *                  Returns false if one could not be found.
     */
    function getTempDir()
    {
        $tmp_locations = array(
			'/tmp', 
			'/var/tmp', 
			'c:\temp', 
			'c:\windows\temp', 
			'c:\winnt\temp'
		);

        /* Try PHP's upload_tmp_dir directive. */
        if ( empty( $tmp ) )
            $tmp = ini_get( 'upload_tmp_dir' );

        /* Otherwise, try to determine the TMPDIR environment variable. */
        if ( empty( $tmp ) )
            $tmp = getenv( 'TMPDIR' );

        /* If we still cannot determine a value, then cycle through a
         * list of preset possibilities. */
        while ( empty( $tmp ) && sizeof( $tmp_locations ) ) 
		{
            $tmp_check = array_shift( $tmp_locations );
			
            if ( @is_dir( $tmp_check ) )
                $tmp = $tmp_check;
        }

        /* If it is still empty, we have failed, so return false;
         * otherwise return the directory determined. */
        return empty( $tmp )? false : $tmp;
    }
	
    /**
     * Create a temporary filename for the lifetime of the script, and
     * (optionally) register it to be deleted at request shutdown.
     *
     * @access public
     *
     * @param string $prefix            Prefix to make the temporary name more
     *                                  recognizable.
     * @param optional boolean $delete  Delete the file at the end of the
     *                                  request?
     * @param optional string $dir      Directory to create the temporary file
     *                                  in.
     *
     * @return string   Returns the full path-name to the temporary file.
     *                  Returns false if a temp file could not be created.
     */
    function getTempFile( $prefix = 'Util', $delete = true, $dir = false )
    {
        if ( !$dir || !is_dir( $dir ) )
            $tmp_dir = Util::getTempDir();
        else
            $tmp_dir = $dir;

        if ( empty( $tmp_dir ) )
            return false;

        $tmp_file = tempnam( $tmp_dir, $prefix );

        /* If the file was created, then register it for deletion and return */
        if ( empty( $tmp_file ) ) 
		{
            return false;
        } 
		else 
		{
            if ( $delete )
                _fileCleanup( $tmp_file );
            
            return $tmp_file;
        }
    }
	
    /**
     * Create a temporary directory in the system's temporary directory.
     *
     * @access public
     *
     * @param optional boolean $delete  Delete the temporary directory at the
     *                                  end of the request?
     *
     * @return string       The pathname to the new temporary directory.
     *                      Returns false if directory not created.
     */
    function createTempDir( $delete = true )
    {
        $temp_dir = Util::getTempDir();
		
        if ( empty( $temp_dir ) ) 
			return false;

        /* Get the first 8 characters of a random string to use as a temporary
           directory name. */
        do 
		{
            $temp_dir .= '/' . substr( md5( uniqid( rand() ) ), 0, 8 );
        } while ( file_exists( $temp_dir ) );

        $old_umask = umask( 0000 );
        
		if ( !mkdir( $temp_dir, 0700 ) ) 
		{
            $temp_dir = false;
        } 
		else 
		{
            if ( $delete )
                _fileCleanup( $temp_dir );
        }
		
        umask( $old_umask );
        return $temp_dir;
    }
	
    /**
     * Determine if we are using a Secure (SSL) connection.
     *
     * @access public
     *
     * @return boolean      True if using SSL, false if not.
     */
    function usingSSLConnection()
    {
        return ( ( array_key_exists( 'HTTPS', $_SERVER ) && $_SERVER['HTTPS'] == 'on' ) || getenv( 'SSL_PROTOCOL_VERSION' ) );
    }

	/**
	 * @static
	 */
	function getString( $values ) 
	{
		if ( is_array( $values ) ) 
		{
			$result = "";
			
			foreach ( $values as $value )
				$result .= $value . " ";
		
			return $result;
		}
	
		return $values;
	}
	
    /**
     * OS independant PHP extension load. Remember to take care
     * on the correct extension name for case sensitive OSes.
     *
     * @param string $ext The extension name
     * @return bool Success or not on the dl() call
	 * @static
     */
    function loadExtension( $ext )
    {
        if ( !extension_loaded( $ext ) ) 
		{
            // if either returns true dl() will produce a FATAL error, stop that
            if ( ( ini_get( 'enable_dl' ) != 1) || ( ini_get( 'safe_mode' ) == 1 ) )
                return false;
            
            if ( stristr( getenv( "OS" ), "Windows" ) )
                $suffix = '.dll';
            else if ( PHP_OS == 'HP-UX' )
                $suffix = '.sl';
            else if ( PHP_OS == 'AIX' )
                $suffix = '.a';
            else if ( PHP_OS == 'OSX' )
                $suffix = '.bundle';
            else
                $suffix = '.so';
            
            return @dl( 'php_' . $ext . $suffix ) || @dl( $ext . $suffix );
        }
		
        return true;
    }

    /**
     * Start output compression, if requested.
     *
     * @access public
	 * @static
     */
    function compressOutput()
    {
        static $started;
		
		using( 'peer.http.agent.Browser' );
		
        if ( isset( $started ) )
            return;

        $brower = new Browser();

        /* Netscape =< 4 is so buggy with compression that we just turn it completely off for those browsers. */
        if ( ( ini_get( 'zlib.output_compression' ) != 1 ) && ( ( $browser->getBrowser() != 'mozilla' ) || ( $browser->getMajor() > 4 ) ) ) 
            ob_start( 'ob_gzhandler' );

        $started = true;
    }
	
	/**
	 * @static
	 */
	function run( $values ) 
	{
		extract( $values );
		$filename = Util::findProgram( $cmd );
			
		if ( empty( $filename ) )
			return array( "result" => "run_run_cmd_not_found" );
	
		if ( $fp = popen( "$filename $args", 'r' ) )
		{
			$buffer = "";
			
			while ( !feof( $fp ) )
				$buffer .= fgets( $fp, 4096 );

			popen( $fp );			
			return array( "result" => "ok", "output" => trim( $buffer ) );
		}
		
		return array( "result" => "run_run_exec_failed" );
	}

	/**
	 * @static
	 */	
	function sudoRun( $values ) // hardcoded just for testing purposes 
	{
		extract( $values );
	
		$filename = Util::findProgram( $cmd    );
		$sudo     = Util::findProgram( "sudo"  );
		$clear    = Util::findProgram( "clear" );
		
		if ( empty( $filename ) || empty( $sudo ) || empty( $clear ) )
			return array( "result" => "run_sudorun_wrong_parameters" );
		
		if ( ( $fhandle = popen( "$sudo -u root $clear \n\n", "w" ) ) ) 
		{
			$fsave = fputs( $fhandle, $password );
			@pclose( $fhandle );
		} 
		else
		{
			return array( "result" => "run_sudorun_sudo_failed" );
		}
					
		exec( "$sudo -u root $clear \n\n",$result_cmd );
		
		$result_cmd = "";
		exec( "$sudo -u root $filename $args \n\n",$result_cmd );
		
		return array( "result" => "ok", "output" => Util::getString( $result_cmd ) );
	}
	
	/**
	 * Find a system program. Do path checking.
	 *
	 * @static
	 */
	function findProgram( $program )
	{
		$path = array(
			'.',
			'./cgi',
			'/bin',
			'/sbin',
			'/usr/bin',
			'/usr/sbin',
			'/usr/local/bin',
			'/usr/local/sbin'
		);
    
		while ( $this_path = current( $path ) )
		{
			if ( is_executable( "$this_path/$program" ) )
				return "$this_path/$program";
			
			next( $path );
		}

		return "";
	}

	/**
	 * @static
	 */
	function findProgram2( $filename )
	{ 
		$path  = getenv( 'path' ) || getenv( 'PATH' ) || getenv( 'Path' ); 
		$paths = explode( ';', $path ); 
         
		// Throw in a few more places to look.
		$paths[] = './'; 
		$paths[] = './cgi/'; 
		$paths[] = '/bin/'; 
		$paths[] = '/sbin/';
		$paths[] = '/usr/bin/'; 
		$paths[] = '/usr/sbin/'; 
		$paths[] = '/usr/local/bin/'; 
		$paths[] = '/usr/local/sbin/'; 
         
		$whereis = '';
		
		reset( $paths ); 
		while ( !$whereis && ( list( , $p ) = each( $paths ) ) )
		{ 
			if ( $p )
			{ 
				$end = $p[strlen( $p )-1]; 
				
				if ( $end != '/' && $end != '\\' )
					$p .= '/'; 
                
				$f = $p . $filename; 
			
				if ( file_exists( $f ) )
				{ 
					$perms = fileperms( $f ); 
					$owner = fileowner( $f ); 
					$myuid = getmyuid(); 
				
					// Enhancement: If somebody could figure out how to chase down 
					// Group IDs without invoking exec() et al... 
					if ( ( $perms & 1 ) || ( ( $perms & 64 ) && ( $owner == $myuid ) ) ) 
						$whereis = $f;
				} 
			} 
		} 
	
		return $whereis;
	}
	 
	/**
	 * @static
	 */
	function programmAvailable( $tool )
	{
		$cmd = sprintf( "which %s", $tool );
		$location = trim( `$cmd` );

		if ( file_exists( $location ) )
		{
			if ( !( is_executable( "$location" ) ) )
				return -1; // user has no privileges
			else
				return 1;
		}
		else
		{
			return -2; // can't find software
		}
	}
	
	/**
	 * Execute a system program. return a trim()'d result.
	 * does very crude pipe checking.  you need ' | ' for it to work
	 * ie $program  = Util::executeProgram( 'netstat', '-anp|grep LIST' );
	 * NOT $program = Util::executeProgram( 'netstat', '-anp|grep LIST' );
	 * @static
	 */
	function executeProgram( $program, $args = '' )
	{
		$buffer  = '';
		$program = Util::findProgram( $program );

		if ( !$program )
			return;

		// see if we've gotten a |, if we have we need to do patch checking on the cmd
		if ( $args )
		{
			$args_list = split( ' ', $args );
		
			for ( $i = 0; $i < count( $args_list ); $i++ )
			{
				if ( $args_list[$i] == '|' )
				{
					$cmd     = $args_list[$i+1];
					$new_cmd = Util::findProgram( $cmd );
					$args    = ereg_replace( "\| $cmd", "| $new_cmd", $args );
				}
			}
		}

		// we've finally got a good cmd line... execute it
		if ( $fp = popen( "$program $args", 'r' ) )
		{
			while ( !feof( $fp ) )
				$buffer .= fgets( $fp, 4096 );
        
			return trim( $buffer );
		}
	}

	/**
	 * @static
	 */
	function freespace()
	{
		$output = array();
		exec( "df", $output );
		$output = join( "\n", $output );
		$space  = eregi_replace( ".* ([0-9]+)% /var.*", "\\1", $output );
	
		return $space;
	}

	/**
	 * @static
	 */
	function getMilliseconds()	
	{
		$p = explode( " ", microtime() );
		return round( ( $p[0] + $p[1] ) * 1000 );
	}

	/**
	 * @static
	 */
	function getMicrotime() 
	{ 
		list( $usec, $sec ) = explode( " ", microtime() ); 
		return ( ( float )$usec + ( float )$sec) * 2; 
	}
	 
	/**
	 * Strips the slashes from a variable if magic quotes is set for GPC.
	 * @static
	 */
	function gpcStripSlashes( $var ) 
	{
		if ( get_magic_quotes_gpc() ) 
		{
			if ( is_array( $var ) )
				$var = stripslashes_array( $var, true );
			else
				$var = stripslashes( $var );
		}
	
		return $var;
	}

	/**
	 * Turns a relative path into a absolute
	 * example: ../index.html to /siteroot/index.html
	 *
	 * @param $path is the relative input path
	 * @param $current_dir is directory of the file which uses the relative path
	 * @static
	 */
	function rel2abs( $path, $current_dir ) 
	{
		$path = str_replace( '\\', '/', $path );
		$current_dir = str_replace( '\\', '/', $current_dir );

		if ( ereg( '^/', $path ) ) 
		{
			$retpath = $path;
		} 
		else 
		{
			$current_dir = ereg_replace( '/$', '', $current_dir );
			$retpath = $current_dir . '/' . $path;
		}

		$retpath = preg_replace( '/\/([^\/]*)\/\.\./', '', $retpath );
		$retpath = ereg_replace( "/$", '',  $retpath );
		$retpath = str_replace( '/./', '/', $retpath );

		return $retpath;
	}

	/**
	 * Returns "http://" or "https://" depends on the current protocol.
	 * @static
	 */
	function getProtocol( $port = "" ) 
	{
		if ( !$port )
			$port = $_SERVER['SERVER_PORT'];
	
		switch ( $port ) 
		{
			case 443:
				return "https";
	
			case 80: 
		
			default:
				return "http";
		}
	}

	/**
	 * @static
	 */
	function getAPI()
	{
		$SERVER_SOFTWARE = getenv( 'SERVER_SOFTWARE' );
		$sapi_type = php_sapi_name();
	
		if ( stristr( $sapi_type, "apache" ) )
			return 'mod';

		if ( stristr( $sapi_type, "isapi" ) )
			return 'mod';

		if ( stristr( $sapi_type, "cgi" ) )
			return 'cgi';

		if ( stristr( $SERVER_SOFTWARE, 'PHP' ) && !stristr( $SERVER_SOFTWARE, 'SCRIPT' ) )
			return 'mod';

		return 'cgi';
	}

	/**
	 * @static
	 */
	function getScriptPath()
	{
		/*
		return str_replace( '//', '/', str_replace( '\\', '/', ( php_sapi_name() == "cgi" || php_sapi_name() == "isapi" )? 
			$_SERVER["PATH_TRANSLATED"] : 
			$_SERVER["SCRIPT_FILENAME"] 
		) );
		*/
		
		$SCRIPT_NAME = getenv( 'SCRIPT_NAME' );
	
		// may be not correct
		$PATH_INFO = getenv( 'PATH_INFO' );

		if ( php_sapi_name() == 'cgi' )
		{
			if ( stristr( getenv( "OS" ), "Windows" ) )
			{
				return dirname( $PATH_INFO );
			}
			else
			{
				if ( !empty( $PATH_INFO ) )
					return dirname( $PATH_INFO );
			}
		}
	
		return dirname( $SCRIPT_NAME );
	}

    /**
     * Caches the result of extension_loaded() calls.
     *
	 * @static
     * @param string $ext  The extension name.
     * @return boolean  Is the extension loaded?
     */
    function extensionExists( $ext )
    {
        static $cache;

        if ( !isset( $cache ) )
            $cache = array();
        
        if ( !isset($cache[$ext] ) )
            $cache[$ext] = extension_loaded( $ext );

        return $cache[$ext];
    }
	
	/**
	 * @static
	 */
	function docRoot()
	{
		$PATH_TRANSLATED = $_SERVER['PATH_TRANSLATED'];
		$SCRIPT_FILENAME = getenv( 'SCRIPT_FILENAME' );

		$SelfDir = Util::getScriptPath();

		if ( stristr( getenv( "OS" ), "Windows" ) ) 
		{
			$SelfDir   = strtolower( $SelfDir );
			$Directory = $PATH_TRANSLATED;
			$Directory = str_replace( '\\\\', '\\', $Directory );
			$temp      = str_replace( '\\',   '/',  $Directory );
			$SelfPos   = strpos( $temp, $SelfDir );
		} 
		else 
		{
			if ( ( php_sapi_name() == 'cgi' ) && isset( $PATH_TRANSLATED ) )
				$Directory = $PATH_TRANSLATED;
			else
				$Directory = $SCRIPT_FILENAME;
			
			if ( !empty( $SelfDir ) )
				$SelfPos = strpos( $Directory, $SelfDir );
			else
				$SelfPos = false;
		}

		return str_replace('\\', '/', substr( $Directory, 0, $SelfPos ) );
	}

	/**
	 * @static
	 */
	function getOS()
	{
		$SERVER_SOFTWARE = getenv( 'SERVER_SOFTWARE' );
		$PATH = getenv( 'PATH' );

		if ( stristr( $SERVER_SOFTWARE, 'Win' ) )
			return 'win';
	
		if ( stristr( $SERVER_SOFTWARE, 'Microsoft' ) )
			return 'win';
	
		if ( stristr( $SERVER_SOFTWARE, 'Unix' ) )
			return 'nix';

		if ( stristr( $PATH, 'C:' ) )
			return 'win';
	
		if ( stristr ( $PATH, '/X11' ) )
			return 'nix';
			
		/*
		// MS Windows 2000 or NT system
		if ( file_exists( "c:\winnt\system32\doskey.exe" ) )
			return 'win';
		// MS Windows 95/98/ME
		else if ( file_exists( "c:\windows\command\Doskey.com" ) )
			return 'win';
		// unix system
		else if ( file_exists( "/usr/ls" ) ) 
			return 'nix';
		else
			return 'nix';
		*/
	}

	/**
	 * Returns a unique time based string (8 chars).
	 * @static
	 */
	function getSpecialID()
	{ 
		$uab  = 57; 
		$lab  = 48; 

		$mic  = microtime(); 
		$smic = substr( $mic, 1, 2 ); 
		$emic = substr( $mic, 4, 6 ); 

		mt_srand( (double)microtime() * 1000000 );
			
		$ch    = ( mt_rand() % ( $uab - $lab ) ) + $lab; 
		$po    = strpos( $emic, chr( $ch ) ); 
		$emica = substr( $emic, 0, $po ); 
		$emicb = substr( $emic, $po, strlen( $emic ) ); 
		$out   = $emica . $smic . $emicb; 

		return strtr( $out, ".", "_" );
	}

	/**
	 * Strips last 8 chars (unique suffix).
	 * @static
	 */
	function stripSpecialID( $name = "" )
	{
		if ( !empty( $name ) )
			return substr( $name, 0, strlen( $name ) - 8 );		
	}
	
	/**
	 * @static
	 */
	function getDefaultBufferSize()
	{
		if ( stristr( getenv( "OS" ), "Windows" ) )
			return 4092;
		else
			return 4094;
	}
	
	/**
	 * Returns account size in mb.
	 *
	 * @static
	 */
	function dirsize( $dir, $basepath = '' )
	{
		$dh = @opendir( $basepath );

		if ( $dh )
		{
			$size = 0;
	
			while ( ( $file = readdir( $dh ) ) !== false )
			{
				if ( ( $file != "." ) && ( $file != ".." ) )
				{
					$path = $dir . "/" . $file;
				
					if ( is_dir( $path ) )
						$size += Util::dirsize( $path );
					else if ( is_file( $path ) )
						$size += filesize( $path );
				}
		
				closedir( $dh );
				$mgs_size = $size / 1048576;    
		
				return $mgs_size;
			}
		}
		else
		{
			return -1;
		}
	}

	/**
	 * Capture the return of a single function.
	 *
	 * Example: ob_capture( 'readfile', '/tmp/file.txt' );
	 * @return String of every output during the function
	 * @static
	 */
	function ob_capture()
	{
		$args = func_get_args();
			
		if ( function_exists( $args[0] ) )
		{
			ob_start();
			eval( array_shift( $args ) . '( $args[' . implode( '], $args[', array_keys( $args ) ) . '] );' );
			$str = ob_get_contents();
			ob_end_clean();
				
			return $str;
		}
		else
		{
			return PEAR::raiseError( "Execution failed." );
		}
	}

	/**
	 * @static
	 */
	function class_sprintf( $obj, $str, $args ) 
	{
		$etc = '$obj->' . implode(', $obj->', $args );
		return $args? eval( "return sprintf(\$str, $etc);" ) : sprintf( $str );
	}

	/**
	 * Returns a string containing an object dump (from print_r).
	 *
	 * @param $obj Object to dump
	 * @return String
	 * @static
	 */
	function sprint_r( $obj )
	{
		ob_start();
		print_r( $obj );
		$r = ob_get_contents();
		ob_end_clean();
		return $r;
	}

	/**
	 * Create a function wrapper.
	 *
	 * @param $new The name of the new function to create
	 * @param $old The name of the old function
	 * @return String of code for the new function
	 * @static
	 */
	function aliasMethod( $new, $old )
	{
		return "
				function $new() {
					\$args = func_get_args();
					eval( '\$ret = $old( \$args['
						. implode( '], \$args[', array_keys( \$args ) )
						. '] );' );
					return \$ret;
				}";
	}
	
    /**
     * Add a name=value pair to an URL, taking care of whether there
     * are existing parameters and whether to use ? or & as the glue.
     *
     * @access public
     *
     * @param string $url       The URL to modify
     * @param string $parameter The name=value pair to add.
     *
     * @return string The modified URL.
     */
    function addParameter( $url, $parameter )
    {
        if ( !empty( $parameter ) && strstr( $url, $parameter ) === false ) 
		{
            if ( substr( $parameter, 0, 1 ) == '?' )
                $parameter = substr( $parameter, 1 );

            $pos = strpos( $url, '?' );
 
 			if ( $pos !== false ) 
                $url = substr_replace($url, $parameter . ini_get( 'arg_separator.output' ), $pos + 1, 0 ); 
			else 
                $url .= '?' . $parameter;
        }
		
        return $url;
    }

    /**
     * Removes a name=value pair from a URL.
     *
     * @access public
     *
     * @param string $url       The URL to modify.
     * @param array $parameter  The array of parameters to remove.
     *
     * @return string  The modified URL.
     */
    function removeParameter( $url, $parameter = array() )
    {
        foreach ( $parameter as $value )
            $url = preg_replace( "/" . $value . "\=\w+\&?(?:amp;)?/", '', $url );

        /* If there are no more parameters left, or the last parameter was
           removed, remove the trailing '?' or '&'. */
        return rtrim( $url, '&?' );
    }
	
    /**
     * Return a session-id-ified version of $PHP_SELF.
     *
     * @access public
     *
     * @param string $query_string (optional) include the query string?
     */
    function selfURL( $query_string = false )
    {
        $url = $_SERVER['PHP_SELF'];

        if ( $query_string && !empty( $_SERVER['QUERY_STRING'] ) )
            $url .= '?' . $_SERVER['QUERY_STRING'];

        return Util::url( $url );
    }

    /**
     * Print a session-id-ified version of $PHP_SELF.
     *
     * @access public
     *
     * @param $query_string (optional) include the query string?
     */
    function pselfURL( $query_string = false )
    {
        echo Util::selfURL( $query_string );
    }
	
    /**
     * Return a session-id-ified version of $uri.
     *
     * @access public
     *
     * @param string  $uri                   The URI to be modified.
     * @param boolean $full                  Generate a full
     *                                       (http://server/path/) URL.
     * @param boolean $always_append_session Tack on the session ID even if
     *                                       cookies are present.
     *
     * @return string The url with the session id appended
     */
    function url( $uri, $full = false, $always_append_session = false, $use_ssl = 0 )
    {
        $protocol = 'http';

        if ( $full ) 
		{
            /* Store connection parameters in local variables. */
            $server_port = $_SERVER['SERVER_PORT'];
            $server_name = $_SERVER['SERVER_NAME'];

            if ( $use_ssl == 1 ) 
			{
                $protocol = 'https';
            } 
			else if ( $use_ssl == 2 ) 
			{
                if ( Util::usingSSLConnection() )
                    $protocol = 'https';
            }

            /* If using non-standard ports, add the port to the URL. */
            if ( ( ( $protocol == 'http'  ) && ( $server_port != 80  ) ) ||
                 ( ( $protocol == 'https' ) && ( $server_port != 443 ) ) ) 
			{
                $server_name .= ':' . $server_port;
            }

            /* Store the webroot in a local variable. */
            $webroot = ap_ini_get( "webroot", "settings" );
            $url = $protocol . '://' . $server_name;
			
            if ( substr( $uri, 0, 1 ) != '/' ) 
			{
                if ( substr( $webroot, -1 ) == '/' )
                    $url .= $webroot . $uri;
                else
                    $url .= $webroot . '/' . $uri;
            } 
			else 
			{
                $url .= $uri;
            }
        } 
		else 
		{
            $url = $uri;
        }

        if ( $always_append_session || !array_key_exists( session_name(), $_COOKIE ) )
            $url = Util::addParameter( $url, urlencode( session_name()) . '=' . session_id() );

        return ( $full? $url : htmlentities( $url ) );
    }
	
    /**
     * Print a session-id-ified version of the URI.
     *
     * @access public
     *
     * @param string  $uri                   the URI to be modified
     * @param boolean $full                  Generate a full
     *                                       (http://server/path/) URL.
     * @param boolean $always_append_session Tack on the session ID even if
     *                                       cookies are present.
     */
    function purl( $uri, $full = false, $always_append_session = false )
    {
        echo Util::url( $uri, $full, $always_append_session );
    }
	
    /**
     * Return a session-id-ified version of $uri, using the current
     * application's webroot setting.
     *
     * @access public
     *
     * @param string  $uri                   The URI to be modified.
     * @param boolean $full                  Generate a full
     *                                       (http://server/path/) URL.
     * @param boolean $always_append_session Tack on the session ID even if
     *                                       cookies are present.
     *
     * @return string The url with the session id appended
     */
    function applicationUrl( $uri, $full = false, $always_append_session = false )
    {
        ap_ini_get( "webroot", "settings" );

        if ( $full ) 
            return Util::url( $uri, $full, $always_append_session ); 
		else if ( substr( $webroot, -1 ) == '/' ) 
            return Util::url( $webroot . $uri, $full, $always_append_session ); 
		else 
            return Util::url( $webroot . '/' . $uri, $full, $always_append_session );
    }
	
    /**
     * Return an anchor tag with the relevant parameters.
     *
     * @access public
     *
     * @param string $url     The full URL to be linked to
     * @param string $status  An optional JavaScript mouse-over string
     * @param string $class   The CSS class of the link
     * @param string $target  The window target to point this link too
     * @param string $onclick JavaScript action for the 'onclick' event.
     * @param string $title   The link title (tooltip)
     *
     * @return string The full <a href> tag.
     */
    function link( $url, $status = '', $class = '', $target = '', $onclick = '', $title = '', $charset = 'ISO-8859-1' )
    {
        $ret = "<a href=\"$url\"";
		
        if ( !empty( $onclick ) )
            $ret .= " onclick=\"$onclick\"";
        
        if ( !empty( $status ) )
            $ret .= ' onmouseout="window.status=\'\';" onmouseover="window.status=\'' . @htmlspecialchars( addslashes( $status ), ENT_QUOTES, $charset ) . '\'; return true;"';
        
        if ( !empty( $class ) )
            $ret .= " class=\"$class\"";
        
        if ( !empty( $target ) )
            $ret .= " target=\"$target\"";
        
        if ( !empty( $title ) )
            $ret .= ' title="' . @htmlspecialchars( $title, ENT_QUOTES, $charset ) . '"';

        return "$ret>";
    }
	
    /**
     * Print an anchor tag with the relevant parameters.
     *
     * @access public
     *
     * @param string $url     The full URL to be linked to
     * @param string $status  An optional JavaScript mouse-over string
     * @param string $class   The CSS class of the link
     * @param string $target  The window target to point this link too
     * @param string $onclick JavaScript action for the 'onclick' event.
     * @param string $title   The link title (tooltip)
     */
    function plink( $url, $status = '', $class = '', $target = '', $onclick = '', $title = '' )
    {
       echo Util::link( $url, $status, $class, $target, $onclick, $title );
    }	
		
    /**
     * Construct a correctly-pathed link to an image.
     *
     * @access public
     *
     * @param          string $src  The image file.
     * @param optional string $attr Any additional attributes for the image tag.
     * @param optional string $dir  The root graphics directory.
     *
     * @return string The full image tag.
     */
    function img( $src, $attr = '', $dir = null )
    {
        $img  = '<img';
        $img .= ' src="' . ( empty( $dir )? '' : $dir . '/' ) . $src . '"';
        $img .= ' border="0"';
        
		if ( !empty( $attr ) ) 
		{
            $img .= ' ' . $attr;
            
			if ( preg_match( '/alt=([\'"])([^\1]*)\1/i', $attr, $match ) )
                $img .= ' title="'. $match[2] . '"';
        }
		
        if ( empty( $attr ) || !strstr( $attr, 'alt' ) )
            $img .= ' alt=""';
        
        $img .= ' />';
        return $img;
    }
	
    /**
     * Construct a correctly-pathed link to an image.
     *
     * @access public
     *
     * @param          string $src  The image file.
     * @param optional string $attr Any additional attributes for the image tag.
     * @param optional string $dir  The root graphics directory.
     */
    function pimg( $src, $attr = null, $dir = null )
    {
        echo Util::img( $src, $attr, $dir );
    }
	
	
	// private methods
	
    /**
     * Delete registered files at request shutdown.
     *
     * This function should never be called manually; it is registered as a
     * shutdown function by Util::deleteAtShutdown() and called automatically
     * at the end of the request. It will retrieve the list of folders and files
     * to delete from Util::deleteAtShutdown()'s static array, and then iterate
     * through, deleting folders recursively.
     *
     * Contains code from gpg_functions.php.
     * Copyright (c) 2002-2003 Braverock Ventures
     *
     * @access private
     */
    function _deleteAtShutdown()
    {
        $registered = Util::deleteAtShutdown();
        $dirs   = $registered[0];
        $files  = $registered[1];
        $secure = $registered[2];

        foreach ( $files as $file => $val ) 
		{
            /* Delete files */
            if ( $val && @file_exists( $file ) ) 
			{
                /* Should we securely delete the file by overwriting the
                   data with a random string? */
                if ( isset( $secure[$file] ) ) 
				{
                    $random_str = '';
                    
					for ( $i = 0; $i < filesize( $file ); $i++ )
                        $random_str .= chr( mt_rand( 0, 255 ) );
                    
                    $fp = fopen( $file, 'r+' );
                    fwrite( $fp, $random_str );
                    fclose( $fp );
                }
				
                @unlink( $file );
            }
        }

        foreach ( $dirs as $dir => $val ) 
		{
            /* Delete directories */
            if ( $val && @file_exists( $dir ) ) 
			{
                /* Make sure directory is empty. */
                $dir_class = dir( $dir );
				
                while ( ( $entry = $dir_class->read() ) !== false ) 
				{
                    if ( $entry != '.' && $entry != '..' )
                        @unlink( $dir . '/' . $entry );
                }
				
                $dir_class->close();
                @rmdir( $dir );
            }
        }
    }
} // END OF Util


/**
 * Removes given elements at request shutdown.
 * If called with a filename will delete that file at request shutdown.
 * If called with a directory will remove that directory and all files in
 * that directory at request shutdown.
 * If called with no arguments, unlink all elements registered.
 * The first time it is called, it initializes the array and registers itself
 * as a shutdown function - no need to do so manually.
 * The second parameter allows the unregistering of previously registered
 * elements.
 *
 * @access public
 *
 * @param optional string $filename   The filename to be deleted at the end of
 *                                    the request.
 * @param optional boolean $register  If true, then register the element for
 *                                    deletion, otherwise, unregister it.
 */
function _fileCleanup( $filename = false, $register = true )
{
  	static $dirs, $files;

    /* Initialization of variables and shutdown functions. */
    if ( !isset( $files ) || !is_array( $files ) ) 
	{
		$dirs  = array();
        $files = array();
        
		register_shutdown_function( '_fileCleanup' );
    }

    if ( $register ) 
	{
        if ( !$filename ) 
		{
            foreach ( $files as $file => $val ) 
			{
                /* Delete file */
                if ( $val && @file_exists( $file ) )
                    @unlink( $file );
            }
			
            foreach ( $dirs as $dir => $val ) 
			{
                /* Delete directories */
                if ( $val && @file_exists( $dir ) ) 
				{
                    /* Make sure directory is empty. */
                    $dir_class = dir( $dir );
                    
					while ( ( $entry = $dir_class->read() ) !== false ) 
					{
                        if ( $entry != '.' && $entry != '..' )
                            @unlink( $dir . '/' . $entry );
                    }
					
                    $dir_class->close();
                    @rmdir( $dir );
                }
            }
        } 
		else 
		{
            if ( @is_dir( $filename ) )
                $dirs[$filename]  = true;
            else
                $files[$filename] = true;
        }
    } 
	else 
	{
        $dirs[$filename]  = false;
        $files[$filename] = false;
    }
}

?>
