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
 * @package gui_calendar
 */
 
/**
 * Constructor
 *
 * @access public
 */
Calendar = function()
{
	this.Base = Base;
	this.Base();
};


Calendar.prototype = new Base();
Calendar.prototype.constructor = Calendar;
Calendar.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Calendar.ppcDF = "m/d/Y";

/**
 * @access public
 * @static
 */
Calendar.ppcTT = "<table width=\"200\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\" bordercolorlight=\"#000000\" bordercolordark=\"#000000\">\n";

/**
 * @access public
 * @static
 */
Calendar.ppcCD = Calendar.ppcTT;

/**
 * @access public
 * @static
 */
Calendar.ppcFT = "<font face=\"MS Sans Serif, sans-serif\" size=\"1\" color=\"#000000\">";

/**
 * @access public
 * @static
 */
Calendar.ppcFC = true;

/**
 * @access public
 * @static
 */
Calendar.ppcTI = false;

/**
 * @access public
 * @static
 */
Calendar.ppcSV = null;

/**
 * @access public
 * @static
 */
Calendar.ppcRL = null;

/**
 * @access public
 * @static
 */
Calendar.ppcNow = new Date();

/**
 * @access public
 * @static
 */
Calendar.ppcPtr = new Date();

/**
 * @access public
 * @static
 */
Calendar.ppcML = new Array( 
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
	31 
);

/**
 * @access public
 * @static
 */
Calendar.ppcMN = new Array(
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
	"December"
);

/**
 * @access public
 * @static
 */
Calendar.ppcWN = new Array(
	"Sunday", 
	"Monday",
	"Tuesday",
	"Wednesday",
	"Thursday",
	"Friday",
	"Saturday"
);


/**
 * @access public
 * @static
 */
Calendar.getCalendarFor = function( target, rules )
{
	Calendar.ppcSV = target;
	Calendar.ppcRL = rules;

	if ( Calendar.ppcFC )
	{
		Calendar.setCalendar();
		Calendar.ppcFC = false;
	}

	if ( ( Calendar.ppcSV != null ) && Calendar.ppcSV )
	{
		var obj = document.all['PopUpCalendar'];
		obj.style.left = document.body.scrollLeft + event.clientX;
		obj.style.top  = document.body.scrollTop  + event.clientY;
		obj.style.visibility = "visible";
	}
 	else
	{
		return Base.raiseError( "Target form field does not exist or is not accessible." );
	}
};

/**
 * @access public
 * @static
 */
Calendar.switchMonth = function( param )
{
	var tmp = param.split( "|" );
	Calendar.setCalendar( tmp[0], tmp[1] );
};

/**
 * @access public
 * @static
 */
Calendar.moveMonth = function( dir )
{
	var obj = null;
	var limit = false;
	var tmp, dptrYear, dptrMonth;
	
	obj = document.ppcMonthList.sItem;
	
	if ( obj != null )
	{
  		if ( ( dir.toLowerCase() == "back" ) && ( obj.selectedIndex > 0 ) )
			obj.selectedIndex--;
  		else if ( ( dir.toLowerCase() == "forward" ) && ( obj.selectedIndex < 12 ) )
			obj.selectedIndex++;
  		else
			limit = true;
	}
	
 	if ( !limit )
	{
  		tmp = obj.options[obj.selectedIndex].value.split( "|" );
  		dptrYear  = tmp[0];
  		dptrMonth = tmp[1];
  		Calendar.setCalendar( dptrYear, dptrMonth );
	}
 	else
	{
		obj.style.backgroundColor = "#FF0000";
		window.setTimeout( "document.ppcMonthList.sItem.style.backgroundColor = '#FFFFFF'", 50 );
	}
};

/**
 * @access public
 * @static
 */
