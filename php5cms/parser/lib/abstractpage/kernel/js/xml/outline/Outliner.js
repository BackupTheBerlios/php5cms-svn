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
 * @package xml_outline
 */
 
/**
 * Constructor
 *
 * @access public
 */
Outliner = function()
{
	this.Base = Base;
	this.Base();
};


Outliner.prototype = new Base();
Outliner.prototype.constructor = Outliner;
Outliner.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Outliner.blockview = function( node )
{
	var s = '';
	var l = node._children.length;
	var p = 0;

	for ( var i in node )
	{
		if ( i.charAt( 0 ) != '_' )
		{
    		s += Outliner.blockview( node[i] );
    		p++;
  		}
	}
	
	for ( var i = 0; i < l; i++ )
	{
    	s += Outliner.blockview( node._children[i] );
		p++;
	}

	if ( p > 0 )
	{
    	list0[listl] = '<TABLE style="width:99%" cellspacing=0 cellpadding=0><TR>' +
			'<TD class="w" style="width:0px" onclick="this.parentNode.parentNode.parentNode.parentNode.innerHTML=list1['+listl+']" valign=top>' +
			'<NOBR>&nbsp;<img src="../img/icons16x16/arrow_bottom.gif" width=16 height=16 border=0>&nbsp;</NOBR></TD>' +
			'<TD style="width:100%"><TABLE style="width:99%" cellspacing=0 class="m">'+s+'</TABLE>' +
			'</TD></TR></TABLE>';
    	
		
		s = list1[listl] = '<NOBR><SPAN onclick="this.parentNode.parentNode.innerHTML=list0['+listl+'];event.cancleBubble=true" class="w">&nbsp;<img src="../img/icons16x16/arrow_right.gif" width=16 height=16 border=0>&nbsp;</SPAN>' +
			node._text.substr( 0, 80 ) + ( ( node._text.length > 80 )? '...' : '' ) + '</NOBR>';

		if ( node._name == '_' )
			s = '<TR><TD class="m" colspan=2><SPAN>' + s + '</SPAN></TD></TR>';
		else
			s = '<TR><TH class="m" colspan=2><B>' + node._name + '</B><BR /><SPAN>' + s + '</SPAN></TH></TR>';

		listl++;
	}
	else
	{
		if ( node._name == '_' )
			s = '<TR><TD class="m" colspan=2>' + node._text + '</TD></TR>';
		else
			s = '<TR><TH class="m"><b>' + node._name + '</b></TH><TD class="m">' + node._text + '</TD></TR>';
	}

	return s;
};

/**
 * @access public
 * @static
 */
Outliner.convertToEscapes = function( str )
{
    // start with &
    var gt = -1;
    while ( str.indexOf( "&", gt + 1 ) > -1 )
	{
        gt = str.indexOf( "&", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "&amp;";
        newStr  = newStr + str.substr( gt + 1, str.length );
        str     = newStr;
    }

    // now <
    gt = -1;
    while ( str.indexOf( "<", gt + 1 ) > -1 )
	{
        var gt = str.indexOf( "<", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "&lt;";
        newStr  = newStr + str.substr( gt + 1, str.length );
        str     = newStr;
    }

    // now >
    gt = -1;
    while ( str.indexOf( ">", gt + 1 ) > -1 )
	{
        var gt = str.indexOf( ">", gt + 1 );
        var newStr = str.substr( 0, gt );
		
        newStr += "&gt;";
        newStr  = newStr + str.substr( gt + 1, str.length );
        str     = newStr;
    }

    return str;
};
