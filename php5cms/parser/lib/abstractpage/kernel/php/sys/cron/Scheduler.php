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


using( 'sys.cron.SchedulerDate' );


/**
 * Scheduler Class
 * 
 * This class is for those who are not lucky enough to have access to
 * cron[tab] on *nix systems
 * 
 * WARNING: this is for running commands on your *nix server.
 * Running just any commands can be very, very dangerous and
 * can do alot of damage. Be careful!
 * 
 * DATE SYNTAX EXAMPLES:
 * 
 *	Remember:
 *		 - Whitespace (space, tab, newline) - delimited fields
 *       - Single values, sets, ranges, wildcards
 * 
 * SECOND	MINUTE				HOUR		DAY		MONTH
 * *		*					*			*		*		(every second)
 * 0,30 	*					*			*		*		(every 30 seconds)
 * 0		0,10,20,30,40,50	*			*		*		(every 10 minutes)
 * 0		0					*			*		*		(beginning of every hour)
 * 0		0					0,6,12,18	*	 	*		(at midnight, 6am, noon, 6pm)
 * 0		0					0			1-7&Fri	*		(midnight, first Fri of the month)
 * 0		0					0			1-7!Fri	*		(midnight, first Mon-Thu,Sat-Sun of the month)
 * 
 * 
 * Example usage:
 * 
 * $bob = new Scheduler;
 * $bob->setLogFile( "bob.log", 0755 ) || die( "Don't have access to bob.log" );
 * // run this command every 5 minutes
 * $bob->addTask( "perl somescript.pl", "0 0,5,10,15,20,25,30,35,40,45,50,55 * * *" );
 * // run this command midnight of the first Friday of odd numbered months
 * $bob->addTask( "php -q somescript.php", "0 0 0 1-7&Fri 1,3,5,7,9,11" );
 * // also run this command midnight of the second Thursday and Saturday of the even numbered months
 * $bob->addTask( "php -q somescript.php", "0 0 0 8-15&Thu,8-15&Sat 2,4,6,8,10,12" );
 * $bob->addTask("echo \"wazaaaaa\\n\" >> somefile", "0,5,10,15,20,25,30,35,40,45,50,55 * * * *");
 * $bob->run();
 *
 * This needs to be run on the php cgi, preferably from the cmdline not the apache module.
 *
 * @package sys_cron
 */

class Scheduler extends PEAR
{
	/**
	 * @access public
	 */
	var $tasks = array();

	/**
	 * every time a task is added it will get a fresh uid even if immediately removed
	 * @access public
	 */
	var $uid_counter = 1;

	/**
	 * @access public
	 */
	var $logfile = false;
	
	/**
	 * @access public
	 */
	var $chmod = false;
	
	
	/**
	 * Returns false if can't touch or chmod.
	 *
	 * @access public
	 */
	function setLogFile( $file, $chmod = 0700 )
	{
		$this->logfile = $file;
		$this->chmod   = $chmod;
		
		return ( touch( $file ) && chmod( $file, $chmod ) );
	}

	/**
	 * @access public
	 */
	function addTask( $cmd, $rules )
	{
		$ds = new SchedulerDate( $rules );

		$this->uid_counter++;
		$this->tasks[] =
			array(
				"uid"   => $this->uid_counter,
				"rules" => $ds,
				"cmd"   => $cmd
			);

		return $this->uid_counter;
	}

	/**
	 * @access public
	 */
	function removeTask( $uid )
	{
		$found = 0;
		
		for ( $i = 0; $i < sizeof( $this->tasks ); $i++ )
		{
			if ( $this->tasks["uid"] == $uid )
			{
				$found = $i;
				array_splice( $this->tasks, $found ); // nuke entry
				break;
			}
		}
		
		return $found;
	}

	/**
	 * @access public
	 */
	function run()
	{
		// Give me some tasks with the ->addTask() method before you ask me to run anything.
		if ( !sizeof( $this->tasks ) )
			return false;

		while ( 1 )
		{	
			$t = time();

			// check each task's candidacy
			foreach ( $this->tasks as $task )
			{
				if ( $task['rules']->nowMatches() )
					$this->_runcmd( $task );
			}
			
			// wait til the next second
			while ( time() == $t )
				usleep( 100000 );
		}
		
		return true;
	}


	// private methods

	/**
	 * @access private
	 */
	function _runcmd( &$task )
	{
		exec( $task["cmd"] );

		if ( $this->logfile )
			$this->_writeLog( $task["uid"], $task["cmd"] );
	}

	/**
	 * @access private
	 */
	function _writeLog( $uid, $msg )
	{
		if ( !( $f = fopen( $this->logfile, 'a' ) ) )
			return false;
		
		$stamp = date( 'm-d-Y H:i:s' );
		fwrite( $f, "[$stamp] ran #$uid: $msg\n" );
		fclose( $f );
		
		return true;
	}
} // END OF Scheduler

?>
