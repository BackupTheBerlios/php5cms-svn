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
 * Generic N-ary tree node.
 *
 * @package util
 */
 
class TreeNode extends PEAR
{
	/**
	 * @access private
	 */
	var $_firstChild;

	/**
	 * @access private
	 */
	var $_lastChild;

	/**
	 * @access private
	 */
	var $_prior;

	/**
	 * @access private
	 */
	var $_next;

	/**
	 * @access private
	 */
	var $_parent;
	
	
	/**
	 * Determine whether or not this class is empty.
	 *
	 * @access public
	 */
	function childless() 
	{
		if ( $this->_firstChild === null )
			return true;
		else
			return false;
	}

	/**
	 * Retrieve parent node.
	 *
	 * @access public
	 */
	function &parent() 
	{
		return $this->_parent;
	}

	/**
	 * Retrieve prior sibling.
	 *
	 * @access public
	 */
	function &prior() 
	{
		return $this->_prior;
	}

	/**
	 * Retrieve next sibling.
	 *
	 * @access public
	 */
	function &next() 
	{
		return $this->_next;
	}

	/**
	 * Retrieve first child.
	 *
	 * @access public
	 */
	function &firstChild() 
	{
		return $this->_firstChild;
	}

	/**
	 * Retrieve last child.
	 *
	 * @access public
	 */
	function &lastChild() 
	{
		return $this->_lastChild;
	}

	/**
	 * Retrieve all child nodes.
	 *
	 * @access public
	 */
	function &nodes() 
	{
		$result = array();
		
		if ( $this->_firstChild === null ) 
			return $result;
		
		$node =& $this->_firstChild;
		
		for (;;) 
		{
			$result[] =& $node;
			
			if ( $node->_next === null )
				break;
			else
				$node =& $node->_next;
		}
		
		return $result;
	}

	/**
	 * Retrieve all descendant nodes.
	 *
	 * @access public
	 */
	function &descendants() 
	{
		$result =  array();
		$nodes  =& $this->nodes();
		
		for ( $i = 0; $i < count( $nodes ); $i++ ) 
		{
			$result[] =& $nodes[$i];
			$merge    =& $nodes[$i]->descendants();
			
			for ( $j = 0; $j < count( $merge ); $j++ )
				$result[] =& $merge[$j];
		}
		
		return $result;
	}

	/**
	 * Retrieve the top-level root node.
	 *
	 * @access public
	 */
	function &root() 
	{
		$result =& $this;
		
		for (;;) 
		{
			$parent =& $result->parent();
			
			if ( $parent !== null )
				$result =& $parent;
			else
				break;
		}
		
		return $result;
	}

	/**
	 * Retrieve the position of this node relative to siblings.
	 *
	 * @access public
	 */
	function position() 
	{
		$result =  0;
		$node   =& $this->_prior;
		
		while ( $node !== null ) 
		{
			$result++;
			$node =& $node->_prior;
		}
		
		return $result;
	}

	/**
	 * Prune all child nodes.
	 *
	 * @access public
	 */
	function purge() 
	{
		$nodes =& $this->descendants();
		
		for ( $i = 0; $i < count( $nodes ); $i++ ) 
		{
			unset( $nodes[$i]->_parent );
			unset( $nodes[$i]->_prior  );
			unset( $nodes[$i]->_next   );
			unset( $nodes[$i]->_firstChild );
			unset( $nodes[$i]->_lastChild  );
			unset( $nodes[$i] );
		}
		
		unset( $this->_firstChild );
		unset( $this->_lastChild  );
	}

	/**
	 * Prune this node from its parent.
	 *
	 * @access public
	 */
	function prune() 
	{
		if ( $this->_parent === null ) 
			return;
		
		if ( $this->_prior === null && $this->_next === null ) 
		{
			// only child
			$this->_parent->purge();
		} 
		else if ( $this->_prior === null ) 
		{
			// first child
			$this->_parent->_firstChild =& $this->_next;
			unset( $this->_next->_prior );
		} 
		else if ( $this->_next === null ) 
		{
			// last child
			$this->_parent->_lastChild =& $this->_prior;
			unset( $this->_prior->_next );
		} 
		else 
		{
			// middle child
			$this->_prior->_next =& $this->_next;
			$this->_next->_prior =& $this->_prior;
		}
		
		unset( $this->_parent );
		unset( $this->_prior  );
		unset( $this->_next   );
	}

	/**
	 * Copy the tree rooted at this node.
	 *
	 * @access public
	 */
	function &copy() 
	{
		// member-wise copy this node, and disassociate with parents, siblings, and children
		$result = $this;
		
		unset( $result->_parent     );
		unset( $result->_firstChild );
		unset( $result->_lastChild  );
		unset( $result->_prior      );
		unset( $result->_next       );

		// Now copy our children and append them to result.
		$children =& $this->nodes();
		
		for ( $i = 0; $i < count( $children ); $i++ )
			$result->append( $children[$i]->copy() );
		
		return $result;
	}