Calendar.selectDate = function( param )
{
	var arr   = param.split( "|" );
	var year  = arr[0];
	var month = arr[1];
	var date  = arr[2];
	var ptr = parseInt( date );
	Calendar.ppcPtr.setDate( ptr );
	
	if ( ( Calendar.ppcSV != null ) && Calendar.ppcSV )
	{
  		if ( Calendar.validDate( date ) )
		{
			Calendar.ppcSV.value = Calendar.dateFormat( year, month, date );
			Calendar.hideCalendar();
		}
  		else
		{
			Base.raiseError( "Invalid date." );
			
			if ( Calendar.ppcTI )
			{
				clearTimeout( Calendar.ppcTI );
				Calendar.ppcTI = false;
			}
		}
	}
 	else
	{
  		Calendar.hideCalendar();
		return Base.raiseError( "Target form field does not exist or is not accessible." );
	}
};

/**
 * @access public
 * @static
 */
Calendar.setCalendar = function( year, month )
{
	if ( year  == null )
		year = Calendar.getFullYear( Calendar.ppcNow );
		
 	if ( month == null )
	{
		month = Calendar.ppcNow.getMonth();
		Calendar.setSelectList( year, month );
	}
 
 	if ( month == 1 )
		Calendar.ppcML[1] = ( Calendar.isLeap( year ) ) ? 29 : 28;

	Calendar.ppcPtr.setYear( year );
	Calendar.ppcPtr.setMonth( month );
	Calendar.ppcPtr.setDate( 1 );
	Calendar.updateContent();
};

/**
 * @access public
 * @static
 */
Calendar.updateContent = function()
{
	Calendar.generateContent();
	document.all['monthDays'].innerHTML = Calendar.ppcCD;
 	Calendar.ppcCD = Calendar.ppcTT;
};

/**
 * @access public
 * @static
 */
Calendar.generateContent = function()
{
	var year  = Calendar.getFullYear( Calendar.ppcPtr );
	var month = Calendar.ppcPtr.getMonth();
	var date  = 1;
	var day   = Calendar.ppcPtr.getDay();
	var len   = Calendar.ppcML[month];
	
	var bgr, cnt, tmp = "";
	var j, i = 0;
	
	for ( j = 0; j < 7; ++j )
	{
  		if ( date > len )
			break;
			
  		for ( i = 0; i < 7; ++i )
		{
   			bgr = ( ( i == 0 ) || ( i == 6 ) )? "#FFFFCC" : "#FFFFFF";
   
   			if ( ( ( j == 0 ) && ( i < day ) ) || ( date > len ) )
			{
				tmp += Calendar.makeCell( bgr, year, month, 0 );
			}
			else
			{
				tmp += Calendar.makeCell( bgr, year, month, date );
				++date;
			}
		}

		Calendar.ppcCD += "<tr align=\"center\">\n" + tmp + "</tr>\n";
		tmp = "";
	}

	Calendar.ppcCD += "</table>\n";
};

/**
 * @access public
 * @static
 */
Calendar.makeCell = function( bgr, year, month, date )
{
	var param     = "\'" + year + "|" + month + "|" + date + "\'";
	var td1       = "<td width=\"20\" bgcolor=\"" + bgr + "\" ";
	var td2       = "</font></span></td>\n";
	var evt       = "onMouseOver=\"this.style.backgroundColor=\'#FF0000\'\" onMouseOut=\"this.style.backgroundColor=\'" + bgr + "\'\" onMouseUp=\"Calendar.selectDate(" + param + ")\" ";
	var ext       = "<span Style=\"cursor: hand\">";
	var lck       = "<span Style=\"cursor: default\">";
	var lnk       = "<a href=\"javascript:Calendar.selectDate(" + param + ")\" onMouseOver=\"window.status=\' \';return true;\">";
	var cellValue = ( date != 0 )? date + "" : "&nbsp;";

	if ( ( Calendar.ppcNow.getDate() == date ) && ( Calendar.ppcNow.getMonth() == month ) && ( Calendar.getFullYear( Calendar.ppcNow ) == year ) )
		cellValue = "<b>" + cellValue + "</b>";
	
	var cellCode = "";

	if ( date == 0 )
		cellCode = td1 + "Style=\"cursor: default\">" + lck + Calendar.ppcFT + cellValue + td2;
 	else
		cellCode = td1 + evt + "Style=\"cursor: hand\">" + ext + Calendar.ppcFT + cellValue + td2;

	return cellCode;
};

