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
 * @package gui_tab
 */
 
/**
 * Constructor
 *
 * @access public
 */
SDKTab = function( newTab )
{
	this.Base = Base;
	this.Base();
	
	this._caption = "";
	this._content = null;
	this._tab     = null;

	// Only initialize of this is actually a tab. Tabs are identifed by a @tabName attribute.
	if ( newTab.tabName )
		this._initializeComponent( newTab );
	else
		return null;
};


SDKTab.prototype = new Base();
SDKTab.prototype.constructor = SDKTab;
SDKTab.superclass = Base.prototype;

/**
 * Returns the caption used for the tab.
 *
 * @access public
 */
SDKTab.prototype.GetCaption = function()
{
	return this._caption;
};

/**
 * Sets the caption used for the tab. Also updates the tabbed title bar.
 *
 * @access public
 */
SDKTab.prototype.SetCaption = function( newCaption )
{
	this._caption = newCaption;
	
	// Should only change title bar if currently active.
	if ( this.IsActive )
		oMTitle.innerText = newCaption;
};

/**
 * Returns the <div> that is attached to this tab object.
 *
 * @access public
 */
SDKTab.prototype.GetContent = function()
{
	return this._content;
};

/**
 * Returns the rendered tab (<TD>) for this tab object.
 *
 * @access public
 */
SDKTab.prototype.GetTab = function()
{
	return this._tab;
};

/**
 * Returns true if this tab object is currently being rendered (e.g. the <div> is displayed),
 * otherwise returns false.
 *
 * @access public
 */
SDKTab.prototype.IsActive = function()
{
	return ( ( this.GetTab().className == "oMTabOn" )? true : false );
};

/**
 * Forces a an inactive tab to become active. That means that the 
 * tab itself changes state and the <div> is rendered.
 * Any previously active tab is made inactive first.
 *
 * @access public
 */
SDKTab.prototype.MakeActive = function()
{
	var tab = this.GetActiveTab();
	
	if ( tab )
		tab.MakeInActive();

	oMTitle.innerText = this.GetCaption();
	this.GetTab().className = "oMTabOn";
	oMTData.appendChild( this.GetContent() );
	this.GetContent().style.display = "block";
	
	// Reset the scroll bar.
	this.SetScrollPosition( 0 );
	
	oMTData.scrollTop = this.GetScrollPosition();
	
	// Save the state to a userData store.
	SDKTab.goPersist.SetAttribute( "selectedTab", this.GetCaption() );
};

/**
 * @access public
 */
SDKTab.prototype.GetActiveTab  = function()
{
	for ( var i = 0; i < SDKTab.gaTabs.length; i++ )
	{
		var tab = SDKTab.gaTabs[i];
		
		if ( tab.IsActive() )
			return tab;
	}
};

/**
 * Forces an active tab to become inactive. Changes the state of the tab
 * and removes the <div> from the screen.
 *
 * @access public
 */
SDKTab.prototype.MakeInActive = function()
{
	this.GetContent().style.display = "none";
	this.GetTab().className = "oMTab";
	oMTitle.innerText = "";
};

/**
 * Event for when the mouse moves over the tab button.
 *
 * @access public
 */
SDKTab.prototype.OnMouseHover = function()
{
	// Should only run event if not already active.
	if ( !this.IsActive() )
		this.GetTab().className = "oMTabHover";
};

/**
 * Event for when the user clicks the tab button.
 *
 * @access public
 */
SDKTab.prototype.OnMouseClick = function()
{
	// Should only run event if not already active.
	if ( !this.IsActive() )
		this.MakeActive();
};

/**
 * Event for when the user moves the mouse from within the tab button area.
 *
 * @access public
 */
SDKTab.prototype.OnMouseFlee = function()
{
	// Should only run event if not alreay active.
	if ( !this.IsActive() )
		this.GetTab().className = "oMTab";
}; 

/**
 * Returns the scroll position for the <div> associated with this tab object.
 *
 * @access public
 */
SDKTab.prototype.GetScrollPosition = function()
{
	this._scroll;
};

/**
 * Sets the scroll position for the <div> associated with this tab object.
 *
 * @access public
 */
