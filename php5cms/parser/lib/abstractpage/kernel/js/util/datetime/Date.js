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
 * @package util_datetime
 */
 
/**
 * @access public
 */
Date.prototype.getMonthName = function()
{
	return [
		"January",
		"February",
		"March",
		"April",
		"May",
		"June",
		"July",
		"August",
		"September",
		"October",
		"November",
		"December"][this.getMonth()];
};

/**
 * @access public
 */
Date.prototype.getGermanMonthName = function()
{
	return [
		"Januar",
		"Februar",
		"März",
		"April",
		"Mai",
		"Juni",
		"Juli",
		"August",
		"September",
		"Oktober",
		"November",
		"Dezember"][this.getMonth()];
};

/**
 * Returns a nicely formatted date string (e.g. January 26, 2002).
 *
 * @return string
 * @access public
 */
Date.prototype.getHumanDateString = function()
{
	return this.getMonthName() + " " + this.getDate() + ", " + this.getFullYear();
};

/**
 * Returns a nicely formatted date string (e.g. 26. Januar 2002).
 *
 * @return string
 * @access public
 */
Date.prototype.getHumanDateStringGerman = function()
{
	return this.getDate() + ". " + this.getGermanMonthName() + " " + this.getFullYear();
};

/**
 * Returns a nicely formatted time string ( mode=1: 24 hours ).
 *
 * @return string
 * @access public
 */
Date.prototype.getHumanTimeString = function( mode )
{
	var h = this.getHours();
	var m = this.getMinutes();
	
	if ( mode == null )
	{
		var t = ( h >= 12 )? "pm" : "am";

		if ( h == 0 )
			h = 24;
		
		if ( h > 12 )
			h -= 12;
		
		h = String( h );
		m = String( m );
	
		if ( m.length == 1 )
			m = "0" + m;

		return h + ":" + m + " " + t;
	}
	else
	{
		h = String( h );
		m = String( m );
	
		if ( m.length == 1 )
			m = "0" + m;

		return h + ":" + m;	
	}
};

/**
 * @access public
 */
Date.prototype.getGermanDate = function() 
{
	var weekdays = new Array( 7 );
	weekdays[0] = "Sonntag";
	weekdays[1] = "Montag";
	weekdays[2] = "Dienstag";
	weekdays[3] = "Mittwoch";
	weekdays[4] = "Donnerstag";
	weekdays[5] = "Freitag";
	weekdays[6] = "Samstag";

	var months = new Array( 12 );
	months[ 0] = "Januar";
	months[ 1] = "Februar";
	months[ 2] = "M&auml;rz";
	months[ 3] = "April";
	months[ 4] = "Mai";
	months[ 5] = "Juni";
	months[ 6] = "Juli";
	months[ 7] = "August";
	months[ 8] = "September";
	months[ 9] = "Oktober";
	months[10] = "November";
	months[11] = "Dezember";

	var year = this.getYear();
	var ds   = "";
	
	ds = ds + weekdays[this.getDay()];
	ds = ds + ", den ";
	ds = ds + Date._zeroPrefix( this.getDate(), 2 );
	ds = ds + ". ";
	ds = ds + months[this.getMonth()];
	ds = ds + " ";
	ds = ds + year;

	return ds;
};

/**
 * @access public
 */
Date.prototype.getGermanTime = function() 
{
	var ds = "";
	ds = ds + Date._zeroPrefix( this.getHours(),   2 );
	ds = ds + ":";
	ds = ds + Date._zeroPrefix( this.getMinutes(), 2 );
	ds = ds + ":";
	ds = ds + Date._zeroPrefix( this.getSeconds(), 2 );

	return ds;
};

/**
 * @access public
 */
Date.prototype.getEnglishDate = function() 
{
	var weekdays = new Array(7);
	weekdays[0] = "Sunday";
	weekdays[1] = "Monday";
	weekdays[2] = "Tuesday";
	weekdays[3] = "Wednesday";
	weekdays[4] = "Thursday";
	weekdays[5] = "Friday";
	weekdays[6] = "Saturday";

	var months = new Array(12);
	months[ 0] = "January";
	months[ 1] = "February";
	months[ 2] = "March";
	months[ 3] = "April";
	months[ 4] = "May";
	months[ 5] = "June";
	months[ 6] = "July";
	months[ 7] = "August";
	months[ 8] = "September";
	months[ 9] = "October";
	months[10] = "November";
	months[11] = "December";

	var year = this.getYear();
	var ds   = "";
	
	ds = ds + weekdays[this.getDay()];
	ds = ds + ", ";
	ds = ds + Date._zeroPrefix( this.getDate(), 2 );
	ds = ds + " ";
	ds = ds + months[this.getMonth()];
	ds = ds + " ";
	ds = ds + year;

	return ds;
};

/**
 * @access public
 */
Date.prototype.getEnglishTime = function() 
{
	var ds = "";
	ds = ds + Date._zeroPrefix( this.getHours(),   2 );
	ds = ds + ":";
	ds = ds + Date._zeroPrefix( this.getMinutes(), 2 );
	ds = ds + ":";
	ds = ds + Date._zeroPrefix( this.getSeconds(), 2 );

	return ds;
};


/**
 * @access public
 * @static
 */
Date.defaultDateMask = "mm/dd/yyyy";

/**
 * Takes yy and tries to output yyyy.
 *
 * @access public
 * @static
 */
Date.makeFourDigitYear = function( s )
{ 
  	if ( s.length > 2 ) 
		return s;
  
  	if ( s < 50 ) 
		return "20" + Date._padZeros( s, 2 );
  	else 
		return "19"+s;
};

/**
 * @access public
 * @static
 */
