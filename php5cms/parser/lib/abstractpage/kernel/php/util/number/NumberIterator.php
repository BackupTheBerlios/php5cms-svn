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
 * Class <code>NumberIterator</code> is an iterator for generating consecutive
 * numbers.
 * <p>
 *   On creation the total number of numbers to generate, the starting number
 *   and the stepping number must be set. The latter two are optional.
 * </p>
 * <p>
 *   Consider a query that returns many columns, and there's a need to divide
 *   the result over multiple pages (with a <code>PagedQuery</code>). A link to
 *   each page in the result is useful, and some simple navigation to advance to
 *   the next page or jump back to the previous one might also come in handy.
 *   The nice way to do this is with the <code>Loop</code>, this
 *   <code>NumberIterator</code>, and a convenient <code>LoopManipulator</code>
 *   object. For example:
 * </p>
 * <pre>
 *   Loop::run( new NumberIterator( $pageCount ), new PageNavigator );
 *</pre>
 * <p>
 *   Of course, the <code>LoopManipulator</code> (<code>PageNavigator</code> in
 *   the example) that writes the appropriate HTML still has to be defined, but
 *   that's a very simple task and if done properly it can be used over and over
 *   again.
 * </p>
 * <p>
 *   Because it is possible to set the number to start with, as well as the
 *   stepping, this class can do more than just the above, although this won't
 *   be necessary very often. A simple application is the generation of
 *   the multiplication table of some number <code>$n</code>:
 * </p>
 * <pre>    $it =& new NumberIterator(10, $n, $n);</pre>
 *
 * @see Loop
 * @see LoopManipulator
 * @package util_number
 */
 
class NumberIterator extends Iterator
{
    /**
     * The total number of numbers to generate
     * @var  int
	 * @access public
     */
    var $size;

    /**
     * The first number that should be generated
     * @var  int
	 * @access public
     */
    var $base;

    /**
     * The stepping size
     * @var  int
	 * @access public
     */
    var $step;

    /**
     * The index of the current number
     * @var  int
	 * @access public
     */
    var $index;

    /**
     * The current number
     * @var  int
	 * @access public
     */
    var $current;


    /**
     * Constructor
	 *
     * @param  $size the total number of numbers to generate
     * @param  $base the first number
     * @param  $step the number to add with each consecutive step
	 * @access public
     */
    function NumberIterator( $size, $base = 1, $step = 1 ) 
    {
        $this->size = $size;
        $this->base = $base;
        $this->step = $step;
        
		$this->reset();
    }


    /**
     * @return void
	 * @access public
     */
    function reset() 
    {
        $this->index   = 0;
        $this->current = $this->base;
    }

    /**
     * @return void
	 * @access public
     */
    function next() 
    {
        $this->index++;
        $this->current += $this->step;
    }

    /**
     * @return bool
	 * @access public
     */
    function isValid() 
    {
        return $this->index < $this->size;
    }

    /**
     * @return int
	 * @access public
     */
    function &getCurrent() 
    {
        return $this->current;
    }
} // END OF NumberIterator

?>
