<?php

require( '../../../../../../prepend.php' );

using( 'peer.mail.sieve.Sieve' );


function display_login( $form = "" )
{
    if ( !strcmp( $form, "admin" ) ) 
	{
        print "<p align=center><b>Administrator demo site</b><br /><i>Please login.</i>";
        print "<table border=0 align=center><form method=POST>";
        print "<tr><td bgcolor=#d0d0d0>User Account [joeblow]: </td><td><input type=text name=uacctname></td></tr>";
        print "<tr><td bgcolor=#d0d0d0>Admin Account [cyrus-admin]: </td><td><input type=text name=uadminacct></td></tr>";
        print "<tr><td bgcolor=#d0d0d0>Admin Password: </td><td><input type=password name=uacctpass></td></tr>";
        print "<tr><td bgcolor=#d0d0d0>Server Name [mail.example.com]: </td><td><input type=text name=userver></td></tr>";
        print "<tr><td bgcolor=#d0d0d0>Server Port [2000]: </td><td><input type=text name=uport></td></tr>";
        print "<tr><td><input type=submit name=\"Login\" action=test.php&><td></tr>";
        print "</form></table>";
        print "<br /><br /><a href=test.php?setform=none><i>Access the normal-user login page</i></a></p></body></html>";
    } 
	else 
	{
        print "<p align=center><b>Demo site</b><br /><i>Please login.</i>";
        print "<table border=0 align=center><form method=POST>";
        print "<tr><td bgcolor=#d0d0d0>Account Name [joeblow]: </td><td><input type=text name=uacctname></td></tr>";
        print "<tr><td bgcolor=#d0d0d0>Password: </td><td><input type=password name=uacctpass></td></tr>";
        print "<tr><td bgcolor=#d0d0d0>Server Name [mail.example.com]: </td><td><input type=text name=userver></td></tr>";
        print "<tr><td bgcolor=#d0d0d0>Server Port [2000]: </td><td><input type=text name=uport></td></tr>";
        print "<tr><td><input type=submit name=\"Login\" action=test.php&><td></tr>";
        print "</form></table>";
        print "<br /><br /><a href=test.php?setform=admin><i>Access the administrator's page</i></a></p></body></html>";
    }    
}


if ( isset( $_POST['action'] ) )
	$action = $_POST['action'];
else if ( isset( $_GET['action'] ) )
	$action = $_POST['action'];
else
	$action = false;

if ( isset( $_POST['script'] ) )
	$script = $_POST['script'];
else if ( isset( $_GET['script'] ) )
	$script = $_GET['script'];
else
	$script = false;


$bgcolor="#c0c0c0";

if ( isset( $action ) && $action == "logout" )
{
    unset( $acctname );
    unset( $acctpass );
    unset( $server );
    unset( $port );
    unset( $adminacct );

    setcookie( "acctname",  "", 0 );
    setcookie( "acctpass",  "", 0 );
    setcookie( "server",    "", 0 );
    setcookie( "port",      "", 0 );
    setcookie( "adminacct", "", 0 );
}

/* This will allow the admin to switch user accounts on the fly without having to login again. */
if ( !strcmp( $action, "suser" ) && isset( $sacctname ) )
    $acctname = $sacctname;

/* This sets the default login view: admin or otherwise */
if ( isset( $setform ) && !strcmp( $setform, "admin" ) ) 
{
    setcookie( "form", "admin" );
    $form = "admin";
}
else if ( isset( $setform ) ) 
{
    setcookie( "form", "", 0 ); 
    $form = "";
}

/* If its not specified, the default view is other */
if ( !isset( $form ) )
    $form = "";

/* Make sure we have info to login with */
if ( !isset( $server ) || !isset( $port ) || !isset( $acctname ) || !isset( $acctpass ) )
{
    if ( !isset( $userver ) || !isset( $uport ) || !isset( $uacctname ) || !isset( $uacctpass ) )
	{
        display_login( $form );
        exit;
    }
    else
	{
        $server   = $userver;
        $port     = $uport;
        $acctname = $uacctname;
        $acctpass = $uacctpass;
        
		if ( strcmp( $uadminacct, "" ) )
            $adminacct = $uadminacct;
        else
            $adminacct = $acctname;
    }
}

