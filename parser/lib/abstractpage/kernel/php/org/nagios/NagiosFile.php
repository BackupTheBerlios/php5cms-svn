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
 * @package org_nagios
 */
 
class NagiosFile extends PEAR
{
	/**
	 * @access public
	 */
    var $config;
	
	/**
	 * @access public
	 */
    var $contactgroups;
	
	/**
	 * @access public
	 */
    var $contacts;
	
	/**
	 * @access public
	 */
    var $hostgroups;
	
	/**
	 * @access public
	 */
    var $hosts;
	
	/**
	 * @access public
	 */
    var $services;
	
	/**
	 * @access public
	 */
    var $host_status;
	
	/**
	 * @access public
	 */
    var $service_status;
	
	/**
	 * @access public
	 */
    var $errormsg = "";
    
	
    /**
	 * Constructor
	 *
	 * @access public
	 */
    function NagiosFile() 
    {
        $this->config         = array();
        $this->contactgroups  = array();
        $this->contacts       = array();
        $this->hostgroups     = array();
        $this->hosts          = array();
        $this->services       = array();
        $this->host_status    = array();
        $this->service_status = array();
    }


	/**
	 * @access public
	 */
    function parseConfigFile( $filename )
    {
        if ( ( $fp = fopen( $filename, 'r' ) ) == false ) 
			return PEAR::raiseError( "Cannot open config file $filename." );

        while ( $line = fgets( $fp, 987 ) ) 
		{
			if ( preg_match( '/^\s*(|#.*)$/', $line ) )
				continue;
              
			if ( preg_match( '/^\s*cfg_file\s*=\s*(\S+)/', $line, $regs ) ) 
			{
				if ( $this->_parseObjectFile( $regs[1] ) === false )
					return false;
                
                continue;
			}
			
			if ( preg_match( '/^\s*(\S+)\s*=\s*(\S+)/', $line, $regs ) ) 
			{
				$this->config[$regs[1]] = $regs[2];
				continue;
			}
		}
	}

	/**
	 * @access public
	 */    
    function parseStatusFile()
    {
        $hostst_fields = array(
			'status',
			'last_check',
			'last_state_change',
			'problem_has_been_acknowledged',
			'time_up',
			'time_down',
			'time_unreachable',
			'(unsigned long)last_notification',
			'current_notification_number',
			'notifications_enabled',
			'event_handler_enabled',
			'checks_enabled',
			'flap_detection_enabled',
			'is_flapping',
			'percent_state_change',
			'scheduled_downtime_depth',
			'failure_prediction_enabled',
			'process_performance_data',
			'plugin_output'
		);
        
		$servicest_fields = array(
			'status',
			'attempts',
			'state_type',
			'last_check',
			'next_check',
			'check_type',
			'checks_enabled',
			'accept_passive_service_checks',
			'event_handler_enabled',
			'last_state_change',
			'problem_has_been_acknowledged',
			'last_hard_state',
			'time_ok',
			'time_unknown',
			'time_warning',
			'time_critical',
			'last_notification',
			'current_notification_number',
			'notifications_enabled',
			'latency',
			'execution_time',
			'flap_detection_enabled',
			'is_flapping',
			'percent_state_change',
			'scheduled_downtime_depth',
			'failure_prediction_enabled',
			'process_performance_data',
			'obsess_over_service',
			'plugin_output'
		);

        if ( ( $fp = fopen( $this->config['status_file'], 'r' ) ) == false ) 
			return PEAR::raiseError( "Could not open status file $filename." );
			
        while ( $line = fgets( $fp, 987 ) ) 
		{
            if ( preg_match( '/\[(\d+)\] SERVICE;([^;]+);([^;]+);(.+)$/', $line, $regs ) ) 
			{
                $tmparr = explode( ';', $regs[4] );
				
                $this->service_status[$regs[2]][$regs[3]] = array(
					'last_update' => $regs[1], 
					'host_name'   => $regs[2], 
					'description' => $regs[3]
				);
				
                foreach ( $servicest_fields as $ordinal => $key )
					$this->service_status[$regs[2]][$regs[3]][$key] = $tmparr[$ordinal];
                
                continue;
            }
			
            if ( preg_match( '/\[(\d+)\] HOST;([^;]+);(.+)$/', $line, $regs ) ) 
			{
                $tmparr = explode( ';', $regs[3] );
				
                $this->host_status[$regs[2]] = array(
					'last_update' => $regs[1], 
					'host_name'   => $regs[2]
				);
				
                foreach ( $hostst_fields as $ordinal => $key )
					$this->host_status[$regs[2]][$key] = $tmparr[$ordinal];
                
                continue;
            }
        }
		
        fclose( $fp );
    }

    /**
	 * Get All HostGroups that are managed by specified ContactGroup.
	 *
	 * @access public
	 */
    function getContactgroupHostgroups( $cgroupname )
    {
        $hgroups = array();
		
        foreach ( $this->hostgroups as $hgroupname => $hgroupparms ) 
		{
            $members = explode( ',', $hgroupparms['contact_groups'] );
			
            if ( in_array( $cgroupname, $members ) )
	            $hgroups[$hgroupname] = $this->hostgroups[$hgroupname];
        }
		
        return $hgroups;
    }

