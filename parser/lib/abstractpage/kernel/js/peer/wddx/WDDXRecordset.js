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
 * @package peer_wddx
 */
 
/**
 * Constructor
 *
 * @access public
 */
WDDXRecordset = function()
{
	this.Base = Base;
	this.Base();
	
	// Add default properties.
	this.preserveFieldCase = false;
	
	// Add extensions.
	if ( typeof( WDDXRecordset.extensions ) == "object" )
	{
		for ( var prop in WDDXRecordset.extensions )
		{
			// Hook-up method to WDDXRecordset object.
			this[prop] = WDDXRecordset.extensions[prop];
		}
	}

	// Perfom any needed initialization.
	if ( WDDXRecordset.arguments.length > 0 )
	{
		var cols = WDDXRecordset.arguments[0];
		var nLen = ( WDDXRecordset.arguments.length > 1 )? WDDXRecordset.arguments[1] : 0;
		
		for ( var i = 0; i < cols.length; ++i )
		{
 		    var colValue = new Array( nLen );
			
		    for ( var j = 0; j < nLen; ++j )
				colValue[j] = null;
		
			this[cols[i]] = colValue;
		}		
	}
};


WDDXRecordset.prototype = new Base();
WDDXRecordset.prototype.constructor = WDDXRecordset;
WDDXRecordset.superclass = Base.prototype;

/**
 * getRowCount() returns the number of rows in the recordset
 *
 * @access public
 */
WDDXRecordset.prototype.getRowCount = function()
{
	var nRowCount = 0;
	
	for ( var col in this )
	{
		if ( typeof( this[col] ) == "object" )
		{
			nRowCount = this[col].length;
			break;
		}
	}
	
	return nRowCount;
};

/**
 * addColumn(name) adds a column with that name and length == getRowCount()
 *
 * @access public
 */
WDDXRecordset.prototype.addColumn = function( name )
{
	var nLen = this.getRowCount();
	var colValue = new Array(nLen);
	
	for ( var i = 0; i < nLen; ++i )
		colValue[i] = null;
	
	this[this.preserveFieldCase ? name : name.toLowerCase()] = colValue;
};

/**
 * addRows() adds n rows to all columns of the recordset
 *
 * @access public
 */
WDDXRecordset.prototype.addRows = function( n )
{
	for ( var col in this )
	{
		var nLen = this[col].length;
		
		for ( var i = nLen; i < nLen + n; ++i )
			this[col][i] = null;
	}
};

/**
 * @access public
 */
WDDXRecordset.prototype.getField = function( row, col )
{
	return this[this.preserveFieldCase? col : col.toLowerCase()][row];
};

/**
 * setField() sets the element in a given (row, col) position to value
 *
 * @access public
 */
WDDXRecordset.prototype.setField = function( row, col, value )
{
	this[this.preserveFieldCase? col : col.toLowerCase()][row] = value;
};

/**
 * wddxSerialize() serializes a recordset
 *
 * @return boolean
 * @access public
 */
WDDXRecordset.prototype.wddxSerialize = function( serializer )
{
	// Create an array and a list of column names.
	var colNamesList = "";
	var colNames = new Array();
	var i = 0;
	
	for ( var col in this )
	{
		if ( typeof( this[col] ) == "object" )
		{
			colNames[i++] = col;

            if ( colNamesList.length > 0 )
				colNamesList += ",";
			
			colNamesList += col;			
		}
	}
	
	var nRows = this.getRowCount();
	serializer.write( "<recordset rowCount='" + nRows + "' fieldNames='" + colNamesList + "'>" );
	var bSuccess = true;
	
	for ( i = 0; bSuccess && i < colNames.length; i++ )
	{
		var name = colNames[i];
		serializer.write( "<field name='" + name + "'>" );
		
		for ( var row = 0; bSuccess && row < nRows; row++ )
			bSuccess = serializer.serializeValue( this[name][row] );
		
		serializer.write( "</field>" );
	}
	
	serializer.write( "</recordset>" );
	return bSuccess;
};

/**
 * dump(escapeStrings) returns an HTML table with the recordset data
 * It is a convenient routine for debugging and testing recordsets.
 *
 * @param  boolean  escapeStrings  determines whether the <>& characters in string values are escaped as &lt;&gt;&amp;
 * @access public
 */
WDDXRecordset.prototype.dump = function( escapeStrings )
{
	// Get row count.
	var nRows = this.getRowCount();
	
	// Determine column names.
	var colNames = new Array();
	var i = 0;
	
	for ( var col in this )
	{
		if ( typeof( this[col] ) == "object" )
			colNames[i++] = col;
	}

    // Build table headers.
	var o = "<table border=1><tr><td><b>RowNumber</b></td>";
	
	for ( i = 0; i < colNames.length; ++i )
		o += "<td><b>" + colNames[i] + "</b></td>";
	
	o += "</tr>";
	
	// Build data cells.
	for ( var row = 0; row < nRows; ++row )
	{
		o += "<tr><td>" + row + "</td>";
		
		for ( i = 0; i < colNames.length; ++i )
		{
        	var elem = this.getField( row, colNames[i] );
			
            if ( escapeStrings && typeof( elem ) == "string" )
            {
            	var str = "";
				
            	for ( var j = 0; j < elem.length; ++j )
                {
                	var ch = elem.charAt( j );
					
                    if ( ch == '<' )
                    	str += "&lt;";
                    else if ( ch == '>' )
                    	str += "&gt;";
                    else if ( ch == '&' )
                    	str += "&amp;";
                    else
                    	str += ch;
                }            
				
				o += ( "<td>" + str + "</td>" );
            }
            else
            {
				o += ( "<td>" + elem + "</td>" );
            }
		}
		
		o += "</tr>";
	}

	// Close table.
	o += "</table>";

	// Return HTML recordset dump
	return o;	
};


/**
 * @access public
 * @static
 */
WDDXRecordset.extensions = new Object;


/**
 * WddxRecordset extensions
 * 
 * The WddxRecordset class has been designed with extensibility in mind.
 * Developers can create new methods for the class outside this file as
 * long as they make a call to registerWddxRecordsetExtension() with the
 * name of the method and the function object that implements the method.
 * The WddxRecordset constructor will automatically register all these
 * methods with instances of the class.
 * 
 * Example:
 * 
 * If I want to add a new WddxRecordset method called addOneRow() I can
 * do the following:
 * 
 * - create the method implementation
 * 
 * function wddxRecordset_addOneRow()
 * {
 * 		this.addRows(1);
 * }
 * 
 * - call registerWddxRecordsetExtension() 
 * 
 * WDDXRecordset.registerExtension("addOneRow", wddxRecordset_addOneRow);
 * 
 * - use the new function
 * 
 * rs = new WDDXRecordset();
 * rs.addOneRow();
 */
 
/**
 * @access public
 * @static
 */
WDDXRecordset.registerExtension = function( name, func )
{
	// Perform simple validation of arguments.
	if ( ( typeof( name ) == "string" ) && ( typeof( func ) == "function" ) )
	{
		// Register extension; override an existing one.
		WDDXRecordset.extensions[name] = func;
	}
};
