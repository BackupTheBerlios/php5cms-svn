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


define( "DEBUG_TEXT",    1 ); // plain text output
define( "DEBUG_HTML",    2 ); // html output with NL converted to <BR>
define( "DEBUG_JS",      4 ); // javascript output
define( "DEBUG_FILE",    8 ); // log file output
define( "DEBUG_SYSLOG", 16 ); // error_log output
define( "DEBUG_QUEUE",  32 ); // queue debug messages

$GLOBALS["AP_DEBUGQUEUE"] = array();


/**
 * Debug Class
 *
 * Error Reference:
 * 1 	E_ERROR
 * 2 	E_WARNING
 * 4 	E_PARSE
 * 8 	E_NOTICE
 * 16 	E_CORE_ERROR
 * 32 	E_CORE_WARNING
 * 64 	E_COMPILE_ERROR
 * 128 	E_COMPILE_WARNING
 * 256 	E_USER_ERROR
 * 512 	E_USER_WARNING
 * 1024	E_USER_NOTICE
 *
 * @package util
 */
 
class Debug extends PEAR
{
	/**
	 * printf format to be used on message
	 * @access public
	 */
	var $format;
	
	/**
	 * default debug-level
	 * @access public
	 */
    var $level = 0;
	
	/**
	 * debug output type
	 * @access public
	 */
    var $output = 0;
	
	/**
	 * default log file
	 * @access public
	 */
    var $logfile = "debug.log";
	
	/**
	 * existing error handler
	 * @access public
	 */
    var $oldhandler = "";
	
	/**
	 * debug mode on or off
	 * @access public
	 */
    var $mode = true;

	/**
	 * HTML OUTPUT STYLE
	 * @access public
	 */
    var $style = "";
	
	/**
	 * text prefix
	 * @access public
	 */
    var $prefix = "*****\n\t";
	
	/**
	 * post fix
	 * @access public
	 */
    var $postfix = "\n*****\n";

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function Debug( $debug_level = 0, $output = DEBUG_HTML )
    {
		$this->SetLevel( $debug_level );
        $this->SetOutput( $output );
    }


	/**
	 * @access public
	 */	
    function SetLevel( $debug_level )
    {   
		$this->level = $debug_level;
    }

	/**
	 * @access public
	 */
    function SetOutput( $output )
    {   
		$this->output = $output;
    }

	/**
	 * @access public
	 */
    function SetLogFile( $filename )
    {   
		$this->logfile = "debug.log";
    }

	/**
	 * @access public
	 */
    function SetHTML( $style )
    {   
		$this->style= $style;
    }

	/**
	 * @access public
	 */
    function SetText( $prefix, $postfix )
    {   
		$this->prefix  = $prefix;
        $this->postfix = $postfix;
    }

	/**
	 * @access public
	 */
    function On()
    {   
		$this->mode = true;
    }
    
	/**
	 * @access public
	 */
    function Off()
    {   
		$this->mode = false;
    }
	
	/**
	 * @access public
	 */
    function SetFormat( $format )
    {   
		$this->format = $format;
    }

	/**
	 * @access public
	 */
    function ErrorHandler( $errno, $errstr, $errfile, $errline )
    {   
		$msg = "FILE: " . basename( $errfile ) . " LINE: $errline MSG: [$errstr]";
        
		// determine debug level:
        // *ERROR:   level 1
        // *WARNING: level 2
        // *NOTICE:  level 3
        switch ( $errno )
        {
            case E_ERROR: 
				$level = 1; 
				break;
            
			case E_WARNING: 
				$level = 2; 
				break;
            
			case E_NOTICE: 
				$level = 3; 
				break;
            
			case E_CORE_ERROR: 
				$level = 1; 
				break;
            
			case E_CORE_WARNING: 
				$level = 2; 
				break;
            
			case E_COMPILE_ERROR: 
				$level = 1; 
				break;
            
			case E_COMPILE_WARNING: 
				$level = 2; 
				break;
            
			case E_USER_ERROR: 
				$level = 1; 
				break;
            
			case E_USER_WARNING: 
				$level = 2;
				break;
            
			case E_USER_NOTICE: 
				$level = 3; 
				break;
        }
		
        return $this->Message( $msg, $level );
    }

	/**
	 * @access public
	 */
    function Message( $msg, $level = 0 )
    {   
		// check debug mode
        if ( !$this->mode )
            return;
        
		// output only if the msg level is greater or equal than debug level
        if ( $level > $this->level )
            return;
		
        // check format
        if ( $this->format )
            $msg = sprintf( $this->format, $msg );

        // text format
        if ( $this->output & DEBUG_TEXT )
        	echo $this->prefix . "$msg" . $this->postfix;
        
        // HTML format
        if ( $this->output & DEBUG_HTML )
        {   
			$out = nl2br( $msg );
            echo ( $this->style? sprintf( $this->style, $out ) : $out );
        }
		
        // Javascript format
        if ( $this->output &  DEBUG_JS )
        {   
			$out = addcslashes( $msg, "/'" );
            echo "<script>alert('$out');</script>";
        }
		
        // Log file
        if ( $this->output & DEBUG_FILE )
        {   
			// logfile name
            $logfile = $this->logfile;
            
			// check log file status
            if ( file_exists( $logfile ) && !is_writable( $logfile ) )
				return PEAR::raiseError( "File $logfile is not writeable." );
            
            // attempt to open file
            $fp = fopen( $this->logfile, "a" );
            
			if ( !$fp )
				return PEAR::raiseError( "Cannot create logfile $logfile." );
				
            // write message
            $out = strftime( "%d %b %y %H:%m" ) . " [$level] $msg\n";
            fwrite( $fp, $out );

            // close file
            fclose( $fp );
        }
		
        // error_log
        if ( $this->output & DEBUG_SYSLOG )
        	error_log( $msg, 0 );
			
        // error_log
        if ( $this->output & DEBUG_SYSLOG )
        	$GLOBALS["AP_DEBUGQUEUE"][] = $msg;
			
		return true;
    }
	
	/**
	 * Get queue and flush.
	 *
     * @return array queue
     * @access public
	 */
	function GetQueue()
	{
		// NOTE: maybe it requires a hard copy here...
		
		$queue = $GLOBALS["AP_DEBUGQUEUE"];
		$GLOBALS["AP_DEBUGQUEUE"] = array();
		
		return $queue;
	}
} // END OF Debug

?>
