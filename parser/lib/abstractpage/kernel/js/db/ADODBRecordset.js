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
 * @package db
 */
 
/**
 * Constructs an ADODBRecordset object (IE)
 *
 * @access public
 */
ADODBRecordset = function( filename )
{
	this.Base = Base;
	this.Base();
	
	this.rst  = new ActiveXObject( "ADODB.Recordset" );
	this.fso  = new ActiveXObject( "Scripting.FileSystemObject" );
	this.file = fso.CreateTextFile( filename || "database.xml" );
};


ADODBRecordset.prototype = new Base();
ADODBRecordset.prototype.constructor = ADODBRecordset;
ADODBRecordset.superclass = Base.prototype;

/**
 * @access public
 */
ADODBRecordset.prototype.tabletoxml = function( table, rows, descrip )
{
	this.file.WriteLine( "<" + table + " name=\"" + descrip + "\">" );
	this.rst.Open( "SELECT * FROM " + table , "Provider=Microsoft.Jet.OLEDB.4.0;Data Source=db1.mdb;" );
	this.propstoxml( rst, "props", "prop" );
	rst.MoveFirst();
	
	for ( i = 0; !rst.EOF; i++ )
	{
		this.recordtoxml( rst, rows );
		this.rst.MoveNext();
	}
	
	this.rst.Close();
	this.file.WriteLine( "</" + table + ">" );
};

/**
 * @access public
 */
ADODBRecordset.prototype.recordtoxml = function( r, n )
{
	var os = "<" + n + " ";
	
	for ( i = 0; i < r.fields.Count; i++ )
		os += r.fields(i).name + "=\"" + r.fields(i).value + "\" ";
	
	os += "/>";
	this.file.WriteLine( os );
};

/**
 * @access public
 */
ADODBRecordset.prototype.propstoxml = function( r, ns, n )
{
	this.file.WriteLine( "<" + ns + "> " );
	
	for ( i = 0; i < r.properties.Count; i++ )
		this.file.WriteLine( "<" + n + " name=\"" + r.properties(i).name + "\" value=\"" + r.properties(i).value + "\" type=\"" + r.properties(i).type + "\" attributes=\"" + r.properties(i).Attributes + "\" />" );
	
	this.file.WriteLine( "</" + ns + "> " );
};


// private methods

/**
 * @access private
 */
ADODBRecordset.prototype._example = function()
{
	this.file.WriteLine( "<?xml version=\"1.0\"?>" );
	this.file.WriteLine( "<products>" );

	this.tabletoxml( "monitors", "monitor", "some monitors" );
	this.tabletoxml( "modems",   "modem",   "some modems"   );

	this.file.WriteLine( "</products>" );
};
