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


define( "PSEUDOCRON_MINUTE",   1 );
define( "PSEUDOCRON_HOUR",	   2 );
define( "PSEUDOCRON_DOM",	   3 );
define( "PSEUDOCRON_MONTH",	   4 );
define( "PSEUDOCRON_DOW",	   5 );
define( "PSEUDOCRON_CMD",	   7 );
define( "PSEUDOCRON_CRONLINE", 8 );


/**
 * Pseudo Cron Class
 *
 * Usually regular tasks like backup up the site's database are run using cron
 * jobs. With cron jobs, you can exactly plan when a certain command is to be 
 * executed. But most homepage owners can't create cron jobs on their web 
 * server - providers demand some extra money for that.
 * The only thing that's certain to happen quite regularly on a web page are 
 * page requests. This is where pseudo-cron comes into play: With every page 
 * request it checks if any cron jobs should have been run since the previous 
 * request. If there are, they are run and logged.
 * 
 * Pseudo-cron uses a syntax very much like the Unix cron's one. For an 
 * overview of the syntax used, see a page of the UNIXGEEKS. The syntax 
 * pseudo-cron uses is different from the one described on that page in 
 * the following points:
 * 
 *   -  there is no user column
 *   -  the executed command has to be an include()able file (which may contain further PHP code) 
 * 
 * All job definitions are made in a text file on the server with a 
 * user-definable name. A valid command line in this file is, for example:
 * 
 * *	2	1,15	*	*	samplejob.inc.php
 * 
 * This runs samplejob.php at 2am on the 1st and 15th of each month.
 * 
 * Features:
 *   -  runs any PHP script
 *   -  periodical or time-controlled script execution
 *   -  logs all executed jobs
 *   -  can be run from an IMG tag in an HTML page
 *   -  follow Unix cron syntax for crontabs
 * 
 * Usage:
 *   -  Modify the variables in the config section below to match your server.
 *   -  Write a PHP script that does the job you want to be run regularly. Be
 *      sure that any paths in it are relative to the script that will run 
 *      pseudo-cron in the end.
 *   -  Set up your crontab file with your script
 *   -  Wait for the next scheduled run :)
 * 
 * Note:
 * You can log messages to pseudo-cron's log file by calling
 *      $this->_logMessage( "log a message" );
 *
 * @package sys_cron
 */

class PseudoCron extends PEAR
{
	/**
	 * The directory where the script can store information on completed jobs and its log file.
	 *
	 * @access public
	 */
	var $writeDir = "cronjobs/";

	/**
	 * Control logging, 1=use log file, 0=don't use log file.
	 *
	 * @access public
	 */
	var $useLog = 1;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function PseudoCron( $useLog = true )
	{
		if ( $useLog )
			$this->enableLog();
	}
	

	/**
	 * access public
	 */
	function setWriteDir( $dir = "" )
	{
		if ( !empty( $dir ) )
			$this->writeDir = $dir;
	}
	
	/**
	 * access public
	 */
	function getWriteDir()
	{
		return $this->writeDir;
	}

	/**
	 * access public
	 */	
	function enableLog()
	{
		$this->useLog = 1;
	}
	
	/**
	 * access public
	 */
	function disableLog()
	{
		$this->useLog = 0;
	}
	