SDKTab.prototype.SetScrollPosition = function( newValue )
{
	this._scroll = newValue;
	
	if ( this.IsActive() )
		oMTData.scrollTop = newValue;
	
	SDKTab.goPersist.SetAttribute( "scroll", newValue );
	SDKTab.goPersist.Save();
};


/**
 * Not used, ignore.
 *
 * @access private
 */
SDKTab.prototype._setColumnHeaders = function()
{
	var heads = this.GetContent().getElementsByTagName( "TH" );
	
	for ( var i = 0; i < heads.length; i++ )
	{
		var cn = heads[i].cloneNode( true );
		oMHeadings.appendChild( cn );
		heads[i].style.display = "none";
	}
}; 

/**
 * Initializes the state of the tab. This includes creating the physical tab
 * as well as associated a <div> with it.
 *
 * @access private
 */
SDKTab.prototype._initializeComponent = function( newTab )
{
	this._caption = newTab.tabName;	// the name used for the tab
	this._content = newTab;			// the <div> that contains the tabbed data
	this._scroll  = 0;				// position of the scroll bar

	// Prepare the tab for use. Create the necessary structure.
	this._tab = document.createElement( "TD" );
	
	this._tab.onmouseover = SDKTab.onMouseOverRedirect;
	this._tab.onmouseout  = SDKTab.onMouseOutRedirect;
	this._tab.onmousedown = SDKTab.onMouseClickRedirect;
	this._tab.onkeypress  = SDKTab.onMouseClickRedirect;
	this._tab.onclick     = SDKTab.onMouseClickRedirect;
	
	this._tab.title = this.GetCaption();
	this._tab.className = "oMTab";
	this._tab.tabIndex  = "0";
	this._tab.innerText = this.GetCaption();
	
	// Simply attachs a reference to this tab object onto the <TD>.
	this._tab.tab = this;
	
	this.GetContent().tab = this;
};


/**
 * @access public
 * @static
 */
SDKTab.goPersist = null;

/**
 * @access public
 * @static
 */
SDKTab.gsGraphicsPath = "img/";

/**
 * @access public
 * @static
 */
SDKTab.gsStoreName = "ap_sdk";

/**
 * @access public
 * @static
 */
SDKTab.gsTabControl = "";

/**
 * @access public
 * @static
 */
SDKTab.gaTabs = new Array();

/**
 * @access public
 * @static
 */
