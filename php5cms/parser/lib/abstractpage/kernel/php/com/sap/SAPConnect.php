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
 * Uses the SAPRFC extension which can be found here:
 * http://saprfc.sourceforge.net
 *
 *
 * Content from file 'ZPHP_TEST.abap':
 *
 * FUNCTION zphp_test.
 * *"----------------------------------------------------------------------
 * *"*"Local interface:
 * *"  IMPORTING
 * *"     VALUE(TEXT1) TYPE  CHAR100
 * *"     VALUE(TEXT2) TYPE  CHAR100
 * *"  EXPORTING
 * *"     VALUE(RESULT) TYPE  CHAR100
 * *"----------------------------------------------------------------------
 *  
 *   CONCATENATE TEXT1 TEXT2 '!' INTO RESULT SEPARATED BY space.
 *  
 * ENDFUNCTION.
 *
 *
 * Example:
 *
 * class ZPHP_Test extends SAPConnect
 * {
 * 		function ZPHP_Test( $tekst1 = "", $tekst2 = "" )
 *   	{
 *       	$this->SAPConnect( -->connection data here<-- );
 *       
 * 	  		if ( !$this->Rfc ) 
 * 				return;
 *       
 * 	  		$this->openFunction( "zphp_test" );
 *       	$this->addImport( "tekst1", $tekst1 );
 *      	$this->addImport( "tekst2", $tekst2 );
 *       	$this->run();
 *       	$this->close();
 *   	}
 * 
 *   
 *   	function returnValue()
 *   	{
 *     		return $this->GetValue( "resultaat" );
 *   	}
 * } // END OF ZPHP_Test
 * 
 * 
 * if ( $action == "run" )
 * {
 *    	$TEST = new ZPHP_Test( $tekst1, $tekst2 );
 *    	echo "<h1>" . $TEST->returnValue() . "</h1>";
 * }
 * 
 * ...
 * 
 * <form name="TEST" action="example.php" method="POST">
 * <table border="1" cellpadding="7">
 * <tr><td>Esimene tekst:</td><td><input type="TEXT" name="tekst1" size="20" value="<? echo $tekst1;?>"></td></tr>
 * <tr><td>Teine tekst:</td><td><input type="TEXT" name="tekst2" size="20" value="<? echo $tekst2;?>"></td></tr>
 * <input type="hidden" name="action" value="run">
 * <tr><td colspan="2" align="CENTER"><input type="SUBMIT" value="Test"></td></tr>
 * </table>
 * </form>
 *
 * @package com_sap
 */

class SAPConnect extends PEAR
{
	/**
	 * @access public
	 */
  	var $Rfc;
	
	/**
	 * @access public
	 */
  	var $Login;
	
	/**
	 * @access public
	 */
  	var $CallFuncName;
	
	/**
	 * @access public
	 */
  	var $FCall;
	
	/**
	 * @access public
	 */
  	var $ImportCount;
	
	/**
	 * @access public
	 */
  	var $Import;
	
	/**
	 * @access public
	 */
  	var $RetVal;
	
	/**
	 * @access public
	 */
  	var $InterFace;
	
	/**
	 * @access public
	 */
  	var $ExportCount;
	
	/**
	 * @access public
	 */
  	var $Export;
	
	/**
	 * @access public
	 */
  	var $TabCount;
	
	/**
	 * @access public
	 */
  	var $TabNames;
	
	/**
	 * @access public
	 */
  	var $TabData;
	
	/**
	 * @access public
	 */
  	var $TabRows;
	
	/**
	 * @access public
	 */
  	var $TabCols;
	
