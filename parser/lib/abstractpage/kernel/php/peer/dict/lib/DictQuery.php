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


using( 'peer.dict.lib.DictBase' );


/**
 * To create query objects to search a DICT server.
 *
 * @package peer_dict_lib
 */
 
class DictQuery extends DictBase
{
	/**
	 * @access public
	 */
	var $term = "";
	
	/**
	 * @access public
	 */
	var $method = "";
	
	/**
	 * @access public
	 */
	var $searchdb = "*";
	
	/**
	 * @access public
	 */
	var $query_type = "DEFINE";
	
	/**
	 * @access public
	 */
	var $result = array();
	
	/**
	 * @access public
	 */
	var $numres = 0;
	
	/**
	 * @access public
	 */
	var $valid_methods = array(
		"exact",
		"prefix",
		"substring",
		"suffix",
		"re",
		"regexp",
		"soundex",
		"lev"
	);


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function DictQuery( $host = "", $port = "", $method = 'exact' )
	{
		$this->DictBase();
		
		$this->method = $method;
		$this->init( $host, $port );
	}
	
	
	/**
	 * @access public
	 */
	function init( $host, $port )
	{
		if ( $host ) 
			$this->set( "host", $host );
		
		if ( $port )
			$this->set( "port", $port );
		
		$this->connect();
		
		// get connection response line
		$line = fgets( $this->socket, $this->max_length );
		
		$this->parse_code( $line );
		
		if ( $this->is_error_code() )
			$this->print_code();
	}

	/**
	 * @access public
	 */ 
	function search( $term, $method, $db )
	{
		if ( !$this->is_method( $method ) )
			return PEAR::raiseError( "Invalid method: " . $method );
		
		$this->clear_results();
		
		$this->term     = $term;
		$this->method   = $method;
		$this->searchdb = $db;

		$query  = ( $method == "exact" )? "DEFINE $db " : "MATCH $db $method ";
		$query .= "\"".$term."\"\r\n";
		fputs( $this->socket, $query );
		$line = fgets( $this->socket, $this->max_length );
		ereg( "^[0-9]{3} ([0-9]+) .+", $line, &$reg );
		$this->numres = (int)$reg[1];

		if ( $method != "exact" )
		{
			$rlist = $this->read_data();
			$this->result = explode( "\r\n", chop( $rlist ) );
		}
		else
		{
			$regex  = "^[0-9]{3} \"([^\"]+)\" ([^\" ]+) \"([^\"]+)\"";
			$allres = array();
			$entry  = array();
			
			for ( $i = 0; $i < $this->numres; $i++ )
			{
				$line = chop( fgets( $this->socket, $this->max_length ) );
				
				if ( $line == "" )
					continue;
					
				ereg( $regex, $line, &$reg );
				
				$entry["term"]       = $reg[1];
				$entry["dbcode"]     = $reg[2];
				$entry["dbname"]     = $reg[3];
				$entry["definition"] = $this->read_data();

				$this->result[$i] = $entry;
			}
		}
		
		return true;
	}	

	/**
	 * @access public
	 */
	function define( $term, $db = "*" )
	{
		$this->search( $term, "exact", $db );
		$this->close();
	}

	/**
	 * @access public
	 */
	function match( $term, $method = "prefix", $db = "*" )
	{
		$this->search( $term, $method, $db );
		$this->close();
	}

	/**
	 * @access public
	 */
	function is_method( $method )
	{
		return in_array( $method, $this->valid_methods );
	}

	/**
	 * @access public
	 */
	function clear_results()
	{
		$this->numres = 0;
		$this->result = array();
	}
} // END OF DictQuery

?>
