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
 * @package util
 */
 
/**
 * @access public
 * @static
 */
RegExp.patterns = 
{
	// common patterns
	htmltag				: /(&lt;[\w\/]+[ ]*[\w\=\"\'\.\/\;\: \)\(-]*&gt;)/gi,
	parameter			: /=("[ \w\'\.\/\;\:\)\(-]+"|'[ \w\"\.\/\;\:\)\(-]+')/gi,
	entity				: /&amp;([\w]+);/gi,
	comment				: /(&lt;\!--[\W _\w\=\"\'\.\/\;\:\)\(-]*--&gt;)/gi,
	whitespace			: /([\\f\\n\\r\\t\\v ])/g,
	quotes				: /^['"]|['"]$/g,
	email				: /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,

	// not yet tested
	number				: /[\+\-]?[\.]?[0-9]+[\.]?[0-9]*/gi,
	percentage			: /[\+\-]?[\.]?[0-9]+[\.]?[0-9]*\%/gi,
	quotedstring		: /(\"[^\"]*\")|(\'[^\']*\')'/gi,
	hexcolor			: /#[0-9a-fA-F]{6}/gi,
	rgbcolor			: /[Rr][Gg][Bb]\([^\)]*\)/gi,
	declarationblock	: /\{[^\}\{]*\}/gi,
	querystring			: /([^\\&]*)/i,
	email2				: /^[a-z0-9_\.-]+@([a-z0-9]+(\-*[a-z0-9]+)*\.)+[a-z]{2,}$/,
	date				: /^(3[01]|0[1-9]|[12]\d)\/(0[1-9]|1[012])\/\d{4}/, // dd/mm/yyyy
	iso8601date			: /^(\d{4})(\d{2})(\d{2})T(\d{2}):(\d{2}):(\d{2})/,
	ipadress			: /(\b(2[0-5][0-5]|1?\d\d?)\.){3}\b(2[0-5][0-5]|1?\d\d?)/
};