	/**
	 * @access public
	 */
  	var $ConnectInfo;
  
  	
	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function SAPConnect( $ASHOST    = "mysaphost", 
						 $SYSNR     = "00",
						 $CLIENT    = "100",
						 $USER      = "myname",
						 $PASSWD    = "mypass",
						 $GWHOST    = "",
						 $GWSERV    = "",
						 $MSHOST    = "",
						 $R3NAME    = "C11",
						 $GROUP     = "",
						 $LANG      = "EN",
						 $TRACE     = "",
						 $LCHECK    = "",
						 $CODEPAGE  = "1100" )
 	{
    	$this->Login["ASHOST"]		= $ASHOST;
    	$this->Login["SYSNR"]		= $SYSNR;
    	$this->Login["CLIENT"]		= $CLIENT;
    	$this->Login["USER"]		= $USER;
    	$this->Login["PASSWD"]		= $PASSWD;
    	$this->Login["GWHOST"]		= $GWHOST;
    	$this->Login["GWSERV"]		= $GWSERV;
    	$this->Login["MSHOST"]		= $MSHOST;
    	$this->Login["R3NAME"]		= $R3NAME;
    	$this->Login["GROUP"]		= $GROUP;
    	$this->Login["LANGUAGE"]	= $LANG;
    	$this->Login["TRACE"]		= $TRACE;
    	$this->Login["LCHECK"]		= $LCHECK;
    	$this->Login["CODEPAGE"]	= $CODEPAGE;

    	$this->Rfc = @saprfc_open( $this->Login );

		// if login failed, show error message
    	if ( !$this->Rfc )
    	{
     		if ( $this->Login["use_load_balancing"] )
			{
				$this = new PEAR_Error( "Login error: Can't login to client " . $this->Login[CLIENT] . " of the system " . $this->Login[R3NAME] . " (Host: " . $this->Login[MSHOST] . ", Group: " . $this->Login[GROUP] . ") as user " . $this->Login[USER] . " - " . saprfc_error() );
				return;
			}
          	else
			{
				$this = new PEAR_Error( "Login error: Can't login to client " . $this->Login["CLIENT"] . " and host " . $this->Login["ASHOST"] . " (System number: " . $this->Login["SYSNR"] . ") as user " . $this->Login["USER"] . " - " . saprfc_error() );
				return;
			}
       
	   		return;
   		}
   
   		$this->ConnectInfo = saprfc_attributes( $this->Rfc );
   	}

	
	/**
	 * @access public
	 */
  	function openFunction( $funcname, $check = 0 )
  	{
    	$this->CallFuncName = strtoupper( $funcname );
  	}

	/**
	 * @access public
	 */
  	function addImport( $parameter, $value )
  	{
      	$this->ImportCount++;
      	$this->Import[$this->ImportCount]["PARAMETER"] = strtoupper( $parameter );
      	$this->Import[$this->ImportCount]["VALUE"]     = $value;
  	}

	/**
	 * @access public
	 */
  	function run()
  	{
    	if ( !$this->Rfc ) 
			return;
    
		if ( $this->CallFuncName )
    	{
       		$this->FCall = @saprfc_function_discover( $this->Rfc, $this->CallFuncName );
       
	   		for ( $i = 1; $i <= $this->ImportCount; $i++ )
           		saprfc_import( $this->FCall, $this->Import[$i]["PARAMETER"], $this->Import[$i]["VALUE"] );
				
       		$this->RetVal    = @saprfc_call_and_receive( $this->FCall );
       		$this->InterFace = @saprfc_function_interface( $this->FCall );
       		
			$this->fillExport();
    	}
  	}

