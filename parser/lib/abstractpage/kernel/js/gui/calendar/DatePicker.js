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
 * @param  Date  oDate  Optional argument representing the date to select
 * @access public
 */
DatePicker = function( oDate, displayFormat )
{
	this.Base = Base;
	this.Base();
	
	// check arguments
	if ( arguments.length == 0 )
	{
		this._selectedDate = new Date;
		this._none = false;
	}
	else
	{
		this._selectedDate = oDate || new Date();
		this._none = oDate == null;
	}

	this.displayFormat   = displayFormat;
	
	this._matrix         = [[],[],[],[],[],[],[]];
	this._showNone       = true;
	this._showToday      = true;
	this._firstWeekDay   = 0;		// start week with monday according to standards
	this._redWeekDay     = 6;		// sunday is the default red day.
	this._dontChangeNone = false;
	
	// event callbacks
	this.onchange = new Function;
	this.onbeforeallowed = new Function;
	this.onafterallowed  = new Function;
};


DatePicker.prototype = new Base();
DatePicker.prototype.constructor = DatePicker;
DatePicker.superclass = Base.prototype;

/**
 * Create the nodes inside the date picker.
 *
 * @access public
 */
DatePicker.prototype.create = function( doc, hideFooter )
{
	if ( doc == null )
		doc = document;

	// create elements
	this._el = doc.createElement( "div" );
	this._el.className = "datePicker";
	
	// header
	var div = doc.createElement( "div" );
	div.className = "header";
	this._el.appendChild( div );
	
	var headerTable = doc.createElement( "table" );
	headerTable.className = "headerTable";
	headerTable.cellSpacing = 0;
	div.appendChild( headerTable );
	
	var tBody = doc.createElement( "tbody" );
	headerTable.appendChild( tBody );
	
	var tr = doc.createElement( "tr" );
	tBody.appendChild( tr );
	
	var td = doc.createElement( "td" );
	this._previousMonth = doc.createElement( "button" );
	this._previousMonth.className = "previousButton";
	td.appendChild( this._previousMonth );
	tr.appendChild( td );
	
	this._topLabel = doc.createElement( "td" );
	this._topLabel.className = "topLabel";
	this._topLabel.appendChild( doc.createTextNode( String.fromCharCode( 160 ) ) );
	tr.appendChild( this._topLabel );
	
	td = doc.createElement( "td" );
	this._nextMonth = doc.createElement( "button" );
	this._nextMonth.className = "nextButton";
	td.appendChild( this._nextMonth );
	tr.appendChild( td );
	
	// grid
	div = doc.createElement( "div" );
	div.className = "grid";
	this._el.appendChild( div );
	this._table = div;
	
	if ( hideFooter )
	{
		// footer
		div = doc.createElement( "div" );
		div.className = "footer";
		this._el.appendChild( div );
	
		var footerTable = doc.createElement( "table" );
		footerTable.className = "footerTable";
		footerTable.cellSpacing = 0;
		div.appendChild( footerTable );
	
		tBody = doc.createElement( "tbody" );
		footerTable.appendChild( tBody );
	
		tr = doc.createElement( "tr" );
		tBody.appendChild( tr );
	
		td = doc.createElement( "td" );
		this._todayButton = doc.createElement( "button" );
		this._todayButton.className = "todayButton";
		this._todayButton.appendChild( doc.createTextNode( "Today" ) );
		td.appendChild( this._todayButton );
		tr.appendChild( td );
	
		td = doc.createElement( "td" );
		td.className = "filler";
		td.appendChild( doc.createTextNode( String.fromCharCode( 160 ) ) );
		tr.appendChild( td );
	
		td = doc.createElement( "td" );
		this._noneButton = doc.createElement( "button" );
		this._noneButton.className = "noneButton";
		this._noneButton.appendChild( doc.createTextNode( "None" ) );
		td.appendChild( this._noneButton );
		tr.appendChild( td );
	}
		
	this._createTable( doc );
	
	this._updateTable();
	this._setTopLabel();

	if ( !this._showNone )
		this._noneButton.style.visibility  = "hidden";
	if ( !this._showToday )
		this._todayButton.style.visibility = "hidden";

	// IE55+ extension		
	this._previousMonth.hideFocus = true;
	this._nextMonth.hideFocus     = true;
	
	if ( hideFooter )
	{
		this._todayButton.hideFocus = true;
		this._noneButton.hideFocus  = true;
	}
		
	// hook up events
	var dp = this;
	
	// buttons
	this._previousMonth.onclick = function()
	{
		dp._dontChangeNone = true;
		dp.goToPreviousMonth();
		dp._dontChangeNone = false;
	};
	this._nextMonth.onclick = function()
	{
		dp._dontChangeNone = true;
		dp.goToNextMonth();
		dp._dontChangeNone = false;
	};
	
	if ( hideFooter )
	{
		this._todayButton.onclick = function()
		{
			dp.goToToday();
		};
		this._noneButton.onclick = function()
		{
			dp.setDate( null );
		};
	}
	
	this._el.onselectstart = function()
	{
		return false;
	};
	this._table.onclick = function( e )
	{
		// find event
		if ( e == null )
			e = window.event;
		
		// find td
		var el = e.target != null ? e.target : e.srcElement;
		
		while ( el.nodeType != 1 )
			el = el.parentNode;
		
		while ( el != null && el.tagName.toLowerCase() != "td" )
			el = el.parentNode;
		
		// if no td found, return
		if ( el == null || el.tagName.toLowerCase() != "td" )
			return;
		
		var d = new Date( dp._selectedDate );
		var n = Number( el.firstChild.data );
		
		if ( isNaN( n ) || n <= 0 || n == null )
			return;
			
		d.setDate( n );
		dp.setDate( d );
	};
	this._el.onkeydown = function( e )
	{
		if ( e == null )
			e = doc.parentWindow.event;
		
		var kc = ( e.keyCode != null )? e.keyCode : e.charCode;
		
		if ( kc < 37 || kc > 40 )
			return true;
		
		var d = new Date( dp._selectedDate ).valueOf();
		
		if ( kc == 37 )			// left
			d -= 24 * 60 * 60 * 1000;
		else if ( kc == 39 )	// right
			d += 24 * 60 * 60 * 1000;
		else if ( kc == 38 )	// up
			d -= 7 * 24 * 60 * 60 * 1000;
		else if ( kc == 40 )	// down
			d += 7 * 24 * 60 * 60 * 1000;

		dp.setDate( new Date( d ) );
		return false;
	};
	// ie6 extension
	this._el.onmousewheel = function( e )
	{
		if ( e == null )
			e = doc.parentWindow.event;
		
		var n = - e.wheelDelta / 120;
		var d = new Date( dp._selectedDate );
		var m = d.getMonth() + n;
		
		d.setMonth( m );
		
		dp._dontChangeNone = true;
		dp.setDate( d );
		dp._dontChangeNone = false;
		
		return false;
	};
	
	return this._el;
};

