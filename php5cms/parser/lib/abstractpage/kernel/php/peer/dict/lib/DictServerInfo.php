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
 * Class DictServerInfo
 *
 * To generate objects containing DICT server information.
 * Extends the DictBase class.
 *
 * @package peer_dict_lib
 */
 
class DictServerInfo extends DictBase
{
	/**
	 * @access public
	 */
	var $info = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function DictServerInfo( $host = "", $port = "", $extended = false )
	{
		$this->DictBase();
		
		$this->init( $host, $port, $extended );
	}

	
	/**
	 * @access public
	 */
	function init( $host, $port, $extended )
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

		// extract capabilities info from response line
		ereg( "^[0-9]{3} (.*) <([^<]*)> <(.*)>", $line, &$reg );
		
		$this->info["signature"]    = $reg[1];
		$this->info["capabilities"] = explode( ".", $reg[2] );
		$this->info["msg-id"]       = $reg[3];
		
		// get description on the server and store verbatim
		$this->info["server"] = $this->show( "SERVER" );

		// get the dbs and strategies for this server
		$dbs = $this->show( "DB" );
		$this->store( "databases", $dbs );
		$strats = $this->show( "STRAT" );
		$this->store( "strategies", $strats );

		// get the description of each database
		// if extended info is requested
		if ( $extended )
			$this->get_dbs_info();

		// close the connection
		$this->close();
	}

	/**
	 * @access public
	 */
	function show( $str )
	{
		fputs( $this->socket, "SHOW " . $str . "\r\n" );
		$tmp  = chop( fgets( $this->socket, $this->max_length ) );
		$tmp2 = explode( " ", $tmp );
		
		if ( $str == "DB" )
			$this->info["num_dbs"] = (int)$tmp2[1];
			
		if ( $str == "STRAT" )
			$this->info["num_strat"] = (int)$tmp2[1];
			
		$data = $this->read_data();
		$tmp  = fgets( $this->socket, $this->max_length );
		
		return $data;
	}

	/**
	 * @access public
	 */
	function store( $str, $data )
	{
		$arr = explode( "\r\n", $data );
		$out = array();
		
		for ( $i = 0; $i < count( $arr ); $i++ )
		{
			if ( chop( $arr[$i] ) == "" )
				continue;
			
			ereg( "^([^ ]+) \"?([^\"]+)\"?", $arr[$i], &$reg );
			$out[$reg[1]] = $reg[2];
		}
		
		$this->info[$str] = $out;
	}

	/**
	 * @access public
	 */
	function get_dbs_info()
	{
		$ndb    = $this->info["num_dbs"];
		$dbs    = $this->info["databases"];
		$dbinfo = array();
		
		while ( list( $k, $v) = each( $dbs ) )
			$dbinfo[$k] = $this->show( "INFO " . $k );
		
		$this->info["dbs_desc"] = $dbinfo;
	}

	/**
	 * @access public
	 */
	function get_info( $str )
	{
		return $this->info[$str];
	}
} // END OF DictServerInfo

?>
