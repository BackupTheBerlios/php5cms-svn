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


using( 'locale.stopwords.lib.StopWords' );


/**
 * Russian stopwords.
 *
 * Charset:   koi8-r
 * Language:  ru
 *
 * @package locale_stopwords_lib
 */
 
class StopWords_ru extends StopWords
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StopWords_ru()
	{
		$this->_populate();
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */	
	function _populate()
	{
		$this->words = array(
			"а",
			"без",
			"более",
			"бы",
			"был",
			"была",
			"были",
			"было",
			"быть",
			"в",
			"вам",
			"вас",
			"весь",
			"во",
			"вот",
			"все",
			"всего",
			"всех",
			"вы",
			"где",
			"да",
			"даже",
			"для",
			"до",
			"его",
			"ее",
			"если",
			"есть",
			"еще",
			"же",
			"за",
			"здесь",
			"и",
			"из",
			"или",
			"им",
			"их",
			"к",
			"как",
			"ко",
			"когда",
			"кто",
			"ли",
			"либо",
			"мне",
			"может",
			"мы",
			"на",
			"надо",
			"наш",
			"не",
			"него",
			"нее",
			"нет",
			"ни",
			"них",
			"но",
			"ну",
			"о",
			"об",
			"однако",
			"он",
			"она",
			"они",
			"оно",
			"от",
			"очень",
			"по",
			"под",
			"при",
			"с",
			"со",
			"так",
			"также",
			"такой",
			"там",
			"те",
			"тем",
			"то",
			"того",
			"тоже",
			"той",
			"только",
			"том",
			"ты",
			"у",
			"уже",
			"хотя",
			"чего",
			"чей",
			"чем",
			"что",
			"чтобы",
			"чье",
			"чья",
			"эта",
			"эти",
			"это",
			"я"
		);
	}
} // END OF StopWords_ru

?>