/* Create sieve instance */
$sieve = new Sieve( $server, $port, $acctname, $acctpass, $adminacct );

/* Try to login properly */
$res = $sieve->login();

if ( $res && !PEAR::isError( $res ) )
{
    setcookie( "acctname",  $acctname  );
    setcookie( "acctpass",  $acctpass  );
    setcookie( "server",    $server    );
    setcookie( "port",      $port      );
    setcookie( "adminacct", $adminacct );
	
    if ( strcmp( $adminacct, $acctname ) ) 
	{
        print "<html><head><title>Sieve-PHP Administration Demo page</title></head><body bgcolor=$bgcolor>"; 
        print "<form method=POST>";
        print "<table border=0 width=100%>";
        print "<tr bgcolor=#8888ff><td width=80%><h3>Switch user account:</h3></td>";
		print "<td align=center width=15%><input name=sacctname value=$acctname type=line></td>";
		print "<td align=center width=5%><input type=submit value=Change></td></tr>";
		print "<input type=hidden name=action value=suser>";
        print "</table>";
        print "</form>";
    }
    else
	{
        print "<html><head><title>Sieve-PHP Demo page</title></head><body bgcolor=$bgcolor>";
  	}
}
else 
{
    print "<b>Unable to log into sieve server!</b><br />";
    print "<html><head><title>Sieve Demo page</title></head><body bgcolor=$bgcolor>";
    display_login( $form );

    exit;
}


$titleline = "Create new script on server";

switch( $action )
{
    case "act":
        if ( isset( $script ) )
		{
			$res = $sieve->setScriptActive( $script );
			
            if ( $res && !PEAR::isError( $res ) )
                print "Successfully changed active script!<br />";
            else
                print "Unable to change active script!<br />"; /* we could display the full output here */
        }
		
        break;

    case "get":
        if ( isset( $script ) )
		{
			$res = $sieve->getScript( $script );
			
            if ( $res && !PEAR::isError( $res ) )
			{
                if ( is_array( $sieve->response ) )
				{
                    foreach ( $sieve->response as $line )
					{
                        $textname   = $script;
                        $textarea  .= $line;
                        $titleline  = "Editing script \"$textname\".  <a href=$PHP_SELF>Create new script</a>";
                    }
				}
                else
				{
                    print $sieve->response . "<br />";
				}
            }
            else
			{
                print "Unable to get script from server.<br />";  /* we should probably show the user the errors */
			}
        }
		
        break;
    
	case "del";
        if ( isset( $script ) )
		{
			$res = $sieve->deleteScript( $script );
			
            if ( $res && !PEAR::isError( $res ) )
                print "Successfully deleted script from server.<br />";
            else
                print "Unable to delete script from server.<br />"; /* we should probaly show the use the errors */
        }
		
        break;
    
	case "send":
        if ( isset( $script ) && isset( $scriptname ) )
		{
			$res = $sieve->sendScript( $scriptname, stripslashes( $script ) );
			
            if ( $res && !PEAR::isError( $res ) )
			{
                print "Successfully loaded script onto server. (Remember to set it active!)<br />";
			}
            else
			{
                print "Unable to load script to server. See server response below:<br /><blockquote><font color=#aa0000>";
                print $res->getMessage();
                print "</font></blockquote>";
				
                $textarea  = stripslashes( $script );
                $textname  = $scriptname;
                $titleline = "Try editing the script again! <a href=$PHP_SELF>Create new script</a>";
            }
        }
		
        break;
}

  
$res = $sieve->listScripts();

