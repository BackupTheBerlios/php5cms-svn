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


using( 'search.dig.lib.NetDigResource' );
using( 'search.dig.lib.NetDigResult' );


/**
 * A nice friendly OO interface to dig.
 *
 * @package search_dig_lib
 */

class NetDig extends PEAR
{
	/**
	 * The address to dig
	 *
	 * @var string $address
	 * @access public
	 */
	var $address;
	
	/**
	 * The server to use for digging
	 *
	 * @var string $server
	 * @access public
	 */
	var $server;
	
	/**
	 * The type of DNS records to dig for
	 *
	 * @var string $query_type
	 * @access public
	 */
	var $query_type;

	/**
	 * The last system command executed (for debugging)
	 *
	 * @var string $cmd
	 * @access public
	 */
	var $cmd;

	/**
	 * The raw output of the system command (for debugging)
	 *
	 * @var string $raw_data
	 * @access public
	 */
	var $raw_data;

	/**
	 * The location of the system dig program
	 *
	 * @var string $dig_prg
	 * @access public
	 */
	var $dig_prog;

	/**
	 * The parsed result of the last dig
	 *
	 * @var string $result
	 * @access public
	 */
	var $result;


	/**
	 * Constructor
     * Called when a new NetDig object is initialized.
	 *
	 * @param string     [$address]  The address to dig (can be set 
	 *                               using the $address property as well)
	 *
	 * @param string     [$server]   The server to dig at (can be set 
	 *                               using the $server property as well)
	 *
	 * @access public
	 */
	function NetDig( $address = false, $server = false )
	{
		$this->address    = $address;
		$this->server     = $server;
		$this->query_type = false;
		$this->cmd        = '';
		$this->raw_data   = '';
		$this->result     = false;
		$this->dig_prog   = trim( `which dig` );
		
		if ( !$this->dig_prog )
		{
			$this = new PEAR_Error( "Couldn't find system dig program." );
			return;
		}
	}


	/**
	 * Does a dig of the given address (or $this->address).
	 *
	 * @param string           [$address] The address to dig (can be set 
	 *                                using the $address property as well)
	 *
	 * @return object NetDigResult    $obj   A new NetDigResult object
	 * @access public
	 */
	function dig( $address = false )
	{
		if ( $address )
			$this->address = $address;

		if ( !$this->address )
			return PEAR::raiseError( "No address specified." );

		if ( !$this->_validate_type() )
			return PEAR::raiseError( $this->query_type . " is an invalid query type." );

		$cmd = escapeshellcmd(
			sprintf( "%s %s %s %s",
				$this->dig_prog,
				( $this->server? '@' . $this->server : '' ),
				$this->address,
				( $this->query_type? $this->query_type : '' )
			)
		);

		$this->cmd      = $cmd;
		$this->raw_data = `$cmd`;
		$this->raw_data = trim(	$this->raw_data );

		return $this->_parse_data();
	}
	
	
	// private methods
		
	/**
	 * Validates the value of $this->query_type.
	 *
	 * @return boolean	$return   True if $this->query_type is a 
	 *                            valid dig query, otherwise false
	 * @access private
	 */
	function _validate_type()
	{
		$return = true;
		
		if ( $this->query_type )
		{
			$this->query_type = strtolower( $this->query_type );
			
			switch ( $this->query_type )
			{
			    case 'a':
				
			    case 'any':
				
			    case 'mx':
				
			    case 'ns':
				
			    case 'soa':
				
			    case 'hinfo':
				
			    case 'axfr':
				
			    case 'txt':
					break;
			    
				default:
					$return = false;
			}
		}
		
		return $return;
	}

