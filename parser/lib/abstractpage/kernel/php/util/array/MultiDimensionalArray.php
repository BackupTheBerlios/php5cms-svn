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


using( 'util.array.ArrayUtil' );


/**
 * A class that simulates a multi-dimentional database using array's.
 *
 * The problem goes as follows -
 *  1. I had to store class names, it's associated db table, and the filename of the class.
 *  2. Usually only 1 of these factors was known.
 *  3. The data was in continuous flux.
 *  4. It needed to be faster and more flexible than a database.
 *  5. I could not think of a better way to do it. (there probably is)
 * 
 * How it works -
 * Build an array of associative array's that looks something like:
 * $aFoo = array();
 * $aFoo[0] = array();
 * $aFoo[0]['fieldName'] = 'foo'; // Insert your field name here. The key 'fieldName' has to be there.
 * $aFoo[0]['required']  = true;  // True or False? Should the field be required when storing data?
 * $aFoo[1] = array();
 * $aFoo[1]['fieldName'] = 'bar'; // The name of another field to store data in.
 * $aFoo[1]['required']  = false; // Is this one required?
 * 
 * Then create the object like:
 * $oArrayCube = new MultiDimensionalArray( $aFoo ); // $aFoo is used as a blueprint for the data structures.
 * 
 * How to use it -
 * 
 * Storing data:
 * $aData = array();
 * $aData['foo'] = 'someData';
 * $aData['bar'] = 'moreData';
 * $key = $oArrayCube->insert( $aData ); // Returns an integer that is the address to get it back out again. 
 * $aData = null; // Go ahead and kill the original, its stored.
 * 
 * Getting data:
 * $aData = $oArrayCube->get( $key ); // Now all the data is back.
 *  or
 * $aKey = array(); // Build an array of keys to get
 * $aKey[0] = 1;
 * $aKey[1] = 2;
 * $aData = $oArrayCube->get( $aKey );  // Now $aData is an array of associative arrays containing data.
 * 
 * Updating data:
 * $oArrayCube->insert( $aData, $key ); // Store the data in $key, do not generate a new $key.
 * 
 * Finding data:
 * If you don't have a key to get the data, then you have to search for it.
 * $aKeys = $oArrayCube->search( 'someData' ); // Returns an array of keys that matched.  Look in all fields.
 *  or
 * $aKeys = $oArrayCube->search( 'someData', 'foo' ) // Returns an array of keys that matched. Only look in field 'foo'.
 * After you have the matching keys, then you can $oArrayCube->get() them.
 * 
 * Removing data:
 * $oArrayCube->delete( $key ); // Returns True/False. Pretty self-explanatory
 * 
 * Reindexing:
 * $oArrayCube->reindex(); // For the most part, this should not have to be done. Keys #'s may change. Clears out empty keys.
 *
 * @package util_array
 */
 
class MultiDimensionalArray extends PEAR
{
	/**
	 * array to store data
	 * @access private
	 */
 	var $_aData;
	
	/**
	 * array to store indexes
	 * @access private
	 */
 	var $_aIndex;
	
	/**
	 * array to store structure
	 * @access private
	 */
 	var $_aSettings;
	
	/**
	 * the last key to be created
	 * @access private
	 */
 	var $_lastKey;


 	/**
	 * Constructor
	 *
	 * Builds structure and sets rules; Input - Array of associative 
	 * arrays with keys: 'fieldName','required'.
	 *
  	 * @param  aDimentions array
	 * @access public
  	 */
 	function MultiDimensionalArray( $aDimentions )
  	{
   		if ( !is_array( $aDimentions ) )
    		return false;

   		$this->_lastKey = 0;
   		$this->_aData   = array();
   		$this->_aIndex  = array();
		
   		$this->_aSettings = array();
   		$this->_aSettings['fields'] = array();

   		foreach ( $aDimentions as $key => $data )
    	{
     		if ( !isset( $data['fieldName'] ) || !isset( $data['required'] ) )
      			continue;
      
     		$this->_aIndex[$data['fieldName']] = array();
     		$this->_aSettings['fields'][$data['fieldName']] = $data['required'];
    	}
  	}

	
 	/**
	 * Retreive a cell.
	 *
  	 * @return array
  	 * @param  iKey int
	 * @access public
  	 */
 	function get( $iKey )
  	{
   		$result = array();
   
   		if ( is_array( $iKey ) )
    	{
     		foreach ( $iKey as $row => $data )
      		{
       			if ( !isset( $data['key'] ) || !is_array( $data['key'] ) )
         			continue;

       			foreach ( $data['key'] as $keyRow => $keyData )
        		{
         			$tmp = $this->get( $keyData );
					
         			if ( !$tmp )
          				continue;
          
         			$result[] = $tmp;
        		}
      		}
    	}

   		if ( is_int( $iKey ) )
     		$result = $this->_aData[$iKey];

   		return $result;
 	}

