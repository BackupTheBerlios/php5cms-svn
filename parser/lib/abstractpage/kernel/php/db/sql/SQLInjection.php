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
 * This class is meant to search in your SQL data values for special characters that may 
 * change the meaning of your SQL data and execute actions that may compromise the security of servers.
 *
 * When some of these suspicious character sequences is found in HTTP POST request values ($_POST), you can:
 * - Destroy the current session
 * - Redirect to a new page
 * - Log the activity
 *
 * To check the request values do the following:
 *
 * $bDestroy_session = true;
 * $url_redirect = 'index.php';
 * $sqlinject = new SQLInjection( $bDestroy_session, $url_redirect );
 * $sqlinject->test( $your_sql_data );
 *
 * Notice: this class recognise only some of the known types of SQL injection methods and so 
 * it is not yet ready to deal with all possible ways to perform this kind of attack.
 *
 * @package db_sql
 */

class SQLInjection extends PEAR
{
    /**
	 * url to redirect if an sql inject attempt is detect. if unset, value is false
	 * @access private
	 * @var    mixed
	 */
    var $urlRedirect;
    
	/**
	 * does the session must be destroy if an attempt is detect
	 * @access private
	 * @var    bool
	 */
    var $bdestroy_session;
    
	/**
	 * the SQL data currently test
	 * @access private
	 * @var    string
	 */
    var $rq;
    
	
    /**
	 * Constructor
	 *
	 * @param bool bdestroy_session optional. does the session must be destroy if an attempt is detect?
	 * @param string urlRedirect optional. url to redirect if an sql inject attempt is detect
     * @public
	 * @var  void
     */
    function SQLInjection( $bdestroy_session = false, $urlRedirect = false )
    {
        $this->urlRedirect = ( ( ( trim( $urlRedirect ) != '' ) && file_exists( $urlRedirect ) )? $urlRedirect : '' );
        $this->bdestroy_session = $bdestroy_session;
        $this->rq = '';
    }

    /**
	 * Test if there is a sql inject attempt detect.
	 *
	 * @param string sRQ required. SQL Data to test
     * @public
	 * @var  bool
     */
    function test( $sRQ )
    {
        $sRQ         = strtolower( $sRQ );
        $this->rq    = $sRQ;
        $aValues     = array();
        $aTemp       = array(); // temp array
        $aWords      = array(); //
        $aSep        = array( ' and ',' or ' ); // separators for detect the
        $sConditions = '(';
        $matches     = array();
        $sSep        = '';

        // is there an attempt to unused part of the rq?
        if ( is_int( ( strpos( $sRQ, "#" ) ) ) && $this->_in_post( '#' ) ) 
			return $this->detect();
        
        // is there a attempt to do a 2nd SQL requete ?
        if ( is_int( strpos( $sRQ, ';' ) ) )
		{
            $aTemp = explode( ';', $sRQ );
			
            if ( $this->_in_post( $aTemp[1] ) ) 
				return $this->detect();
        }
        
        $aTemp = explode( " where ", $sRQ );
		
        if ( count( $aTemp ) == 1 ) 
			return false;
        
		$sConditions = $aTemp[1];
        $aWords = explode( " ", $sConditions );
		
        if ( strcasecmp( $aWords[0], 'select' ) !=0 ) 
			$aSep[] = ',';
        
		$sSep = '(' . implode( '|', $aSep ) . ')';
        $aValues = preg_split( $sSep,$sConditions, -1, PREG_SPLIT_NO_EMPTY );

        // test the always true expressions
        foreach ( $aValues as $i => $v )
        {
            // SQL injection like 1=1 or a=a or 'za'='za'
            if ( is_int( strpos( $v, '=' ) ) )
            {
                 $aTemp = explode( '=', $v );
				 
                 if ( trim( $aTemp[0] ) == trim( $aTemp[1] ) ) 
				 	return $this->detect();
            }
            
            // SQL injection like 1<>2
            if ( is_int( strpos( $v, '<>' ) ) )
            {
                $aTemp = explode( '<>', $v );
                
				if ( ( trim( $aTemp[0] ) != trim( $aTemp[1] ) )&& ( $this->_in_post( '<>' ) ) ) 
					return $this->detect();
            }
        }
        
        if ( strpos( $sConditions, ' null' ) )
        {
            if ( preg_match( "/null +is +null/", $sConditions ) ) 
				return $this->detect();
            
			if ( preg_match( "/is +not +null/", $sConditions, $matches ) )
            {
                foreach ( $matches as $i => $v )
                {
                    if ( $this->_in_post( $v ) )
						return $this->detect();
                }
            }
        }
        
        if ( preg_match( "/[a-z0-9]+ +between +[a-z0-9]+ +and +[a-z0-9]+/", $sConditions,$matches ) )
        {
            $Temp     = explode( ' between ', $matches[0] );
            $Evaluate = $Temp[0];
            $Temp     = explode( ' and ', $Temp[1] );
            
			if ( ( strcasecmp( $Evaluate, $Temp[0] ) > 0 ) && ( strcasecmp( $Evaluate, $Temp[1] ) < 0 ) && $this->_in_post( $matches[0] ) ) 
				return $this->detect();
        }
		
        return false;
    }

    function detect()
    {
        // destroy session?
        if ( $this->bdestroy_session ) 
			session_destroy();
        
		// redirect?
        if ( $this->urlRedirect != '' )
		{
             if ( !headers_sent() )
			 	header( "location: $this->urlRedirect" );
        }
		
        return true;
    }
	
	
	// private methods
	
    function _in_post( $value )
    {
        foreach ( $_POST as $i => $v )
        {
             if ( is_int( strpos( strtolower( $v ), $value ) ) ) 
			 	return true;
        }
		
        return false;
    }
} // END OF SQLInjection

?>