	/**
	 * Replace this node with another.
	 *
	 * @access public
	 */
	function assign( &$node ) 
	{
		// first copy the node
		$result =& $node->copy();

		// then member-wise assign to $this
		$parent =& $this->_parent;
		$prior  =& $this->_prior;
		$next   =& $this->_next;
		
		$this = $result;
		$this->_parent =& $parent;
		$this->_prior  =& $prior;
		$this->_next   =& $next;

		// finally, make our (new) children point back to us
		$children =& $this->nodes();
		
		for ( $i = 0; $i < count( $children ); $i++ )
			$children[$i]->_parent =& $this;
	}

	/**
	 * Append child node.
	 *
	 * @access public
	 */
	function &append( &$treeNode ) 
	{
		if ( is_array( $treeNode ) ) 
		{
			$result = array();
			
			for ( $i = 0; $i < count( $treeNode ); $i++ )
				$result[] =& $this->append( $treeNode[$i] );
			
			return $result;
		} 
		else if ( get_class( $treeNode ) != "treenode" && !is_subclass_of( $treeNode, "treenode" ) ) 
		{
			if ( is_null( $treeNode ) ) 
				$info = "NULL";
			else if ( is_object( $treeNode ) ) 
				$info = get_class( $treeNode );
			else 
				$info = gettype( $treeNode );
			
			return false;
		} 
		else 
		{
			// Ensure that the node hasn't been appended somewhere else already.
			// If it has, copy it first, and append that.
			if ( $treeNode->_parent !== null ) 
				$treeNode =& $treeNode->copy();

			$treeNode->_parent =& $this;
			
			if ( $this->_firstChild === null ) 
			{
				unset( $treeNode->_next  );
				unset( $treeNode->_prior );
				
				$this->_firstChild =& $treeNode;
				$this->_lastChild  =& $treeNode;
			} 
			else 
			{
				$treeNode->_prior =& $this->_lastChild;
				unset( $treeNode->_next );
				$this->_lastChild->_next =& $treeNode;
				$this->_lastChild =& $treeNode;
			}

			return $treeNode;
		}
	}

	/**
	 * Insert sibling node before this node.
	 *
	 * @access public
	 */
	function &insert( &$treeNode ) 
	{
		if ( is_array( $treeNode ) ) 
		{
			$result = array();
			
			for ( $i = 0; $i < count( $treeNode ); $i++ )
				$result[] =& $this->insert( $treeNode[$i] );
			
			return $result;
		} 
		else if ( get_class( $treeNode ) != "treenode" && !is_subclass_of( $treeNode, "treenode" ) ) 
		{
			return false;
		} 
		else 
		{
			// Ensure that the node hasn't been inserted somewhere else already.
			// If it has, copy it first, and insert that.
			if ( $treeNode->_parent !== null ) 
				$treeNode =& $treeNode->copy();

			// Ensure that we have a parent.  If not, we can't insert
			// somebody before us, now can we?
			if ( $this->_parent === null ) 
				return false;
			
			$treeNode->_next   =& $this;
			$treeNode->_parent =& $this->_parent;
			
			if ( $this->_prior === null ) 
			{
				// inserting new first child
				$this->_parent->_firstChild =& $treeNode;
				unset( $treeNode->_prior );
			} 
			else 
			{
				$this->_prior->_next =& $treeNode;
				$treeNode->_prior    =& $this->_prior;
			}
			
			$this->_prior =& $treeNode;
			return $treeNode;
		}
	}

	/**
	 * Perform a depth-first traversal.
	 *
	 * @access public
	 */
	function depthFirst( &$treeIterator, $level = 0 ) 
	{
		$top  =  $level;
		$node =& $this;

		// Iterative tree traversal:
		//   1. Visit node downward.
		//   2. If node is not a leaf, move to first child and restart.
		//   3. Visit node upward.
		//   4. If on original level, stop.
		//   5. If not the rightmost sibling, move to right sibling and restart.
		//   6. Move to parent, go to step 3.
		for (;;) 
		{
			// 1. Visit node downward
			$down = $treeIterator->down( $node, $level );

			if ( $node->_firstChild !== null && $down ) 
			{
				// 2. Move to first child and restart
				$level++;
				$node =& $node->_firstChild;
				
				continue;
			}

			for (;;) 
			{
				// 3. Visit node upward.
				$up = $treeIterator->up( $node, $level );
				
				if ( !$up ) 
					return;

				// 4. If on original level, stop.
				if ( $level == $top ) 
					return;

				// 5. If not on the rightmost sibling, move to right sibling and restart.
				if ( $node->_next !== null ) 
				{
					$node =& $node->_next;
					continue 2;
				}

				// 6. Move to parent, go to step 3.
				// internal inconsistency
				if ( $node->_parent === null ) 
				{
				}
				
				$node =& $node->_parent;
				$level--;
			}
		}
	}
} // END OF TreeNode

?>
