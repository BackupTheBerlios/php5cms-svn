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
 * Class <code>LoopManipulator</code> defines the interface for manipulators to
 * be used with class <code>Loop</code>.
 * <p>
 *   Every <code>LoopManipulator</code> must implement these four methods:
 * </p>
 * <ul>
 *   <li>
 *     <code>prepare()</code>: execute code right before the first object is
 *     processed.
 *   </li>
 *   <li>
 *     <code>current(&$object, $index)</code>: execute code for the current
 *     object.
 *   </li>
 *   <li>
 *     <code>between($index)</code>: execute code between every two objects.
 *     This method is not called before the first object, nor after the last.
 *   </li>
 *   <li>
 *     <code>finish($total)</code>: execute code right after the last object is
 *     processed.
 *   </li>
 * </ul>
 * <p>
 *   Note that it isn't strictly necessary to implement all loop manipulators as
 *   subclasses of this one, although it does simplify things: if some loop
 *   manipulator only needs to provide a few methods, implementing it as a
 *   subclass of this class allows the unused methods to be left unspecified.
 * </p>
 * <p>
 *   Using class <code>Loop</code> and some loop manipulator not only makes it
 *   very easy to implement a specialized iteration algorithm, it also allows
 *   the behavior of the algorithm to be implemented in stages, or existing 
 *   behavior to be reused. Given a <code>LoopManipulator</code>, it can be used
 *   directly, subclassed or wrapped inside another manipulator. The following
 *   simple manipulator, for example, makes it possible to print any HTML output
 *   inside a specified number of columns:
 * </p>
 * <pre>
 *   class HtmlTable extends LoopManipulator
 *   {
 *       var $manipulator;
 *       var $break;
 *
 *       function HtmlTable( &$manipulator, $total, $columns )
 *       {
 *           $this->manipulator =& $manipulator;
 *           $this->break       =  ceil( $total / $columns );
 *       }
 *
 *       function prepare()
 *       {
 *           echo "&lt;table&gt;\n  &lt;tr&gt;\n";
 *           $this->manipulator->prepare();
 *       }
 *
 *       function current( &$object, $index )
 *       {
 *           $this->manipulator->current( $object, $index );
 *       }
 *
 *       function between($index)
 *       {
 *           $this->manipulator->between( $index );
 *
 *           if ( $index % $this->break == 0 )
 *               echo "  &lt;/tr&gt;\n  &lt;tr&gt;\n";
 *       }
 *
 *       function finish( $total )
 *       {
 *          $this->manipulator->finish( $total );
 *          echo "  &lt;/tr&gt;\n&lt;/table&gt;\n";
 *       }
 *   }
 *
 *   class BookPrinter extends LoopManipulator
 *   {
 *       function current( &$row, $index )
 *       {
 *           echo "${row['title']}&lt;br&gt;\n";
 *       }
 *   }
 *
 *   $result = $database->query( 'SELECT title FROM book' );
 *   Loop::run(
 *       new QueryIterator( $result ),
 *       new HtmlTable( new BookPrinter, $result->getRowCount(), 2 );
 *   );
 * </pre>
 * <p>
 *   The (extremely simplified and untested) class <code>HtmlTable</code> above
 *   can be reused over and over again whenever some output must be printed in 
 *   columns. Also, class <code>BookPrinter</code> can be used in other problem
 *   areas as well, as it knows nothing about the HTML table it happens to be
 *   printed in (in this occassion).
 * </p>
 *
 * @see Loop
 * @package util
 */
 
class LoopManipulator extends PEAR
{
    /**
     * Execute code right before the first object is processed.
	 *
     * @return void
	 * @access public
     */
    function prepare() 
    { 
    }

    /**
     * Execute code for the current object.
	 *
     * @param $object the object that must be handled
     * @param $index the index of the current object
     * @return void
	 * @access public
     */
    function current( &$object, $index ) 
    {
    }
        
    /**
     * Execute code right between two objects.
	 *
     * @param $index the index of the object about to be processed
     * @return void
	 * @access public
     */
    function between( $index ) 
    { 
    }

    /**
     * Execute code right after the last object has been processed.
	 *
     * @param $total the total number of objects processed
     * @return void
	 * @access public
     */
    function finish( $total ) 
    { 
    }
} // END OF LoopManipulator

?>
