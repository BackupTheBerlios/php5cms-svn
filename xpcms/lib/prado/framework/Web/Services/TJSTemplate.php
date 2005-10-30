<?php

/**
 *
 * @author $Author: weizhuo $
 * @version $Id: TJSTemplate.php,v 1.1 2005/03/11 11:35:58 weizhuo Exp $
 */
class TJSTemplate extends TControl 
{
	const JS = 'tp_template.js';

	/**
	 *
	 */
	function onPreRender($param) 
	{
		parent::onPreRender($param);
		$page=$this->getPage();
		if(!$page->isScriptFileRegistered('TJSTemplate'))
		{
			$scriptFile=$this->Application->getResourceLocator()->getJsPath().'/'.self::JS;
			$page->registerScriptFile('TJSTemplate',$scriptFile);
		}		
	}

	/**
	 *
	 */
	function renderBody() 
	{
		$contents = trim(parent::renderBody());
		$contents = addslashes($contents);
		$lines = preg_split("/\n|\r/", $contents);
		$jslines = '';
		foreach($lines as $line)
		{
			if(!empty($line))
				$jslines .= "\"$line\" + \n";
		}
		$jslines .= "\"\"";
		//var_dump($jslines);
		$id = $this->ID;
		$value = "{ '$id' : $jslines }";

		$this->Page->registerArrayDeclaration('TJSTemplate', $value);

		$script = '
		function findTemplateById(name) 
		{
			for(i in TJSTemplate)
			{
				for(index in TJSTemplate[i])
				{
					if(index == name)
						return TJSTemplate[i][index];
				}
			}
			return null;
		}';

		$this->Page->registerEndScript('TJSTemplate', $script);

		return;
	}
}

?>