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


using( 'peer.xmlrpc.simple.SimpleXMLRPC' );


/**
 * Class for Serverside XML RPC Processing.
 *
 * This class offers methods for Serverside Remote Procedure Call Handling
 *
 * @package peer_xmlrpc_simple
 */

class SimpleXMLRPCServer extends SimpleXMLRPC
{
    /**
     * The Package (Class) containing the called Procedure.
     *
     * @access private
     * @var    string
     */
	var $_rpc_package;

    /**
     * The Package (Class) containing the User defined Procedures.
     *
     * @access private
     * @var    string
     */
	var $_user_package;	

    /**
     * The RP Call.
     *
     * @access private
     * @var    string
     */
	var $_xmlrpc_method;
    
	/**
     * If _halt_operation is true, User methods are able to send back user defined XMLRPC Responds
     *
     * @access private
     * @var    string
     */
	var $_halt_operation = false;
	
	/**
	 * $_post_data contains the HTTP_RAW_POST_DATA
	 *
	 * @access	private
	 * @var		string
	 */
	var $_post_data;
	
	/**
	 * $raw_params contains the whole <params>...</params> Procedure Call Params
	 *
	 * @access	public
	 * @var		string
	 */
	var $raw_params;


    /**
     * Constructor
	 *
     * @access   public
	 * @return	 mixed	true or exit
	 * @see		 $_no_call_error
     */    
	function SimpleXMLRPCServer( $debug_call = false )
	{
		global $HTTP_RAW_POST_DATA, $t;
		
		list( $low, $high ) = split( " ", microtime() );
		$t = $high + $low;
		
		if ( !$HTTP_RAW_POST_DATA && !$debug_call )
		{ 
			$this = new PEAR_Error( "No Procedure Call." );
			return;
		}
		else
		{
			$this->_post_data = $HTTP_RAW_POST_DATA;
			
			if ( $debug_call )
				$this->debug_call = $debug_call;		
		}
	}
	
	
    /**
     * Sets the Name of the User Package.
	 *
     * @access   public
	 * @param	 string	$package Name of Package (class) with User defined Methods (Procedures)
	 * @return	 boolean true if everything is alright, false otherwise
	 * @see	     $raw_params, $_post_data, $_user_package, parseMethodCall(), parseXMLRPC()
     */	
	function setUserPackage( $package )
	{
		return $this->_user_package = $package;
	}

    /**
     * This method returns the Server Response to the given RPC.
	 *
     * @access    public
     * @param     string $return_value 
	 * @return	  mixed XML Return Values
	 * @see		  _execRPC(), _output()
     */	
	function ready()
	{
		// $raw_params contains the XML <params>...</params> Part
		$get_raw_params   = $this->_post_data;
		$get_raw_params   = split( '<params>', $get_raw_params );
		$this->raw_params = "<params>" . str_replace( "</methodCall>", "", $get_raw_params[1] );
			
		if ( $this->debug_call )
			$this->parseXMLRPC( $this->debug_call );
		else
			$this->parseXMLRPC( $this->_post_data );
			
		$this->parseMethodCall();		
		$return_value = $this->_execRPC();
			
		if ( $this->_halt_operation != true )
			return 	$this->_output( $return_value );
		else
			return false;
	}
	
	
	// private methods
	
    /**
     * The Remote Procedure Call.
	 *
     * @access   private
	 * @return 	 mixed
	 * @see		 _handleUserFunc(), _errorResponse()
     */
	function _execRPC( )
	{
		if ( class_exists( $this->_rpc_package ) )
		{
			// class (package) exists
			$obj = new $this->_rpc_package;
			
			// We have to check if the method $this->rpc_method[0] exists
			if( !method_exists( $obj, $this->_xmlrpc_method ) )
				return $this->_errorResponse( "Method does not exist on Server" );
			else
				return $this->_handleUserFunc( call_user_method( $this->_xmlrpc_method, $obj, &$this ) );
		}
		else
		{
			// The Package specified in $this->_rpc_package[0] does not exist.
			return $this->_errorResponse( "Package " . $this->_rpc_package . " does not exist on this Server" );
		}
	}	
	
    /**
     * This Method handles the Userdefined functions.
	 *
	 * The Method receives the Output of an User defined function (array or string).
	 *
     * @access   private
	 * @param	 mixed	$return_of_userf	Array or String containing the Output of the Userfunction
	 * @return	 mixed
	 * @see		 _execRPC()
     */	
	function _handleUserFunc( $return_of_userf )
	{
		$rpc_ret  = "<methodResponse>";
		$rpc_ret .= "   <params>";
		$rpc_ret .= "       <param>";
		$rpc_ret .= $return_of_userf;
		$rpc_ret .= "       </param>";		
		$rpc_ret .= "   </params>";
		$rpc_ret .= "</methodResponse>";
		
		return $rpc_ret;
	}
	
    /**
     * This method sends the XML Return Values back to Client.
	 *
	 * The method adds appropriate Headers for XML Content Type
	 *
     * @access   private
     * @param    string $data The complete Outputdata to be send back to Client
	 * @see		 ready()
     */		
	function _output( $data )
	{
		global $t;

	    list( $low, $high ) = split( " ", microtime() );
    	
		$s    = $high + $low;
    	$used = $s - $t;
    
		$data_pre  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n\n";
		// $data_pre .= "<!-- method called: " . $this->_rpc_package . "." . $this->_xmlrpc_method . " -->\n";	        
		// $data_pre .= "<!-- response time: " . sprintf( "%3.4f", $used ) . " -->\n\n";
	    
		ob_start();
		
		// Our Data
		echo $data_pre . $data;
		
		// Data End
	    $buffer = ob_get_contents();
		header( "X-XMLRPC: Abstractpage XMLRPC/1.0" );
		header( "Status: 200" );
		header( "Content-type: text/xml; charset=ISO-8859-1" );
		header( "Content-length: " . strlen( $buffer ) . "\n" );		
	    
		ob_end_flush();
	}
} // END OF SimpleXMLRPCServer

?>