	/**
	 * access public
	 */
	function runJob( $job ) 
	{
		$extjob  = array();
		$jobfile = $this->_getJobFileName( $job[PSEUDOCRON_CMD] );
	
		$this->_parseElement( $job[PSEUDOCRON_MINUTE], $extjob[PSEUDOCRON_MINUTE], 60 );
		$this->_parseElement( $job[PSEUDOCRON_HOUR],   $extjob[PSEUDOCRON_HOUR],   24 );
		$this->_parseElement( $job[PSEUDOCRON_DOM],    $extjob[PSEUDOCRON_DOM],    31 );
		$this->_parseElement( $job[PSEUDOCRON_MONTH],  $extjob[PSEUDOCRON_MONTH],  12 );
		$this->_parseElement( $job[PSEUDOCRON_DOW],    $extjob[PSEUDOCRON_DOW],     7 );
	
		$lastActual    = $this->_getLastActualRunTime( $job[PSEUDOCRON_CMD] );
		$lastScheduled = $this->_getLastScheduledRunTime( $extjob );
	
		if ( $lastScheduled>$lastActual ) 
		{
			$this->_logMessage( "Running 	" . $job[PSEUDOCRON_CRONLINE] );
			$this->_logMessage( "  Last run:       " . date( "r", $lastActual )    );
			$this->_logMessage( "  Last scheduled: " . date( "r", $lastScheduled ) );
			$this->_markLastRun( $job[PSEUDOCRON_CMD], $lastScheduled );
		
			@include( $job[PSEUDOCRON_CMD] ); // any error messages are supressed
			$this->_logMessage( "Completed	" . $job[PSEUDOCRON_CRONLINE] );
		
			return true;
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * access public
	 */
	function parseCronFile( $cronTabFile ) 
	{
		$file = file( $cronTabFile );
		$job  = array();
		$jobs = array();
	
		for ( $i = 0; $i < count( $file ); $i++ ) 
		{
			if ( $file[$i][0] != '#' ) 
			{
				if ( preg_match( "~^([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-7,/*]+|(-|/|Sun|Mon|Tue|Wed|Thu|Fri|Sat)+)\\s+([^#]*)(#.*)?$~i", $file[$i], $job ) ) 
				{
					$jobNumber = count( $jobs );
					$jobs[$jobNumber] = $job;
				
					if ( $jobs[$jobNumber][PSEUDOCRON_DOW][0] != '*' && !is_numeric( $jobs[$jobNumber][PSEUDOCRON_DOW] ) ) 
					{
						$jobs[$jobNumber][PSEUDOCRON_DOW] = str_replace(
							array( "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ),
							array( 0,     1,     2,     3,     4,     5,     6     ),
							$jobs[$jobNumber][PSEUDOCRON_DOW]
						);
					}
				
					$jobs[$jobNumber][PSEUDOCRON_CMD]      = trim( $job[PSEUDOCRON_CMD] );
					$jobs[$jobNumber][PSEUDOCRON_CRONLINE] = $file[$i];
				}
			}
		}
	
		return $jobs;
	}
	
	
	// private methods

	/**
	 * @access public
	 */
	function _logMessage( $msg ) 
	{
		if ( $this->useLog == 1 ) 
		{
			$logfile = $this->writeDir . "pseudo-cron.log";
			$file = fopen( $logfile, "a" );
		
			if ( $msg[strlen( $msg ) - 1] != "\n" )
				$msg.="\n";
		
			fputs( $file, date( "r", time() ) . "  " . $msg );
			fclose( $file );
		}
	}

	/**
	 * @access public
	 */
	function _lTrimZeros( $number ) 
	{
		while ( $number[0] == '0' )
			$number = substr( $number, 1 );
	
		return $number;
	}

	/**
	 * @access public
	 */
	function _parseElement( $element, &$targetArray, $numberOfElements ) 
	{
		$subelements = explode( ",", $element );
	
		for ( $i = 0; $i < $numberOfElements; $i++ )
			$targetArray[$i] = $subelements[0] == "*";
	
		for ( $i = 0; $i < count( $subelements ); $i++ ) 
		{
			if ( preg_match( "~^(\\*|([0-9]{1,2})(-([0-9]{1,2}))?)(/([0-9]{1,2}))?$~", $subelements[$i], $matches ) ) 
			{
				if ( $matches[1] == "*" ) 
				{
					$matches[2] = 0;					// from
					$matches[4] = $numberOfElements;	// to
				} 
				else if ( ( count( $matches ) >= 4 ) && $matches[4] == "" ) 
				{
					$matches[4] = $matches[2];
				}
			
				if ( ( count( $matches ) >= 5 ) )
				{
					if ( $matches[5][0] != "/" )
						$matches[6] = 1; // step
			
					for ( $j = $this->_lTrimZeros( $matches[2] ); $j <= $this->_lTrimZeros( $matches[4] ); $j += $this->_lTrimZeros( $matches[6] ) )
						$targetArray[$j] = true;
				}
			}
		}
	}

	/**
	 * @access public
	 */
	function _decDate( &$dateArr, $amount, $unit ) 
	{
		if ( $unit == "mday" ) 
		{
			$dateArr["hours"]    = 23;
			$dateArr["minutes"]  = 59;
			$dateArr["seconds"]  = 59;
			$dateArr["mday"]    -= $amount;
			$dateArr["wday"]    -= $amount % 7;
		
			if ( $dateArr["wday"] < 0 )
				$dateArr["wday"] += 7;
		
			if ( $dateArr["mday"] < 1 ) 
			{
				$dateArr["mon"]--;
			
				switch ( $dateArr["mon"] ) 
				{
					case 0:
						$dateArr["mon"] = 12;
						$dateArr["year"]--;
						// fall through
				
					case 1:
				
					case 3:
				
					case 5:
				
					case 7:
				
					case 8:
				
					case 10:
				
					case 12:
						$dateArr["mday"] = 31;
						break;
				
					case 4:
				
					case 6:
				
					case 9:
				
					case 11:
						$dateArr["mday"] = 30;
						break;
				
					case 2:
						$dateArr["mday"] = 28;
						break;
				}
			}
		} 
		else if ( $unit == "hour" ) 
		{
			if ( $dateArr["hours"] == 0 ) 
			{
				$this->_decDate( $dateArr, 1, "mday" );
			} 
			else 
			{
				$dateArr["minutes"] = 59;
				$dateArr["seconds"] = 59;
				$dateArr["hours"]--;
			}
		} 
		else if ( $unit == "minute" ) 
		{
			if ( $dateArr["minutes"] == 0 ) 
			{
				$this->_decDate( $dateArr, 1, "hour" );
			} 
			else 
			{
				$dateArr["seconds"] = 59;
				$dateArr["minutes"]--;
			}
		}
	}

	/**
	 * @access public
	 */
	function _getLastScheduledRunTime( $job ) 
	{
		$dateArr = getdate();
		$minutesBack = 0;
	
		while ( $minutesBack < 525600 && ( !$job[PSEUDOCRON_MINUTE][$dateArr["minutes"]] || !$job[PSEUDOCRON_HOUR][$dateArr["hours"]] || ( !$job[PSEUDOCRON_DOM][$dateArr["mday"]] || !$job[PSEUDOCRON_DOW][$dateArr["wday"]] ) || !$job[PSEUDOCRON_MONTH][$dateArr["mon"]] ) ) 
		{
			if ( !$job[PSEUDOCRON_DOM][$dateArr["mday"]] || !$job[PSEUDOCRON_DOW][$dateArr["wday"]] ) 
			{
				$this->_decDate( $dateArr, 1, "mday" );
				$minutesBack += 1440;

				continue;
			}
		
			if ( !$job[PSEUDOCRON_HOUR][$dateArr["hours"]] ) 
			{
				$this->_decDate( $dateArr, 1, "hour" );
				$minutesBack += 60;
			
				continue;
			}
		
			if ( !$job[PSEUDOCRON_MINUTE][$dateArr["minutes"]] ) 
			{
				$this->_decDate( $dateArr, 1, "minute" );
				$minutesBack++;
			
				continue;
			}
		}
	
		return mktime( $dateArr["hours"], $dateArr["minutes"], 0, $dateArr["mon"], $dateArr["mday"], $dateArr["year"] );
	}

	/**
	 * @access public
	 */
	function _getJobFileName( $jobname ) 
	{
		$jobfile = $this->writeDir . urlencode( $jobname ) . ".job";
		return $jobfile;
	}

	/**
	 * @access public
	 */
	function _getLastActualRunTime( $jobname ) 
	{
		$jobfile = $this->_getJobFileName( $jobname );
	
		if ( file_exists( $jobfile ) ) 
		{
			$file    = fopen( $jobfile, "r" );
			$lastRun = fgets( $file, 100 );
		
			fclose( $file );
		
			if ( is_numeric( $lastRun ) )
				return $lastRun;
		}
	
		return 0;
	}

	/**
	 * @access public
	 */
	function _markLastRun( $jobname, $lastRun ) 
	{
		$jobfile = $this->_getJobFileName( $jobname );
		$file    = fopen( $jobfile, "w" );
	
		fputs( $file, $lastRun );
		fclose( $file );
	}	
} // END OF PseudoCron

?>
