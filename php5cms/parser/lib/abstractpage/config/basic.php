<?php

/* Don't change unless you know what you're doing */

$GLOBALS['AP_BASIC_CONFIGURATION'] = array(
	'settings' => array(
		'agent_name' 			=> 'Abstractpage',
		'cookie_path' 			=> '/abstractpage',
    	'cookie_domain' 		=> $_SERVER['SERVER_NAME'],
		'server_name' 			=> $_SERVER['SERVER_NAME'],
		'server_port' 			=> $_SERVER['SERVER_PORT']
	),
	'file' => array(
		/* some programs we rely on */
		'file_at' 				=> '/usr/bin/at',
		'file_df' 				=> '/bin/df',
		'file_passwd'	 		=> '/etc/passwd',
		'file_shadow' 			=> '/etc/shadow',
		'file_httperf' 			=> '/proc/httperf',
		'file_mounts' 			=> '/proc/mounts',
		'file_cpuinfo' 			=> '/proc/cpuinfo',
		'file_uptime' 			=> '/proc/uptime',
		'file_loadavg'	 		=> '/proc/loadavg',
		'file_version' 			=> '/proc/version',
		'file_dev' 				=> '/proc/net/dev',
		'file_meminfo' 			=> '/proc/meminfo',
		'file_scsi' 			=> '/proc/scsi/scsi',
		'file_sensor' 			=> '/usr/local/bin/sensors',
		'file_status' 			=> '/proc/self/status',
		'file_ide' 				=> '/proc/ide',
		'file_pci' 				=> '/proc/pci',
		'file_pgp' 				=> '/usr/local/bin/gpg',
		'file_hostname' 		=> '/proc/sys/kernel/hostname',
		'file_uptime' 			=> '/proc/uptime',
		'file_serialnumber' 	=> '/proc/serialnumber',
		'file_stat' 			=> '/proc/stat'
	),
	'path' => array(
		/* relative to AP_ROOT */
		'path_tmp_ap'			=> 'tmp/',
		'path_cache' 			=> 'tmp/cache/',
		'path_fonts'		 	=> 'data/fonts/',
		'path_metrics_afm' 		=> 'data/fonts/metrics/afm/',
		'path_metrics_php' 		=> 'data/fonts/metrics/php/',
		'path_encodings'		=> 'data/encodings/',
		'path_nano_db'			=> 'var/nano/',
		
		/* os stuff */
		'path_tmp_os' 			=> ( stristr( getenv( "OS" ), "Windows" )? 'C:\\temp\\'  : '/tmp/' )
	),
	'session' => array(
		'session_name'			=> 'Abstractpage',
		'session_timeout'		=> 0, /* session ends when user closes browser */
		'session_cache_limiter' => 'nocache',
		'session_handler_type'	=> 'none',
		'session_params'        => array(
		
			/* Callback functions for type 'ext', defaults to functions 
			   in file session.php which has to be included */
			'callback_open'     => 'ap_session_open',
			'callback_close'    => 'ap_session_close',
			'callback_read'     => 'ap_session_read',
			'callback_write'    => 'ap_session_write',
			'callback_destroy'  => 'ap_session_destroy',
			'callback_gc'       => 'ap_session_gc',
			
			/* Database or other drivers might require configuration 
			   parameters here. */
			'phptype'			=> 'mysql',
			'hostspec' 			=> 'localhost',
			'username' 			=> 'abstractpage',
			'password' 			=> '*****',
			'database' 			=> 'abstractpage'
		)
	)
);

?>