SDKTab.initTabbedMembers = function()
{
	if ( document.getElementById( "oMT" ) )
	{
		// need to get the topic id for this page.
		var mshaid = document.all( "MS-HAID" );

		if ( mshaid )
			gsPageId = mshaid.getAttribute( "content" );

		// assembly an array of all the divs that are tabs
		SDKTab.locateAvailableTabs();

		divscol = document.all.tags( "div" );
		divsize = divscol.length;
		
		if ( divsize > 0 )
		{
			SDKTab.gsTabControl = '';

			// this defines the tabbed title bar.
			SDKTab.gsTabControl += '<TABLE class="oMembersTable" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse" bordercolor="#111111" width="100%">';
			SDKTab.gsTabControl += '	<STYLE>';
			SDKTab.gsTabControl += '		.oMembersTable	{ font:Menu; font-size:70%; }';
			SDKTab.gsTabControl += '		TD				{ font:Menu; font-size:70%; }';
			SDKTab.gsTabControl += '		.oMTab			{ background:#eeeeee; width:100%; height:20px; border-top=1px solid #5f2318; padding:7px; padding-left:7px; cursor:hand; }';
			SDKTab.gsTabControl += '		.oMTabOn		{ background:#999999; width:100%; height:20px; color:#ffffff; border-top:1px groove white; padding:7px; padding-left:7px; cursor:hand; }';
			SDKTab.gsTabControl += '		.oMTabHover		{ background:#dddddd; width:100%; height:20px; border-top=1px solid #5f2318; padding:7px; padding-left:7px; cursor:hand; }';
			SDKTab.gsTabControl += '	</STYLE>';
			SDKTab.gsTabControl += '	<TR>';
			SDKTab.gsTabControl += '		<TD width="100%" height="24" bgcolor="#5f2318">';
			SDKTab.gsTabControl += '			<TABLE border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse" bordercolor="#111111" width="100%" id="AutoNumber4">';
			SDKTab.gsTabControl += '				<TR>';
			SDKTab.gsTabControl += '					<TD width="4"  height="20px">&nbsp;</TD>';
			SDKTab.gsTabControl += '					<TD width="*"  height="20px"><DIV id="oMTitle" style="color:white; font:Caption">&nbsp;</DIV></TD>';
			SDKTab.gsTabControl += '					<TD width="16" height="20px" style="padding-top:2px; padding-bottom:2px;"><IMG id="oCollapso" src="' + SDKTab.gsGraphicsPath + 'sdk_expand.gif" onclick="SDKTab.expand_onclick_handler();" title="Expand" state="collapsed" style="cursor:hand" width="16" height="16"></TD>';
			SDKTab.gsTabControl += '					<TD width="4"  height="20px">&nbsp;</TD>';
			SDKTab.gsTabControl += '				</TR>';
			SDKTab.gsTabControl += '			</TABLE>';
			SDKTab.gsTabControl += '		</TD>';
			SDKTab.gsTabControl += '	</TR>';
			
			SDKTab.gsTabControl += '	<TR>';
			SDKTab.gsTabControl += '		<TD width="100%">';
			SDKTab.gsTabControl += '			<TABLE border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse" bordercolor="#111111" width="100%">';
			SDKTab.gsTabControl += '				<TR>';
			SDKTab.gsTabControl += '					<TD width="95px" style="padding:5px; padding-top:0px; background:#eeeeee; border-top:1px solid white; border-left:1px solid #5f2318; border-bottom:1px solid #5f2318; border-right:4px solid #5f2318" valign="top">';
			SDKTab.gsTabControl += '						<TABLE border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse" bordercolor="#111111" width="100%">';
			SDKTab.gsTabControl += '							<TBODY id="oMTabberList">';
			SDKTab.gsTabControl += '								<TR>';
			SDKTab.gsTabControl += '									<TD width="100%"  height="20px" bgcolor="#eeeeee" style="padding-left:2px;"><B>Show:</B></TD>';
			SDKTab.gsTabControl += '								</TR>';
			
			// The tabs will be dynamically built later based on the <div> content.
		
			SDKTab.gsTabControl += '							</TBODY>';
			SDKTab.gsTabControl += '						</TABLE>';
			SDKTab.gsTabControl += '					</TD>';
			SDKTab.gsTabControl += '					<TD width="*" valign="top" style="border-right:1px solid #5f2318; border-bottom:1px solid #5f2318" id="oMTabberContent">';
			SDKTab.gsTabControl += '						<TABLE border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" height="100%" width="100%>';
			SDKTab.gsTabControl += '							<TR>';
			SDKTab.gsTabControl += '								<TD height="20" width="100%" bgcolor="#dddddd" style="border-collapse:collapse" bordercolor="#111111" height="100%">';
			SDKTab.gsTabControl += '									<TABLE border="0" cellpadding="0" cellspaceing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%">';
			SDKTab.gsTabControl += '										<TR>';
			SDKTab.gsTabControl += '											<TD width="100%" height="*">';
			SDKTab.gsTabControl += '												<DIV id="oMTData" onscroll="SDKTab.scroll_onscroll_handler();" style="height:255px; overflow:auto; overflow-x:hidden;"></DIV>';
			SDKTab.gsTabControl += '											</TD>';
			SDKTab.gsTabControl += '										</TR>';
			SDKTab.gsTabControl += '									</TABLE>';
			SDKTab.gsTabControl += '								</TD>';
			SDKTab.gsTabControl += '							</TR>';
			SDKTab.gsTabControl += '						</TABLE>';
			
			// The actual member links appear here based on the selected tabs.
			
			SDKTab.gsTabControl += '					</TD>';
			SDKTab.gsTabControl += '				</TR>';
			SDKTab.gsTabControl += '			</TABLE>';
			SDKTab.gsTabControl += '		</TD>';
			SDKTab.gsTabControl += '	</TR>';
			SDKTab.gsTabControl += '</TABLE>';

			// Renders the initial, constant, content.

			oMT.insertAdjacentHTML( "beforeBegin", SDKTab.gsTabControl );
			SDKTab.goPersist = new Persistence( oMTData, SDKTab.gsStoreName );
			
			// Cycles through all the tabs and renders the tab buttons.
			for ( var i = 0; i < SDKTab.gaTabs.length; i++ )
			{
				var tab = SDKTab.gaTabs[i];
				var tr  = document.createElement( "TR" );
				tr.appendChild( tab.GetTab() );
				    
				oMTabberList.appendChild( tr );
			}
			
			// Determine the initial tab to display. If there is persistent
			// information, they use that, otherwise, just use the first
			// tab in the list.
			SDKTab.restoreInitState()
		}
	}
};	

