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
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
TemplateParser = function()
{
	this.Base = Base;
	this.Base();
	
	this.vars        = new Dictionary();
	this.conditions  = new Dictionary();
	this.templates   = new Dictionary();
	this.buffer		 = new HTTPBuffer();
	
	this.actualkey   = null;
	this.parsed      = "";
	
	this.loadedfiles = 0;
	this.loadallnum  = 0;
	
	this.var_start   = "{";
	this.var_end     = "}";
	
	this.cond_start  = "[%";
	this.cond_end    = "%]";
	
	// event fired when all templates are loaded
	this.onloadingdone = new Function;
};


TemplateParser.prototype = new Base();
TemplateParser.prototype.constructor = TemplateParser;
TemplateParser.superclass = Base.prototype;


// variables

/**
 * @access public
 */
TemplateParser.prototype.addVar = function( key, val )
{
	if ( ( key != null ) && ( val != null ) )
		this.vars.add( key, val );
};

/**
 * @access public
 */
TemplateParser.prototype.setVar = function( key, val )
{
	if ( ( key != null ) && ( val != null ) )
		this.vars.set( key, val );
};

/**
 * @access public
 */
TemplateParser.prototype.getVar = function( key )
{
	return this.vars.get( key );
};

/**
 * @access public
 */
TemplateParser.prototype.removeVar = function( key )
{
	return this.vars.remove( key );
};

/**
 * @access public
 */
TemplateParser.prototype.clearVars = function()
{
	this.vars.empty();
};


// conditions

/**
 * @access public
 */
TemplateParser.prototype.addCondition = function( key, val )
{
	if ( ( key != null ) && ( val != null ) )
		this.conditions.add( key, val );
};

/**
 * @access public
 */
TemplateParser.prototype.getCondition = function( key )
{
	return this.conditions.get( key );
};

/**
 * @access public
 */
TemplateParser.prototype.removeCondition = function( key )
{
	return this.conditions.remove( key );
};

/**
 * @access public
 */
TemplateParser.prototype.clearConditions = function()
{
	this.conditions.empty();
};


// templates

/**
 * @access public
 */
TemplateParser.prototype.addTemplate = function( key, html )
{
	if ( ( key != null ) && ( html != null ) )
		this.templates.add( key, html );
};

/**
 * @access public
 */
TemplateParser.prototype.loadTemplates = function()
{
	var arr;	
	var tplobj = this;

	tplobj.loadedfiles = 0;
	tplobj.filequeue   = arguments[0];
	tplobj.loadallnum  = arguments[0].length;
	
	this.buffer.onload = function( e )
	{
		tplobj.addTemplate( tplobj.filequeue[tplobj.loadedfiles++][0], this.getHTML() );
		
		// everything loaded, now fire event
		if ( tplobj.loadedfiles == tplobj.loadallnum )
			tplobj.onloadingdone();
	}
	
	for ( var i = 0; i < arguments[0].length; i++ )
	{
		arr = arguments[0][i];
		this.buffer.getURL( this.getTemplatePath()? this.getTemplatePath() + arr[1] : arr[1] );
	}
};

/**
 * @access public
 */
TemplateParser.prototype.setTemplatePath = function( path )
{
	if ( path != null )
	{
		this.templatepath = path;
		return this.getTemplatePath();
	}
	
	return false;	
};

/**
 * @access public
 */
TemplateParser.prototype.getTemplatePath = function()
{
	if ( !this.templatepath )
		return false;
	else
		return this.templatepath;
};

/**
 * @access public
 */
TemplateParser.prototype.getTemplate = function( key )
{
	return this.templates.get( key );
};

/**
 * @access public
 */
TemplateParser.prototype.removeTemplate = function( key )
{
	return this.templates.remove( key );
};

/**
 * @access public
 */
TemplateParser.prototype.clearTemplates = function()
{
	this.templates.empty();
};

/**
 * @access public
 */
TemplateParser.prototype.isTemplate = function( key )
{
	return this.templates.contains( key );
};

/**
 * @access public
 */
