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
 * This class implements a Finite State Machine (FSM).
 *
 * In addition to maintaining state, this FSM also maintains a user-defined
 * payload, therefore making effectively making the machine a Push-Down
 * Automata (a finite state machine with memory).
 *
 * @package util
 */

class FSM extends PEAR
{
    /**
     * Represents the initial state of the machine.
     *
     * @var string
     * @see $_currentState
	 * @access private
     */
    var $_initialState = '';

    /**
     * Contains the current state of the machine.
     *
     * @var string
     * @see $_initialState
	 * @access private
     */
    var $_currentState = '';

    /**
     * Contains the payload that will be passed to each action function.
     *
     * @var mixed
	 * @access private
     */
    var $_payload = null;

    /**
     * Maps (inputSymbol, currentState) --> (action, nextState).
     *
     * @var array
     * @see $_inputState, $_currentState
	 * @access private
     */
    var $_transitions = array();

    /**
     * Maps (currentState) --> (action, nextState).
     *
     * @var array
     * @see $_inputState, $_currentState
	 * @access private
     */
    var $_transitionsAny = array();

    /**
     * Contains the default transition that is used if no more appropriate
     * transition has been defined.
     *
     * @var array
	 * @access private
     */
    var $_defaultTransition = null;


    /**
	 * Constructor
	 *
     * This method constructs a new Finite State Machine (FSM) object.  In
     * addition to defining the machine's initial state, a "payload" may also
     * be specified.  The payload represents a variable that will be passed
     * along to each of the action functions.  If the FSM is being used for
     * parsing, the payload is often a array that is used as a stack.
     *
     * @param   string  $initialState   The initial state of the FSM.
     * @param   mixed   $payload        A payload that will be passed to each
     *                                  action function.
	 * @access  public
     */
    function FSM( $initialState, &$payload )
    {
        $this->_initialState =  $initialState;
        $this->_currentState =  $initialState;
        $this->_payload      = &$payload;
    }

	
    /**
     * This method resets the FSM by setting the current state back to the
     * initial state (set by the constructor).  The current input symbol is
     * also reset to NULL.
	 *
	 * @access  public
     */
    function reset()
    {
        $this->_currentState = $this->_initialState;
        $this->_inputSymbol  = null;
    }

    /**
     * This method adds a new transition that associates:
     *
     *      (symbol, currentState) --> (nextState, action)
     *
     * The action may be set to NULL, in which case the processing routine
     * will ignore the action and just set the next state.
     *
     * @param   string  $symbol         The input symbol.
     * @param   string  $state          This transition's starting state.
     * @param   string  $nextState      This transition's ending state.
     * @param   string  $action         The name of the function to invoke
     *                                  when this transition occurs.
     * @see     addTransitions()
	 * @access  public
     */
    function addTransition( $symbol, $state, $nextState, $action = null )
    {
        $this->_transitions["$symbol,$state"] = array( $nextState, $action );
    }

    /**
     * This method adds the same transition for multiple different symbols.
     *
     * @param   array   $symbols        A list of input symbols.
     * @param   string  $state          This transition's starting state.
     * @param   string  $nextState      This transition's ending state.
     * @param   string  $action         The name of the function to invoke
     *                                  when this transition occurs.
     *
     * @see     addTransition()
	 * @access  public
     */
    function addTransitions( $symbols, $state, $nextState, $action = null )
    {
        foreach ( $symbols as $symbol )
            $this->addTransition( $symbol, $state, $nextState, $action );
    }

    /**
     * This method adds a new transition that associates:
     *
     *      (currentState) --> (nextState, action)
     *
     * The processing routine checks these associations if it cannot first
     * find a match for (symbol, currentState).
     *
     * @param   string  $state          This transition's starting state.
     * @param   string  $nextState      This transition's ending state.
     * @param   string  $action         The name of the function to invoke
     *                                  when this transition occurs.
     *
     * @see     addTransition()
	 * @access  public
     */
    function addTransitionAny( $state, $nextState, $action = null )
    {
        $this->_transitionsAny[$state] = array( $nextState, $action );
    }

    /**
     * This method sets the default transition.  This defines an action and
     * next state that will be used if the processing routine cannot find a
     * suitable match in either transition list.  This is useful for catching
     * errors caused by undefined states.
     *
     * The default transition can be removed by setting $nextState to NULL.
     *
     * @param   string  $nextState      The transition's ending state.
     * @param   string  $action         The name of the function to invoke
     *                                  when this transition occurs.
	 * @access  public
     */
    function setDefaultTransition( $nextState, $action )
    {
        if ( empty( $nextState ) )
		{
            $this->_defaultTransition = null;
            return;
        }

        $this->_defaultTransition = array( $nextState, $action );
    }

    /**
     * This method returns (nextState, action) given an input symbol and
     * state.  The FSM is not modified in any way.  This method is rarely
     * called directly (generally only for informational purposes).
     *
     * If the transition cannot be found in either of the transitions lists,
     * the default transition will be returned.  Note that it is possible for
     * the default transition to be set to NULL.
     *
     * @param   string  $symbol         The input symbol.
     *
     * @return  mixed   Array representing (nextState, action), or NULL if the
     *                  transition could not be found and not default
     *                  transition has been defined.
	 * @access  public
     */
    function getTransition( $symbol )
    {
        $state = $this->_currentState;

        if ( array_key_exists( "$symbol,$state", $this->_transitions ) )
            return $this->_transitions["$symbol,$state"];
        else if ( array_key_exists( $state, $this->_transitionsAny ) )
            return $this->_transitionsAny[$state];
        else
            return $this->_defaultTransition;
    }

    /**
     * This method is the main processing routine.  It causes the FSM to
     * change states and execute actions.
     *
     * The transition is determined by calling getTransition() with the
     * provided symbol and the current state.  If no valid transition is found,
     * process() returns immediately.
     *
     * If no action is defined for the transition, only the state will be
     * changed.
     *
     * @param   string  $symbol         The input symbol.
     * @see     processList()
	 * @access  public
     */
    function process( $symbol )
    {
        $transition = $this->getTransition( $symbol );

        /* If a valid array wasn't returned, return immediately. */
        if ( !is_array( $transition ) || ( count( $transition ) != 2 ) )
            return;

        /* If an action for this transition has been specified, execute it. */
        if ( !empty( $transition[1] ) )
            call_user_func( $transition[1], $symbol, &$this->_payload );

        /* Update the current state to this transition's exit state. */
        $this->_currentState = $transition[0];
    }

    /**
     * This method processes a list of symbols.  Each symbol in the list is
     * sent to process().
     *
     * @param   array   $symbols        List of input symbols to process.
	 * @access  public
     */
    function processList( $symbols )
    {
        foreach ( $symbols as $symbol )
            $this->process( $symbol );
    }
} // END OF FSM

?>
