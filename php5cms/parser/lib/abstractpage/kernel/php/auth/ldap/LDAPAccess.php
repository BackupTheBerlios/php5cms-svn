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
 * Provides an OOP interface to an LDAP server.
 *
 * @package auth_ldap
 */
 
class LDAPAccess extends PEAR
{ 
	/**
	 * @access public
	 */
	var $server = "";
	
	/**
	 * @access public
	 */
	var $port = "";
	
	/**
	 * @access public
	 */
	var $securebindname = "";
	
	/**
	 * @access public
	 */
	var $bindpw = "";
	
	/**
	 * @access public
	 */
	var $base_dn = ""; 

	/**
	 * @access public
	 */
	var $link; 
	
	/**
	 * @access public
	 */
	var $connected; 

	/**
	 * @access public
	 */
	var $filter;
	
	/**
	 * @access public
	 */
	var $attribs; 

	/**
	 * used during enumeration
	 * @access public
	 */
	var $berident; 


	/**
	 * Constructor. 
	 *
	 * @access public
	 */
	function LDAPAccess( $server = 'localhost', $port = 389 )
	{
    	$this->server  		  = $server;
		$this->port    		  = $port;
		
		$this->base_dn 		  = 'dc=ipass,dc=net';
		$this->securebindname = 'cn=,ou=,o=';
	}


	/**
	 * Connects to $this->server with an anonymous bind.
	 *
	 * @access public
	 */
	function Connect()
	{ 
    	if ( !$this->connected )
		{ 
        	$this->link = ldap_connect( $this->server, $this->port ); 
        
			if ( !$this->link )
			{
				return PEAR::raiseError( "Could not connect to LDAP Server " . $this->server );
        	}
			else
			{ 
            	if ( ldap_bind( $this->link, '', '' ) )
				{
                	$this->connected = 1;
                	return true;
            	}
				else
				{
					return PEAR::raiseError( "Could not bind to " . $this->server );
            	} 
        	} 
    	} 

		return true; // already connected 
	} 

	/**
	 * Connects to $this->server and Binds as securebindname/bindpw.
	 *
	 * @access public
	 */
	function SConnect( $bn = "", $bp = "" )
	{ 
    	$this->securebindname = ( !empty( $bn ) )? $bn : $this->securebindname; 
    	$this->bindpw         = ( !empty( $bp ) )? $bp : $this->bindpw; 
    	
		if ( !$this->connected )
		{ 
        	$this->link = ldap_connect( $this->server, $this->port ); 
        
			if (!$this->link )
			{ 
            	return PEAR::raiseError( "Could not connect to LDAP Server" . $this->server );
        	}
			else
			{ 
            	if ( @ldap_bind( $this->link, $this->securebindname, $this->bindpw ) )
				{ 
                	$this->connected = 1; 
                	return true; 
            	}
				else
				{
					return PEAR::raiseError( "Could not bind to " . $this->server . " [" . $this->securebindname . "]" );
            	}     
        	} 
    	} 

		// already connected 
		return true;
	} 

	/**
	 * Closes the connection ($this->link) to the ldap server.
	 *
	 * @access public
	 */
	function Close()
	{ 
    	if ( $this->link )
		{ 
        	ldap_unbind( $this->link ); 
        	$this->link      = 0; 
        	$this->connected = 0; 
        
			return true; 
    	} 

		return true; 
	} 

	/**
	 * Add takes an array as a param, the first element of the  array
	 * should be the Distinguished Name (DN) of the entry you are adding.
	 *
	 * @access public
	 */
	function Add( $arr )
	{ 
    	if ( is_array( $arr ) && $this->connected )
		{ 
        	$dn = $arr["dn"]; 
        
			for ( reset( $arr ), next( $arr ); $key = key( $arr ); next( $arr ) )
				$arr2[$key] = $arr[$key]; 
         
        	$r = @ldap_add( $this->link, $dn, $arr2 ); 
        
			if ( !$r )
				return PEAR::raiseError( "LDAP_ADD failed." );
        
			return true;     
    	} 

		return PEAR::raiseError( "Argument passed in was not an array." );
	} 

	/**
	 * Add takes an array as a param, the first element of the array should be
	 * the Distinguished Name (DN) of the entry you are adding an attribute to.
	 *
	 * @access public
	 */
	function AddA( $arr )
	{ 
    	if ( is_array( $arr ) )
		{ 
        	if ( $this->connected )
			{ 
            	$dn = $arr["dn"]; 
            
				for ( reset( $arr ), next( $arr ); $key = key( $arr ); next( $arr ) )
					$arr2[$key] = $arr[$key]; 
             
            	$r = @ldap_mod_add( $this->link, $dn, $arr2 ); 
            
				if ( !$r )
					return PEAR::raiseError( "LDAP_MOD_ADD failed." );
            
				return true;     
        	} 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "Argument passed in was not an array." );
	} 

