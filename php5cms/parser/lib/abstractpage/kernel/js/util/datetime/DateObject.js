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
 * Constructor
 *
 * @access public
 */
DateObject = function()
{
	this.Base = Base;
	this.Base();
};


DateObject.prototype = new Base();
DateObject.prototype.constructor = DateObject;
DateObject.superclass = Base.prototype;

/**
 * @access public
 */
DateObject.prototype.getNiceDate = function( ticks )
{
	var str = "";
	var d = new Date( ticks * 1000 );
	
	str += DateObject.months[d.getMonth()];
	str += " "  + d.getDate();
	str += ", " + d.getFullYear();

	return str;
};

/**
 * @access public
 */
DateObject.prototype.isDate = function( val, format )
{
	var date = this.getDateFromFormat( val,format );
	
	if ( date == 0 )
		return false;
		
	return true;
};

/**
 * @access public
 */
DateObject.prototype.compareDates = function( date1, dateformat1, date2, dateformat2 )
{
	var d1 = this.getDateFromFormat( date1, dateformat1 );
	var d2 = this.getDateFromFormat( date2, dateformat2 );
	
	if ( d1 == 0 || d2 == 0 )
		return -1;
	else if ( d1 > d2 )
		return 1;
	else
		return 0;
};

/**
 * @access public
 */
DateObject.prototype.formatDate = function( date, format )
{
	format = format + "";

	var result = "";
	var i_format = 0;
	var c = "";
	var token = "";
	
	var y = date.getYear()  + "";
	var M = date.getMonth() + 1;
	var d = date.getDate();
	var H = date.getHours();
	var m = date.getMinutes();
	var s = date.getSeconds();
	var yyyy, yy, MMM, MM, dd, hh, h, mm, ss, ampm, HH, H, KK, K, kk, k;
	
	// Convert real date parts into formatted versions
	// Year
	if ( y.length < 4 )
		y = y - 0 + 1900;
	
	y    = "" + y;
	yyyy = y;
	yy   = y.substring( 2, 4 );
	
	// Month
	if ( M < 10 )
		MM = "0" + M;
	else
		MM = M;
		
	MMM = DateObject.monthNames[M - 1];
	
	// Date
	if ( d < 10 )
		dd = "0" + d;
	else
		dd = d;
		
	// Hour
	h = H + 1;
	K = H;
	k = H + 1;
	
	if ( h > 12 )
		h-=12;
		
	if ( h == 0 )
		h = 12;
		
	if ( h < 10 )
		hh = "0" + h;
	else
		hh = h;
		
	if ( H < 10 )
		HH = "0" + K;
	else
		HH = H;
		
	if ( K > 11 )
		K-=12;
		
	if ( K < 10 )
		KK = "0" + K;
	else
		KK = K;
		
	if ( k < 10 )
		kk = "0" + k;
	else
		kk = k;
		
	// AM/PM
	if ( H > 11 )
		ampm = "PM";
	else
		ampm = "AM";
		
	// Minute
	if ( m < 10 )
		mm = "0" + m;
	else
		mm = m;
		
	// Second
	if ( s < 10 )
		ss = "0" + s;
	else
		ss = s;
		
	// Now put them all into an object!
	var value = new Object();
	
	value["yyyy"] = yyyy;
	value["yy"]   = yy;
	value["y"]    = y;
	value["MMM"]  = MMM;
	value["MM"]   = MM;
	value["M"]    = M;
	value["dd"]   = dd;
	value["d"]    = d;
	value["hh"]   = hh;
	value["h"]    = h;
	value["HH"]   = HH;
	value["H"]    = H;
	value["KK"]   = KK;
	value["K"]    = K;
	value["kk"]   = kk;
	value["k"]    = k;
	value["mm"]   = mm;
	value["m"]    = m;
	value["ss"]   = ss;
	value["s"]    = s;
	value["a"]    = ampm;
	
	while ( i_format < format.length )
	{
		// Get next token from format string
		c = format.charAt( i_format );
		token = "";
		
		while ( ( format.charAt( i_format ) == c ) && ( i_format < format.length ) )
		{
			token += format.charAt( i_format );
			i_format++;
		}
		
		if ( value[token] != null )
			result = result + value[token];
		else
			result = result + token;
	}
	
	return result;
};

/**
 * @access public
 */
