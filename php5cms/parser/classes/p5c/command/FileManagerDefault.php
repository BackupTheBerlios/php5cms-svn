<?php
import('p5c.Command');
import('p5c.model.FileList');
import('p5c.view.FileManager');

/**
 *
 * @access  public
 * @package p5c_command
 * @author  Manuel Heße <manuel.hesse@xplib.de>
 * @version $Revision$
 * @see     p5c_Command
 */
class p5c_command_FileManagerDefault extends p5c_Command {
	
	
	// {{{
	
	
	/**
	 *
	 */
	public function execute(p5c_http_Request $request, 
	                        p5c_http_Response $response) {

        ##
		
		$config = p5c_Config::getInstance();
		
		$key = null;
		$order = -1;
		
		$sort = $request->getParameter('sort');
		if (is_array($sort)) {
			$sort = key($sort);
		}
		if (preg_match('#^([a-z0-9]+)-(asc|desc)$#i', $sort, $match)) {
			$key   = $match[1];
			$order = $match[2];
		}
		
		if (($path = $request->getParameter('workdir')) === null) {
			$path = $config->getProperty('filemanager.directory') . '/parser';
		}
		
		if (($chdir = $request->getParameter('chdir')) != null) {
			
			if (is_array($chdir)) {
				$chdir = key($chdir);
			}
			
			if (strpos($chdir, '..') !== false) {
				$path = preg_replace('#/?[^/]+/?$#U', '', $path);
			} else {
				$path .= '/' . $chdir;
			}
		}
		$path = str_replace(array('..', '//'), array('', '/'), $path);
		
		$fileList = new p5c_model_FileList($path, $key, $order);
		
		$this->view = new p5c_view_FileManager($fileList);
	} // end public function execute(p5c_http_Request $request, ...)
	
	
	// }}}
	
	
} // end class p5c_command_FileManagerDefault extends p5c_Command
?>
