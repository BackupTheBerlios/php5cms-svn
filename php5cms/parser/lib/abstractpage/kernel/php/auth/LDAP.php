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
 * @package auth
 */
 
class LDAP extends Base
{ 
    /**
     * @access public
     */
    public $server = "";
    
    /**
     * @access public
     */
    public $port = "";
    
    /**
     * @access public
     */
    public $securebindname = "";
    
    /**
     * @access public
     */
    public $bindpw = "";
    
    /**
     * @access public
     */
    public $base_dn = ""; 

    /**
     * @access public
     */
    public $link; 
    
    /**
     * @access public
     */
    public $connected; 

    /**
     * @access public
     */
    public $filter;
    
    /**
     * @access public
     */
    public $attribs; 

    /**
     * used during enumeration
     * @access public
     */
    public $berident; 


    /**
     * Constructor
     *
     * @access public
     */
    public function __construct( $server = 'localhost', $port = 389 )
    {
        $this->server = $server;
        $this->port   = $port;
        
        $this->base_dn        = 'dc=ipass,dc=net';
        $this->securebindname = 'cn=,ou=,o=';
    }

    /**
     * Destructor
     *
     * @access public
     */
    public function __destruct()
    {
        $this->close();
    }
    
    
    /**
     * Connects to $this->server with an anonymous bind.
     *
     * @access public
     */
    public function connect()
    { 
        if ( !$this->connected )
        { 
            $this->link = ldap_connect( $this->server, $this->port ); 
        
            if ( !$this->link )
            {
                return false;
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
                    return false;
                } 
            } 
        } 