TemplateParser.prototype.parseTemplate = function( key, preventSub )
{
	if ( key == null )
		return false;
	
	var gt, pattern, i, j;
	
	var tpl  = this.getTemplate( key );
	var str  = new String( tpl );
	var keys = this.vars.getKeys();

	// condition handling
	str = this.parseConditions( str );
	
	// preprocess subtemplates
	if ( !preventSub )
	{
		for ( i in keys )
		{
			if ( this.isTemplate( this.vars.get( keys[i] ) ) )
				this.setVar( keys[i], this.parseTemplate( this.vars.get( keys[i] ), true ) );
		}
	}

	var gt, newStr;	
	for ( i in keys )
	{
		// loop template
		if ( typeof( this.vars.get( keys[i] ) ) == "object" )
		{
			// TODO
		}
		else
		{
			gt = -1;			
			pattern = this.var_start + keys[i] + this.var_end;
			
			while ( str.indexOf( pattern, gt + 1 ) > -1 )
			{		
				gt = str.indexOf( pattern, gt + 1 );
				newStr  = str.substr( 0, gt );
				newStr += this.vars.get( keys[i] );
				newStr += str.substr( gt + pattern.length, str.length );
				str = newStr;
			}
		}
	}
	
	this.parsed = str;
	return str;
};

/**
 * @access public
 */
TemplateParser.prototype.insertParsed = function( key, div, convertSpecial )
{
	if ( ( key != null ) && !document.all[div] )
		return false;
		
	if ( convertSpecial == true )
		document.all[div].innerText = this.parseTemplate( key ).convertToEscapes();
	else
		document.all[div].innerHTML = this.parseTemplate( key );

	return true;
};

/**
 * @access public
 */
TemplateParser.prototype.parseConditions = function( str )
{
	var start, end, mapObj;
	var ret = "";
	var map = new Array();
	
	outer:
	for ( var i = 0; i < str.length; i++ )
	{		
		if ( str.substring( i, i + 4 ) ==  this.cond_start + "IF" )
		{
			start = i;
			
			for ( var j = i; j < str.length; j++ )
			{
				if ( str.substring( j, j + 7 ) ==  "ENDIF" + this.cond_end )
				{
					end = j + 7;
					i   = j + 6;
					
					// this is our chunk
					var chunk = str.substring( start, end )
					chunk = chunk.split( this.cond_start );
					
					var statement, regexp, operator, statElements, tplVar, result;
					var added = false;
					
					for ( k = 1; k < chunk.length; k++ )
					{
						chunk[k] = chunk[k].split( this.cond_end );
						
						// parse statement
						statement = chunk[k][0];
						
						// sniff for operator
						regexp = statement.search( /[^a-zA-Z0-9\s*]+/ );

						if ( regexp > -1 )
						{
							operator     = statement.match( /[^a-zA-Z0-9\s*]+/ );
							statElements = statement.split( /[^a-zA-Z0-9\s*]+/ );
							
							// strip case ( "IF access" -> "access" )
							statElements[0] = statElements[0].substring( statElements[0].indexOf( " " ) + 1, statElements[0].length );
							tplVar = this.getCondition( statElements[0] );
							
							// eval statement
							result = eval( tplVar + operator + statElements[1] );
							
							// statement is true
							if ( result )
							{
								added  = true;
								ret   += chunk[k][1];
							}
						}
						
						// push else if all checks failed
						if ( ( statement.indexOf( "ELSE" ) != -1 ) && ( added == false ) )
							ret += chunk[k][1];
					}

					continue outer;
				}
			}
		}
		else
		{
			ret += str.charAt( i );
		}
	} 
	
	return ret;
};

/**
 * @access public
 */
TemplateParser.prototype.flush = function()
{
	this.parsed = "";
	
	this.clearVars();
	this.clearConditions();
	this.clearTemplates();
};

/**
 * @access public
 */
TemplateParser.prototype.dump = function()
{
	var vars = this.vars.getKeys();
	var cond = this.conditions.getKeys();	
	
	var str  = "Var dump:\n\n";
	
	for ( var i in vars )
		str += "key: " + vars[i] + " - value: " + this.vars.get( vars[i] ) + "\n";
	
	str += "\n\n\nConditions:\n\n";
	
	for ( var i in cond )
		str += "key: " + cond[i] + " - value: " + this.conditions.get( cond[i] ) + "\n";
		
	return str;
};
