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
 * @package html_form
 */
 
/**
 * Constructor
 *
 * @access public
 */
SelectUtil = function()
{
	this.Base = Base;
	this.Base();
};


SelectUtil.prototype = new Base();
SelectUtil.prototype.constructor = SelectUtil;
SelectUtil.superclass = Base.prototype;

/**
 * @access public
 */
SelectUtil.prototype.selectUnselectMatchingOptions = function( obj, regex, which, only )
{
	if ( window.RegExp )
	{
		if ( which == "select" )
		{
			var selected1 = true;
			var selected2 = false;
		}
		else if ( which == "unselect" )
		{
			var selected1 = false;
			var selected2 = true;
		}
		else
		{
			return;
		}
		
		var re = new RegExp( regex );
		
		for ( var i = 0; i < obj.options.length; i++ )
		{
			if ( re.test( obj.options[i].text ) )
			{
				obj.options[i].selected = selected1;
			}
			else
			{
				if ( only == true )
					obj.options[i].selected = selected2;
			}
		}
	}
};

/**
 * This function selects all options that match the regular expression
 * passed in. Currently-selected options will not be changed.
 *
 * @access public
 */
SelectUtil.prototype.selectMatchingOptions = function( obj, regex )
{
	this.selectUnselectMatchingOptions( obj, regex, "select", false );
};

/**
 * This function selects all options that match the regular expression
 * passed in. Selected options that don't match will be un-selected.
 *
 * @access public
 */
SelectUtil.prototype.selectOnlyMatchingOptions = function( obj, regex )
{
	this.selectUnselectMatchingOptions( obj, regex, "select", true );
};

/**
 * This function Unselects all options that match the regular expression
 * passed in. 
 *
 * @access public
 */
SelectUtil.prototype.unSelectMatchingOptions = function( obj, regex )
{
	this.selectUnselectMatchingOptions( obj, regex, "unselect", false );
};

/**
 * Pass this function a SELECT object and the options will be sorted
 * by their text (display) values.
 *
 * @access public
 */
SelectUtil.prototype.sortSelect = function( obj )
{
	var o = new Array();
	
	for ( var i = 0; i < obj.options.length; i++ )
		o[o.length] = new Option( obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected );
	
	o = o.sort( 
		function( a, b )
		{ 
			if ( ( a.text + "" ) < ( b.text + "" ) )
				return -1;
				
			if ( ( a.text + "" ) > ( b.text + "" ) )
				return 1;
				
			return 0;
		} 
	);

	for ( var i = 0; i < o.length; i++ )
		obj.options[i] = new Option( o[i].text, o[i].value, o[i].defaultSelected, o[i].selected );
};

/**
 * This function takes a select box and selects all options (in a 
 * multiple select object). This is used when passing values between
 * two select boxes. Select all options in the right box before 
 * submitting the form so the values will be sent to the server.
 *
 * @access public
 */
SelectUtil.prototype.selectAllOptions = function( obj )
{
	for ( var i = 0; i < obj.options.length; i++ )
		obj.options[i].selected = true;
};

/**
 * This function moves options between select boxes. Works best with
 * multi-select boxes to create the common Windows control effect.
 * Passes all selected values from the first object to the second
 * object and re-sorts each box.
 *
 * If a third argument of 'false' is passed, then the lists are not
 * sorted after the move.
 *
 * If a fourth string argument is passed, this will function as a
 * Regular Expression to match against the TEXT or the options. If 
 * the text of an option matches the pattern, it will NOT be moved.
 * It will be treated as an unmoveable option.
 * You can also put this into the <SELECT> object as follows:
 *    onDblClick="moveSelectedOptions(this,this.form.target)
 * This way, when the user double-clicks on a value in one box, it
 * will be transferred to the other (in browsers that support the 
 * onDblClick() event handler).
 *
 * @access public
 */