Date.getMonthLength = function( m, y ) // 0 = jan
{
  	y = Date.makeFourDigitYear( y ); // year must be yyyy
  	m = parseInt( m, 10 );
  
  	// check for leap year - any year divisible by 4 or 400 - add one day to february
  	if ( m == 1 )
	{
     	if ( y / 4 == Math.floor( y / 4 ) || y / 400 == Math.floor( y / 400 ) ) 
			return 29;
	}

	return [
	31,
	28,
	31,
	30,
	31,
	30,
	31,
	31,
	30,
	31,
	30,
	31][m];
};

/**
 * @access public
 * @static
 */
Date.addDaysToDate = function( d, n )
{
  	d = new Date( d );
  	n = parseInt( n ); // forces a cast to integer
  	var month   = d.getMonth();
  	var day     = d.getDate();
  	var year    = d.getFullYear();
  	var tempday = day + n;
  
  	if ( tempday < 1 )
	{
    	month--;
    
		if ( month < 0 ) 
			month = 11;
    
		tempday = Date.getMonthLength( month, year ) - tempday;
  	}
  
  	if ( tempday > Date.getMonthLength( month, year ) )
	{
    	tempday = Date.getMonthLength( month, year ) - tempday;
    	month++;
    
		if ( month > 11 ) 
			month = 0;
  	}
  
  	return Date.createDate( month, tempday, year );
};

/**
 * @access public
 * @static
 */
Date.formatDate = function( d, f )
{
  	f = f || Date.defaultDateMask;
  	var del = null;
	
  	if ( f.indexOf( "/" ) > -1 ) 
		del = "/";
	else if ( f.indexOf( "-" ) > -1 )
		del = "-";
	else if ( f.indexOf( "." ) > -1 )
		del = ".";
  	
	if ( !del )
		return;

  	// breakout the parts of the format string
  	f = f.split( del );
  	d = new Date( d );
  
  	for ( var a = 0; a < f.length; a++ )
	{
    	f[a] = f[a].toUpperCase();
    
		if ( f[a].charAt( 0 ) == "M" )
			f[a] = Date._padZeros( d.getMonth() + 1, f[a].length );
			
    	if ( f[a].charAt( 0 ) == "D" )
			f[a] = Date._padZeros( d.getDate(), f[a].length );
			
    	if ( f[a].charAt( 0 ) == "Y" )
			f[a] = Date._padZeros( Date._rightStr( d.getFullYear(), f[a].length ) );
  	}
  
  	var r = f[0];
  
  	for ( a = 1; a < f.length; a++ )
		r += del + f[a];
  
  	return r;
};

/**
 * Returns false if not a date or returns the date as a Date object.
 *
 * @access public
 * @static
 */
Date.isDate = function( d, f )
{
  	// find the delimiter used (note: only one can be used at a time!!)
  	d = String( d );
  	f = f || sysutils.defaultDateMask;
  	var del = null;
  
  	if ( f.indexOf( "/" ) > -1 )
		del = "/";
	else if ( f.indexOf( "-" ) > -1 )
		del = "-";
	else if ( f.indexOf( "." ) > -1 )
		del = ".";
  	
	if ( !del )
		return false;

  	f = f.split( del );
  	d = d.split( del );
  
  	if ( d.length != f.length )
		return false;
  
  	var mo = null;
	var da = null; 
	var yr = null;
  
  	for ( var a = 0; a < f.length; a++ )
	{
    	f[a] = f[a].toUpperCase();
    
		if ( f[a].charAt( 0 ) == "M" )
			mo = Date._padZeros( d[a],f[a].length );
    	
		if ( f[a].charAt( 0 ) == "D" )
			da = Date._padZeros( d[a],f[a].length );
    	
		if ( f[a].charAt( 0 ) == "Y" )
			yr = Date._padZeros( d[a],f[a].length );
  	}

  	if ( !Date._isInteger( mo ) || !Date._isInteger( da ) || !Date._isInteger( yr ) )
		return false;
  
  	if ( ( mo < 1 ) || ( mo > 12 ) )
		return false;
  
  	if ( ( da < 1 ) || ( mo > Date.getMonthLength( mo - 1, yr ) ) )
		return false;
  
  	mo = parseInt( mo, 10 ) - 1;
 	da = parseInt( da, 10 );
  	yr = parseInt( yr, 10 );
  
  	return new Date( yr, mo, da );
};

/**
 * Returns day of week the first day of the month falls on.
 *
 * @access public
 * @static
 */
Date.getStartDay = function( m, y )
{
  	var tempdate = new Date();
  	tempdate.setDate( 1 );
  	tempdate.setMonth( m );
  	tempdate.setFullYear( y );
  
  	return tempdate.getDay(); // 0 to 6
};

/**
 * @access public
 * @static
 */
Date.createDate = function( m, d, y )
{
  	return Date.formatDate( new Date( parseInt( y,10 ), parseInt( m, 10 ), parseInt( d, 10 ) ) );
};

/**
 * @access public
 * @static
 */
Date.daysSince = function( d1, d2 )
{
  	return Math.abs( Math.floor( ( d1 - d2 ) / 86400000 ) );
};


// private methods

/**
 * @access private
 * @static
 */
Date._padZeros = function( s, n )
{
  	s = String( s );
  
  	while ( s.length < n )
		s = "0" + s;
		
  	return s;
};

/**
 * @access private
 * @static
 */
Date._zeroPrefix = function( i, len )
{
	var s = i.toString();
	
	while ( s.length < len )
		s = "0" + s;
		
	return s;
};

/**
 * @access private
 * @static
 */
Date._rightStr = function( s, n )
{
  	s = String( s );
  	return s.substr( s.length - n, n );
};

/**
 * @access private
 * @static
 */
Date._isInteger = function( s )
{
	return s == parseInt( s, 10 );
};
