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
 * @package format_swf
 */
 
/**
 * Constructor
 *
 * @access public
 */
Soundbank = function( swffile )
{
	this.Base = Base;
	this.Base();

	// populate sound table
	this.sndtable = new Dictionary();
	this.sndtable.add( 'audiologo',    2 );
	this.sndtable.add( 'alert',      100 );
	this.sndtable.add( 'critical',   200 );
	this.sndtable.add( 'ding',       300 );
	this.sndtable.add( 'error',      400 );
	this.sndtable.add( 'menu',       500 );
	this.sndtable.add( 'minimize',   600 );
	this.sndtable.add( 'newmail',    700 );
	this.sndtable.add( 'notify',     800 );
	this.sndtable.add( 'online',     900 );
	this.sndtable.add( 'printed',   1000 );
	this.sndtable.add( 'restore',   1100 );
	this.sndtable.add( 'trash',     1200 );
	
	var divname = "soundbankDiv";
	var width   = 20;
	var height  = 20;
	
	var snddiv = document.createElement( "DIV" );
	snddiv.id  = divname;
	snddiv.style.width  = width;
	snddiv.style.height = height;
	snddiv.style.visibility = "hidden";
	document.body.appendChild( snddiv );
	
	this.flash = new Flash( width, height, swffile || "soundbank.swf", false, false, null, null, divname );
};


Soundbank.prototype = new Base();
Soundbank.prototype.constructor = Soundbank;
Soundbank.superclass = Base.prototype;

/**
 * @access public
 */
Soundbank.prototype.play = function( which )
{
	if ( which == null || !this.sndtable.contains( which ) )
		return false;
		
	this.flash.playFrom( this.sndtable.get( which ) );
	return true;
};

/**
 * @access public
 */
Soundbank.prototype.has = function( which )
{
	return !this.sndtable.contains( which );
};