	/**
	 * Modify takes an array as a param, the first element of the array should
	 * be the Distinguished Name (DN) of the entry you are modifying.
	 *
	 * @access public
	 */
	function Modify( $arr )
	{ 
    	if ( is_array( $arr ) )
		{ 
        	if ( $this->connected )
			{ 
            	$dn = $arr["dn"]; 
            
				for ( reset( $arr ), next( $arr ); $key = key( $arr ); next( $arr ) )
					$arr2[$key] = $arr[$key]; 
            
				$r = @ldap_modify( $this->link, $dn, $arr2 ); 
            
				if ( !$r )
					return PEAR::raiseError( "LDAP_MODIFY failed." );
            
				return true;     
        	} 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "Argument passed in was not an array." );
	} 

	/**
	 * Deletes DN from directory.
	 *
	 * @access public
	 */
	function Delete( $dn )
	{ 
    	if ( !empty($dn) )
		{ 
        	if ( $this->connected )
			{ 
            	$r = @ldap_delete( $this->link, $dn ); 
            
				if ( !$r )
					return PEAR::raiseError( "LDAP_DELETE failed." );
            
				return true;     
        	} 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "Bad argument passed in." );
	} 

	/**
	 * Deletes attribute from DN.
	 *
	 * @access public
	 */
	function DeleteA( $arr )
	{ 
    	if ( is_array( $arr ) )
		{ 
        	if ( $this->connected )
			{ 
            	$dn = $arr["dn"]; 
            
				for ( reset($arr), next( $arr ); $key = key( $arr ); next( $arr ) )
					$arr2[$key] = $arr[$key]; 
            
				$r = @ldap_mod_del( $this->link, $dn, $arr2 ); 
            
				if ( !$r )
					return PEAR::raiseError( "LDAP_MOD_DEL failed." );
            
				return true;     
        	} 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "Argument passed in was not an array." );
	} 

	/**
	 * Search wraps Read, List and Search calls into one. It takes one argument .. one of
	 * "BASE", "ONELEVEL" or "SUB". You are expected to make calls to BaseDN, Filter and Attrs  
	 * everytime before you call Search().
	 *
	 * @access public
	 */
	function Filter( $filter )
	{ 
    	if ( !empty ( $filter ) )
		{ 
        	$this->filter = $filter; 
        	return true; 
    	}
		else
		{ 
        	$this->filter = "cn=*"; 
    	} 

		return false; 
	} 

	/**
	 * @access public
	 */
	function Attrs( $attrs )
	{ 
    	if ( !empty ( $attrs ) )
			$this->attribs = explode( ",", $attrs ); 
		else
			$this->attribs = array(); 

		return false; 
	} 

	/**
	 * @access public
	 */
	function BaseDN( $basedn )
	{ 
    	if ( !empty( $basedn ) )
		{ 
        	$this->base_dn = $basedn; 
        	return true; 
    	} 

		return false; 
	}

	/**
	 * @access public
	 */
	function Search( $scope = "SUB" )
	{ 
    	if ( !$this->connected )
			return PEAR::raiseError( "Not connected to LDAP server." );
    
		if ( empty( $this->base_dn ) )
			return PEAR::raiseError( "No BaseDN provided." );
     
    	if ( empty( $this->filter ) )
			$this->filter = "cn=*"; 
    	
		if ( !is_array( $this->attribs ) )
			$this->attribs = array(); 
    
		switch ( $scope )
		{ 
        	case "BASE" :
				return @ldap_read( $this->link, $this->base_dn, $this->filter, $this->attribs ); 
                break; 
        
			case "ONELEVEL" :
				return @ldap_list( $this->link, $this->base_dn, $this->filter, $this->attribs ); 
                break; 
        
			case "SUB" :
				return @ldap_search( $this->link, $this->base_dn, $this->filter, $this->attribs ); 
                break; 
 		} 

		return PEAR::raiseError( "LDAP_SEARCH failed." );
	} 

