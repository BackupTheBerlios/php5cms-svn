<?php

require_once(dirname(__FILE__).'/TAJAXServer.php');

class TCallbackServer extends TAJAXServer
{
	protected $callID;
	protected $postDataKeys = array('__VIEWSTATE');
	
    /**
    * Get the Javascript client generator
    * @return JPSpan_Generator
    * @access public
    */
    function getGenerator() {
        require_once JPSPAN . 'Generator.php';
        $G = new JPSpan_Generator();
		$gen = new TCallbackGenerator();
		$gen->setService($this->service);
		$gen->setPostDataList($this->postDataKeys);
        $G->init(
            $gen,
            $this->descriptions,
            $this->serverUrl,
            $this->RequestEncoding
            );
        return $G;
    }
    
    function setPostDataKeys($list)
    {
    	$this->postDataKeys = array_merge($list, $this->postDataKeys);
    }
    
    function handleCallback($object, $args)
    {
		
    	$param = new TCallbackEventParameter();
    	$param->args = $this->exportObject($args);
    	
    	if($object instanceof TControl)
    	{	
    		try
    		{    	
	    		$control = $object->findObject($this->callID);
				if(is_null($control) && $object instanceof ICallbackEventHandler)
					$control = $object;

		    	if(!is_null($control) && $control instanceof ICallbackEventHandler)
		    		return $control->raiseCallbackEvent($param);
		    	else
		    		trigger_error('Invalid control for '.$this->callID);
    		}
		    catch(Exception $e)
		    {
		    	trigger_error('Invalid control for '.$this->callID);
		    }
    	}    	
    }
    
    function exportObject($args)
    {
    	$parameters = array();
    	foreach($args as $arg)
    	{
    		if($arg instanceof JPSpan_Object)
    		{
    			if(isset($arg->__data))    			
    				unset($arg->__data);
    			if(isset($arg->__parameters))
    				$parameters[] = $arg->__parameters;
    		}
    	}
    	return $parameters;
    }
    
    function loadCallBackPostData()
    {
    	$args = array();
    	$this->getArgs($args);
    	
    	foreach($args as $arg)
    	{
    		if($arg instanceof JPSpan_Object && isset($arg->__data))
    		{
   				$data = $arg->__data;
   				foreach($data as $name => $value)
   				{
   					if(in_array($name, $this->postDataKeys))
   						$_REQUEST[$name] = $value;
   				}
    		}    		
    	}
    }
       
