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
 * This class is derived from the perl module Net::Dict
 * but ended up being far from similar code. However functionality 
 * is virtually identical to the perl module.
 *
 * @package peer_dict
 */

class Dict extends PEAR
{
	/**
	 * This defines what dictionaries we want to look for by default.
	 * Should not need any changes. Can be set with $this->setDicts( $arrayofnewdicts );
	 * @access public
	 */
	var $dicts = array( "*" );
	
	/**
	 * If the user doesn't specify any server, pick a random one of these:
	 * Should not need any changes.
	 * @access public
	 */
	var $default_hosts = array(
		"dict.org",
		"little-charlie.isri.unlv.edu"
	);

	/**
	 * After initializing, this var contains the servername we are conncted to.
	 * READ-ONLY!
	 * @access public
	 */
	var $host = "";

	/**
	 * After initializing, this var contains the port we are conncted to.
	 * READ-ONLY!
	 * @access public
	 */
	var $port;

	/**
	 * If there's been an error, this var contains the explicit error message.
	 * If this is "" you can be quite sure there hasn't been an error.
	 * @access public
	 */
	var $error = "";

	/**
	 * Connection Pointer.
	 * @access public
	 */
	var $fp = 0;
	

	/**
	 * Constructor
	 *
	 * $newdictclass = new Dict("dict.org");			OR
	 * $newdictclass = new Dict("dict.org", 2628);		OR
	 * $newdictclass = new Dict("dict.org", 2628, 1);
	 *
	 * @access public
	 */
	function Dict($host = "", $port = 2628 )
	{
		$this->port = $port;
		
		if ( $host == "" )
		{
			srand( (double)microtime() * 1000000 );
			
			// Get a random server off the default list.
			$this->host = $this->default_hosts[rand( 0, count( $this->default_hosts ) )];
		}
		else
		{
			$this->host = $host;
		}
		
		// And now, we initialize the connection.
		$this->fp = fsockopen( $this->host, $this->port, &$errno, &$errstr );

		// Something went wrong..
		if ( $this->fp == false )
		{
			$this = new PEAR_Error( "Could not establish connection." );
			return;
		}
		
		list( $status, $msg ) = $this->_getStatus();

		if ($status != "220")
		{
			$this = new PEAR_Error( "Could not establish connection." );
			return;
		}
	}

	/**
	 * Destructor
	 *
	 * Logs off the server the correct way, rather than just killing the connection.
	 * (Unforunately PHP doesn't seem to support destructors, so please call this by hand!)
	 */
	function _Dict()
	{
		if ( !$this->fp )
			return false;
			
		$this->fwrite( $this->fp, "QUIT\n" );
		$this->fclose( $this->fp );

		return true;
	}

	
	/**
	 * Used to set the arrays to do search functions in.
	 * $this->setDicts( array( "dict1", "dict2", "dict3" ) );
	 *
	 * @access public
	 */
	function setDicts( $dictarray )
	{
		// Only arrays allowed.
		if ( !is_array( $dictarray ) )
			return false;
			
		$this->dicts = $dictarray;
		return true;
	}

	/**
	 * @access public
	 */
	function define($word)
	{
		$ret = "";
		
		if ( $word == "" )
			return "";

		$ra = array();		
		$i  = 0;
		
		while ( $dictionary = $this->dicts[$i] )
		{
			$i += 1;
	
			fwrite( $this->fp, "define $dictionary \"$word\"\n" );
			list( $status, $msg ) = $this->_getStatus();

			if ( $status == "552" ) 
			{
				$this->error = "No match. ($dictionary)\n";
				$ra[] = array( $dictionary, "", "No Match." );
			} 
			else if ( $status == "551" )
			{
				$this->error .= "Invalid dictionary. ($dictionary)\n";
				$ra[] = array( $dictionary, "", "Invalid dictionary." );
			} 
			else if ( $status == "150" )
			{	
				preg_match( "/(\d+) .*/", $msg, $regs );
				$numdefs = $regs[1];

				while ( $numdefs-- )
				{
					list( $status, $msg ) = $this->_getStatus();

					if ( $status == "151" )
					{
						unset( $regs );
						preg_match( "/^\"(.+)\" (\w+) \"(.+)\"/", $msg, $regs );
						
						$word     = $regs[1];
						$dictname = $regs[2];
						$dict     = $regs[3];
						
						$line = "";
						while ( !ereg( "^\.", $line ) )
						{
							$line = fgets( $this->fp, 4096 );
							$line = chop( chop( $line ) );
							
							if ( !ereg( "^\.", $line ) )
								$def .= "$line\n";
						}
						
						$ra[] = array( $dict, $def, "" );
						unset( $def );
					}
				}
				
				// and the last status message..
				list( $status, $msg ) = $this->_getStatus();
				
				// return $matches;
				$this->error = ""; // no errors
			}
		}
		
		return $ra;
	}

