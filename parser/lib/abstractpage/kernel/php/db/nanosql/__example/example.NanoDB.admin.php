<?php

require( '../../../../../prepend.php' );

using( 'db.nanosql.NanoDB' );
using( 'db.nanosql.backup.NanoDBBackup' );


// Configuration

// Username/Password used to access the database.
define( "ADMIN_USERNAME", "admin" );
define( "ADMIN_PASSWORD", "admin" );

// You probably don't want to change the following constants.
// Note that we assume there is a "db" directory available to 
// us -- change it as required.
define( "ADMIN_DB", "nanoadmin" );		// admin DB to store login cookie
define( "SESSION_TIMEOUT", 60 * 60 );	// 1hr session timeout


// HTML header
$HTML_HEAD = <<<ENDH
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>
<head>

<title>NanoDB Manager</title>

</head>

<body text="#000000" bgcolor="#ffffff" link="#0000ff" vlink="#0000ff" alink="#0000ff">
<h1>NanoDB Manager</h1>
ENDH;

// HTML footer
$HTML_TAIL = <<<ENDT
</body>
</html>
ENDT;

// Check for an existing session.
$db = new NanoDB();
$db->autoclean( 5 );

if ( !$db->open( NANODB_DB_DIRECTORY . ADMIN_DB ) )
{
   	// Create the login database.
   	$schema = array(
      	array( "sessionid", NANODB_STRING, "key" )
   	);
   
   	if ( !$db->create( NANODB_DB_DIRECTORY . ADMIN_DB, $schema ) )
      	die( "Cannot create admin database." );
}

// Set up variables to known values.
if ( !isset( $cmd ) ) 
	$cmd = "";

if ( !isset( $orderby ) ) 
	$orderby = "";

// Is the user trying to log in?
if ( $cmd == "login" )
{
   	// Verify password
   	if ( ( $username == ADMIN_USERNAME ) && ( $password == ADMIN_PASSWORD ) )
   	{
      	// Create a new session.
      	do
      	{
         	$session["sessionid"] = $sessionid = generate_sessionid( 50 );
      	} while ( !$db->add( $session ) );

      	// Set the session cookie.
      	setcookie( "sessionid", $sessionid );
   	}
   	else
   	{
      	// Invalid password
      	login_page( "<b>Invalid username or password.</b><br><br>\n" );
      	return;
   	}
}

if ( !isset( $sessionid ) )
{
   	// Not in a session: then ask them to log in.
   	login_page();
   	return;
}
else
{
   	// Verify the cookie.
   	$session = $db->getbykey( $sessionid );
	
   	if ( !$session ) 
   	{
      	// Invalid cookie: expire it.
      	setcookie( "sessionid", $sessionid, 0 );
      	login_page();
      
	  	return;
   	}
}

// Do special commands here that must be done before any HTML is output.
switch ( $cmd )
{
   	case "logout":
      	// Expire their cookie.
      	setcookie( "sessionid", $sessionid, 0 );
      
      	// Delete the session from the database.
      	$db->removebykey( $session["sessionid"] );
      
      	echo $HTML_HEAD;
      	echo "<p>You have been logged out.</p>\n";
      	echo $HTML_TAIL;
      
	  	return;
   
   	case "backupdb":
      	$format = stripslashes( $format );
      	send_dbsnapshot( $format );
      
	  	return;
}

$db->close();
echo $HTML_HEAD;

