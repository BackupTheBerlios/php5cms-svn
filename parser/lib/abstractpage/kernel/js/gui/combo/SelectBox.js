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
 * @package gui_combo
 */
 
/**
 * Constructor
 *
 * @access public
 */
SelectBox = function()
{
	this.Base = Base;
	this.Base();
};


SelectBox.prototype = new Base();
SelectBox.prototype.constructor = SelectBox;
SelectBox.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
SelectBox.overOptionCss = "background: buttonhighlight; color: black; border: 1 inset window; padding-left: 2; padding-right: 0; padding-bottom: 0; padding-top: 2";

/**
 * @access public
 * @static
 */
SelectBox.sizedBorderCss = "2 outset buttonhighlight";

/**
 * @access public
 * @static
 */
SelectBox.globalSelect = null;

/**
 * @access public
 * @static
 */
SelectBox.initSelectBox = function( el )
{
	SelectBox.copySelected( el );
	var size = el.getAttribute( "size" );

	el.options = el.children[1].children;
	el.selectedIndex = SelectBox.findSelected( el );
	el.remove = new Function( "i", "SelectBox.int_remove(this,i)" );
	el.item   = new Function( "i", "return this.options[i]" );
	el.add    = new Function( "e", "i", "SelectBox.int_add(this, e, i)" );
	el.options[el.selectedIndex].selected = true;

	dropdown = el.children[1];

	if ( size != null )
	{
		if ( size > 1 )
		{
			el.size = size;
			dropdown.style.zIndex = 0;
			SelectBox.initSized( el );
		}
		else
		{
			el.size = 1;
			dropdown.style.zIndex = 99;
			
			if ( dropdown.offsetHeight > 200 )
			{
				dropdown.style.height   = "200";
				dropdown.style.overflow = "auto";
			}
		}
	}
	
	SelectBox.highlightSelected( el, true );
};

/**
 * @access public
 * @static
 */
SelectBox.int_remove = function( el, i )
{
	if ( el.options[i] != null )
		el.options[i].outerHTML = "";
};

/**
 * @access public
 * @static
 */
SelectBox.int_add = function( el, e, i )
{
	var html = "<div class='option' noWrap";

	if ( e.value != null )
		html += " value='" + e.value + "'";
	
	if ( e.style.cssText != null )
		html += " style='" + e.style.cssText + "'";

	html += ">";

	if ( e.text != null )
		html += e.text;
	
	html += "</div>";

	if ( ( i == null ) || ( i >= el.options.length ) )
		i = el.options.length - 1;

	el.options[i].insertAdjacentHTML( "AfterEnd", html );
};

/**
 * @access public
 * @static
 */
SelectBox.initSized = function( el )
{
	var h = 0;
	el.children[0].style.display = "none";

	dropdown = el.children[1];
	dropdown.style.visibility = "visible";

	if ( dropdown.children.length > el.size )
	{
		dropdown.style.overflow = "auto";

		for ( var i = 0; i < el.size; i++ )
			h += dropdown.children[i].offsetHeight;

		if ( dropdown.style.borderWidth != null )
			dropdown.style.pixelHeight = h + 4;
		else
			dropdown.style.height = h;
	}

	dropdown.style.border = SelectBox.sizedBorderCss;
	el.style.height = dropdown.style.pixelHeight;
};

/**
 * @access public
 * @static
 */
SelectBox.copySelected = function( el )
{
	var selectedIndex = SelectBox.findSelected( el );

	selectedCell = el.children[0].rows[0].cells[0];
	selectedDiv  = el.children[1].children[selectedIndex];	
	selectedCell.innerHTML = selectedDiv.outerHTML;
};

/**
 * @access public
 * @static
 */
SelectBox.findSelected = function( el )
{
	var selected = null;
	ec = el.children[1].children; // the table is the first child
	var ecl = ec.length;
	
	for ( var i = 0; i < ecl; i++ )
	{
		if ( ec[i].getAttribute( "selected" ) != null )
		{
			if ( selected == null )
				selected = i;
			else
				ec[i].removeAttribute( "selected" );
		}
	}
	
	if ( selected == null )
		selected = 0;

	return selected;
};

/**
 * @access public
 * @static
 */
SelectBox.toggleDropDown = function( el )
{
	if ( el.size == 1 )
	{
		dropDown = el.children[1];
		
		if ( dropDown.style.visibility == "" )
			dropDown.style.visibility = "hidden";
			
		if ( dropDown.style.visibility == "hidden" )
			SelectBox.showDropDown( dropDown );
		else
			SelectBox.hideDropDown( dropDown );
	}
};

/**
 * @access public
 * @static
 */
