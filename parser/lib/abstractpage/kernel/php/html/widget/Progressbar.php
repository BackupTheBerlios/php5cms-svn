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


/**
 * ProgressBar Class (works ONLY under IE4 & more)
 * Note: Don't forget to use set_time_limit() on long processes
 *
 * Example:
 *
 * $prb1 = new ProgressBar( 200, 40 );
 * $prb1->left = 50;
 * $prb1->top  = 60;
 * $prb1->min  = 20;
 * $prb1->max  = 165;
 * $prb1->drawHtml();
 *
 * @set_time_limit( 300 );
 * 
 * for ( $i = 20; $i <= 165; $i++ )
 * {
 * 		$prb1->moveIt( $i );
 *   	
 * 		for ( $j = 0; $j < 100000; $j++ )
 * 			$k = $j;
 *	}
 *
 * @package html_widget
 */
 
class ProgressBar extends PEAR
{
	/**
	 * @access public
	 */
  	var $width;
	
	/**
	 * @access public
	 */
  	var $height;

	/**
	 * @access public
	 */
  	var $left = 0;
	
	/**
	 * @access public
	 */
  	var $top = 0;  	

	/**
	 * @access public
	 */
	var $min = 0;
	
	/**
	 * @access public
	 */
  	var $max = 100;
	
	/**
	 * @access public
	 */
  	var $step = 1;
	
	/**
	 * progress bar color
	 * @access public
	 */
  	var $color = '#0A246A';
	
	/**
	 * background color
	 * @access public
	 */
  	var $bgr_color = '#FFFFFF';
	
	/**
	 * text color
	 * @access public
	 */
  	var $txt_color = '#FF0000';
	
	/**
	 * border color
	 * @access public
	 */
  	var $brd_color = '#000000';

	/**
	 * @access private
	 */
  	var $_code;
	
	/**
	 * @access private
	 */
  	var $_val = 0;


	/**
	 * Constructor
	 *
	 * @param  int    $width
	 * @param  int    $height
	 * @access public
	 */
  	function ProgressBar( $width, $height )
  	{
		$this->width  = $width;
    	$this->height = $height;
    
		$this->_code = md5( uniqid( '' ) );
  	}


	/**
	 * @access public
	 */
  	function setVal( $val )
  	{
    	if ( $val > $this->max )
			$val = $this->max;
    
		if ( $val < $this->min )
			$val = $this->min;
    
		$this->_val = $val;
  	}

	/**
	 * @access public
	 */
  	function moveIt( $val )
  	{
  	  	$this->setVal( $val );
    
		$prc = $this->_calculatePercent();
    	$cw  = $this->_calculateWidth();
    
		echo '<script language="javascript">ptxt' . $this->_code . '.innerText="'  . $prc . '%";</script>' . "\n";
    	echo '<script language="javascript">pbar' . $this->_code . '.style.width=' . $cw  . ';</script>'   . "\n";
    
		flush();
  	}

	/**
	 * @access public
	 */
  	function getHtml()
  	{
  		$this->setVal( $this->_val );
    	
		$prc = $this->_calculatePercent();
    	$cw  = $this->_calculateWidth();

  		$hh = $this->height;
  	
		if ( $hh <= 100 ) 
			$koef = 0.0017 * $hh + 0.64; 
		else 
			$koef = 0.81;
  	
		$px = round( $hh * $koef );

  		$size1 = 'width:' . $this->width . 'px;height:' . $this->height . 'px;';
  		$size2 = 'width:' . ( $this->width - 2 ) . 'px;height:' . ( $this->height - 2 ) . 'px;';
  		
		$position1 = 'position:absolute;top:' . $this->top . ';left:' . $this->left . ';';
  		$position2 = 'position:absolute;top:' . ( $this->top + 1 ) . ';left:' . ( $this->left + 1 ) . ';';
    	
		$font = 'font-family:Tahoma;font-weight:bold;font-size:' . $px . 'px;';

    	$style1 = $position1 . $size1 . $font . 'border:1px solid ' . $this->brd_color . ';text-align:center;background-color:' . $this->bgr_color . ';';
    	$style2 = $position2 . $size2 . $font . 'color: ' . $this->txt_color . ';z-index:1;text-align:center;';
    	$style3 = $position2 . $font  . 'width:' . $cw . 'px; height: ' . ( $this->height - 2 ) . 'px; background-color: ' . $this->color . ';z-index:0;';

    	return
    		'<div id="pbrd' . $this->_code . '" style="' . $style1 . '"></div>'.
    		'<div id="ptxt' . $this->_code . '" style="' . $style2 . '">' . $prc . '%</div>'.
    		'<div id="pbar' . $this->_code . '" style="' . $style3 . '"></div>';
  	}

	/**
	 * @access public
	 */
  	function drawHtml()
  	{
    	echo $this->getHtml();
  	}
  
  
  	// private methods

	/**
	 * @access private
	 */
  	function _calculatePercent()
  	{
    	$p = round( ( $this->_val - $this->min ) / ( $this->max - $this->min ) * 100 );
    
		if ( $p > 100 )
			$p = 100;
    
		return $p;
  	}

	/**
	 * @access private
	 */
  	function _calculateWidth()
  	{
    	$w = round( ( $this->_val - $this->min ) * ( $this->width - 2 ) / ( $this->max - $this->min ) );
    
		if ( $this->_val <= $this->min )
			$w = 0;
			
    	if ( $this->_val >= $this->max )
			$w = $this->width - 2;
    
		return $w;
  	}
} // END OF ProgressBar

?>
