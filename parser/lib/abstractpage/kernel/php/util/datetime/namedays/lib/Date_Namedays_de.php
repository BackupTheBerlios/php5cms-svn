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


using( 'util.datetime.namedays.lib.Date_Namedays' );


/**
 * @package util_datetime_namedays_lib
 */
 
class Date_Namedays_de extends Date_Namedays
{
	/**
	 * Array of Namedays
	 *
	 * @var    array
	 * @access private
	 */
	var $_namedays = array( 
  		"1" => array(
    		"2"  => "Basilius",
    		"3"  => "Genoveva",
    		"4"  => "Angela",
    		"5"  => "Emilie",
    		"7"  => "Virginia",
    		"8"  => "Erhard",
    		"9"  => "Julian",
   	 		"10" => "Wilhelm",
    		"11" => "Tasso",
    		"12" => "Ernst",
    		"13" => "Jutta",
    		"14" => "Felix",
    		"15" => "Gabriel",
    		"16" => "Marcellus",
    		"17" => "Anton",
    		"18" => "Regina",
    		"19" => "Mario",
    		"21" => "Agnes",
    		"22" => "Vinzenz",
    		"23" => "Heinrich",
    		"24" => "Vera",
    		"26" => "Robert",
    		"27" => "Angela",
    		"29" => "Valerius",
    		"30" => "Martina"
  		),
  		"2" => array(
    		"1"  => "Brigitta",
    		"3"  => "Blasius",
    		"4"  => "Andreas",
    		"5"  => "Agatha",
    		"6"  => "Dorothea",
    		"7"  => "Richard",
    		"8"  => "Hieronimus",
    		"9"  => "Apollonia",
    		"10" => "Wilhelm",
    		"11" => "Theodor",
    		"12" => "Reginald",
    		"13" => "Gerlinde",
    		"14" => "Valentin",
    		"15" => "Siegfried",
    		"18" => "Konstantia",
    		"19" => "Hadwig",
    		"23" => "Otto",
    		"25" => "Walpurga",
    		"26" => "Alexander",
    		"27" => "Leander",
    		"28" => "Roman"
  		),
  		"3" => array(
    		"1"  => "David",
    		"2"  => "Karl",
    		"3"  => "Friedrich",
    		"4"  => "Kasimir",
    		"5"  => "Gerda",
    		"6"  => "Coletta",
    		"8"  => "Johann v. G.",
    		"9"  => "Franziska",
    		"10" => "Emil",
    		"11" => "Theresia",
    		"12" => "Maximilian",
    		"13" => "Gerald",
    		"14" => "Mathilde",
    		"15" => "Klemens",
   	 		"16" => "Hilarius",
    		"17" => "Gertrud",
    		"18" => "Eduard",
    		"19" => "Josef",
    		"20" => "Claudia",
   	 		"21" => "Alexandra",
    		"22" => "Lea",
    		"24" => "Katharina",
    		"26" => "Ludger",
    		"27" => "Frowin",
    		"29" => "Berthold",
    		"30" => "Amadeus",
    		"31" => "Cornelia"
  		),
  		"4" => array(
    		"6"  => "Sixtus",
    		"8"  => "Walter",
    		"9"  => "Waltraud",
    		"10" => "Ezechiel",
    		"11" => "Stanislaus",
    		"13" => "Martin",
    		"14" => "Valerian",
    		"15" => "Waltmann",
    		"16" => "Bernadette",
    		"17" => "Eberhard",
    		"18" => "Aja",
    		"20" => "Simon",
    		"21" => "Anselm",
    		"22" => "Wolfhelm",
    		"23" => "Georg",
    		"24" => "Helmut",
    		"26" => "Trudpert",
    		"27" => "Zita",
    		"29" => "Katharina",
    		"30" => "Hildegard"
  		),
  		"5" => array(
    		"2"  => "Boris",
    		"3"  => "Viola",
    		"4"  => "Florian",
    		"5"  => "Sigrid",
    		"6"  => "Antonia",
    		"7"  => "Gisela",
    		"10" => "Antonin",
    		"11" => "Gangolf",
    		"12" => "Pankratius",
    		"14" => "Bonifatius",
    		"15" => "Sophie",
    		"16" => "Johannes",
    		"17" => "Pascal",
    		"18" => "Erich",
    		"19" => "Celestin",
    		"20" => "Elfriede",
    		"21" => "Herman",
    		"22" => "Rita",
    		"25" => "Gregor",
    		"27" => "Augustin",
    		"28" => "Wilhelm",
    		"29" => "Maximin",
    		"30" => "Ferdinand",
    		"31" => "Petronella"
  		),
  		"6" => array(
    		"1"  => "Justin",
    		"2"  => "Erasmus",
    		"4"  => "Klothilde",
    		"5"  => "Bonifaz",
    		"6"  => "Norbert",
    		"7"  => "Robert",
    		"8"  => "Ilga",
    		"9"  => "Gratia",
    		"10" => "Heinrich",
    		"12" => "Johann",
    		"14" => "Lothar",
    		"15" => "Bernhard",
    		"16" => "Benno",
    		"17" => "Rainer",
    		"18" => "Markus",
    		"19" => "Elisabeth",
    		"20" => "Adalbert",
    		"21" => "Aloisius",
    		"22" => "Thomas",
    		"23" => "Edeltraud",
    		"25" => "Dorothea",
    		"27" => "Harald",
    		"28" => "Diethild",
    		"30" => "Otto"
  		),
  		"7" => array(
    		"1"  => "Theobald",
    		"3"  => "Thomas",
    		"4"  => "Ulrich",
    		"5"  => "Anton",
    		"7"  => "Willibald",
    		"9"  => "Veronika",
    		"10" => "Knud",
   	 		"11" => "Oliver",
    		"15" => "Egon",
    		"16" => "Carmen",
    		"17" => array( "Roland", "Alexius" ),
    		"18" => "Friedrich",
    		"19" => "Justa",
    		"20" => "Margareta",
   	 		"22" => "Maria Magdalena",
    		"23" => "Brigitta",
    		"24" => "Christophorus",
    		"25" => "Jakob",
    		"26" => "Anna",
    		"27" => "Berthold",
    		"29" => "Martha",
    		"30" => "Ingeborg"
		),
		"8" => array(
    		"2"  => "Eusebius",
    		"3"  => "Lydia",
    		"4"  => "Rainer",
    		"5"  => "Oswald",
    		"7"  => "Kajetan",
    		"8"  => "Gustav",
    		"9"  => "Roman",
    		"10" => "Astrid",
    		"11" => "Susanna",
    		"12" => "Hilaria",
   	 		"13" => "Gertrud",
    		"14" => "Maximilian",
    		"16" => "Stefan",
    		"17" => "Hyazinth",
    		"18" => "Helene",
    		"20" => "Bernhard",
    		"21" => "Pius X.",
    		"24" => "Isolde",
    		"26" => "Margareta",
    		"27" => "Monika",
    		"28" => "Augustin",
    		"29" => "Sabine",
    		"30" => "Heribert",
    		"31" => "Raimund"
  		),
  		"9" => array(
    		"1"  => "Verena",
    		"2"  => "Ren",
    		"3"  => "Gregor",
    		"4"  => "Rosalia",
    		"5"  => "Albert",
    		"6"  => "Beata",
    		"7"  => "Regina",
    		"10" => "Nikolaus",
    		"11" => "Helga",
    		"13" => "Johannes",
    		"15" => "Dolores",
    		"16" => "Ludmilla",
    		"17" => "Hildegard",
    		"19" => "Igor",
    		"20" => "Candida",
    		"22" => "Moritz",
    		"23" => "Thekla",
    		"24" => "Rupert",
    		"26" => "Egmont",
    		"27" => "Vinzenz",
    		"28" => "Wenzel",
    		"29" => "Michael"
		),
		"10" => array(
    		"1"  => "Theresia",
    		"3"  => "Udo",
    		"5"  => "Attila",
    		"6"  => "Bruno",
    		"7"  => "Markus I.", // ?
    		"8"  => "Simeon",
    		"9"  => "Sibylle",
    		"10" => "Viktor",
    		"11" => "Jakob",
    		"12" => "Horst",
    		"13" => "Eduard",
    		"14" => "Kallistus",
    		"16" => "Hedwig",
    		"17" => "Rudolf",
    		"20" => "Wendelin",
    		"21" => "Ursula",
    		"22" => "Salome",
    		"23" => "Severin",
    		"24" => "Anton",
    		"25" => "Chrysanth.", // ?
    		"27" => "Wolfhard",
    		"31" => "Wolfgang"
  		),
		"11" => array(
    		"3"  => "Silvia",
    		"5"  => "Emmerich",
    		"6"  => "Leonhard",
    		"7"  => "Engelbert",
    		"8"  => "Gottfried",
    		"9"  => "Theodor",
    		"12" => "Christian",
    		"13" => "Stanislaus",
    		"14" => "Alberich",
    		"15" => "Leopold",
    		"16" => "Othmar",
    		"17" => "Gertrud",
    		"18" => "Roman",
    		"19" => "Elisabeth",
    		"20" => "Edmund",
    		"22" => "C�cilia",
    		"23" => "Klemens",
    		"24" => "Flora",
    		"25" => "Katharina",
    		"26" => "Konrad",
    		"27" => "Modestus",
    		"29" => "Julanda",
    		"30" => "Andreas"
		),
  		"12" => array(
   	 		"1"  => "Natalie",
    		"2"  => "Bibiana",
    		"3"  => "Franz X.", // ?
    		"4"  => "Barbara",
    		"6"  => "Nikolaus",
    		"7"  => "Ambrosius",
    		"9"  => "Valerie",
    		"10" => "Diethard",
    		"11" => "David",
    		"13" => "Lucia",
    		"14" => "Johannes",
    		"15" => "Christiana",
    		"16" => "Adelheid",
    		"17" => "Lazarus",
    		"18" => "Gatian",
    		"20" => "Eugen",
    		"21" => "Ingomar",
    		"22" => "Jutta",
    		"23" => "Victoria",
    		"30" => "Hermine",
    		"31" => "Silvester"
  		)
	);
	
	 
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Date_Namedays_de( $params = array() )
	{
		$this->Date_Namedays( $params );
	}
} // END OF Date_Namedays_de

?>
