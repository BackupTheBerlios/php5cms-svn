<?php

require( '../../../../../prepend.php' );

using( 'util.text.DoubleMetaPhone' );


$mp = new DoubleMetaPhone( "" );

if ( $mp->primary != "" || $mp->secondary != "" ) {
	print "<br>=(," . $mp->primary . "), (," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ALLERTON" );
if ( $mp->primary != "ALRT" || $mp->secondary != "ALRT" ) {
	print "<br>ALLERTON=(ALRT," . $mp->primary . "), (ALRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Acton" );
if ( $mp->primary != "AKTN" || $mp->secondary != "AKTN" ) {
	print "<br>Acton=(AKTN," . $mp->primary . "), (AKTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Adams" );
if ( $mp->primary != "ATMS" || $mp->secondary != "ATMS" ) {
	print "<br>Adams=(ATMS," . $mp->primary . "), (ATMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Aggar" );
if ( $mp->primary != "AKR" || $mp->secondary != "AKR" ) {
	print "<br>Aggar=(AKR," . $mp->primary . "), (AKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ahl" );
if ( $mp->primary != "AL" || $mp->secondary != "AL" ) {
	print "<br>Ahl=(AL," . $mp->primary . "), (AL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Aiken" );
if ( $mp->primary != "AKN" || $mp->secondary != "AKN" ) {
	print "<br>Aiken=(AKN," . $mp->primary . "), (AKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Alan" );
if ( $mp->primary != "ALN" || $mp->secondary != "ALN" ) {
	print "<br>Alan=(ALN," . $mp->primary . "), (ALN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Alcock" );
if ( $mp->primary != "ALKK" || $mp->secondary != "ALKK" ) {
	print "<br>Alcock=(ALKK," . $mp->primary . "), (ALKK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Alden" );
if ( $mp->primary != "ALTN" || $mp->secondary != "ALTN" ) {
	print "<br>Alden=(ALTN," . $mp->primary . "), (ALTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Aldham" );
if ( $mp->primary != "ALTM" || $mp->secondary != "ALTM" ) {
	print "<br>Aldham=(ALTM," . $mp->primary . "), (ALTM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Allen" );
if ( $mp->primary != "ALN" || $mp->secondary != "ALN" ) {
	print "<br>Allen=(ALN," . $mp->primary . "), (ALN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Allerton" );
if ( $mp->primary != "ALRT" || $mp->secondary != "ALRT" ) {
	print "<br>Allerton=(ALRT," . $mp->primary . "), (ALRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Alsop" );
if ( $mp->primary != "ALSP" || $mp->secondary != "ALSP" ) {
	print "<br>Alsop=(ALSP," . $mp->primary . "), (ALSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Alwein" );
if ( $mp->primary != "ALN" || $mp->secondary != "ALN" ) {
	print "<br>Alwein=(ALN," . $mp->primary . "), (ALN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ambler" );
if ( $mp->primary != "AMPL" || $mp->secondary != "AMPL" ) {
	print "<br>Ambler=(AMPL," . $mp->primary . "), (AMPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Andevill" );
if ( $mp->primary != "ANTF" || $mp->secondary != "ANTF" ) {
	print "<br>Andevill=(ANTF," . $mp->primary . "), (ANTF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Andrews" );
if ( $mp->primary != "ANTR" || $mp->secondary != "ANTR" ) {
	print "<br>Andrews=(ANTR," . $mp->primary . "), (ANTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Andreyco" );
if ( $mp->primary != "ANTR" || $mp->secondary != "ANTR" ) {
	print "<br>Andreyco=(ANTR," . $mp->primary . "), (ANTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Andriesse" );
if ( $mp->primary != "ANTR" || $mp->secondary != "ANTR" ) {
	print "<br>Andriesse=(ANTR," . $mp->primary . "), (ANTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Angier" );
if ( $mp->primary != "ANJ" || $mp->secondary != "ANJR" ) {
	print "<br>Angier=(ANJ," . $mp->primary . "), (ANJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Annabel" );
if ( $mp->primary != "ANPL" || $mp->secondary != "ANPL" ) {
	print "<br>Annabel=(ANPL," . $mp->primary . "), (ANPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Anne" );
if ( $mp->primary != "AN" || $mp->secondary != "AN" ) {
	print "<br>Anne=(AN," . $mp->primary . "), (AN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Anstye" );
if ( $mp->primary != "ANST" || $mp->secondary != "ANST" ) {
	print "<br>Anstye=(ANST," . $mp->primary . "), (ANST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Appling" );
if ( $mp->primary != "APLN" || $mp->secondary != "APLN" ) {
	print "<br>Appling=(APLN," . $mp->primary . "), (APLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Apuke" );
if ( $mp->primary != "APK" || $mp->secondary != "APK" ) {
	print "<br>Apuke=(APK," . $mp->primary . "), (APK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Arnold" );
if ( $mp->primary != "ARNL" || $mp->secondary != "ARNL" ) {
	print "<br>Arnold=(ARNL," . $mp->primary . "), (ARNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ashby" );
if ( $mp->primary != "AXP" || $mp->secondary != "AXP" ) {
	print "<br>Ashby=(AXP," . $mp->primary . "), (AXP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Astwood" );
if ( $mp->primary != "ASTT" || $mp->secondary != "ASTT" ) {
	print "<br>Astwood=(ASTT," . $mp->primary . "), (ASTT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Atkinson" );
if ( $mp->primary != "ATKN" || $mp->secondary != "ATKN" ) {
	print "<br>Atkinson=(ATKN," . $mp->primary . "), (ATKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Audley" );
if ( $mp->primary != "ATL" || $mp->secondary != "ATL" ) {
	print "<br>Audley=(ATL," . $mp->primary . "), (ATL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Austin" );
if ( $mp->primary != "ASTN" || $mp->secondary != "ASTN" ) {
	print "<br>Austin=(ASTN," . $mp->primary . "), (ASTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Avenal" );
if ( $mp->primary != "AFNL" || $mp->secondary != "AFNL" ) {
	print "<br>Avenal=(AFNL," . $mp->primary . "), (AFNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ayer" );
if ( $mp->primary != "AR" || $mp->secondary != "AR" ) {
	print "<br>Ayer=(AR," . $mp->primary . "), (AR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ayot" );
if ( $mp->primary != "AT" || $mp->secondary != "AT" ) {
	print "<br>Ayot=(AT," . $mp->primary . "), (AT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Babbitt" );
if ( $mp->primary != "PPT" || $mp->secondary != "PPT" ) {
	print "<br>Babbitt=(PPT," . $mp->primary . "), (PPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bachelor" );
if ( $mp->primary != "PXLR" || $mp->secondary != "PKLR" ) {
	print "<br>Bachelor=(PXLR," . $mp->primary . "), (PKLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bachelour" );
if ( $mp->primary != "PXLR" || $mp->secondary != "PKLR" ) {
	print "<br>Bachelour=(PXLR," . $mp->primary . "), (PKLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bailey" );
if ( $mp->primary != "PL" || $mp->secondary != "PL" ) {
	print "<br>Bailey=(PL," . $mp->primary . "), (PL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Baivel" );
if ( $mp->primary != "PFL" || $mp->secondary != "PFL" ) {
	print "<br>Baivel=(PFL," . $mp->primary . "), (PFL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Baker" );
if ( $mp->primary != "PKR" || $mp->secondary != "PKR" ) {
	print "<br>Baker=(PKR," . $mp->primary . "), (PKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Baldwin" );
if ( $mp->primary != "PLTN" || $mp->secondary != "PLTN" ) {
	print "<br>Baldwin=(PLTN," . $mp->primary . "), (PLTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Balsley" );
if ( $mp->primary != "PLSL" || $mp->secondary != "PLSL" ) {
	print "<br>Balsley=(PLSL," . $mp->primary . "), (PLSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Barber" );
if ( $mp->primary != "PRPR" || $mp->secondary != "PRPR" ) {
	print "<br>Barber=(PRPR," . $mp->primary . "), (PRPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Barker" );
if ( $mp->primary != "PRKR" || $mp->secondary != "PRKR" ) {
	print "<br>Barker=(PRKR," . $mp->primary . "), (PRKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Barlow" );
if ( $mp->primary != "PRL" || $mp->secondary != "PRLF" ) {
	print "<br>Barlow=(PRL," . $mp->primary . "), (PRLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Barnard" );
if ( $mp->primary != "PRNR" || $mp->secondary != "PRNR" ) {
	print "<br>Barnard=(PRNR," . $mp->primary . "), (PRNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Barnes" );
if ( $mp->primary != "PRNS" || $mp->secondary != "PRNS" ) {
	print "<br>Barnes=(PRNS," . $mp->primary . "), (PRNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Barnsley" );
if ( $mp->primary != "PRNS" || $mp->secondary != "PRNS" ) {
	print "<br>Barnsley=(PRNS," . $mp->primary . "), (PRNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Barouxis" );
if ( $mp->primary != "PRKS" || $mp->secondary != "PRKS" ) {
	print "<br>Barouxis=(PRKS," . $mp->primary . "), (PRKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bartlet" );
if ( $mp->primary != "PRTL" || $mp->secondary != "PRTL" ) {
	print "<br>Bartlet=(PRTL," . $mp->primary . "), (PRTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Basley" );
if ( $mp->primary != "PSL" || $mp->secondary != "PSL" ) {
	print "<br>Basley=(PSL," . $mp->primary . "), (PSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Basset" );
if ( $mp->primary != "PST" || $mp->secondary != "PST" ) {
	print "<br>Basset=(PST," . $mp->primary . "), (PST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bassett" );
if ( $mp->primary != "PST" || $mp->secondary != "PST" ) {
	print "<br>Bassett=(PST," . $mp->primary . "), (PST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Batchlor" );
if ( $mp->primary != "PXLR" || $mp->secondary != "PXLR" ) {
	print "<br>Batchlor=(PXLR," . $mp->primary . "), (PXLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bates" );
if ( $mp->primary != "PTS" || $mp->secondary != "PTS" ) {
	print "<br>Bates=(PTS," . $mp->primary . "), (PTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Batson" );
if ( $mp->primary != "PTSN" || $mp->secondary != "PTSN" ) {
	print "<br>Batson=(PTSN," . $mp->primary . "), (PTSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bayes" );
if ( $mp->primary != "PS" || $mp->secondary != "PS" ) {
	print "<br>Bayes=(PS," . $mp->primary . "), (PS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bayley" );
if ( $mp->primary != "PL" || $mp->secondary != "PL" ) {
	print "<br>Bayley=(PL," . $mp->primary . "), (PL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Beale" );
if ( $mp->primary != "PL" || $mp->secondary != "PL" ) {
	print "<br>Beale=(PL," . $mp->primary . "), (PL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Beauchamp" );
if ( $mp->primary != "PXMP" || $mp->secondary != "PKMP" ) {
	print "<br>Beauchamp=(PXMP," . $mp->primary . "), (PKMP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Beauclerc" );
if ( $mp->primary != "PKLR" || $mp->secondary != "PKLR" ) {
	print "<br>Beauclerc=(PKLR," . $mp->primary . "), (PKLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Beech" );
if ( $mp->primary != "PK" || $mp->secondary != "PK" ) {
	print "<br>Beech=(PK," . $mp->primary . "), (PK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Beers" );
if ( $mp->primary != "PRS" || $mp->secondary != "PRS" ) {
	print "<br>Beers=(PRS," . $mp->primary . "), (PRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Beke" );
if ( $mp->primary != "PK" || $mp->secondary != "PK" ) {
	print "<br>Beke=(PK," . $mp->primary . "), (PK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Belcher" );
if ( $mp->primary != "PLXR" || $mp->secondary != "PLKR" ) {
	print "<br>Belcher=(PLXR," . $mp->primary . "), (PLKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Benjamin" );
if ( $mp->primary != "PNJM" || $mp->secondary != "PNJM" ) {
	print "<br>Benjamin=(PNJM," . $mp->primary . "), (PNJM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Benningham" );
if ( $mp->primary != "PNNK" || $mp->secondary != "PNNK" ) {
	print "<br>Benningham=(PNNK," . $mp->primary . "), (PNNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bereford" );
if ( $mp->primary != "PRFR" || $mp->secondary != "PRFR" ) {
	print "<br>Bereford=(PRFR," . $mp->primary . "), (PRFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bergen" );
if ( $mp->primary != "PRJN" || $mp->secondary != "PRKN" ) {
	print "<br>Bergen=(PRJN," . $mp->primary . "), (PRKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Berkeley" );
if ( $mp->primary != "PRKL" || $mp->secondary != "PRKL" ) {
	print "<br>Berkeley=(PRKL," . $mp->primary . "), (PRKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Berry" );
if ( $mp->primary != "PR" || $mp->secondary != "PR" ) {
	print "<br>Berry=(PR," . $mp->primary . "), (PR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Besse" );
if ( $mp->primary != "PS" || $mp->secondary != "PS" ) {
	print "<br>Besse=(PS," . $mp->primary . "), (PS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bessey" );
if ( $mp->primary != "PS" || $mp->secondary != "PS" ) {
	print "<br>Bessey=(PS," . $mp->primary . "), (PS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bessiles" );
if ( $mp->primary != "PSLS" || $mp->secondary != "PSLS" ) {
	print "<br>Bessiles=(PSLS," . $mp->primary . "), (PSLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bigelow" );
if ( $mp->primary != "PJL" || $mp->secondary != "PKLF" ) {
	print "<br>Bigelow=(PJL," . $mp->primary . "), (PKLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bigg" );
if ( $mp->primary != "PK" || $mp->secondary != "PK" ) {
	print "<br>Bigg=(PK," . $mp->primary . "), (PK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bigod" );
if ( $mp->primary != "PKT" || $mp->secondary != "PKT" ) {
	print "<br>Bigod=(PKT," . $mp->primary . "), (PKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Billings" );
if ( $mp->primary != "PLNK" || $mp->secondary != "PLNK" ) {
	print "<br>Billings=(PLNK," . $mp->primary . "), (PLNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bimper" );
if ( $mp->primary != "PMPR" || $mp->secondary != "PMPR" ) {
	print "<br>Bimper=(PMPR," . $mp->primary . "), (PMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Binker" );
if ( $mp->primary != "PNKR" || $mp->secondary != "PNKR" ) {
	print "<br>Binker=(PNKR," . $mp->primary . "), (PNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Birdsill" );
if ( $mp->primary != "PRTS" || $mp->secondary != "PRTS" ) {
	print "<br>Birdsill=(PRTS," . $mp->primary . "), (PRTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bishop" );
if ( $mp->primary != "PXP" || $mp->secondary != "PXP" ) {
	print "<br>Bishop=(PXP," . $mp->primary . "), (PXP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Black" );
if ( $mp->primary != "PLK" || $mp->secondary != "PLK" ) {
	print "<br>Black=(PLK," . $mp->primary . "), (PLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blagge" );
if ( $mp->primary != "PLK" || $mp->secondary != "PLK" ) {
	print "<br>Blagge=(PLK," . $mp->primary . "), (PLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blake" );
if ( $mp->primary != "PLK" || $mp->secondary != "PLK" ) {
	print "<br>Blake=(PLK," . $mp->primary . "), (PLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blanck" );
if ( $mp->primary != "PLNK" || $mp->secondary != "PLNK" ) {
	print "<br>Blanck=(PLNK," . $mp->primary . "), (PLNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bledsoe" );
if ( $mp->primary != "PLTS" || $mp->secondary != "PLTS" ) {
	print "<br>Bledsoe=(PLTS," . $mp->primary . "), (PLTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blennerhasset" );
if ( $mp->primary != "PLNR" || $mp->secondary != "PLNR" ) {
	print "<br>Blennerhasset=(PLNR," . $mp->primary . "), (PLNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blessing" );
if ( $mp->primary != "PLSN" || $mp->secondary != "PLSN" ) {
	print "<br>Blessing=(PLSN," . $mp->primary . "), (PLSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blewett" );
if ( $mp->primary != "PLT" || $mp->secondary != "PLT" ) {
	print "<br>Blewett=(PLT," . $mp->primary . "), (PLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bloctgoed" );
if ( $mp->primary != "PLKT" || $mp->secondary != "PLKT" ) {
	print "<br>Bloctgoed=(PLKT," . $mp->primary . "), (PLKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bloetgoet" );
if ( $mp->primary != "PLTK" || $mp->secondary != "PLTK" ) {
	print "<br>Bloetgoet=(PLTK," . $mp->primary . "), (PLTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bloodgood" );
if ( $mp->primary != "PLTK" || $mp->secondary != "PLTK" ) {
	print "<br>Bloodgood=(PLTK," . $mp->primary . "), (PLTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blossom" );
if ( $mp->primary != "PLSM" || $mp->secondary != "PLSM" ) {
	print "<br>Blossom=(PLSM," . $mp->primary . "), (PLSM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Blount" );
if ( $mp->primary != "PLNT" || $mp->secondary != "PLNT" ) {
	print "<br>Blount=(PLNT," . $mp->primary . "), (PLNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bodine" );
if ( $mp->primary != "PTN" || $mp->secondary != "PTN" ) {
	print "<br>Bodine=(PTN," . $mp->primary . "), (PTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bodman" );
if ( $mp->primary != "PTMN" || $mp->secondary != "PTMN" ) {
	print "<br>Bodman=(PTMN," . $mp->primary . "), (PTMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "BonCoeur" );
if ( $mp->primary != "PNKR" || $mp->secondary != "PNKR" ) {
	print "<br>BonCoeur=(PNKR," . $mp->primary . "), (PNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bond" );
if ( $mp->primary != "PNT" || $mp->secondary != "PNT" ) {
	print "<br>Bond=(PNT," . $mp->primary . "), (PNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Boscawen" );
if ( $mp->primary != "PSKN" || $mp->secondary != "PSKN" ) {
	print "<br>Boscawen=(PSKN," . $mp->primary . "), (PSKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bosworth" );
if ( $mp->primary != "PSR0" || $mp->secondary != "PSRT" ) {
	print "<br>Bosworth=(PSR0," . $mp->primary . "), (PSRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bouchier" );
if ( $mp->primary != "PX" || $mp->secondary != "PKR" ) {
	print "<br>Bouchier=(PX," . $mp->primary . "), (PKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bowne" );
if ( $mp->primary != "PN" || $mp->secondary != "PN" ) {
	print "<br>Bowne=(PN," . $mp->primary . "), (PN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bradbury" );
if ( $mp->primary != "PRTP" || $mp->secondary != "PRTP" ) {
	print "<br>Bradbury=(PRTP," . $mp->primary . "), (PRTP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bradder" );
if ( $mp->primary != "PRTR" || $mp->secondary != "PRTR" ) {
	print "<br>Bradder=(PRTR," . $mp->primary . "), (PRTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bradford" );
if ( $mp->primary != "PRTF" || $mp->secondary != "PRTF" ) {
	print "<br>Bradford=(PRTF," . $mp->primary . "), (PRTF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bradstreet" );
if ( $mp->primary != "PRTS" || $mp->secondary != "PRTS" ) {
	print "<br>Bradstreet=(PRTS," . $mp->primary . "), (PRTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Braham" );
if ( $mp->primary != "PRHM" || $mp->secondary != "PRHM" ) {
	print "<br>Braham=(PRHM," . $mp->primary . "), (PRHM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brailsford" );
if ( $mp->primary != "PRLS" || $mp->secondary != "PRLS" ) {
	print "<br>Brailsford=(PRLS," . $mp->primary . "), (PRLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brainard" );
if ( $mp->primary != "PRNR" || $mp->secondary != "PRNR" ) {
	print "<br>Brainard=(PRNR," . $mp->primary . "), (PRNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brandish" );
if ( $mp->primary != "PRNT" || $mp->secondary != "PRNT" ) {
	print "<br>Brandish=(PRNT," . $mp->primary . "), (PRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Braun" );
if ( $mp->primary != "PRN" || $mp->secondary != "PRN" ) {
	print "<br>Braun=(PRN," . $mp->primary . "), (PRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brecc" );
if ( $mp->primary != "PRK" || $mp->secondary != "PRK" ) {
	print "<br>Brecc=(PRK," . $mp->primary . "), (PRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brent" );
if ( $mp->primary != "PRNT" || $mp->secondary != "PRNT" ) {
	print "<br>Brent=(PRNT," . $mp->primary . "), (PRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brenton" );
if ( $mp->primary != "PRNT" || $mp->secondary != "PRNT" ) {
	print "<br>Brenton=(PRNT," . $mp->primary . "), (PRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Briggs" );
if ( $mp->primary != "PRKS" || $mp->secondary != "PRKS" ) {
	print "<br>Briggs=(PRKS," . $mp->primary . "), (PRKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brigham" );
if ( $mp->primary != "PRM" || $mp->secondary != "PRM" ) {
	print "<br>Brigham=(PRM," . $mp->primary . "), (PRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brobst" );
if ( $mp->primary != "PRPS" || $mp->secondary != "PRPS" ) {
	print "<br>Brobst=(PRPS," . $mp->primary . "), (PRPS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brome" );
if ( $mp->primary != "PRM" || $mp->secondary != "PRM" ) {
	print "<br>Brome=(PRM," . $mp->primary . "), (PRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bronson" );
if ( $mp->primary != "PRNS" || $mp->secondary != "PRNS" ) {
	print "<br>Bronson=(PRNS," . $mp->primary . "), (PRNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brooks" );
if ( $mp->primary != "PRKS" || $mp->secondary != "PRKS" ) {
	print "<br>Brooks=(PRKS," . $mp->primary . "), (PRKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brouillard" );
if ( $mp->primary != "PRLR" || $mp->secondary != "PRLR" ) {
	print "<br>Brouillard=(PRLR," . $mp->primary . "), (PRLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brown" );
if ( $mp->primary != "PRN" || $mp->secondary != "PRN" ) {
	print "<br>Brown=(PRN," . $mp->primary . "), (PRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Browne" );
if ( $mp->primary != "PRN" || $mp->secondary != "PRN" ) {
	print "<br>Browne=(PRN," . $mp->primary . "), (PRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brownell" );
if ( $mp->primary != "PRNL" || $mp->secondary != "PRNL" ) {
	print "<br>Brownell=(PRNL," . $mp->primary . "), (PRNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bruley" );
if ( $mp->primary != "PRL" || $mp->secondary != "PRL" ) {
	print "<br>Bruley=(PRL," . $mp->primary . "), (PRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bryant" );
if ( $mp->primary != "PRNT" || $mp->secondary != "PRNT" ) {
	print "<br>Bryant=(PRNT," . $mp->primary . "), (PRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Brzozowski" );
if ( $mp->primary != "PRSS" || $mp->secondary != "PRTS" ) {
	print "<br>Brzozowski=(PRSS," . $mp->primary . "), (PRTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Buide" );
if ( $mp->primary != "PT" || $mp->secondary != "PT" ) {
	print "<br>Buide=(PT," . $mp->primary . "), (PT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bulmer" );
if ( $mp->primary != "PLMR" || $mp->secondary != "PLMR" ) {
	print "<br>Bulmer=(PLMR," . $mp->primary . "), (PLMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bunker" );
if ( $mp->primary != "PNKR" || $mp->secondary != "PNKR" ) {
	print "<br>Bunker=(PNKR," . $mp->primary . "), (PNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Burden" );
if ( $mp->primary != "PRTN" || $mp->secondary != "PRTN" ) {
	print "<br>Burden=(PRTN," . $mp->primary . "), (PRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Burge" );
if ( $mp->primary != "PRJ" || $mp->secondary != "PRK" ) {
	print "<br>Burge=(PRJ," . $mp->primary . "), (PRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Burgoyne" );
if ( $mp->primary != "PRKN" || $mp->secondary != "PRKN" ) {
	print "<br>Burgoyne=(PRKN," . $mp->primary . "), (PRKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Burke" );
if ( $mp->primary != "PRK" || $mp->secondary != "PRK" ) {
	print "<br>Burke=(PRK," . $mp->primary . "), (PRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Burnett" );
if ( $mp->primary != "PRNT" || $mp->secondary != "PRNT" ) {
	print "<br>Burnett=(PRNT," . $mp->primary . "), (PRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Burpee" );
if ( $mp->primary != "PRP" || $mp->secondary != "PRP" ) {
	print "<br>Burpee=(PRP," . $mp->primary . "), (PRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bursley" );
if ( $mp->primary != "PRSL" || $mp->secondary != "PRSL" ) {
	print "<br>Bursley=(PRSL," . $mp->primary . "), (PRSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Burton" );
if ( $mp->primary != "PRTN" || $mp->secondary != "PRTN" ) {
	print "<br>Burton=(PRTN," . $mp->primary . "), (PRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Bushnell" );
if ( $mp->primary != "PXNL" || $mp->secondary != "PXNL" ) {
	print "<br>Bushnell=(PXNL," . $mp->primary . "), (PXNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Buss" );
if ( $mp->primary != "PS" || $mp->secondary != "PS" ) {
	print "<br>Buss=(PS," . $mp->primary . "), (PS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Buswell" );
if ( $mp->primary != "PSL" || $mp->secondary != "PSL" ) {
	print "<br>Buswell=(PSL," . $mp->primary . "), (PSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Butler" );
if ( $mp->primary != "PTLR" || $mp->secondary != "PTLR" ) {
	print "<br>Butler=(PTLR," . $mp->primary . "), (PTLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Calkin" );
if ( $mp->primary != "KLKN" || $mp->secondary != "KLKN" ) {
	print "<br>Calkin=(KLKN," . $mp->primary . "), (KLKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Canada" );
if ( $mp->primary != "KNT" || $mp->secondary != "KNT" ) {
	print "<br>Canada=(KNT," . $mp->primary . "), (KNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Canmore" );
if ( $mp->primary != "KNMR" || $mp->secondary != "KNMR" ) {
	print "<br>Canmore=(KNMR," . $mp->primary . "), (KNMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Canney" );
if ( $mp->primary != "KN" || $mp->secondary != "KN" ) {
	print "<br>Canney=(KN," . $mp->primary . "), (KN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Capet" );
if ( $mp->primary != "KPT" || $mp->secondary != "KPT" ) {
	print "<br>Capet=(KPT," . $mp->primary . "), (KPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Card" );
if ( $mp->primary != "KRT" || $mp->secondary != "KRT" ) {
	print "<br>Card=(KRT," . $mp->primary . "), (KRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Carman" );
if ( $mp->primary != "KRMN" || $mp->secondary != "KRMN" ) {
	print "<br>Carman=(KRMN," . $mp->primary . "), (KRMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Carpenter" );
if ( $mp->primary != "KRPN" || $mp->secondary != "KRPN" ) {
	print "<br>Carpenter=(KRPN," . $mp->primary . "), (KRPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cartwright" );
if ( $mp->primary != "KRTR" || $mp->secondary != "KRTR" ) {
	print "<br>Cartwright=(KRTR," . $mp->primary . "), (KRTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Casey" );
if ( $mp->primary != "KS" || $mp->secondary != "KS" ) {
	print "<br>Casey=(KS," . $mp->primary . "), (KS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Catterfield" );
if ( $mp->primary != "KTRF" || $mp->secondary != "KTRF" ) {
	print "<br>Catterfield=(KTRF," . $mp->primary . "), (KTRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ceeley" );
if ( $mp->primary != "SL" || $mp->secondary != "SL" ) {
	print "<br>Ceeley=(SL," . $mp->primary . "), (SL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Chambers" );
if ( $mp->primary != "XMPR" || $mp->secondary != "XMPR" ) {
	print "<br>Chambers=(XMPR," . $mp->primary . "), (XMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Champion" );
if ( $mp->primary != "XMPN" || $mp->secondary != "XMPN" ) {
	print "<br>Champion=(XMPN," . $mp->primary . "), (XMPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Chapman" );
if ( $mp->primary != "XPMN" || $mp->secondary != "XPMN" ) {
	print "<br>Chapman=(XPMN," . $mp->primary . "), (XPMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Chase" );
if ( $mp->primary != "XS" || $mp->secondary != "XS" ) {
	print "<br>Chase=(XS," . $mp->primary . "), (XS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cheney" );
if ( $mp->primary != "XN" || $mp->secondary != "XN" ) {
	print "<br>Cheney=(XN," . $mp->primary . "), (XN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Chetwynd" );
if ( $mp->primary != "XTNT" || $mp->secondary != "XTNT" ) {
	print "<br>Chetwynd=(XTNT," . $mp->primary . "), (XTNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Chevalier" );
if ( $mp->primary != "XFL" || $mp->secondary != "XFLR" ) {
	print "<br>Chevalier=(XFL," . $mp->primary . "), (XFLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Chillingsworth" );
if ( $mp->primary != "XLNK" || $mp->secondary != "XLNK" ) {
	print "<br>Chillingsworth=(XLNK," . $mp->primary . "), (XLNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Christie" );
if ( $mp->primary != "KRST" || $mp->secondary != "KRST" ) {
	print "<br>Christie=(KRST," . $mp->primary . "), (KRST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Chubbuck" );
if ( $mp->primary != "XPK" || $mp->secondary != "XPK" ) {
	print "<br>Chubbuck=(XPK," . $mp->primary . "), (XPK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Church" );
if ( $mp->primary != "XRX" || $mp->secondary != "XRK" ) {
	print "<br>Church=(XRX," . $mp->primary . "), (XRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clark" );
if ( $mp->primary != "KLRK" || $mp->secondary != "KLRK" ) {
	print "<br>Clark=(KLRK," . $mp->primary . "), (KLRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clarke" );
if ( $mp->primary != "KLRK" || $mp->secondary != "KLRK" ) {
	print "<br>Clarke=(KLRK," . $mp->primary . "), (KLRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cleare" );
if ( $mp->primary != "KLR" || $mp->secondary != "KLR" ) {
	print "<br>Cleare=(KLR," . $mp->primary . "), (KLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clement" );
if ( $mp->primary != "KLMN" || $mp->secondary != "KLMN" ) {
	print "<br>Clement=(KLMN," . $mp->primary . "), (KLMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clerke" );
if ( $mp->primary != "KLRK" || $mp->secondary != "KLRK" ) {
	print "<br>Clerke=(KLRK," . $mp->primary . "), (KLRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clibben" );
if ( $mp->primary != "KLPN" || $mp->secondary != "KLPN" ) {
	print "<br>Clibben=(KLPN," . $mp->primary . "), (KLPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clifford" );
if ( $mp->primary != "KLFR" || $mp->secondary != "KLFR" ) {
	print "<br>Clifford=(KLFR," . $mp->primary . "), (KLFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clivedon" );
if ( $mp->primary != "KLFT" || $mp->secondary != "KLFT" ) {
	print "<br>Clivedon=(KLFT," . $mp->primary . "), (KLFT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Close" );
if ( $mp->primary != "KLS" || $mp->secondary != "KLS" ) {
	print "<br>Close=(KLS," . $mp->primary . "), (KLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Clothilde" );
if ( $mp->primary != "KL0L" || $mp->secondary != "KLTL" ) {
	print "<br>Clothilde=(KL0L," . $mp->primary . "), (KLTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cobb" );
if ( $mp->primary != "KP" || $mp->secondary != "KP" ) {
	print "<br>Cobb=(KP," . $mp->primary . "), (KP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Coburn" );
if ( $mp->primary != "KPRN" || $mp->secondary != "KPRN" ) {
	print "<br>Coburn=(KPRN," . $mp->primary . "), (KPRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Coburne" );
if ( $mp->primary != "KPRN" || $mp->secondary != "KPRN" ) {
	print "<br>Coburne=(KPRN," . $mp->primary . "), (KPRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cocke" );
if ( $mp->primary != "KK" || $mp->secondary != "KK" ) {
	print "<br>Cocke=(KK," . $mp->primary . "), (KK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Coffin" );
if ( $mp->primary != "KFN" || $mp->secondary != "KFN" ) {
	print "<br>Coffin=(KFN," . $mp->primary . "), (KFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Coffyn" );
if ( $mp->primary != "KFN" || $mp->secondary != "KFN" ) {
	print "<br>Coffyn=(KFN," . $mp->primary . "), (KFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Colborne" );
if ( $mp->primary != "KLPR" || $mp->secondary != "KLPR" ) {
	print "<br>Colborne=(KLPR," . $mp->primary . "), (KLPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Colby" );
if ( $mp->primary != "KLP" || $mp->secondary != "KLP" ) {
	print "<br>Colby=(KLP," . $mp->primary . "), (KLP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cole" );
if ( $mp->primary != "KL" || $mp->secondary != "KL" ) {
	print "<br>Cole=(KL," . $mp->primary . "), (KL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Coleman" );
if ( $mp->primary != "KLMN" || $mp->secondary != "KLMN" ) {
	print "<br>Coleman=(KLMN," . $mp->primary . "), (KLMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Collier" );
if ( $mp->primary != "KL" || $mp->secondary != "KLR" ) {
	print "<br>Collier=(KL," . $mp->primary . "), (KLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Compton" );
if ( $mp->primary != "KMPT" || $mp->secondary != "KMPT" ) {
	print "<br>Compton=(KMPT," . $mp->primary . "), (KMPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cone" );
if ( $mp->primary != "KN" || $mp->secondary != "KN" ) {
	print "<br>Cone=(KN," . $mp->primary . "), (KN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cook" );
if ( $mp->primary != "KK" || $mp->secondary != "KK" ) {
	print "<br>Cook=(KK," . $mp->primary . "), (KK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cooke" );
if ( $mp->primary != "KK" || $mp->secondary != "KK" ) {
	print "<br>Cooke=(KK," . $mp->primary . "), (KK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cooper" );
if ( $mp->primary != "KPR" || $mp->secondary != "KPR" ) {
	print "<br>Cooper=(KPR," . $mp->primary . "), (KPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Copperthwaite" );
if ( $mp->primary != "KPR0" || $mp->secondary != "KPRT" ) {
	print "<br>Copperthwaite=(KPR0," . $mp->primary . "), (KPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Corbet" );
if ( $mp->primary != "KRPT" || $mp->secondary != "KRPT" ) {
	print "<br>Corbet=(KRPT," . $mp->primary . "), (KRPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Corell" );
if ( $mp->primary != "KRL" || $mp->secondary != "KRL" ) {
	print "<br>Corell=(KRL," . $mp->primary . "), (KRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Corey" );
if ( $mp->primary != "KR" || $mp->secondary != "KR" ) {
	print "<br>Corey=(KR," . $mp->primary . "), (KR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Corlies" );
if ( $mp->primary != "KRLS" || $mp->secondary != "KRLS" ) {
	print "<br>Corlies=(KRLS," . $mp->primary . "), (KRLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Corneliszen" );
if ( $mp->primary != "KRNL" || $mp->secondary != "KRNL" ) {
	print "<br>Corneliszen=(KRNL," . $mp->primary . "), (KRNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cornelius" );
if ( $mp->primary != "KRNL" || $mp->secondary != "KRNL" ) {
	print "<br>Cornelius=(KRNL," . $mp->primary . "), (KRNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cornwallis" );
if ( $mp->primary != "KRNL" || $mp->secondary != "KRNL" ) {
	print "<br>Cornwallis=(KRNL," . $mp->primary . "), (KRNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cosgrove" );
if ( $mp->primary != "KSKR" || $mp->secondary != "KSKR" ) {
	print "<br>Cosgrove=(KSKR," . $mp->primary . "), (KSKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Count of Brionne" );
if ( $mp->primary != "KNTF" || $mp->secondary != "KNTF" ) {
	print "<br>Count of Brionne=(KNTF," . $mp->primary . "), (KNTF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Covill" );
if ( $mp->primary != "KFL" || $mp->secondary != "KFL" ) {
	print "<br>Covill=(KFL," . $mp->primary . "), (KFL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cowperthwaite" );
if ( $mp->primary != "KPR0" || $mp->secondary != "KPRT" ) {
	print "<br>Cowperthwaite=(KPR0," . $mp->primary . "), (KPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cowperwaite" );
if ( $mp->primary != "KPRT" || $mp->secondary != "KPRT" ) {
	print "<br>Cowperwaite=(KPRT," . $mp->primary . "), (KPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Crane" );
if ( $mp->primary != "KRN" || $mp->secondary != "KRN" ) {
	print "<br>Crane=(KRN," . $mp->primary . "), (KRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Creagmile" );
if ( $mp->primary != "KRKM" || $mp->secondary != "KRKM" ) {
	print "<br>Creagmile=(KRKM," . $mp->primary . "), (KRKM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Crew" );
if ( $mp->primary != "KR" || $mp->secondary != "KRF" ) {
	print "<br>Crew=(KR," . $mp->primary . "), (KRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Crispin" );
if ( $mp->primary != "KRSP" || $mp->secondary != "KRSP" ) {
	print "<br>Crispin=(KRSP," . $mp->primary . "), (KRSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Crocker" );
if ( $mp->primary != "KRKR" || $mp->secondary != "KRKR" ) {
	print "<br>Crocker=(KRKR," . $mp->primary . "), (KRKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Crockett" );
if ( $mp->primary != "KRKT" || $mp->secondary != "KRKT" ) {
	print "<br>Crockett=(KRKT," . $mp->primary . "), (KRKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Crosby" );
if ( $mp->primary != "KRSP" || $mp->secondary != "KRSP" ) {
	print "<br>Crosby=(KRSP," . $mp->primary . "), (KRSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Crump" );
if ( $mp->primary != "KRMP" || $mp->secondary != "KRMP" ) {
	print "<br>Crump=(KRMP," . $mp->primary . "), (KRMP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cunningham" );
if ( $mp->primary != "KNNK" || $mp->secondary != "KNNK" ) {
	print "<br>Cunningham=(KNNK," . $mp->primary . "), (KNNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Curtis" );
if ( $mp->primary != "KRTS" || $mp->secondary != "KRTS" ) {
	print "<br>Curtis=(KRTS," . $mp->primary . "), (KRTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cutha" );
if ( $mp->primary != "K0" || $mp->secondary != "KT" ) {
	print "<br>Cutha=(K0," . $mp->primary . "), (KT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Cutter" );
if ( $mp->primary != "KTR" || $mp->secondary != "KTR" ) {
	print "<br>Cutter=(KTR," . $mp->primary . "), (KTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "D'Aubigny" );
if ( $mp->primary != "TPN" || $mp->secondary != "TPKN" ) {
	print "<br>D'Aubigny=(TPN," . $mp->primary . "), (TPKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "DAVIS" );
if ( $mp->primary != "TFS" || $mp->secondary != "TFS" ) {
	print "<br>DAVIS=(TFS," . $mp->primary . "), (TFS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dabinott" );
if ( $mp->primary != "TPNT" || $mp->secondary != "TPNT" ) {
	print "<br>Dabinott=(TPNT," . $mp->primary . "), (TPNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dacre" );
if ( $mp->primary != "TKR" || $mp->secondary != "TKR" ) {
	print "<br>Dacre=(TKR," . $mp->primary . "), (TKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Daggett" );
if ( $mp->primary != "TKT" || $mp->secondary != "TKT" ) {
	print "<br>Daggett=(TKT," . $mp->primary . "), (TKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Danvers" );
if ( $mp->primary != "TNFR" || $mp->secondary != "TNFR" ) {
	print "<br>Danvers=(TNFR," . $mp->primary . "), (TNFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Darcy" );
if ( $mp->primary != "TRS" || $mp->secondary != "TRS" ) {
	print "<br>Darcy=(TRS," . $mp->primary . "), (TRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Davis" );
if ( $mp->primary != "TFS" || $mp->secondary != "TFS" ) {
	print "<br>Davis=(TFS," . $mp->primary . "), (TFS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dawn" );
if ( $mp->primary != "TN" || $mp->secondary != "TN" ) {
	print "<br>Dawn=(TN," . $mp->primary . "), (TN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dawson" );
if ( $mp->primary != "TSN" || $mp->secondary != "TSN" ) {
	print "<br>Dawson=(TSN," . $mp->primary . "), (TSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Day" );
if ( $mp->primary != "T" || $mp->secondary != "T" ) {
	print "<br>Day=(T," . $mp->primary . "), (T," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Daye" );
if ( $mp->primary != "T" || $mp->secondary != "T" ) {
	print "<br>Daye=(T," . $mp->primary . "), (T," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "DeGrenier" );
if ( $mp->primary != "TKRN" || $mp->secondary != "TKRN" ) {
	print "<br>DeGrenier=(TKRN," . $mp->primary . "), (TKRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dean" );
if ( $mp->primary != "TN" || $mp->secondary != "TN" ) {
	print "<br>Dean=(TN," . $mp->primary . "), (TN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Deekindaugh" );
if ( $mp->primary != "TKNT" || $mp->secondary != "TKNT" ) {
	print "<br>Deekindaugh=(TKNT," . $mp->primary . "), (TKNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dennis" );
if ( $mp->primary != "TNS" || $mp->secondary != "TNS" ) {
	print "<br>Dennis=(TNS," . $mp->primary . "), (TNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Denny" );
if ( $mp->primary != "TN" || $mp->secondary != "TN" ) {
	print "<br>Denny=(TN," . $mp->primary . "), (TN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Denton" );
if ( $mp->primary != "TNTN" || $mp->secondary != "TNTN" ) {
	print "<br>Denton=(TNTN," . $mp->primary . "), (TNTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Desborough" );
if ( $mp->primary != "TSPR" || $mp->secondary != "TSPR" ) {
	print "<br>Desborough=(TSPR," . $mp->primary . "), (TSPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Despenser" );
if ( $mp->primary != "TSPN" || $mp->secondary != "TSPN" ) {
	print "<br>Despenser=(TSPN," . $mp->primary . "), (TSPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Deverill" );
if ( $mp->primary != "TFRL" || $mp->secondary != "TFRL" ) {
	print "<br>Deverill=(TFRL," . $mp->primary . "), (TFRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Devine" );
if ( $mp->primary != "TFN" || $mp->secondary != "TFN" ) {
	print "<br>Devine=(TFN," . $mp->primary . "), (TFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dexter" );
if ( $mp->primary != "TKST" || $mp->secondary != "TKST" ) {
	print "<br>Dexter=(TKST," . $mp->primary . "), (TKST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dillaway" );
if ( $mp->primary != "TL" || $mp->secondary != "TL" ) {
	print "<br>Dillaway=(TL," . $mp->primary . "), (TL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dimmick" );
if ( $mp->primary != "TMK" || $mp->secondary != "TMK" ) {
	print "<br>Dimmick=(TMK," . $mp->primary . "), (TMK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dinan" );
if ( $mp->primary != "TNN" || $mp->secondary != "TNN" ) {
	print "<br>Dinan=(TNN," . $mp->primary . "), (TNN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dix" );
if ( $mp->primary != "TKS" || $mp->secondary != "TKS" ) {
	print "<br>Dix=(TKS," . $mp->primary . "), (TKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Doggett" );
if ( $mp->primary != "TKT" || $mp->secondary != "TKT" ) {
	print "<br>Doggett=(TKT," . $mp->primary . "), (TKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Donahue" );
if ( $mp->primary != "TNH" || $mp->secondary != "TNH" ) {
	print "<br>Donahue=(TNH," . $mp->primary . "), (TNH," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dorfman" );
if ( $mp->primary != "TRFM" || $mp->secondary != "TRFM" ) {
	print "<br>Dorfman=(TRFM," . $mp->primary . "), (TRFM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dorris" );
if ( $mp->primary != "TRS" || $mp->secondary != "TRS" ) {
	print "<br>Dorris=(TRS," . $mp->primary . "), (TRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dow" );
if ( $mp->primary != "T" || $mp->secondary != "TF" ) {
	print "<br>Dow=(T," . $mp->primary . "), (TF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Downey" );
if ( $mp->primary != "TN" || $mp->secondary != "TN" ) {
	print "<br>Downey=(TN," . $mp->primary . "), (TN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Downing" );
if ( $mp->primary != "TNNK" || $mp->secondary != "TNNK" ) {
	print "<br>Downing=(TNNK," . $mp->primary . "), (TNNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dowsett" );
if ( $mp->primary != "TST" || $mp->secondary != "TST" ) {
	print "<br>Dowsett=(TST," . $mp->primary . "), (TST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Duck?" );
if ( $mp->primary != "TK" || $mp->secondary != "TK" ) {
	print "<br>Duck?=(TK," . $mp->primary . "), (TK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dudley" );
if ( $mp->primary != "TTL" || $mp->secondary != "TTL" ) {
	print "<br>Dudley=(TTL," . $mp->primary . "), (TTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Duffy" );
if ( $mp->primary != "TF" || $mp->secondary != "TF" ) {
	print "<br>Duffy=(TF," . $mp->primary . "), (TF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dunn" );
if ( $mp->primary != "TN" || $mp->secondary != "TN" ) {
	print "<br>Dunn=(TN," . $mp->primary . "), (TN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dunsterville" );
if ( $mp->primary != "TNST" || $mp->secondary != "TNST" ) {
	print "<br>Dunsterville=(TNST," . $mp->primary . "), (TNST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Durrant" );
if ( $mp->primary != "TRNT" || $mp->secondary != "TRNT" ) {
	print "<br>Durrant=(TRNT," . $mp->primary . "), (TRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Durrin" );
if ( $mp->primary != "TRN" || $mp->secondary != "TRN" ) {
	print "<br>Durrin=(TRN," . $mp->primary . "), (TRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Dustin" );
if ( $mp->primary != "TSTN" || $mp->secondary != "TSTN" ) {
	print "<br>Dustin=(TSTN," . $mp->primary . "), (TSTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Duston" );
if ( $mp->primary != "TSTN" || $mp->secondary != "TSTN" ) {
	print "<br>Duston=(TSTN," . $mp->primary . "), (TSTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Eames" );
if ( $mp->primary != "AMS" || $mp->secondary != "AMS" ) {
	print "<br>Eames=(AMS," . $mp->primary . "), (AMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Early" );
if ( $mp->primary != "ARL" || $mp->secondary != "ARL" ) {
	print "<br>Early=(ARL," . $mp->primary . "), (ARL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Easty" );
if ( $mp->primary != "AST" || $mp->secondary != "AST" ) {
	print "<br>Easty=(AST," . $mp->primary . "), (AST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ebbett" );
if ( $mp->primary != "APT" || $mp->secondary != "APT" ) {
	print "<br>Ebbett=(APT," . $mp->primary . "), (APT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Eberbach" );
if ( $mp->primary != "APRP" || $mp->secondary != "APRP" ) {
	print "<br>Eberbach=(APRP," . $mp->primary . "), (APRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Eberhard" );
if ( $mp->primary != "APRR" || $mp->secondary != "APRR" ) {
	print "<br>Eberhard=(APRR," . $mp->primary . "), (APRR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Eddy" );
if ( $mp->primary != "AT" || $mp->secondary != "AT" ) {
	print "<br>Eddy=(AT," . $mp->primary . "), (AT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Edenden" );
if ( $mp->primary != "ATNT" || $mp->secondary != "ATNT" ) {
	print "<br>Edenden=(ATNT," . $mp->primary . "), (ATNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Edwards" );
if ( $mp->primary != "ATRT" || $mp->secondary != "ATRT" ) {
	print "<br>Edwards=(ATRT," . $mp->primary . "), (ATRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Eglinton" );
if ( $mp->primary != "AKLN" || $mp->secondary != "ALNT" ) {
	print "<br>Eglinton=(AKLN," . $mp->primary . "), (ALNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Eliot" );
if ( $mp->primary != "ALT" || $mp->secondary != "ALT" ) {
	print "<br>Eliot=(ALT," . $mp->primary . "), (ALT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Elizabeth" );
if ( $mp->primary != "ALSP" || $mp->secondary != "ALSP" ) {
	print "<br>Elizabeth=(ALSP," . $mp->primary . "), (ALSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ellis" );
if ( $mp->primary != "ALS" || $mp->secondary != "ALS" ) {
	print "<br>Ellis=(ALS," . $mp->primary . "), (ALS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ellison" );
if ( $mp->primary != "ALSN" || $mp->secondary != "ALSN" ) {
	print "<br>Ellison=(ALSN," . $mp->primary . "), (ALSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ellot" );
if ( $mp->primary != "ALT" || $mp->secondary != "ALT" ) {
	print "<br>Ellot=(ALT," . $mp->primary . "), (ALT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Elny" );
if ( $mp->primary != "ALN" || $mp->secondary != "ALN" ) {
	print "<br>Elny=(ALN," . $mp->primary . "), (ALN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Elsner" );
if ( $mp->primary != "ALSN" || $mp->secondary != "ALSN" ) {
	print "<br>Elsner=(ALSN," . $mp->primary . "), (ALSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Emerson" );
if ( $mp->primary != "AMRS" || $mp->secondary != "AMRS" ) {
	print "<br>Emerson=(AMRS," . $mp->primary . "), (AMRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Empson" );
if ( $mp->primary != "AMPS" || $mp->secondary != "AMPS" ) {
	print "<br>Empson=(AMPS," . $mp->primary . "), (AMPS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Est" );
if ( $mp->primary != "AST" || $mp->secondary != "AST" ) {
	print "<br>Est=(AST," . $mp->primary . "), (AST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Estabrook" );
if ( $mp->primary != "ASTP" || $mp->secondary != "ASTP" ) {
	print "<br>Estabrook=(ASTP," . $mp->primary . "), (ASTP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Estes" );
if ( $mp->primary != "ASTS" || $mp->secondary != "ASTS" ) {
	print "<br>Estes=(ASTS," . $mp->primary . "), (ASTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Estey" );
if ( $mp->primary != "AST" || $mp->secondary != "AST" ) {
	print "<br>Estey=(AST," . $mp->primary . "), (AST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Evans" );
if ( $mp->primary != "AFNS" || $mp->secondary != "AFNS" ) {
	print "<br>Evans=(AFNS," . $mp->primary . "), (AFNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fallowell" );
if ( $mp->primary != "FLL" || $mp->secondary != "FLL" ) {
	print "<br>Fallowell=(FLL," . $mp->primary . "), (FLL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Farnsworth" );
if ( $mp->primary != "FRNS" || $mp->secondary != "FRNS" ) {
	print "<br>Farnsworth=(FRNS," . $mp->primary . "), (FRNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Feake" );
if ( $mp->primary != "FK" || $mp->secondary != "FK" ) {
	print "<br>Feake=(FK," . $mp->primary . "), (FK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Feke" );
if ( $mp->primary != "FK" || $mp->secondary != "FK" ) {
	print "<br>Feke=(FK," . $mp->primary . "), (FK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fellows" );
if ( $mp->primary != "FLS" || $mp->secondary != "FLS" ) {
	print "<br>Fellows=(FLS," . $mp->primary . "), (FLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fettiplace" );
if ( $mp->primary != "FTPL" || $mp->secondary != "FTPL" ) {
	print "<br>Fettiplace=(FTPL," . $mp->primary . "), (FTPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Finney" );
if ( $mp->primary != "FN" || $mp->secondary != "FN" ) {
	print "<br>Finney=(FN," . $mp->primary . "), (FN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fischer" );
if ( $mp->primary != "FXR" || $mp->secondary != "FSKR" ) {
	print "<br>Fischer=(FXR," . $mp->primary . "), (FSKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fisher" );
if ( $mp->primary != "FXR" || $mp->secondary != "FXR" ) {
	print "<br>Fisher=(FXR," . $mp->primary . "), (FXR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fisk" );
if ( $mp->primary != "FSK" || $mp->secondary != "FSK" ) {
	print "<br>Fisk=(FSK," . $mp->primary . "), (FSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fiske" );
if ( $mp->primary != "FSK" || $mp->secondary != "FSK" ) {
	print "<br>Fiske=(FSK," . $mp->primary . "), (FSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fletcher" );
if ( $mp->primary != "FLXR" || $mp->secondary != "FLXR" ) {
	print "<br>Fletcher=(FLXR," . $mp->primary . "), (FLXR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Folger" );
if ( $mp->primary != "FLKR" || $mp->secondary != "FLJR" ) {
	print "<br>Folger=(FLKR," . $mp->primary . "), (FLJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Foliot" );
if ( $mp->primary != "FLT" || $mp->secondary != "FLT" ) {
	print "<br>Foliot=(FLT," . $mp->primary . "), (FLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Folyot" );
if ( $mp->primary != "FLT" || $mp->secondary != "FLT" ) {
	print "<br>Folyot=(FLT," . $mp->primary . "), (FLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fones" );
if ( $mp->primary != "FNS" || $mp->secondary != "FNS" ) {
	print "<br>Fones=(FNS," . $mp->primary . "), (FNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fordham" );
if ( $mp->primary != "FRTM" || $mp->secondary != "FRTM" ) {
	print "<br>Fordham=(FRTM," . $mp->primary . "), (FRTM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Forstner" );
if ( $mp->primary != "FRST" || $mp->secondary != "FRST" ) {
	print "<br>Forstner=(FRST," . $mp->primary . "), (FRST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fosten" );
if ( $mp->primary != "FSTN" || $mp->secondary != "FSTN" ) {
	print "<br>Fosten=(FSTN," . $mp->primary . "), (FSTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Foster" );
if ( $mp->primary != "FSTR" || $mp->secondary != "FSTR" ) {
	print "<br>Foster=(FSTR," . $mp->primary . "), (FSTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Foulke" );
if ( $mp->primary != "FLK" || $mp->secondary != "FLK" ) {
	print "<br>Foulke=(FLK," . $mp->primary . "), (FLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fowler" );
if ( $mp->primary != "FLR" || $mp->secondary != "FLR" ) {
	print "<br>Fowler=(FLR," . $mp->primary . "), (FLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Foxwell" );
if ( $mp->primary != "FKSL" || $mp->secondary != "FKSL" ) {
	print "<br>Foxwell=(FKSL," . $mp->primary . "), (FKSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fraley" );
if ( $mp->primary != "FRL" || $mp->secondary != "FRL" ) {
	print "<br>Fraley=(FRL," . $mp->primary . "), (FRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Franceys" );
if ( $mp->primary != "FRNS" || $mp->secondary != "FRNS" ) {
	print "<br>Franceys=(FRNS," . $mp->primary . "), (FRNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Franke" );
if ( $mp->primary != "FRNK" || $mp->secondary != "FRNK" ) {
	print "<br>Franke=(FRNK," . $mp->primary . "), (FRNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frascella" );
if ( $mp->primary != "FRSL" || $mp->secondary != "FRSL" ) {
	print "<br>Frascella=(FRSL," . $mp->primary . "), (FRSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frazer" );
if ( $mp->primary != "FRSR" || $mp->secondary != "FRSR" ) {
	print "<br>Frazer=(FRSR," . $mp->primary . "), (FRSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fredd" );
if ( $mp->primary != "FRT" || $mp->secondary != "FRT" ) {
	print "<br>Fredd=(FRT," . $mp->primary . "), (FRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Freeman" );
if ( $mp->primary != "FRMN" || $mp->secondary != "FRMN" ) {
	print "<br>Freeman=(FRMN," . $mp->primary . "), (FRMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "French" );
if ( $mp->primary != "FRNX" || $mp->secondary != "FRNK" ) {
	print "<br>French=(FRNX," . $mp->primary . "), (FRNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Freville" );
if ( $mp->primary != "FRFL" || $mp->secondary != "FRFL" ) {
	print "<br>Freville=(FRFL," . $mp->primary . "), (FRFL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frey" );
if ( $mp->primary != "FR" || $mp->secondary != "FR" ) {
	print "<br>Frey=(FR," . $mp->primary . "), (FR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frick" );
if ( $mp->primary != "FRK" || $mp->secondary != "FRK" ) {
	print "<br>Frick=(FRK," . $mp->primary . "), (FRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frier" );
if ( $mp->primary != "FR" || $mp->secondary != "FRR" ) {
	print "<br>Frier=(FR," . $mp->primary . "), (FRR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Froe" );
if ( $mp->primary != "FR" || $mp->secondary != "FR" ) {
	print "<br>Froe=(FR," . $mp->primary . "), (FR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frorer" );
if ( $mp->primary != "FRRR" || $mp->secondary != "FRRR" ) {
	print "<br>Frorer=(FRRR," . $mp->primary . "), (FRRR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frost" );
if ( $mp->primary != "FRST" || $mp->secondary != "FRST" ) {
	print "<br>Frost=(FRST," . $mp->primary . "), (FRST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Frothingham" );
if ( $mp->primary != "FR0N" || $mp->secondary != "FRTN" ) {
	print "<br>Frothingham=(FR0N," . $mp->primary . "), (FRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Fry" );
if ( $mp->primary != "FR" || $mp->secondary != "FR" ) {
	print "<br>Fry=(FR," . $mp->primary . "), (FR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gaffney" );
if ( $mp->primary != "KFN" || $mp->secondary != "KFN" ) {
	print "<br>Gaffney=(KFN," . $mp->primary . "), (KFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gage" );
if ( $mp->primary != "KJ" || $mp->secondary != "KK" ) {
	print "<br>Gage=(KJ," . $mp->primary . "), (KK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gallion" );
if ( $mp->primary != "KLN" || $mp->secondary != "KLN" ) {
	print "<br>Gallion=(KLN," . $mp->primary . "), (KLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gallishan" );
if ( $mp->primary != "KLXN" || $mp->secondary != "KLXN" ) {
	print "<br>Gallishan=(KLXN," . $mp->primary . "), (KLXN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gamble" );
if ( $mp->primary != "KMPL" || $mp->secondary != "KMPL" ) {
	print "<br>Gamble=(KMPL," . $mp->primary . "), (KMPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Garbrand" );
if ( $mp->primary != "KRPR" || $mp->secondary != "KRPR" ) {
	print "<br>Garbrand=(KRPR," . $mp->primary . "), (KRPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gardner" );
if ( $mp->primary != "KRTN" || $mp->secondary != "KRTN" ) {
	print "<br>Gardner=(KRTN," . $mp->primary . "), (KRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Garrett" );
if ( $mp->primary != "KRT" || $mp->secondary != "KRT" ) {
	print "<br>Garrett=(KRT," . $mp->primary . "), (KRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gassner" );
if ( $mp->primary != "KSNR" || $mp->secondary != "KSNR" ) {
	print "<br>Gassner=(KSNR," . $mp->primary . "), (KSNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gater" );
if ( $mp->primary != "KTR" || $mp->secondary != "KTR" ) {
	print "<br>Gater=(KTR," . $mp->primary . "), (KTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gaunt" );
if ( $mp->primary != "KNT" || $mp->secondary != "KNT" ) {
	print "<br>Gaunt=(KNT," . $mp->primary . "), (KNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gayer" );
if ( $mp->primary != "KR" || $mp->secondary != "KR" ) {
	print "<br>Gayer=(KR," . $mp->primary . "), (KR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gerken" );
if ( $mp->primary != "KRKN" || $mp->secondary != "JRKN" ) {
	print "<br>Gerken=(KRKN," . $mp->primary . "), (JRKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gerritsen" );
if ( $mp->primary != "KRTS" || $mp->secondary != "JRTS" ) {
	print "<br>Gerritsen=(KRTS," . $mp->primary . "), (JRTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gibbs" );
if ( $mp->primary != "KPS" || $mp->secondary != "JPS" ) {
	print "<br>Gibbs=(KPS," . $mp->primary . "), (JPS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Giffard" );
if ( $mp->primary != "JFRT" || $mp->secondary != "KFRT" ) {
	print "<br>Giffard=(JFRT," . $mp->primary . "), (KFRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gilbert" );
if ( $mp->primary != "KLPR" || $mp->secondary != "JLPR" ) {
	print "<br>Gilbert=(KLPR," . $mp->primary . "), (JLPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gill" );
if ( $mp->primary != "KL" || $mp->secondary != "JL" ) {
	print "<br>Gill=(KL," . $mp->primary . "), (JL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gilman" );
if ( $mp->primary != "KLMN" || $mp->secondary != "JLMN" ) {
	print "<br>Gilman=(KLMN," . $mp->primary . "), (JLMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Glass" );
if ( $mp->primary != "KLS" || $mp->secondary != "KLS" ) {
	print "<br>Glass=(KLS," . $mp->primary . "), (KLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Goddard\Gifford" );
if ( $mp->primary != "KTRT" || $mp->secondary != "KTRT" ) {
	print "<br>Goddard\Gifford=(KTRT," . $mp->primary . "), (KTRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Godfrey" );
if ( $mp->primary != "KTFR" || $mp->secondary != "KTFR" ) {
	print "<br>Godfrey=(KTFR," . $mp->primary . "), (KTFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Godwin" );
if ( $mp->primary != "KTN" || $mp->secondary != "KTN" ) {
	print "<br>Godwin=(KTN," . $mp->primary . "), (KTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Goodale" );
if ( $mp->primary != "KTL" || $mp->secondary != "KTL" ) {
	print "<br>Goodale=(KTL," . $mp->primary . "), (KTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Goodnow" );
if ( $mp->primary != "KTN" || $mp->secondary != "KTNF" ) {
	print "<br>Goodnow=(KTN," . $mp->primary . "), (KTNF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gorham" );
if ( $mp->primary != "KRM" || $mp->secondary != "KRM" ) {
	print "<br>Gorham=(KRM," . $mp->primary . "), (KRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Goseline" );
if ( $mp->primary != "KSLN" || $mp->secondary != "KSLN" ) {
	print "<br>Goseline=(KSLN," . $mp->primary . "), (KSLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gott" );
if ( $mp->primary != "KT" || $mp->secondary != "KT" ) {
	print "<br>Gott=(KT," . $mp->primary . "), (KT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gould" );
if ( $mp->primary != "KLT" || $mp->secondary != "KLT" ) {
	print "<br>Gould=(KLT," . $mp->primary . "), (KLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Grafton" );
if ( $mp->primary != "KRFT" || $mp->secondary != "KRFT" ) {
	print "<br>Grafton=(KRFT," . $mp->primary . "), (KRFT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Grant" );
if ( $mp->primary != "KRNT" || $mp->secondary != "KRNT" ) {
	print "<br>Grant=(KRNT," . $mp->primary . "), (KRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gray" );
if ( $mp->primary != "KR" || $mp->secondary != "KR" ) {
	print "<br>Gray=(KR," . $mp->primary . "), (KR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Green" );
if ( $mp->primary != "KRN" || $mp->secondary != "KRN" ) {
	print "<br>Green=(KRN," . $mp->primary . "), (KRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Griffin" );
if ( $mp->primary != "KRFN" || $mp->secondary != "KRFN" ) {
	print "<br>Griffin=(KRFN," . $mp->primary . "), (KRFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Grill" );
if ( $mp->primary != "KRL" || $mp->secondary != "KRL" ) {
	print "<br>Grill=(KRL," . $mp->primary . "), (KRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Grim" );
if ( $mp->primary != "KRM" || $mp->secondary != "KRM" ) {
	print "<br>Grim=(KRM," . $mp->primary . "), (KRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Grisgonelle" );
if ( $mp->primary != "KRSK" || $mp->secondary != "KRSK" ) {
	print "<br>Grisgonelle=(KRSK," . $mp->primary . "), (KRSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gross" );
if ( $mp->primary != "KRS" || $mp->secondary != "KRS" ) {
	print "<br>Gross=(KRS," . $mp->primary . "), (KRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Guba" );
if ( $mp->primary != "KP" || $mp->secondary != "KP" ) {
	print "<br>Guba=(KP," . $mp->primary . "), (KP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Gybbes" );
if ( $mp->primary != "KPS" || $mp->secondary != "JPS" ) {
	print "<br>Gybbes=(KPS," . $mp->primary . "), (JPS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Haburne" );
if ( $mp->primary != "HPRN" || $mp->secondary != "HPRN" ) {
	print "<br>Haburne=(HPRN," . $mp->primary . "), (HPRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hackburne" );
if ( $mp->primary != "HKPR" || $mp->secondary != "HKPR" ) {
	print "<br>Hackburne=(HKPR," . $mp->primary . "), (HKPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Haddon?" );
if ( $mp->primary != "HTN" || $mp->secondary != "HTN" ) {
	print "<br>Haddon?=(HTN," . $mp->primary . "), (HTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Haines" );
if ( $mp->primary != "HNS" || $mp->secondary != "HNS" ) {
	print "<br>Haines=(HNS," . $mp->primary . "), (HNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hale" );
if ( $mp->primary != "HL" || $mp->secondary != "HL" ) {
	print "<br>Hale=(HL," . $mp->primary . "), (HL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hall" );
if ( $mp->primary != "HL" || $mp->secondary != "HL" ) {
	print "<br>Hall=(HL," . $mp->primary . "), (HL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hallet" );
if ( $mp->primary != "HLT" || $mp->secondary != "HLT" ) {
	print "<br>Hallet=(HLT," . $mp->primary . "), (HLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hallock" );
if ( $mp->primary != "HLK" || $mp->secondary != "HLK" ) {
	print "<br>Hallock=(HLK," . $mp->primary . "), (HLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Halstead" );
if ( $mp->primary != "HLST" || $mp->secondary != "HLST" ) {
	print "<br>Halstead=(HLST," . $mp->primary . "), (HLST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hammond" );
if ( $mp->primary != "HMNT" || $mp->secondary != "HMNT" ) {
	print "<br>Hammond=(HMNT," . $mp->primary . "), (HMNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hance" );
if ( $mp->primary != "HNS" || $mp->secondary != "HNS" ) {
	print "<br>Hance=(HNS," . $mp->primary . "), (HNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Handy" );
if ( $mp->primary != "HNT" || $mp->secondary != "HNT" ) {
	print "<br>Handy=(HNT," . $mp->primary . "), (HNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hanson" );
if ( $mp->primary != "HNSN" || $mp->secondary != "HNSN" ) {
	print "<br>Hanson=(HNSN," . $mp->primary . "), (HNSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Harasek" );
if ( $mp->primary != "HRSK" || $mp->secondary != "HRSK" ) {
	print "<br>Harasek=(HRSK," . $mp->primary . "), (HRSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Harcourt" );
if ( $mp->primary != "HRKR" || $mp->secondary != "HRKR" ) {
	print "<br>Harcourt=(HRKR," . $mp->primary . "), (HRKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hardy" );
if ( $mp->primary != "HRT" || $mp->secondary != "HRT" ) {
	print "<br>Hardy=(HRT," . $mp->primary . "), (HRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Harlock" );
if ( $mp->primary != "HRLK" || $mp->secondary != "HRLK" ) {
	print "<br>Harlock=(HRLK," . $mp->primary . "), (HRLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Harris" );
if ( $mp->primary != "HRS" || $mp->secondary != "HRS" ) {
	print "<br>Harris=(HRS," . $mp->primary . "), (HRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hartley" );
if ( $mp->primary != "HRTL" || $mp->secondary != "HRTL" ) {
	print "<br>Hartley=(HRTL," . $mp->primary . "), (HRTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Harvey" );
if ( $mp->primary != "HRF" || $mp->secondary != "HRF" ) {
	print "<br>Harvey=(HRF," . $mp->primary . "), (HRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Harvie" );
if ( $mp->primary != "HRF" || $mp->secondary != "HRF" ) {
	print "<br>Harvie=(HRF," . $mp->primary . "), (HRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Harwood" );
if ( $mp->primary != "HRT" || $mp->secondary != "HRT" ) {
	print "<br>Harwood=(HRT," . $mp->primary . "), (HRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hathaway" );
if ( $mp->primary != "H0" || $mp->secondary != "HT" ) {
	print "<br>Hathaway=(H0," . $mp->primary . "), (HT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Haukeness" );
if ( $mp->primary != "HKNS" || $mp->secondary != "HKNS" ) {
	print "<br>Haukeness=(HKNS," . $mp->primary . "), (HKNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hawkes" );
if ( $mp->primary != "HKS" || $mp->secondary != "HKS" ) {
	print "<br>Hawkes=(HKS," . $mp->primary . "), (HKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hawkhurst" );
if ( $mp->primary != "HKRS" || $mp->secondary != "HKRS" ) {
	print "<br>Hawkhurst=(HKRS," . $mp->primary . "), (HKRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hawkins" );
if ( $mp->primary != "HKNS" || $mp->secondary != "HKNS" ) {
	print "<br>Hawkins=(HKNS," . $mp->primary . "), (HKNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hawley" );
if ( $mp->primary != "HL" || $mp->secondary != "HL" ) {
	print "<br>Hawley=(HL," . $mp->primary . "), (HL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Heald" );
if ( $mp->primary != "HLT" || $mp->secondary != "HLT" ) {
	print "<br>Heald=(HLT," . $mp->primary . "), (HLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Helsdon" );
if ( $mp->primary != "HLST" || $mp->secondary != "HLST" ) {
	print "<br>Helsdon=(HLST," . $mp->primary . "), (HLST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hemenway" );
if ( $mp->primary != "HMN" || $mp->secondary != "HMN" ) {
	print "<br>Hemenway=(HMN," . $mp->primary . "), (HMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hemmenway" );
if ( $mp->primary != "HMN" || $mp->secondary != "HMN" ) {
	print "<br>Hemmenway=(HMN," . $mp->primary . "), (HMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Henck" );
if ( $mp->primary != "HNK" || $mp->secondary != "HNK" ) {
	print "<br>Henck=(HNK," . $mp->primary . "), (HNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Henderson" );
if ( $mp->primary != "HNTR" || $mp->secondary != "HNTR" ) {
	print "<br>Henderson=(HNTR," . $mp->primary . "), (HNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hendricks" );
if ( $mp->primary != "HNTR" || $mp->secondary != "HNTR" ) {
	print "<br>Hendricks=(HNTR," . $mp->primary . "), (HNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hersey" );
if ( $mp->primary != "HRS" || $mp->secondary != "HRS" ) {
	print "<br>Hersey=(HRS," . $mp->primary . "), (HRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hewes" );
if ( $mp->primary != "HS" || $mp->secondary != "HS" ) {
	print "<br>Hewes=(HS," . $mp->primary . "), (HS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Heyman" );
if ( $mp->primary != "HMN" || $mp->secondary != "HMN" ) {
	print "<br>Heyman=(HMN," . $mp->primary . "), (HMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hicks" );
if ( $mp->primary != "HKS" || $mp->secondary != "HKS" ) {
	print "<br>Hicks=(HKS," . $mp->primary . "), (HKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hidden" );
if ( $mp->primary != "HTN" || $mp->secondary != "HTN" ) {
	print "<br>Hidden=(HTN," . $mp->primary . "), (HTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Higgs" );
if ( $mp->primary != "HKS" || $mp->secondary != "HKS" ) {
	print "<br>Higgs=(HKS," . $mp->primary . "), (HKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hill" );
if ( $mp->primary != "HL" || $mp->secondary != "HL" ) {
	print "<br>Hill=(HL," . $mp->primary . "), (HL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hills" );
if ( $mp->primary != "HLS" || $mp->secondary != "HLS" ) {
	print "<br>Hills=(HLS," . $mp->primary . "), (HLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hinckley" );
if ( $mp->primary != "HNKL" || $mp->secondary != "HNKL" ) {
	print "<br>Hinckley=(HNKL," . $mp->primary . "), (HNKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hipwell" );
if ( $mp->primary != "HPL" || $mp->secondary != "HPL" ) {
	print "<br>Hipwell=(HPL," . $mp->primary . "), (HPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hobart" );
if ( $mp->primary != "HPRT" || $mp->secondary != "HPRT" ) {
	print "<br>Hobart=(HPRT," . $mp->primary . "), (HPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hoben" );
if ( $mp->primary != "HPN" || $mp->secondary != "HPN" ) {
	print "<br>Hoben=(HPN," . $mp->primary . "), (HPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hoffmann" );
if ( $mp->primary != "HFMN" || $mp->secondary != "HFMN" ) {
	print "<br>Hoffmann=(HFMN," . $mp->primary . "), (HFMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hogan" );
if ( $mp->primary != "HKN" || $mp->secondary != "HKN" ) {
	print "<br>Hogan=(HKN," . $mp->primary . "), (HKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Holmes" );
if ( $mp->primary != "HLMS" || $mp->secondary != "HLMS" ) {
	print "<br>Holmes=(HLMS," . $mp->primary . "), (HLMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hoo" );
if ( $mp->primary != "H" || $mp->secondary != "H" ) {
	print "<br>Hoo=(H," . $mp->primary . "), (H," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hooker" );
if ( $mp->primary != "HKR" || $mp->secondary != "HKR" ) {
	print "<br>Hooker=(HKR," . $mp->primary . "), (HKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hopcott" );
if ( $mp->primary != "HPKT" || $mp->secondary != "HPKT" ) {
	print "<br>Hopcott=(HPKT," . $mp->primary . "), (HPKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hopkins" );
if ( $mp->primary != "HPKN" || $mp->secondary != "HPKN" ) {
	print "<br>Hopkins=(HPKN," . $mp->primary . "), (HPKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hopkinson" );
if ( $mp->primary != "HPKN" || $mp->secondary != "HPKN" ) {
	print "<br>Hopkinson=(HPKN," . $mp->primary . "), (HPKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hornsey" );
if ( $mp->primary != "HRNS" || $mp->secondary != "HRNS" ) {
	print "<br>Hornsey=(HRNS," . $mp->primary . "), (HRNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Houckgeest" );
if ( $mp->primary != "HKJS" || $mp->secondary != "HKKS" ) {
	print "<br>Houckgeest=(HKJS," . $mp->primary . "), (HKKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hough" );
if ( $mp->primary != "H" || $mp->secondary != "H" ) {
	print "<br>Hough=(H," . $mp->primary . "), (H," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Houstin" );
if ( $mp->primary != "HSTN" || $mp->secondary != "HSTN" ) {
	print "<br>Houstin=(HSTN," . $mp->primary . "), (HSTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "How" );
if ( $mp->primary != "H" || $mp->secondary != "HF" ) {
	print "<br>How=(H," . $mp->primary . "), (HF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Howe" );
if ( $mp->primary != "H" || $mp->secondary != "H" ) {
	print "<br>Howe=(H," . $mp->primary . "), (H," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Howland" );
if ( $mp->primary != "HLNT" || $mp->secondary != "HLNT" ) {
	print "<br>Howland=(HLNT," . $mp->primary . "), (HLNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hubner" );
if ( $mp->primary != "HPNR" || $mp->secondary != "HPNR" ) {
	print "<br>Hubner=(HPNR," . $mp->primary . "), (HPNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hudnut" );
if ( $mp->primary != "HTNT" || $mp->secondary != "HTNT" ) {
	print "<br>Hudnut=(HTNT," . $mp->primary . "), (HTNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hughes" );
if ( $mp->primary != "HS" || $mp->secondary != "HS" ) {
	print "<br>Hughes=(HS," . $mp->primary . "), (HS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hull" );
if ( $mp->primary != "HL" || $mp->secondary != "HL" ) {
	print "<br>Hull=(HL," . $mp->primary . "), (HL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hulme" );
if ( $mp->primary != "HLM" || $mp->secondary != "HLM" ) {
	print "<br>Hulme=(HLM," . $mp->primary . "), (HLM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hume" );
if ( $mp->primary != "HM" || $mp->secondary != "HM" ) {
	print "<br>Hume=(HM," . $mp->primary . "), (HM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hundertumark" );
if ( $mp->primary != "HNTR" || $mp->secondary != "HNTR" ) {
	print "<br>Hundertumark=(HNTR," . $mp->primary . "), (HNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hundley" );
if ( $mp->primary != "HNTL" || $mp->secondary != "HNTL" ) {
	print "<br>Hundley=(HNTL," . $mp->primary . "), (HNTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hungerford" );
if ( $mp->primary != "HNKR" || $mp->secondary != "HNJR" ) {
	print "<br>Hungerford=(HNKR," . $mp->primary . "), (HNJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hunt" );
if ( $mp->primary != "HNT" || $mp->secondary != "HNT" ) {
	print "<br>Hunt=(HNT," . $mp->primary . "), (HNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hurst" );
if ( $mp->primary != "HRST" || $mp->secondary != "HRST" ) {
	print "<br>Hurst=(HRST," . $mp->primary . "), (HRST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Husbands" );
if ( $mp->primary != "HSPN" || $mp->secondary != "HSPN" ) {
	print "<br>Husbands=(HSPN," . $mp->primary . "), (HSPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hussey" );
if ( $mp->primary != "HS" || $mp->secondary != "HS" ) {
	print "<br>Hussey=(HS," . $mp->primary . "), (HS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Husted" );
if ( $mp->primary != "HSTT" || $mp->secondary != "HSTT" ) {
	print "<br>Husted=(HSTT," . $mp->primary . "), (HSTT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hutchins" );
if ( $mp->primary != "HXNS" || $mp->secondary != "HXNS" ) {
	print "<br>Hutchins=(HXNS," . $mp->primary . "), (HXNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Hutchinson" );
if ( $mp->primary != "HXNS" || $mp->secondary != "HXNS" ) {
	print "<br>Hutchinson=(HXNS," . $mp->primary . "), (HXNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Huttinger" );
if ( $mp->primary != "HTNK" || $mp->secondary != "HTNJ" ) {
	print "<br>Huttinger=(HTNK," . $mp->primary . "), (HTNJ," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Huybertsen" );
if ( $mp->primary != "HPRT" || $mp->secondary != "HPRT" ) {
	print "<br>Huybertsen=(HPRT," . $mp->primary . "), (HPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Iddenden" );
if ( $mp->primary != "ATNT" || $mp->secondary != "ATNT" ) {
	print "<br>Iddenden=(ATNT," . $mp->primary . "), (ATNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ingraham" );
if ( $mp->primary != "ANKR" || $mp->secondary != "ANKR" ) {
	print "<br>Ingraham=(ANKR," . $mp->primary . "), (ANKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ives" );
if ( $mp->primary != "AFS" || $mp->secondary != "AFS" ) {
	print "<br>Ives=(AFS," . $mp->primary . "), (AFS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jackson" );
if ( $mp->primary != "JKSN" || $mp->secondary != "AKSN" ) {
	print "<br>Jackson=(JKSN," . $mp->primary . "), (AKSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jacob" );
if ( $mp->primary != "JKP" || $mp->secondary != "AKP" ) {
	print "<br>Jacob=(JKP," . $mp->primary . "), (AKP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jans" );
if ( $mp->primary != "JNS" || $mp->secondary != "ANS" ) {
	print "<br>Jans=(JNS," . $mp->primary . "), (ANS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jenkins" );
if ( $mp->primary != "JNKN" || $mp->secondary != "ANKN" ) {
	print "<br>Jenkins=(JNKN," . $mp->primary . "), (ANKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jewett" );
if ( $mp->primary != "JT" || $mp->secondary != "AT" ) {
	print "<br>Jewett=(JT," . $mp->primary . "), (AT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jewitt" );
if ( $mp->primary != "JT" || $mp->secondary != "AT" ) {
	print "<br>Jewitt=(JT," . $mp->primary . "), (AT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Johnson" );
if ( $mp->primary != "JNSN" || $mp->secondary != "ANSN" ) {
	print "<br>Johnson=(JNSN," . $mp->primary . "), (ANSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jones" );
if ( $mp->primary != "JNS" || $mp->secondary != "ANS" ) {
	print "<br>Jones=(JNS," . $mp->primary . "), (ANS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Josephine" );
if ( $mp->primary != "JSFN" || $mp->secondary != "HSFN" ) {
	print "<br>Josephine=(JSFN," . $mp->primary . "), (HSFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Judd" );
if ( $mp->primary != "JT" || $mp->secondary != "AT" ) {
	print "<br>Judd=(JT," . $mp->primary . "), (AT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "June" );
if ( $mp->primary != "JN" || $mp->secondary != "AN" ) {
	print "<br>June=(JN," . $mp->primary . "), (AN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kamarowska" );
if ( $mp->primary != "KMRS" || $mp->secondary != "KMRS" ) {
	print "<br>Kamarowska=(KMRS," . $mp->primary . "), (KMRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kay" );
if ( $mp->primary != "K" || $mp->secondary != "K" ) {
	print "<br>Kay=(K," . $mp->primary . "), (K," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kelley" );
if ( $mp->primary != "KL" || $mp->secondary != "KL" ) {
	print "<br>Kelley=(KL," . $mp->primary . "), (KL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kelly" );
if ( $mp->primary != "KL" || $mp->secondary != "KL" ) {
	print "<br>Kelly=(KL," . $mp->primary . "), (KL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Keymber" );
if ( $mp->primary != "KMPR" || $mp->secondary != "KMPR" ) {
	print "<br>Keymber=(KMPR," . $mp->primary . "), (KMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Keynes" );
if ( $mp->primary != "KNS" || $mp->secondary != "KNS" ) {
	print "<br>Keynes=(KNS," . $mp->primary . "), (KNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kilham" );
if ( $mp->primary != "KLM" || $mp->secondary != "KLM" ) {
	print "<br>Kilham=(KLM," . $mp->primary . "), (KLM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kim" );
if ( $mp->primary != "KM" || $mp->secondary != "KM" ) {
	print "<br>Kim=(KM," . $mp->primary . "), (KM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kimball" );
if ( $mp->primary != "KMPL" || $mp->secondary != "KMPL" ) {
	print "<br>Kimball=(KMPL," . $mp->primary . "), (KMPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "King" );
if ( $mp->primary != "KNK" || $mp->secondary != "KNK" ) {
	print "<br>King=(KNK," . $mp->primary . "), (KNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kinsey" );
if ( $mp->primary != "KNS" || $mp->secondary != "KNS" ) {
	print "<br>Kinsey=(KNS," . $mp->primary . "), (KNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kirk" );
if ( $mp->primary != "KRK" || $mp->secondary != "KRK" ) {
	print "<br>Kirk=(KRK," . $mp->primary . "), (KRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kirton" );
if ( $mp->primary != "KRTN" || $mp->secondary != "KRTN" ) {
	print "<br>Kirton=(KRTN," . $mp->primary . "), (KRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kistler" );
if ( $mp->primary != "KSTL" || $mp->secondary != "KSTL" ) {
	print "<br>Kistler=(KSTL," . $mp->primary . "), (KSTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kitchen" );
if ( $mp->primary != "KXN" || $mp->secondary != "KXN" ) {
	print "<br>Kitchen=(KXN," . $mp->primary . "), (KXN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kitson" );
if ( $mp->primary != "KTSN" || $mp->secondary != "KTSN" ) {
	print "<br>Kitson=(KTSN," . $mp->primary . "), (KTSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Klett" );
if ( $mp->primary != "KLT" || $mp->secondary != "KLT" ) {
	print "<br>Klett=(KLT," . $mp->primary . "), (KLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Kline" );
if ( $mp->primary != "KLN" || $mp->secondary != "KLN" ) {
	print "<br>Kline=(KLN," . $mp->primary . "), (KLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Knapp" );
if ( $mp->primary != "NP" || $mp->secondary != "NP" ) {
	print "<br>Knapp=(NP," . $mp->primary . "), (NP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Knight" );
if ( $mp->primary != "NT" || $mp->secondary != "NT" ) {
	print "<br>Knight=(NT," . $mp->primary . "), (NT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Knote" );
if ( $mp->primary != "NT" || $mp->secondary != "NT" ) {
	print "<br>Knote=(NT," . $mp->primary . "), (NT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Knott" );
if ( $mp->primary != "NT" || $mp->secondary != "NT" ) {
	print "<br>Knott=(NT," . $mp->primary . "), (NT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Knox" );
if ( $mp->primary != "NKS" || $mp->secondary != "NKS" ) {
	print "<br>Knox=(NKS," . $mp->primary . "), (NKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Koeller" );
if ( $mp->primary != "KLR" || $mp->secondary != "KLR" ) {
	print "<br>Koeller=(KLR," . $mp->primary . "), (KLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "La Pointe" );
if ( $mp->primary != "LPNT" || $mp->secondary != "LPNT" ) {
	print "<br>La Pointe=(LPNT," . $mp->primary . "), (LPNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "LaPlante" );
if ( $mp->primary != "LPLN" || $mp->secondary != "LPLN" ) {
	print "<br>LaPlante=(LPLN," . $mp->primary . "), (LPLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Laimbeer" );
if ( $mp->primary != "LMPR" || $mp->secondary != "LMPR" ) {
	print "<br>Laimbeer=(LMPR," . $mp->primary . "), (LMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lamb" );
if ( $mp->primary != "LMP" || $mp->secondary != "LMP" ) {
	print "<br>Lamb=(LMP," . $mp->primary . "), (LMP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lambertson" );
if ( $mp->primary != "LMPR" || $mp->secondary != "LMPR" ) {
	print "<br>Lambertson=(LMPR," . $mp->primary . "), (LMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lancto" );
if ( $mp->primary != "LNKT" || $mp->secondary != "LNKT" ) {
	print "<br>Lancto=(LNKT," . $mp->primary . "), (LNKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Landry" );
if ( $mp->primary != "LNTR" || $mp->secondary != "LNTR" ) {
	print "<br>Landry=(LNTR," . $mp->primary . "), (LNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lane" );
if ( $mp->primary != "LN" || $mp->secondary != "LN" ) {
	print "<br>Lane=(LN," . $mp->primary . "), (LN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Langendyck" );
if ( $mp->primary != "LNJN" || $mp->secondary != "LNKN" ) {
	print "<br>Langendyck=(LNJN," . $mp->primary . "), (LNKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Langer" );
if ( $mp->primary != "LNKR" || $mp->secondary != "LNJR" ) {
	print "<br>Langer=(LNKR," . $mp->primary . "), (LNJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Langford" );
if ( $mp->primary != "LNKF" || $mp->secondary != "LNKF" ) {
	print "<br>Langford=(LNKF," . $mp->primary . "), (LNKF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lantersee" );
if ( $mp->primary != "LNTR" || $mp->secondary != "LNTR" ) {
	print "<br>Lantersee=(LNTR," . $mp->primary . "), (LNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Laquer" );
if ( $mp->primary != "LKR" || $mp->secondary != "LKR" ) {
	print "<br>Laquer=(LKR," . $mp->primary . "), (LKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Larkin" );
if ( $mp->primary != "LRKN" || $mp->secondary != "LRKN" ) {
	print "<br>Larkin=(LRKN," . $mp->primary . "), (LRKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Latham" );
if ( $mp->primary != "LTM" || $mp->secondary != "LTM" ) {
	print "<br>Latham=(LTM," . $mp->primary . "), (LTM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lathrop" );
if ( $mp->primary != "L0RP" || $mp->secondary != "LTRP" ) {
	print "<br>Lathrop=(L0RP," . $mp->primary . "), (LTRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lauter" );
if ( $mp->primary != "LTR" || $mp->secondary != "LTR" ) {
	print "<br>Lauter=(LTR," . $mp->primary . "), (LTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lawrence" );
if ( $mp->primary != "LRNS" || $mp->secondary != "LRNS" ) {
	print "<br>Lawrence=(LRNS," . $mp->primary . "), (LRNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Leach" );
if ( $mp->primary != "LK" || $mp->secondary != "LK" ) {
	print "<br>Leach=(LK," . $mp->primary . "), (LK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Leager" );
if ( $mp->primary != "LKR" || $mp->secondary != "LJR" ) {
	print "<br>Leager=(LKR," . $mp->primary . "), (LJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Learned" );
if ( $mp->primary != "LRNT" || $mp->secondary != "LRNT" ) {
	print "<br>Learned=(LRNT," . $mp->primary . "), (LRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Leavitt" );
if ( $mp->primary != "LFT" || $mp->secondary != "LFT" ) {
	print "<br>Leavitt=(LFT," . $mp->primary . "), (LFT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lee" );
if ( $mp->primary != "L" || $mp->secondary != "L" ) {
	print "<br>Lee=(L," . $mp->primary . "), (L," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Leete" );
if ( $mp->primary != "LT" || $mp->secondary != "LT" ) {
	print "<br>Leete=(LT," . $mp->primary . "), (LT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Leggett" );
if ( $mp->primary != "LKT" || $mp->secondary != "LKT" ) {
	print "<br>Leggett=(LKT," . $mp->primary . "), (LKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Leland" );
if ( $mp->primary != "LLNT" || $mp->secondary != "LLNT" ) {
	print "<br>Leland=(LLNT," . $mp->primary . "), (LLNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Leonard" );
if ( $mp->primary != "LNRT" || $mp->secondary != "LNRT" ) {
	print "<br>Leonard=(LNRT," . $mp->primary . "), (LNRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lester" );
if ( $mp->primary != "LSTR" || $mp->secondary != "LSTR" ) {
	print "<br>Lester=(LSTR," . $mp->primary . "), (LSTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lestrange" );
if ( $mp->primary != "LSTR" || $mp->secondary != "LSTR" ) {
	print "<br>Lestrange=(LSTR," . $mp->primary . "), (LSTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lethem" );
if ( $mp->primary != "L0M" || $mp->secondary != "LTM" ) {
	print "<br>Lethem=(L0M," . $mp->primary . "), (LTM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Levine" );
if ( $mp->primary != "LFN" || $mp->secondary != "LFN" ) {
	print "<br>Levine=(LFN," . $mp->primary . "), (LFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lewes" );
if ( $mp->primary != "LS" || $mp->secondary != "LS" ) {
	print "<br>Lewes=(LS," . $mp->primary . "), (LS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lewis" );
if ( $mp->primary != "LS" || $mp->secondary != "LS" ) {
	print "<br>Lewis=(LS," . $mp->primary . "), (LS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lincoln" );
if ( $mp->primary != "LNKL" || $mp->secondary != "LNKL" ) {
	print "<br>Lincoln=(LNKL," . $mp->primary . "), (LNKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lindsey" );
if ( $mp->primary != "LNTS" || $mp->secondary != "LNTS" ) {
	print "<br>Lindsey=(LNTS," . $mp->primary . "), (LNTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Linher" );
if ( $mp->primary != "LNR" || $mp->secondary != "LNR" ) {
	print "<br>Linher=(LNR," . $mp->primary . "), (LNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lippet" );
if ( $mp->primary != "LPT" || $mp->secondary != "LPT" ) {
	print "<br>Lippet=(LPT," . $mp->primary . "), (LPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lippincott" );
if ( $mp->primary != "LPNK" || $mp->secondary != "LPNK" ) {
	print "<br>Lippincott=(LPNK," . $mp->primary . "), (LPNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lockwood" );
if ( $mp->primary != "LKT" || $mp->secondary != "LKT" ) {
	print "<br>Lockwood=(LKT," . $mp->primary . "), (LKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Loines" );
if ( $mp->primary != "LNS" || $mp->secondary != "LNS" ) {
	print "<br>Loines=(LNS," . $mp->primary . "), (LNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lombard" );
if ( $mp->primary != "LMPR" || $mp->secondary != "LMPR" ) {
	print "<br>Lombard=(LMPR," . $mp->primary . "), (LMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Long" );
if ( $mp->primary != "LNK" || $mp->secondary != "LNK" ) {
	print "<br>Long=(LNK," . $mp->primary . "), (LNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Longespee" );
if ( $mp->primary != "LNJS" || $mp->secondary != "LNKS" ) {
	print "<br>Longespee=(LNJS," . $mp->primary . "), (LNKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Look" );
if ( $mp->primary != "LK" || $mp->secondary != "LK" ) {
	print "<br>Look=(LK," . $mp->primary . "), (LK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lounsberry" );
if ( $mp->primary != "LNSP" || $mp->secondary != "LNSP" ) {
	print "<br>Lounsberry=(LNSP," . $mp->primary . "), (LNSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lounsbury" );
if ( $mp->primary != "LNSP" || $mp->secondary != "LNSP" ) {
	print "<br>Lounsbury=(LNSP," . $mp->primary . "), (LNSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Louthe" );
if ( $mp->primary != "L0" || $mp->secondary != "LT" ) {
	print "<br>Louthe=(L0," . $mp->primary . "), (LT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Loveyne" );
if ( $mp->primary != "LFN" || $mp->secondary != "LFN" ) {
	print "<br>Loveyne=(LFN," . $mp->primary . "), (LFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lowe" );
if ( $mp->primary != "L" || $mp->secondary != "L" ) {
	print "<br>Lowe=(L," . $mp->primary . "), (L," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ludlam" );
if ( $mp->primary != "LTLM" || $mp->secondary != "LTLM" ) {
	print "<br>Ludlam=(LTLM," . $mp->primary . "), (LTLM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lumbard" );
if ( $mp->primary != "LMPR" || $mp->secondary != "LMPR" ) {
	print "<br>Lumbard=(LMPR," . $mp->primary . "), (LMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lund" );
if ( $mp->primary != "LNT" || $mp->secondary != "LNT" ) {
	print "<br>Lund=(LNT," . $mp->primary . "), (LNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Luno" );
if ( $mp->primary != "LN" || $mp->secondary != "LN" ) {
	print "<br>Luno=(LN," . $mp->primary . "), (LN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lutz" );
if ( $mp->primary != "LTS" || $mp->secondary != "LTS" ) {
	print "<br>Lutz=(LTS," . $mp->primary . "), (LTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lydia" );
if ( $mp->primary != "LT" || $mp->secondary != "LT" ) {
	print "<br>Lydia=(LT," . $mp->primary . "), (LT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lynne" );
if ( $mp->primary != "LN" || $mp->secondary != "LN" ) {
	print "<br>Lynne=(LN," . $mp->primary . "), (LN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Lyon" );
if ( $mp->primary != "LN" || $mp->secondary != "LN" ) {
	print "<br>Lyon=(LN," . $mp->primary . "), (LN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "MacAlpin" );
if ( $mp->primary != "MKLP" || $mp->secondary != "MKLP" ) {
	print "<br>MacAlpin=(MKLP," . $mp->primary . "), (MKLP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "MacBricc" );
if ( $mp->primary != "MKPR" || $mp->secondary != "MKPR" ) {
	print "<br>MacBricc=(MKPR," . $mp->primary . "), (MKPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "MacCrinan" );
if ( $mp->primary != "MKRN" || $mp->secondary != "MKRN" ) {
	print "<br>MacCrinan=(MKRN," . $mp->primary . "), (MKRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "MacKenneth" );
if ( $mp->primary != "MKN0" || $mp->secondary != "MKNT" ) {
	print "<br>MacKenneth=(MKN0," . $mp->primary . "), (MKNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "MacMael nam Bo" );
if ( $mp->primary != "MKML" || $mp->secondary != "MKML" ) {
	print "<br>MacMael nam Bo=(MKML," . $mp->primary . "), (MKML," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "MacMurchada" );
if ( $mp->primary != "MKMR" || $mp->secondary != "MKMR" ) {
	print "<br>MacMurchada=(MKMR," . $mp->primary . "), (MKMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Macomber" );
if ( $mp->primary != "MKMP" || $mp->secondary != "MKMP" ) {
	print "<br>Macomber=(MKMP," . $mp->primary . "), (MKMP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Macy" );
if ( $mp->primary != "MS" || $mp->secondary != "MS" ) {
	print "<br>Macy=(MS," . $mp->primary . "), (MS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Magnus" );
if ( $mp->primary != "MNS" || $mp->secondary != "MKNS" ) {
	print "<br>Magnus=(MNS," . $mp->primary . "), (MKNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mahien" );
if ( $mp->primary != "MHN" || $mp->secondary != "MHN" ) {
	print "<br>Mahien=(MHN," . $mp->primary . "), (MHN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Malmains" );
if ( $mp->primary != "MLMN" || $mp->secondary != "MLMN" ) {
	print "<br>Malmains=(MLMN," . $mp->primary . "), (MLMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Malory" );
if ( $mp->primary != "MLR" || $mp->secondary != "MLR" ) {
	print "<br>Malory=(MLR," . $mp->primary . "), (MLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mancinelli" );
if ( $mp->primary != "MNSN" || $mp->secondary != "MNSN" ) {
	print "<br>Mancinelli=(MNSN," . $mp->primary . "), (MNSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mancini" );
if ( $mp->primary != "MNSN" || $mp->secondary != "MNSN" ) {
	print "<br>Mancini=(MNSN," . $mp->primary . "), (MNSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mann" );
if ( $mp->primary != "MN" || $mp->secondary != "MN" ) {
	print "<br>Mann=(MN," . $mp->primary . "), (MN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Manning" );
if ( $mp->primary != "MNNK" || $mp->secondary != "MNNK" ) {
	print "<br>Manning=(MNNK," . $mp->primary . "), (MNNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Manter" );
if ( $mp->primary != "MNTR" || $mp->secondary != "MNTR" ) {
	print "<br>Manter=(MNTR," . $mp->primary . "), (MNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marion" );
if ( $mp->primary != "MRN" || $mp->secondary != "MRN" ) {
	print "<br>Marion=(MRN," . $mp->primary . "), (MRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marley" );
if ( $mp->primary != "MRL" || $mp->secondary != "MRL" ) {
	print "<br>Marley=(MRL," . $mp->primary . "), (MRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marmion" );
if ( $mp->primary != "MRMN" || $mp->secondary != "MRMN" ) {
	print "<br>Marmion=(MRMN," . $mp->primary . "), (MRMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marquart" );
if ( $mp->primary != "MRKR" || $mp->secondary != "MRKR" ) {
	print "<br>Marquart=(MRKR," . $mp->primary . "), (MRKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marsh" );
if ( $mp->primary != "MRX" || $mp->secondary != "MRX" ) {
	print "<br>Marsh=(MRX," . $mp->primary . "), (MRX," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marshal" );
if ( $mp->primary != "MRXL" || $mp->secondary != "MRXL" ) {
	print "<br>Marshal=(MRXL," . $mp->primary . "), (MRXL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marshall" );
if ( $mp->primary != "MRXL" || $mp->secondary != "MRXL" ) {
	print "<br>Marshall=(MRXL," . $mp->primary . "), (MRXL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Martel" );
if ( $mp->primary != "MRTL" || $mp->secondary != "MRTL" ) {
	print "<br>Martel=(MRTL," . $mp->primary . "), (MRTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Martha" );
if ( $mp->primary != "MR0" || $mp->secondary != "MRT" ) {
	print "<br>Martha=(MR0," . $mp->primary . "), (MRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Martin" );
if ( $mp->primary != "MRTN" || $mp->secondary != "MRTN" ) {
	print "<br>Martin=(MRTN," . $mp->primary . "), (MRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marturano" );
if ( $mp->primary != "MRTR" || $mp->secondary != "MRTR" ) {
	print "<br>Marturano=(MRTR," . $mp->primary . "), (MRTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Marvin" );
if ( $mp->primary != "MRFN" || $mp->secondary != "MRFN" ) {
	print "<br>Marvin=(MRFN," . $mp->primary . "), (MRFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mary" );
if ( $mp->primary != "MR" || $mp->secondary != "MR" ) {
	print "<br>Mary=(MR," . $mp->primary . "), (MR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mason" );
if ( $mp->primary != "MSN" || $mp->secondary != "MSN" ) {
	print "<br>Mason=(MSN," . $mp->primary . "), (MSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Maxwell" );
if ( $mp->primary != "MKSL" || $mp->secondary != "MKSL" ) {
	print "<br>Maxwell=(MKSL," . $mp->primary . "), (MKSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mayhew" );
if ( $mp->primary != "MH" || $mp->secondary != "MHF" ) {
	print "<br>Mayhew=(MH," . $mp->primary . "), (MHF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McAllaster" );
if ( $mp->primary != "MKLS" || $mp->secondary != "MKLS" ) {
	print "<br>McAllaster=(MKLS," . $mp->primary . "), (MKLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McAllister" );
if ( $mp->primary != "MKLS" || $mp->secondary != "MKLS" ) {
	print "<br>McAllister=(MKLS," . $mp->primary . "), (MKLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McConnell" );
if ( $mp->primary != "MKNL" || $mp->secondary != "MKNL" ) {
	print "<br>McConnell=(MKNL," . $mp->primary . "), (MKNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McFarland" );
if ( $mp->primary != "MKFR" || $mp->secondary != "MKFR" ) {
	print "<br>McFarland=(MKFR," . $mp->primary . "), (MKFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McIlroy" );
if ( $mp->primary != "MSLR" || $mp->secondary != "MSLR" ) {
	print "<br>McIlroy=(MSLR," . $mp->primary . "), (MSLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McNair" );
if ( $mp->primary != "MKNR" || $mp->secondary != "MKNR" ) {
	print "<br>McNair=(MKNR," . $mp->primary . "), (MKNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McNair-Landry" );
if ( $mp->primary != "MKNR" || $mp->secondary != "MKNR" ) {
	print "<br>McNair-Landry=(MKNR," . $mp->primary . "), (MKNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McRaven" );
if ( $mp->primary != "MKRF" || $mp->secondary != "MKRF" ) {
	print "<br>McRaven=(MKRF," . $mp->primary . "), (MKRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mead" );
if ( $mp->primary != "MT" || $mp->secondary != "MT" ) {
	print "<br>Mead=(MT," . $mp->primary . "), (MT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Meade" );
if ( $mp->primary != "MT" || $mp->secondary != "MT" ) {
	print "<br>Meade=(MT," . $mp->primary . "), (MT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Meck" );
if ( $mp->primary != "MK" || $mp->secondary != "MK" ) {
	print "<br>Meck=(MK," . $mp->primary . "), (MK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Melton" );
if ( $mp->primary != "MLTN" || $mp->secondary != "MLTN" ) {
	print "<br>Melton=(MLTN," . $mp->primary . "), (MLTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mendenhall" );
if ( $mp->primary != "MNTN" || $mp->secondary != "MNTN" ) {
	print "<br>Mendenhall=(MNTN," . $mp->primary . "), (MNTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mering" );
if ( $mp->primary != "MRNK" || $mp->secondary != "MRNK" ) {
	print "<br>Mering=(MRNK," . $mp->primary . "), (MRNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Merrick" );
if ( $mp->primary != "MRK" || $mp->secondary != "MRK" ) {
	print "<br>Merrick=(MRK," . $mp->primary . "), (MRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Merry" );
if ( $mp->primary != "MR" || $mp->secondary != "MR" ) {
	print "<br>Merry=(MR," . $mp->primary . "), (MR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mighill" );
if ( $mp->primary != "ML" || $mp->secondary != "ML" ) {
	print "<br>Mighill=(ML," . $mp->primary . "), (ML," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Miller" );
if ( $mp->primary != "MLR" || $mp->secondary != "MLR" ) {
	print "<br>Miller=(MLR," . $mp->primary . "), (MLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Milton" );
if ( $mp->primary != "MLTN" || $mp->secondary != "MLTN" ) {
	print "<br>Milton=(MLTN," . $mp->primary . "), (MLTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mohun" );
if ( $mp->primary != "MHN" || $mp->secondary != "MHN" ) {
	print "<br>Mohun=(MHN," . $mp->primary . "), (MHN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Montague" );
if ( $mp->primary != "MNTK" || $mp->secondary != "MNTK" ) {
	print "<br>Montague=(MNTK," . $mp->primary . "), (MNTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Montboucher" );
if ( $mp->primary != "MNTP" || $mp->secondary != "MNTP" ) {
	print "<br>Montboucher=(MNTP," . $mp->primary . "), (MNTP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Moore" );
if ( $mp->primary != "MR" || $mp->secondary != "MR" ) {
	print "<br>Moore=(MR," . $mp->primary . "), (MR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Morrel" );
if ( $mp->primary != "MRL" || $mp->secondary != "MRL" ) {
	print "<br>Morrel=(MRL," . $mp->primary . "), (MRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Morrill" );
if ( $mp->primary != "MRL" || $mp->secondary != "MRL" ) {
	print "<br>Morrill=(MRL," . $mp->primary . "), (MRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Morris" );
if ( $mp->primary != "MRS" || $mp->secondary != "MRS" ) {
	print "<br>Morris=(MRS," . $mp->primary . "), (MRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Morton" );
if ( $mp->primary != "MRTN" || $mp->secondary != "MRTN" ) {
	print "<br>Morton=(MRTN," . $mp->primary . "), (MRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Moton" );
if ( $mp->primary != "MTN" || $mp->secondary != "MTN" ) {
	print "<br>Moton=(MTN," . $mp->primary . "), (MTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Muir" );
if ( $mp->primary != "MR" || $mp->secondary != "MR" ) {
	print "<br>Muir=(MR," . $mp->primary . "), (MR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mulferd" );
if ( $mp->primary != "MLFR" || $mp->secondary != "MLFR" ) {
	print "<br>Mulferd=(MLFR," . $mp->primary . "), (MLFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mullins" );
if ( $mp->primary != "MLNS" || $mp->secondary != "MLNS" ) {
	print "<br>Mullins=(MLNS," . $mp->primary . "), (MLNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Mulso" );
if ( $mp->primary != "MLS" || $mp->secondary != "MLS" ) {
	print "<br>Mulso=(MLS," . $mp->primary . "), (MLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Munger" );
if ( $mp->primary != "MNKR" || $mp->secondary != "MNJR" ) {
	print "<br>Munger=(MNKR," . $mp->primary . "), (MNJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Munt" );
if ( $mp->primary != "MNT" || $mp->secondary != "MNT" ) {
	print "<br>Munt=(MNT," . $mp->primary . "), (MNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Murchad" );
if ( $mp->primary != "MRXT" || $mp->secondary != "MRKT" ) {
	print "<br>Murchad=(MRXT," . $mp->primary . "), (MRKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Murdock" );
if ( $mp->primary != "MRTK" || $mp->secondary != "MRTK" ) {
	print "<br>Murdock=(MRTK," . $mp->primary . "), (MRTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Murray" );
if ( $mp->primary != "MR" || $mp->secondary != "MR" ) {
	print "<br>Murray=(MR," . $mp->primary . "), (MR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Muskett" );
if ( $mp->primary != "MSKT" || $mp->secondary != "MSKT" ) {
	print "<br>Muskett=(MSKT," . $mp->primary . "), (MSKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Myers" );
if ( $mp->primary != "MRS" || $mp->secondary != "MRS" ) {
	print "<br>Myers=(MRS," . $mp->primary . "), (MRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Myrick" );
if ( $mp->primary != "MRK" || $mp->secondary != "MRK" ) {
	print "<br>Myrick=(MRK," . $mp->primary . "), (MRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "NORRIS" );
if ( $mp->primary != "NRS" || $mp->secondary != "NRS" ) {
	print "<br>NORRIS=(NRS," . $mp->primary . "), (NRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Nayle" );
if ( $mp->primary != "NL" || $mp->secondary != "NL" ) {
	print "<br>Nayle=(NL," . $mp->primary . "), (NL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Newcomb" );
if ( $mp->primary != "NKMP" || $mp->secondary != "NKMP" ) {
	print "<br>Newcomb=(NKMP," . $mp->primary . "), (NKMP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Newcomb(e)" );
if ( $mp->primary != "NKMP" || $mp->secondary != "NKMP" ) {
	print "<br>Newcomb(e)=(NKMP," . $mp->primary . "), (NKMP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Newkirk" );
if ( $mp->primary != "NKRK" || $mp->secondary != "NKRK" ) {
	print "<br>Newkirk=(NKRK," . $mp->primary . "), (NKRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Newton" );
if ( $mp->primary != "NTN" || $mp->secondary != "NTN" ) {
	print "<br>Newton=(NTN," . $mp->primary . "), (NTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Niles" );
if ( $mp->primary != "NLS" || $mp->secondary != "NLS" ) {
	print "<br>Niles=(NLS," . $mp->primary . "), (NLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Noble" );
if ( $mp->primary != "NPL" || $mp->secondary != "NPL" ) {
	print "<br>Noble=(NPL," . $mp->primary . "), (NPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Noel" );
if ( $mp->primary != "NL" || $mp->secondary != "NL" ) {
	print "<br>Noel=(NL," . $mp->primary . "), (NL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Northend" );
if ( $mp->primary != "NR0N" || $mp->secondary != "NRTN" ) {
	print "<br>Northend=(NR0N," . $mp->primary . "), (NRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Norton" );
if ( $mp->primary != "NRTN" || $mp->secondary != "NRTN" ) {
	print "<br>Norton=(NRTN," . $mp->primary . "), (NRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Nutter" );
if ( $mp->primary != "NTR" || $mp->secondary != "NTR" ) {
	print "<br>Nutter=(NTR," . $mp->primary . "), (NTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Odding" );
if ( $mp->primary != "ATNK" || $mp->secondary != "ATNK" ) {
	print "<br>Odding=(ATNK," . $mp->primary . "), (ATNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Odenbaugh" );
if ( $mp->primary != "ATNP" || $mp->secondary != "ATNP" ) {
	print "<br>Odenbaugh=(ATNP," . $mp->primary . "), (ATNP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ogborn" );
if ( $mp->primary != "AKPR" || $mp->secondary != "AKPR" ) {
	print "<br>Ogborn=(AKPR," . $mp->primary . "), (AKPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Oppenheimer" );
if ( $mp->primary != "APNM" || $mp->secondary != "APNM" ) {
	print "<br>Oppenheimer=(APNM," . $mp->primary . "), (APNM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Otis" );
if ( $mp->primary != "ATS" || $mp->secondary != "ATS" ) {
	print "<br>Otis=(ATS," . $mp->primary . "), (ATS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Oviatt" );
if ( $mp->primary != "AFT" || $mp->secondary != "AFT" ) {
	print "<br>Oviatt=(AFT," . $mp->primary . "), (AFT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "PRUST?" );
if ( $mp->primary != "PRST" || $mp->secondary != "PRST" ) {
	print "<br>PRUST?=(PRST," . $mp->primary . "), (PRST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Paddock" );
if ( $mp->primary != "PTK" || $mp->secondary != "PTK" ) {
	print "<br>Paddock=(PTK," . $mp->primary . "), (PTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Page" );
if ( $mp->primary != "PJ" || $mp->secondary != "PK" ) {
	print "<br>Page=(PJ," . $mp->primary . "), (PK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Paine" );
if ( $mp->primary != "PN" || $mp->secondary != "PN" ) {
	print "<br>Paine=(PN," . $mp->primary . "), (PN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Paist" );
if ( $mp->primary != "PST" || $mp->secondary != "PST" ) {
	print "<br>Paist=(PST," . $mp->primary . "), (PST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Palmer" );
if ( $mp->primary != "PLMR" || $mp->secondary != "PLMR" ) {
	print "<br>Palmer=(PLMR," . $mp->primary . "), (PLMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Park" );
if ( $mp->primary != "PRK" || $mp->secondary != "PRK" ) {
	print "<br>Park=(PRK," . $mp->primary . "), (PRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Parker" );
if ( $mp->primary != "PRKR" || $mp->secondary != "PRKR" ) {
	print "<br>Parker=(PRKR," . $mp->primary . "), (PRKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Parkhurst" );
if ( $mp->primary != "PRKR" || $mp->secondary != "PRKR" ) {
	print "<br>Parkhurst=(PRKR," . $mp->primary . "), (PRKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Parrat" );
if ( $mp->primary != "PRT" || $mp->secondary != "PRT" ) {
	print "<br>Parrat=(PRT," . $mp->primary . "), (PRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Parsons" );
if ( $mp->primary != "PRSN" || $mp->secondary != "PRSN" ) {
	print "<br>Parsons=(PRSN," . $mp->primary . "), (PRSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Partridge" );
if ( $mp->primary != "PRTR" || $mp->secondary != "PRTR" ) {
	print "<br>Partridge=(PRTR," . $mp->primary . "), (PRTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pashley" );
if ( $mp->primary != "PXL" || $mp->secondary != "PXL" ) {
	print "<br>Pashley=(PXL," . $mp->primary . "), (PXL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pasley" );
if ( $mp->primary != "PSL" || $mp->secondary != "PSL" ) {
	print "<br>Pasley=(PSL," . $mp->primary . "), (PSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Patrick" );
if ( $mp->primary != "PTRK" || $mp->secondary != "PTRK" ) {
	print "<br>Patrick=(PTRK," . $mp->primary . "), (PTRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pattee" );
if ( $mp->primary != "PT" || $mp->secondary != "PT" ) {
	print "<br>Pattee=(PT," . $mp->primary . "), (PT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Patten" );
if ( $mp->primary != "PTN" || $mp->secondary != "PTN" ) {
	print "<br>Patten=(PTN," . $mp->primary . "), (PTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pawley" );
if ( $mp->primary != "PL" || $mp->secondary != "PL" ) {
	print "<br>Pawley=(PL," . $mp->primary . "), (PL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Payne" );
if ( $mp->primary != "PN" || $mp->secondary != "PN" ) {
	print "<br>Payne=(PN," . $mp->primary . "), (PN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Peabody" );
if ( $mp->primary != "PPT" || $mp->secondary != "PPT" ) {
	print "<br>Peabody=(PPT," . $mp->primary . "), (PPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Peake" );
if ( $mp->primary != "PK" || $mp->secondary != "PK" ) {
	print "<br>Peake=(PK," . $mp->primary . "), (PK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pearson" );
if ( $mp->primary != "PRSN" || $mp->secondary != "PRSN" ) {
	print "<br>Pearson=(PRSN," . $mp->primary . "), (PRSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Peat" );
if ( $mp->primary != "PT" || $mp->secondary != "PT" ) {
	print "<br>Peat=(PT," . $mp->primary . "), (PT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pedersen" );
if ( $mp->primary != "PTRS" || $mp->secondary != "PTRS" ) {
	print "<br>Pedersen=(PTRS," . $mp->primary . "), (PTRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Percy" );
if ( $mp->primary != "PRS" || $mp->secondary != "PRS" ) {
	print "<br>Percy=(PRS," . $mp->primary . "), (PRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Perkins" );
if ( $mp->primary != "PRKN" || $mp->secondary != "PRKN" ) {
	print "<br>Perkins=(PRKN," . $mp->primary . "), (PRKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Perrine" );
if ( $mp->primary != "PRN" || $mp->secondary != "PRN" ) {
	print "<br>Perrine=(PRN," . $mp->primary . "), (PRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Perry" );
if ( $mp->primary != "PR" || $mp->secondary != "PR" ) {
	print "<br>Perry=(PR," . $mp->primary . "), (PR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Peson" );
if ( $mp->primary != "PSN" || $mp->secondary != "PSN" ) {
	print "<br>Peson=(PSN," . $mp->primary . "), (PSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Peterson" );
if ( $mp->primary != "PTRS" || $mp->secondary != "PTRS" ) {
	print "<br>Peterson=(PTRS," . $mp->primary . "), (PTRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Peyton" );
if ( $mp->primary != "PTN" || $mp->secondary != "PTN" ) {
	print "<br>Peyton=(PTN," . $mp->primary . "), (PTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Phinney" );
if ( $mp->primary != "FN" || $mp->secondary != "FN" ) {
	print "<br>Phinney=(FN," . $mp->primary . "), (FN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pickard" );
if ( $mp->primary != "PKRT" || $mp->secondary != "PKRT" ) {
	print "<br>Pickard=(PKRT," . $mp->primary . "), (PKRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pierce" );
if ( $mp->primary != "PRS" || $mp->secondary != "PRS" ) {
	print "<br>Pierce=(PRS," . $mp->primary . "), (PRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pierrepont" );
if ( $mp->primary != "PRPN" || $mp->secondary != "PRPN" ) {
	print "<br>Pierrepont=(PRPN," . $mp->primary . "), (PRPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pike" );
if ( $mp->primary != "PK" || $mp->secondary != "PK" ) {
	print "<br>Pike=(PK," . $mp->primary . "), (PK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pinkham" );
if ( $mp->primary != "PNKM" || $mp->secondary != "PNKM" ) {
	print "<br>Pinkham=(PNKM," . $mp->primary . "), (PNKM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pitman" );
if ( $mp->primary != "PTMN" || $mp->secondary != "PTMN" ) {
	print "<br>Pitman=(PTMN," . $mp->primary . "), (PTMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pitt" );
if ( $mp->primary != "PT" || $mp->secondary != "PT" ) {
	print "<br>Pitt=(PT," . $mp->primary . "), (PT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pitts" );
if ( $mp->primary != "PTS" || $mp->secondary != "PTS" ) {
	print "<br>Pitts=(PTS," . $mp->primary . "), (PTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Plantagenet" );
if ( $mp->primary != "PLNT" || $mp->secondary != "PLNT" ) {
	print "<br>Plantagenet=(PLNT," . $mp->primary . "), (PLNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Platt" );
if ( $mp->primary != "PLT" || $mp->secondary != "PLT" ) {
	print "<br>Platt=(PLT," . $mp->primary . "), (PLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Platts" );
if ( $mp->primary != "PLTS" || $mp->secondary != "PLTS" ) {
	print "<br>Platts=(PLTS," . $mp->primary . "), (PLTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pleis" );
if ( $mp->primary != "PLS" || $mp->secondary != "PLS" ) {
	print "<br>Pleis=(PLS," . $mp->primary . "), (PLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pleiss" );
if ( $mp->primary != "PLS" || $mp->secondary != "PLS" ) {
	print "<br>Pleiss=(PLS," . $mp->primary . "), (PLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Plisko" );
if ( $mp->primary != "PLSK" || $mp->secondary != "PLSK" ) {
	print "<br>Plisko=(PLSK," . $mp->primary . "), (PLSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pliskovitch" );
if ( $mp->primary != "PLSK" || $mp->secondary != "PLSK" ) {
	print "<br>Pliskovitch=(PLSK," . $mp->primary . "), (PLSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Plum" );
if ( $mp->primary != "PLM" || $mp->secondary != "PLM" ) {
	print "<br>Plum=(PLM," . $mp->primary . "), (PLM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Plume" );
if ( $mp->primary != "PLM" || $mp->secondary != "PLM" ) {
	print "<br>Plume=(PLM," . $mp->primary . "), (PLM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Poitou" );
if ( $mp->primary != "PT" || $mp->secondary != "PT" ) {
	print "<br>Poitou=(PT," . $mp->primary . "), (PT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pomeroy" );
if ( $mp->primary != "PMR" || $mp->secondary != "PMR" ) {
	print "<br>Pomeroy=(PMR," . $mp->primary . "), (PMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Poretiers" );
if ( $mp->primary != "PRTR" || $mp->secondary != "PRTR" ) {
	print "<br>Poretiers=(PRTR," . $mp->primary . "), (PRTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pote" );
if ( $mp->primary != "PT" || $mp->secondary != "PT" ) {
	print "<br>Pote=(PT," . $mp->primary . "), (PT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Potter" );
if ( $mp->primary != "PTR" || $mp->secondary != "PTR" ) {
	print "<br>Potter=(PTR," . $mp->primary . "), (PTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Potts" );
if ( $mp->primary != "PTS" || $mp->secondary != "PTS" ) {
	print "<br>Potts=(PTS," . $mp->primary . "), (PTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Powell" );
if ( $mp->primary != "PL" || $mp->secondary != "PL" ) {
	print "<br>Powell=(PL," . $mp->primary . "), (PL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pratt" );
if ( $mp->primary != "PRT" || $mp->secondary != "PRT" ) {
	print "<br>Pratt=(PRT," . $mp->primary . "), (PRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Presbury" );
if ( $mp->primary != "PRSP" || $mp->secondary != "PRSP" ) {
	print "<br>Presbury=(PRSP," . $mp->primary . "), (PRSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Priest" );
if ( $mp->primary != "PRST" || $mp->secondary != "PRST" ) {
	print "<br>Priest=(PRST," . $mp->primary . "), (PRST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Prindle" );
if ( $mp->primary != "PRNT" || $mp->secondary != "PRNT" ) {
	print "<br>Prindle=(PRNT," . $mp->primary . "), (PRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Prior" );
if ( $mp->primary != "PRR" || $mp->secondary != "PRR" ) {
	print "<br>Prior=(PRR," . $mp->primary . "), (PRR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Profumo" );
if ( $mp->primary != "PRFM" || $mp->secondary != "PRFM" ) {
	print "<br>Profumo=(PRFM," . $mp->primary . "), (PRFM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Purdy" );
if ( $mp->primary != "PRT" || $mp->secondary != "PRT" ) {
	print "<br>Purdy=(PRT," . $mp->primary . "), (PRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Purefoy" );
if ( $mp->primary != "PRF" || $mp->secondary != "PRF" ) {
	print "<br>Purefoy=(PRF," . $mp->primary . "), (PRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pury" );
if ( $mp->primary != "PR" || $mp->secondary != "PR" ) {
	print "<br>Pury=(PR," . $mp->primary . "), (PR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Quinter" );
if ( $mp->primary != "KNTR" || $mp->secondary != "KNTR" ) {
	print "<br>Quinter=(KNTR," . $mp->primary . "), (KNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rachel" );
if ( $mp->primary != "RXL" || $mp->secondary != "RKL" ) {
	print "<br>Rachel=(RXL," . $mp->primary . "), (RKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rand" );
if ( $mp->primary != "RNT" || $mp->secondary != "RNT" ) {
	print "<br>Rand=(RNT," . $mp->primary . "), (RNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rankin" );
if ( $mp->primary != "RNKN" || $mp->secondary != "RNKN" ) {
	print "<br>Rankin=(RNKN," . $mp->primary . "), (RNKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ravenscroft" );
if ( $mp->primary != "RFNS" || $mp->secondary != "RFNS" ) {
	print "<br>Ravenscroft=(RFNS," . $mp->primary . "), (RFNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Raynsford" );
if ( $mp->primary != "RNSF" || $mp->secondary != "RNSF" ) {
	print "<br>Raynsford=(RNSF," . $mp->primary . "), (RNSF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Reakirt" );
if ( $mp->primary != "RKRT" || $mp->secondary != "RKRT" ) {
	print "<br>Reakirt=(RKRT," . $mp->primary . "), (RKRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Reaves" );
if ( $mp->primary != "RFS" || $mp->secondary != "RFS" ) {
	print "<br>Reaves=(RFS," . $mp->primary . "), (RFS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Reeves" );
if ( $mp->primary != "RFS" || $mp->secondary != "RFS" ) {
	print "<br>Reeves=(RFS," . $mp->primary . "), (RFS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Reichert" );
if ( $mp->primary != "RXRT" || $mp->secondary != "RKRT" ) {
	print "<br>Reichert=(RXRT," . $mp->primary . "), (RKRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Remmele" );
if ( $mp->primary != "RML" || $mp->secondary != "RML" ) {
	print "<br>Remmele=(RML," . $mp->primary . "), (RML," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Reynolds" );
if ( $mp->primary != "RNLT" || $mp->secondary != "RNLT" ) {
	print "<br>Reynolds=(RNLT," . $mp->primary . "), (RNLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rhodes" );
if ( $mp->primary != "RTS" || $mp->secondary != "RTS" ) {
	print "<br>Rhodes=(RTS," . $mp->primary . "), (RTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Richards" );
if ( $mp->primary != "RXRT" || $mp->secondary != "RKRT" ) {
	print "<br>Richards=(RXRT," . $mp->primary . "), (RKRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Richardson" );
if ( $mp->primary != "RXRT" || $mp->secondary != "RKRT" ) {
	print "<br>Richardson=(RXRT," . $mp->primary . "), (RKRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ring" );
if ( $mp->primary != "RNK" || $mp->secondary != "RNK" ) {
	print "<br>Ring=(RNK," . $mp->primary . "), (RNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Roberts" );
if ( $mp->primary != "RPRT" || $mp->secondary != "RPRT" ) {
	print "<br>Roberts=(RPRT," . $mp->primary . "), (RPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Robertson" );
if ( $mp->primary != "RPRT" || $mp->secondary != "RPRT" ) {
	print "<br>Robertson=(RPRT," . $mp->primary . "), (RPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Robson" );
if ( $mp->primary != "RPSN" || $mp->secondary != "RPSN" ) {
	print "<br>Robson=(RPSN," . $mp->primary . "), (RPSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rodie" );
if ( $mp->primary != "RT" || $mp->secondary != "RT" ) {
	print "<br>Rodie=(RT," . $mp->primary . "), (RT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rody" );
if ( $mp->primary != "RT" || $mp->secondary != "RT" ) {
	print "<br>Rody=(RT," . $mp->primary . "), (RT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rogers" );
if ( $mp->primary != "RKRS" || $mp->secondary != "RJRS" ) {
	print "<br>Rogers=(RKRS," . $mp->primary . "), (RJRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ross" );
if ( $mp->primary != "RS" || $mp->secondary != "RS" ) {
	print "<br>Ross=(RS," . $mp->primary . "), (RS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rosslevin" );
if ( $mp->primary != "RSLF" || $mp->secondary != "RSLF" ) {
	print "<br>Rosslevin=(RSLF," . $mp->primary . "), (RSLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rowland" );
if ( $mp->primary != "RLNT" || $mp->secondary != "RLNT" ) {
	print "<br>Rowland=(RLNT," . $mp->primary . "), (RLNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ruehl" );
if ( $mp->primary != "RL" || $mp->secondary != "RL" ) {
	print "<br>Ruehl=(RL," . $mp->primary . "), (RL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Russell" );
if ( $mp->primary != "RSL" || $mp->secondary != "RSL" ) {
	print "<br>Russell=(RSL," . $mp->primary . "), (RSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ruth" );
if ( $mp->primary != "R0" || $mp->secondary != "RT" ) {
	print "<br>Ruth=(R0," . $mp->primary . "), (RT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ryan" );
if ( $mp->primary != "RN" || $mp->secondary != "RN" ) {
	print "<br>Ryan=(RN," . $mp->primary . "), (RN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Rysse" );
if ( $mp->primary != "RS" || $mp->secondary != "RS" ) {
	print "<br>Rysse=(RS," . $mp->primary . "), (RS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sadler" );
if ( $mp->primary != "STLR" || $mp->secondary != "STLR" ) {
	print "<br>Sadler=(STLR," . $mp->primary . "), (STLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Salmon" );
if ( $mp->primary != "SLMN" || $mp->secondary != "SLMN" ) {
	print "<br>Salmon=(SLMN," . $mp->primary . "), (SLMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Salter" );
if ( $mp->primary != "SLTR" || $mp->secondary != "SLTR" ) {
	print "<br>Salter=(SLTR," . $mp->primary . "), (SLTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Salvatore" );
if ( $mp->primary != "SLFT" || $mp->secondary != "SLFT" ) {
	print "<br>Salvatore=(SLFT," . $mp->primary . "), (SLFT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sanders" );
if ( $mp->primary != "SNTR" || $mp->secondary != "SNTR" ) {
	print "<br>Sanders=(SNTR," . $mp->primary . "), (SNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sands" );
if ( $mp->primary != "SNTS" || $mp->secondary != "SNTS" ) {
	print "<br>Sands=(SNTS," . $mp->primary . "), (SNTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sanford" );
if ( $mp->primary != "SNFR" || $mp->secondary != "SNFR" ) {
	print "<br>Sanford=(SNFR," . $mp->primary . "), (SNFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sanger" );
if ( $mp->primary != "SNKR" || $mp->secondary != "SNJR" ) {
	print "<br>Sanger=(SNKR," . $mp->primary . "), (SNJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sargent" );
if ( $mp->primary != "SRJN" || $mp->secondary != "SRKN" ) {
	print "<br>Sargent=(SRJN," . $mp->primary . "), (SRKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Saunders" );
if ( $mp->primary != "SNTR" || $mp->secondary != "SNTR" ) {
	print "<br>Saunders=(SNTR," . $mp->primary . "), (SNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Schilling" );
if ( $mp->primary != "XLNK" || $mp->secondary != "XLNK" ) {
	print "<br>Schilling=(XLNK," . $mp->primary . "), (XLNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Schlegel" );
if ( $mp->primary != "XLKL" || $mp->secondary != "SLKL" ) {
	print "<br>Schlegel=(XLKL," . $mp->primary . "), (SLKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Scott" );
if ( $mp->primary != "SKT" || $mp->secondary != "SKT" ) {
	print "<br>Scott=(SKT," . $mp->primary . "), (SKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sears" );
if ( $mp->primary != "SRS" || $mp->secondary != "SRS" ) {
	print "<br>Sears=(SRS," . $mp->primary . "), (SRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Segersall" );
if ( $mp->primary != "SJRS" || $mp->secondary != "SKRS" ) {
	print "<br>Segersall=(SJRS," . $mp->primary . "), (SKRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Senecal" );
if ( $mp->primary != "SNKL" || $mp->secondary != "SNKL" ) {
	print "<br>Senecal=(SNKL," . $mp->primary . "), (SNKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sergeaux" );
if ( $mp->primary != "SRJ" || $mp->secondary != "SRK" ) {
	print "<br>Sergeaux=(SRJ," . $mp->primary . "), (SRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Severance" );
if ( $mp->primary != "SFRN" || $mp->secondary != "SFRN" ) {
	print "<br>Severance=(SFRN," . $mp->primary . "), (SFRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sharp" );
if ( $mp->primary != "XRP" || $mp->secondary != "XRP" ) {
	print "<br>Sharp=(XRP," . $mp->primary . "), (XRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sharpe" );
if ( $mp->primary != "XRP" || $mp->secondary != "XRP" ) {
	print "<br>Sharpe=(XRP," . $mp->primary . "), (XRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sharply" );
if ( $mp->primary != "XRPL" || $mp->secondary != "XRPL" ) {
	print "<br>Sharply=(XRPL," . $mp->primary . "), (XRPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shatswell" );
if ( $mp->primary != "XTSL" || $mp->secondary != "XTSL" ) {
	print "<br>Shatswell=(XTSL," . $mp->primary . "), (XTSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shattack" );
if ( $mp->primary != "XTK" || $mp->secondary != "XTK" ) {
	print "<br>Shattack=(XTK," . $mp->primary . "), (XTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shattock" );
if ( $mp->primary != "XTK" || $mp->secondary != "XTK" ) {
	print "<br>Shattock=(XTK," . $mp->primary . "), (XTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shattuck" );
if ( $mp->primary != "XTK" || $mp->secondary != "XTK" ) {
	print "<br>Shattuck=(XTK," . $mp->primary . "), (XTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shaw" );
if ( $mp->primary != "X" || $mp->secondary != "XF" ) {
	print "<br>Shaw=(X," . $mp->primary . "), (XF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sheldon" );
if ( $mp->primary != "XLTN" || $mp->secondary != "XLTN" ) {
	print "<br>Sheldon=(XLTN," . $mp->primary . "), (XLTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sherman" );
if ( $mp->primary != "XRMN" || $mp->secondary != "XRMN" ) {
	print "<br>Sherman=(XRMN," . $mp->primary . "), (XRMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shinn" );
if ( $mp->primary != "XN" || $mp->secondary != "XN" ) {
	print "<br>Shinn=(XN," . $mp->primary . "), (XN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shirford" );
if ( $mp->primary != "XRFR" || $mp->secondary != "XRFR" ) {
	print "<br>Shirford=(XRFR," . $mp->primary . "), (XRFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shirley" );
if ( $mp->primary != "XRL" || $mp->secondary != "XRL" ) {
	print "<br>Shirley=(XRL," . $mp->primary . "), (XRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shively" );
if ( $mp->primary != "XFL" || $mp->secondary != "XFL" ) {
	print "<br>Shively=(XFL," . $mp->primary . "), (XFL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shoemaker" );
if ( $mp->primary != "XMKR" || $mp->secondary != "XMKR" ) {
	print "<br>Shoemaker=(XMKR," . $mp->primary . "), (XMKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Short" );
if ( $mp->primary != "XRT" || $mp->secondary != "XRT" ) {
	print "<br>Short=(XRT," . $mp->primary . "), (XRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shotwell" );
if ( $mp->primary != "XTL" || $mp->secondary != "XTL" ) {
	print "<br>Shotwell=(XTL," . $mp->primary . "), (XTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Shute" );
if ( $mp->primary != "XT" || $mp->secondary != "XT" ) {
	print "<br>Shute=(XT," . $mp->primary . "), (XT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sibley" );
if ( $mp->primary != "SPL" || $mp->secondary != "SPL" ) {
	print "<br>Sibley=(SPL," . $mp->primary . "), (SPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Silver" );
if ( $mp->primary != "SLFR" || $mp->secondary != "SLFR" ) {
	print "<br>Silver=(SLFR," . $mp->primary . "), (SLFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Simes" );
if ( $mp->primary != "SMS" || $mp->secondary != "SMS" ) {
	print "<br>Simes=(SMS," . $mp->primary . "), (SMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sinken" );
if ( $mp->primary != "SNKN" || $mp->secondary != "SNKN" ) {
	print "<br>Sinken=(SNKN," . $mp->primary . "), (SNKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sinn" );
if ( $mp->primary != "SN" || $mp->secondary != "SN" ) {
	print "<br>Sinn=(SN," . $mp->primary . "), (SN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Skelton" );
if ( $mp->primary != "SKLT" || $mp->secondary != "SKLT" ) {
	print "<br>Skelton=(SKLT," . $mp->primary . "), (SKLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Skiffe" );
if ( $mp->primary != "SKF" || $mp->secondary != "SKF" ) {
	print "<br>Skiffe=(SKF," . $mp->primary . "), (SKF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Skotkonung" );
if ( $mp->primary != "SKTK" || $mp->secondary != "SKTK" ) {
	print "<br>Skotkonung=(SKTK," . $mp->primary . "), (SKTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Slade" );
if ( $mp->primary != "SLT" || $mp->secondary != "XLT" ) {
	print "<br>Slade=(SLT," . $mp->primary . "), (XLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Slye" );
if ( $mp->primary != "SL" || $mp->secondary != "XL" ) {
	print "<br>Slye=(SL," . $mp->primary . "), (XL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Smedley" );
if ( $mp->primary != "SMTL" || $mp->secondary != "XMTL" ) {
	print "<br>Smedley=(SMTL," . $mp->primary . "), (XMTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Smith" );
if ( $mp->primary != "SM0" || $mp->secondary != "XMT" ) {
	print "<br>Smith=(SM0," . $mp->primary . "), (XMT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Snow" );
if ( $mp->primary != "SN" || $mp->secondary != "XNF" ) {
	print "<br>Snow=(SN," . $mp->primary . "), (XNF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Soole" );
if ( $mp->primary != "SL" || $mp->secondary != "SL" ) {
	print "<br>Soole=(SL," . $mp->primary . "), (SL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Soule" );
if ( $mp->primary != "SL" || $mp->secondary != "SL" ) {
	print "<br>Soule=(SL," . $mp->primary . "), (SL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Southworth" );
if ( $mp->primary != "S0R0" || $mp->secondary != "STRT" ) {
	print "<br>Southworth=(S0R0," . $mp->primary . "), (STRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sowles" );
if ( $mp->primary != "SLS" || $mp->secondary != "SLS" ) {
	print "<br>Sowles=(SLS," . $mp->primary . "), (SLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Spalding" );
if ( $mp->primary != "SPLT" || $mp->secondary != "SPLT" ) {
	print "<br>Spalding=(SPLT," . $mp->primary . "), (SPLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Spark" );
if ( $mp->primary != "SPRK" || $mp->secondary != "SPRK" ) {
	print "<br>Spark=(SPRK," . $mp->primary . "), (SPRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Spencer" );
if ( $mp->primary != "SPNS" || $mp->secondary != "SPNS" ) {
	print "<br>Spencer=(SPNS," . $mp->primary . "), (SPNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sperry" );
if ( $mp->primary != "SPR" || $mp->secondary != "SPR" ) {
	print "<br>Sperry=(SPR," . $mp->primary . "), (SPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Spofford" );
if ( $mp->primary != "SPFR" || $mp->secondary != "SPFR" ) {
	print "<br>Spofford=(SPFR," . $mp->primary . "), (SPFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Spooner" );
if ( $mp->primary != "SPNR" || $mp->secondary != "SPNR" ) {
	print "<br>Spooner=(SPNR," . $mp->primary . "), (SPNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sprague" );
if ( $mp->primary != "SPRK" || $mp->secondary != "SPRK" ) {
	print "<br>Sprague=(SPRK," . $mp->primary . "), (SPRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Springer" );
if ( $mp->primary != "SPRN" || $mp->secondary != "SPRN" ) {
	print "<br>Springer=(SPRN," . $mp->primary . "), (SPRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "St. Clair" );
if ( $mp->primary != "STKL" || $mp->secondary != "STKL" ) {
	print "<br>St. Clair=(STKL," . $mp->primary . "), (STKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "St. Claire" );
if ( $mp->primary != "STKL" || $mp->secondary != "STKL" ) {
	print "<br>St. Claire=(STKL," . $mp->primary . "), (STKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "St. Leger" );
if ( $mp->primary != "STLJ" || $mp->secondary != "STLK" ) {
	print "<br>St. Leger=(STLJ," . $mp->primary . "), (STLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "St. Omer" );
if ( $mp->primary != "STMR" || $mp->secondary != "STMR" ) {
	print "<br>St. Omer=(STMR," . $mp->primary . "), (STMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stafferton" );
if ( $mp->primary != "STFR" || $mp->secondary != "STFR" ) {
	print "<br>Stafferton=(STFR," . $mp->primary . "), (STFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stafford" );
if ( $mp->primary != "STFR" || $mp->secondary != "STFR" ) {
	print "<br>Stafford=(STFR," . $mp->primary . "), (STFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stalham" );
if ( $mp->primary != "STLM" || $mp->secondary != "STLM" ) {
	print "<br>Stalham=(STLM," . $mp->primary . "), (STLM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stanford" );
if ( $mp->primary != "STNF" || $mp->secondary != "STNF" ) {
	print "<br>Stanford=(STNF," . $mp->primary . "), (STNF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stanton" );
if ( $mp->primary != "STNT" || $mp->secondary != "STNT" ) {
	print "<br>Stanton=(STNT," . $mp->primary . "), (STNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Star" );
if ( $mp->primary != "STR" || $mp->secondary != "STR" ) {
	print "<br>Star=(STR," . $mp->primary . "), (STR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Starbuck" );
if ( $mp->primary != "STRP" || $mp->secondary != "STRP" ) {
	print "<br>Starbuck=(STRP," . $mp->primary . "), (STRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Starkey" );
if ( $mp->primary != "STRK" || $mp->secondary != "STRK" ) {
	print "<br>Starkey=(STRK," . $mp->primary . "), (STRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Starkweather" );
if ( $mp->primary != "STRK" || $mp->secondary != "STRK" ) {
	print "<br>Starkweather=(STRK," . $mp->primary . "), (STRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stearns" );
if ( $mp->primary != "STRN" || $mp->secondary != "STRN" ) {
	print "<br>Stearns=(STRN," . $mp->primary . "), (STRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stebbins" );
if ( $mp->primary != "STPN" || $mp->secondary != "STPN" ) {
	print "<br>Stebbins=(STPN," . $mp->primary . "), (STPN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Steele" );
if ( $mp->primary != "STL" || $mp->secondary != "STL" ) {
	print "<br>Steele=(STL," . $mp->primary . "), (STL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stephenson" );
if ( $mp->primary != "STFN" || $mp->secondary != "STFN" ) {
	print "<br>Stephenson=(STFN," . $mp->primary . "), (STFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stevens" );
if ( $mp->primary != "STFN" || $mp->secondary != "STFN" ) {
	print "<br>Stevens=(STFN," . $mp->primary . "), (STFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stoddard" );
if ( $mp->primary != "STTR" || $mp->secondary != "STTR" ) {
	print "<br>Stoddard=(STTR," . $mp->primary . "), (STTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stodder" );
if ( $mp->primary != "STTR" || $mp->secondary != "STTR" ) {
	print "<br>Stodder=(STTR," . $mp->primary . "), (STTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stone" );
if ( $mp->primary != "STN" || $mp->secondary != "STN" ) {
	print "<br>Stone=(STN," . $mp->primary . "), (STN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Storey" );
if ( $mp->primary != "STR" || $mp->secondary != "STR" ) {
	print "<br>Storey=(STR," . $mp->primary . "), (STR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Storrada" );
if ( $mp->primary != "STRT" || $mp->secondary != "STRT" ) {
	print "<br>Storrada=(STRT," . $mp->primary . "), (STRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Story" );
if ( $mp->primary != "STR" || $mp->secondary != "STR" ) {
	print "<br>Story=(STR," . $mp->primary . "), (STR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stoughton" );
if ( $mp->primary != "STFT" || $mp->secondary != "STFT" ) {
	print "<br>Stoughton=(STFT," . $mp->primary . "), (STFT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stout" );
if ( $mp->primary != "STT" || $mp->secondary != "STT" ) {
	print "<br>Stout=(STT," . $mp->primary . "), (STT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stow" );
if ( $mp->primary != "ST" || $mp->secondary != "STF" ) {
	print "<br>Stow=(ST," . $mp->primary . "), (STF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Strong" );
if ( $mp->primary != "STRN" || $mp->secondary != "STRN" ) {
	print "<br>Strong=(STRN," . $mp->primary . "), (STRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Strutt" );
if ( $mp->primary != "STRT" || $mp->secondary != "STRT" ) {
	print "<br>Strutt=(STRT," . $mp->primary . "), (STRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stryker" );
if ( $mp->primary != "STRK" || $mp->secondary != "STRK" ) {
	print "<br>Stryker=(STRK," . $mp->primary . "), (STRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Stuckeley" );
if ( $mp->primary != "STKL" || $mp->secondary != "STKL" ) {
	print "<br>Stuckeley=(STKL," . $mp->primary . "), (STKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sturges" );
if ( $mp->primary != "STRJ" || $mp->secondary != "STRK" ) {
	print "<br>Sturges=(STRJ," . $mp->primary . "), (STRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sturgess" );
if ( $mp->primary != "STRJ" || $mp->secondary != "STRK" ) {
	print "<br>Sturgess=(STRJ," . $mp->primary . "), (STRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sturgis" );
if ( $mp->primary != "STRJ" || $mp->secondary != "STRK" ) {
	print "<br>Sturgis=(STRJ," . $mp->primary . "), (STRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Suevain" );
if ( $mp->primary != "SFN" || $mp->secondary != "SFN" ) {
	print "<br>Suevain=(SFN," . $mp->primary . "), (SFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sulyard" );
if ( $mp->primary != "SLRT" || $mp->secondary != "SLRT" ) {
	print "<br>Sulyard=(SLRT," . $mp->primary . "), (SLRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Sutton" );
if ( $mp->primary != "STN" || $mp->secondary != "STN" ) {
	print "<br>Sutton=(STN," . $mp->primary . "), (STN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Swain" );
if ( $mp->primary != "SN" || $mp->secondary != "XN" ) {
	print "<br>Swain=(SN," . $mp->primary . "), (XN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Swayne" );
if ( $mp->primary != "SN" || $mp->secondary != "XN" ) {
	print "<br>Swayne=(SN," . $mp->primary . "), (XN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Swayze" );
if ( $mp->primary != "SS" || $mp->secondary != "XTS" ) {
	print "<br>Swayze=(SS," . $mp->primary . "), (XTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Swift" );
if ( $mp->primary != "SFT" || $mp->secondary != "XFT" ) {
	print "<br>Swift=(SFT," . $mp->primary . "), (XFT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Taber" );
if ( $mp->primary != "TPR" || $mp->secondary != "TPR" ) {
	print "<br>Taber=(TPR," . $mp->primary . "), (TPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Talcott" );
if ( $mp->primary != "TLKT" || $mp->secondary != "TLKT" ) {
	print "<br>Talcott=(TLKT," . $mp->primary . "), (TLKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tarne" );
if ( $mp->primary != "TRN" || $mp->secondary != "TRN" ) {
	print "<br>Tarne=(TRN," . $mp->primary . "), (TRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tatum" );
if ( $mp->primary != "TTM" || $mp->secondary != "TTM" ) {
	print "<br>Tatum=(TTM," . $mp->primary . "), (TTM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Taverner" );
if ( $mp->primary != "TFRN" || $mp->secondary != "TFRN" ) {
	print "<br>Taverner=(TFRN," . $mp->primary . "), (TFRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Taylor" );
if ( $mp->primary != "TLR" || $mp->secondary != "TLR" ) {
	print "<br>Taylor=(TLR," . $mp->primary . "), (TLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tenney" );
if ( $mp->primary != "TN" || $mp->secondary != "TN" ) {
	print "<br>Tenney=(TN," . $mp->primary . "), (TN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Thayer" );
if ( $mp->primary != "0R" || $mp->secondary != "TR" ) {
	print "<br>Thayer=(0R," . $mp->primary . "), (TR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Thember" );
if ( $mp->primary != "0MPR" || $mp->secondary != "TMPR" ) {
	print "<br>Thember=(0MPR," . $mp->primary . "), (TMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Thomas" );
if ( $mp->primary != "TMS" || $mp->secondary != "TMS" ) {
	print "<br>Thomas=(TMS," . $mp->primary . "), (TMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Thompson" );
if ( $mp->primary != "TMPS" || $mp->secondary != "TMPS" ) {
	print "<br>Thompson=(TMPS," . $mp->primary . "), (TMPS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Thorne" );
if ( $mp->primary != "0RN" || $mp->secondary != "TRN" ) {
	print "<br>Thorne=(0RN," . $mp->primary . "), (TRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Thornycraft" );
if ( $mp->primary != "0RNK" || $mp->secondary != "TRNK" ) {
	print "<br>Thornycraft=(0RNK," . $mp->primary . "), (TRNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Threlkeld" );
if ( $mp->primary != "0RLK" || $mp->secondary != "TRLK" ) {
	print "<br>Threlkeld=(0RLK," . $mp->primary . "), (TRLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Throckmorton" );
if ( $mp->primary != "0RKM" || $mp->secondary != "TRKM" ) {
	print "<br>Throckmorton=(0RKM," . $mp->primary . "), (TRKM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Thwaits" );
if ( $mp->primary != "0TS" || $mp->secondary != "TTS" ) {
	print "<br>Thwaits=(0TS," . $mp->primary . "), (TTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tibbetts" );
if ( $mp->primary != "TPTS" || $mp->secondary != "TPTS" ) {
	print "<br>Tibbetts=(TPTS," . $mp->primary . "), (TPTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tidd" );
if ( $mp->primary != "TT" || $mp->secondary != "TT" ) {
	print "<br>Tidd=(TT," . $mp->primary . "), (TT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tierney" );
if ( $mp->primary != "TRN" || $mp->secondary != "TRN" ) {
	print "<br>Tierney=(TRN," . $mp->primary . "), (TRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tilley" );
if ( $mp->primary != "TL" || $mp->secondary != "TL" ) {
	print "<br>Tilley=(TL," . $mp->primary . "), (TL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tillieres" );
if ( $mp->primary != "TLRS" || $mp->secondary != "TLRS" ) {
	print "<br>Tillieres=(TLRS," . $mp->primary . "), (TLRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tilly" );
if ( $mp->primary != "TL" || $mp->secondary != "TL" ) {
	print "<br>Tilly=(TL," . $mp->primary . "), (TL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tisdale" );
if ( $mp->primary != "TSTL" || $mp->secondary != "TSTL" ) {
	print "<br>Tisdale=(TSTL," . $mp->primary . "), (TSTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Titus" );
if ( $mp->primary != "TTS" || $mp->secondary != "TTS" ) {
	print "<br>Titus=(TTS," . $mp->primary . "), (TTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tobey" );
if ( $mp->primary != "TP" || $mp->secondary != "TP" ) {
	print "<br>Tobey=(TP," . $mp->primary . "), (TP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tooker" );
if ( $mp->primary != "TKR" || $mp->secondary != "TKR" ) {
	print "<br>Tooker=(TKR," . $mp->primary . "), (TKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Towle" );
if ( $mp->primary != "TL" || $mp->secondary != "TL" ) {
	print "<br>Towle=(TL," . $mp->primary . "), (TL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Towne" );
if ( $mp->primary != "TN" || $mp->secondary != "TN" ) {
	print "<br>Towne=(TN," . $mp->primary . "), (TN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Townsend" );
if ( $mp->primary != "TNSN" || $mp->secondary != "TNSN" ) {
	print "<br>Townsend=(TNSN," . $mp->primary . "), (TNSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Treadway" );
if ( $mp->primary != "TRT" || $mp->secondary != "TRT" ) {
	print "<br>Treadway=(TRT," . $mp->primary . "), (TRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Trelawney" );
if ( $mp->primary != "TRLN" || $mp->secondary != "TRLN" ) {
	print "<br>Trelawney=(TRLN," . $mp->primary . "), (TRLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Trinder" );
if ( $mp->primary != "TRNT" || $mp->secondary != "TRNT" ) {
	print "<br>Trinder=(TRNT," . $mp->primary . "), (TRNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tripp" );
if ( $mp->primary != "TRP" || $mp->secondary != "TRP" ) {
	print "<br>Tripp=(TRP," . $mp->primary . "), (TRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Trippe" );
if ( $mp->primary != "TRP" || $mp->secondary != "TRP" ) {
	print "<br>Trippe=(TRP," . $mp->primary . "), (TRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Trott" );
if ( $mp->primary != "TRT" || $mp->secondary != "TRT" ) {
	print "<br>Trott=(TRT," . $mp->primary . "), (TRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "True" );
if ( $mp->primary != "TR" || $mp->secondary != "TR" ) {
	print "<br>True=(TR," . $mp->primary . "), (TR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Trussebut" );
if ( $mp->primary != "TRSP" || $mp->secondary != "TRSP" ) {
	print "<br>Trussebut=(TRSP," . $mp->primary . "), (TRSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tucker" );
if ( $mp->primary != "TKR" || $mp->secondary != "TKR" ) {
	print "<br>Tucker=(TKR," . $mp->primary . "), (TKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Turgeon" );
if ( $mp->primary != "TRJN" || $mp->secondary != "TRKN" ) {
	print "<br>Turgeon=(TRJN," . $mp->primary . "), (TRKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Turner" );
if ( $mp->primary != "TRNR" || $mp->secondary != "TRNR" ) {
	print "<br>Turner=(TRNR," . $mp->primary . "), (TRNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tuttle" );
if ( $mp->primary != "TTL" || $mp->secondary != "TTL" ) {
	print "<br>Tuttle=(TTL," . $mp->primary . "), (TTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tyler" );
if ( $mp->primary != "TLR" || $mp->secondary != "TLR" ) {
	print "<br>Tyler=(TLR," . $mp->primary . "), (TLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tylle" );
if ( $mp->primary != "TL" || $mp->secondary != "TL" ) {
	print "<br>Tylle=(TL," . $mp->primary . "), (TL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Tyrrel" );
if ( $mp->primary != "TRL" || $mp->secondary != "TRL" ) {
	print "<br>Tyrrel=(TRL," . $mp->primary . "), (TRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ua Tuathail" );
if ( $mp->primary != "AT0L" || $mp->secondary != "ATTL" ) {
	print "<br>Ua Tuathail=(AT0L," . $mp->primary . "), (ATTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ulrich" );
if ( $mp->primary != "ALRX" || $mp->secondary != "ALRK" ) {
	print "<br>Ulrich=(ALRX," . $mp->primary . "), (ALRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Underhill" );
if ( $mp->primary != "ANTR" || $mp->secondary != "ANTR" ) {
	print "<br>Underhill=(ANTR," . $mp->primary . "), (ANTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Underwood" );
if ( $mp->primary != "ANTR" || $mp->secondary != "ANTR" ) {
	print "<br>Underwood=(ANTR," . $mp->primary . "), (ANTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Unknown" );
if ( $mp->primary != "ANKN" || $mp->secondary != "ANKN" ) {
	print "<br>Unknown=(ANKN," . $mp->primary . "), (ANKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Valentine" );
if ( $mp->primary != "FLNT" || $mp->secondary != "FLNT" ) {
	print "<br>Valentine=(FLNT," . $mp->primary . "), (FLNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Van Egmond" );
if ( $mp->primary != "FNKM" || $mp->secondary != "FNKM" ) {
	print "<br>Van Egmond=(FNKM," . $mp->primary . "), (FNKM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Van der Beek" );
if ( $mp->primary != "FNTR" || $mp->secondary != "FNTR" ) {
	print "<br>Van der Beek=(FNTR," . $mp->primary . "), (FNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Vaughan" );
if ( $mp->primary != "FKN" || $mp->secondary != "FKN" ) {
	print "<br>Vaughan=(FKN," . $mp->primary . "), (FKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Vermenlen" );
if ( $mp->primary != "FRMN" || $mp->secondary != "FRMN" ) {
	print "<br>Vermenlen=(FRMN," . $mp->primary . "), (FRMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Vincent" );
if ( $mp->primary != "FNSN" || $mp->secondary != "FNSN" ) {
	print "<br>Vincent=(FNSN," . $mp->primary . "), (FNSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Volentine" );
if ( $mp->primary != "FLNT" || $mp->secondary != "FLNT" ) {
	print "<br>Volentine=(FLNT," . $mp->primary . "), (FLNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wagner" );
if ( $mp->primary != "AKNR" || $mp->secondary != "FKNR" ) {
	print "<br>Wagner=(AKNR," . $mp->primary . "), (FKNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Waite" );
if ( $mp->primary != "AT" || $mp->secondary != "FT" ) {
	print "<br>Waite=(AT," . $mp->primary . "), (FT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Walker" );
if ( $mp->primary != "ALKR" || $mp->secondary != "FLKR" ) {
	print "<br>Walker=(ALKR," . $mp->primary . "), (FLKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Walter" );
if ( $mp->primary != "ALTR" || $mp->secondary != "FLTR" ) {
	print "<br>Walter=(ALTR," . $mp->primary . "), (FLTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wandell" );
if ( $mp->primary != "ANTL" || $mp->secondary != "FNTL" ) {
	print "<br>Wandell=(ANTL," . $mp->primary . "), (FNTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wandesford" );
if ( $mp->primary != "ANTS" || $mp->secondary != "FNTS" ) {
	print "<br>Wandesford=(ANTS," . $mp->primary . "), (FNTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Warbleton" );
if ( $mp->primary != "ARPL" || $mp->secondary != "FRPL" ) {
	print "<br>Warbleton=(ARPL," . $mp->primary . "), (FRPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ward" );
if ( $mp->primary != "ART" || $mp->secondary != "FRT" ) {
	print "<br>Ward=(ART," . $mp->primary . "), (FRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Warde" );
if ( $mp->primary != "ART" || $mp->secondary != "FRT" ) {
	print "<br>Warde=(ART," . $mp->primary . "), (FRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Ware" );
if ( $mp->primary != "AR" || $mp->secondary != "FR" ) {
	print "<br>Ware=(AR," . $mp->primary . "), (FR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wareham" );
if ( $mp->primary != "ARHM" || $mp->secondary != "FRHM" ) {
	print "<br>Wareham=(ARHM," . $mp->primary . "), (FRHM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Warner" );
if ( $mp->primary != "ARNR" || $mp->secondary != "FRNR" ) {
	print "<br>Warner=(ARNR," . $mp->primary . "), (FRNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Warren" );
if ( $mp->primary != "ARN" || $mp->secondary != "FRN" ) {
	print "<br>Warren=(ARN," . $mp->primary . "), (FRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Washburne" );
if ( $mp->primary != "AXPR" || $mp->secondary != "FXPR" ) {
	print "<br>Washburne=(AXPR," . $mp->primary . "), (FXPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Waterbury" );
if ( $mp->primary != "ATRP" || $mp->secondary != "FTRP" ) {
	print "<br>Waterbury=(ATRP," . $mp->primary . "), (FTRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Watson" );
if ( $mp->primary != "ATSN" || $mp->secondary != "FTSN" ) {
	print "<br>Watson=(ATSN," . $mp->primary . "), (FTSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "WatsonEllithorpe" );
if ( $mp->primary != "ATSN" || $mp->secondary != "FTSN" ) {
	print "<br>WatsonEllithorpe=(ATSN," . $mp->primary . "), (FTSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Watts" );
if ( $mp->primary != "ATS" || $mp->secondary != "FTS" ) {
	print "<br>Watts=(ATS," . $mp->primary . "), (FTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wayne" );
if ( $mp->primary != "AN" || $mp->secondary != "FN" ) {
	print "<br>Wayne=(AN," . $mp->primary . "), (FN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Webb" );
if ( $mp->primary != "AP" || $mp->secondary != "FP" ) {
	print "<br>Webb=(AP," . $mp->primary . "), (FP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Weber" );
if ( $mp->primary != "APR" || $mp->secondary != "FPR" ) {
	print "<br>Weber=(APR," . $mp->primary . "), (FPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Webster" );
if ( $mp->primary != "APST" || $mp->secondary != "FPST" ) {
	print "<br>Webster=(APST," . $mp->primary . "), (FPST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Weed" );
if ( $mp->primary != "AT" || $mp->secondary != "FT" ) {
	print "<br>Weed=(AT," . $mp->primary . "), (FT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Weeks" );
if ( $mp->primary != "AKS" || $mp->secondary != "FKS" ) {
	print "<br>Weeks=(AKS," . $mp->primary . "), (FKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wells" );
if ( $mp->primary != "ALS" || $mp->secondary != "FLS" ) {
	print "<br>Wells=(ALS," . $mp->primary . "), (FLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wenzell" );
if ( $mp->primary != "ANSL" || $mp->secondary != "FNTS" ) {
	print "<br>Wenzell=(ANSL," . $mp->primary . "), (FNTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "West" );
if ( $mp->primary != "AST" || $mp->secondary != "FST" ) {
	print "<br>West=(AST," . $mp->primary . "), (FST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Westbury" );
if ( $mp->primary != "ASTP" || $mp->secondary != "FSTP" ) {
	print "<br>Westbury=(ASTP," . $mp->primary . "), (FSTP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Whatlocke" );
if ( $mp->primary != "ATLK" || $mp->secondary != "ATLK" ) {
	print "<br>Whatlocke=(ATLK," . $mp->primary . "), (ATLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wheeler" );
if ( $mp->primary != "ALR" || $mp->secondary != "ALR" ) {
	print "<br>Wheeler=(ALR," . $mp->primary . "), (ALR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Whiston" );
if ( $mp->primary != "ASTN" || $mp->secondary != "ASTN" ) {
	print "<br>Whiston=(ASTN," . $mp->primary . "), (ASTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "White" );
if ( $mp->primary != "AT" || $mp->secondary != "AT" ) {
	print "<br>White=(AT," . $mp->primary . "), (AT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Whitman" );
if ( $mp->primary != "ATMN" || $mp->secondary != "ATMN" ) {
	print "<br>Whitman=(ATMN," . $mp->primary . "), (ATMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Whiton" );
if ( $mp->primary != "ATN" || $mp->secondary != "ATN" ) {
	print "<br>Whiton=(ATN," . $mp->primary . "), (ATN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Whitson" );
if ( $mp->primary != "ATSN" || $mp->secondary != "ATSN" ) {
	print "<br>Whitson=(ATSN," . $mp->primary . "), (ATSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wickes" );
if ( $mp->primary != "AKS" || $mp->secondary != "FKS" ) {
	print "<br>Wickes=(AKS," . $mp->primary . "), (FKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wilbur" );
if ( $mp->primary != "ALPR" || $mp->secondary != "FLPR" ) {
	print "<br>Wilbur=(ALPR," . $mp->primary . "), (FLPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wilcotes" );
if ( $mp->primary != "ALKT" || $mp->secondary != "FLKT" ) {
	print "<br>Wilcotes=(ALKT," . $mp->primary . "), (FLKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wilkinson" );
if ( $mp->primary != "ALKN" || $mp->secondary != "FLKN" ) {
	print "<br>Wilkinson=(ALKN," . $mp->primary . "), (FLKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Willets" );
if ( $mp->primary != "ALTS" || $mp->secondary != "FLTS" ) {
	print "<br>Willets=(ALTS," . $mp->primary . "), (FLTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Willett" );
if ( $mp->primary != "ALT" || $mp->secondary != "FLT" ) {
	print "<br>Willett=(ALT," . $mp->primary . "), (FLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Willey" );
if ( $mp->primary != "AL" || $mp->secondary != "FL" ) {
	print "<br>Willey=(AL," . $mp->primary . "), (FL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Williams" );
if ( $mp->primary != "ALMS" || $mp->secondary != "FLMS" ) {
	print "<br>Williams=(ALMS," . $mp->primary . "), (FLMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Williston" );
if ( $mp->primary != "ALST" || $mp->secondary != "FLST" ) {
	print "<br>Williston=(ALST," . $mp->primary . "), (FLST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wilson" );
if ( $mp->primary != "ALSN" || $mp->secondary != "FLSN" ) {
	print "<br>Wilson=(ALSN," . $mp->primary . "), (FLSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wimes" );
if ( $mp->primary != "AMS" || $mp->secondary != "FMS" ) {
	print "<br>Wimes=(AMS," . $mp->primary . "), (FMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Winch" );
if ( $mp->primary != "ANX" || $mp->secondary != "FNK" ) {
	print "<br>Winch=(ANX," . $mp->primary . "), (FNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Winegar" );
if ( $mp->primary != "ANKR" || $mp->secondary != "FNKR" ) {
	print "<br>Winegar=(ANKR," . $mp->primary . "), (FNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wing" );
if ( $mp->primary != "ANK" || $mp->secondary != "FNK" ) {
	print "<br>Wing=(ANK," . $mp->primary . "), (FNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Winsley" );
if ( $mp->primary != "ANSL" || $mp->secondary != "FNSL" ) {
	print "<br>Winsley=(ANSL," . $mp->primary . "), (FNSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Winslow" );
if ( $mp->primary != "ANSL" || $mp->secondary != "FNSL" ) {
	print "<br>Winslow=(ANSL," . $mp->primary . "), (FNSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Winthrop" );
if ( $mp->primary != "AN0R" || $mp->secondary != "FNTR" ) {
	print "<br>Winthrop=(AN0R," . $mp->primary . "), (FNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wise" );
if ( $mp->primary != "AS" || $mp->secondary != "FS" ) {
	print "<br>Wise=(AS," . $mp->primary . "), (FS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wood" );
if ( $mp->primary != "AT" || $mp->secondary != "FT" ) {
	print "<br>Wood=(AT," . $mp->primary . "), (FT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Woodbridge" );
if ( $mp->primary != "ATPR" || $mp->secondary != "FTPR" ) {
	print "<br>Woodbridge=(ATPR," . $mp->primary . "), (FTPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Woodward" );
if ( $mp->primary != "ATRT" || $mp->secondary != "FTRT" ) {
	print "<br>Woodward=(ATRT," . $mp->primary . "), (FTRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wooley" );
if ( $mp->primary != "AL" || $mp->secondary != "FL" ) {
	print "<br>Wooley=(AL," . $mp->primary . "), (FL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Woolley" );
if ( $mp->primary != "AL" || $mp->secondary != "FL" ) {
	print "<br>Woolley=(AL," . $mp->primary . "), (FL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Worth" );
if ( $mp->primary != "AR0" || $mp->secondary != "FRT" ) {
	print "<br>Worth=(AR0," . $mp->primary . "), (FRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Worthen" );
if ( $mp->primary != "AR0N" || $mp->secondary != "FRTN" ) {
	print "<br>Worthen=(AR0N," . $mp->primary . "), (FRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Worthley" );
if ( $mp->primary != "AR0L" || $mp->secondary != "FRTL" ) {
	print "<br>Worthley=(AR0L," . $mp->primary . "), (FRTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wright" );
if ( $mp->primary != "RT" || $mp->secondary != "RT" ) {
	print "<br>Wright=(RT," . $mp->primary . "), (RT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wyer" );
if ( $mp->primary != "AR" || $mp->secondary != "FR" ) {
	print "<br>Wyer=(AR," . $mp->primary . "), (FR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wyere" );
if ( $mp->primary != "AR" || $mp->secondary != "FR" ) {
	print "<br>Wyere=(AR," . $mp->primary . "), (FR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wynkoop" );
if ( $mp->primary != "ANKP" || $mp->secondary != "FNKP" ) {
	print "<br>Wynkoop=(ANKP," . $mp->primary . "), (FNKP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Yarnall" );
if ( $mp->primary != "ARNL" || $mp->secondary != "ARNL" ) {
	print "<br>Yarnall=(ARNL," . $mp->primary . "), (ARNL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Yeoman" );
if ( $mp->primary != "AMN" || $mp->secondary != "AMN" ) {
	print "<br>Yeoman=(AMN," . $mp->primary . "), (AMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Yorke" );
if ( $mp->primary != "ARK" || $mp->secondary != "ARK" ) {
	print "<br>Yorke=(ARK," . $mp->primary . "), (ARK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Young" );
if ( $mp->primary != "ANK" || $mp->secondary != "ANK" ) {
	print "<br>Young=(ANK," . $mp->primary . "), (ANK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ab Wennonwen" );
if ( $mp->primary != "APNN" || $mp->secondary != "APNN" ) {
	print "<br>ab Wennonwen=(APNN," . $mp->primary . "), (APNN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ap Llewellyn" );
if ( $mp->primary != "APLL" || $mp->secondary != "APLL" ) {
	print "<br>ap Llewellyn=(APLL," . $mp->primary . "), (APLL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ap Lorwerth" );
if ( $mp->primary != "APLR" || $mp->secondary != "APLR" ) {
	print "<br>ap Lorwerth=(APLR," . $mp->primary . "), (APLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "d'Angouleme" );
if ( $mp->primary != "TNKL" || $mp->secondary != "TNKL" ) {
	print "<br>d'Angouleme=(TNKL," . $mp->primary . "), (TNKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Audeham" );
if ( $mp->primary != "TTHM" || $mp->secondary != "TTHM" ) {
	print "<br>de Audeham=(TTHM," . $mp->primary . "), (TTHM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Bavant" );
if ( $mp->primary != "TPFN" || $mp->secondary != "TPFN" ) {
	print "<br>de Bavant=(TPFN," . $mp->primary . "), (TPFN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Beauchamp" );
if ( $mp->primary != "TPXM" || $mp->secondary != "TPKM" ) {
	print "<br>de Beauchamp=(TPXM," . $mp->primary . "), (TPKM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Beaumont" );
if ( $mp->primary != "TPMN" || $mp->secondary != "TPMN" ) {
	print "<br>de Beaumont=(TPMN," . $mp->primary . "), (TPMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Bolbec" );
if ( $mp->primary != "TPLP" || $mp->secondary != "TPLP" ) {
	print "<br>de Bolbec=(TPLP," . $mp->primary . "), (TPLP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Braiose" );
if ( $mp->primary != "TPRS" || $mp->secondary != "TPRS" ) {
	print "<br>de Braiose=(TPRS," . $mp->primary . "), (TPRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Braose" );
if ( $mp->primary != "TPRS" || $mp->secondary != "TPRS" ) {
	print "<br>de Braose=(TPRS," . $mp->primary . "), (TPRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Briwere" );
if ( $mp->primary != "TPRR" || $mp->secondary != "TPRR" ) {
	print "<br>de Briwere=(TPRR," . $mp->primary . "), (TPRR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Cantelou" );
if ( $mp->primary != "TKNT" || $mp->secondary != "TKNT" ) {
	print "<br>de Cantelou=(TKNT," . $mp->primary . "), (TKNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Cherelton" );
if ( $mp->primary != "TXRL" || $mp->secondary != "TKRL" ) {
	print "<br>de Cherelton=(TXRL," . $mp->primary . "), (TKRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Cherleton" );
if ( $mp->primary != "TXRL" || $mp->secondary != "TKRL" ) {
	print "<br>de Cherleton=(TXRL," . $mp->primary . "), (TKRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Clare" );
if ( $mp->primary != "TKLR" || $mp->secondary != "TKLR" ) {
	print "<br>de Clare=(TKLR," . $mp->primary . "), (TKLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Claremont" );
if ( $mp->primary != "TKLR" || $mp->secondary != "TKLR" ) {
	print "<br>de Claremont=(TKLR," . $mp->primary . "), (TKLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Clifford" );
if ( $mp->primary != "TKLF" || $mp->secondary != "TKLF" ) {
	print "<br>de Clifford=(TKLF," . $mp->primary . "), (TKLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Colville" );
if ( $mp->primary != "TKLF" || $mp->secondary != "TKLF" ) {
	print "<br>de Colville=(TKLF," . $mp->primary . "), (TKLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Courtenay" );
if ( $mp->primary != "TKRT" || $mp->secondary != "TKRT" ) {
	print "<br>de Courtenay=(TKRT," . $mp->primary . "), (TKRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Fauconberg" );
if ( $mp->primary != "TFKN" || $mp->secondary != "TFKN" ) {
	print "<br>de Fauconberg=(TFKN," . $mp->primary . "), (TFKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Forest" );
if ( $mp->primary != "TFRS" || $mp->secondary != "TFRS" ) {
	print "<br>de Forest=(TFRS," . $mp->primary . "), (TFRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Gai" );
if ( $mp->primary != "TK" || $mp->secondary != "TK" ) {
	print "<br>de Gai=(TK," . $mp->primary . "), (TK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Grey" );
if ( $mp->primary != "TKR" || $mp->secondary != "TKR" ) {
	print "<br>de Grey=(TKR," . $mp->primary . "), (TKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Guernons" );
if ( $mp->primary != "TKRN" || $mp->secondary != "TKRN" ) {
	print "<br>de Guernons=(TKRN," . $mp->primary . "), (TKRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Haia" );
if ( $mp->primary != "T" || $mp->secondary != "T" ) {
	print "<br>de Haia=(T," . $mp->primary . "), (T," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Harcourt" );
if ( $mp->primary != "TRKR" || $mp->secondary != "TRKR" ) {
	print "<br>de Harcourt=(TRKR," . $mp->primary . "), (TRKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Hastings" );
if ( $mp->primary != "TSTN" || $mp->secondary != "TSTN" ) {
	print "<br>de Hastings=(TSTN," . $mp->primary . "), (TSTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Hoke" );
if ( $mp->primary != "TK" || $mp->secondary != "TK" ) {
	print "<br>de Hoke=(TK," . $mp->primary . "), (TK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Hooch" );
if ( $mp->primary != "TK" || $mp->secondary != "TK" ) {
	print "<br>de Hooch=(TK," . $mp->primary . "), (TK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Hugelville" );
if ( $mp->primary != "TJLF" || $mp->secondary != "TKLF" ) {
	print "<br>de Hugelville=(TJLF," . $mp->primary . "), (TKLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Huntingdon" );
if ( $mp->primary != "TNTN" || $mp->secondary != "TNTN" ) {
	print "<br>de Huntingdon=(TNTN," . $mp->primary . "), (TNTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Insula" );
if ( $mp->primary != "TNSL" || $mp->secondary != "TNSL" ) {
	print "<br>de Insula=(TNSL," . $mp->primary . "), (TNSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Keynes" );
if ( $mp->primary != "TKNS" || $mp->secondary != "TKNS" ) {
	print "<br>de Keynes=(TKNS," . $mp->primary . "), (TKNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Lacy" );
if ( $mp->primary != "TLS" || $mp->secondary != "TLS" ) {
	print "<br>de Lacy=(TLS," . $mp->primary . "), (TLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Lexington" );
if ( $mp->primary != "TLKS" || $mp->secondary != "TLKS" ) {
	print "<br>de Lexington=(TLKS," . $mp->primary . "), (TLKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Lusignan" );
if ( $mp->primary != "TLSN" || $mp->secondary != "TLSK" ) {
	print "<br>de Lusignan=(TLSN," . $mp->primary . "), (TLSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Manvers" );
if ( $mp->primary != "TMNF" || $mp->secondary != "TMNF" ) {
	print "<br>de Manvers=(TMNF," . $mp->primary . "), (TMNF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Montagu" );
if ( $mp->primary != "TMNT" || $mp->secondary != "TMNT" ) {
	print "<br>de Montagu=(TMNT," . $mp->primary . "), (TMNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Montault" );
if ( $mp->primary != "TMNT" || $mp->secondary != "TMNT" ) {
	print "<br>de Montault=(TMNT," . $mp->primary . "), (TMNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Montfort" );
if ( $mp->primary != "TMNT" || $mp->secondary != "TMNT" ) {
	print "<br>de Montfort=(TMNT," . $mp->primary . "), (TMNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Mortimer" );
if ( $mp->primary != "TMRT" || $mp->secondary != "TMRT" ) {
	print "<br>de Mortimer=(TMRT," . $mp->primary . "), (TMRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Morville" );
if ( $mp->primary != "TMRF" || $mp->secondary != "TMRF" ) {
	print "<br>de Morville=(TMRF," . $mp->primary . "), (TMRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Morvois" );
if ( $mp->primary != "TMRF" || $mp->secondary != "TMRF" ) {
	print "<br>de Morvois=(TMRF," . $mp->primary . "), (TMRF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Neufmarche" );
if ( $mp->primary != "TNFM" || $mp->secondary != "TNFM" ) {
	print "<br>de Neufmarche=(TNFM," . $mp->primary . "), (TNFM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Odingsells" );
if ( $mp->primary != "TTNK" || $mp->secondary != "TTNK" ) {
	print "<br>de Odingsells=(TTNK," . $mp->primary . "), (TTNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Odyngsells" );
if ( $mp->primary != "TTNK" || $mp->secondary != "TTNK" ) {
	print "<br>de Odyngsells=(TTNK," . $mp->primary . "), (TTNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Percy" );
if ( $mp->primary != "TPRS" || $mp->secondary != "TPRS" ) {
	print "<br>de Percy=(TPRS," . $mp->primary . "), (TPRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Pierrepont" );
if ( $mp->primary != "TPRP" || $mp->secondary != "TPRP" ) {
	print "<br>de Pierrepont=(TPRP," . $mp->primary . "), (TPRP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Plessetis" );
if ( $mp->primary != "TPLS" || $mp->secondary != "TPLS" ) {
	print "<br>de Plessetis=(TPLS," . $mp->primary . "), (TPLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Porhoet" );
if ( $mp->primary != "TPRT" || $mp->secondary != "TPRT" ) {
	print "<br>de Porhoet=(TPRT," . $mp->primary . "), (TPRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Prouz" );
if ( $mp->primary != "TPRS" || $mp->secondary != "TPRS" ) {
	print "<br>de Prouz=(TPRS," . $mp->primary . "), (TPRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Quincy" );
if ( $mp->primary != "TKNS" || $mp->secondary != "TKNS" ) {
	print "<br>de Quincy=(TKNS," . $mp->primary . "), (TKNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Ripellis" );
if ( $mp->primary != "TRPL" || $mp->secondary != "TRPL" ) {
	print "<br>de Ripellis=(TRPL," . $mp->primary . "), (TRPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Ros" );
if ( $mp->primary != "TRS" || $mp->secondary != "TRS" ) {
	print "<br>de Ros=(TRS," . $mp->primary . "), (TRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Salisbury" );
if ( $mp->primary != "TSLS" || $mp->secondary != "TSLS" ) {
	print "<br>de Salisbury=(TSLS," . $mp->primary . "), (TSLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Sanford" );
if ( $mp->primary != "TSNF" || $mp->secondary != "TSNF" ) {
	print "<br>de Sanford=(TSNF," . $mp->primary . "), (TSNF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Somery" );
if ( $mp->primary != "TSMR" || $mp->secondary != "TSMR" ) {
	print "<br>de Somery=(TSMR," . $mp->primary . "), (TSMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de St. Hilary" );
if ( $mp->primary != "TSTL" || $mp->secondary != "TSTL" ) {
	print "<br>de St. Hilary=(TSTL," . $mp->primary . "), (TSTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de St. Liz" );
if ( $mp->primary != "TSTL" || $mp->secondary != "TSTL" ) {
	print "<br>de St. Liz=(TSTL," . $mp->primary . "), (TSTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Sutton" );
if ( $mp->primary != "TSTN" || $mp->secondary != "TSTN" ) {
	print "<br>de Sutton=(TSTN," . $mp->primary . "), (TSTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Toeni" );
if ( $mp->primary != "TTN" || $mp->secondary != "TTN" ) {
	print "<br>de Toeni=(TTN," . $mp->primary . "), (TTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Tony" );
if ( $mp->primary != "TTN" || $mp->secondary != "TTN" ) {
	print "<br>de Tony=(TTN," . $mp->primary . "), (TTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Umfreville" );
if ( $mp->primary != "TMFR" || $mp->secondary != "TMFR" ) {
	print "<br>de Umfreville=(TMFR," . $mp->primary . "), (TMFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Valognes" );
if ( $mp->primary != "TFLN" || $mp->secondary != "TFLK" ) {
	print "<br>de Valognes=(TFLN," . $mp->primary . "), (TFLK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Vaux" );
if ( $mp->primary != "TF" || $mp->secondary != "TF" ) {
	print "<br>de Vaux=(TF," . $mp->primary . "), (TF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Vere" );
if ( $mp->primary != "TFR" || $mp->secondary != "TFR" ) {
	print "<br>de Vere=(TFR," . $mp->primary . "), (TFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Vermandois" );
if ( $mp->primary != "TFRM" || $mp->secondary != "TFRM" ) {
	print "<br>de Vermandois=(TFRM," . $mp->primary . "), (TFRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Vernon" );
if ( $mp->primary != "TFRN" || $mp->secondary != "TFRN" ) {
	print "<br>de Vernon=(TFRN," . $mp->primary . "), (TFRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Vexin" );
if ( $mp->primary != "TFKS" || $mp->secondary != "TFKS" ) {
	print "<br>de Vexin=(TFKS," . $mp->primary . "), (TFKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Vitre" );
if ( $mp->primary != "TFTR" || $mp->secondary != "TFTR" ) {
	print "<br>de Vitre=(TFTR," . $mp->primary . "), (TFTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Wandesford" );
if ( $mp->primary != "TNTS" || $mp->secondary != "TNTS" ) {
	print "<br>de Wandesford=(TNTS," . $mp->primary . "), (TNTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Warenne" );
if ( $mp->primary != "TRN" || $mp->secondary != "TRN" ) {
	print "<br>de Warenne=(TRN," . $mp->primary . "), (TRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "de Westbury" );
if ( $mp->primary != "TSTP" || $mp->secondary != "TSTP" ) {
	print "<br>de Westbury=(TSTP," . $mp->primary . "), (TSTP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "di Saluzzo" );
if ( $mp->primary != "TSLS" || $mp->secondary != "TSLT" ) {
	print "<br>di Saluzzo=(TSLS," . $mp->primary . "), (TSLT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Alan" );
if ( $mp->primary != "FTSL" || $mp->secondary != "FTSL" ) {
	print "<br>fitz Alan=(FTSL," . $mp->primary . "), (FTSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Geoffrey" );
if ( $mp->primary != "FTSJ" || $mp->secondary != "FTSK" ) {
	print "<br>fitz Geoffrey=(FTSJ," . $mp->primary . "), (FTSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Herbert" );
if ( $mp->primary != "FTSR" || $mp->secondary != "FTSR" ) {
	print "<br>fitz Herbert=(FTSR," . $mp->primary . "), (FTSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz John" );
if ( $mp->primary != "FTSJ" || $mp->secondary != "FTSJ" ) {
	print "<br>fitz John=(FTSJ," . $mp->primary . "), (FTSJ," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Patrick" );
if ( $mp->primary != "FTSP" || $mp->secondary != "FTSP" ) {
	print "<br>fitz Patrick=(FTSP," . $mp->primary . "), (FTSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Payn" );
if ( $mp->primary != "FTSP" || $mp->secondary != "FTSP" ) {
	print "<br>fitz Payn=(FTSP," . $mp->primary . "), (FTSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Piers" );
if ( $mp->primary != "FTSP" || $mp->secondary != "FTSP" ) {
	print "<br>fitz Piers=(FTSP," . $mp->primary . "), (FTSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Randolph" );
if ( $mp->primary != "FTSR" || $mp->secondary != "FTSR" ) {
	print "<br>fitz Randolph=(FTSR," . $mp->primary . "), (FTSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Richard" );
if ( $mp->primary != "FTSR" || $mp->secondary != "FTSR" ) {
	print "<br>fitz Richard=(FTSR," . $mp->primary . "), (FTSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Robert" );
if ( $mp->primary != "FTSR" || $mp->secondary != "FTSR" ) {
	print "<br>fitz Robert=(FTSR," . $mp->primary . "), (FTSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Roy" );
if ( $mp->primary != "FTSR" || $mp->secondary != "FTSR" ) {
	print "<br>fitz Roy=(FTSR," . $mp->primary . "), (FTSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Scrob" );
if ( $mp->primary != "FTSS" || $mp->secondary != "FTSS" ) {
	print "<br>fitz Scrob=(FTSS," . $mp->primary . "), (FTSS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Walter" );
if ( $mp->primary != "FTSL" || $mp->secondary != "FTSL" ) {
	print "<br>fitz Walter=(FTSL," . $mp->primary . "), (FTSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Warin" );
if ( $mp->primary != "FTSR" || $mp->secondary != "FTSR" ) {
	print "<br>fitz Warin=(FTSR," . $mp->primary . "), (FTSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "fitz Williams" );
if ( $mp->primary != "FTSL" || $mp->secondary != "FTSL" ) {
	print "<br>fitz Williams=(FTSL," . $mp->primary . "), (FTSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "la Zouche" );
if ( $mp->primary != "LSX" || $mp->secondary != "LSK" ) {
	print "<br>la Zouche=(LSX," . $mp->primary . "), (LSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "le Botiller" );
if ( $mp->primary != "LPTL" || $mp->secondary != "LPTL" ) {
	print "<br>le Botiller=(LPTL," . $mp->primary . "), (LPTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "le Despenser" );
if ( $mp->primary != "LTSP" || $mp->secondary != "LTSP" ) {
	print "<br>le Despenser=(LTSP," . $mp->primary . "), (LTSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "le deSpencer" );
if ( $mp->primary != "LTSP" || $mp->secondary != "LTSP" ) {
	print "<br>le deSpencer=(LTSP," . $mp->primary . "), (LTSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Allendale" );
if ( $mp->primary != "AFLN" || $mp->secondary != "AFLN" ) {
	print "<br>of Allendale=(AFLN," . $mp->primary . "), (AFLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Angouleme" );
if ( $mp->primary != "AFNK" || $mp->secondary != "AFNK" ) {
	print "<br>of Angouleme=(AFNK," . $mp->primary . "), (AFNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Anjou" );
if ( $mp->primary != "AFNJ" || $mp->secondary != "AFNJ" ) {
	print "<br>of Anjou=(AFNJ," . $mp->primary . "), (AFNJ," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Aquitaine" );
if ( $mp->primary != "AFKT" || $mp->secondary != "AFKT" ) {
	print "<br>of Aquitaine=(AFKT," . $mp->primary . "), (AFKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Aumale" );
if ( $mp->primary != "AFML" || $mp->secondary != "AFML" ) {
	print "<br>of Aumale=(AFML," . $mp->primary . "), (AFML," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Bavaria" );
if ( $mp->primary != "AFPF" || $mp->secondary != "AFPF" ) {
	print "<br>of Bavaria=(AFPF," . $mp->primary . "), (AFPF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Boulogne" );
if ( $mp->primary != "AFPL" || $mp->secondary != "AFPL" ) {
	print "<br>of Boulogne=(AFPL," . $mp->primary . "), (AFPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Brittany" );
if ( $mp->primary != "AFPR" || $mp->secondary != "AFPR" ) {
	print "<br>of Brittany=(AFPR," . $mp->primary . "), (AFPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Brittary" );
if ( $mp->primary != "AFPR" || $mp->secondary != "AFPR" ) {
	print "<br>of Brittary=(AFPR," . $mp->primary . "), (AFPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Castile" );
if ( $mp->primary != "AFKS" || $mp->secondary != "AFKS" ) {
	print "<br>of Castile=(AFKS," . $mp->primary . "), (AFKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Chester" );
if ( $mp->primary != "AFXS" || $mp->secondary != "AFKS" ) {
	print "<br>of Chester=(AFXS," . $mp->primary . "), (AFKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Clermont" );
if ( $mp->primary != "AFKL" || $mp->secondary != "AFKL" ) {
	print "<br>of Clermont=(AFKL," . $mp->primary . "), (AFKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Cologne" );
if ( $mp->primary != "AFKL" || $mp->secondary != "AFKL" ) {
	print "<br>of Cologne=(AFKL," . $mp->primary . "), (AFKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Dinan" );
if ( $mp->primary != "AFTN" || $mp->secondary != "AFTN" ) {
	print "<br>of Dinan=(AFTN," . $mp->primary . "), (AFTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Dunbar" );
if ( $mp->primary != "AFTN" || $mp->secondary != "AFTN" ) {
	print "<br>of Dunbar=(AFTN," . $mp->primary . "), (AFTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of England" );
if ( $mp->primary != "AFNK" || $mp->secondary != "AFNK" ) {
	print "<br>of England=(AFNK," . $mp->primary . "), (AFNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Essex" );
if ( $mp->primary != "AFSK" || $mp->secondary != "AFSK" ) {
	print "<br>of Essex=(AFSK," . $mp->primary . "), (AFSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Falaise" );
if ( $mp->primary != "AFFL" || $mp->secondary != "AFFL" ) {
	print "<br>of Falaise=(AFFL," . $mp->primary . "), (AFFL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Flanders" );
if ( $mp->primary != "AFFL" || $mp->secondary != "AFFL" ) {
	print "<br>of Flanders=(AFFL," . $mp->primary . "), (AFFL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Galloway" );
if ( $mp->primary != "AFKL" || $mp->secondary != "AFKL" ) {
	print "<br>of Galloway=(AFKL," . $mp->primary . "), (AFKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Germany" );
if ( $mp->primary != "AFKR" || $mp->secondary != "AFJR" ) {
	print "<br>of Germany=(AFKR," . $mp->primary . "), (AFJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Gloucester" );
if ( $mp->primary != "AFKL" || $mp->secondary != "AFKL" ) {
	print "<br>of Gloucester=(AFKL," . $mp->primary . "), (AFKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Heristal" );
if ( $mp->primary != "AFRS" || $mp->secondary != "AFRS" ) {
	print "<br>of Heristal=(AFRS," . $mp->primary . "), (AFRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Hungary" );
if ( $mp->primary != "AFNK" || $mp->secondary != "AFNK" ) {
	print "<br>of Hungary=(AFNK," . $mp->primary . "), (AFNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Huntington" );
if ( $mp->primary != "AFNT" || $mp->secondary != "AFNT" ) {
	print "<br>of Huntington=(AFNT," . $mp->primary . "), (AFNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Kiev" );
if ( $mp->primary != "AFKF" || $mp->secondary != "AFKF" ) {
	print "<br>of Kiev=(AFKF," . $mp->primary . "), (AFKF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Kuno" );
if ( $mp->primary != "AFKN" || $mp->secondary != "AFKN" ) {
	print "<br>of Kuno=(AFKN," . $mp->primary . "), (AFKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Landen" );
if ( $mp->primary != "AFLN" || $mp->secondary != "AFLN" ) {
	print "<br>of Landen=(AFLN," . $mp->primary . "), (AFLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Laon" );
if ( $mp->primary != "AFLN" || $mp->secondary != "AFLN" ) {
	print "<br>of Laon=(AFLN," . $mp->primary . "), (AFLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Leinster" );
if ( $mp->primary != "AFLN" || $mp->secondary != "AFLN" ) {
	print "<br>of Leinster=(AFLN," . $mp->primary . "), (AFLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Lens" );
if ( $mp->primary != "AFLN" || $mp->secondary != "AFLN" ) {
	print "<br>of Lens=(AFLN," . $mp->primary . "), (AFLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Lorraine" );
if ( $mp->primary != "AFLR" || $mp->secondary != "AFLR" ) {
	print "<br>of Lorraine=(AFLR," . $mp->primary . "), (AFLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Louvain" );
if ( $mp->primary != "AFLF" || $mp->secondary != "AFLF" ) {
	print "<br>of Louvain=(AFLF," . $mp->primary . "), (AFLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Mercia" );
if ( $mp->primary != "AFMR" || $mp->secondary != "AFMR" ) {
	print "<br>of Mercia=(AFMR," . $mp->primary . "), (AFMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Metz" );
if ( $mp->primary != "AFMT" || $mp->secondary != "AFMT" ) {
	print "<br>of Metz=(AFMT," . $mp->primary . "), (AFMT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Meulan" );
if ( $mp->primary != "AFML" || $mp->secondary != "AFML" ) {
	print "<br>of Meulan=(AFML," . $mp->primary . "), (AFML," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Nass" );
if ( $mp->primary != "AFNS" || $mp->secondary != "AFNS" ) {
	print "<br>of Nass=(AFNS," . $mp->primary . "), (AFNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Normandy" );
if ( $mp->primary != "AFNR" || $mp->secondary != "AFNR" ) {
	print "<br>of Normandy=(AFNR," . $mp->primary . "), (AFNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Ohningen" );
if ( $mp->primary != "AFNN" || $mp->secondary != "AFNN" ) {
	print "<br>of Ohningen=(AFNN," . $mp->primary . "), (AFNN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Orleans" );
if ( $mp->primary != "AFRL" || $mp->secondary != "AFRL" ) {
	print "<br>of Orleans=(AFRL," . $mp->primary . "), (AFRL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Poitou" );
if ( $mp->primary != "AFPT" || $mp->secondary != "AFPT" ) {
	print "<br>of Poitou=(AFPT," . $mp->primary . "), (AFPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Polotzk" );
if ( $mp->primary != "AFPL" || $mp->secondary != "AFPL" ) {
	print "<br>of Polotzk=(AFPL," . $mp->primary . "), (AFPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Provence" );
if ( $mp->primary != "AFPR" || $mp->secondary != "AFPR" ) {
	print "<br>of Provence=(AFPR," . $mp->primary . "), (AFPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Ringelheim" );
if ( $mp->primary != "AFRN" || $mp->secondary != "AFRN" ) {
	print "<br>of Ringelheim=(AFRN," . $mp->primary . "), (AFRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Salisbury" );
if ( $mp->primary != "AFSL" || $mp->secondary != "AFSL" ) {
	print "<br>of Salisbury=(AFSL," . $mp->primary . "), (AFSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Saxony" );
if ( $mp->primary != "AFSK" || $mp->secondary != "AFSK" ) {
	print "<br>of Saxony=(AFSK," . $mp->primary . "), (AFSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Scotland" );
if ( $mp->primary != "AFSK" || $mp->secondary != "AFSK" ) {
	print "<br>of Scotland=(AFSK," . $mp->primary . "), (AFSK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Senlis" );
if ( $mp->primary != "AFSN" || $mp->secondary != "AFSN" ) {
	print "<br>of Senlis=(AFSN," . $mp->primary . "), (AFSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Stafford" );
if ( $mp->primary != "AFST" || $mp->secondary != "AFST" ) {
	print "<br>of Stafford=(AFST," . $mp->primary . "), (AFST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Swabia" );
if ( $mp->primary != "AFSP" || $mp->secondary != "AFSP" ) {
	print "<br>of Swabia=(AFSP," . $mp->primary . "), (AFSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of Tongres" );
if ( $mp->primary != "AFTN" || $mp->secondary != "AFTN" ) {
	print "<br>of Tongres=(AFTN," . $mp->primary . "), (AFTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "of the Tributes" );
if ( $mp->primary != "AF0T" || $mp->secondary != "AFTT" ) {
	print "<br>of the Tributes=(AF0T," . $mp->primary . "), (AFTT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "unknown" );
if ( $mp->primary != "ANKN" || $mp->secondary != "ANKN" ) {
	print "<br>unknown=(ANKN," . $mp->primary . "), (ANKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "van der Gouda" );
if ( $mp->primary != "FNTR" || $mp->secondary != "FNTR" ) {
	print "<br>van der Gouda=(FNTR," . $mp->primary . "), (FNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "von Adenbaugh" );
if ( $mp->primary != "FNTN" || $mp->secondary != "FNTN" ) {
	print "<br>von Adenbaugh=(FNTN," . $mp->primary . "), (FNTN," . $mp->secondary . ")\n";
}

# added some additional names to check out specific test cases

$mp = new DoubleMetaPhone( "ARCHITure" );
if ( $mp->primary != "ARKT" || $mp->secondary != "ARKT" ) {
	print "<br>ARCHITure=(ARKT," . $mp->primary . "), (ARKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Arnoff" );
if ( $mp->primary != "ARNF" || $mp->secondary != "ARNF" ) {
	print "<br>Arnoff=(ARNF," . $mp->primary . "), (ARNF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Arnow" );
if ( $mp->primary != "ARN" || $mp->secondary != "ARNF" ) {
	print "<br>Arnow=(ARN," . $mp->primary . "), (ARNF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "DANGER" );
if ( $mp->primary != "TNJR" || $mp->secondary != "TNKR" ) {
	print "<br>DANGER=(TNJR," . $mp->primary . "), (TNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Jankelowicz" );
if ( $mp->primary != "JNKL" || $mp->secondary != "ANKL" ) {
	print "<br>Jankelowicz=(JNKL," . $mp->primary . "), (ANKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "MANGER" );
if ( $mp->primary != "MNJR" || $mp->secondary != "MNKR" ) {
	print "<br>MANGER=(MNJR," . $mp->primary . "), (MNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McClellan" );
if ( $mp->primary != "MKLL" || $mp->secondary != "MKLL" ) {
	print "<br>McClellan=(MKLL," . $mp->primary . "), (MKLL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McHugh" );
if ( $mp->primary != "MK" || $mp->secondary != "MK" ) {
	print "<br>McHugh=(MK," . $mp->primary . "), (MK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "McLaughlin" );
if ( $mp->primary != "MKLF" || $mp->secondary != "MKLF" ) {
	print "<br>McLaughlin=(MKLF," . $mp->primary . "), (MKLF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ORCHEStra" );
if ( $mp->primary != "ARKS" || $mp->secondary != "ARKS" ) {
	print "<br>ORCHEStra=(ARKS," . $mp->primary . "), (ARKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ORCHID" );
if ( $mp->primary != "ARKT" || $mp->secondary != "ARKT" ) {
	print "<br>ORCHID=(ARKT," . $mp->primary . "), (ARKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Pierce" );
if ( $mp->primary != "PRS" || $mp->secondary != "PRS" ) {
	print "<br>Pierce=(PRS," . $mp->primary . "), (PRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "RANGER" );
if ( $mp->primary != "RNJR" || $mp->secondary != "RNKR" ) {
	print "<br>RANGER=(RNJR," . $mp->primary . "), (RNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Schlesinger" );
if ( $mp->primary != "XLSN" || $mp->secondary != "SLSN" ) {
	print "<br>Schlesinger=(XLSN," . $mp->primary . "), (SLSN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Uomo" );
if ( $mp->primary != "AM" || $mp->secondary != "AM" ) {
	print "<br>Uomo=(AM," . $mp->primary . "), (AM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Vasserman" );
if ( $mp->primary != "FSRM" || $mp->secondary != "FSRM" ) {
	print "<br>Vasserman=(FSRM," . $mp->primary . "), (FSRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Wasserman" );
if ( $mp->primary != "ASRM" || $mp->secondary != "FSRM" ) {
	print "<br>Wasserman=(ASRM," . $mp->primary . "), (FSRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Womo" );
if ( $mp->primary != "AM" || $mp->secondary != "FM" ) {
	print "<br>Womo=(AM," . $mp->primary . "), (FM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "Yankelovich" );
if ( $mp->primary != "ANKL" || $mp->secondary != "ANKL" ) {
	print "<br>Yankelovich=(ANKL," . $mp->primary . "), (ANKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "accede" );
if ( $mp->primary != "AKST" || $mp->secondary != "AKST" ) {
	print "<br>accede=(AKST," . $mp->primary . "), (AKST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "accident" );
if ( $mp->primary != "AKST" || $mp->secondary != "AKST" ) {
	print "<br>accident=(AKST," . $mp->primary . "), (AKST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "adelsheim" );
if ( $mp->primary != "ATLS" || $mp->secondary != "ATLS" ) {
	print "<br>adelsheim=(ATLS," . $mp->primary . "), (ATLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "aged" );
if ( $mp->primary != "AJT" || $mp->secondary != "AKT" ) {
	print "<br>aged=(AJT," . $mp->primary . "), (AKT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ageless" );
if ( $mp->primary != "AJLS" || $mp->secondary != "AKLS" ) {
	print "<br>ageless=(AJLS," . $mp->primary . "), (AKLS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "agency" );
if ( $mp->primary != "AJNS" || $mp->secondary != "AKNS" ) {
	print "<br>agency=(AJNS," . $mp->primary . "), (AKNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "aghast" );
if ( $mp->primary != "AKST" || $mp->secondary != "AKST" ) {
	print "<br>aghast=(AKST," . $mp->primary . "), (AKST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "agio" );
if ( $mp->primary != "AJ" || $mp->secondary != "AK" ) {
	print "<br>agio=(AJ," . $mp->primary . "), (AK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "agrimony" );
if ( $mp->primary != "AKRM" || $mp->secondary != "AKRM" ) {
	print "<br>agrimony=(AKRM," . $mp->primary . "), (AKRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "album" );
if ( $mp->primary != "ALPM" || $mp->secondary != "ALPM" ) {
	print "<br>album=(ALPM," . $mp->primary . "), (ALPM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "alcmene" );
if ( $mp->primary != "ALKM" || $mp->secondary != "ALKM" ) {
	print "<br>alcmene=(ALKM," . $mp->primary . "), (ALKM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "alehouse" );
if ( $mp->primary != "ALHS" || $mp->secondary != "ALHS" ) {
	print "<br>alehouse=(ALHS," . $mp->primary . "), (ALHS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "antique" );
if ( $mp->primary != "ANTK" || $mp->secondary != "ANTK" ) {
	print "<br>antique=(ANTK," . $mp->primary . "), (ANTK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "artois" );
if ( $mp->primary != "ART" || $mp->secondary != "ARTS" ) {
	print "<br>artois=(ART," . $mp->primary . "), (ARTS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "automation" );
if ( $mp->primary != "ATMX" || $mp->secondary != "ATMX" ) {
	print "<br>automation=(ATMX," . $mp->primary . "), (ATMX," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "bacchus" );
if ( $mp->primary != "PKS" || $mp->secondary != "PKS" ) {
	print "<br>bacchus=(PKS," . $mp->primary . "), (PKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "bacci" );
if ( $mp->primary != "PX" || $mp->secondary != "PX" ) {
	print "<br>bacci=(PX," . $mp->primary . "), (PX," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "bajador" );
if ( $mp->primary != "PJTR" || $mp->secondary != "PHTR" ) {
	print "<br>bajador=(PJTR," . $mp->primary . "), (PHTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "bellocchio" );
if ( $mp->primary != "PLX" || $mp->secondary != "PLX" ) {
	print "<br>bellocchio=(PLX," . $mp->primary . "), (PLX," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "bertucci" );
if ( $mp->primary != "PRTX" || $mp->secondary != "PRTX" ) {
	print "<br>bertucci=(PRTX," . $mp->primary . "), (PRTX," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "biaggi" );
if ( $mp->primary != "PJ" || $mp->secondary != "PK" ) {
	print "<br>biaggi=(PJ," . $mp->primary . "), (PK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "bough" );
if ( $mp->primary != "P" || $mp->secondary != "P" ) {
	print "<br>bough=(P," . $mp->primary . "), (P," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "breaux" );
if ( $mp->primary != "PR" || $mp->secondary != "PR" ) {
	print "<br>breaux=(PR," . $mp->primary . "), (PR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "broughton" );
if ( $mp->primary != "PRTN" || $mp->secondary != "PRTN" ) {
	print "<br>broughton=(PRTN," . $mp->primary . "), (PRTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "cabrillo" );
if ( $mp->primary != "KPRL" || $mp->secondary != "KPR" ) {
	print "<br>cabrillo=(KPRL," . $mp->primary . "), (KPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "caesar" );
if ( $mp->primary != "SSR" || $mp->secondary != "SSR" ) {
	print "<br>caesar=(SSR," . $mp->primary . "), (SSR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "cagney" );
if ( $mp->primary != "KKN" || $mp->secondary != "KKN" ) {
	print "<br>cagney=(KKN," . $mp->primary . "), (KKN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "campbell" );
if ( $mp->primary != "KMPL" || $mp->secondary != "KMPL" ) {
	print "<br>campbell=(KMPL," . $mp->primary . "), (KMPL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "carlisle" );
if ( $mp->primary != "KRLL" || $mp->secondary != "KRLL" ) {
	print "<br>carlisle=(KRLL," . $mp->primary . "), (KRLL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "carlysle" );
if ( $mp->primary != "KRLL" || $mp->secondary != "KRLL" ) {
	print "<br>carlysle=(KRLL," . $mp->primary . "), (KRLL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "chemistry" );
if ( $mp->primary != "KMST" || $mp->secondary != "KMST" ) {
	print "<br>chemistry=(KMST," . $mp->primary . "), (KMST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "chianti" );
if ( $mp->primary != "KNT" || $mp->secondary != "KNT" ) {
	print "<br>chianti=(KNT," . $mp->primary . "), (KNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "chorus" );
if ( $mp->primary != "KRS" || $mp->secondary != "KRS" ) {
	print "<br>chorus=(KRS," . $mp->primary . "), (KRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "cough" );
if ( $mp->primary != "KF" || $mp->secondary != "KF" ) {
	print "<br>cough=(KF," . $mp->primary . "), (KF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "czerny" );
if ( $mp->primary != "SRN" || $mp->secondary != "XRN" ) {
	print "<br>czerny=(SRN," . $mp->primary . "), (XRN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "deffenbacher" );
if ( $mp->primary != "TFNP" || $mp->secondary != "TFNP" ) {
	print "<br>deffenbacher=(TFNP," . $mp->primary . "), (TFNP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "dumb" );
if ( $mp->primary != "TM" || $mp->secondary != "TM" ) {
	print "<br>dumb=(TM," . $mp->primary . "), (TM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "edgar" );
if ( $mp->primary != "ATKR" || $mp->secondary != "ATKR" ) {
	print "<br>edgar=(ATKR," . $mp->primary . "), (ATKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "edge" );
if ( $mp->primary != "AJ" || $mp->secondary != "AJ" ) {
	print "<br>edge=(AJ," . $mp->primary . "), (AJ," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "filipowicz" );
if ( $mp->primary != "FLPT" || $mp->secondary != "FLPF" ) {
	print "<br>filipowicz=(FLPT," . $mp->primary . "), (FLPF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "focaccia" );
if ( $mp->primary != "FKX" || $mp->secondary != "FKX" ) {
	print "<br>focaccia=(FKX," . $mp->primary . "), (FKX," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "gallegos" );
if ( $mp->primary != "KLKS" || $mp->secondary != "KKS" ) {
	print "<br>gallegos=(KLKS," . $mp->primary . "), (KKS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "gambrelli" );
if ( $mp->primary != "KMPR" || $mp->secondary != "KMPR" ) {
	print "<br>gambrelli=(KMPR," . $mp->primary . "), (KMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "geithain" );
if ( $mp->primary != "K0N" || $mp->secondary != "JTN" ) {
	print "<br>geithain=(K0N," . $mp->primary . "), (JTN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ghiradelli" );
if ( $mp->primary != "JRTL" || $mp->secondary != "JRTL" ) {
	print "<br>ghiradelli=(JRTL," . $mp->primary . "), (JRTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "ghislane" );
if ( $mp->primary != "JLN" || $mp->secondary != "JLN" ) {
	print "<br>ghislane=(JLN," . $mp->primary . "), (JLN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "gough" );
if ( $mp->primary != "KF" || $mp->secondary != "KF" ) {
	print "<br>gough=(KF," . $mp->primary . "), (KF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "hartheim" );
if ( $mp->primary != "HR0M" || $mp->secondary != "HRTM" ) {
	print "<br>hartheim=(HR0M," . $mp->primary . "), (HRTM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "heimsheim" );
if ( $mp->primary != "HMSM" || $mp->secondary != "HMSM" ) {
	print "<br>heimsheim=(HMSM," . $mp->primary . "), (HMSM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "hochmeier" );
if ( $mp->primary != "HKMR" || $mp->secondary != "HKMR" ) {
	print "<br>hochmeier=(HKMR," . $mp->primary . "), (HKMR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "hugh" );
if ( $mp->primary != "H" || $mp->secondary != "H" ) {
	print "<br>hugh=(H," . $mp->primary . "), (H," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "hunger" );
if ( $mp->primary != "HNKR" || $mp->secondary != "HNJR" ) {
	print "<br>hunger=(HNKR," . $mp->primary . "), (HNJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "hungry" );
if ( $mp->primary != "HNKR" || $mp->secondary != "HNKR" ) {
	print "<br>hungry=(HNKR," . $mp->primary . "), (HNKR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "island" );
if ( $mp->primary != "ALNT" || $mp->secondary != "ALNT" ) {
	print "<br>island=(ALNT," . $mp->primary . "), (ALNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "isle" );
if ( $mp->primary != "AL" || $mp->secondary != "AL" ) {
	print "<br>isle=(AL," . $mp->primary . "), (AL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "jose" );
if ( $mp->primary != "HS" || $mp->secondary != "HS" ) {
	print "<br>jose=(HS," . $mp->primary . "), (HS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "laugh" );
if ( $mp->primary != "LF" || $mp->secondary != "LF" ) {
	print "<br>laugh=(LF," . $mp->primary . "), (LF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "mac caffrey" );
if ( $mp->primary != "MKFR" || $mp->secondary != "MKFR" ) {
	print "<br>mac caffrey=(MKFR," . $mp->primary . "), (MKFR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "mac gregor" );
if ( $mp->primary != "MKRK" || $mp->secondary != "MKRK" ) {
	print "<br>mac gregor=(MKRK," . $mp->primary . "), (MKRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "pegnitz" );
if ( $mp->primary != "PNTS" || $mp->secondary != "PKNT" ) {
	print "<br>pegnitz=(PNTS," . $mp->primary . "), (PKNT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "piskowitz" );
if ( $mp->primary != "PSKT" || $mp->secondary != "PSKF" ) {
	print "<br>piskowitz=(PSKT," . $mp->primary . "), (PSKF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "queen" );
if ( $mp->primary != "KN" || $mp->secondary != "KN" ) {
	print "<br>queen=(KN," . $mp->primary . "), (KN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "raspberry" );
if ( $mp->primary != "RSPR" || $mp->secondary != "RSPR" ) {
	print "<br>raspberry=(RSPR," . $mp->primary . "), (RSPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "resnais" );
if ( $mp->primary != "RSN" || $mp->secondary != "RSNS" ) {
	print "<br>resnais=(RSN," . $mp->primary . "), (RSNS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "rogier" );
if ( $mp->primary != "RJ" || $mp->secondary != "RJR" ) {
	print "<br>rogier=(RJ," . $mp->primary . "), (RJR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "rough" );
if ( $mp->primary != "RF" || $mp->secondary != "RF" ) {
	print "<br>rough=(RF," . $mp->primary . "), (RF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "san jacinto" );
if ( $mp->primary != "SNHS" || $mp->secondary != "SNHS" ) {
	print "<br>san jacinto=(SNHS," . $mp->primary . "), (SNHS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schenker" );
if ( $mp->primary != "XNKR" || $mp->secondary != "SKNK" ) {
	print "<br>schenker=(XNKR," . $mp->primary . "), (SKNK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schermerhorn" );
if ( $mp->primary != "XRMR" || $mp->secondary != "SKRM" ) {
	print "<br>schermerhorn=(XRMR," . $mp->primary . "), (SKRM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schmidt" );
if ( $mp->primary != "XMT" || $mp->secondary != "SMT" ) {
	print "<br>schmidt=(XMT," . $mp->primary . "), (SMT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schneider" );
if ( $mp->primary != "XNTR" || $mp->secondary != "SNTR" ) {
	print "<br>schneider=(XNTR," . $mp->primary . "), (SNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "school" );
if ( $mp->primary != "SKL" || $mp->secondary != "SKL" ) {
	print "<br>school=(SKL," . $mp->primary . "), (SKL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schooner" );
if ( $mp->primary != "SKNR" || $mp->secondary != "SKNR" ) {
	print "<br>schooner=(SKNR," . $mp->primary . "), (SKNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schrozberg" );
if ( $mp->primary != "XRSP" || $mp->secondary != "SRSP" ) {
	print "<br>schrozberg=(XRSP," . $mp->primary . "), (SRSP," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schulman" );
if ( $mp->primary != "XLMN" || $mp->secondary != "XLMN" ) {
	print "<br>schulman=(XLMN," . $mp->primary . "), (XLMN," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schwabach" );
if ( $mp->primary != "XPK" || $mp->secondary != "XFPK" ) {
	print "<br>schwabach=(XPK," . $mp->primary . "), (XFPK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "schwarzach" );
if ( $mp->primary != "XRSK" || $mp->secondary != "XFRT" ) {
	print "<br>schwarzach=(XRSK," . $mp->primary . "), (XFRT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "smith" );
if ( $mp->primary != "SM0" || $mp->secondary != "XMT" ) {
	print "<br>smith=(SM0," . $mp->primary . "), (XMT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "snider" );
if ( $mp->primary != "SNTR" || $mp->secondary != "XNTR" ) {
	print "<br>snider=(SNTR," . $mp->primary . "), (XNTR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "succeed" );
if ( $mp->primary != "SKST" || $mp->secondary != "SKST" ) {
	print "<br>succeed=(SKST," . $mp->primary . "), (SKST," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "sugarcane" );
if ( $mp->primary != "XKRK" || $mp->secondary != "SKRK" ) {
	print "<br>sugarcane=(XKRK," . $mp->primary . "), (SKRK," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "svobodka" );
if ( $mp->primary != "SFPT" || $mp->secondary != "SFPT" ) {
	print "<br>svobodka=(SFPT," . $mp->primary . "), (SFPT," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "tagliaro" );
if ( $mp->primary != "TKLR" || $mp->secondary != "TLR" ) {
	print "<br>tagliaro=(TKLR," . $mp->primary . "), (TLR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "thames" );
if ( $mp->primary != "TMS" || $mp->secondary != "TMS" ) {
	print "<br>thames=(TMS," . $mp->primary . "), (TMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "theilheim" );
if ( $mp->primary != "0LM" || $mp->secondary != "TLM" ) {
	print "<br>theilheim=(0LM," . $mp->primary . "), (TLM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "thomas" );
if ( $mp->primary != "TMS" || $mp->secondary != "TMS" ) {
	print "<br>thomas=(TMS," . $mp->primary . "), (TMS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "thumb" );
if ( $mp->primary != "0M" || $mp->secondary != "TM" ) {
	print "<br>thumb=(0M," . $mp->primary . "), (TM," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "tichner" );
if ( $mp->primary != "TXNR" || $mp->secondary != "TKNR" ) {
	print "<br>tichner=(TXNR," . $mp->primary . "), (TKNR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "tough" );
if ( $mp->primary != "TF" || $mp->secondary != "TF" ) {
	print "<br>tough=(TF," . $mp->primary . "), (TF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "umbrella" );
if ( $mp->primary != "AMPR" || $mp->secondary != "AMPR" ) {
	print "<br>umbrella=(AMPR," . $mp->primary . "), (AMPR," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "vilshofen" );
if ( $mp->primary != "FLXF" || $mp->secondary != "FLXF" ) {
	print "<br>vilshofen=(FLXF," . $mp->primary . "), (FLXF," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "von schuller" );
if ( $mp->primary != "FNXL" || $mp->secondary != "FNXL" ) {
	print "<br>von schuller=(FNXL," . $mp->primary . "), (FNXL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "wachtler" );
if ( $mp->primary != "AKTL" || $mp->secondary != "FKTL" ) {
	print "<br>wachtler=(AKTL," . $mp->primary . "), (FKTL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "wechsler" );
if ( $mp->primary != "AKSL" || $mp->secondary != "FKSL" ) {
	print "<br>wechsler=(AKSL," . $mp->primary . "), (FKSL," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "weikersheim" );
if ( $mp->primary != "AKRS" || $mp->secondary != "FKRS" ) {
	print "<br>weikersheim=(AKRS," . $mp->primary . "), (FKRS," . $mp->secondary . ")\n";
}
$mp = new DoubleMetaPhone( "zhao" );
if ( $mp->primary != "J" || $mp->secondary != "J" ) {
	print "<br>zhao=(J," . $mp->primary . "), (J," . $mp->secondary . ")\n";
}

print "<br>Done! Any errors are printed above.\n";

?>
