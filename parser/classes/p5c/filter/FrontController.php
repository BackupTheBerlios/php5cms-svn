<?php
import('p5c.CommandFactory');

class p5c_filter_FrontController {
	
	
	public function run(p5c_http_Request $request, p5c_http_Response $response) {
		
		$module = $request->getParameter('module');
		$action = $request->getParameter('action');
		
		
		$cmdFactory = new p5c_CommandFactory();
		
		
		try {
			$command = $cmdFactory->createCommand($module, $action);
		} catch (p5c_CommandNotFoundException $e) {
			
		}
		
		$command->execute($request, $response);
		
		if (($view = $command->getView()) != null) {
			$view->display();
		}
	} // end public function run(p5c_http_Request $request, ...)
	
	
} // end class p5c_filter_FrontController
?>
