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
 * @package gui_richedit
 */
 
/**
 * Constructor
 *
 * @access public
 */
SmartEdit = function()
{
	this.Base = Base;
	this.Base();
};


SmartEdit.prototype = new Base();
SmartEdit.prototype.constructor = SmartEdit;
SmartEdit.superclass = Base.prototype;

/**
 * @constant
 * @static
 */
SmartEdit.SEP_PADDING = 5;

/**
 * @constant
 * @static
 */
SmartEdit.HANDLE_PADDING = 7;

/**
 * @access public
 * @static
 */
SmartEdit.pureText = true;

/**
 * @access public
 * @static
 */
SmartEdit.bTextMode = false;

/**
 * @access public
 * @static
 */
SmartEdit.YInitialized = false;

/**
 * @access public
 * @static
 */
SmartEdit.yToolbars = new Array();


/**
 * @access public
 * @static
 */
SmartEdit.init = function()
{
	SmartEdit.YInitialized = true;
	var i, s, curr;

	// Find all the toolbars and initialize them.
	for ( i = 0; i < document.body.all.length; i++ )
	{
    	//Get the current object and assign to curr
    	curr = document.body.all[i];    

		// Now we will look in every div tag class value which is the child node of <div class="yToolBar"> 
		if ( curr.className == "yToolbar" )
		{
			// See InitTB for more! if return false then the current
			// classname is invalid or not contain in our CSS.
			if ( !SmartEdit.initTB( curr ) )
				return Base.raiseError( "Toolbar: " + curr.id + " failed to initialize. Status: false" );
      
			// Get the next child node of <div class="YToolBar"> parent nodes.
			SmartEdit.yToolbars[SmartEdit.yToolbars.length] = curr;
		}
	}

	// Lay out the page, set handler.
	SmartEdit.layout();

	mytext.document.open();
	mytext.document.write( "<BODY MONOSPACE STYLE=\"font:10pt arial,sans-serif\"></body>" );
	mytext.document.close();
	
	mytext.document.designMode = "On"
};

/**
 * Initialize a toolbar.
 *
 * @access public
 * @static
 */
SmartEdit.initTB = function( y )
{
	// Set initial size of toolbar to that of the handle.
	y.TBWidth = 0;
    
	// Populate the toolbar with its contents.
	if ( !SmartEdit.populateTB( y ) )
		return false;
  
	// Set the toolbar width and put in the handle.
	y.style.posWidth = y.TBWidth;
  
	return true;
};

/**
 * Populate a toolbar with the elements within it.
 *
 * @access public
 * @static
 */
SmartEdit.populateTB = function( y )
{
	var i, elements, element;

	// Iterate through all the top-level elements in the toolbar.
	elements = y.children;

	for ( i = 0; i < elements.length; i++ )
	{
		element = elements[i];

		if ( element.tagName == "SCRIPT" || element.tagName == "!" )
			continue;
    
		switch ( element.className )
		{
			case "Btn":
				if ( element.YINITIALIZED == null )
				{
					if ( !SmartEdit.initButton( element ) )
						return Base.raiseError( "Problem initializing:" + element.id );
				}
      
				element.style.posLeft = y.TBWidth;
				y.TBWidth += element.offsetWidth + 1;
				break;
      
			case "TBGen":
				element.style.posLeft = y.TBWidth;
				y.TBWidth += element.offsetWidth + 1;
				break;
      
			case "TBSep":
				element.style.posLeft = y.TBWidth + 2;
				y.TBWidth += SmartEdit.SEP_PADDING;
				break;
      
			case "TBHandle":
				element.style.posLeft = 2;
				y.TBWidth += element.offsetWidth + SmartEdit.HANDLE_PADDING;
				break;
      
			default:
				return Base.raiseError( "Invalid class: " + element.className + " on Element: " + element.id + " <" + element.tagName + ">" );
		}
	}

	y.TBWidth += 1;
	return true;
};

/**
 * Layout the docked toolbars.
 *
 * @access public
 * @static
 */
