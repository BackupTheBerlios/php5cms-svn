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
 * @package peer_http
 */
 
/**
 * Constructor
 *
 * @access public
 */
DownloadTime = function()
{
	this.Base = Base;
	this.Base();
};


DownloadTime.prototype = new Base();
DownloadTime.prototype.constructor = DownloadTime;
DownloadTime.superclass = Base.prototype;

/**
 * @access public
 */
DownloadTime.prototype.calculateFromMB = function( size )
{
	if ( size != null )
		return false;
		
	return this._calculate( size, 1024 );
};

/**
 * @access public
 */
DownloadTime.prototype.calculateFromKB = function( size )
{
	if ( size != null )
		return false;
		
	return this._calculate( size, 1 );
};

/**
 * @access public
 */
DownloadTime.prototype.dump = function( size, mult )
{
	var str = "";
	var obj = this._calculate( size, mult || 1024 );
	
	str += "9.6 Kb: "  + obj.kb9_6[0]       + "h " + obj.kb9_6[1]       + "m " + obj.kb9_6[2]       + "s\n";
	str += "14.4 Kb: " + obj.kb14_4[0]      + "h " + obj.kb14_4[1]      + "m " + obj.kb14_4[2]      + "s\n";
	str += "28.8 Kb: " + obj.kb28_8[0]      + "h " + obj.kb28_8[1]      + "m " + obj.kb28_8[2]      + "s\n";
	str += "33.6 Kb: " + obj.kb33_6[0]      + "h " + obj.kb33_6[1]      + "m " + obj.kb33_6[2]      + "s\n";
	str += "56 Kb: "   + obj.kb56[0]        + "h " + obj.kb56[1]        + "m " + obj.kb56[2]        + "s\n";
	str += "ISDN64: "  + obj.kb_isdn64[0]   + "h " + obj.kb_isdn64[1]   + "m " + obj.kb_isdn64[2]   + "s\n";
	str += "ISDN128: " + obj.kb_isdn_128[0] + "h " + obj.kb_isdn_128[1] + "m " + obj.kb_isdn_128[2] + "s\n";
	str += "DSL768: "  + obj.kb_dsl_768[0]  + "h " + obj.kb_dsl_768[1]  + "m " + obj.kb_dsl_768[2]  + "s\n";
	
	return str;
};


// private methods

/**
 * @access private
 */
DownloadTime.prototype._calculate = function( size, mult )
{
	var result = 
	{
		'kb9_6':       this._getRaw( size, mult, DownloadTime.factors.kb9_6       ),
		'kb14_4':      this._getRaw( size, mult, DownloadTime.factors.kb14_4      ),
		'kb28_8':      this._getRaw( size, mult, DownloadTime.factors.kb28_8      ),
		'kb33_6':      this._getRaw( size, mult, DownloadTime.factors.kb33_6      ),
		'kb56':        this._getRaw( size, mult, DownloadTime.factors.kb56        ),
		'kb_isdn64':   this._getRaw( size, mult, DownloadTime.factors.kb_isdn64   ),
		'kb_isdn_128': this._getRaw( size, mult, DownloadTime.factors.kb_isdn_128 ),
		'kb_dsl_768':  this._getRaw( size, mult, DownloadTime.factors.kb_dsl_768  )
		/*
		'' : null,
		'' : null,
		'' : null,
		'' : null
		*/
	};
	
	return result;
};

/**
 * @access private
 */
DownloadTime.prototype._getRaw = function( size, mult, factor )
{
	var totalTime = ( ( size * mult ) / factor );

	with ( Math )
	{
		var totalHours    = floor( ( totalTime / 3600 ) );
		var totalHoursMod = ( totalTime % 3600 );
		var totalMin      = floor( totalHoursMod / 60 );
		var totalMinMod   = ( totalHoursMod % 60 );
		var totalSec      = floor( totalMinMod );
	}

	return [ totalHours, totalMin, totalSec ];
};


/**
 * @access public
 * @static
 */
DownloadTime.factors =
{
	'kb9_6':       '1.1719',
	'kb14_4':      '1.7578',
	'kb28_8':      '3.5156',
	'kb33_6':      '4.1016',
	'kb56':        '6.8359',
	'kb_isdn64':   '7.8125',
	'kb_isdn_128': '16.6250',
	'kb_dsl_768':  '187.9883' // ?
	/*
	'': '1220.7031',
	'': '3295.8984',
	'': '5493.1641',
	'': '18920.8984'
	*/
};