/**
 * @access public
 */
DatePicker.prototype.paint = function( ele )
{
	if ( ele) 
		ele.appendChild( this.create() );
	else
		document.body.appendChild( this.create() );
};

/**
 * @access public
 */
DatePicker.prototype.setDate = function( oDate )
{
	// if null then set None
	if ( oDate == null )
	{
		if ( !this._none )
		{
			this._none = true;
			this._setTopLabel();
			this._updateTable();
			
			if ( typeof this.onchange == "function" )
				this.onchange();	
		}
		
		return;
	}

	// if string or number create a Date object
	if ( typeof oDate == "string" || typeof oDate == "number" )
	{
		oDate = new Date( oDate );
	}
	
	// do not update if not really changed
	if ( this._selectedDate.getDate()     != oDate.getDate()     ||
		 this._selectedDate.getMonth()    != oDate.getMonth()    ||
		 this._selectedDate.getFullYear() != oDate.getFullYear() ||
		 this._none )
	{
		if ( !this._dontChangeNone )
			this._none = false;
		
		this._selectedDate = new Date( oDate );
	
		this._setTopLabel();
		this._updateTable();
		
		if ( typeof this.onchange == "function" )
			this.onchange();
	}
	
	if ( !this._dontChangeNone )
		this._none = false;
};

/**
 * @access public
 */