	/**
	 * Count the number of entries returned from a search.
	 *
	 * @access public
	 */
	function Count( $res ) 
	{ 
    	if ( $res )
		{ 
        	if ( $this->connected )
				return ldap_count_entries( $this->link, $res ); 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 

	/**
	 * Returns a result_entry_identifier for the first entry in a  
	 * result_identifier passed in from a call to Search().
	 *
	 * @access public
	 */
	function First( $res )
	{ 
    	if ( $res )
		{ 
        	if ( $this->connected )
				return ldap_first_entry( $this->link, $res ); 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 
         
	/**
	 * Returns a result_entry_identifier for the next entry in a result set.
	 *
	 * @access public
	 */
	function Next( $res )
	{ 
    	if ( $res )
		{ 
        	if ( $this->connected )
				return ldap_next_entry( $this->link, $res ); 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 

	/**
	 * Returns an array of the first attribute in an entry.
	 *
	 * @access public
	 */
	function FirstAttr( $res )
	{ 
    	$this->berident = 0; 
    
		if ( $res )
		{ 
        	if ( $this->connected )
			{ 
            	$fattr = ldap_first_attribute( $this->link, $res, &$this->berident ); 
            
				if ( !empty($fattr ) )
				{ 
                	$tmparr    = ldap_get_values( $this->link, $res, $fattr ); 
                	$tmparr2[] = $fattr; 
                
					for ( $i = 0; $i < count( $tmparr ); $i++ )  
                    	$tmparr2[] = $tmparr[$i]; 
                
					return $tmparr2; 
            	} 
        	} 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 

	/**
	 * Returns a result_entry_identifier for the first entry in a  
	 * result_identifier passed in from a call to Search().
	 *
	 * @access public
	 */
	function NextAttr( $res )
	{ 
    	if ( $res )
		{ 
        	if ( $this->connected )
			{ 
            	$nattr = ldap_next_attribute( $this->link, $res, &$this->berident ); 
            
				if ( !empty($nattr) )
				{ 
                	$tmparr    = ldap_get_values( $this->link, $res, $nattr ); 
                	$tmparr2[] = $nattr; 
                
					for ( $i = 0; $i < count( $tmparr ); $i++ )  
                    	$tmparr2[] = $tmparr[$i]; 
                
					return $tmparr2; 
            	} 
        	} 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 

	/**
	 * Returns a multi-dimensional array of all the entries and 
	 * attributes in a search result. If sortattr is not empty 
	 * it will sort the entries based on that attribute. Default 
	 * is not to sort.
	 *
	 * @access public
	 */
	function All( $res, $sortattr = "" )
	{     
    	if ( $res )
		{ 
        	if ( $this->connected )
			{ 
            	if ( empty( $sortattr ) )
				{ 
                	return ldap_get_entries( $this->link, $res ); 
            	}
				else
				{ 
                	$entries = ldap_get_entries( $this->link, $res ); 
                
					for ( $i = 0; $i < count( $entries ); $i++ )
						$temparr[$entries[$i][$sortattr][0].$i] = $entries[$i]; 
                
					ksort( $temparr ); 
                
					for ( reset( $temparr ); $key = key( $temparr ); next( $temparr ) )
						$entries1[] = $temparr[$key]; 
            
					return $entries1; 
            	} 
        	} 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 

	/**
	 * Returns a multi-dimensional array of all the attributes  
	 * of an entry in a search result.
	 *
	 * @access public
	 */
	function AllAttrs( $res )
	{ 
    	if ( $res )
		{ 
        	if ( $this->connected )
				return ldap_get_attributes( $this->link, $res ); 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 

	/**
	 * Release the memory associated with a result_identifier.
	 *
	 * @access public
	 */
	function Free( $res )
	{ 
    	if ( $res && $this->connected )
			ldap_free_result( $res ); 

		return true; 
	} 

	/**
	 * Get the DN of the result entry.
	 *
	 * @access public
	 */
	function GetDN( $res )
	{ 
    	if ( $res )
		{ 
        	if ( $this->connected )
				return ldap_get_dn( $this->link, $res ); 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No result identifier." );
	} 

	/**
	 * Explodes a DN into an array. With attributes determines 
	 * if the array components are return with in full context mode: 
	 * ie: array[0]="cn=user",array[1]="ou=orgunit" otherwise: 
	 * array[0]="user",array[1]="orgunit"... 
	 *
	 * @access public
	 */
	function ExplodeDN( $dn, $wa = "1" )
	{ 
    	if ( !empty( $dn ) )
		{ 
        	if ( $this->connected )
				return ldap_explode_dn( $dn, $wa ); 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No DN passed in." );
	} 

	/**
	 * Return a DN in a user friendly way (strip type names).
	 *
	 * @access public
	 */
	function Friendly( $dn )
	{ 
    	if ( !empty($dn) )
		{ 
        	if ( $this->connected )
				return ldap_dn2ufn( $dn ); 
        
			return PEAR::raiseError( "Not connected to LDAP server." );
    	} 

		return PEAR::raiseError( "No DN passed in." );
	}
} // END OF LDAPAccess

?>
