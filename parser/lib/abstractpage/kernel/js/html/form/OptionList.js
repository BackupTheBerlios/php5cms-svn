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
OptionList = function()
{
	this.Base = Base;
	this.Base();
	
	if ( arguments.length < 2 )
		Base.raiseError( "Not enough arguments." );
		
	this.target = arguments[0];
	this.dependencies = new Array();
	
	for ( var i = 1; i < arguments.length; i++ )
		this.dependencies[this.dependencies.length] = arguments[i];
	
	this.form            = null;
	this.dependentValues = new Object();
	this.defaultValues   = new Object();
	this.options         = new Object();
	this.delimiter       = "|";
	this.longestString   = "";
	this.numberOfOptions = 0;
};


OptionList.prototype = new Base();
OptionList.prototype.constructor = OptionList;
OptionList.superclass = Base.prototype;

/**
 * Set the delimiter to something other than | when defining condition values.
 *
 * @access public
 */
OptionList.prototype.setDelimiter = function( val )
{
	this.delimiter = val;
};

/**
 * Set the default option to be selected when the list is painted.
 *
 * @access public
 */
OptionList.prototype.setDefaultOption = function( condition, val )
{
	this.defaultValues[condition] = val;
};

/**
 * Init call to map the form to the object and populate it.
 *
 * @access public
 */
OptionList.prototype.init = function( theform )
{
	this.form = theform;
	this.populate();
};

/**
 * Add options to the list.
 * Pass the condition string, then the list of text/value pairs that populate the list.
 *
 * @access public
 */
OptionList.prototype.addOptions = function( dependentValue )
{
	if ( typeof this.options[dependentValue] != "object" )
		this.options[dependentValue] = new Array();
		
	for ( var i = 1; i < arguments.length; i += 2 )
	{
		// Keep track of the longest potential string, to draw the option list.
		if ( arguments[i].length > this.longestString.length )
			this.longestString = arguments[i];
		
		this.numberOfOptions++;
		
		this.options[dependentValue][this.options[dependentValue].length] = arguments[i];
		this.options[dependentValue][this.options[dependentValue].length] = arguments[i+1];
	}
};

/**
 * Populate the list.
 *
 * @access public
 */
OptionList.prototype.populate = function()
{
	var theform = this.form;
	
	var i,j,obj,obj2;
	// Get the current value(s) of all select lists this list depends on.
	this.dependentValues = new Object;
	var dependentValuesInitialized = false;
	
	for ( i = 0; i < this.dependencies.length; i++ )
	{
		var sel = theform[this.dependencies[i]];
		var selName = sel.name;
		
		// If this is the first dependent list, just fill in the dependentValues.
		if ( !dependentValuesInitialized )
		{
			dependentValuesInitialized = true;
			
			for ( j = 0; j < sel.options.length; j++ )
			{
				if ( sel.options[j].selected )
					this.dependentValues[sel.options[j].value] = true;
			}
		}
		// Otherwise, add new options for every existing option
		else
		{
			var tmpList = new Object();
			var newList = new Object();
			
			for ( j = 0; j < sel.options.length; j++ )
			{
				if ( sel.options[j].selected )
					tmpList[sel.options[j].value] = true;
				
			}
			
			for ( obj in this.dependentValues )
			{
				for ( obj2 in tmpList )
					newList[obj + this.delimiter + obj2] = true;
			}
			
			this.dependentValues = newList;
		}
	}

	var targetSel = theform[this.target];
		
	// Store the currently-selected values of the target list to maintain them (in case of multiple select lists)
	var targetSelected = new Object();
	
	for ( i = 0; i < targetSel.options.length; i++ )
	{
		if ( targetSel.options[i].selected )
			targetSelected[targetSel.options[i].value] = true;
	}

	// Clear all target options.
	targetSel.options.length = 0;
		
	for ( i in this.dependentValues )
	{
		if ( typeof this.options[i] == "object" )
		{
			var o = this.options[i];
			
			for ( j = 0; j < o.length; j += 2 )
			{
				var text = o[j];
				var val  = o[j+1];
				
				targetSel.options[targetSel.options.length] = new Option( text, val, false, false );
				
				if ( this.defaultValues[i] == val )
					targetSelected[val] = true;
			}
		}
	}
	
	targetSel.selectedIndex = -1;
	
	// Select the options that were selected before.
	for ( i = 0; i < targetSel.options.length; i++ )
	{
		if ( targetSelected[targetSel.options[i].value] != null && targetSelected[targetSel.options[i].value] == true )
			targetSel.options[i].selected = true;
	}
};