	/**
 	 * Do an inexact match on a word. Return list of close matched ones.
 	 *
	 * Always returns an array, but defines $this->error on a error, in which case you can see in what
 	 * dictionary this happened:
 	 * list($baddictionary, $nothing, $errormessage) = $returned[0];
 	 * Upon succes it returns an array of which each element is an array($dictionary, $description, $empty);
	 *
	 * @access public
 	 */
	function match( $word, $match )
	{
		$ret = "";

		if ( $word == "" )
			return "";

		$i = 0;
		while ( $dictionary = $this->dicts[$i] )
		{
			$i += 1;
			fwrite( $this->fp, "match $dictionary $match \"$word\"\n");
			list( $status, $msg ) = $this->_getStatus();		

			if ( $status == "152" )
			{
				$line = "";
				while ( !ereg( "^\.", $line ) )
				{
					$line = fgets( $this->fp, 4096 );
					$line = chop( chop( $line ) );
					
					if ( !ereg( "^\.", $line ) )
					{
						unset( $regs );
						ereg( "(\w+) \"(.*)\"", $line, $regs );
						$ar[] = array( $regs[1], $regs[2], "" );
					}
				}
				
				list( $status, $msg ) = $this->_getStatus();
				$this->error = ""; // No errors...
			} 
			else if ( $status == "552" )
			{
				$this->error .= "No match. ($dictionary)\n";
				$ar[] = array( $dictionary, "", "No match." );
			} 
			else if ( $status == "551" )
			{
				$this->error .= "Invalid strategy. ($dictionary)\n";
				$ar[] = array( $dictionary, "", "Invalid strategy." ); // No match
			} 
			else 
			{
				$this->error .= "Unknown error. ($dictionary)\n";
				$ar[] = array( $dictionary, "", "Unknown error. ($dictionary)\n" );
			}
		}
		
		return $ar;
	}

	/**
 	 * Returns false on an error, otherwise returns an array with the supported strats.
 	 * list($supportedstrat, $stratdescription) = $returned[0];
	 *
	 * @access public
 	 */
	function showStart()
	{
		$line = "";
		fwrite( $this->fp, "SHOW STRAT\n");
		list( $status, $msg ) = $this->_getStatus();	
		
		if ( $status == "111" )
		{
			while ( !ereg( "^\.", $line ) )
			{
				$line = fgets( $this->fp, 4096 );
				$line = chop( chop( $line ) );
				
				if (!ereg("^\.", $line))
				{
					if (preg_match("^/(.*) \"(.*)\"$/", $line, $regs))
					{
						$ret[] = array($regs[1], $regs[2]);
					}
				}
			}
			$this->_getStatus(); 
			return $ret;
		} else {
			return false;
		}
	}

	/**
 	 * Returns false on error, otherwise it returns all info on dbname in a string.
	 *
	 * @access public
 	 */
	function dbInfo( $dbname )
	{
		if ( $dbname == "" )
			return false;
			
		$line = "";
		fwrite( $this->fp, "SHOW INFO $dbname\n" );
		list( $status, $msg ) = $this->_getStatus();
	
		if ( $status == "112" )
		{
			while ( !ereg( "^\.", $line ) )
			{
				$line = fgets( $this->fp, 4096 );
				$line = chop( chop( $line ) );
				
				if ( !ereg( "^\.", $line ) )
					$info .= $line."\n";
			}
			
			$this->_getStatus();
			return $info;
		} 
		else
		{
			return false;
		}
	}

	/**
 	 * Returns false on error, otherwise it returns all info of the server in a string.
	 *
	 * @access public
 	 */
	function serverInfo()
	{
		$line = "";
		fwrite( $this->fp, "SHOW SERVER\n");
		list( $status, $msg ) = $this->_getStatus();
		
		if ( $status == "114" )
		{
			while ( !ereg( "^\.", $line ) )
			{
				$line = fgets( $this->fp, 4096 );
				$line = chop( chop( $line ) );
				
				if ( !ereg( "^\.", $line ) )
					$info .= $line."\n";
			}
			
			$this->_getStatus();
			return $info;
		} 
		else
		{
			return false;
		}
	}

	/**
 	 * Returns false on an error, otherwise returns an array with the all databases on server.
 	 * list($dbid, $dbdescription) = $returned[0];
	 *
	 * @access public
 	 */
	function showDatabases()
	{
		$line = "";
		fwrite( $this->fp, "SHOW DB\n" );
		list( $status, $msg ) = $this->_getStatus();	

		if ( $status == "110" )
		{
			while ( !ereg( "^\.", $line ) )
			{
				$line = fgets( $this->fp, 4096 );
				$line = chop( chop( $line ) );
				
				if ( !ereg( "^\.", $line ) )
				{
					if ( preg_match( "/^(.*) \"(.*)\"$/", $line, $regs ) )
						$ret[] = array( $regs[1], $regs[2] );
				}
			}
			
			$this->_getStatus(); 
			return $ret;
		} 
		else 
		{
			return false;
		}
	}
	
	
	// private methods

	/**
	 * @access private
	 */	
	function _getStatus()
	{
		$status = "000";
		$msg    = "Error";
		
		if ( $this->fp )
		{
			$line = fgets( $this->fp, 4096 );
			
			if ( preg_match( "/^(\d\d\d) (.*)/", $line, $regs ) )
			{
				$status = $regs[1];
				$msg    = $regs[2];
			}
		}

		return array( $status, $msg );
	}
} // END OF Dict

?>