SmartEdit.layoutTBs = function()
{
	NumTBs = SmartEdit.yToolbars.length;

	// If no toolbars we're outta here.
	if ( NumTBs == 0 )
		return;

	//Get the total size of a TBline.
	var i;
	var ScrWid   = ( document.body.offsetWidth ) - 6;
	var TotalLen = ScrWid;

	for ( i = 0 ; i < NumTBs ; i++ )
	{
		TB = SmartEdit.yToolbars[i];

		if ( TB.TBWidth > TotalLen )
			TotalLen = TB.TBWidth;
	}

	var PrevTB, LastWid, CurrWid;
	var LastStart = 0;
	var RelTop    = 0;

	// Set up the first toolbar.
	var TB = SmartEdit.yToolbars[0];
	TB.style.posTop  = 0;
	TB.style.posLeft = 0;

	// Lay out the other toolbars.
	var Start = TB.TBWidth;
	for ( i = 1 ; i < SmartEdit.yToolbars.length ; i++ )
	{
		PrevTB  = TB;
		TB      = SmartEdit.yToolbars[i];
		CurrWid = TB.TBWidth;

		if ( ( Start + CurrWid ) > ScrWid )
		{ 
			// TB needs to go on next line.
			Start   = 0;
			LastWid = TotalLen - LastStart;
		} 
		else
		{ 
			// Ok on this line.
			LastWid  = PrevTB.TBWidth;
			RelTop  -= TB.offsetHeight;
		}
      
		// Set TB position and LastTB width.
		TB.style.posTop    = RelTop;
		TB.style.posLeft   = Start;
		PrevTB.style.width = LastWid;

		// Increment counters.
		LastStart = Start;
		Start += CurrWid;
	} 

	// Set width of last toolbar.
	TB.style.width = TotalLen - LastStart;
  
	// Move everything after the toolbars up the appropriate amount.
	i--;
	TB = SmartEdit.yToolbars[i];
	
	var TBInd = TB.sourceIndex;
	var A = TB.document.all;
	var item;
	
	for ( i in A )
	{
		item = A.item( i );
		
		if ( !item )
			continue;
		
		if ( !item.style )
			continue;
		
		if ( item.sourceIndex <= TBInd )
			continue;
		
		if ( item.style.position == "absolute" )
			continue;
		
		item.style.posTop = RelTop;
	}
};

/**
 * Lays out the page.
 *
 * @access public
 * @static
 */
SmartEdit.layout = function()
{
	SmartEdit.layoutTBs();
};

/**
 * Check if toolbar is being used when in text mode.
 *
 * @access public
 * @static
 */
SmartEdit.isRTextMode = function()
{
	if ( !SmartEdit.bTextMode )
		return true;

	mytext.focus();
	return false;
};

/**
 * Formats text in mytext.
 *
 * @access public
 * @static
 */
SmartEdit.execute = function( what, opt )
{
	if ( !SmartEdit.isRTextMode() )
		return;
  
	if ( opt == "removeFormat" )
	{
		what = opt;
		opt  = null;
	}

	if ( opt == null )
		mytext.document.execCommand( what );
	else
		mytext.document.execCommand( what, "", opt );
  
	SmartEdit.pureText = false;
	mytext.focus();
};

/**
 * Switches between text and html mode.
 *
 * @access public
 * @static
 */
SmartEdit.setMode = function( newMode )
{
	SmartEdit.bTextMode = newMode;
	var cont;

	if ( SmartEdit.bTextMode )
	{
 		SmartEdit.cleanHtml();
		SmartEdit.cleanHtml();

		cont = mytext.document.body.innerHTML;
		mytext.document.body.innerText = cont;
	}
	else
	{
		cont = mytext.document.body.innerText;
		mytext.document.body.innerHTML = cont;
	}
  
	mytext.focus();
};

/**
 * Finds and returns an element.
 *
 * @access public
 * @static
 */
SmartEdit.getEl = function( sTag, start )
{
	// Copy the selected character to "start" string while start!=NULL && tagName doesn't have 'A'.
	while ( ( start != null ) && ( start.tagName != sTag ) )
		start = start.parentElement;

	return start;
};

/**
 * @access public
 * @static
 */
SmartEdit.createLink = function()
{
	// Is View Source is Checked!
	if ( !SmartEdit.isRTextMode() )
		return;
  
	var isA = SmartEdit.getEl( "A", mytext.document.selection.createRange().parentElement() );
	var str = prompt( "Enter URL :", isA? isA.href : "http:\/\/" );
  
	// If str selection type is None!(If the user didn't block the string) then
	// get the string and add the <A HREF and paste it.  
	if ( ( str != null ) && ( str != "http://" ) )
	{
		if ( mytext.document.selection.type == "None" )
		{    	
			var sel = mytext.document.selection.createRange();
			sel.pasteHTML( "<A HREF=\"" + str + "\">" + str + "</A> " );
			sel.select();
 		}
		else
		{ 
			// If user had selected/blocked the string  just pass this command.
			SmartEdit.execute( "CreateLink", str );
		}
	}
	else
	{ 
		// If nothing entered just Focust in our IFRAME.
		mytext.focus();
	}
};