/**
 * @access public
 * @static
 */
SDKTab.restoreInitState = function()
{
	persistTab    = SDKTab.goPersist.GetAttribute( "selectedTab" );
	persistExpand = SDKTab.goPersist.GetAttribute( "expanded"    );
	persistScroll = SDKTab.goPersist.GetAttribute( "scroll"      );
	
	if ( SDKTab.gaTabs[persistTab] )
		SDKTab.gaTabs[persistTab].MakeActive();
	else
		SDKTab.gaTabs[0].MakeActive();
	
	if ( persistExpand)
		SDKTab.toggleExpandDataView();
	
	if ( persistScroll)
		SDKTab.gaTabs[0].GetActiveTab().SetScrollPosition( persistScroll );
};

/**
 * Cycles through all the <div> tags and locate any that might be tabs.
 *
 * @access public
 * @static
 */
SDKTab.locateAvailableTabs = function()
{
	var key;
	var divs = document.all.tags( "div" );
	
	for ( key in divs )
	{
		var div = divs[key];
		
		// This is a tag. Try to add it to the tab collection for later use.
		if ( div.tabName )
		{
			SDKTab.gaTabs[div.tabName] = SDKTab.gaTabs[SDKTab.gaTabs.length] = new SDKTab( div );
			div.style.display = "none";
		}
	}
};

/**
 * Expands or collapses the list based on the interaction with the expand/collapse glyph.
 *
 * @access public
 * @static
 */
SDKTab.toggleExpandDataView = function()
{
	if ( oCollapso.state == "collapsed" )
	{
		// The state is collapase so force environment to be expanded.
		oCollapso.title = "Collapse";
		oMTData.style.overflow = "visible";
		oCollapso.src   = SDKTab.gsGraphicsPath + "sdk_collapse.gif";
		oCollapso.state = "expanded";
		
		// now that the view is being expanded, must save this state.
		SDKTab.goPersist.SetAttribute( "expanded", "true" );
	}
	else
	{
		// The state is expanded so force environment to be expanded.
		oCollapso.title = "Expand";
		oMTData.style.overflow = "auto";
		oCollapso.src   = SDKTab.gsGraphicsPath + "sdk_expand.gif";
		oCollapso.state = "collapsed";

		// now that the view is being collapsed, must remove expanded state.
		SDKTab.goPersist.SetAttribute( "expanded", "false" );
	}
};

/**
 * @access public
 * @static
 */
SDKTab.expand_onclick_handler = function()
{
	SDKTab.toggleExpandDataView();
};

/**
 * @access public
 * @static
 */
SDKTab.scroll_onscroll_handler = function()
{
	SDKTab.gaTabs[0].GetActiveTab().SetScrollPosition( oMTData.scrollTop );
};

/**
 * @access public
 * @static
 */
SDKTab.onMouseOverRedirect = function()
{
	this.tab.OnMouseHover();
};

/**
 * @access public
 * @static
 */
SDKTab.onMouseOutRedirect = function()
{
	this.tab.OnMouseFlee();
};

/**
 * @access public
 * @static
 */
SDKTab.onMouseClickRedirect = function()
{
	this.tab.OnMouseClick();
};
