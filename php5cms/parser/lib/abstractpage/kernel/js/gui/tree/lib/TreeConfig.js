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
 * Constructor
 *
 * @access public
 */
TreeConfig = function()
{
	this.Base = Base;
	this.Base();
};


TreeConfig.prototype = new Base();
TreeConfig.prototype.constructor = TreeConfig;
TreeConfig.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
TreeConfig.rootIcon = 'img/folder.png';

/**
 * @access public
 * @static
 */
TreeConfig.openRootIcon = 'img/openfolder.png';

/**
 * @access public
 * @static
 */
TreeConfig.folderIcon = 'img/folder.png';

/**
 * @access public
 * @static
 */
TreeConfig.openFolderIcon = 'img/openfolder.png';

/**
 * @access public
 * @static
 */
TreeConfig.fileIcon = 'img/file.png';

/**
 * @access public
 * @static
 */
TreeConfig.iIcon = 'img/i.png';

/**
 * @access public
 * @static
 */
TreeConfig.lIcon = 'img/l.png';

/**
 * @access public
 * @static
 */
TreeConfig.lMinusIcon = 'img/l_minus.png';

/**
 * @access public
 * @static
 */
TreeConfig.lPlusIcon = 'img/l_plus.png';

/**
 * @access public
 * @static
 */
TreeConfig.tIcon = 'img/t.png';

/**
 * @access public
 * @static
 */
TreeConfig.tMinusIcon = 'img/t_minus.png';

/**
 * @access public
 * @static
 */
TreeConfig.tPlusIcon = 'img/t_plus.png';

/**
 * @access public
 * @static
 */
TreeConfig.blankIcon = 'img/blank.png';

/**
 * @access public
 * @static
 */
TreeConfig.shortcutIcon = 'img/blank.png';

/**
 * @access public
 * @static
 */
TreeConfig.defaultText = 'Tree Item';

/**
 * @access public
 * @static
 */
TreeConfig.defaultAction = 'void(0);';

/**
 * @access public
 * @static
 */
TreeConfig.defaultBehavior = 'classic';

/**
 * @access public
 * @static
 */
TreeConfig.shortcutMode = false;

/**
 * @access public
 * @static
 */
TreeConfig.allowKeyboardNavigation = true;

/**
 * @access public
 * @static
 */
TreeConfig.enableContextMenu = true;

/**
 * @access public
 * @static
 */
TreeConfig.contextMenuCallbackFn = new Function;

/**
 * @access public
 * @static
 */
TreeConfig.loadingText = 'Loading...';

/**
 * @access public
 * @static
 */
TreeConfig.loadErrorTextTemplate = 'Error loading "%1%"';

/**
 * @access public
 * @static
 */
TreeConfig.emptyErrorTextTemplate = 'Error "%1%" does not contain any tree items';