DatePicker.prototype.getDate = function()
{
	if ( this._none )
		return null;
	
	return new Date( this._selectedDate );	// create a new instance
};

/**
 * @access public
 */
DatePicker.prototype.goToNextMonth = function()
{
	var d = new Date( this._selectedDate );
	d.setMonth( d.getMonth() + 1 );
	this.setDate( d );
};

/**
 * @access public
 */
DatePicker.prototype.goToPreviousMonth = function()
{
	var d = new Date( this._selectedDate );
	d.setMonth( d.getMonth() - 1 );
	this.setDate( d );
};

/**
 * @access public
 */
DatePicker.prototype.goToToday = function()
{
	if ( this._none )
	{
		// change the selectedDate to force update if none was true
		this._selectedDate = new Date( this._selectedDate + 10000000000 );
	}
	
	this._none = false;
	this.setDate( new Date() );
};

/**
 * @access public
 */
DatePicker.prototype.setShowToday = function( bShowToday )
{
	if ( typeof bShowToday == "string" )
		bShowToday = !/false|0|no/i.test( bShowToday );
		
	if ( this._todayButton != null )
		this._todayButton.style.visibility = bShowToday ? "visible" : "hidden";

	this._showToday = bShowToday;
};

/**
 * @access public
 */
DatePicker.prototype.getShowToday = function()
{
	return this._showToday;
};

/**
 * @access public
 */
DatePicker.prototype.setShowNone = function( bShowNone )
{
	if ( typeof bShowNone == "string" )
		bShowNone = !/false|0|no/i.test( bShowNone );

	if ( this._noneButton != null )
		this._noneButton.style.visibility = bShowNone ? "visible" : "hidden";
	
	this._showNone = bShowNone;
};

/**
 * @access public
 */
DatePicker.prototype.getShowNone = function()
{
	return this._showNone;
};

/**
 * Note: 0 is monday and 6 is sunday as in the ISO standard
 *
 * @access public
 */
DatePicker.prototype.setFirstWeekDay = function( nFirstWeekDay )
{
	if ( this._firstWeekDay != nFirstWeekDay )
	{
		this._firstWeekDay = nFirstWeekDay;
		this._updateTable();
	}
};

/**
 * @access public
 */
DatePicker.prototype.getFirstWeekDay = function()
{
	return this._firstWeekDay;
};

/**
 * Note: 0 is monday and 6 is sunday as in the ISO standard
 *
 * @access public
 */
DatePicker.prototype.setRedWeekDay = function ( nRedWeekDay )
{
	if ( this._redWeekDay != nRedWeekDay )
	{
		this._redWeekDay = nRedWeekDay;
		this._updateTable();
	}
};

/**
 * @access public
 */
DatePicker.prototype.getRedWeekDay = function ()
{
	return this._redWeekDay;
};


// private methods

/**
 * Creates the table elements and inserts them into the date picker.
 *
 * @access private
 */
DatePicker.prototype._createTable = function( doc )
{
	var str, i;
	var rows = 6;
	var cols = 7;
	var currentWeek = 0;

	var table = doc.createElement( "table" );
	table.className = "gridTable";
	table.cellSpacing = 0;
	
	var tBody = doc.createElement( "tbody" );
	table.appendChild( tBody );
	
	// days row
	var tr = doc.createElement( "tr" );
	tr.className = "daysRow";

	var td, tn;
	var nbsp = String.fromCharCode( 160 );

	for ( i = 0; i < cols; i++ )
	{
		td = doc.createElement( "td" );
		td.appendChild( doc.createTextNode( nbsp ) );
		tr.appendChild( td );
	}
	
	tBody.appendChild( tr );
	
	// upper line
	tr = doc.createElement( "tr" );
	td = doc.createElement( "td" );
	
	td.className = "upperLine";
	td.colSpan   = 7;
	
	tr.appendChild( td );
	tBody.appendChild( tr );

	// rest
	for ( i = 0; i < rows; i++ )
	{
		tr = doc.createElement( "tr" );
		
		for ( var j = 0; j < cols; j++ )
		{
			td = doc.createElement( "td" );
			td.appendChild( doc.createTextNode( nbsp ) );
			tr.appendChild( td );
		}
		
		tBody.appendChild( tr );
	}
	
	str += "</table>";
	
	if ( this._table != null )
		this._table.appendChild( table )
};