switch ( $cmd )
{
   	case "showdb":
		$dbname  = stripslashes( $dbname  );
		$orderby = stripslashes( $orderby );
		echo "   <h2>Database Listing of '$dbname'</h2>\n";
      
	  	if ( $orderby == "" )
         	$orderby = NULL;
      
	  	dump_database( $dbname, true, $orderby );
      	break;

   	case "drop":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Drop Database of '$dbname'</h2>\n";
      	confirm_drop_database( $dbname );
      
	  	break;
   
   	case "dropped":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Database Index of '$dbname'</h2>\n";
      	drop_database( $dbname );
      	list_databases();
      
	  	break;

   	case "addrecord":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Add Record to '$dbname'</h2>\n";
      	show_add_edit( $dbname );
      
	  	break;

   	case "addedrecord":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Database Listing of '$dbname'</h2>\n";
      	add_entry( $dbname, $record );
      	dump_database( $dbname, true );
      
	  	break;

   	case "addfield":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Add Field to '$dbname'</h2>\n";
      	show_add_field( $dbname );
      
	  	break;

   	case "addedfield":
      	$dbname    = stripslashes( $dbname );
      	$fieldname = stripslashes( $fieldname );
      	$default   = ( isset( $default )? stripslashes( $default ) : "" );
      	$iskey     = ( isset( $iskey   )? $iskey : "" );
      	echo "   <h2>Database Listing of '$dbname'</h2>\n";
      	add_field( $dbname, $fieldname, $fieldtype, $default, $iskey );
      	dump_database( $dbname, true );
      
	  	break;

	case "editrecord":
		$dbname = stripslashes( $dbname );
		echo "   <h2>Edit Record in '$dbname'</h2>\n";
		show_add_edit( $dbname, NanoDB::str2int( $index ) );

		break;

   	case "editedrecord":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Database Listing of '$dbname'</h2>\n";
      	edit_entry( $dbname, $record, NanoDB::str2int( $index ) );
      	dump_database( $dbname, true );
      
	  	break;

   	case "deletedrecord":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Database Listing of '$dbname'</h2>\n";
      	delete_entry( $dbname, NanoDB::str2int( $index ) );
      	dump_database( $dbname, true );
      
	  	break;

   	case "deletefield":
      	$dbname = stripslashes( $dbname );
      	echo "   <h2>Delete Field in '$dbname'</h2>\n";
      	show_delete_field( $dbname );
      
	  	break;

   	case "deletedfield":
      	$dbname    = stripslashes( $dbname );
      	$fieldname = stripslashes( $fieldname );
      	echo "   <h2>Database Listing</h2>\n";
      	delete_field( $dbname, $fieldname );
      	dump_database( $dbname, true );
      
	  	break;

   	case "createdb":
      	echo "   <h2>Create New Database</h2>\n";
      	show_create_database();
      
	  	break;

   	case "createddb":
		$dbname = stripslashes( $dbname );
      	echo "   <h2>Create Database</h2>\n";
      
	  	if ( create_database( $dbname ) )
         	dump_database( $dbname, true );
      	else
         	list_databases();
      
	  	break;

   	case "cleanup":
      	$dbname = stripslashes( $dbname );
      
      	if ( !isset( $show ) )
         	echo "   <h2>Database Listing of '$dbname'</h2>\n";
      
      	cleanup_database( $dbname );

      	if ( !isset( $show ) )
         	dump_database( $dbname, true );
      	else
         	list_databases();

      	break;

   	default:
      	echo "   <h2>Database Index</h2>\n";
      	list_databases();
      
	  	break;
}

echo  "   [ <a href=\"$PHP_SELF?cmd=showalldb\">Database Index</a> ] "
	. "[ Backup databases as "
	. "<a href=\"$PHP_SELF?cmd=backupdb&format=PHPAR\">PHPAR</a> "
    . "<a href=\"$PHP_SELF?cmd=backupdb&format=PEAR\">PEAR</a> "
    . "<a href=\"$PHP_SELF?cmd=backupdb&format=RAWAR\">RAWAR</a>"
    . " ] "
    . "[ <a href=\"$PHP_SELF?cmd=logout\">Logout</a> ] "
    . "\n";

echo $HTML_TAIL;
return;




function generate_sessionid( $length )
{
   	$chars = "abcdefghijklmnopqrstuvwzyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
   	srand( time() );
   	$result = "";
   
   	for ( $i = 0; $i < $length; $i++ )
      	$result .= $chars{ rand() % strlen( $chars )};

   	return $result;
}

