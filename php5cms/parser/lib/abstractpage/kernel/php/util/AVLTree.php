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
 * For documentation of the abstract data type "AVL tree"
 * see http://www.osocs.org/examples/avltree/lecture.html
 *
 * Usage:
 *
 * class NumTree extends AVLTree  
 * {
 * 		// Constructor
 *     	function NumTree( $val )  
 * 		{
 *     		$this->AVLTree();
 *         
 * 			$this->data  = $val;
 *         		$this->depth = 1;
 *     	}
 *     
 * 	
 *     	function add( $val )  
 * 		{
 *         	if ( $val == $this->data )  
 * 			{
 *             	echo "$val already in tree<BR>\n * ";
 *             	return ;
 *         	}
 * 
 * 	    	if ( $val < $this->data )  
 * 			{
 * 	        	if ( $this->left === null )
 * 				{
 * 	        		$this->left =& new NumTree( $val );
 * 				}
 * 	        	else  
 * 				{
 * 	            	$this->left->add( $val );
 * 	            	$this->balance();
 * 	        	}
 * 	    	} 
 * 			else  
 * 			{
 * 		    	assert( $val > $this->data );
 * 		    
 * 	        	if ( $this->right === null )
 * 				{
 * 	        		$this->right =& new NumTree( $val );
 * 				}
 * 	        	else  
 * 				{
 * 	            	$this->right->add( $val );
 * 	            	$this->balance();
 * 	        	}
 * 	    	}
 * 	    
 *    	    $this->getDepthFromChildren();
 *     	}
 * } // END OF NumTree
 * 
 * 
 * $n =& new NumTree( 8 );
 * echo $n->toString();
 * $n->add( 9 );
 * echo $n->toString();
 * $n->add( 10);
 * echo $n->toString();
 * $n->add( 2 );
 * echo $n->toString();
 * $n->add( 1 );
 * echo $n->toString();
 * $n->add( 5 );
 * echo $n->toString();
 * $n->add( 3 );
 * echo $n->toString();
 * $n->add( 6 );
 * // $noisy = true;
 * echo $n->toString();
 * $n->add( 4 );
 * echo $n->toString();
 * $n->add( 7 );
 * echo $n->toString();
 * $n->add( 11);
 * echo $n->toString();
 * $n->add( 12);
 * echo $n->toString();
 *
 * @package util
 */
 
class AVLTree extends PEAR 
{
	/**
	 * @access public
	 */
	var $left;
	
	/**
	 * @access public
	 */
	var $right;
	
	/**
	 * @access public
	 */
	var $depth;
	
	/**
	 * @access public
	 */
	var $data;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function AVLTree() 
	{
	    $this->left  = null;
	    $this->right = null;
	    $this->depth = 0;
	    $this->data  = null;
	}
	

	/**
	 * @access public
	 */	
	function balance()  
	{
		$ldepth = $this->left !== null
		        ? $this->left->depth
		        : 0;
		        
		$rdepth = $this->right !== null
		        ? $this->right->depth
		        : 0;
		 
		// LR or LL rotation       
		if ( $ldepth > $rdepth + 1 )  
		{
	        $lldepth = $this->left->left !== null
	                 ? $this->left->left->depth
	                 : 0;

	        $lrdepth = $this->left->right !== null
	                 ? $this->left->right->depth
	                 : 0;

			// LR rotation
			if ( $lldepth < $lrdepth ) 
			{
				$this->left->rotateRR(); 	// consist of a RR rotation of the left child ...
			}                     			// ... plus a LL rotation of this node, which happens anyway 

			$this->rotateLL();
		}
		// RR or RL rorarion 
		else if ( $ldepth + 1 < $rdepth ) 
		{
	        $rrdepth = $this->right->right !== null
	                 ? $this->right->right->depth
	                 : 0;

	        $rldepth = $this->right->left !== null
	                 ? $this->right->left->depth
	                 : 0;

			// RR rotation
			if ( $rldepth > $rrdepth ) 
			{
				$this->right->rotateLL(); 	// consist of a LL rotation of the right child ...
			}                     			// ... plus a RR rotation of this node, which happens anyway 

			$this->rotateRR();
		}	    
	}
	
