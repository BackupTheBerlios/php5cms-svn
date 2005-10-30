<?php
/**
 * Project description.
 * @author $Author: weizhuo $
 * @version $Id: CustomValidator.php,v 1.1 2005/06/13 07:04:30 weizhuo Exp $
 * @package DefaultPackage
 */

using('System.Web.Services');

/**
 *
 * @author $Author: weizhuo $
 * @version $Id: CustomValidator.php,v 1.1 2005/06/13 07:04:30 weizhuo Exp $
 */
class CustomValidator extends TPageWithCallback implements ICallbackEventHandler
{
	/**
	 *
	 */
	function CustomValidation($sender, $params) 
	{
		$params->isValid = $this->customIsValid();
	}

	function raiseCallbackEvent($param) 
	{
		return $this->customIsValid();
	}

	function customIsValid()
	{
		return $this->text1->Text == "Wei";
	}

	function onPreRender($param) 
	{
		parent::onPreRender($param);
		$ref = $this->getCallbackEventReference($this, null, 
						"CustomValidationOnReturn.bind(sender)");
		$script = "
			function CustomValidation(sender, value)
			{
				{$ref}
				return false;
			}

			function CustomValidationOnReturn(result)
			{
				this.setValid(result);
			}
		";

		$this->registerEndScript($this->ClientID, $script);
	}
}

?>