function login_page( $message = "" )
{
   	global $HTML_HEAD, $HTML_TAIL;
   	global $PHP_SELF;

   	echo $HTML_HEAD;

   	if ( $message != "" )
      	echo $message;

	?>
   	<p>Please enter your login name and password:</p>
	<form method="post" action="<?php echo $PHP_SELF; ?>">
	<input type="hidden" name="cmd" value="login">
	<table border=0>
	<tr>
		<td>Username:</td>
		<td><input type="text" name="username"></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="password" name="password"></td>
	</tr>
	</table>
	<input type="submit" value="Log in">
	</form>
	<?php
   	echo $HTML_TAIL;
}

function list_databases()
{
   	global $PHP_SELF;

	?>
   	<table border=1 cellpadding=2 width=100%>
   	<tr bgcolor="#f0f0f0">
      	<th>Database Name</th>
      	<th>Records</th>
      	<th>Dirty</th>
      	<th>[Tools]</th>
   	</tr>
	<?php
   	$db = new NanoDB();
   	$handle = opendir( NANODB_DB_DIRECTORY ); 
   	$count  = 0;
   
   	while ( false != ( $file = readdir( $handle ) ) ) 
	{
      	if ( preg_match( "/\.meta$/i", $file ) && ( $file != ADMIN_DB . ".meta" ) )
      	{
         	// Show the database name
         	if ( $count % 2 == 0 )
            	echo "   <tr bgcolor=\"#c0c0c0\">\n";
         	else
            	echo "   <tr>\n";

         	++$count;

         	// We have a database file. Extract the database name.
         	$dbname = substr( $file, 0, strlen( $file ) - 5 );

			echo "      <td><a href=\"$PHP_SELF?cmd=showdb&dbname=$dbname\">$dbname</a></td>\n";

			// Open the database, and show how many records
			if ( $db->open( NANODB_DB_DIRECTORY . $dbname ) )
         	{
            	echo "      <td><center>" . $db->size() . "</center></td>\n";
            	echo "      <td><center>" . $db->sizedirty() . "</center></td>\n";
            	echo "      <td><center>";
            	echo "[ <a href=\"$PHP_SELF?cmd=drop&dbname=$dbname\">delete</a> ] ";
            	echo "[ <a href=\"$PHP_SELF?cmd=cleanup&dbname=$dbname&show=index\">clean</a> ] ";
            	echo "</center></td>\n";
            
				$db->close();
         	}
         	else
         	{
            	echo "      <td colspan=3><center><i>Error Opening</i></center></td>\n";
         	}

         	echo "   </tr>\n";
      	}
   	}
   
   	if ($count == 0)
   	{
      	// Nothing in the list...
     	echo "   <tr>\n";
      	echo "      <td colspan=4><center><i>None Available</i></center></td>\n";
      	echo "   </tr>\n";
   	}
   
   	echo "   </table>\n";
   	echo "   <br>\n";

  	closedir( $handle ); 

   	echo "   [ <a href=\"$PHP_SELF?cmd=showalldb\">Refresh</a> ]";
   	echo " [ <a href=\"$PHP_SELF?cmd=createdb\">Create New Database</a> ]\n";
   	echo "   <hr>\n";
}      

function confirm_drop_database( $dbname )
{
   	global $PHP_SELF;

   	echo "<p>You are about to delete the database <b>$dbname</b> and all content. Please confirm.</p>\n";
   
   	// dump_database( $dbname );

	?>
   	<form method="post" action="<?php echo $PHP_SELF; ?>">
   	<input type="hidden" name="cmd" value="dropped">
   	<input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
   	<input type="submit" value="Yes! Drop Database">
   	</form>
	<?php
}

