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
ExcelTab = function()
{
	this.Base = Base;
	this.Base();
};


ExcelTab.prototype = new Base();
ExcelTab.prototype.constructor = ExcelTab;
ExcelTab.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
ExcelTab.styleArr = new Array(
	"window",
	"buttonface",
	"windowframe",
	"windowtext",
	"threedlightshadow",
	"threedhighlight",
	"threeddarkshadow",
	"threedshadow"
);

/**
 * @access public
 * @static
 */
ExcelTab.tabs = null;

/**
 * @access public
 * @static
 */
ExcelTab.tabCount = null;

/**
 * @access public
 * @static
 */
ExcelTab.tabCurrent = null;

/**
 * @access public
 * @static
 */
ExcelTab.tabCount = null;

/**
 * @access public
 * @static
 */
ExcelTab.tabX = new Array( ExcelTab.tabCount );

/**
 * @access public
 * @static
 */
ExcelTab.startItem = 1;

/**
 * @access public
 * @static
 */
ExcelTab.fnSetTabs = function( arr )
{
	ExcelTab.tabs = arr;
	ExcelTab.tabCount = ExcelTab.tabs.length;
};

/**
 * @access public
 * @static
 */
ExcelTab.fnBuildFrameset = function()
{
	var szHTML = "<frameset rows=\"*,18\" border=0 width=0 frameborder=no framespacing=0>" +
		"<frame src=\"" + document.all.item( "shLink" )[ExcelTab.startItem].href + "\" name=\"frSheet\" noresize>" +
		"<frameset cols=\"54,*\" border=0 width=0 frameborder=no framespacing=0>" +
		"<frame src=\"\" name=\"frScroll\" marginwidth=0 marginheight=0 scrolling=no>" +
		"<frame src=\"\" name=\"frTabs\" marginwidth=0 marginheight=0 scrolling=no>" +
		"</frameset></frameset><plaintext>";

	with ( document )
	{
		open( "text/html", "replace" );
		write( szHTML );
		close();
	}

	ExcelTab.fnBuildTabStrip();
};

/**
 * @access public
 * @static
 */