DateObject.prototype.getDateFromFormat = function( val, format )
{
	val = val + "";
	format = format + "";
	
	var x, y;
	var i_val    = 0;
	var i_format = 0;
	var c        = "";
	var token    = "";
	var token2   = "";
	
	var now   = new Date();
	var year  = now.getYear();
	var month = now.getMonth() + 1;
	var date  = now.getDate();
	var hh    = now.getHours();
	var mm    = now.getMinutes();
	var ss    = now.getSeconds();
	var ampm  = "";
	
	while ( i_format < format.length )
	{
		// Get next token from format string
		c = format.charAt( i_format );
		token = "";
		
		while ( ( format.charAt( i_format ) == c ) && ( i_format < format.length ) )
		{
			token += format.charAt( i_format );
			i_format++;
		}
		
		// Extract contents of value based on format token.
		if ( token == "yyyy" || token == "yy" || token == "y" )
		{
			// 4-digit year
			if ( token == "yyyy" )
			{
				x = 4;
				y = 4;
			}
			
			// 2-digit year
			if ( token == "yy" )
			{
				x = 2;
				y = 2;
			}
			
			// 2-or-4-digit year
			if ( token == "y" )
			{
				x = 2;
				y = 4;
			}
			
			year = this._getInt( val, i_val, x, y );
			
			if ( year == null )
				return 0;
				
			i_val += year.length;
			
			if ( year.length == 2 )
			{
				if ( year > 70 )
					year = 1900 + ( year - 0 );
				else
					year = 2000 + ( year - 0 );
			}
		}
		// Month name
		else if ( token == "MMM" )
		{
			month = 0;
			
			for ( var i = 0; i < DateObject.monthNames.length; i++ )
			{
				var month_name = DateObject.monthNames[i];
				
				if ( val.substring( i_val, i_val + month_name.length ).toLowerCase() == month_name.toLowerCase() )
				{
					month = i+1;
					
					if ( month > 12 )
						month -= 12;
						
					i_val += month_name.length;
					break;
				}
			}
			
			if ( month == 0 )
				return 0;
				
			if ( ( month < 1 ) || ( month > 12 ) )
				return 0;
				
			// TODO: Process Month Name
		}
		else if ( token == "MM" || token == "M" )
		{
			x     = token.length;
			y     = 2;
			month = this._getInt( val, i_val, x, y );
			
			if ( month == null )
				return 0;
				
			if ( ( month < 1 ) || ( month > 12 ) )
				return 0;
				
			i_val += month.length;
		}
		else if ( token == "dd" || token == "d" )
		{
			x    = token.length;
			y    = 2;
			date = this._getInt( val, i_val, x, y );
			
			if ( date == null )
				return 0;
				
			if ( ( date < 1) || ( date > 31 ) )
				return 0;
				
			i_val += date.length;
		}
		else if ( token == "hh" || token == "h" )
		{
			x  = token.length;
			y  = 2;
			hh = this._getInt( val, i_val, x, y );
			
			if ( hh == null )
				return 0;
				
			if ( ( hh < 1 ) || ( hh > 12 ) )
				return 0;
				
			i_val += hh.length;
			hh--;
		}
		else if ( token == "HH" || token == "H" )
		{
			x  = token.length;
			y  = 2;
			hh = this._getInt( val, i_val, x, y );
			
			if ( hh == null )
				return 0;
				
			if ( ( hh < 0 ) || ( hh > 23 ) )
				return 0;
				
			i_val += hh.length;
		}
		else if ( token == "KK" || token == "K" )
		{
			x  = token.length;
			y  = 2;
			hh = this._getInt( val, i_val, x, y );
			
			if ( hh == null )
				return 0;
				
			if ( ( hh < 0 ) || ( hh > 11 ) )
				return 0;
			i_val += hh.length;
		}
		else if ( token == "kk" || token == "k" )
		{
			x  = token.length;
			y  = 2;
			hh = this._getInt( val, i_val, x, y );
			
			if ( hh == null )
				return 0;
				
			if ( ( hh < 1 ) || ( hh > 24 ) )
				return 0;
				
			i_val += hh.length;
			h--;
		}
		else if ( token == "mm" || token == "m" )
		{
			x  = token.length;
			y  = 2;
			mm = this._getInt( val, i_val, x, y );
			
			if ( mm == null )
				return 0;
				
			if ( ( mm < 0 ) || ( mm > 59 ) )
				return 0;
				
			i_val += mm.length;
		}
		else if ( token == "ss" || token == "s" )
		{
			x  = token.length;
			y  = 2;
			ss = this._getInt( val, i_val, x, y );
			
			if ( ss == null )
				return 0;
				
			if ( ( ss < 0 ) || ( ss > 59 ) )
				return 0;
				
			i_val += ss.length;
		}
		else if ( token == "a" )
		{
			if ( val.substring( i_val, i_val + 2 ).toLowerCase() == "am" )
				ampm = "AM";
			else if ( val.substring( i_val, i_val + 2 ).toLowerCase() == "pm" )
				ampm = "PM";
			else
				return 0;
		}
		else
		{
			if ( val.substring( i_val, i_val + token.length ) != token )
				return 0;
			else
				i_val += token.length;
		}
	}
	
	// If there are any trailing characters left in the value, it doesn't match
	if ( i_val != val.length )
		return 0;
	
	// Is date valid for month?
	if ( month == 2 )
	{
		// Check for leap year
		if ( ( ( year % 4 == 0 ) && ( year % 100 != 0 ) ) || ( year % 400 == 0 ) )
		{
			if ( date > 29 )
				return false;
		}
		else
		{
			if ( date > 28 )
				return false;
		}
	}
	
	if ( ( month == 4 ) || ( month == 6 ) || ( month == 9 ) || ( month == 11 ) )
	{
		if ( date > 30 )
			return false;
	}
	
	// Correct hours value
	if ( hh < 12 && ampm == "PM" )
		hh+=12;
	else if ( hh > 11 && ampm == "AM" )
		hh-=12;
	
	var newdate = new Date( year,month - 1, date, hh, mm, ss );
	return newdate.getTime();
};


// private methods

/**
 * @access private
 */
DateObject.prototype._isInteger = function( val )
{
	var digits = "1234567890";
	
	for ( var i = 0; i < val.length; i++ )
	{
		if ( digits.indexOf( val.charAt( i ) ) == -1 )
			return false;
	}
	
	return true;
};

/**
 * @access private
 */
DateObject.prototype._getInt = function( str, i, minlength, maxlength )
{
	for ( x = maxlength; x >= minlength; x-- )
	{
		var token = str.substring( i, i + x );
		
		if ( token.length < minlength )
			return null;
		
		if ( this._isInteger( token ) ) 
			return token;		
	}
	
	return null;
};


/**
 * @access public
 * @static
 */
DateObject.monthNames = new Array(
	'January',
	'February',
	'March',
	'April',
	'May',
	'June',
	'July',
	'August',
	'September',
	'October',
	'November',
	'December',
	'Jan',
	'Feb',
	'Mar',
	'Apr',
	'May',
	'Jun',
	'Jul',
	'Aug',
	'Sep',
	'Oct',
	'Nov',
	'Dec'
);
