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


/**
 * @package commerce
 */
 
class CargoConnect extends PEAR
{
	var $_codes = array(
		// AWP - Airway bill prefix (who is carrying it)
		// AWN - Airway bill number (tracking number)
		
		//     AWP    AWN                    Airline Code
		array( "000", "Demonstration", 		 "ZZ" ),
		array( "053", "Aer Lingus", 		 "EI" ),
		array( "014", "Air Canada", 		 "AC" ),
		array( "999", "Air China", 			 "CA" ),
		array( "057", "Air France", 		 "AF" ),
		array( "098", "Air India", 			 "AI" ),
		array( "201", "Air Jamaica", 		 "JM" ),
		array( "643", "Air Malta", 			 "KM" ),
		array( "086", "Air New Zealand", 	 "NZ" ),
		array( "656", "Air Niugini", 		 "PX" ),
		array( "027", "Alaska Airlines", 	 "AS" ),
		array( "055", "Alitalia", 			 "AZ" ),
		array( "001", "American Airlines", 	 "AA" ),
		array( "090", "Ansett Australia", 	 "AN" ),
		array( "988", "Asiana Cargo", 		 "OZ" ),
		array( "257", "Austrian Airlines", 	 "OS" ),
		array( "240", "Aviateca", 			 "GU" ),
		array( "196", "Balkan Bulg'n", 		 "LZ" ),
		array( "125", "British Airways", 	 "BA" ),
		array( "604", "Cameroon Airlines", 	 "UY" ),
		array( "172", "Cargolux", 			 "CV" ),
		array( "160", "Cathay Pacific", 	 "CX" ),
		array( "005", "Continental", 		 "CO" ),
		array( "048", "Cyprus Airways", 	 "CY" ),
		array( "064", "Czeck Airlines", 	 "OK" ),
		array( "006", "Delta", 				 "DL" ),
		array( "114", "El Al", 				 "LY" ),
		array( "176", "Emirates", 			 "EK" ),
		array( "071", "Ethiopian", 			 "ET" ),
		array( "695", "EVA Airways", 		 "BR" ),
		array( "023", "Fedex", 				 "FX" ),
		array( "105", "Finnair", 			 "AY" ),
		array( "126", "Garuda Airlines", 	 "GA" ),
		array( "072", "Gulf Air", 			 "GF" ),
		array( "075", "Iberia", 			 "IB" ),
		array( "108", "Icelandair", 		 "FI" ),
		array( "096", "Iran Air", 			 "IR" ),
		array( "131", "JAL", 				 "JL" ),
		array( "234", "Japan Air System", 	 "JD" ),
		array( "180", "Korean Air", 		 "KE" ),
		array( "229", "Kuwait Airways", 	 "KU" ),
		array( "133", "LACSA", 				 "LR" ),
		array( "231", "Lauda Air", 			 "NG" ),
		array( "080", "LOT Polish Air", 	 "LO" ),
		array( "020", "Lufthansa", 			 "LH" ),
		array( "012", "Northwest", 			 "NW" ),
		array( "050", "Olympic Airways", 	 "OA" ),
		array( "214", "PIA", 				 "PK" ),
		array( "079", "Philippine Airlines", "PR" ),
		array( "672", "Royal Brunei Air", 	 "BI" ),
		array( "081", "Qantas", 			 "QF" ),
		array( "117", "SAS", 				 "SK" ),
		array( "065", "Saudia", 			 "SV" ),
		array( "618", "Singapore Airlines",  "SQ" ),
		array( "724", "Swiss", 				 "LX" ),
		array( "217", "Thai Airways", 		 "TG" ),
		array( "270", "Trans Med. Air", 	 "TL" ),
		array( "566", "Ukraine Intern'tl", 	 "PS" ),
		array( "016", "United Airlines", 	 "UA" ),
		array( "042", "Varig", 				 "RG" ),
		array( "738", "Vietnam Airlines", 	 "VN" )
	);
} // END OF CargoConnect

?>
