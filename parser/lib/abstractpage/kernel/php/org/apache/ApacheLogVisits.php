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


using( 'org.apache.ApacheLogReader' );


/**
 * @package org_apache
 */
 
class ApacheLogVisits extends ApacheLogReader
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ApacheLogVisits( $logfile, $logformat = "" )
	{
		$this->ApacheLogReader( $logfile, $logformat = "" );
	}


	/**
	 * @access public
	 */
	function getVisits( $page = "",$date_from = 0, $date_to = 0 )
	{
		if ( ( $page != "" ) && ( !array_key_exists( 'Request', $this->_infos ) ) )
			return PEAR::raiseError( "Cannot select a page." );
		
		if ( ( ( $date_from != 0 ) || ( $date_to != 0 ) ) && !( ( array_key_exists( 'DateTime', $this->_infos ) ) || ( array_key_exists( 'DateTimeF', $this->_infos ) ) ) )
			return PEAR::raiseError( "Cannot select a date." );
		
		$log = $this->_logs;
		
		if ( ( $page != "" ) || ( $date_from != "" ) || ( $date_to != "" ) )
		{
			array_unshift( $log, array( $page, $date_from, $date_to ) );
			$log = array_filter( $log, "_filter" );		
		}
		
		$ret[0] = count( $log );
		$log = array_filter( $log, "_distinctip" );
		
		$log = ApacheLogReader::_reindexArray( $log );
		$ret[1] = count( $log );
		
		return $ret;
	}
} // END OF ApacheLogVisits


function _distinctip( $var )
{	
	static $iplist = array();
				
	if ( in_array( $var['host'], $iplist ) )
	{
		return false;
	}
	else
	{
		array_push($iplist,$var['host']);
		return true;
	}
}

function _filter( $var )
{	
	static $page;
	static $date_from;
	static $date_to;
				
	if ( isset( $var[0] ) )
	{
		$page = $var[0];
		
		if ( substr( $page, 0, 1 ) != "/" ) 
			$page = "/" . $page;
			
		$date_from = $var[1];
		$date_to   = $var[2];
		
		return false;
	}
				
	if ( $page != "" )
	{
		if ( substr( $var['Request'], 0, strlen( $page ) ) != $page )  
			return false;
	}
				
	if ( $date_from != "" )
	{
		if ( mktime( $var['DateTime']['hour'], $var['DateTime']['minute'], $var['DateTime']['second'], $var['DateTime']['month'], $var['DateTime']['day'], $var['DateTime']['year'] ) < $date_from ) 
			return false;
		}
				
	if ( $date_to != "" )
	{
		if ( mktime( $var['DateTime']['hour'], $var['DateTime']['minute'], $var['DateTime']['second'], $var['DateTime']['month'], $var['DateTime']['day'], $var['DateTime']['year'] ) > $date_to ) 
			return false;
	}
	
	return true;
}

?>
