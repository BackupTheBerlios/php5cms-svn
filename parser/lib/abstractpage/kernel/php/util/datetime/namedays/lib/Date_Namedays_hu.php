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
|         Ferenc Raffael <raffaelf@interware.hu>                       |
+----------------------------------------------------------------------+
*/


using( 'util.datetime.namedays.lib.Date_Namedays' );


/**
 * @package util_datetime_namedays_lib
 */
 
class Date_Namedays_hu extends Date_Namedays
{
	/**
	 * Array of Namedays
	 *
	 * @var    array
	 * @access private
	 */
	var $_namedays = array(
		"1" => array(
			"1"  => array( "�j�v", "Fruzsina" ), 
			"2"  => "�bel", 
			"3"  => array( "Genov�va", "Benj�min" ), 
			"4"  => array( "Titusz", "Leona" ), 
			"5"  => "Simon", 
			"6"  => "Boldizs�r", 
			"7"  => array( "Attila", "Ram�na" ), 
			"8"  => "Gy�ngyv�r", 
			"9"  => "Marcell", 
			"10" => "Mel�nia", 
			"11" => "�gota", 
			"12" => "Ern�", 
			"13" => "Veronika", 
			"14" => "B�dog", 
			"15" => array( "L�r�nt", "L�r�nd" ), 
			"16" => "Guszt�v", 
			"17" => array( "Antal", "Ant�nia" ), 
			"18" => "Piroska", 
			"19" => array( "S�ra", "M�ri�" ), 
			"20" => array( "F�bi�n", "Sebesty�n" ), 
			"21" => "�gnes", 
			"22" => array( "Vince", "Art�r" ), 
			"23" => array( "Zelma", "Rajmund" ), 
			"24" => "Tim�t", 
			"25" => "P�l", 
			"26" => array( "Vanda", "Paula" ), 
			"27" => "Angelika", 
			"28" => array( "K�roly", "Karola" ), 
			"29" => "Ad�l",
			"30" => array( "Martina", "Gerda" ), 
			"31" => "Marcella"
		),
		"2" => array(
			"1"  => "Ign�c", 
			"2"  => array( "Karolina", "Aida" ), 
			"3"  => "Bal�zs", 
			"4"  => array( "R�hel", "Csenge" ), 
			"5"  => array( "�gota", "Ingrid" ), 
			"6"  => array( "Dorottya", "D�ra" ), 
			"7"  => array( "T�dor", "R�me�" ), 
			"8"  => "Aranka", 
			"9"  => array( "Abig�l", "Alex" ), 
			"10" => "Elvira", 
			"11" => array( "Bertold", "Marietta" ), 
			"12" => array( "L�dia", "L�via" ), 
			"13" => array( "Ella", "Linda" ), 
			"14" => array( "Valentin nap", "B�lint" ), 
			"15" => array( "Kolos", "Georgina" ), 
			"16" => array( "Julianna", "Lilla" ), 
			"17" => "Don�t", 
			"18" => "Bernadett", 
			"19" => "Zsuzsanna", 
			"20" => array( "Alad�r", "�lmos" ), 
			"21" => "Eleon�ra", 
			"22" => "Gerzson", 
			"23" => "Alfr�d", 
			"24" => "Sz�k�nap", 
			"25" => "M�ty�s", 
			"26" => "G�za", 
			"27" => "Edina", 
			"28" => array( "�kos", "B�tor" ), 
			"29" => "Elem�r"
		), 
		"3" => array(
			"1"  => "Albin", 
			"2"  => "Lujza", 
			"3"  => "Korn�lia", 
			"4"  => "K�zm�r", 
			"5"  => array( "Adorj�n", "Adri�n" ), 
			"6"  => array( "Leon�ra", "Inez" ), 
			"7"  => "Tam�s", 
			"8"  => array( "N�nap", "Zolt�n" ), 
			"9"  => array( "Franciska", "Fanni" ), 
			"10" => "Ildik�", 
			"11" => "Szil�rd", 
			"12" => "Gergely", 
			"13" => array( "Kriszti�n", "Ajt�ny" ), 
			"14" => "Matild", 
			"15" => array( "Nemzeti �nnep", "Krist�f" ), 
			"16" => "Henrietta", 
			"17" => array( "Gertr�d", "Patrik" ), 
			"18" => array( "S�ndor", "Ede" ), 
			"19" => array( "J�zsef", "B�nk" ), 
			"20" => "Klaudia", 
			"21" => "Benedek", 
			"22" => array( "Be�ta", "Izolda" ), 
			"23" => "Em�ke", 
			"24" => array( "G�bor", "Karina" ), 
			"25" => array( "Ir�n", "�risz" ), 
			"26" => "Em�nuel", 
			"27" => "Hajnalka", 
			"28" => array( "Gedeon", "Johanna" ), 
			"29" => "Auguszta", 
			"30" => "Zal�n", 
			"31" => "�rp�d"
		), 
		"4" => array(
			"1"  => "Hug�", 
			"2"  => "�ron", 
			"3"  => array( "Buda", "Rich�rd" ), 
			"4"  => "Izidor", 
			"5"  => "Vince", 
			"6"  => array( "Vilmos", "B�borka" ), 
			"7"  => "Herman", 
			"8"  => "D�nes", 
			"9"  => "Erhard", 
			"10" => "Zsolt", 
			"11" => array( "Le�", "Szaniszl�" ), 
			"12" => "Gyula", 
			"13" => "Ida", 
			"14" => "Tibor", 
			"15" => array( "Anaszt�zia", "Tas" ), 
			"16" => "Csongor", 
			"17" => "Rudolf", 
			"18" => array( "Andrea", "Ilma" ), 
			"19" => "Emma", 
			"20" => "Tivadar", 
			"21" => "Konr�d", 
			"22" => array( "Csilla", "No�mi" ), 
			"23" => "B�la", 
			"24" => "Gy�rgy", 
			"25" => "M�rk", 
			"26" => "Ervin", 
			"27" => array( "Zita", "Mariann" ), 
			"28" => "Val�ria", 
			"29" => "P�ter", 
			"30" => array( "Katalin", "Kitti" )
		), 
		"5" => array(
			"1"  => array( "Munka �nnepe", "F�l�p", "Jakab" ), 
			"2"  => "Zsigmond", 
			"3"  => array( "T�mea", "Irma" ), 
			"4"  => array( "M�nika", "Fl�ri�n" ), 
			"5"  => "Gy�rgyi", 
			"6"  => array( "Ivett", "Frida" ), 
			"7"  => "Gizella", 
			"8"  => "Mih�ly", 
			"9"  => "Gergely",
			"10" => array( "�rmin", "Palma" ), 
			"11" => "Ferenc", 
			"12" => "Pongr�c", 
			"13" => array( "Szerv�c", "Imola" ), 
			"14" => "Bonif�c", 
			"15" => array( "Zs�fia", "Szonja" ), 
			"16" => array( "M�zes", "Botond" ), 
			"17" => "Paszk�l", 
			"18" => array( "Erik", "Alexandra" ), 
			"19" => array( "Iv�", "Mil�n" ), 
			"20" => array( "Bern�t", "Fel�cia" ), 
			"21" => "Konstantin", 
			"22" => array( "J�lia", "Rita" ), 
			"23" => "Dezs�", 
			"24" => array( "Eszter", "Eliza" ), 
			"25" => "Orb�n", 
			"26" => array( "F�l�p", "Evelin" ), 
			"27" => "Hella", 
			"28" => array( "Emil", "Csan�d" ), 
			"29" => "Magdolna", 
			"30" => array( "Janka", "Zsanett" ), 
			"31" => array( "Ang�la", "Petronella" )
		), 
		"6" => array(
			"1"  => "T�nde", 
			"2"  => array( "K�rmen", "Anita" ), 
			"3"  => "Klotild", 
			"4"  => "Bulcs�", 
			"5"  => "Fatime", 
			"6"  => array( "Norbert", "Cintia" ), 
			"7"  => "R�bert", 
			"8"  => "Med�rd", 
			"9"  => "F�lix", 
			"10" => array( "Margit", "Gr�ta" ), 
			"11" => "Barnab�s", 
			"12" => "Vill�", 
			"13" => array( "Antal", "Anett" ), 
			"14" => "Vazul", 
			"15" => array( "Jol�n", "Vid" ), 
			"16" => "Jusztin", 
			"17" => array( "Laura", "Alida" ), 
			"18" => array( "Arnold", "Levente" ), 
			"19" => "Gy�rf�s", 
			"20" => "Rafael", 
			"21" => array( "Alajos", "Leila" ), 
			"22" => "Paulina", 
			"23" => "Zolt�n", 
			"24" => "Iv�n", 
			"25" => "Vilmos", 
			"26" => array( "J�nos", "P�l" ), 
			"27" => "L�szl�", 
			"28" => array( "Levente", "Ir�n" ), 
			"29" => array( "P�ter", "P�l" ), // ? 
			"30" => "P�l"
		), 
		"7" => array(
			"1"  => array( "Tiham�r", "Annam�ria" ), 
			"2"  => "Ott�", 
			"3"  => array( "Korn�l", "Soma" ), 
			"4"  => "Ulrik", 
			"5"  => array( "Emese", "Sarolta" ), 
			"6"  => "Csaba", 
			"7"  => "Apoll�nia", 
			"8"  => "Ell�k", 
			"9"  => "Lukr�cia", 
			"10" => "Am�lia", 
			"11" => array( "M�ra", "Lili" ), 
			"12" => array( "Izabella", "Dalma" ), 
			"13" => "Jen�", 
			"14" => array( "�rs", "Stella" ), 
			"15" => array( "Henrik", "Roland" ), 
			"16" => "Valter", 
			"17" => array( "Endre", "Elek" ), 
			"18" => "Frigyes", 
			"19" => "Em�lia", 
			"20" => "Ill�s", 
			"21" => array( "D�niel", "Daniella" ), 
			"22" => "Magdolna", 
			"23" => "Lenke", 
			"24" => array( "Kinga", "Kics�" ), 
			"25" => array( "Krist�f", "Jakab" ), 
			"26" => array( "Anna", "Anik�" ), 
			"27" => array( "Olga", "Lili�na" ), 
			"28" => "Szabolcs", 
			"29" => array( "M�rta", "Fl�ra" ), 
			"30" => array( "Judit", "X�nia" ), 
			"31" => "Oszk�r"
		), 
		"8" => array(
			"1"  => "Bogl�rka", 
			"2"  => "Lehel", 
			"3"  => "Hermina", 
			"4"  => array( "Domonkos", "Dominika" ), 
			"5"  => "Krisztina", 
			"6"  => array( "Berta", "Bettina" ), 
			"7"  => "Ibolya", 
			"8"  => "L�szl�", 
			"9"  => "Em�d", 
			"10" => "L�rinc", 
			"11" => array( "Zsuzsanna", "Tiborc" ), 
			"12" => "Kl�ra", 
			"13" => "Ipoly", 
			"14" => "Marcell", 
			"15" => "M�ria", 
			"16" => "�brah�m", 
			"17" => "J�cint", 
			"18" => "Ilona", 
			"19" => "Huba", 
			"20" => array( "Szt Istv�n �s az �llamalap�t�s �nnepe", "Vajk" ), 
			"21" => array( "Samuel", "Hajna" ), 
			"22" => array( "Menyh�rt", "Mirjam" ), 
			"23" => "Bence", 
			"24" => "Bertalan", 
			"25" => array( "Lajos", "Patr�cia" ), 
			"26" => "Izs�", 
			"27" => "G�sp�r", 
			"28" => "�goston", 
			"29" => array( "Beatrix", "Erna" ), 
			"30" => "R�zsa", 
			"31" => array( "Erika", "Bella" )
		), 
		"9" => array(
			"1"  => array( "Egyed", "Egon" ), 
			"2"  => array( "Rebeka", "Dorina" ), 
			"3"  => "Hilda", 
			"4"  => "Roz�lia", 
			"5"  => array( "Viktor", "L�rinc" ), 
			"6"  => "Zakari�s", 
			"7"  => "Regina", 
			"8"  => array( "M�ria", "Adrienn" ), 
			"9"  => "�d�m", 
			"10" => array( "Nikolett", "Hunor" ), 
			"11" => "Teod�ra", 
			"12" => "M�ria", 
			"13" => "Korn�l", 
			"14" => array( "Szer�na", "Rox�na" ), 
			"15" => array( "Enik�", "Melitta" ), 
			"16" => "Edit", 
			"17" => "Zs�fia", 
			"18" => "Di�na", 
			"19" => "Vilhelmina", 
			"20" => "Friderika", 
			"21" => array( "M�t�", "Mirella" ), 
			"22" => "M�ric", 
			"23" => "Tekla", 
			"24" => array( "Gell�rt", "Merc�desz" ), 
			"25" => array( "Eufrozina", "Kende" ), 
			"26" => "Jusztina", 
			"27" => "Adalbert", 
			"28" => "Vencel", 
			"29" => "Mih�ly", 
			"30" => "Jeromos"
		), 
		"10" => array(
			"1"  => "Malvin", 
			"2"  => "Petra", 
			"3"  => "Helga", 
			"4"  => "Ferenc", 
			"5"  => "Aur�l", 
			"6"  => array( "Br�n�", "Ren�ta" ), 
			"7"  => "Am�lia", 
			"8"  => "Kopp�ny", 
			"9"  => "D�nes", 
			"10" => "Gedeon", 
			"11" => array( "Brigitta", "Gitta" ), 
			"12" => "Miksa", 
			"13" => array( "K�lm�n", "Ede" ), 
			"14" => "Hel�n", 
			"15" => "Ter�z", 
			"16" => "G�l", 
			"17" => "Hedvig", 
			"18" => "Luk�cs", 
			"19" => "N�ndor", 
			"20" => "Vendel", 
			"21" => "Orsolya", 
			"22" => "El�d", 
			"23" => array( "Nemzeti �nnep", "Gy�ngyi" ), 
			"24" => "Salamon", 
			"25" => array( "Blanka", "Bianka" ), 
			"26" => "D�m�t�r", 
			"27" => "Szabina", 
			"28" => array( "Simon", "Szimonetta" ), 
			"29" => "N�rcisz", 
			"30" => "Alfonz", 
			"31" => "Farkas"
		), 
		"11" => array(
			"1"  => "Marianna", 
			"2"  => "Achilles", 
			"3"  => "Gy�z�", 
			"4"  => "K�roly", 
			"5"  => "Imre", 
			"6"  => "L�n�rd", 
			"7"  => "Rezs�", 
			"8"  => "Zsombor", 
			"9"  => "Tivadar", 
			"10" => "R�ka", 
			"11" => "M�rton", 
			"12" => array( "J�n�s", "Ren�t�" ), 
			"13" => "Szilvia", 
			"14" => "Aliz", 
			"15" => array( "Albert", "Lip�t" ), 
			"16" => "�d�n", 
			"17" => array( "Hortenzia", "Gerg�" ), 
			"18" => "Jen�", 
			"19" => array( "Erzs�bet", "Zs�ka" ), 
			"20" => "Jol�n", 
			"21" => "Oliv�r", 
			"22" => "Cec�lia", 
			"23" => array( "Kelemen", "Klementina" ), 
			"24" => "Emma",
			"25" => "Katalin", 
			"26" => "Vir�g", 
			"27" => "Virgil", 
			"28" => "Stef�nia", 
			"29" => "Taksony", 
			"30" => array( "Andr�s", "Andor" )
		), 
		"12" => array(
			"1"  => "Elza", 
			"2"  => array( "Malinda", "Vivien" ), 
			"3"  => array( "Ferenc", "Ol�via" ), 
			"4"  => array( "Borb�la", "Barbara" ), 
			"5"  => "Vilma", 
			"6"  => "Mikl�s", 
			"7"  => "Ambrus", 
			"8"  => "M�ria", 
			"9"  => "Nat�lia", 
			"10" => "Judit", 
			"11" => "�rp�d", 
			"12" => "Gabriella", 
			"13" => array( "Luca", "Ott�lia" ), 
			"14" => "Szil�rda", 
			"15" => "Val�r", 
			"16" => array( "Etelka", "Aletta" ), 
			"17" => array( "L�z�r", "Olimpia" ), 
			"18" => "Auguszta", 
			"19" => "Viola", 
			"20" => "Teofil", 
			"21" => "Tam�s", 
			"22" => "Z�n�", 
			"23" => "Vikt�ria", 
			"24" => array( "Szenteste", "�d�m", "�va" ), 
			"25" => array( "Kar�csony", "Eug�nia" ), 
			"26" => array( "Kar�csony", "Istv�n" ), 
			"27" => "J�nos", 
			"28" => "Kamilla", 
			"29" => array( "Tam�s", "Tamara" ), 
			"30" => "D�vid", 
			"31" => "Szilveszter"
		)
	); 
	
	 
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Date_Namedays_hu( $params = array() )
	{
		$this->Date_Namedays( $params );
	}
} // END OF Date_Namedays_hu

?>
