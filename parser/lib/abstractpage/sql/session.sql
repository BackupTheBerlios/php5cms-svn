# Session SQL File

#
# Sessions Table Struct
#
CREATE TABLE session(
	session_id 	char(32) NOT NULL,
	expires 	int,
	data 		text,
	PRIMARY KEY (session_id),
	INDEX (expires)
);