function dump_database( $dbname, $toolmenu = false, $orderby = null )
{
   	global $PHP_SELF;

   	$db = new NanoDB();
   	
	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Could not open database: $dbname\n";
      	return false;
   	}

   	$schema = &$db->schema();

   	echo count( $schema ) . " field" . ( ( count( $schema ) != 1 )? "s" : "" ) . "; ";
   	echo $db->size() . " record" . ( ( $db->size() != 1 )? "s" : "" );
   
   	if ( $orderby !== null )
      	echo ", ordered by field '<i>$orderby</i>'";
   
   	echo ":<br>\n";
   	echo "   <table border=1 cellspacing=2 width=100%>\n";

   	// Do not allow edits if the schema has arrays in it
   	$contains_array = false;

   	// Dump the DB schema
  	echo "   <tr bgcolor=\"#f0f0f0\">\n";
   	
	foreach ( $schema as $field )
   	{
		$name  = $field[0];
		$type  = $field[1];
		$iskey = isset( $field[2] ) && ( $field[2] == "key" );

      	echo "      <th>";
      	echo "<a href=\"$PHP_SELF?cmd=showdb&dbname=$dbname&orderby=$name\">$name</a> ";

      	// Show the key type in italics.
      	if ( $iskey ) 
			echo "<i>";
      
	  	echo "(";
      
	  	switch ( $type )
      	{
         	case NANODB_INT:
            	echo "int";
            	break;
         
		 	case NANODB_INT_AUTOINC:
            	echo "auto-inc int";
            	break;
         
		 	case NANODB_FLOAT:
            	echo "float";
            	break;
         
		 	case NANODB_BOOL:
            	echo "boolean";
            	break;
        
		 	case NANODB_STRING:
            	echo "string";
            	break;
         
		 	case NANODB_ARRAY:
            	echo "array";
            	$contains_array = true;
            	break;
      	}
      
	  	if ( $iskey ) 
			echo " - key";
      
	  	echo ")";
      
	  	if ( $iskey ) 
			echo "</i>";
      
      	echo "</th>\n";
   	}                        

	// Add in the tool menu if required
   	if ( $toolmenu )
      	echo "      <th>[Tools]</th>\n";

   	echo "   </tr>\n";

   	if ( $db->size() == 0 )
   	{
      	echo "      <td colspan=" . ( count( $schema ) + 1 ) . "><center><i>No records</i></center></td>\n";
   	}
   	else
   	{
      	// Dump the DB contents.  Note that we request that each record
      	// has an extra (optional) field added to it, the 'NANODB_IFIELD' field.  
      	// This will contain the original order index of each record, that we
      	// can then use foe editing and deletion, rather than a primary key.
      	$allrecords = &$db->getall( $orderby, true );
      	$rcount = 0;
      
	  	foreach ( $allrecords as $record )
      	{
         	$index = $record[NANODB_IFIELD];

         	if ( $rcount % 2 == 0 )
            	echo "   <tr bgcolor=\"#c0c0c0\">\n";
         	else
            	echo "   <tr>\n";

			foreach ( $schema as $field )
			{
				$field_name = &$field[0];
				echo "      <td>";
				
				if ( is_array( $record[$field_name] ) )
					print_r( $record[$field_name] );
				else if ( is_bool( $record[$field_name] ) )
					echo ( ( $record[$field_name] == true )? "true" : "false" );
            	else
               		echo $record[$field_name];
            
				echo "</td>\n";
         	}

			if ( $toolmenu )
         	{
            	echo "      <td><center>";
            
            	// Edit record
            	echo "[ <a href=\"$PHP_SELF?cmd=editrecord&dbname=$dbname&index=$index\">edit</a> ]";

            	// Delete record
            	echo "[ <a href=\"$PHP_SELF?cmd=deletedrecord&dbname=$dbname&index=$index\">delete</a> ] ";
            
            	echo "</center></td>\n";
         	}

         	echo "   </tr>\n";
         	++$rcount;
      	}
   	}

   	echo "   </table>\n";
   	echo "   <br>\n";

	if ( $toolmenu )
   	{
      	echo "   [ <a href=\"$PHP_SELF?cmd=showdb&dbname=$dbname";
      
	  	if ( $orderby !== null ) 
			echo "&orderby=$orderby";
      
	  	echo "\">Refresh</a> ] ";
      	echo "[ <a href=\"$PHP_SELF?cmd=addrecord&dbname=$dbname\">Add Record</a> ] ";
      	echo "[ <a href=\"$PHP_SELF?cmd=addfield&dbname=$dbname\">Add Field</a> ] ";
      	echo "[ <a href=\"$PHP_SELF?cmd=deletefield&dbname=$dbname\">Delete Field</a> ] ";
      	echo "[ <a href=\"$PHP_SELF?cmd=cleanup&dbname=$dbname\">Cleanup Database</a> (".$db->sizedirty()." dirty record".(($db->sizedirty() != 1)?"s":".").") ] ";
      	echo "   <hr>\n";
   	}

   	$db->close();
}