        return true;
    } 

    /**
     * Connects to $this->server and Binds as securebindname/bindpw.
     *
     * @return bool
     * @access public
     */
    public function sconnect( $bn = "", $bp = "" )
    { 
        $this->securebindname = ( !empty( $bn ) )? $bn : $this->securebindname; 
        $this->bindpw         = ( !empty( $bp ) )? $bp : $this->bindpw; 
        
        if ( !$this->connected )
        { 
            $this->link = ldap_connect( $this->server, $this->port ); 
        
            if (!$this->link )
            { 
                return false;
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
                    return false;
                }
            }
        }
        
        return true;
    } 

    /**
     * Closes the connection ($this->link) to the ldap server.
     *
     * @access public
     */
    public function close()
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
     * @return bool
     * @access public
     */
    public function add( $arr )
    { 
        if ( is_array( $arr ) && $this->connected )
        { 
            $dn = $arr["dn"]; 
        
            for ( reset( $arr ), next( $arr ); $key = key( $arr ); next( $arr ) )
                $arr2[$key] = $arr[$key]; 
         
            $r = @ldap_add( $this->link, $dn, $arr2 ); 
        
            if ( !$r )
                return false;
        
            return true;     
        } 

        return false;
    } 

    /**
     * Add takes an array as a param, the first element of the array should be
     * the Distinguished Name (DN) of the entry you are adding an attribute to.
     *
     * @return bool
     * @access public
     */
    public function addA( $arr )
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
                    return false;
            
                return true;     
            } 
        
            return false;
        } 

        return false;
    } 

    /**
     * Modify takes an array as a param, the first element of the array should
     * be the Distinguished Name (DN) of the entry you are modifying.
     *
     * @return bool
     * @access public
     */
    public function modify( $arr )
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
                    return false;
            
                return true;     
            } 
        
            return false;
        } 

        return false;
    } 

    /**
     * Deletes DN from directory.
     *
     * @return bool
     * @access public
     */
    public function delete( $dn )
    { 
        if ( !empty($dn) )
        { 
            if ( $this->connected )
            { 
                $r = @ldap_delete( $this->link, $dn ); 
            
                if ( !$r )
                    return false;
            
                return true;     
            } 
        
            return false;
        } 

        return false;
    } 

    /**
     * Deletes attribute from DN.
     *
     * @return bool
     * @access public
     */
    public function deleteA( $arr )
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
                    return false;
            
                return true;     
            } 
        
            return false;
        } 

        return false;
    } 

    /**
     * Search wraps Read, List and Search calls into one. It takes one argument .. one of
     * "BASE", "ONELEVEL" or "SUB". You are expected to make calls to BaseDN, Filter and Attrs  
     * everytime before you call search().
     *
     * @access public
     */
    public function filter( $filter )
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
    public function attrs( $attrs )
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
    public function baseDN( $basedn )
    { 
        if ( !empty( $basedn ) )
        { 
            $this->base_dn = $basedn; 
            return true; 
        } 

        return false; 
    }

    /**
     * @return bool
     * @access public
     */
    public function search( $scope = "SUB" )
    { 
        if ( !$this->connected )
            return false;
    
        if ( empty( $this->base_dn ) )
            return false;
     
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

        return false;
    } 

    /**
     * Count the number of entries returned from a search.
     *
     * @return bool
     * @access public
     */
    public function count( $res ) 
    { 
        if ( $res )
        { 
            if ( $this->connected )
                return ldap_count_entries( $this->link, $res ); 
        
            return false;
        } 

        return false;
    } 

    /**
     * Returns a result_entry_identifier for the first entry in a  
     * result_identifier passed in from a call to search().
     *
     * @return bool
     * @access public
     */
    public function first( $res )
    { 
        if ( $res )
        { 
            if ( $this->connected )
                return ldap_first_entry( $this->link, $res ); 
        
            return false;
        } 

        return false;
    } 
         
    /**
     * Returns a result_entry_identifier for the next entry in a result set.
     *
     * @return bool
     * @access public
     */
    public function next( $res )
    { 
        if ( $res )
        { 
            if ( $this->connected )
                return ldap_next_entry( $this->link, $res ); 
        
            return false;
        } 

        return false;
    } 

    /**
     * Returns an array of the first attribute in an entry.
     *
     * @return bool
     * @access public
     */
    public function firstAttr( $res )
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
        
            return false;
        } 

        return false;
    } 

    /**
     * Returns a result_entry_identifier for the first entry in a  
     * result_identifier passed in from a call to search().
     *
     * @return bool
     * @access public
     */
    public function nextAttr( $res )
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
        
            return false;
        } 

        return false;
    } 

    /**
     * Returns a multi-dimensional array of all the entries and 
     * attributes in a search result. If sortattr is not empty 
     * it will sort the entries based on that attribute. Default 
     * is not to sort.
     *
     * @access public
     */
    public function all( $res, $sortattr = "" )
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
        
            return false;
        } 

        return false;
    } 

    /**
     * Returns a multi-dimensional array of all the attributes  
     * of an entry in a search result.
     *
     * @access public
     */
    public function allAttrs( $res )
    { 
        if ( $res )
        { 
            if ( $this->connected )
                return ldap_get_attributes( $this->link, $res ); 
        
            return false;
        } 

        return false;
    } 

    /**
     * Release the memory associated with a result_identifier.
     *
     * @access public
     */
    public function free( $res )
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
    public function getDN( $res )
    { 
        if ( $res )
        { 
            if ( $this->connected )
                return ldap_get_dn( $this->link, $res ); 
        
            return false;
        } 

        return false;
    } 

    /**
     * Explodes a DN into an array. With attributes determines 
     * if the array components are return with in full context mode: 
     * ie: array[0]="cn=user",array[1]="ou=orgunit" otherwise: 
     * array[0]="user",array[1]="orgunit"... 
     *
     * @access public
     */
    public function explodeDN( $dn, $wa = "1" )
    { 
        if ( !empty( $dn ) )
        { 
            if ( $this->connected )
                return ldap_explode_dn( $dn, $wa ); 
        
            return false;
        } 

        return false;
    } 

    /**
     * Return a DN in a user friendly way (strip type names).
     *
     * @access public
     */
    public function friendly( $dn )
    { 
        if ( !empty($dn) )
        { 
            if ( $this->connected )
                return ldap_dn2ufn( $dn ); 
        
            return false;
        } 

        return false;
    }
} // END OF LDAP

?>