if ( $res && !PEAR::isError( $res ) )
{
    if ( !isset( $sieve->response ) )
	{
        print "No scripts found on server<br />";
	}
    else if ( is_array( $sieve->response ) )
	{
        print "<i>Scripts available for this account.</i><br />";
        print "<table border=0>";
        
		foreach ( $sieve->response as $response )
		{
            if ( $rowcolor=="#dddddd" )
              $rowcolor = "#d0d0d0";
            else
              $rowcolor = "#dddddd";
			  
            print "<tr bgcolor=$rowcolor><td>Script (" . ++$i . "): </td>";
            print "<td> $response </td>";
            print "<td><a href=test.php?action=get&script=$response>View/Edit Script</a></td>";
            print "<td><a href=test.php?action=del&script=$response>Delete Script</a></td>";
            
			if ( $sieve->response["ACTIVE"] == $response )
                print "<td>*** Active ***</td></tr>";
            else
                print "<td><a href=test.php?action=act&script=$response>Activate Script</a></td></tr>";
        }
		
        print "</table>";
    }
    else
	{
        print "<table border=0>";
        print "<tr bgcolor=#d0d0d0><td>Script (1): </td>";
        print "<td> " . $sieve->response . "</td>";
        print "<td> <a href=test.php?action=get&script=" . $sieve->response . ">View/Edit Script</a></td>";
        print "<td><a href=test.php?action=del&script="  . $sieve->response . ">Delete Script</a></td>";
        
		if ( $sieve->response["ACTIVE"] == $response )
            print "<td align>*** Active ***</td></tr>";
        else
            print "<td><a href=test.php?action=act&script=$response>Activate Script</a></td></tr>";
        
		print "</table>";
        
		if ( isset( $sieve->response["ACTIVE"] ) )
            print "Active script: " . $sieve->response["ACTIVE"] . "<br />";
        else
            print "No active scripts found.<br />";
    }
}
else if ( $res->getCode() == EC_NOSCRIPTS )
{
    print "No scripts found for this account.<br />";
}
else
{
    print "Unable to get listing from sieve server!<br />";
}

print "<br /><a href=test.php?action=logout>Logout</a><br />";
print "<br /><hr /><i>$titleline</i>";
print "<form method=post>";
print "<table border=0>";
print "<tr><td bgcolor=#d0d0d0>Script name</td><td><input type=text name=scriptname value=$textname></td></tr>";
print "<tr><td bgcolor=#d0d0d0>Script</td><td><textarea name=script cols=100 rows=20>$textarea</textarea><input type=hidden name=action value=send></td></tr>";
print "<tr><td>&nbsp</td><td><input type=submit name=\"Send Script\" action=test.php></td></tr></table>";
print "</form>";
print "<hr />";
print "<i> A sample server script might be: </i><br />";

?>

require "fileinto";<br />
require "reject";<br />
<br />
## filter out our junk mail first... send to junk folder until the script<br />
## is stable.<br />
if header :contains "from" "@bigfoot" {<br />
    fileinto "INBOX.junkmail"; }<br />
<br />
## filter our subject lines into categories for our mailing lists.<br />
if header :contains "subject" "[PHP-DEV]" {<br />
    fileinto "INBOX.Listen.PHP-DEV"; }<br />
if header :contains "subject" "[PEAR]" {<br />
    fileinto "INBOX.Listen.PEAR"; }<br />
if header :contains "subject" "[PEAR-DEV]" {<br />
    fileinto "INBOX.Listen.PEAR-DEV"; }<br />
if header :contains "subject" "[imp]" {<br />
    fileinto "INBOX.Listen.imp"; }<br />
if header :contains "subject" "[dev]" {<br />
    fileinto "INBOX.Listen.imp-dev"; }<br />

<?php

print "<i>-or-</i><br />";
print "redirect \"joeblow@test.com\";<br />";
print "<i>-or-</i><br />";
print "require \"reject\";<br />";
print "if size :over 200K { <br />";
print "reject \"Sorry, I don't like large attachments!  Please send me a URL instead!\"; <br />";
print "}<br /><hr /><br />";
print "<b>This is approaching a semi-stable state.  Everything should work fine.</b>";

$sieve->logout();

print "</body></html>";

?>