ExcelTab.fnBuildTabStrip = function()
{
	var szHTML =  "<html><head><style>.clScroll {font:8pt Courier New;color:" + ExcelTab.styleArr[6]+";cursor:default;line-height:10pt;}" +
		".clScroll2 {font:10pt Arial;color:" + ExcelTab.styleArr[6] + ";cursor:default;line-height:11pt;}</style></head>" +
		"<body onclick=\"event.returnValue=false;\" ondragstart=\"event.returnValue=false;\" onselectstart=\"event.returnValue=false;\" bgcolor=" + ExcelTab.styleArr[4] + " topmargin=0 leftmargin=0><table cellpadding=0 cellspacing=0 width=100%>" +
		"<tr><td colspan=6 height=1 bgcolor=" + ExcelTab.styleArr[2] + "></td></tr>" +
		"<tr><td style=\"font:1pt\">&nbsp;<td>" +
		"<td valign=top id=tdScroll class=\"clScroll\" onclick=\"parent.ExcelTab.fnFastScrollTabs(0);\" onmouseover=\"parent.ExcelTab.fnMouseOverScroll(0);\" onmouseout=\"parent.ExcelTab.fnMouseOutScroll(0);\"><a>&#171;</a></td>" +
		"<td valign=top id=tdScroll class=\"clScroll2\" onclick=\"parent.ExcelTab.fnScrollTabs(0);\" ondblclick=\"parent.ExcelTab.fnScrollTabs(0);\" onmouseover=\"parent.ExcelTab.fnMouseOverScroll(1);\" onmouseout=\"parent.ExcelTab.fnMouseOutScroll(1);\"><a>&lt</a></td>" +
		"<td valign=top id=tdScroll class=\"clScroll2\" onclick=\"parent.ExcelTab.fnScrollTabs(1);\" ondblclick=\"parent.ExcelTab.fnScrollTabs(1);\" onmouseover=\"parent.ExcelTab.fnMouseOverScroll(2);\" onmouseout=\"parent.ExcelTab.fnMouseOutScroll(2);\"><a>&gt</a></td>" +
		"<td valign=top id=tdScroll class=\"clScroll\" onclick=\"parent.ExcelTab.fnFastScrollTabs(1);\" onmouseover=\"parent.ExcelTab.fnMouseOverScroll(3);\" onmouseout=\"parent.ExcelTab.fnMouseOutScroll(3);\"><a>&#187;</a></td>" +
		"<td style=\"font:1pt\">&nbsp;<td></tr></table></body></html>";

	with ( frames['frScroll'].document )
	{
		open( "text/html", "replace" );
		write( szHTML );
		close();
	}

	szHTML = "<html><head>" +
		"<style>A:link,A:visited,A:active {text-decoration:none;"+"color:" + ExcelTab.styleArr[3] + ";}" +
		".clTab {cursor:hand;background:" + ExcelTab.styleArr[1] + ";font:8pt Arial;padding-left:3px;padding-right:3px;text-align:center;}" +
		".clBorder {background:" + ExcelTab.styleArr[2] + ";font:1pt;}" +
		"</style></head><body onload=\"parent.ExcelTab.fnInit();\" onselectstart=\"event.returnValue=false;\" ondragstart=\"event.returnValue=false;\" bgcolor=" + ExcelTab.styleArr[4] +
		" topmargin=0 leftmargin=0><table id=tbTabs cellpadding=0 cellspacing=0>";

	var iCellCount = ( ExcelTab.tabCount + 1 ) * 2;
	
	for ( var i = 0; i < iCellCount; i += 2 )
		szHTML+="<col width=1><col>";

	for ( var iRow = 0; iRow < 6; iRow++ )
	{
		szHTML += "<tr>";

		if ( iRow == 5 )
		{
			szHTML+="<td colspan=" + iCellCount + "></td>";
		}
		else
		{
			if ( iRow == 0 )
			{
				for( i = 0; i < iCellCount; i++ )
					szHTML+="<td height=1 class=\"clBorder\"></td>";
			}
			else if ( iRow == 1 )
			{
				for( i = 0; i < ExcelTab.tabCount; i++ )
				{
					szHTML += "<td height=1 nowrap class=\"clBorder\">&nbsp;</td>";
					szHTML += "<td id=tdTab height=1 nowrap class=\"clTab\" onmouseover=\"parent.ExcelTab.fnMouseOverTab(" + i + ");\" onmouseout=\"parent.ExcelTab.fnMouseOutTab(" + i + ");\">" +
 						"<a href=\"" + document.all.item("shLink")[i].href + "\" target=\"frSheet\" id=aTab>&nbsp;" + ExcelTab.tabs[i] + "&nbsp;</a></td>";
				}
    
				szHTML += "<td id=tdTab height=1 nowrap class=\"clBorder\"><a id=aTab>&nbsp;</a></td><td width=100%></td>";
			}
			else if ( iRow == 2 )
			{
				for ( i = 0; i < ExcelTab.tabCount; i++ )
					szHTML += "<td height=1></td><td height=1 class=\"clBorder\"></td>";
				
				szHTML += "<td height=1></td><td height=1></td>";
			}
			else if ( iRow == 3 )
			{
				for ( i = 0; i < iCellCount; i++ )
					szHTML+="<td height=1></td>";
			}
			else if ( iRow == 4 )
			{
				for ( i = 0; i < ExcelTab.tabCount; i++ )
					szHTML += "<td height=1 width=1></td><td height=1></td>";
				
				szHTML += "<td height=1 width=1></td><td></td>";
			}
		}
  
		szHTML += "</tr>";
	}

	szHTML += "</table></body></html>";
	
	with ( frames['frTabs'].document )
	{
		open( "text/html", "replace" );
		charset = document.charset;
		write( szHTML );
		close();
	}
};

/**
 * @access public
 * @static
 */
ExcelTab.fnInit = function()
{
	ExcelTab.tabX[0] = 0;

	for ( var i = 1; i <= ExcelTab.tabCount; i++ )
	{
		with ( frames['frTabs'].document.all.tbTabs.rows[1].cells[ExcelTab.fnTabToCol( i - 1 )] )
			ExcelTab.tabX[i] = offsetLeft + offsetWidth - 6;
	}
};

/**
 * @access public
 * @static
 */
ExcelTab.fnTabToCol = function( iTab )
{
	return 2 * iTab + 1;
};

/**
 * @access public
 * @static
 */
ExcelTab.fnNextTab = function( fDir )
{
	var i;
	var iNextTab =- 1;

	with ( frames['frTabs'].document.body )
	{
		if ( fDir == 0 )
		{
			if ( scrollLeft > 0 )
			{
				for ( i = 0; i < ExcelTab.tabCount && ExcelTab.tabX[i] < scrollLeft; i++ );
    			
				if ( i < ExcelTab.tabCount )
					iNextTab = i - 1;
			}
		}
		else
		{
			if ( ExcelTab.tabX[ExcelTab.tabCount] + 6 > offsetWidth + scrollLeft )
			{
    			for ( i = 0; i < ExcelTab.tabCount && ExcelTab.tabX[i] <= scrollLeft; i++ );
				
				if ( i < ExcelTab.tabCount )
					iNextTab = i;
			}
		}
	}

	return iNextTab;
};