/**
 * @access public
 * @static
 */
Calendar.setSelectList = function( year, month )
{
	var i = 0;
	var obj = null;

	obj = document.ppcMonthList.sItem;

	while ( i < 13 )
	{
		obj.options[i].value = year + "|"   + month;
		obj.options[i].text  = year + " • " + Calendar.ppcMN[month];
		i++;
		month++;
		
		if ( month == 12 )
		{
			year++;
			month = 0;
		}
	}
};

/**
 * @access public
 * @static
 */
Calendar.hideCalendar = function()
{
	document.all['PopUpCalendar'].style.visibility = "hidden";
	Calendar.ppcTI = false;
	Calendar.setCalendar();
	Calendar.ppcSV = null;
	
	var obj = document.ppcMonthList.sItem;
	obj.selectedIndex = 0;
};

/**
 * @access public
 * @static
 */
Calendar.isLeap = function( year )
{
	if ( ( year % 400 == 0 ) || ( ( year % 4 == 0 ) && ( year % 100 != 0 ) ) )
		return true;
 	else
		return false;
};

/**
 * @access public
 * @static
 */
Calendar.getFullYear = function( obj )
{
	return obj.getYear();
};

/**
 * @access public
 * @static
 */
Calendar.validDate = function( date )
{
	var reply = true;

	if ( Calendar.ppcRL == null )
	{
	}
 	else
	{
		var arr  = Calendar.ppcRL.split( ":" );
		var mode = arr[0];
		var arg  = arr[1];
		var key  = arr[2].charAt(0).toLowerCase();
		
		if ( key != "d" )
		{
			var day = Calendar.ppcPtr.getDay();
			var orn = Calendar.isEvenOrOdd( date );

			reply = ( mode == "[^]" )? ! ( ( day == arg ) && ( ( orn == key ) || ( key == "a" ) ) ) : ( ( day == arg ) && ( ( orn == key ) || ( key == "a" ) ) );
		}
  		else
		{
			reply = ( mode == "[^]" )? ( date != arg ) : ( date == arg );
		}
	}

	return reply;
};

/**
 * @access public
 * @static
 */
Calendar.isEvenOrOdd = function( date )
{
	if ( date - 21 > 0 )
		return "e";
	else if ( date - 14 > 0 )
		return "o";
	else if ( date - 7 > 0 )
		return "e";
	else
		return "o";
};

/**
 * @access public
 * @static
 */
Calendar.dateFormat = function( year, month, date )
{
	if ( Calendar.ppcDF == null )
		Calendar.ppcDF = "m/d/Y";
		
	var day   = Calendar.ppcPtr.getDay();
	var crt   = "";
	var str   = "";
	var chars = Calendar.ppcDF.length;

	for ( var i = 0; i < chars; ++i )
	{
  		crt = Calendar.ppcDF.charAt(i);

		switch ( crt )
		{
			case "M":
				str += Calendar.ppcMN[month];
				break;
				
			case "m":
				str += ( month < 9 )? ( "0" + ( ++month ) ) : ++month;
				break;
				
			case "Y":
				str += year;
				break;
				
			case "y":
				str += year.substring( 2 );
				break;
				
			case "d":
				str += ( ( Calendar.ppcDF.indexOf( "m" ) != -1 ) && ( date < 10 ) )? ( "0" + date ) : date;
				break;
			
			case "W":
				str += Calendar.ppcWN[day];
				break;
			
			default:
				str += crt;
		}
	}
	
	return unescape( str );
};
