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
|Authors: Stephan Schmidt <schst@php-tools.net>                        |
|         Gerd Schaufelberger <gerd@php-tools.de>                      |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'peer.Server' );


/**
 * XMLServer (needs domxml extension)
 * PHP socket xml server base class
 *	
 * Events that can be handled:
 * 	- onStart
 *	- onConnect
 *	- onConnectionRefused
 *	- onClose
 *	- onShutdown
 *	- onReceiveRequest
 *
 * Methods used to send responses:
 *	- sendResponse
 *	- broadcastResponse
 *
 * @package peer
 */

class XMLServer extends Server
{
	/**
  	 * Server received data - decodes the request.
 	 *
 	 * @access	private
 	 * @param	integer	$clientId	id of the client that sent the data
	 * @param	string	$xml		xml data
	 */
	function onReceiveData( $clientId, $xml )
	{
		// create dom tree
		$xmldoc = xmldoc( trim( $xml ) );
		
		// get root element (type of request)
		$root        = $xmldoc->root();
		$requestType = $root->node_name();
		
		// extract request parameters
		$requestParams = array();
		foreach ( $root->children() as $child )
		{
			if ( $child->node_type() != XML_ELEMENT_NODE )
				continue;
	
			$content = "";
			foreach ( $child->children() as $tmp )
			{
				if ( $tmp->node_type() != XML_TEXT_NODE && $tmp->node_type() != XML_CDATA_SECTION_NODE )
					continue;

				$content .=	$tmp->node_value();
			}
			
			$requestParams[$child->node_name()]	= $content;
		}

		if ( method_exists( $this, "onReceiveRequest" ) )
			$this->onReceiveRequest( $clientId, $requestType, $requestParams );
	}

	/**
	 * Send a response.
	 *
	 * @access	public
	 * @param	integer	$clientId	id of the client to that the response should be sent
	 * @param	string	$responseType	type of response
	 * @param	array	$responseParams	all params
	 * @return	boolean	$success
	 */
	function sendResponse( $clientId, $responseType, $responseParams )
	{
		$xml = $this->encodeResponse( $responseType, $responseParams );
		$this->sendData( $clientId, $xml );
	}

	/**
	 * Send response to all clients.
	 *
	 * @access	public
	 * @param	string	$data		data to send
	 * @param	array	$exclude	client ids to exclude
	 */
	function broadcastResponse( $responseType, $responseParams, $exclude = array() )
	{
		$xml = $this->encodeResponse( $responseType, $responseParams );
		$this->broadcastData( $xml, $exclude );
	}
	
	/**
	 * Encode a request.
	 *
	 * @access	public
	 * @param	string	$responseType	type of response
	 * @param	array	$responseParams	all params
	 * @return	string	$xml	encoded reponse
	 */
	function encodeResponse( $responseType, $responseParams )
	{
		if ( empty( $responseParams ) )
			return sprintf( "<%s/>\0", $responseType );

		$xml = sprintf( "<%s>", $responseType );
		foreach( $responseParams as $key => $value )
		{
			if ( $value == "" )
				$xml .=	sprintf( "<%s/>", $key );
			else
				$xml .= sprintf( "<%s>%s</%s>", $key, $value, $key );
		}
		
		$xml .=	sprintf( "</%s>\0", $responseType );
		return	$xml;
	}
} // END OF XMLServer

?>
