<?php
/**
 * TListBox class file
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * Copyright(c) 2004 by Qiang Xue. All rights reserved.
 *
 * To contact the author write to {@link mailto:qiang.xue@gmail.com Qiang Xue}
 * The latest version of PRADO can be obtained from:
 * {@link http://prado.sourceforge.net/}
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Marcus Nyeholt <tanus@users.sourceforge.net>
 * @version $Revision: 1.13 $  $Date: 2005/01/19 15:11:09 $
 * @package System.Web.UI.WebControls
 */

/**
 * TListBox class
 *
 * TListBox create a list box that allows single or multiple selection.
 *
 * Namespace: System.Web.UI.WebControls
 *
 * Properties
 * - <b>Rows</b>, integer, default=4, kept in viewstate
 *   <br>Gets or sets the number of rows displayed in the TListBox component.
 * - <b>SelectionMode</b>, string, default=Single, kept in viewstate
 *   <br>Gets or sets the selection mode (Single, Multiple) of the TListBox component.
 *
 * Example (template)
 * <code>
 *  <com:TListBox SelectionMode="Multiple">
 *    <com:TListItem Text="item1" Value="value1" />
 *    <com:TListItem Text="item2" Value="value2" />
 *    <com:TListItem Text="item3" Value="value3" />
 *  </com:TListBox>
 * </code>
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Marcus Nyeholt <tanus@users.sourceforge.net>
 * @version $Revision: 1.13 $  $Date: 2005/01/19 15:11:09 $
 * @package System.Web.UI.WebControls
 */
class TListBox extends TListControl
{
	/**
	 * Sets the HTML tag displaying the listbox.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTagName('select');
	}
	
	/**
	 * @return integer the number of rows to be displayed in the component
	 */
	public function getRows()
	{
		return $this->getViewState('Rows', 4);
	}
	
	/**
	 * Sets the number of rows to be displayed in the component
	 * @param integer the number of rows
	 */
	public function setRows($value)
	{
		if($value<=0)
			$value=4;
		$this->setViewState('Rows', $value, 4);
	}
	
	/**
	 * @return string the selection mode (Single, Multiple )
	 */
	public function getSelectionMode()
	{
		return $this->getViewState('SelectionMode', 'Single');
	}
	
	/**
	 * Sets the selection mode of the component (Single, Multiple)
	 * @param string the selection mode
	 */
	function setSelectionMode($value)
	{
		if($value!=='Multiple')
			$value='Single';
		$this->setViewState('SelectionMode',$value,'Single');
	}
	
	/**
	 * Returns the attributes to be rendered.
	 * This method overrides the parent's implementation to add checking
	 * for rows and the multiple flag.
	 * @return ArrayObject attributes to be rendered
	 */
	protected function getAttributesToRender()
	{
		$attributes=parent::getAttributesToRender();
		if($this->getSelectionMode() == 'Multiple')
		{
			$attributes['multiple']='multiple';
			$attributes['name']=$this->getUniqueID().'[]';
		}
		else
			$attributes['name']=$this->getUniqueID();
		if($this->isAutoPostBack())
			$attributes['onchange']='javascript:'.$this->getPage()->getPostBackClientEvent($this,'');
		$attributes['size']=$this->getRows();
		return $attributes;
	}
	
	/**
	 * Renders the list as an HTML select element.
	 * @return string the rendering result
	 */
	protected function renderBody()
	{
		$content="\n";
		$formatString = $this->getDataTextFormatString();
		foreach($this->getItems() as $item)
		{
			$text=$item->getText();
			if(strlen($formatString))
				$text=sprintf($formatString,$text);
			if($this->isEncodeText())
				$text=pradoEncodeData($text);
			$value=$item->getValue();
			if($item->isSelected())
				$content.='<option value="'.$value.'" selected="selected">'.$text."</option>\n";
			else
				$content.='<option value="'.$value.'">'.$text."</option>\n";
		}
		return $content;
	}
}

?>