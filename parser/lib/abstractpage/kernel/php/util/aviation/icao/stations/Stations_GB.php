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


using( 'util.aviation.icao.stations.Stations' );


/**
 * @package util_aviation_icao_stations
 */
 
class Stations_GB extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_GB()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'United Kingdom';

		$this->icaos = array(
			'EGPD' => 'Aberdeen/Dyce',
			'EGUC' => 'Aberporth',
			'EGWZ' => 'Alconbury Royal Air Force Base',
			'EGJA' => 'Alderney/Channel Island',
			'EGSL' => 'Andrewsfield',
			'EGTA' => 'Aylesbury/Thame',
			'EGYE' => 'Barkston Heath',
			'EGNL' => 'Barrow/Walney Island',
			'EGSM' => 'Beccles ( Ellough )',
			'EGSB' => 'Bedford/Castle Mill',
			'EGSV' => 'Bedford/Thurleigh',
			'EGAA' => 'Belfast/Aldergrove Airport',
			'EGAC' => 'Belfast/Harbour',
			'EGHJ' => 'Bembridge',
			'EGPL' => 'Benbecula',
			'EGUB' => 'Benson',
			'EGKB' => 'Biggin Hill',
			'EGBB' => 'Birmingham/Airport',
			'EGLK' => 'Blackbushe',
			'EGNH' => 'Blackpool Airport',
			'EGLA' => 'Bodmin',
			'EGKC' => 'Bognor Regis',
			'EGDM' => 'Boscombe Down',
			'EGQM' => 'Boulmer',
			'EGSN' => 'Bourn',
			'EGHH' => 'Bournemouth Airport',
			'EGRR' => 'Bracknell/Beaufort Park',
			'EGDA' => 'Brawdy',
			'EGGD' => 'Bristol/Lulsgate',
			'EGVN' => 'Brize Norton',
			'EGNB' => 'Brough',
			'EGCK' => 'Caernarfon',
			'EGSC' => 'Cambridge',
			'EGFF' => 'Cardiff-Wales Airport',
			'EGFC' => 'Cardiff/tremorfa Heliport',
			'EGNC' => 'Carlisle',
			'EGKE' => 'Challock',
			'EGHR' => 'Chichester/Goodwood',
			'EGDC' => 'Chivenor',
			'EGXG' => 'Church Fenton',
			'EGSW' => 'Clacton',
			'EGUO' => 'Colerne',
			'EGYC' => 'Coltishall',
			'EGHA' => 'Compton Abbas',
			'EGXC' => 'Coningsby',
			'EGWC' => 'Cosford',
			'EGXJ' => 'Cottesmore',
			'EGBE' => 'Coventry Airport',
			'EGTC' => 'Cranfield',
			'EGYD' => 'Cranwell',
			'EGSO' => 'Crowfield',
			'EGDR' => 'Culdrose',
			'EGLD' => 'Denham',
			'EGBD' => 'Derby',
			'EGXD' => 'Dishforth',
			'EGPN' => 'Dundee/Riverside',
			'EGTD' => 'Dunsfold',
			'EGSU' => 'Duxford',
			'EGSR' => 'Earls Colne',
			'EGNX' => 'East Midlands',
			'EGPH' => 'Edinburgh Airport',
			'EGAE' => 'Eglinton/Londonderr',
			'EGTR' => 'Elstree',
			'EGTE' => 'Exeter Airport',
			'EGVA' => 'Fairford',
			'EGTF' => 'Fairoaks',
			'EGLF' => 'Farnborough',
			'EGUF' => 'Farnborough Military',
			'EGCL' => 'Fenland',
			'EGTG' => 'Filton/Bristol',
			'EGXI' => 'Finningley',
			'EGMA' => 'Fowlmere',
			'EGPF' => 'Glasgow Airport',
			'EGSD' => 'Great Yarmouth/North Denes',
			'EGJB' => 'Guernsey Airport',
			'EGBO' => 'Halfpenny Green',
			'EGWN' => 'Halton',
			'EGTH' => 'Hatfield',
			'EGFE' => 'Haverfordwest',
			'EGNR' => 'Hawarden',
			'EGSK' => 'Hethel',
			'EGYH' => 'Holbeach',
			'EGXH' => 'Honington',
			'EGNA' => 'Hucknall',
			'EGNJ' => 'Humberside',
			'EGPE' => 'Inverness/Dalcross',
			'EGSE' => 'Ipswich',
			'EGNS' => 'Isle Of Man/Ronaldsway Airport',
			'EGHN' => 'Isle Of Wight/Sandown',
			'EGJJ' => 'Jersey Airport',
			'EGDK' => 'Kemble',
			'EGQK' => 'Kinloss',
			'EGPA' => 'Kirkwall Airport',
			'EGUL' => 'Lakenheath',
			'EGHC' => 'Lands End/St Just',
			'EGHL' => 'Lasham',
			'EGKH' => 'Lashenden/Headcorn',
			'EGTI' => 'Leavesden',
			'EGXV' => 'Leconfield',
			'EGUS' => 'Lee On Solent',
			'EGNM' => 'Leeds And Bradford',
			'EGXE' => 'Leeming',
			'EGBG' => 'Leicester',
			'EGQL' => 'Leuchars',
			'EGXU' => 'Linton-On-Ouse',
			'EGGP' => 'Liverpool/John Lennon Airport',
			'EGOD' => 'Llanbedr',
			'EGKK' => 'London/Gatwick Airport',
			'EGLL' => 'London/Heathrow Airport',
			'EGLW' => 'London/Westland Heliport',
			'EGLC' => 'London City Airport',
			'EGRB' => 'London Weather Centre',
			'EGQS' => 'Lossiemouth',
			'EGGW' => 'Luton Airport',
			'EGMD' => 'Lydd Airport',
			'EGDL' => 'Lyneham',
			'EGQJ' => 'Machrihanish',
			'EGCB' => 'Manchester/Barton',
			'EGCC' => 'Manchester Airport',
			'EGMH' => 'Manston Civil',
			'EGUM' => 'Manston Military',
			'EGYM' => 'Marham',
			'EGDW' => 'Merryfield',
			'EGVP' => 'Middle Wallop',
			'EGUN' => 'Mildenhall',
			'EGOQ' => 'Mona',
			'EGDN' => 'Netheravon',
			'EGNF' => 'Netherthorpe',
			'EGNT' => 'Newcastle',
			'EGXN' => 'Newton',
			'EGSX' => 'North Weald',
			'EGBK' => 'Northampton/Sywell',
			'EGWU' => 'Northolt',
			'EGSH' => 'Norwich Weather Centre',
			'EGBN' => 'Nottingham',
			'EGTW' => 'Oaksey Park',
			'EGVO' => 'Odiham',
			'EGLS' => 'Old Sarum',
			'EGTK' => 'Oxford/Kidlington',
			'EGLG' => 'Panshanger',
			'EGOP' => 'Pembrey Sands',
			'EGHK' => 'Penzance Heliport',
			'EGTP' => 'Perranporth',
			'EGSF' => 'Peterborough/Conington',
			'EGSP' => 'Peterborough/Sibson',
			'EGDB' => 'Plymouth',
			'EGHD' => 'Plymouth/Roborough',
			'EGHP' => 'Popham',
			'EGDP' => 'Portland/Rnas',
			'EGDO' => 'Predannack',
			'EGPK' => 'Prestwick Airport',
			'EGKR' => 'Redhill',
			'EGNE' => 'Retford/Gamston',
			'EGTO' => 'Rochester',
			'EGDG' => 'Saint Mawgan',
			'EGNG' => 'Samlesbury',
			'EGCF' => 'Sandtoft',
			'EGXP' => 'Scampton',
			'EGPM' => 'Scatsa/Shetland Island',
			'EGHE' => 'Scilly, Saint Mary\'S',
			'EGSJ' => 'Seething',
			'EGOS' => 'Shawbury',
			'EGSY' => 'Sheffield City',
			'EGCJ' => 'Sherburn-In-Elmet',
			'EGSA' => 'Shipdham',
			'EGBS' => 'Shobdon',
			'EGKA' => 'Shoreham Airport',
			'EGCV' => 'Sleap',
			'EGHI' => 'Southampton/Weather Centre',
			'EGMC' => 'Southend-On-Sea',
			'EGOM' => 'Spadeadam',
			'EGDX' => 'St Athan',
			'EGSS' => 'Stansted Airport',
			'EGSG' => 'Stapleford',
			'EGBJ' => 'Staverton Private',
			'EGPO' => 'Stornoway',
			'EGCS' => 'Sturgate',
			'EGPB' => 'Sumburgh Cape',
			'EGFH' => 'Swansea',
			'EGQA' => 'Tain Range',
			'EGBM' => 'Tatenhill',
			'EGNV' => 'Tees-Side',
			'EGOE' => 'Ternhill',
			'EGHO' => 'Thruxton',
			'EGPU' => 'Tiree',
			'EGXZ' => 'Topcliffe',
			'EGBT' => 'Turweston',
			'EGPW' => 'Unst Island',
			'EGDJ' => 'Upavon',
			'EGOV' => 'Valley',
			'EGXW' => 'Waddington',
			'EGYW' => 'Wainfleet',
			'EGNO' => 'Warton',
			'EGUW' => 'Wattisham',
			'EGBW' => 'Wellesbourne Mountford',
			'EGCW' => 'Welshpool',
			'EGOY' => 'West Freugh',
			'EGLM' => 'White Waltham',
			'EGPC' => 'Wick',
			'EGNW' => 'Wickenby',
			'EGXT' => 'Wittering',
			'EGCD' => 'Woodford',
			'EGOW' => 'Woodvale',
			'EGDT' => 'Wroughton',
			'EGTB' => 'Wycombe Air Park/Booker',
			'EGUY' => 'Wyton',
			'EGHG' => 'Yeovil/Westland',
			'EGDY' => 'Yeovilton'
		);
	}
} // END OF Stations_GB

?>
