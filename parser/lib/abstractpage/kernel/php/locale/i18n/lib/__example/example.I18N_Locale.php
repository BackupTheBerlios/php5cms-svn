<?php

require( '../../../../../../prepend.php' );

using( 'locale.i18n.lib.I18N_Locale' );


$time   = time();
$number = 1234.5678;
$amount = 2001.11;

$localeArr = array(
    'de_DE',
    'es_ES',
    'fr_FR',
    'tr_TR',
	'en_GB'

);

echo "<pre>\n";

foreach ( $localeArr as $each )
{
    $locale = I18N_Locale::factory( $each );
        
    echo $locale->regionalSettings[I18N_REGIONAL_INTL_LANGUAGE_NAME] . "\n\n";
        
    echo "Date:\t" . 
        $locale->formatDate( $time, I18N_DATETIME_FULL   ) . " | " . 
        $locale->formatDate( $time, I18N_DATETIME_MEDIUM ) . " | " . 
        $locale->formatDate( $time, I18N_DATETIME_SHORT  );
    
    echo "\n";
	
    echo "Time:\t" .
        $locale->formatTime( $time, I18N_DATETIME_FULL   ) . " | " . 
        $locale->formatTime( $time, I18N_DATETIME_MEDIUM ) . " | " . 
        $locale->formatTime( $time, I18N_DATETIME_SHORT  );
	
    echo "\n";
    
    echo "Number:\t" . 
        $locale->formatNumber( $number, I18N_NUMBER_FLOAT   ) . " | " .
        $locale->formatNumber( $number, I18N_NUMBER_INTEGER );
    
    echo "\n";
    
    echo "Money:\t" . 
        $locale->formatCurrency( $amount, I18N_CURRENCY_LOCAL       ) . " | " .
        $locale->formatCurrency( $amount, I18N_CURRENCY_SYMBOL_INTL ) . " | " .
        $locale->formatCurrency( $amount, I18N_CURRENCY_SYMBOL      );

    echo "\n</pre><hr /><pre>\n";
}

echo "</pre>\n";

?>
