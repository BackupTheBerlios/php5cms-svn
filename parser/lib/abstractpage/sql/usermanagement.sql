# UserManagement SQL File

#
# Sessions Table Struct
#
CREATE TABLE usermanagement_sessions 
(
  	id 				bigint(20) 		unsigned NOT NULL auto_increment,
  	session_id 		varchar(32) 	NOT NULL default '',
  	session_start 	datetime 		NOT NULL default '0000-00-00 00:00:00',
  	session_stop 	datetime 		default NULL,
  	user_id 		int(10) 		unsigned NOT NULL default '0',
  	last_action 	int(10) 		UNSIGNED NOT NULL default '0',
  	remoteip 		varchar(15) 	NOT NULL default '',
  	browser 		varchar(255) 	default NULL,
  	referer 		varchar(255) 	default NULL,
  	PRIMARY KEY ( id ),
  	UNIQUE KEY session_id ( session_id )
) TYPE=MyISAM COMMENT='DBUM Session Data' AUTO_INCREMENT=1 ;



#
# Groups Table Struct
#
CREATE TABLE usermanagement_groups 
(
  	group_id 		int(10) 		unsigned NOT NULL auto_increment,
  	group_name 		varchar(100) 	NOT NULL default '',
  	group_desc 		varchar(255) 	default NULL,
  	activated 		tinyint(1) 		unsigned NOT NULL default '1',
  	level 			smallint(3) 	unsigned NOT NULL default '999',
  	PRIMARY KEY ( group_id ),
  	UNIQUE KEY group_name ( group_name )
) TYPE=MyISAM COMMENT='DBUM Groups Table' AUTO_INCREMENT=1 ;

#
# Groups Table Data
#
INSERT INTO usermanagement_groups VALUES( 1, 'admin',  'Admin Group',  1, 100 );
INSERT INTO usermanagement_groups VALUES( 2, 'user',   'User Group',   1, 500 );
INSERT INTO usermanagement_groups VALUES( 3, 'public', 'Public Group', 1, 900 );



#
# Users Table Struct
#
CREATE TABLE usermanagementusers 
(
  	user_id 		int(10) 		unsigned NOT NULL auto_increment,
  	group_id 		int(10) 		unsigned NOT NULL default '0',
  	session_id 		varchar(32) 	NOT NULL,
  	failed_logins 	smallint(2) 	unsigned NOT NULL default '0',
  	locked 			tinyint(1) 		unsigned NOT NULL default '0',
  	activated 		tinyint(1) 		unsigned NOT NULL default '0',
  	created 		datetime 		NOT NULL default '0000-00-00 00:00:00',
  	username 		varchar(50) 	NOT NULL,
  	password	 	varchar(40) 	NOT NULL,
  	first_name 		varchar(100) 	default NULL,
  	last_name 		varchar(100) 	default NULL,
  	street 			varchar(255) 	default NULL,
  	postcode 		varchar(20) 	default NULL,
  	hometown 		varchar(150) 	default NULL,
  	email 			varchar(150) 	NOT NULL,
  	website 		varchar(255) 	NULL,
  	telephone 		varchar(50) 	default NULL,
  	fax 			varchar(50) 	default NULL,
  	mobil 			varchar(50) 	default NULL,
  	signature 		text 			default NULL,
  	icq 			bigint(20) 		default NULL,
  	msn 			varchar(255) 	default NULL,
  	aim 			varchar(255) 	default NULL,
  	PRIMARY KEY  (user_id),
  	UNIQUE KEY session_id (session_id,username,email)
) TYPE=MyISAM COMMENT='DBUM Users Data Table' AUTO_INCREMENT=1 ;

#
# Groups Table Data
#
INSERT INTO usermanagement_users VALUES( 1, 1, 'cdbddf19b633bd90c53ac23936188746', 0, 0, 1, '2003-07-20 16:01:00', 'Admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', NULL, NULL, NULL, NULL, NULL, 'admin@localhost', NULL, NULL, NULL, NULL, NULL, NULL, NULL );
