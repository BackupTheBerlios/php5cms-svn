<?php
/**
 * TPageWithCallback class file.
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
 * @author Xiang Wei Zhuo <weizhuo[at]gmail.com>
 * @version v1.0, last update on Mon May 02 21:22:05 EST 2005
 * @package System.Web.UI
 */

/**
 * A Page than can handle components with callbacks. 
 * 
 * Any component that implements ICallbackEventHandler interface must sit 
 * within a page that extends TPageWithCallback. This type of page with 
 * implement a different page life cycle if the request is a callback. 
 *
 * Namespace: System.Web.UI
 *
 * Properties
 * - <b>IsCallBack</b>, boolean, read-only
 *   <br>Gets the value that indicates whether the current request is a
 *      client callback.
 * - <b>IsServiceRequest</b>, boolean, read-only
 *   <br>Gets the value that indicates the page is requested for service.
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail.com>
 * @version v1.0, last update on Mon May 02 21:22:05 EST 2005
 * @package System.Web.Services
 */
class TPageWithCallback extends TPage
{
	/**
	 * AJAX service provider, JPSpan
	 * @var TService_Callback 
	 */
	protected $service;
	
	/**
	 * A list of callback candiates, controls that implement ICallbackEventHandler.
	 * @var array 
	 */
	protected $callbacksCandidates = array();
	
	/**
	 * A list of control that implement IPostBackDataHandler.
	 * @var type 
	 */
	protected $postDataContainerIDs = array();
	
	/**
	 * Initialize the page.
	 * Init the service manager, add callback service.
	 * Create the javascript href (URL) required for callback.
	 * @param TEventParameter init parameters
	 */
	function onInit($param)
	{
		$module = $this->Module;
		if(is_null($module)) 
			$url = 'page='.get_class($this).'&';
		else
			$url = 'page='.$module->getID().':'.get_class($this).'&';

		$this->service =  new TService_Callback($url);

		if($this->isServiceRequest())
		{
			$serviceManager = $this->Application->getServiceManager();
			$serviceManager->addService(TService_Callback::service,$this->service);
		}
		parent::onInit($param);
	}
	
	/**
	 * Register the callback script file.
	 * @param TEventParameter pre-render parameters
	 */
	function onPreRender($param)
	{
		if(!empty($this->callbacksCandidates) 
			&& !$this->isScriptFileRegistered(TService_Callback::service))
		{
			$this->registerScriptFile(TService_Callback::service, 
				$this->getCallbackScriptFile());
		}
		parent::onPreRender($param);
	}
	
	/**
	 * Register controls that can handle callbacks. Controls that implement
	 * ICallbackEventHandler are automatically added during component registration.
	 * @param ICallbackEventHandler a control that can handle callback.
	 */
	public function registerCallbackCandidate(ICallbackEventHandler $control)
	{			
		$this->callbacksCandidates[$control->getUniqueID()] = $control;	
	}
	
	/**
	 * Remove the control from the list of callback candidates.
	 * @param ICallbackEventHandler the control to remove.
	 */
	public function unregisterCallbackCandidate($control)
	{
		if(isset($this->callbacksCandidates[$control->getUniqueID()]))
			unset($this->callbacksCandidates[$control->getUniqueID()]);
	}
	
	function getCallbackEventReference($control, $args, $clientCallback, $context=null)
	{
		if($control instanceof ICallbackEventHandler)
			$ID = get_class($this).'.raiseCallbackEvent'.$this->getCallbackIDRef($control);
		else
		{
			if(is_null($context))
			{
				throw new Exception('context parameter must not be null if '.
				'the control does not implement ICallbackEventHandler');
			}
			$ID = get_class($control).'.'.$context;
		}
		
		$clientCall = substr($args,0,11) == 'javascript:' 
						? substr($args,11) : "'{$args}'";
		
		return "prado_DoCallback('{$ID}',{$clientCall}, {$clientCallback});";
	}
	
	protected function getCallbackScriptFile()
	{
		$classname = get_class($this);
		$name = is_null($this->Module) ? $classname : $this->Module->getID().':'.$classname;
		$page = $this->Request->constructUrl($name);
		return $page.'&amp;'.TService_Callback::service.'&amp;client';
	}
	
	public function isCallback()
	{
		return !is_null($this->service) && $this->service->isCallback();
	}
	
	public function isServiceRequest()
	{
		return (isset($_SERVER['QUERY_STRING']) 
			&& strpos($_SERVER['QUERY_STRING'], TService_Callback::service) !== false);
	}
	
	function execute()
	{
		if($this->isServiceRequest())
		{
			$this->onPreInit(new TEventParameter);
			$this->determinePostBackMode();
			$this->onInitRecursive(new TEventParameter);				
			$this->service->setPostDataKeys($this->postDataContainerIDs);
			if($this->isCallBack())
			{					
				$this->service->loadCallBackPostData();				
				try
				{
					$state=$this->loadPageStateFromPersistenceMedium();
					$this->loadViewState($state);
					$this->loadPostData();
					$this->onLoadRecursive(new TEventParameter);
					$this->loadPostData();
					$this->raisePostDataChangedEvents();
					$this->handlePostBackEvent();

				}
				catch (Exception $e)
				{
					if($this->Application->getApplicationState() == TApplication::STATE_DEBUG)
						trigger_error($e->getMessage());
				}
			}
			else
			{
				$this->onLoadRecursive(new TEventParameter);
			}
						
			$this->registerCallbackHandler($this, $this->getCallbackCandidateDescriptions());
			$this->Application->getServiceManager()->execute(TService_Callback::service);
			
			$this->onUnloadRecursive(new TEventParameter);
		}
		else
		{
			parent::execute();
		}
	}	
	
	public function registerCallbackHandler($object, $description=null)
	{
		if(is_null($this->service))
			throw new Exception(get_class($this).'::onInit($param) must call parent::onInit($param)');
		$this->service->addHandler($object,$description);	
		return $object;	
	}
	
	protected function getCallbackCandidateDescriptions()
	{
		$description = new TCallbackComponentDescription();
		$description->Class = get_class($this);
				
		foreach($this->callbacksCandidates as $control)
		{
			$description->methods[] = 'raiseCallbackEvent'.$this->getCallbackIDRef($control);
		}		
		return $description;
	}
	
	protected function getCallbackIDRef($control)
	{
		$id = str_replace(':','.',$control->getUniqueID());
		return strlen($id) > 0 ? '.'.$id : '';
	}
	
	public function registerPostDataLoader(IPostBackDataHandler $control)
	{
		$this->postDataContainerIDs[] = $control->getUniqueID();
		parent::registerPostDataLoader($control);
	}
	
	public function unregisterPostDataLoader($control)
	{
		$ID = $control->getUniqueID();
		$total = count($this->postDataContainerIDs);
		foreach($this->postDataContainerIDs as $index => $value)
		{
			if($value == $ID) unset($this->postDataContainerIDs[$index]);
		}
		parent::unregisterPostDataLoader($control);
	}
}

?>