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
 * Class to interface to the UNIX "at" program.
 *
 * @package sys
 */

class UnixAt extends PEAR
{
	/**
	 * @access public
	 */
    var $at_prog;

	/**
	 * @access public
	 */
    var $error = false;
	
	/**
	 * @access public
	 */
    var $runtime = false;
	
	/**
	 * @access public
	 */
    var $job = false;
	
	/**
	 * @access public
	 */
    var $lastexec = '';


    /**
     * Constructor
     *
     * @access public
     */
    function UnixAt()
    {
		$this->at_prog = ap_ini_get( "file_at", "file" );
        $this->_reset();
    }


    /**
     * Adds an at command.
	 *
     * This makes an "at" job, where $cmd is the shell command to run
     * and $timespec describes when the function should run.  See the
     * at man page for a description of the spec.
     *
     * $queue is an optional 1 character string [a-zA-Z] that can define
     * which queue to put the job in.
     *
     * If $mail is set, then an email will be sent when the job runs,
     * even if the job doesn't output anything.  The mail gets sent to
     * the user running the script (probably the webserver, i.e.
     * nobody@localhost).
     *
     * The add() method returns false on error (in which case, check
     * $at->error for the message), or the job number on success.
     * On succes, $at->runtime is also set with the timestamp corresponding
     * to when the job will run.
     *
     * @param $cmd        shell command
     * @param $timespec   time when command should run, formatted accoring to the spec for at
     * @param $queue      optional at queue specifier
     * @param $mail       optional flag to specify whether to send email
     *
     * @access public
     */
    function add( $cmd, $timespec, $queue = false, $mail = false )
    {
        $this->_reset();

        if ( $queue && !preg_match( '/^[a-zA-Z]{1,1}$/', $queue ) )
            return PEAR::raiseError( 'Invalid queue specification.' );

        $cmd  = escapeshellcmd( $cmd );
        $exec = sprintf( "echo \"%s\" | %s %s %s %s 2>&1",
            addslashes( $cmd ),
            $this->at_prog,
            ( $queue? '-q ' . $queue : '' ),
            ( $mail?  '-m' : '' ),
            $timespec
        );

        $result = $this->_doexec( $exec );

        if ( preg_match( '/garbled time/i', $result ) )
            return PEAR::raiseError( 'Garbled time.' );

        if ( preg_match( '/job (\d+) at (.*)/i', $result, $m ) )
		{
            $this->runtime = $this->_parsedate( $m[2] );
            $this->job = $m[1];
			
            return $this->job;
        }
		else
		{
            return PEAR::raiseError( 'Exec Error: ' . $result );
        }
    }

    /**
     * Shows commands in the at queue.
     *
     * This returns an array listing all queued jobs.  The array's keys
     * are the job numbers, and each entry is itself an associative array
     * listing the runtime (timestamp) and queue (char).
     *
     * You can optionally provide a queue character to only list the jobs
     * in that queue.
     *
     * @param $queue        optional queue specifier
     *
     * @access public
     */
    function show( $queue = false )
    {
        $this->_reset();

        if ( $queue && !preg_match( '/^[a-zA-Z]{1,1}$/', $queue ) )
            return PEAR::raiseError( 'Invalid queue specification.' );

        $exec = sprintf(
			"%s -l %s",
            $this->at_prog,
            ( $queue? '-q ' . $queue : '' )
        );

        $result = $this->_doexec( $exec );
        $lines  = explode( "\n", $result );
        $return = array();

        foreach ( $lines as $line )
		{
            if ( trim( $line ) )
			{
                list( $job, $day, $time, $queue ) = preg_split( '/\s+/', trim( $line ) );
                
				$return[$job] = array(
                    'runtime' => $this->_parsedate( $day . ' ' . $time ),
                    'queue'   => $queue
                );
            }
        }

        return $return;
    }

    /**
     * Remove job from the at queue.
     *
     * This removes jobs from the queue.  Returns false if the job doesn't
     * exist or on failure, or true on success.
     *
     * @param $job        job to remove
     *
     * @access public
     */
    function remove( $job = false )
    {
        $this->_reset();

        if ( !$job )
            return PEAR::raiseError( 'No job specified.' );

        $queue = $this->show();

        if ( !isset( $queue[$job] ) )
            return PEAR::raiseError( 'Job ' . $job . ' does not exist.' );

        $exec = sprintf(
			"%s -d %s",
            $this->at_prog,
            $job
        );

        $this->_doexec( $exec );

        // this is required since the shell command doesn't return anything on success
        $queue = $this->show();
        return !isset( $queue[$job] );
    }

	
	// private methods
	
    /**
     * Reset class.
     *
     * @access private
     */
    function _reset()
    {
        $this->error    = false;
        $this->runtime  = false;
        $this->job      = false;
        $this->lastexec = '';
    }

    /**
     * Parse date string returned from shell command.
     *
     * @param $str    date string to parse
     *
     * @access private
     */
    function _parsedate( $str )
    {
        if ( preg_match( '/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/i', $str, $m ) )
            return mktime( $m[4], $m[5], 0, $m[2], $m[3], $m[1] );
        else
            return false;
    }

    /**
     * Run a shell command.
     *
     * @param $cmd    command to run
     *
     * @access private
     */
    function _doexec( $cmd )
    {
        $this->lastexec = $cmd;
        return `$cmd`;
    }
} // END OF UnixAt

?>