	/**
	 * @access public
	 */	
    function getContactContactgroups( $contactname )
    {
        $groups = array();
		
        foreach ( $this->contactgroups as $groupname => $groupparms ) 
		{
            $members = explode( ',', $groupparms['members'] );
            
			if ( in_array( $contactname, $members ) )
	            $groups[$groupname] = $this->contactgroups[$groupname];
        }
		
        return $groups;
    }
	
	/**
	 * @access public
	 */
    function getHostgroupMembers( $groupname )
    {
        return explode( ',', $this->hostgroups[$groupname]['members'] );
    }

	/**
	 * @access public
	 */
    function getHostsGroups( $host )
    {
        $hosts_groups = array();
		
        foreach ( $this->hostgroups as $groupname => $group ) 
		{
            $members = explode( ',', $group['members'] );
			
            if ( in_array( $host, $members ) )
	        	$hosts_groups[] = $groupname;
        }
		
        return $hosts_groups;
    }

	/**
	 * @access public
	 */
    function getServiceStatus( $host, $service )
    {
        return $this->service_status[$host][$service];
    }

	/**
	 * @access public
	 */
    function getHostByAddr( $ipaddr )
    {
        foreach ( $this->hosts as $hostname => $parms )
        {
            if ( $parms['register'] === 0 ) 
				continue;
            
			if ( $parms['address'] == $ipaddr )
                return $parms['host_name'];
        }
		
        return false;
    }
	
	/**
	 * @access public
	 */
    function getHostServices( $host )
    {
        $hostservices = array();
		$hosts_groups = $this->getHostsGroups( $host );
    
		foreach ( $this->services as $service => $parms )
        {
            if ( $parms['register'] == '0' ) 
				continue;
            
			$hosts = explode( ',', $parms['host'] );
	    	$services_groups = explode( ',', $parms['hostgroup_name'] );
			
            if ( in_array( $host, $hosts ) )
                $hostservices[$service] = $parms;
            
	    	foreach ( $hosts_groups as $key => $host_group ) 
			{
	        	if ( in_array( $host_group, $services_groups ) )
		    		$hostservices[$service] = $parms;
	    	}
        }

        return $hostservices;
    }
    
	
	// private methods

	/**
	 * @access private
	 */
    function _parseObjectFile( $filename )
    {
        $scanstate = '';

        if ( ( $fp = fopen( $filename, 'r' ) ) == false ) 
			return false;
		
        while ( $line = fgets( $fp, 987 ) ) 
		{
            if ( preg_match( '/^\s*(|#.*)$/', $line ) )
                continue;
            
            if ( preg_match( '/^\s*define\s+(\S+)\s*{\s*$/', $line, $regs ) ) 
			{
                $scanstate = $regs[1];
                $tmpobject = array();
                
				continue;
            }
			
			// completed object
            if ( preg_match( '/\s*}/', $line ) ) 
			{
                switch( $scanstate ) 
				{
                	case 'contactgroup':
                   	 	if ( !empty( $tmpobject['contactgroup_name'] ) )
                        	$this->contactgroups[$tmpobject['contactgroup_name']] = $tmpobject;
                    
                    	break;
                
					case 'contact':
                    	if ( !empty( $tmpobject['contact_name'] ) )
                        	$this->contacts[$tmpobject['contact_name']] = $tmpobject;
                    
                    	break;
                
					case 'host':
                    	if ( !empty( $tmpobject['host_name'] ) )
                        	$this->hosts[$tmpobject['host_name']] = $tmpobject;
                    
                    	if ( !empty( $tmpobject['name'] ) )
                        	$this->hosts[$tmpobject['name']] = $tmpobject;
                    
                    	break;
                
					case 'hostgroup':
                    	if ( !empty( $tmpobject['hostgroup_name'] ) )
                        	$this->hostgroups[$tmpobject['hostgroup_name']] = $tmpobject;
                    
                    	break;
                
					case 'service':
                    	if ( !empty( $tmpobject['name'] ) )
                        	$this->services[$tmpobject['name']] = $tmpobject;
                    
                    	break;
                }
				
                $scanstate = '';
                continue;
            }
			
            if ( preg_match( '/\s*(\S+)\s+(\S+)/', $line, $regs ) ) 
			{
                if ( $regs[1] == 'use' ) 
				{
                    $registered = isset( $tmpobject['register'] );
					
                    switch ( $scanstate ) 
					{
                    	case 'host':
                        	$tmpobject = array_merge( $this->hosts[$regs[2]], $tmpobject );
                        	break;
							
                    	case 'hostgroup':
                        	$tmpobject = array_merge( $this->hostgroups[$regs[2]], $tmpobject );
                        	break;
                    
						case 'hostgroup':
                        	$tmpobject = array_merge( $this->services[$regs[2]], $tmpobject );
                        	break;
                    }
					
                    if ( !$registered )
                        unset( $tmpobject['register'] );
                } 
				else 
				{
                    $tmpobject[$regs[1]] = $regs[2];
                }
				
                continue;
            }
        }
    }
} // END OF NagiosFile

?>