function show_add_edit( $dbname, $index = -1 )
{
   	global $PHP_SELF;

   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Could not open database: $dbname\n";
      	return false;
   	}

   	$schema = &$db->schema();

   	if ( count( $schema ) == 0 )
   	{
      	echo "   <p>You cannot add a record to a database that does not have any fields.</p>\n";
      	return;
   	}

   	if ( $index != -1 )
   	{
      	$record = &$db->getbyindex( $index );
      
	  	if ( !$record )
      	{
         	echo "Could not retrieve item to edit, from database: $dbname\n";
         	return false;
      	}
   	}

   	if ( $index == -1 )
      	echo "   <p>Please enter a new record:</p>\n";
   	else
      	echo "   <p>Please edit the record:</p>\n";

	?>
   	<form method="post" action="<?php echo $PHP_SELF; ?>">
   	<input type="hidden" name="cmd" value="<?php if ($index == -1) echo "addedrecord"; else echo "editedrecord"; ?>">
   	<input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
   	<input type="hidden" name="index" value="<?php echo $index; ?>">
   	<table border=0>
	<?php
   	foreach ( $schema as $field )
	{
      	$name  = &$field[0];
      	$type  = &$field[1];
      	$iskey = ( isset( $field[2] ) && ( $field[2] == "key" ) ) || ( isset( $field[3] ) && ( $field[3] == "key" ) );

      	echo "   <tr>\n";
      
	  	if ( $iskey )
         	echo "      <td><b><i>$name</i></b>:</td>\n";
      	else
         	echo "      <td><b>$name</b>:</td>\n";

     	switch ( $type )
      	{
         	case NANODB_ARRAY:
            	echo "      <td>[ Not Supported ]</td>\n";
            	break;

         	case NANODB_INT_AUTOINC:
            	if ( $index == -1 )
            	{
               		// Text entry
               		echo "      <td>[ Determined by Database ]</td>\n";
            	}
            	else
            	{
               		// Editing
               		echo  "      <td>"
						. "[ Determined by Database as: " . $record[$name] . " ]"
						. "<input type=\"hidden\" name=\"record[$name]\" value=\""
						. $record[$name]
						. "\"></td>\n";
            	}
            
				break;

         	case NANODB_BOOL:
            	if ( $index == -1 )
            	{
               		// Text entry
               		echo   "      <td>"
                   		 . "<input type=\"radio\" name=\"record[$name]\" value=\"true\">true "
                   		 . "<input type=\"radio\" name=\"record[$name]\" value=\"false\" checked>false"
                   		 . "</td>\n";
            	}
            	else
            	{
               		// Editing
               		$true  = ( $record[$name] == true )? "checked" : "";
               		$false = ( $record[$name] == true )? "" : "checked";

					echo  "      <td>"
						. "<input type=\"radio\" name=\"record[$name]\" value=\"true\" $true>true "
						. "<input type=\"radio\" name=\"record[$name]\" value=\"false\" $false>false"
						. "</td>\n";
            	}
            
				break;

         	default: /* All other types */
            	if ( $index == -1 )
            	{
               		// Text entry
               		echo "      <td>";
               
			   		if ( $type == NANODB_STRING )
					{
                  		echo  "<textarea "
                      		. "name=\"record[$name]\" "
                      		. "rows=\"2\" "
                      		. "cols=\"80\"></textarea>"
                      		. "</td>\n";
					}
                  	else
					{
                     	echo  "<input type=\"text\" name=\"record[$name]\" value=\""
                         	. ( isset( $record[$name] )? $record[$name] : "" )
                         	. "\"></td>\n";
					}
            	}
            	else
            	{
               		// Edit
               		// Do not allow editing of a primary key...
               		if ( $iskey )
               		{
                  		echo  "      <td><input type=\"hidden\" "
                      		. "name=\"record[$name]\" "
                      		. "value=\"$record[$name]\">"
                      		. $record[$name]
                      		. "</td>\n";
               		}
               		else
               		{
                  		echo "      <td>";
                  
				  		if ( $type == NANODB_STRING )
						{
                     		echo  "<textarea name=\"record[$name]\" rows=\"2\" cols=\"80\">"
                         		. $record[$name]
                         		. "</textarea></td>\n";
						}
                  		else
						{
                     		echo  "<input type=\"text\" name=\"record[$name]\" value=\""
                         		. $record[$name]
                         		. "\"></td>\n";
						}
               		}
            	}
            
				break;
      	}

      	echo "      <td><i>(";
      
	  	switch ($type)
		{
			case NANODB_INT:
				echo "int";
				break;

			case NANODB_INT_AUTOINC:
				echo "auto-inc int";
				break;
         
			case NANODB_STRING:
				echo "string";
				break;

			case NANODB_BOOL:
				echo "boolean";
				break;
         
			case NANODB_FLOAT:
				echo "float";
				break;

			case NANODB_ARRAY:
				echo "array";
				break;

			default:
				echo "UNKNOWN";
				break;
		}
		
		if ( $iskey )
         	echo " - primary key";
      
	  	echo ")</i></td>\n";
      	echo "   </tr>\n";
   	}
	
	?>
   	</table>
   	<p><input type="submit" value="<?php if ($index == -1) echo "Add New Record"; else echo "Edit Record"; ?>"></p>
   	</form>
	<?php

   	$db->close();
}

