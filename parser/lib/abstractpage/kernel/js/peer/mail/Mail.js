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
 * @package peer_mail
 */
 
/**
 * Constructor
 *
 * @access public
 */
Mail = function( to, cc, bc, su, bo, prio )
{
	this.Base = Base;
	this.Base();
	
	this.to = to;	// To:
	this.cc = cc;	// CC:
	this.bc = bc;	// BCC:
	this.su = su;	// Subject:
	this.bo = bo;	// Body:
	this.pr = prio;	// Priorität:
};


Mail.prototype = new Base();
Mail.prototype.constructor = Mail;
Mail.superclass = Base.prototype;

/**
 * @access public
 */
Mail.prototype.sendMail = function()
{
	var urlStr = this.to;

	if ( this.cc )
	{
		urlStr = this.formatMailurl(urlStr);
		urlStr += "body=" + escape( this.bo );
	}
	
	// Todo
	
	location = 'mailto:' + urlStr;
};

/**
 * @access public
 */
Mail.prototype.formatMailurl = function( Str )
{
	var Str;

	if ( Str.indexOf( "?" ) == -1 )
		Str += "?";
    else
		Str += "&";
    
	return Str;
};