/**
 * This method updates all the text nodes inside the table as well
 * as all the classNames on the tds.
 *
 * @access private
 */
DatePicker.prototype._updateTable = function ()
{
	// if no element no need to continue
	if ( this._table == null )
		return;
	
	var i;
	var str  = "";
	var rows = 6;
	var cols = 7;
	var currentWeek = 0;
		
	var cells = new Array( rows );
	this._matrix = new Array( rows );
	
	for ( i = 0; i < rows; i++ )
	{
		cells[i] = new Array( cols );
		this._matrix[i] = new Array( cols );
	}

	// Set the tmpDate to this month
	var tmpDate = new Date( this._selectedDate.getFullYear(), this._selectedDate.getMonth(), 1 );
	
	var today = new Date();
	
	// go thorugh all days this month and store the text
	// and the class name in the cells matrix
	for ( i = 1; i < 32; i++ )
	{
		tmpDate.setDate( i );
		
		// convert to ISO, Monday is 0 and 6 is Sunday
		var weekDay  = ( tmpDate.getDay() + 6 ) % 7;
		var colIndex = ( weekDay - this._firstWeekDay + 7 ) % 7;
		
		if ( tmpDate.getMonth() == this._selectedDate.getMonth() )
		{
			var isToday = tmpDate.getDate() == today.getDate() && tmpDate.getMonth() == today.getMonth() && tmpDate.getFullYear() == today.getFullYear();
			cells[currentWeek][colIndex] = { text: "", className: "" };
			
			if ( this._selectedDate.getDate() == tmpDate.getDate() && !this._none )
				cells[currentWeek][colIndex].className += "selected ";
				
			if ( isToday )
				cells[currentWeek][colIndex].className += "today ";
				
			if ( ( tmpDate.getDay() + 6 ) % 7 == this._redWeekDay ) // ISO
				cells[currentWeek][colIndex].className += "red";
			
			cells[currentWeek][colIndex].text =			
				this._matrix[currentWeek][colIndex] = tmpDate.getDate();
			
			if ( colIndex == 6 )
				currentWeek++;			
		}
	}
	
	// fix day letter order if not standard
	var weekDays = DatePicker.days;
													
	if ( this._firstWeekDay != 0 )
	{
		weekDays = new Array(7);
		
		for ( i = 0; i < 7; i++)
			weekDays[i] = DatePicker.days[ (i + this._firstWeekDay) % 7];
	}

	// update text in days row
	var tds = this._table.firstChild.tBodies[0].rows[0].cells;

	for ( i = 0; i < cols; i++ )
		tds[i].firstChild.data = weekDays[i];
		
	// update the text nodes and class names
	var trs = this._table.firstChild.tBodies[0].rows;
	var tmpCell;
	var nbsp = String.fromCharCode( 160 );

	for ( var y = 0; y < rows; y++ )
	{
		for ( var x = 0; x < cols; x++ )
		{
			tmpCell = trs[y + 2].cells[x];
			
			if ( typeof cells[y][x] != "undefined" )
			{
				tmpCell.className = cells[y][x].className;
				tmpCell.firstChild.data = cells[y][x].text;
			}
			else
			{
				tmpCell.className = "";
				tmpCell.firstChild.data = nbsp;
			}
		}
	}
};

/**
 * Sets the label showing the year and selected month.
 *
 * @access private
 */
DatePicker.prototype._setTopLabel = function()
{
	if ( this.displayFormat = "de" )
		var str = DatePicker.months[ this._selectedDate.getMonth() ] + " " + this._selectedDate.getFullYear();
	else
		var str = this._selectedDate.getFullYear() + " " + DatePicker.months[ this._selectedDate.getMonth() ];
		
	if ( this._topLabel != null )
		this._topLabel.firstChild.data = str;
};


/**
 * @access public
 * @static
 */
DatePicker.months = [
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
];

/**
 * @access public
 * @static
 */
DatePicker.days = [
	"m",
	"t",
	"w",
	"t",
	"f",
	"s",
	"s"
];
