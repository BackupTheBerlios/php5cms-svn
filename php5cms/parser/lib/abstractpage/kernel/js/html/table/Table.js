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
 * @package html_table
 */
 
/**
 * Constructor
 *
 * @access public
 */
Table = function()
{
	this.Base = Base;
	this.Base();
	
	var propsDefaultLength = 2;
	
	this.properties = new Object();
	this.cols = 1;
	this.rows = 1;
	
	for ( var i = 1; i <= Table.arguments.length; i++ )
	{
		if ( Table.arguments[( i - 1 )].indexOf( "cols" ) >= 0 )
			propsDefaultLength--;
		
		if ( Table.arguments[( i - 1 )].indexOf( "rows" ) >= 0 )
			propsDefaultLength--;
		
		eval( "this.properties." + Table.arguments[( i - 1 )] );
	}
	
	this.propsLengthPublic = ( Table.arguments.length + propsDefaultLength );
	this.propsLengthPrivat = 7;
	
	this.content = this.tableContent( this.properties.cols, this.properties.rows );
};


Table.prototype = new Base();
Table.prototype.constructor = Table;
Table.superclass = Base.prototype;

/**
 * @access public
 */
Table.prototype.draw = function()
{
	this.span();

	var props = "";
	var i = 0;
	
	var dummyRows = this.content.length;
	var dummyCols = this.content[0].length;
	var dummyCode = '<table';
	
	for ( prop in this.properties )
	{
		if ( i >= this.propsLengthPublic )
			break;
		
		dummyCode = dummyCode + ' ' + prop + '="' + this.properties[prop] + '"';
		i++;
	}
	
	dummyCode = dummyCode + '>\n';

	for ( var i = 1; i <= dummyRows; i++ )
	{
		dummyCode = dummyCode + '<tr>\n';
		
		for ( var k = 1; k <= dummyCols; k++ )
		{
			if ( this.content[( i - 1 )][( k - 1 )][2] == "false" )
				dummyCode = dummyCode + '\t<td ' + this.content[( i - 1 )][( k - 1 )][0] + '>' + this.content[( i - 1 )][( k - 1 )][1] + '</td>\n';
		}
		
		dummyCode = dummyCode+'</tr>\n';
	}

	dummyCode = dummyCode+'</table>\n';
	return dummyCode;
};

/**
 * @access public
 */
Table.prototype.span = function()
{
	var dummyRows = this.content.length;
	var dummyCols = this.content[0].length;

	for ( var i = 1; i <= dummyRows; i++ )
	{
		var rowspan   = 0;
		var colspan   = 0;
		var fromHere  = 0;
		var tillThere = 0;
		var myString  = "";
		
		for ( var k = 1; k <= dummyCols; k++ )
		{
			rowspan = 0;
			colspan = 0;
			
			if ( this.content[( i - 1 )][( k - 1 )][2] == "false" )
			{
				myString = ( this.content[( i - 1 )][( k - 1 )][0]) + "";
				
				if ( myString.indexOf( "colspan" ) >= 0 )
				{
					fromHere  = ( myString.indexOf( "colspan" ) ) + 9;
					tillThere = ( myString.indexOf( '"', fromHere ) ) - 1;
					
					if ( ( fromHere - tillThere ) == 0 )
						colspan = myString.charAt( fromHere );
					else
						colspan = myString.substring( fromHere, tillThere );
						
					colspan = parseInt( colspan );
				}
				
				if ( myString.indexOf( "rowspan" ) >= 0 )
				{
					fromHere  = ( myString.indexOf( "rowspan" ) ) + 9;
					tillThere = ( myString.indexOf( '"', fromHere ) ) - 1;
					
					if ( ( fromHere - tillThere ) == 0 )
						rowspan = myString.charAt( fromHere );
					else
						rowspan = myString.substring( fromHere, tillThere );
					
					rowspan = parseInt( rowspan );
				}
				
				if ( ( colspan >= 2 ) && ( rowspan <= 1 ) )
				{
					for ( var m = 2; m <= colspan; m++ )
						this.content[( i - 1 )][( ( k - 1 ) + ( m - 1 ) )][2] = "true";
				}
				
				if ( ( rowspan >= 2 ) && ( colspan <= 1 ) )
				{
					for ( var m = 2;m <= rowspan; m++ )
						this.content[( ( i - 1 ) + ( m - 1 ) )][( k - 1 )][2] = "true";
				}
				
				if ( ( rowspan >= 2 ) && ( colspan >= 2 ) )
				{
					for ( var m = 1; m <= rowspan; m++ )
					{
						for ( var p = 1; p <= colspan; p++ )
							this.content[( ( i - 1 ) + ( m - 1 ) )][( ( k - 1 ) + ( p - 1 ) )][2] = "true";
					}
					
					this.content[( i - 1 )][( k - 1 )][2] = "false";
				}
			}
		}
	}
};

/**
 * @access public
 */
Table.prototype.tableContent = function( dummyCols, dummyRows )
{
	var dummyContent = "[";
	
	for ( var i = 1; i <= dummyRows; i++ )
	{
		dummyContent = dummyContent + "[";
		
		for ( var k = 1; k <= dummyCols; k++ )
			dummyContent = dummyContent + "[['align=" + '"left"' + " valign=" + '"top"' + "'],[' '],['false']],";
		
		dummyContent = dummyContent.substring( 0, ( ( dummyContent.length ) - 1 ) );
		dummyContent = dummyContent + "],"
	}
	
	dummyContent = dummyContent.substring( 0, ( ( dummyContent.length ) - 1 ) );
	dummyContent = dummyContent + "]";
	
	return eval( dummyContent );
};

/**
 * @access public
 */
Table.prototype.contentArraysToArguments = function()
{
	var dummyRows = this.content.length;
	var dummyCols = this.content[0].length;
	
	for ( var i = 1; i <= dummyRows; i++ )
	{
		for ( var k = 1; k <= dummyCols; k++ )
		{
			this.content[( i - 1 )][( k - 1 )].props = this.content[( i - 1 )][( k - 1 )][0];
			this.content[( i - 1 )][( k - 1 )].value = this.content[( i - 1 )][( k - 1 )][1];
			this.content[( i - 1 )][( k - 1 )].spans = this.content[( i - 1 )][( k - 1 )][2];
		}
	}
};

/**
 * @access public
 */
Table.prototype.contentArgumentsToArrays = function()
{
	var dummyRows = this.content.length;
	var dummyCols = this.content[0].length;
	
	for ( var i = 1; i <= dummyRows; i++ )
	{
		for ( var k = 1; k <= dummyCols; k++ )
		{
			this.content[( i - 1 )][( k - 1 )][0] = this.content[( i - 1 )][( k - 1 )].props;
			this.content[( i - 1 )][( k - 1 )][1] = this.content[( i - 1 )][( k - 1 )].value;
			this.content[( i - 1 )][( k - 1 )][2] = this.content[( i - 1 )][( k - 1 )].spans;
		}
	}
};