 	/**
	 * Lookup keys matching fieldValue; (optional) fieldName - specify field to look in.
	 *
  	 * @return array
  	 * @param  fieldValue array||string
  	 * @param  fieldName string
	 * @access public
  	 */
 	function search( $fieldValue, $fieldName = null )
  	{
   		$aKeysFound = array();
   		$c = 0;
   
   		if ( !isset( $fieldName ) )
    	{
     		foreach ( $this->_aSettings['fields'] as $field => $bIsRequired )
      		{
       			if ( isset( $this->_aIndex[$field][$fieldValue] ) )
        		{
         			$aKeysFound[$c] = array();
         			$aKeysFound[$c]['key'] = $this->_aIndex[$field][$fieldValue];
         			$aKeysFound[$c++]['field'] = $field;
        		}
      		}
    	}
   		else
    	{
     		if ( isset( $this->_aIndex[$fieldName][$fieldValue] ) )
      		{
       			$aKeysFound[$c] = array();
       			$aKeysFound[$c]['key'] = $this->_aIndex[$fieldName][$fieldValue];
       			$aKeysFound[$c++]['field'] = $fieldName;
      		}
    	}
   
   		return ( $c > 0 )? $aKeysFound : false;
  	}

 	/**
	 * Store the contents of aData.
	 *
  	 * @return int
  	 * @param  aData array
  	 * @param  key int
	 * @access public
  	 */
 	function insert( $aData, $key = null )
  	{
   		$iKey = !$key? ++$this->_lastKey : $key;

   		if ( !$this->bIsDataValid( $aData ) )
     		return false;

   		$this->_aData[$iKey] = $aData;
   		$this->_indexAdd( $iKey, $aData );
   
   		return $iKey;
  	}

 	/**
	 * Delete the specified cell and any associated indexes.
	 *
  	 * @return bool
  	 * @param  iKey int
	 * @access public
  	 */
 	function delete( $iKey )
  	{
   		if ( !isset( $this->_aData[$iKey] ) )
     		return false;
    
   		$this->_indexRemove( $iKey );
   		$this->_aData = ArrayUtil::deleteKey( $this->_aData, $iKey );
   
   		return true;
  	}

 	/**
	 * Rebuild data indexes.
	 *
  	 * @return bool
	 * @access public
  	 */
 	function reindex()
  	{
   		if ( !isset( $this->_aData ) || !is_array( $this->_aData ) )
     		return false;
    
   		$oldData = $this->_aData;
   		$this->_aData  = array();
   		$this->_aIndex = array();
   
   		foreach ( $oldData as $iKey => $aData )
    	{
     		if ( count( $aData ) > 0 )
       			$this->insert( $aData );
    	}

   		return true;
  	}

	/**
	 * Check to see if fields set as required are present.
	 *
  	 * @return bool
   	 * @param  aData array
	 * @access public
  	 */
 	function bIsDataValid( $aData )
  	{
   		$bIsValid = true;
   
   		foreach ( $this->_aSettings['fields'] as $fieldName => $bIsRequired )
    	{
     		if ( !$bIsRequired || !$bIsValid )
       			continue;
      
     		$bIsValid = ( strlen( trim( $aData[$fieldName] ) ) > 0 )? true : false;
    	}
   
   		return true;
  	}
	
	
	// private methods
	
 	/**
	 * Create indexes for the specified key.
	 *
  	 * @return bool
  	 * @param  iKey int
  	 * @param  aCell array
	 * @access private
  	 */
 	function _indexAdd( $iKey, $aCell = false )
  	{
   		$aData = is_array( $aCell )? $aCell : $this->get( $iKey );
   
   		if ( !$aData )
    		return false;

   		foreach ( $aData as $fieldName => $fieldValue )
    	{
     		if ( !isset( $this->_aIndex[$fieldName][$fieldValue] ) )
       			$this->_aIndex[$fieldName][$fieldValue] = array();
      
     		$this->_aIndex[$fieldName][$fieldValue][] = $iKey;
    	}
   
   		return true;
  	}

 	/**
	 * Remove the indexes associated with the specified key.
	 *
  	 * @return bool
  	 * @param  iKey int
	 * @access private
  	 */
 	function _indexRemove( $iKey )
  	{
   		if ( !$aData = $this->get( $iKey ) )
     		return false;

   		foreach ( $aData as $fieldName => $fieldValue )
    	{
    	 	if ( !isset( $this->_aIndex[$fieldName][$fieldValue] ) || !is_array( $this->_aIndex[$fieldName][$fieldValue] ) )
       			continue;
      
     		$aIndex =& $this->_aIndex[$fieldName];
     
	 		if ( count( $aIndex[$fieldValue] ) > 1 )
      		{
       			$iTmp = array_search( $iKey, $aIndex[$fieldValue] );
      		 	$aIndex[$fieldValue] = MultiDimensionalArray::array_delete_key( $aIndex[$fieldValue], $iTmp );
      		}
     		else
      		{
       			$aIndex = MultiDimensionalArray::array_delete_key( $aIndex, $fieldValue );
      		}
    	}
   
   		return false;
  	}
} // END OF MultiDimensionalArray

?>