	/**
	 * Parses the raw data in $this->raw_data.
	 *
	 * @return obj NetDigResult  $return   A NetDigResult object
	 * @access private
	 */
	function _parse_data()
	{
		if ( !$this->raw_data )
			return PEAR::raiseError( "No raw data to parse." );

		$regex = '/' .
			'^;(.*?)' .
			';; QUESTION SECTION\:(.*?)' .
			'(;; ANSWER SECTION\:(.*?))?' .
			'(;; AUTHORITY SECTION\:(.*?))?' .
			'(;; ADDITIONAL SECTION\:(.*?))?' .
			'(;;.*)' .
			'/ims';

		if ( preg_match( $regex, $this->raw_data, $matches ) )
		{
			$result = new NetDigResult();
			$temp   = explode( "\n", trim( $matches[1] ) );
			
			if ( preg_match( '/DiG (.*?) /i', $temp[0], $m ) )
				$result->dig_version = trim( $m[1] );
			
			if ( preg_match( '/status: (.*?), id: (.*?)$/i', $temp[3], $m ) )
			{
				$result->status = trim( $m[1] );
				$result->id = trim( $m[2] );
			}

			if ( preg_match( '/flags: (.*?); query: (.*?), answer: (.*?), authority: (.*?), additional: (.*?)$/i', $temp[4], $m ) )
			{
				$result->flags            =      trim( $m[1] );
				$result->query_count      = (int)trim( $m[2] );
				$result->answer_count     = (int)trim( $m[3] );
				$result->authority_count  = (int)trim( $m[4] );
				$result->additional_count = (int)trim( $m[5] );
			}

			// query section
			$line = trim( preg_replace( '/^(;*)/', '', trim( $matches[2] ) ) );
			list( $host, $class, $type ) = preg_split( '/[\s]+/', $line, 3 );
			$result->query[] = new NetDigResource( $host, false, $class, $type, false );

			// answer section
			$temp = trim($matches[4]);
			
			if ( $temp )
			{
				$temp = explode( "\n", $temp );
				
				if ( count( $temp ) )
				{
					foreach( $temp as $line )
						$result->answer[] = $this->_parse_resource( $line );
				}
			}

			// authority section
			$temp = trim( $matches[6] );
			
			if ( $temp )
			{
				$temp = explode( "\n", $temp );
				
				if ( count( $temp ) )
				{
					foreach( $temp as $line )
						$result->authority[] = $this->_parse_resource( $line );
				}
			}

			// additional section
			$temp = trim( $matches[8] );
			
			if ( $temp )
			{
				$temp = explode( "\n", $temp );

				if ( count( $temp ) )
				{
					foreach( $temp as $line )
						$result->additional[] = $this->_parse_resource( $line );
				}
			}

			// footer
			$temp = explode( "\n", trim( $matches[9] ) );
			
			if ( preg_match( '/query time: (.*?)$/i', $temp[0], $m ) )
				$result->query_time	= trim( $m[1] );
			
			if ( preg_match( '/server: (.*?)#(.*?)\(/i', $temp[1], $m ) )
			{
				$result->dig_server	= trim( $m[1] );
				$result->dig_port	= trim( $m[2] );
			}

			// done
			$result->consistency_check = (
				( count( $result->query      ) == $result->query_count      ) &&
				( count( $result->answer     ) == $result->answer_count     ) &&
				( count( $result->authority  ) == $result->authority_count  ) &&
				( count( $result->additional ) == $result->additional_count )
			);

			return $result;

		}

		return PEAR::raiseError( "Cannot parse raw data." );
	}

	/**
	 * Parses a resource record line.
	 *
	 * @param string  $line	The line to parse
	 * @return obj NetDigResource  $return   A NetDigResource object
	 * @access private
	 */
	function _parse_resource( $line )
	{
		// trim and remove leading ;, if present
		$line = trim( preg_replace( '/^(;*)/', '', trim( $line ) ) );

		if ( $line )
		{
			list( $host, $ttl, $class, $type, $data ) = preg_split( '/[\s]+/', $line, 5 );
			return new NetDigResource( $host, $ttl, $class, $type, $data );
		}

		return false;
	}
} // END OF NetDig

?>
