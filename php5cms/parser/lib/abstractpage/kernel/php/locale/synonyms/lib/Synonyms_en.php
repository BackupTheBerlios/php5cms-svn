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


using( 'locale.synonyms.lib.Synonyms' );


/**
 * English synonyms.
 *
 * Charset:  us-ascii
 * Language: en
 *
 * @package locale_synonyms_lib
 */
 
class Synonyms_en extends Synonyms
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Synonyms_en()
	{
		$this->_populate();
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populate()
	{
		$this->synonyms = array(
			"abcense" => "absence",
			"abridgement" => "abridgment",
			"accomodate" => "accommodate",
			"acknowledgment" => "acknowledgement",
			"airplane" => "aeroplane",
			"andy" => "andrew",
			"anemia" => "anaemia",
			"anemic" => "anaemic",
			"anesthesia" => "anaesthesia",
			"anesthesiologist" => "anaesthesiologist",
			"anesthesiololy" => "anaesthesiology",
			"anesthetic" => "anaesthetic",
			"anesthetist" => "anaesthetist",
			"apr" => "april",
			"archean" => "archaean",
			"archeology" => "archaeology",
			"archeozoic" => "archaeozoic",
			"armor" => "armour",
			"artic" => "arctic",
			"attendence" => "attendance",
			"aug" => "august",
			"barbecue" => "barbeque" => "bbq",
			"behavior" => "behaviour",
			"behaviorism" => "behaviourism",
			"biassed" => "biased",
			"biol" => "biology",
			"buletin" => "bulletin",
			"calender" => "calendar",
			"canceled" => "cancelled",
			"car" => "auto" => "automobile",
			"catalog" => "catalogue",
			"cenozoic" => "caenozoic",
			"center" => "centre",
			"check" => "cheque",
			"color" => "colour",
			"colored" => "coloured",
			"coloring" => "colouring",
			"colorless" => "colourless",
			"comission" => "commission",
			"comittee" => "committee",
			"commitee" => "committee",
			"conceed" => "concede",
			"connexion" => "connection",
			"curiculum" => "curriculum",
			"dec" => "december",
			"defense" => "defence",
			"dept" => "department",
			"develope" => "develop",
			"discription" => "description",
			"dulness" => "dullness",
			"encyclopedia" => "encyclopaedia",
			"enroll" => "enrol",
			"esthetic" => "aesthetic",
			"etiology" => "aetiology",
			"exhorbitant" => "exorbitant",
			"exhuberant" => "exuberant",
			"existance" => "existence",
			"favorite" => "favourite",
			"feb" => "february",
			"fetus" => "foetus",
			"ficticious" => "fictitious",
			"flavor" => "flavour",
			"flourescent" => "fluorescent",
			"foriegn" => "foreign",
			"fourty" => "forty",
			"gage" => "guage",
			"geneology" => "genealogy",
			"grammer" => "grammar",
			"gray" => "grey",
			"guerilla" => "guerrilla",
			"gynecological" => "gynaecological",
			"gynecologist" => "gynaecologist",
			"gynecology" => "gynaecology",
			"harbor" => "harbour",
			"heighth" => "height",
			"hemaglobin" => "haemaglobin",
			"hematin" => "haematin",
			"hematite" => "haematite",
			"hematologist" => "haematologist",
			"hematology" => "haematology",
			"hemophilia" => "haemophilia",
			"hemorrhage" => "haemorrhage",
			"hemorrhoids" => "haemorrhoids",
			"honor" => "honour",
			"innoculate" => "inoculate",
			"installment" => "instalment",
			"irrelevent" => "irrelevant",
			"irrevelant" => "irrelevant",
			"jan" => "january",
			"jeweler" => "jeweller",
			"judgement" => "judgment",
			"jul" => "july",
			"jun" => "june",
			"labeled" => "labelled",
			"labor" => "labour",
			"laborer" => "labourer",
			"laborers" => "labourers",
			"laboring" => "labouring",
			"lib" => "library",
			"licence" => "license",
			"liesure" => "leisure",
			"liquify" => "liquefy",
			"maintainance" => "maintenance",
			"maintenence" => "maintenance",
			"marshal" => "marshall",
			"medieval" => "mediaeval",
			"medievalism" => "mediaevalism",
			"medievalist" => "mediaevalist",
			"meg" => "margaret",
			"meter" => "metre",
			"milage" => "mileage",
			"millipede" => "millepede",
			"miscelaneous" => "miscellaneous",
			"morgage" => "mortgage",
			"noticable" => "noticeable",
			"nov" => "november",
			"occurence" => "occurrence",
			"oct" => "october",
			"offense" => "offence",
			"ommision" => "omission",
			"ommission" => "omission",
			"organisation" => "organization",
			"organise" => "organize",
			"organised" => "organized",
			"pajamas" => "pyjamas",
			"paleography" => "palaeography",
			"paleolithic" => "palaeolithic",
			"paleontological" => "palaeontological",
			"paleontologist" => "palaeontologist",
			"paleontology" => "palaeontology",
			"paleozoic" => "palaeozoic",
			"pamplet" => "pamphlet",
			"paralell" => "parallel",
			"parl" => "parliament",
			"parlt" => "parliament",
			"pediatric" => "paediatric",
			"pediatrician" => "paediatrician",
			"pediatrics" => "paediatrics",
			"pedodontia" => "paedodontia",
			"pedodontics" => "paedodontics",
			"personel" => "personnel",
			"practise" => "practice",
			"program" => "programme",
			"psych" => "psychology",
			"qld" => "queensland",
			"questionaire" => "questionnaire",
			"rarify" => "rarefy",
			"reccomend" => "recommend",
			"recieve" => "receive",
			"resistence" => "resistance",
			"restaraunt" => "restaurant",
			"savior" => "saviour",
			"sep" => "september",
			"seperate" => "separate",
			"sept" => "september",
			"sieze" => "seize",
			"summarize" => "summarise",
			"summerize" => "summarise",
			"superceed" => "supercede",
			"superintendant" => "superintendent",
			"supersede" => "supercede",
			"suprise" => "surprise",
			"surprize" => "surprise",
			"synchronise" => "synchronize",
			"tas" => "tasmania",
			"temperary" => "temporary",
			"theater" => "theatre",
			"threshhold" => "threshold",
			"transfered" => "transferred",
			"truely" => "truly",
			"truley" => "truly",
			"useable" => "usable",
			"valor" => "valour",
			"vic" => "victoria",
			"vigor" => "vigour",
			"vol" => "volume",
			"withold" => "withhold",
			"yeild" => "yield"
		);
	}
} // END OF Synonyms_en

?>