	/**
	 * @access public
	 */
  	function fillExport()
  	{
      	for ( $i = 0; $i < count( $this->InterFace ); $i++ )
      	{
         	$interface = $this->InterFace[$i];
         
		 	if ( $interface[type] == "EXPORT" )
         	{
            	$this->ExportCount++;
            	$this->Export[$this->ExportCount]["TYPE"] = "EXPORT";
            	$this->Export[$this->ExportCount]["NAME"] = $interface["name"];
            	$var = saprfc_export( $this->FCall, $interface["name"] );
            	$this->Export[$this->ExportCount]["VALUE"] = $var;
         	}
         
		 	if ( $interface[type] == "TABLE" )
         	{
            	$this->TabCount++;
            	$this->TabName[$this->TabCount] = $interface[name];
            	$this->TabRows[$this->TabCount] = saprfc_table_rows( $this->FCall, $interface["name"] );
            
				for ( $j = 1; $j <= $this->TabRows[$this->TabCount]; $j++ )
                	$this->TabData[$this->TabCount][] = saprfc_table_read( $this->FCall, $interface[name], $j );
           
		   		for ( $i = 0; $i < count( $interface[def] ); $i++ )
                	$this->TabCols[$this->TabCount][$i+1] = $interface[def][$i][name];
         	}
      	}
  	}

	/**
	 * @access public
	 */
  	function getTableColumns( $tabname = "" )
  	{
        for ( $i = 1; $i <= $this->TabCount; $i++ )
		{
        	if ( $this->TabName[$i] == $tabname || $this->TabCount == 1 )
			{
          		for ( $j = 0; $j < count( $this->TabCols[$i] ); $j++ ) 
					$S = $S . ( $S? ",," : "" ) . $this->TabCols[$i][$j];
			}
		}
      
	  	return $S;
  	}

	/**
	 * @access public
	 */
  	function getValue( $name )
  	{
    	for ( $i = 1; $i <= $this->ExportCount; $i++ )
		{
       		if ( $this->Export[$i]["NAME"] == strtoupper( $name ) ) 
				return $this->Export[$i]["VALUE"];
		}
  	}

	/**
	 * @access public
	 */
  	function getTabValue( $tab, $col, $row )
  	{
      	$NAME = $this->TabCols[$tab][$row + 1];
      	return $this->TabData[$tab][$col][$NAME];
  	}

	/**
	 * @access public
	 */
  	function getTabValue1( $tab, $col, $NAME )
  	{
      	return $this->TabData[$tab][$col][$NAME];
  	}

	/**
	 * @access public
	 */
  	function close()
  	{
    	@saprfc_function_free( $this->FCall );
    	@saprfc_close( $this->Rfc );
  	}

	/**
	 * @access public
	 */
  	function dump()
  	{
    	echo "<TABLE BORDER=1>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "own_host" ) )         . "</TD><TD>" . $this->ConnectInfo["own_host"]         . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "partner_host" ) )     . "</TD><TD>" . $this->ConnectInfo["partner_host"]     . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "sysid" ) )            . "</TD><TD>" . $this->ConnectInfo["sysid"]            . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "user" ) )             . "</TD><TD>" . $this->ConnectInfo["user"]             . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "language" ) )         . "</TD><TD>" . $this->ConnectInfo["language"]         . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "trace" ) )            . "</TD><TD>" . $this->ConnectInfo["trace"]            . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "own_codepage" ) )     . "</TD><TD>" . $this->ConnectInfo["own_codepage"]     . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "partner_codepage" ) ) . "</TD><TD>" . $this->ConnectInfo["partner_codepage"] . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "rfc_role" ) )         . "</TD><TD>" . $this->ConnectInfo["rfc_role"]         . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "own_type" ) )         . "</TD><TD>" . $this->ConnectInfo["own_type"]         . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "own_rel" ) )          . "</TD><TD>" . $this->ConnectInfo["own_rel"]          . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "partner_type" ) )     . "</TD><TD>" . $this->ConnectInfo["partner_type"]     . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "partner_rel" ) )      . "</TD><TD>" . $this->ConnectInfo["partner_rel"]      . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "kernel_rel" ) )       . "</TD><TD>" . $this->ConnectInfo["kernel_rel"]       . "</TD></TR>";
		echo "<TR><TD>" . str_replace( "_", " ", strtoupper( "CPIC_convid" ) )      . "</TD><TD>" . $this->ConnectInfo["CPIC_convid"]      . "</TD></TR>";
    	echo "</TABLE>";
  	}
} // END OF SAPConnect

?>
