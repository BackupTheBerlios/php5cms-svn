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
|Authors: Chris Bizer <chris@bizer.de>                                 |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Iterator for traversing models. 
 * This class can be used for iterating forward and backward trough RdfMemoryModels.
 * It should be instanced using the getIterator() method of a RdfMemoryModel.
 *
 * @package xml_rdf_lib_util
 */ 

class RdfStatementIterator extends PEAR
{
 	/**
	 * Reference to the RdfMemoryModel
	 *
	 * @var		object RdfMemoryModel
	 * @access	private
	 */	
    var $model;

 	/**
	 * Current position
	 * RdfStatementIterator does not use the build in PHP array iterator,
	 * so you can use serveral iterators on a single RdfMemoryModel.
	 *
	 * @var		integer
	 * @access	private
	 */	
    var $position;
   
  
   	/**
     * Constructor
     *
     * @param	object	RdfMemoryModel
	 * @access	public
     */
    function RdfStatementIterator( &$model ) 
	{
		$this->model    = $model;
		$this->position = -1;
	}
 
  	/**
   	 * Returns true if there are more statements.
	 *
   	 * @return	boolean
   	 * @access	public  
   	 */
  	function hasNext()
	{
  		if ( $this->position < count( $this->model->triples ) - 1 )
  			return true;
		else
			return false;
   	}

  	/**
   	 * Returns true if the first statement has not been reached.
	 *
   	 * @return	boolean
   	 * @access	public  
   	 */
  	function hasPrevious()
	{
  		if ( $this->position > 0 )
			return true;
		else
			return false;
	}  
   
  	/**
   	 * Returns the next statement.
	 *
   	 * @return	statement or null if there is no next statement.
   	 * @access	public  
   	 */
  	function next()
	{
  		if ( $this->position < count($this->model->triples ) - 1 ) 
		{
  			$this->position++;
			return $this->model->triples[$this->position];
		} 
		else 
		{
			return null;
		}
  	}

  	/**
   	 * Returns the previous statement.
	 *
   	 * @return	statement or null if there is no previous statement.
   	 * @access	public  
   	 */
  	function previous()
	{
    	if ( $this->position > 0 ) 
		{
  			$this->position--;
			return $this->model->triples[$this->position];
		} 
		else 
		{
			return null;
		}   
  	}

  	/**
   	 * Returns the current statement.
	 *
   	 * @return	statement or null if there is no current statement.
   	 * @access	public  
   	 */
  	function current()
	{
  		if ( ( $this->position >= 0 ) && ( $this->position < count( $this->model->triples ) ) )
			return $this->model->triples[$this->position];
		else
			return null;
   	}
   
  	/**
   	 * Moves the pointer to the first statement.
	 *
   	 * @return	void
   	 * @access	public  
   	 */
  	function moveFirst()
	{
  		$this->position = 0;
   	}

  	/**
   	 * Moves the pointer to the last statement.
	 *
   	 * @return	void
   	 * @access	public  
   	 */
  	function moveLast()
	{
  		$this->position = count( $this->model->triples ) - 1;
   	}
   
  	/**
   	 * Moves the pointer to a specific statement.
   	 * If you set an off-bounds value, next(), previous() and current() will return null.
	 *
   	 * @return	void
   	 * @access	public  
   	 */
  	function moveTo( $position )
	{
  		$this->position = $position;
   	}
   
  	/**
   	 * Returns the current position of the iterator.
	 *
   	 * @return	integer
   	 * @access	public  
   	 */
  	function getCurrentPosition()
	{
  	 	return $this->position;
   	}
} // END OF RdfStatementIterator

?>
