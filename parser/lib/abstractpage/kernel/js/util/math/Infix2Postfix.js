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
 * Infix to Postfix Conversion Class
 * - converts an infix (inorder) expression to postfix (postorder)
 * - for eg. '1*2+3' converts to '12*3+'
 * - valid Operators are +, -, *, /
 *
 * @package util_math
 */
 
/**
 * Constructor
 *
 * @access public
 */
Infix2Postfix = function()
{
	this.Base = Base;
	this.Base();
};


Infix2Postfix.prototype = new Base();
Infix2Postfix.prototype.constructor = Infix2Postfix;
Infix2Postfixsuperclass = Base.prototype;

/**
 * @access public
 * @static
 */
Infix2Postfix.convert = function( infixStr, postfixStr )
{
	var postfixStr = new Array();
	var stackArr   = new Array();
 
 	var postfixPtr = 0;
 	infixStr = infixStr.split( '' );
	
 	for ( var i = 0; i < infixStr.length; i++ )
 	{
  		if ( Infix2Postfix.isOperand( infixStr[i] ) )
  		{
   			postfixStr[postfixPtr] = infixStr[i];
   			postfixPtr++;
  		}
  		else
  		{
   			while ( ( !Infix2Postfix.isEmpty( stackArr ) ) && ( Infix2Postfix.precedence( Infix2Postfix.topStack( stackArr ), infixStr[i] ) ) )
   			{
    			postfixStr[postfixPtr] = Infix2Postfix.topStack( stackArr );
    			Infix2Postfix.popStack( stackArr );
    			postfixPtr++;
   			}
   
   			if ( ( !Infix2Postfix.isEmpty( stackArr ) ) && ( infixStr[i] == ")" ) )
   				Infix2Postfix.popStack( stackArr );
   			else
   				Infix2Postfix.pushStack( stackArr, infixStr[i] );
  		}
 	}
 
 	while ( !Infix2Postfix.isEmpty( stackArr ) )
 	{
  		postfixStr[postfixStr.length] = Infix2Postfix.topStack( stackArr );
  		Infix2Postfix.popStack( stackArr );
 	}
 
 	var returnVal = '';
 
 	for ( var i = 0; i < postfixStr.length; i++ )
 		returnVal += postfixStr[i];
 
 	return ( returnVal );
};

/**
 * @access public
 * @static
 */
Infix2Postfix.pushStack = function( stackArr, ele )
{
	stackArr[stackArr.length] = ele;
};

/**
 * @access public
 * @static
 */
Infix2Postfix.popStack = function( stackArr )
{
	var _temp = stackArr[stackArr.length - 1];
	delete stackArr[stackArr.length - 1];
 	stackArr.length--;

	return ( _temp );
};

/**
 * @access public
 * @static
 */
Infix2Postfix.topStack = function( stackArr )
{
	return ( stackArr[stackArr.length - 1] );
};

/**
 * @access public
 * @static
 */
Infix2Postfix.isEmpty = function( stackArr )
{
	return ( ( stackArr.length == 0 )? true : false );
};

/**
 * @access public
 * @static
 */
Infix2Postfix.isOperand = function( who )
{
	return ( !Infix2Postfix.isOperator( who )? true : false );
};

/**
 * @access public
 * @static
 */
Infix2Postfix.isOperator = function( who )
{
	return ( ( who == "+" || who == "-" || who == "*" || who == "/" || who == "(" || who == ")" )? true : false );
};

/**
 * @access public
 * @static
 */
Infix2Postfix.precedence = function( char1, char2 )
{
	var char1_index, char2_index;
	var _def_prcd = "-+*/";
 
 	for ( var i = 0; i < _def_prcd.length; i++ )
 	{
		if ( char1 == _def_prcd.charAt( i ) ) 
			char1_index = i;

		if ( char2 == _def_prcd.charAt( i ) ) 
			char2_index = i;
 	}

	if ( ( ( char1_index == 0 ) || ( char1_index == 1 ) ) && ( char2_index > 1 ) ) 
		return false;
	else 
		return true;
};