function show_add_field( $dbname )
{
   	global $PHP_SELF;

   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Could not open database: $dbname\n";
      	return false;
   	}

	?>
   	<p>Please enter a new field:</p>
   	<form method="post" action="<?php echo $PHP_SELF; ?>">
   	<input type="hidden" name="cmd" value="addedfield">
   	<input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
   	<table border=0>
   	<tr>
      	<td><b>Field name:</b></td>
      	<td><input type="text" name="fieldname"></td>
   	</tr>
   	<tr>
      	<td><b>Field type:</b></td>
      	<td>
       	<select name="fieldtype">
			<option>NANODB_INT
            <option>NANODB_INT_AUTOINC
            <option>NANODB_FLOAT
            <option>NANODB_BOOL
            <option>NANODB_STRING
            <option>NANODB_ARRAY
		</select>
		</td>
	</tr>
	<?php
   	if ( $db->size() > 0 )
   	{
	?>
   	<tr>
      	<td><b>Default value:</b></td>
      	<td><input type="text" name="default"></td>
   	</tr>
	<?php
   	}
	else
   	{
	?>
   	<tr>
      	<td><input type="checkbox" name="iskey"> Set as primary key.</td>
   </tr>
	<?php
   	}
	?>
   	</table>
   	<p><input type="submit" value="Add New Field"></p>
   	</form>
	<?php

   	$db->close();
}

function drop_database( $dbname )
{
   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Cannot open database: $dbname\n";
      	return false;
   	}

   	if ( !$db->drop() )
   	{
      	echo "Cannot drop database: $dbname\n";
      	return false;
   	}

   	echo "<p>Database <b>$dbname</b> dropped.</p>\n";
}

