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
 * Class <code>Loop</code> implements an iteration that can be influenced by a
 * manipulator as defined by class <code>LoopManipulator</code>.
 * <p>
 *   To start the loop, just call the static method <code>run</code> with an
 *   iterator and a loop manipulator. The method returns the total number of
 *   objects that was processed.
 * </p>
 * <p>
 *   See class <code>LoopManipulator</code> for more information on how to
 *   influence loops and why this simple class is extremely useful.
 * </p>
 *
 * @see Iterator
 * @see LoopManipulator
 * @package util
 */
 
class Loop extends PEAR
{
    /**
     * Run a loop on an iterator and a manipulator and return the number of
     * items processed.
	 *
     * @param  $iterator the <code>Iterator</code> to run the loop on
     * @param  $manipulator the <code>LoopManipulator</code> to use
     * @return int
     * @static
	 * @access public
     */
    function run( &$iterator, &$manipulator )
    {
        $index = 0;
        $iterator->reset();
        
		if ( $iterator->isValid() )
            $manipulator->prepare();
        
        for ( ; $iterator->isValid(); $iterator->next() )
        {
            $current =& $iterator->getCurrent();
            
			if ( $index )
                $manipulator->between( $index );
            
            $manipulator->current( $current, $index++ );
        }
		
        if ( $index )
            $manipulator->finish( $index );
        
        return $index;
    }
} // END OF Loop

?>
