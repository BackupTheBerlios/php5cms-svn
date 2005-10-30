<?php

class TAJAXScript extends TControl
{
	/**
	 *
	 */
	function onPreRender($param) 
	{
		parent::onPreRender($param);
		$page=$this->getPage();
		if(!$page->isScriptFileRegistered('TAJAXScript'))
		{
			$scriptFile = $_SERVER['SCRIPT_NAME'].'?__AJAX&client';
			$page->registerScriptFile('TAJAXScript',$scriptFile);
		}		
	}

	function renderBody() 
	{
		$contents = trim(parent::renderBody());
		$this->Page->registerEndScript('TAJAXScript',$contents);
		return;
	}

}

?>