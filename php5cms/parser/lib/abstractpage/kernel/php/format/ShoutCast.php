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
 * @package format
 */
 
class ShoutCast extends PEAR
{
	/**
	 * @access public
	 */
	var $host;
	
	/**
	 * @access public
	 */
	var $port;
	
	/**
	 * @access public
	 */
	var $passwd;
	
	/**
	 * @access private
	 */
	var $_xml;
	

	/**
	 * @access public
	 */
	function openstats() 
	{
		$fp = fsockopen( $this->host, $this->port, $errno, $errstr, 10 );
		
		if ( !$fp ) 
		{
			return PEAR::raiseError( "$errstr ($errno)" );
		} 
		else 
		{
		    fputs( $fp, "GET /admin.cgi?pass=" . $this->passwd . "&mode=viewxml HTTP/1.0\r\n" );
		    fputs( $fp, "User-Agent: Mozilla\r\n\r\n" );
			
		    while ( !feof( $fp ) )
     	   		$this->_xml .= fgets( $fp, 512 );
		    
		    fclose( $fp );

		    if ( stristr( $this->_xml, "HTTP/1.0 200 OK" ) == true )
				$this->_xml = trim( substr( $this->_xml, 42 ) );
			else 
				return PEAR::raiseError( "Bad login." );

			$xmlparser = xml_parser_create();
			
			if ( !xml_parse_into_struct( $xmlparser, $this->_xml, $this->_values, $this->_indexes ) ) 
			{
				xml_parser_free( $xmlparser );
				return PEAR::raiseError( "Unparsable XML." );
			}
			
			xml_parser_free( $xmlparser );
			return true;
		}
	}

	/**
	 * @access public
	 */	
	function getCurrentListenersCount() 
	{
		return ( $this->_values[$this->_indexes["CURRENTLISTENERS"][0]]["value"] );
	}

	/**
	 * @access public
	 */
	function getPeakListenersCount() 
	{
		return ( $this->_values[$this->_indexes["PEAKLISTENERS"][0]]["value"] );
	}

	/**
	 * @access public
	 */
	function getMaxListenersCount() 
	{
		return ( $this->_values[$this->_indexes["MAXLISTENERS"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getReportedListenersCount() 
	{
		return ( $this->_values[$this->_indexes["REPORTEDLISTENERS"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getAverageListenTime() 
	{
		return ( $this->_values[$this->_indexes["AVERAGETIME"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getServerGenre() 
	{
		return ( $this->_values[$this->_indexes["SERVERGENRE"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getServerURL() 
	{
		return ( $this->_values[$this->_indexes["SERVERURL"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getServerTitle() 
	{
		return ( $this->_values[$this->_indexes["SERVERTITLE"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getCurrentSongTitle() 
	{
		return ( $this->_values[$this->_indexes["SONGTITLE"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getIRC() 
	{
		return ( $this->_values[$this->_indexes["IRC"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getAIM() 
	{
		return ( $this->_values[$this->_indexes["AIM"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getICQ() 
	{
		return ( $this->_values[$this->_indexes["ICQ"][0]]["value"] );
	}

	/**
	 * @access public
	 */
	function getWebHitsCount() 
	{
		return ( $this->_values[$this->_indexes["WEBHITS"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getStreamHitsCount() 
	{
		return ( $this->_values[$this->_indexes["STREAMHITS"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getStreamStatus() 
	{
		return ( $this->_values[$this->_indexes["STREAMSTATUS"][0]]["value"] );
	}
	
	/**
	 * @access public
	 */
	function getBitRate() 
	{
		return ( $this->_values[$this->_indexes["BITRATE"][0]]["value"] );
	}

	/**
	 * @access public
	 */	
	function getSongHistory() 
	{
		for ( $i = 1; $i < sizeof( $this->_indexes['TITLE'] ); $i++ ) 
		{
			$arrhistory[$i-1] = array(
				"playedat" => $this->_values[$this->_indexes['PLAYEDAT'][$i]]['value'],
				"title"    => $this->_values[$this->_indexes['TITLE'][$i]]['value']
			);
		}

		return ( $arrhistory );
	}

	/**
	 * @access public
	 */
	function getListeners() 
	{
		for ( $i = 0; $i < sizeof( $this->_indexes['USERAGENT'] ); $i++ ) 
		{
			$arrlisteners[$i] = array(
				"hostname"    => $this->_values[$this->_indexes['HOSTNAME'][$i]]['value'],
				"useragent"   => $this->_values[$this->_indexes['USERAGENT'][$i]]['value'],
				"underruns"   => $this->_values[$this->_indexes['UNDERRUNS'][$i]]['value'],
				"connecttime" => $this->_values[$this->_indexes['CONNECTTIME'][$i]]['value'],
				"pointer"     => $this->_values[$this->_indexes['POINTER'][$i]]['value'],
				"uid"         => $this->_values[$this->_indexes['UID'][$i]]['value'],
			);
		}

		return ( $arrlisteners );
	}

	/**
	 * @access public
	 */	
	function convertSeconds( $seconds ) 
	{
		$tmpseconds = substr( "00" . $seconds % 60, -2 );
		
		if ( $seconds > 59 ) 
		{
			if ( $seconds > 3599 ) 
			{
				$tmphours   = substr( "0" . intval( $seconds / 3600 ), -2 );
				$tmpminutes = substr( "0" . intval( $seconds / 60 - ( 60 * $tmphours ) ), -2 );
			
				return ( $tmphours . ":" . $tmpminutes . ":" . $tmpseconds );
			} 
			else 
			{
				return ( "00:" . substr( "0" . intval( $seconds / 60 ), -2 ) . ":" . $tmpseconds );
			}
		} 
		else 
		{
			return ( "00:00:" . $tmpseconds );
		}
	}
} // END OF ShoutCast

?>