	/**
	 * The left side is too long => rotate from the left (_not_ leftwards).
	 *
	 * @access public
	 */
	function rotateLL()  
	{
	    $data_before  =& $this->data;
	    $right_before =& $this->right;
	    
	    $this->data   =& $this->left->data;
	    $this->right  =& $this->left;
	    $this->left   =& $this->left->left;
	    
		$this->right->left  =& $this->right->right;
	    $this->right->right =& $right_before;
	    $this->right->data  =& $data_before;
	    
		$this->right->updateInNewLocation();
	    $this->updateInNewLocation();
	}
	
	/**
	 * The right side is too long => rotate from the right (_not_ rightwards).
	 *
	 * @access public
	 */
	function rotateRR()  
	{
	    $data_before =& $this->data;
	    $left_before =& $this->left;
	    
	    $this->data  =& $this->right->data;
	    $this->left  =& $this->right;
	    $this->right =& $this->right->right;
	    
		$this->left->right =& $this->left->left;
	    $this->left->left  =& $left_before;
	    $this->left->data  =& $data_before;
	    
		$this->left->updateInNewLocation();
	    $this->updateInNewLocation();
	}
	
	/**
	 * @access public
	 */
	function updateInNewLocation()  
	{
	    $this->getDepthFromChildren();
	}
	
	/**
	 * @access public
	 */
	function getDepthFromChildren()  
	{
	    $this->depth = ( $this->data !== null )? 1 : 0;

	    if ( $this->left !== null )
	    	$this->depth = $this->left->depth + 1;

	    if ( $this->right !== null && $this->depth <= $this->right->depth )
	    	$this->depth = $this->right->depth + 1;
	}

	/**
	 * @access public
	 */
	function toString()	 
	{
	    $s     = "<table border><tr>\n" . $this->toTD( 0 ) . "</tr>\n";
	    $depth = $this->depth - 1;
		
	    for ( $d = 0; $d < $depth; ++$d )  
		{
	        $s .= "<tr>";
	        
        	$s .= $this->left !== null
        	    ? $this->left->toTD( $d )
        	    : "<td></td>";
	        	
        	$s .= $this->right !== null
        	    ? $this->right->toTD( $d )
        	    : "<td></td>";
	        	
	        $s .= "</tr>\n";
	    }
	    
	    $s .= "</table>\n";
	    return $s;
	}
	
	/**
	 * @access public
	 */	
	function toTD( $depth )  
	{
		if ( $depth == 0 ) 
		{
			$s  = "<td align=center colspan=" . $this->getNLeafs() . ">";
			$s .= $this->data . "[" . $this->depth . "]</td>\n";
		} 
		else  
		{
			if ( $this->left !== null )
		    	$s = $this->left->toTD( $depth - 1);
		    else
		        $s = "<td></td>";

			if ( $this->right !== null ) 
			{
		    	$s .= $this->right->toTD( $depth - 1);
		    } 
			else  
			{
		    	if ( $this->left !== null )
			        $s .= "<td></td>";
		    }
		}
		
		return $s;
	}

	/**
	 * @access public
	 */	
	function getNLeafs() 
	{
	    if ( $this->left !== null ) 
		{
	    	$nleafs = $this->left->getNLeafs();
	    	
		    if ( $this->right !== null )
		    	$nleafs += $this->right->getNLeafs();
		    else
		    	++$nleafs; // plus one for the right "stump"
		} 
		else 
		{
		    if ( $this->right !== null )
		    	$nleafs = $this->right->getNLeafs() + 1; // plus one for the left "stump"
		    else
		    	$nleafs = 1; // this node is a leaf
		}
		
	    return $nleafs;
	}
} // END OF AVLTree

?>