function send_dbsnapshot( $format = "PEAR" )
{
   	global $HTML_HEAD, $HTML_TAIL;
 
   	switch ( $format )
   	{
      	case "PEAR":
         	$ext = ".gz";
         	break;
      
	  	case "RAWAR":
         	$ext = ".bin";
         	break;
      
	  	case "PHPAR":
         	$ext = ".gz";
         	break;
      
      	default:
         	echo $HTML_HEAD;
         	echo "Invalid archive format.";
         	echo $HTML_TAIL;
         
		 	return false;
	}

	$ar = NanoDBBackup::factory( strtolower( $format ) );
   	$handle = opendir( NANODB_DB_DIRECTORY ); 
   
   	while ( ( $file = readdir( $handle ) ) !== false ) 
	{
      	if ( preg_match( "/\.meta$/i", $file ) || preg_match( "/\.data$/i", $file ) )
         	$ar->addfile( NANODB_DB_DIRECTORY . $file );
   	}
   
   	closedir( $handle ); 

   // Send the file to the browser
   $ar->send( "nanodb.backup-" . gmdate( "y.m.d" ) . $ext, NANODB_TMP_DIRECTORY );

   return true;
}

function add_entry( $dbname, $record )
{
   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Cannot open database: $dbname\n";
      	return false;
   	}

   	// Convert special fields if required
   	$schema = &$db->schema();
   
   	foreach ( $schema as $field )
   	{
      	switch ( $field[1] )
      	{
         	case NANODB_INT:
            	$record[$field[0]] = NanoDB::str2int( $record[$field[0]] );
            	break;

         	case NANODB_INT_AUTOINC:
            	// NanoDB determines this value...
            	break;

         	case NANODB_FLOAT:
            	$record[$field[0]] = str2float( $record[$field[0]] );
            	break;

         	case NANODB_BOOL:
            	$record[$field[0]] = ( $record[$field[0]] == "true" );
            	break;
         
         	case NANODB_STRING:
            	$record[$field[0]] = stripslashes( $record[$field[0]] );
            	break;
         
         	case NANODB_ARRAY:
            	// Unfortunately arrays aren't supported...
            	$record[$field[0]] = array();
            	break;
      	}
   	}

   	echo "<p><b>";
   	
	if ( $db->add( $record ) )
      	echo "Item added.";
   	else
      	echo "Could not add item.";
   
   	echo "</b></p>\n";
   	$db->close();
}

function edit_entry( $dbname, $record, $index )
{
   	$db = new NanoDB();
   	
	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Cannot open database: $dbname\n";
      	return false;
   	}

   	// Convert special fields if required.
   	$schema = &$db->schema();
   
   	foreach ( $schema as $field )
   	{
      	switch ( $field[1] )
      	{
         	case NANODB_INT_AUTOINC:
         
		 	case NANODB_INT:
            	$record[$field[0]] = NanoDB::str2int( $record[$field[0]] );
            	break;

			case NANODB_FLOAT:
            	$record[$field[0]] = NanoDB::str2float( $record[$field[0]] );
            	break;

			case NANODB_BOOL:
				$record[$field[0]] = ( $record[$field[0]] == "true" );
				break;
         
         	case NANODB_STRING:
            	$record[$field[0]] = stripslashes( $record[$field[0]] );
            	break;
         
         	case NANODB_ARRAY:
            	// Unfortunately arrays aren't supported...
            	$record[$field[0]] = array();
            	break;
      	}
   	}

   	echo "<p><b>";
   
   	if ( $db->edit( $record, $index ) )
      	echo "Item edited.";
   	else
      	echo "Could not edit item.";
   
   	echo "</b></p>\n";
   	$db->close();
}

function delete_entry( $dbname, $index )
{
   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Cannot open database: $dbname\n";
      	return false;
   	}

   	echo "<p><b>";
   
   	if ( $db->removebyindex( $index ) )
      	echo "Item deleted.";
   	else
      	echo "Could not delete item.";
   
   	echo "</b></p>\n";
   	$db->close();
}

