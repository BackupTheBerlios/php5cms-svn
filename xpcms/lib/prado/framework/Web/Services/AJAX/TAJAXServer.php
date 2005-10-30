<?php

require_once(dirname(__FILE__).'/JPSpan.php');
require_once(JPSPAN.'Server/PostOffice.php');

class TAJAXServer extends JPSpan_Server_PostOffice
{

	protected $service;

	/**
	 *
	 */
	function __construct($service) 
	{
		$this->service = $service;
		parent::JPSpan_Server_PostOffice();
	}

    /**
    * Get the Javascript client generator
    * @return JPSpan_Generator
    * @access public
    */
    function getGenerator() {
        require_once JPSPAN . 'Generator.php';
        $G = new JPSpan_Generator();
		$gen = new TAJAXGenerator();
		$gen->setService($this->service);
        $G->init(
            $gen,
            $this->descriptions,
            $this->serverUrl,
            $this->RequestEncoding
            );
        return $G;
    }

    /**
    * Registers a user handler class with the server
    * @see http://wact.sourceforge.net/index.php/Handle
    * @param mixed handle to user class
    * @return void
    * @access public
    */
    function addHandler(& $Handle, $Description = NULL) 
	{
        if ( is_null($Description) ) {
            if ( FALSE !== ($Description = TAJAXServer_Handle::examine($Handle)) ) {
                $this->handlers[$Description->Class] = $Handle;
                $this->descriptions[$Description->Class] = $Description;
            } else {
                trigger_error('Invalid handle',E_USER_ERROR);
            }
        } else {
            if ( isset($Description->Class) && is_string($Description->Class) && is_array($Description->methods) ) 
			{            
                $this->handlers[$Description->Class] = $Handle;
                $this->descriptions[$Description->Class] = $Description;
            } else {
                trigger_error('Invalid description',E_USER_ERROR);
            }
        }
    }

    /**
    * Return reference to a handler given it's name.
    * Note this will also resolve the handle
    * @param string handler name (class name)
    * @return mixed object handler or FALSE if not found
    * @access public
    */
    function & getHandler($name) 
	{
        if ( isset($this->handlers[$name]) ) 
		{
            TAJAXServer_Handle::resolve($this->handlers[$name]);
            return $this->handlers[$name];
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
       
        if ( preg_match('/^[a-zA-Z]+[0-9a-zA-Z_]*$/',$uriPath[0]) != 1 ) {
            trigger_error('Invalid handler name: '.$uriPath[0],E_USER_ERROR);
            return FALSE;
        }
        
        if ( preg_match('/^[a-zA-Z]+[0-9a-zA-Z_]*$/',$uriPath[1]) != 1 ) {
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
        
        $this->calledClass = $uriPath[0];
        $this->calledMethod = $uriPath[1];

		return TRUE;
        
    }

    /**
    * Returns the portion of the URL to the right of the executed
    * PHP script e.g. http://localhost/index.php/foo/bar/ returns
    * 'foo/bar'. Returns the string up to the end or to the first ?
    * character
    * @return string
    * @access public
    * @static
    */
    function getUriPath() {
        $basePath = explode('/',$_SERVER['SCRIPT_NAME']);
        $script = array_pop($basePath);
        $basePath = implode('/',$basePath);
        
        // Determine URI path - path variables to the right of the PHP script
        if ( false !== strpos ( $_SERVER['REQUEST_URI'], $script ) ) {
            $uriPath = explode( $script,$_SERVER['REQUEST_URI'] );
            $uriPath = $uriPath[1];
        } else {
            $pattern = '/^'.str_replace('/','\/',$basePath).'/';
            $uriPath = preg_replace($pattern,'',$_SERVER['REQUEST_URI']);
        }
		
		$uriPath = str_replace('?'.$this->service.'&', '', $uriPath);

        if ( FALSE !== ( $pos = strpos($uriPath,'?') )  ) {
            $uriPath = substr($uriPath,0,$pos);
        }
        $uriPath = preg_replace(array('/^\//','/\/$/'),'',$uriPath);
        return $uriPath;
        
    }

}

/**
 *
 * @author $Author: weizhuo $
 * @version $Id: TAJAXServer.php,v 1.2 2005/03/11 11:42:15 weizhuo Exp $
 */
class TAJAXGenerator extends JPSpan_PostOffice_Generator
{
	protected $service;

	/**
	 *
	 */
	function setService($service) 
	{
		$this->service = $service;
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
?>

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
    oParent.<?php echo $method; ?> = function() {
        var url = this.__serverurl+'/<?php echo $method; ?>/';
        return this.__call(url,arguments,'<?php echo $method; ?>');
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

/**
 *
 * @author $Author: weizhuo $
 * @version $Id: TAJAXServer.php,v 1.2 2005/03/11 11:42:15 weizhuo Exp $
 */
class TAJAXServer_Handle extends JPSpan_Handle 
{
	function examine($Handle) 
	{
        switch ( gettype($Handle) ) {
            case 'array':
                $Class = array_shift($Handle);
            break;
            case 'string':
                $Class = $Handle;
            break;
            case 'object':
                $Class = get_class($Handle);
            break;
            default:
                return FALSE;
            break;
        }
        
        if (is_integer($Pos = strpos($Class, '|'))) {
                $File = substr($Class, 0, $Pos);
                $Class = substr($Class, $Pos + 1);
                require_once $File;
        }
        
        //$Class = strtolower($Class);
        
        $Description = new JPSpan_HandleDescription();
        $Description->Class = $Class;
        
        $methods = get_class_methods($Class);
        if ( is_null($methods) ) {
            return FALSE;
        }
        //$methods = array_map('strtolower',$methods);
        
        if ( FALSE !== ( $constructor = array_search($Class,$methods) ) ) {
            unset($methods[$constructor]);
        }
        
        foreach ( $methods as $method ) {
            if ( preg_match('/^[a-zA-Z]+[0-9a-zA-Z_]*$/',$method) == 1 ) {
                $Description->methods[] = $method;
            }
        }
        return $Description;
	}
}

?>