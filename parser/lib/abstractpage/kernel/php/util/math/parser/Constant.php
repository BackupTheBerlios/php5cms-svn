<?php

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


using( 'util.math.parser.MathExpression' );


/**
 * @package util_math_parser
 */
 
class Constant extends MathExpression 
{
	/**
	 * @access public
	 */
	var $value;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Constant( $the_value ) 
	{
		$this->value = (double)$the_value;
		$this->prop["const"] = true;
	}
	
	
	/**
	 * @access public
	 */
	function evalf() 
	{
		return $this->value;
	}
} // END OF Constant


$GLOBALS["AP_MATHPARSER_VARIABLES"]["E"]       = new Constant( M_E        );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["PI"]      = new Constant( M_PI       );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["LOG2E"]   = new Constant( M_LOG2E    );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["LOG10E"]  = new Constant( M_LOG10E   );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["LN2"]     = new Constant( M_LN2      );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["LN10"]    = new Constant( M_LN10     );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["PI2"]     = new Constant( M_PI_2     );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["PI4"]     = new Constant( M_PI_4     );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["1PI"]     = new Constant( M_1_PI     );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["2PI"]     = new Constant( M_2_PI     );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["2SQRTPI"] = new Constant( M_2_SQRTPI );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["SQRT2"]   = new Constant( M_SQRT2    );
$GLOBALS["AP_MATHPARSER_VARIABLES"]["SQRT12"]  = new Constant( M_SQRT1_2  );

?>
