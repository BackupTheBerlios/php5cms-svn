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
|Authors: Vincent Oostindië <eclipse@sunlight.tmfweb.nl>               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.Iterator' );


/**
 * Class <code>StringIterator</code> provides an iterator for strings.
 * <p>
 *   Class <code>StringIterator</code> offers an implementation of the
 *   <code>Iterator</code> interface for iterating over the characters in a
 *   string. As with any iterator, changing the structure of the object iterated
 *   over (e.g. changing the string to a different string) might result in
 *   unexpected behavior. However it is possible to change the character 
 *   returned by <code>getCurrent</code>. For example:
 * </p>
 * <pre>
 *   $string = 'Encrypt me!';
 *   $key    = array(-1, 3, -2, 0, 0, 1);
 *   $size   = count($key);
 *   $index  = 0;
 *   for ($it =& new StringIterator($string); $it->isValid(); $it->next()) 
 *   {
 *       $char  =& $it->getCurrent();
 *       $char  =  chr(ord($char) + $key[$index]);
 *       $index =  ($index + 1) % $size;
 *   }
 *   print 'Encrypted string: ' . $string;
 * </pre>
 * <p>
 *   The above encryption algorithm is actually unbreakable if the key-array
 *   is at least as long as the encrypted string. (It's a one-time-pad in that
 *   case.) The decryption algorithm is left as an exercise for the reader...
 * </p>
 *
 * @see Iterator
 * @package util_text
 */
 
class StringIterator extends Iterator 
{
    /**
     * The string to iterate over
     * @var  string
	 * @access public
     */
    var $string;

    /**
     * The current index in the string
     * @var  int
	 * @access public
     */
    var $index;

    /**
     * The current character
     * @var  char
	 * @access public
     */
    var $char;

    /**
     * The total length of the string
     * @var  int
	 * @access public
     */
    var $size;


    /**
     * Constructor
	 *
     * @param  $string the string to iterate over
	 * @access public
     */
    function StringIterator( &$string ) 
    {
        $this->string =& $string;
        $this->size   =  strlen( $string );
		
        $this->reset();
    }

    
    /**
     * @return void
	 * @access public
     */
    function reset() 
    {
        $this->index = 0;
        $this->char  = ( $this->size )? $this->string{0} : '';
    }

    /**
     * @return void
	 * @access public
     */
    function next() 
    {
        $this->string{$this->index} = $this->char{0};
        $this->index++;
        $this->char = ( $this->index < $this->size )? $this->string{$this->index} : '';
    }

    /**
     * @return bool
	 * @access public
     */
    function isValid() 
    {
        return ( $this->index < $this->size );
    }

    /**
     * Return a reference to the current character.
	 *
     * @return char
	 * @access public
     */
    function &getCurrent() 
    {
        return $this->char;
    }
} // END OF StringIterator

?>
