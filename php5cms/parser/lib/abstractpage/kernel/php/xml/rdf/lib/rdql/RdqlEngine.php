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
|Authors: Radoslaw Oldakowski <radol@gmx.de>                           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.RdfUtil' );


/**
 * Some general methods common for RdqlMemEngine.
 *
 * @package xml_rdf_lib_rdql
 */

Class RdqlEngine extends PEAR
{
	/**
 	 * Prints a query result as HTML table.
 	 * You can change the colors in the configuration file.
 	 *
 	 * @param array $queryResult [][?VARNAME] = object RdfNode
 	 * @access private
 	 */
 	function writeQueryResultAsHtmlTable( $queryResult )
	{
   		if ( current( $queryResult[0] ) == null )
		{
      		echo "No match<br>";
      		return;
   		}

   		echo "<table border='1' cellpadding='3' cellspacing='0'><tr><td><b>No.</b></td>";
   		
		foreach ( $queryResult[0] as $varName => $value )
     		echo "<td align='center'><b>$varName</b></td>";
   		
		echo "</tr>";

   		foreach ( $queryResult as $n => $var )
		{
     		echo "<tr><td width='20' align='right'>" .( $n + 1 ) . ".</td>";
     		
			foreach ( $var as $varName => $value )
			{
       			echo RDFAPI_INDENTATION . RDFAPI_INDENTATION . "<td bgcolor=\"";
   	   			echo RdfUtil::chooseColor( $value );
       			echo "\">";
       			echo "<p>";

      	 		$lang  = null;
       			$dtype = null;
       			
				if ( is_a( $value, "Literal" ) )
				{
    	   			if ( $value->getLanguage() != null )
			  			$lang =  " <b>(xml:lang=\"" . $value->getLanguage() . "\") </b> ";
		   			
					if ( $value->getDatatype() != null )
  			  			$dtype =  " <b>(rdf:datatype=\"" . $value->getDatatype() . "\") </b> ";
       			}
  	   			
				echo RdfUtil::getNodeTypeName( $value ) . $value->getLabel() . $lang . $dtype . "</p>";
     		}
     		
			echo "</tr>";
   		}
   		
		echo "</table>";
 	}
} // END OF RdqlEngine

?>
