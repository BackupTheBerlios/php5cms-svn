<?php

require( '../../../../prepend.php' );

using( 'commerce.DTA' );


/**
 * Initialize new DTA file.
 * In this example the file contains debits.
 * This means that in an exchange the sender is the person, who gets the money
 * and the receiver is the person who has to pay.
 * You always have to differentiate between the DTA FILE SENDER and the MONEY SENDER.
 */

$dta_file = new DTA( DTA_DEBIT );

/**
 * Set file sender. This is also the default sender for transactions.
 */
$dta_file->setAccountFileSender( 
	array(
    	"name"           => "Michael Mustermann",
    	"bank_code"      => 11112222,
    	"account_number" => 87654321
) );

/**
 * Add transaction.
 */
$dta_file->addExchange(
    array(
        "name"           => "Franz Beispiel",     // Name ofaccount owner.
        "bank_code"      => 33334444,             // Bank code.
        "account_number" => 13579000,             // Account number.
    ),
    12.45,                                        // Amount of money.
    array(                                        // Description of the transaction ("Verwendungszweck").
        "Bill Nr. 01234",
        "Information"
    )
);

/**
 * Output DTA-File.
 */
echo $dta_file->getFileContent();

/**
 * Write DTA-File.
 */
$dta_file->saveFile( "DTAUS0" );

?>
