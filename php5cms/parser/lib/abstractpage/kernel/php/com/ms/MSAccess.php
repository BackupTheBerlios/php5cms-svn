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


using( 'com.microsoft.COMObject' );


class MSAccess extends Base
{
    /**
     * @access private
     */
    private $_RS = 0;
    
    /**
     * @access private
     */
    private $_ADODB = 0;

    /**
     * @private string
     * @access private
     */  
    private $_strProvider = 'Provider=Microsoft.Jet.OLEDB.4.0';
    
    /**
     * @private string
     * @access private
     */
    private $_strMode = 'Mode=ReadWrite';
    
    /**
     * @private string
     * @access private
     */
    private $_strPSI = 'Persist Security Info=False';
    
    /**
     * @private string
     * @access private
     */
    private $_strDataSource = '';
    
    /**
     * @private string
     * @access private
     */
    private $_strConn = '';
    
    /**
     * @private string
     * @access private
     */
    private $_strRealPath = '';
  
    
    /**
     * Constructor
     *
     * @access public
     * @throws Exception
     */
    public function __construct( $dsn = '' )
    {
        $this->_strRealPath = realpath( $dsn );
    
        if ( strlen( $this->_strRealPath ) > 0 )
            $this->_strDataSource = 'Data Source='.$this->_strRealPath;
        else
            throw Exception( "File not found" );
    }

    /**
     * Destructor
     */
    function __destruct()
    {
        if ( $this->_RS || $this->_ADODB )
            $this->close();
    }
    
    
    /**
     * @return bool   success
     * @access public
     */
    public function open( )
    {
        if ( strlen( $this->_strRealPath ) > 0 )
        {
            $this->_strConn = $this->_strProvider . ';' 
                . $this->_strDataSource . ';' 
                . $this->_strMode . ';' 
                . $this->_strPSI;
        
            $this->_ADODB = &new COMObject( 'ADODB.Connection' );
            
            // $this->_ADODB = new COM( 'ADODB.Connection' );
      
            if( $this->_ADODB )
            {
                $this->_ADODB->open( $this->_strConn );
                return true;
            }
            else
            {
                return false;
            }
        }
    }
  
    /**
     * @access public
     */
    public function execute( $strSQL )
    {
        $this->_RS = $this->_ADODB->execute( $strSQL );
    }

    /**
     * @access public
     */  
    public function eof()
    {
        return $this->_RS->EOF;
    }
  
    /**
     * @access public
     */
    public function movenext( )
    {
        $this->_RS->MoveNext();
    }

    /**
     * @access public
     */  
    public function movefirst()
    {
        $this->_RS->MoveFirst();
    }

    /**
     * @access public
     */  
    public function close()
    {
        $this->_RS->Close();
        $this->_RS = null;
  
        $this->_ADODB->Close();
        $this->_ADODB = null;
    }

    /**
     * @access public
     */  
    public function fieldvalue( $fieldname )
    {
        return $this->_RS->Fields[$fieldname]->value;
    }

    /**
     * @access public
     */  
    public function fieldname( $fieldnumber )
    {
        return $this->_RS->Fields[$fieldnumber]->name;
    }
} // END OF MSAccess

?>