/**
 * Sets the text color.
 *
 * @access public
 * @static
 */
SmartEdit.setFgColor = function()
{
	if ( !SmartEdit.isRTextMode() )
		return;

	// TODO
};

/**
 * Sets the background color.
 *
 * @access public
 * @static
 */
SmartEdit.setBgColor = function()
{
	if ( !SmartEdit.isRTextMode() )
		return;

	// TODO
};

/**
 * @access public
 * @static
 */
SmartEdit.cleanHtml = function()
{
	var fonts = mytext.document.body.all.tags( "FONT" );
	var curr;
	
	for ( var i = fonts.length - 1; i >= 0; i-- )
	{
		curr = fonts[i];
		
		if ( curr.style.backgroundColor == "#ffffff" )
			curr.outerHTML = curr.innerHTML;
	}
};

/**
 * @access public
 * @static
 */
SmartEdit.getPureHtml = function()
{
	var str   = "";
	var paras = mytext.document.body.all.tags( "P" );

	if ( paras.length > 0 )
	{
		for ( var i = paras.length - 1; i >= 0; i-- )
			str = paras[i].innerHTML + "\n" + str;
	}
	else
	{
		str = mytext.document.body.innerHTML;
	}

	return str;
};

/** 
 * Initialize a toolbar button.
 *
 * @access public
 * @static
 */
SmartEdit.initButton = function( btn )
{
	btn.onmouseover   = SmartEdit.btnMouseOver;
	btn.onmouseout    = SmartEdit.btnMouseOut;
	btn.onmousedown   = SmartEdit.btnMouseDown;
	btn.onmouseup     = SmartEdit.btnMouseUp;
	btn.ondragstart   = SmartEdit.yCancelEvent;
	btn.onselectstart = SmartEdit.yCancelEvent;
	btn.onselect      = SmartEdit.yCancelEvent;
	btn.YUSERONCLICK  = btn.onclick;
	btn.onclick       = SmartEdit.yCancelEvent;
	btn.YINITIALIZED  = true;

	return true;
};

/**
 * Handler that simply cancels an event.
 *
 * @access public
 * @static
 */
SmartEdit.yCancelEvent = function()
{
	event.returnValue  = false;
	event.cancelBubble = true;

	return false;
};

/**
 * Toolbar button onmouseover handler.
 *
 * @access public
 * @static
 */
SmartEdit.btnMouseOver = function()
{
	if ( event.srcElement.tagName != "IMG" )
		return false;

	var image   = event.srcElement;
	var element = image.parentElement;
  
	// Change button look based on current state of image.
	if ( image.className == "Ico" )
		element.className = "BtnMouseOverUp";
	else if ( image.className == "IcoDown" )
		element.className = "BtnMouseOverDown";

	event.cancelBubble = true;
};

/**
 * Toolbar button onmouseout handler.
 *
 * @access public
 * @static
 */
SmartEdit.btnMouseOut = function()
{
	if ( event.srcElement.tagName != "IMG" )
	{
		event.cancelBubble = true;
		return false;
	}

	var image   = event.srcElement;
	var element = image.parentElement;
	yRaisedElement = null;
  
	element.className = "Btn";
	image.className   = "Ico";

	event.cancelBubble = true;
};

/**
 * Toolbar button onmousedown handler.
 *
 * @access public
 * @static
 */
SmartEdit.btnMouseDown = function()
{
	if ( event.srcElement.tagName != "IMG" )
	{
		event.cancelBubble = true;
 		event.returnValue  = false;

		return false;
	}

	var image   = event.srcElement;
	var element = image.parentElement;

	element.className = "BtnMouseOverDown";
	image.className   = "IcoDown";

	event.cancelBubble = true;
	event.returnValue  = false;

	return false;
};

/**
 * Toolbar button onmouseup handler.
 *
 * @access public
 * @static
 */
SmartEdit.btnMouseUp = function()
{
	if ( event.srcElement.tagName != "IMG" )
	{
		event.cancelBubble = true;
 		return false;
	}

	var image   = event.srcElement;
	var element = image.parentElement;

	if ( element.YUSERONCLICK )
		eval( element.YUSERONCLICK + "anonymous()" );

	element.className = "BtnMouseOverUp";
	image.className   = "Ico";

	event.cancelBubble = true;
	return false;
};
