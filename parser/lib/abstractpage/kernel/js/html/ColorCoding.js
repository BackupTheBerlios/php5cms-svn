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
 * @package html
 */
 
/**
 * Constructor
 *
 * @access public
 */
ColorCoding = function()
{
	this.Base = Base;
	this.Base();
	
	this.css = new CSS();
	this.css.addRule( ".cc_paramvalue",  "color: #0000FF; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_tag",         "color: #000080; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_img",         "color: #800080; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_A",           "color: #008000; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_form",        "color: #FF8000; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_table",       "color: #008080; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_styletag",    "color: #800080; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_styleitem",   "color: #800080; font-family: 'Courier New'; font-size: 12px;" );
	this.css.addRule( ".cc_htmlcomment", "color: #808080; font-family: 'Courier New'; font-size: 12px; font-style: italic;" );
	this.css.addRule( ".cc_entity",      "color: #000000; font-family: 'Courier New'; font-size: 12px; font-weight: bold;"  );
};


ColorCoding.prototype = new Base();
ColorCoding.prototype.constructor = ColorCoding;
ColorCoding.superclass = Base.prototype;

/**
 * @access public
 */
ColorCoding.prototype.colorize = function( html )
{
	html = html.replace( /@/gi,   "_AT_" );
	html = html.replace( /#/gi, "_HASH_" );

	var htmltag = /(&lt;[\w\/]+[ ]*[\w\=\"\'\.\/\;\: \)\(-]*&gt;)/gi;
	html = html.replace( htmltag, "<span class=cc_tag>$1</span>" );

	var imgtag = /<span class=cc_tag>(&lt;IMG[ ]*[\w\=\"\'\.\/\;\: \)\(-]*&gt;)<\/span>/gi;
	html = html.replace( imgtag,"<span class=cc_img>$1</span>" );

	var formtag = /<span class=cc_tag>(&lt;[\/]*(form|input){1}[ ]*[\w\=\"\'\.\/\;\: \)\(-]*&gt;)<\/span>/gi;
	html = html.replace( formtag, "<br><span class=cc_form>$1</span>" );

	var tabletag = /<span class=cc_tag>(&lt;[\/]*(table|tbody|th|tr|td){1}([ ]*[\w\=\"\'\.\/\;\:\)\(-]*){0,}&gt;)<\/span>/gi;
	html = html.replace( tabletag, "<span class=cc_table>$1</span>" );

	//var Atag = /<span class=cc_tag>(&lt;(\/a&gt;|[\W _\w\=\"\'\.\/\;\:\)\(-]&gt;){1})<\/span>/gi;
	var Atag   = /<span class=cc_tag>(&lt;\/a&gt;){1}<\/span>/gi;
	html = html.replace( Atag, "<span class=cc_A>$1</span>" );

	var Atag = /<span class=cc_tag>(&lt;a [\W _\w\=\"\'\.\/\;\:\)\(-]+&gt;){1,}<\/span>/gi;
	html = html.replace( Atag, "<span class=cc_A>$1</span>" );

	var parameter = /=("[ \w\'\.\/\;\:\)\(-]+"|'[ \w\"\.\/\;\:\)\(-]+')/gi;
	html = html.replace( parameter, "=<span class=cc_paramvalue>$1</span>" );

	var entity = /&amp;([\w]+);/gi;
	html = html.replace( entity, "<span class=cc_entity>&amp;$1;</span>" );

	var comment = /(&lt;\!--[\W _\w\=\"\'\.\/\;\:\)\(-]*--&gt;)/gi;
	html = html.replace( comment, "<br><span class=cc_htmlcomment>$1</span>" );

	html = html.replace( /_AT_/gi,   "@" );
	html = html.replace( /_HASH_/gi, "#" );

	return html;
};