    /**
    * Serve a request
    * @param boolean send headers
    * @return boolean FALSE if failed (invalid request - see errors)
    * @access public
    */
    function serve($sendHeaders = TRUE) {
        require_once JPSPAN . 'Monitor.php';
        $M = & JPSpan_Monitor::instance();
        
        $this->calledClass = NULL;
        $this->calledMethod = NULL;

        if ( $this->resolveCall() ) {
        
            $M->setRequestInfo('class',$this->calledClass);
            $M->setRequestInfo('method',$this->calledMethod);
            
            if ( FALSE !== ($Handler = & $this->getHandler($this->calledClass) ) ) 
            {
            	$args = array();
                $M->setRequestInfo('args',$args);
                
                if($Handler instanceof TPageWithCallback)
                {
                	$this->getArgs($args);
                	$response = $this->handleCallback($Handler, $args);
                }
                else if ( $this->getArgs($args) ) {    
                    $M->setRequestInfo('args',$args);
                    
                    $response = call_user_func_array(
                        array(
                            & $Handler,
                            $this->calledMethod
                        ),
                        $args
                    );
                    
                } else {  
                    $response = call_user_func(
                        array(
                            & $Handler,
                            $this->calledMethod
                        )
                    );
                    
                }
                
                require_once JPSPAN . 'Serializer.php';

                $M->setResponseInfo('payload',$response);
                $M->announceSuccess();
                
                $response = JPSpan_Serializer::serialize($response);
                
                if ( $sendHeaders ) {
                    header('Content-Length: '.strlen($response));
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
                    header('Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . 'GMT'); 
                    header('Cache-Control: no-cache, must-revalidate'); 
                    header('Pragma: no-cache');
                }
                echo $response;

                return TRUE;
                
            } else {
           
                trigger_error('Invalid handle for: '.$this->calledClass,E_USER_ERROR);
                return FALSE;
                
            }
            
        }
        return FALSE;
    }    
    
    /**
    * Resolve the call - identify the handler class and method and store
    * locally
    * @return boolean FALSE if failed (invalid request - see errors)
    * @access private
    */
    function resolveCall() {
        $uriPath = explode('/',$this->getUriPath());                
        if ( count($uriPath) != 2 ) {
            trigger_error('Invalid call syntax',E_USER_ERROR);
            return FALSE;
        }
       
        //check the class name
        if ( preg_match('/^[a-zA-Z]+[0-9a-zA-Z_]*$/',$uriPath[0]) != 1 ) {
            trigger_error('Invalid handler name: '.$uriPath[0],E_USER_ERROR);
            return FALSE;
        }
        //method name
        if ( preg_match('/^[a-zA-Z_]+[0-9a-zA-Z_\.]*$/',$uriPath[1]) != 1 ) {
            trigger_error('Invalid handler method: '.$uriPath[1],E_USER_ERROR);
            return FALSE;
        }
        
        if ( !array_key_exists($uriPath[0],$this->descriptions) ) {
            trigger_error('Unknown handler: '.$uriPath[0],E_USER_ERROR);
            return FALSE;
        }
        
        if ( !in_array($uriPath[1],$this->descriptions[$uriPath[0]]->methods) ) {
            trigger_error('Unknown handler method: '.$uriPath[1],E_USER_ERROR);
            return FALSE;
        }
        
        $method = explode('.', $uriPath[1]);
		               
        $this->calledClass = $uriPath[0];
        $this->calledMethod = array_shift($method);
       	$this->callID = implode('.',$method);;

        return TRUE;
        
    }    
}

class TCallbackGenerator extends TAJAXGenerator
{
	protected $postDataList = array();
	
	public function setPostDataList($list)
	{
		$this->postDataList = $list;
	}
	
  /**
    * Generate the starting includes section of the script
    * @param JPSpan_CodeWriter
    * @return void
    * @access private
    */
    function generateScriptHeader(& $Code) 
    {    
        ob_start();
?>
/**@
* include 'remoteobject.js';
* include 'request/callback.js';
<?php
if ( $this->RequestEncoding == 'xml' ) {
?>
* include 'request/rawpost.js';
* include 'encode/xml.js';
<?php
} else {
?>
* include 'request/post.js';
* include 'encode/php.js';
<?php
}
?>
*/
<?php
        $Code->append(ob_get_contents());
        ob_end_clean();
    }	
	
    
    /**
    * Generate code for a single description (a single PHP class)
    * @param JPSpan_CodeWriter
    * @param JPSpan_HandleDescription
    * @return void
    * @access private
    */
    function generateHandleClient(& $Code, & $Description) 
	{
        ob_start();
        $id_string = 'new Array()';
        if(count($this->postDataList) > 0)
        	$id_string = '[%s]';
        $ids = array();
        foreach ($this->postDataList as $item)
        	$ids[] = "'".$item."'";
        $id_string = sprintf($id_string, implode(',',$ids));       
?>
var PradoPostDataIDs = <? echo $id_string; ?>;
function <?php echo $Description->Class; ?>() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = '<?php 
        echo $this->serverUrl . '?'.$this->service.'&/' . $Description->Class; ?>';
    
    oParent.__remoteClass = '<?php echo $Description->Class; ?>';
    
<?php
if ( $this->RequestEncoding == 'xml' ) {
?>
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
<?php
} else {
?>
    oParent.__request = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
<?php
}
foreach ( $Description->methods as $method ) {
?>
    
    // @access public
    oParent.<?php echo str_replace('.','_', $method); ?> = function() {
        var url = this.__serverurl+'/<?php echo $method; ?>/';
        return this.__call(url,arguments,'<?php echo str_replace('.','_', $method); ?>');
    };
<?php
}
?>
    
    return oParent;
}

<?php
        $Code->append(ob_get_contents());
        ob_end_clean();
    }
}

?>