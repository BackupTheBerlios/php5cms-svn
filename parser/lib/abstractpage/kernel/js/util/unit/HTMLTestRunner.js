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
|Authors: Joerg Schaible <joehni@mail.berlios.de>                      |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Class for an application running test suites reporting in HTML.
 *
 * @package util_unit
 */

/**
 * Constructor
 *
 * @access public
 */
HTMLTestRunner = function()
{
	TextTestRunner.call( this );
	
	this.mPrefix  = "";
	this.mPostfix = "";
};


HTMLTestRunner.prototype = new TextTestRunner();

/**
 * Write a header starting the application.
 * The function will add a \<pre\> tag in front of the header.
 *
 * @access public
 */
HTMLTestRunner.prototype.printHeader = function()
{
	this.setPrefix( "<pre>" );
	TextTestRunner.prototype.printHeader.call( this );
	this.setPrefix( "" );
};

/**
 * Write a footer at application end with a summary of the tests.
 * The function will add a \</pre\> tag at the end of the footer.
 *
 * @param  TestResult  result  The result of the test run.
 * @access public
 */
HTMLTestRunner.prototype.printFooter = function( result )
{
	this.setPostfix( "</pre>" );
	TextTestRunner.prototype.printFooter.call( this, result );
	this.setPostfix( "" );
};

/**
 * Set prefix of printed lines.
 *
 * @param  String  prefix  The prefix.
 * @access public
 */
HTMLTestRunner.prototype.setPrefix = function( prefix )
{
	this.mPrefix = prefix;
};

/**
 * Set postfix of printed lines.
 *
 * @param  String  postfix  The postfix.
 * @access public
 */
HTMLTestRunner.prototype.setPostfix = function( postfix )
{
	this.mPostfix = postfix;
};

/**
 * Write a line of text to the output stream.
 * The function will convert '\&' and '\<' to \&amp; and \&lt; and add
 * prefix and postfix to the string.
 *
 * @param  String  str  The text to print on the line.
 * @access public
 */
HTMLTestRunner.prototype.writeLn = function( str ) 
{ 
	str = str.toString();
	str = str.replace( /&/g, "&amp;" ); 
	str = str.replace( /</g, "&lt;"  ); 
	str = this.mPrefix + str + this.mPostfix;
	
	TextTestRunner.prototype.writeLn.call( this, str );
};