/**
 * @access public
 * @static
 */
ExcelTab.fnScrollTabs = function( fDir )
{
	var iNextTab = ExcelTab.fnNextTab( fDir );

	if ( iNextTab >= 0 )
	{
		frames['frTabs'].scroll( ExcelTab.tabX[iNextTab], 0 );
		return true;
	}
	else
	{
		return false;
	}
};

/**
 * @access public
 * @static
 */
ExcelTab.fnFastScrollTabs = function( fDir )
{
	if ( ExcelTab.tabCount > 16 )
		frames['frTabs'].scroll( ExcelTab.tabX[fDir? ExcelTab.tabCount - 1 : 1], 0 );
	else if ( ExcelTab.fnScrollTabs( fDir ) > 0 )
		window.setTimeout( "ExcelTab.fnFastScrollTabs(" + fDir + ");", 5 );
};

/**
 * @access public
 * @static
 */
ExcelTab.fnSetTabProps = function( iTab, fActive )
{
	var i;
	var iCol = ExcelTab.fnTabToCol( iTab );

	if ( iTab >= 0 )
	{
		with ( frames['frTabs'].document.all )
		{
			with ( tbTabs )
			{
				for ( i = 0; i <= 4; i++ )
				{
					with ( rows[i] )
					{
						if ( i == 0 )
						{
							cells[iCol].style.background = ExcelTab.styleArr[fActive? 0 : 2];
						}
						else if ( i > 0 && i < 4 )
						{
							if ( fActive )
							{
								cells[iCol-1].style.background = ExcelTab.styleArr[2];
								cells[iCol].style.background   = ExcelTab.styleArr[0];
								cells[iCol+1].style.background = ExcelTab.styleArr[2];
							}
							else
							{
								if ( i == 1 )
								{
									cells[iCol-1].style.background = ExcelTab.styleArr[2];
									cells[iCol].style.background   = ExcelTab.styleArr[1];
									cells[iCol+1].style.background = ExcelTab.styleArr[2];
								}
								else
								{
									cells[iCol-1].style.background = ExcelTab.styleArr[4];
									cells[iCol].style.background   = ExcelTab.styleArr[( i == 2 )? 2 : 4];
									cells[iCol+1].style.background = ExcelTab.styleArr[4];
								}
							}
						}
						else
						{
							cells[iCol].style.background = ExcelTab.styleArr[fActive? 2 : 4];
						}
					}
				}
			}
			
			with ( aTab[iTab].style )
			{
				cursor = ( fActive? "default" : "hand" );
				color  = ExcelTab.styleArr[3];
			}
		}
	}
};

/**
 * @access public
 * @static
 */
ExcelTab.fnMouseOverScroll = function( iCtl )
{
	frames['frScroll'].document.all.tdScroll[iCtl].style.color = ExcelTab.styleArr[7];
};

/**
 * @access public
 * @static
 */
ExcelTab.fnMouseOutScroll = function( iCtl )
{
	frames['frScroll'].document.all.tdScroll[iCtl].style.color = ExcelTab.styleArr[6];
};

/**
 * @access public
 * @static
 */
ExcelTab.fnMouseOverTab = function( iTab )
{
	if ( iTab != ExcelTab.tabCurrent )
	{
		var iCol = ExcelTab.fnTabToCol( iTab );
		
		with ( frames['frTabs'].document.all )
			tdTab[iTab].style.background = ExcelTab.styleArr[5];
	}
};

/**
 * @access public
 * @static
 */
ExcelTab.fnMouseOutTab = function( iTab )
{
	if ( iTab >= 0 )
	{
		var elFrom = frames['frTabs'].event.srcElement;
		var elTo   = frames['frTabs'].event.toElement;

		if ( ( !elTo ) ||
			 ( elFrom.tagName == elTo.tagName ) ||
			 ( elTo.tagName   == "A" && elTo.parentElement != elFrom ) ||
			 ( elFrom.tagName == "A" && elFrom.parentElement != elTo ) )
		{
			if ( iTab != ExcelTab.tabCurrent )
			{
				with ( frames['frTabs'].document.all )
					tdTab[iTab].style.background = ExcelTab.styleArr[1];
			}
		}
	}
};

/**
 * @access public
 * @static
 */
ExcelTab.fnSetActiveSheet = function( iSh )
{
	if ( iSh != ExcelTab.tabCurrent )
	{
		ExcelTab.fnSetTabProps( ExcelTab.tabCurrent, false );
		ExcelTab.fnSetTabProps( iSh, true );
		ExcelTab.tabCurrent = iSh;
	}
};