function add_field( $dbname, $fieldname, $fieldtype, $default, $iskey )
{
   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Cannot open database: $dbname\n";
      	return false;
   	}

   	switch ( $fieldtype )
   	{
      	case "NANODB_INT":
         	$type    = NANODB_INT;
         	$default = NanoDB::str2int( $default );
         	break;

		case "NANODB_INT_AUTOINC":
			$type    = NANODB_INT_AUTOINC;
			$default = NanoDB::str2int( $default );
			break;

      	case "NANODB_FLOAT":
         	$type    = NANODB_FLOAT;
         	$default = NanoDB::str2float( $default );
         	break;

      	case "NANODB_BOOL":
         	$type    = NANODB_BOOL;
         	$default == ( strtolower( $default ) == "true" );
         	break;

      	case "NANODB_STRING":
         	$type = NANODB_STRING;
         	break;
      
      	case "NANODB_ARRAY":
         	$type    = NANODB_ARRAY;
         	$default = array();
         	break;
      
      	default:
         	echo "<p><b>Invalid field type</b></p>\n";
         	return;
   	}

   	echo "<p><b>";
   
   	if ( $db->addfield( $fieldname, $type, $default, $iskey == "on" ) )
      	echo "Field added.";
   	else
      	echo "Could not add field.";
   
   	echo "</b></p>\n";
   	$db->close();
}

function show_delete_field( $dbname )
{
   	global $PHP_SELF;

   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Could not open database: $dbname\n";
      	return false;
   	}

   	$schema = &$db->schema();

   	if ( count( $schema ) == 0 )
   	{
      	echo "   <p>There are no fields to delete.</p>\n";
      	return;
   	}
	?>
   	<p>Please select the field you wish to delete:</p>
   	<form method="post" action="<?php echo $PHP_SELF; ?>">
   	<input type="hidden" name="cmd" value="deletedfield">
   	<input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
   	<table border=0>
   	<tr>
      	<td><b>Field name:</b></td>
      	<td>
         	<select name="fieldname">
			<?php
         	foreach ( $schema as $field )
            	echo "            <option>" . $field[0] . "\n";
			?>
         	</select>
      	</td>
   	</tr>
   	</table>
   	<p><input type="submit" value="Delete Field"></p>
   	</form>
	<?php

   	$db->close();
}

function delete_field( $dbname, $fieldname )
{
   	$db = new NanoDB();
   
   	if ( !$db->open(NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Cannot open database: $dbname\n";
      	return false;
   	}

   	echo "<p><b>";
   
   	if ( $db->removefield( $fieldname ) )
      	echo "Field '$fieldname' deleted.";
   	else
      	echo "Could not delete field '$fieldname'.";
   
   	echo "</b></p>\n";
   	$db->close();
}

function show_create_database()
{
   	global $PHP_SELF;

	?>
   	<p>Please enter the name of the new database:</p>
   	<form method="post" action="<?php echo $PHP_SELF; ?>">
   	<input type="hidden" name="cmd" value="createddb">
   	<table border=0>
   	<tr>
      	<td><b>Database name:</b></td>
      	<td><input type="text" name="dbname"></td>
   	</tr>
   	</table>
   	<p><input type="submit" value="Create Database"></p>
   	</form>
	<?php
}

function create_database( $dbname )
{
   	$db = new NanoDB();

   	// Create an empty database with an empty schema
   	if ( !$db->create( NANODB_DB_DIRECTORY . $dbname, array() ) )
   	{
      	echo "<p><b>Could not create database.</b></p>\n";
      	return false;
   	}
   	
	$db->close();
   	echo "<p><b>Database created.</b></p>\n";
   
   	return true;
}

function cleanup_database( $dbname )
{
   	$db = new NanoDB();
   
   	if ( !$db->open( NANODB_DB_DIRECTORY . $dbname ) )
   	{
      	echo "Cannot open database: $dbname\n";
      	return false;
   	}

  	echo "<p><b>";
   
   	if ( $db->cleanup() )
      	echo "Database cleaned of dirty records.";
   	else
      	echo "Could not cleanup database.";
   
   	echo "</b></p>\n";
   	$db->close();
}

?>