SelectUtil.prototype.moveSelectedOptions = function( from, to )
{
	// Unselect matching options, if required.
	if ( arguments.length > 3 )
	{
		var regex = arguments[3];
		
		if ( regex != "" )
			this.unSelectMatchingOptions( from, regex );
	}
	
	for ( var i = 0; i < from.options.length; i++ )
	{
		var o = from.options[i];
		
		if ( o.selected )
			to.options[to.options.length] = new Option( o.text, o.value, false, false );
	}
	
	// Delete them from original.
	for ( var i = ( from.options.length - 1 ); i >= 0; i-- )
	{
		var o = from.options[i];
		
		if ( o.selected )
			from.options[i] = null;
	}
	
	if ( ( arguments.length < 3 ) || ( arguments[2] == true ) )
	{
		this.sortSelect( from );
		this.sortSelect( to );
	}
	
	from.selectedIndex = -1;
	to.selectedIndex   = -1;
};

/**
 * This function copies options between select boxes instead of 
 * moving items. Duplicates in the target list are not allowed.
 *
 * @access public
 */
SelectUtil.prototype.copySelectedOptions = function( from, to )
{
	var options = new Object();
	
	for ( var i = 0; i < to.options.length; i++ )
		options[to.options[i].text] = true;
	
	for ( var i = 0; i < from.options.length; i++ )
	{
		var o = from.options[i];
		
		if ( o.selected )
		{
			if ( options[o.text] == null || options[o.text] == "undefined" )
				to.options[to.options.length] = new Option( o.text, o.value, false, false );
		}
	}
	
	if ( ( arguments.length < 3 ) || ( arguments[2] == true ) )
		this.sortSelect( to );
	
	from.selectedIndex = -1;
	to.selectedIndex   = -1;
};

/**
 * Move all options from one select box to another.
 *
 * @access public
 */
SelectUtil.prototype.moveAllOptions = function( from, to )
{
	this.selectAllOptions( from );
	
	if ( arguments.length == 2 )
		this.moveSelectedOptions( from, to );
	else if ( arguments.length == 3 )
		this.moveSelectedOptions( from, to, arguments[2] );
	else if ( arguments.length == 4 )
		this.moveSelectedOptions( from, to, arguments[2], arguments[3] );
};

/**
 * Copy all options from one select box to another, instead of
 * removing items. Duplicates in the target list are not allowed.
 *
 * @access public
 */
SelectUtil.prototype.copyAllOptions = function( from, to )
{
	this.selectAllOptions( from );
	
	if ( arguments.length == 2 )
		this.copySelectedOptions( from, to );
	else if ( arguments.length == 3 )
		this.copySelectedOptions( from, to, arguments[2] );
};

/**
 * Swap positions of two options in a select list.
 *
 * @access public
 */
SelectUtil.prototype.swapOptions = function( obj, i, j )
{
	var o = obj.options;
	var i_selected = o[i].selected;
	var j_selected = o[j].selected;
	var temp  = new Option( o[i].text, o[i].value, o[i].defaultSelected, o[i].selected );
	var temp2 = new Option( o[j].text, o[j].value, o[j].defaultSelected, o[j].selected );

	o[i] = temp2;
	o[j] = temp;
	o[i].selected = j_selected;
	o[j].selected = i_selected;
};

/**
 * Move selected option in a select list up one.
 *
 * @access public
 */
/*
SelectUtil.prototype.moveOptionUp = function( obj )
{
	// If > 1 option selected, do nothing.
	var selectedCount = 0;
	
	for ( i = 0; i < obj.options.length; i++ )
	{
		if ( obj.options[i].selected )
			selectedCount++;
	}
	
	if ( selectedCount > 1 )
		return;
	
	// If this is the first item in the list, do nothing.
	var i = obj.selectedIndex;
	
	if ( i == 0 )
		return;
	
	this.swapOptions( obj, i, i - 1 );
	obj.options[i-1].selected = true;
};
*/

/**
 * Move selected option in a select list down one.
 *
 * @access public
 */
/*
SelectUtil.prototype.moveOptionDown = function( obj )
{
	// If > 1 option selected, do nothing.
	var selectedCount = 0;
	
	for ( i = 0; i < obj.options.length; i++ )
	{
		if ( obj.options[i].selected )
			selectedCount++;
	}
	
	if ( selectedCount > 1 )
		return;
	
	// If this is the last item in the list, do nothing.
	var i = obj.selectedIndex;
	
	if ( i == ( obj.options.length - 1 ) )
		return;
	
	this.swapOptions( obj, i, i + 1 );
	obj.options[i+1].selected = true;
};
*/