SelectBox.optionClick = function()
{
	el = Util.getReal( window.event.srcElement, "className", "option" );

	if ( el.className == "option" )
	{
		dropdown    = el.parentElement;
		selectBox   = dropdown.parentElement;
		oldSelected = dropdown.children[SelectBox.findSelected(selectBox)];

		if ( oldSelected != el )
		{
			oldSelected.removeAttribute( "selected" );
			el.setAttribute( "selected", 1 );
			selectBox.selectedIndex = SelectBox.findSelected( selectBox );
		}

		if ( selectBox.onchange != null )
		{
			if ( selectBox.id != "" )
			{
				eval( selectBox.onchange.replace(/this/g, selectBox.id) );
			}
			else
			{
				SelectBox.globalSelect = selectBox;
				eval( selectBox.onchange.replace(/this/g, "SelectBox.globalSelect") );
			}
		}
		
		if ( el.backupCss != null )
			el.style.cssText = el.backupCss;
		
		SelectBox.copySelected( selectBox );
		SelectBox.toggleDropDown( selectBox );
		SelectBox.highlightSelected( selectBox, true );
	}
};

/**
 * @access public
 * @static
 */
SelectBox.optionOver = function()
{
	var toEl   = Util.getReal( window.event.toElement,   "className", "option" );
	var fromEl = Util.getReal( window.event.fromElement, "className", "option" );
	
	if ( toEl == fromEl )
		return;
	
	var el = toEl;
	
	if ( el.className == "option" )
	{
		if ( el.backupCss == null )
			el.backupCss = el.style.cssText;
		
		SelectBox.highlightSelected( el.parentElement.parentElement, false );
		el.style.cssText = el.backupCss + "; " + SelectBox.overOptionCss;
		this.highlighted = true;
	}
};

/**
 * @access public
 * @static
 */
SelectBox.optionOut = function()
{
	var toEl   = Util.getReal( window.event.toElement,   "className", "option" );
	var fromEl = Util.getReal( window.event.fromElement, "className", "option" );

	if ( fromEl == fromEl.parentElement.children[SelectBox.findSelected(fromEl.parentElement.parentElement)] )
	{
		if ( toEl == null || toEl.className != "option" )
			return;
	}
	
	if ( toEl != null )
	{
		if ( toEl.className != "option" )
		{
			if ( fromEl.className == "option" )
				SelectBox.highlightSelected( fromEl.parentElement.parentElement, true );
		}
	}
	
	if ( toEl == fromEl )
		return;
	
	var el = fromEl;

	if ( el.className == "option" )
	{
		if ( el.backupCss != null )
			el.style.cssText = el.backupCss;
	}
};

/**
 * @access public
 * @static
 */
SelectBox.highlightSelected = function( el, add )
{
	var selectedIndex = SelectBox.findSelected( el );
	selected = el.children[1].children[selectedIndex];
	
	if ( add )
	{
		if ( selected.backupCss == null )
			selected.backupCss = selected.style.cssText;
		
		selected.style.cssText = selected.backupCss + "; " + SelectBox.overOptionCss;
	}
	else if ( !add )
	{
		if ( selected.backupCss != null )
			selected.style.cssText = selected.backupCss;
	}
};

/**
 * @access public
 * @static
 */
SelectBox.hideShownDropDowns = function()
{
	var el      = Util.getReal( window.event.srcElement, "className", "select" );
	var spans   = document.all.tags("SPAN");
	var selects = new Array();
	var index   = 0;
	
	for ( var i = 0; i < spans.length; i++ )
	{
		if ( ( spans[i].className == "select" ) && ( spans[i] != el ) )
		{
			dropdown = spans[i].children[1];
			
			if ( ( spans[i].size == 1 ) && ( dropdown.style.visibility == "visible" ) )
				selects[index++] = dropdown;
		}
	}
	
	for ( var j = 0; j < selects.length; j++ )
		SelectBox.hideDropDown( selects[j] );
};

/**
 * @access public
 * @static
 */
SelectBox.hideDropDown = function( el )
{
	if ( typeof(fade) == "function" )
		FadeObject.fade( el, false );
	else
		el.style.visibility = "hidden";
};

/**
 * @access public
 * @static
 */
SelectBox.showDropDown = function( el )
{
	if ( typeof( Fade.fade ) == "function" )
		FadeObject.fade( el, true );
	else if ( typeof( Swipe.swipe ) == "function" )
		SwipeObject.swipe( el, 2 );
	else
		el.style.visibility = "visible";
};

/**
 * @access public
 * @static
 */
SelectBox.initSelectBoxes = function()
{
	var spans   = document.all.tags( "SPAN" );
	var selects = new Array();
	var index   = 0;
	
	for ( var i = 0; i < spans.length; i++ )
	{
		if ( spans[i].className == "select" )
			selects[index++] = spans[i];
	}
	
	for ( var j = 0; j < selects.length; j++ )
		SelectBox.initSelectBox( selects[j] );
};
