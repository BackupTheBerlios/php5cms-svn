<?php

require( '../../../../../../prepend.php' );

using( 'peer.mail.sieve.Sieve' );


/**
 * These variable have to be modified so as to match your account and server
 * settings
 */

$server         = "mail.example.com";
$port           = 2000;
$acctname       = "joe";
$acctpass       = "blow";
$testscriptname = "testsievescript";
$authtypes      = "PLAIN";


/* You should not need to modify anything below this line */

print "<html>";
print "<head>";
print "<title>Sieve Test</title>";
print "</head>";
print "<body>";
print "<h2>[Step 1] Trying to login to sieve server:</h2>";
print "Requested authentication types are: $authtypes <br />";


$sieve = new Sieve( $server, $port, $acctname, $acctpass, $acctname, $authtypes );

$res = $sieve->login();

if ( $res && !PEAR::isError( $res ) )
{
    print "Login Successful.<br>";
}
else
{
    print "Login Failed! Please check your settings in testsieve_config.php.<br />";
    print "If they are correct, then you should check your SIEVE server setup.<br />";
    print "Also check your server logs.";
    
	exit;
}


print "<h2>[Step 2]  Verify server information: </h2><br><blockquote>";
print "<p><strong>Server version:</strong> " . $sieve->capabilities["implementation"] . "</p>";
print "<p><strong>Server auth types:</strong></p>";
print '<ul>';

while ( list( $key, $value ) = each( $sieve->capabilities["auth"] ) )
	print '<li>'.$key."</li>";
  
print "</ul>";
print "<p><strong>Server modules:</strong></p>";
print '<ul>';

while ( list( $key, $value ) = each( $sieve->capabilities["modules"] ) )
	print '<li>'.$key."</li>";

print "</ul>";
print "<p><strong>STARTTLS option:</strong></p>";
print '<ul>';

if ( isset( $sieve->capabilities['starttls'] ) && $sieve->capabilities['starttls'] == true )
  	print "<li>Available.</li>";
else
  	print "<li>Not Available.</li>";

print "</ul>";
print "<br>";

if ( isset( $sieve->capabilities["unknown"] ) )
{
    print "<i>--- Unknown server header info:</i>";
    
	if ( is_array( $sieve->capabilities["unknown"] ) )
	{
        foreach ( $sieve->capabilities["unknown"] as $unk )
            print "-> $unk <br>";
	}
    else
	{
        print "-> ".$sieve->capabilities["unknown"]."<br>";
	}
}

print "</blockquote>";
print "<h2>[Step 3]  List available scripts for this user: </h2>";

$res = $sieve->listScripts()
if ( $res && !PEAR::isError( $res ) )
{
    // print "\n<br>RESPONSE: "; print_r($sieve->response);

    if ( is_array( $sieve->response ) ) 
	{
        print '<ol>';
        
		foreach ( $sieve->response as $result )
            print '<li>'. $result .'</li>';
		
		print '</ol>';
    } 
	else 
	{
      	 print "$result<br>";
    }

    if ( isset( $sieve->response["ACTIVE"] ) ) 
	{
		$myactivescript = $sieve->response["ACTIVE"];
        print "<p>Active script: <strong>" . $myactivescript . "</strong></p>";
    }
    else
	{
        print "<p>No active scripts found!</p>>";
	}
}
else
{
    print "<p>No Scripts found on server!</p>";
}

print "<h2>[Step 4] Download/Display active script</h2>";

if ( isset( $myactivescript ) )  
{
	$i = 0; 
	$activescript = "";
	
	$res = $sieve->getScript( $myactivescript );
	
   	if ( $res && !PEAR::isError( $res ) ) 
	{
     	print '<small><blockquote>';
     	
		if ( is_array( $sieve->response ) ) 
		{
       		foreach ( $sieve->response as $result ) 
			{
         		print ++$i . ": $result <br>";
         		$activescript .= $result;
       		}
     	} 
		else 
		{
       		print "> $result <br>";
     	}
     	
		print '</blockquote></small>';
	} 
	else 
	{
		print "Sieve Error: " . $res->getMessage();
	}  
} 
else
{
	print "No active scripts found!<br>";
}

print "<h2>[Step 5] Maniupliate/Upload script</h2>";

if ( !isset( $activescript ) || $activescript == "" ) 
{
  	print "No active script!  Creating one<P>";
  	$activescript = "require \"vacation\";
	vacation \"Sorry, I'm away.
	I'll read your message when I get around to it.
	Thanks.\";
	";
} 
else 
{
  	print "Adding comment to end of existing script.<BR>";
  	$activescript .= "/* Adding a comment to end of script */";
}

$testscriptname = "sievephp_testscript";
print "New script name: $testscriptname<br />";

print "New script: <small><pre>\n$activescript\n</pre></small>";
print "<p>Sending $testscriptname...";

$res = $sieve->sendScript( $testscriptname, $activescript );

if ( $res && !PEAR::isError( $res ) ) 
{
	print "Upload Succeded.<br>";
}
else 
{
    print "Failed.<br />Error Message:";
    print $sieve->getMessage();
}

print "Making script active...";

$res = $sieve->setScriptActive( $testscriptname );

if ( $res && !PEAR::isError( $res ) ) 
{
    print "Succedded . <br />";
} 
else 
{
    print "Failed.<br />Error Message:";
    print $sieve->getMessage();
}

print "<h2>[Step 6]  Logging out:</h2>";

$res = $sieve->logout();

if ( $res && !PEAR::isError( $res ) ) 
{
    print "OK<br>";
} 
else 
{
    print "Failed.<br />Error Message:";
    print $sieve->getMessage();
}

?>
