<?php

function atl_1_0_fcba2294d007db001cca769239783342($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1"/>
    <meta http-equiv="content-style-type" content="text/css"/>
    <meta http-equiv="content-script-type" content="text/javascript"/>
    <title>');
    // TAG title AT LINE 12
    $_src_tag = "title"; $_src_line = 12;
    $temp_0 = $__ctx__->get("page/title");
    $__out__->write($temp_0);
    $temp_2 = "theme.css";
    $__out__->writeStructure('</title>
    <style type="text/css" media="screen">');
    $temp_1 =& $temp_2;
    $temp_1 = $__tpl__->realpath($temp_1);
    if (PEAR::isError($temp_1)) {
        $__ctx__->_errorRaised = true;
        $__out__->write($temp_1);
    }
    else {
        $__out__->writeStructure(join("", file($temp_1)));
    }
    $temp_3 = $__ctx__->get("form/action");
    $__out__->writeStructure('</style>
  </head>
  <body>
    <div id="page">
		  <form');
    $action =& $temp_3;
    $__out__->writeStructure(' action="');
    $__out__->write($action);
    $__out__->writeStructure('" method="post">
			  <input type="hidden" name="phpcmsaction" value="FILEMANAGER"/>
				<input type="hidden" name="work_dir" value="'. $__ctx__->getToString("form/workdir") .'"/>
				<input type="hidden" name="fileurl" value="'. $__ctx__->getToString("form/workfile") .'"/>
				<table border="0" cellspacing="3" cellpadding="3" width="600">
					<tr>
						<td class="header-large">');
    // TAG td AT LINE 23
    $_src_tag = "td"; $_src_line = 23;
    $temp_4 = $__ctx__->get("form/title");
    $__out__->write($temp_4);
    $__out__->writeStructure('</td>
					</tr>
					
					<tr>
						<td>');
    // TAG td AT LINE 27
    $_src_tag = "td"; $_src_line = 27;
    $temp_5 = $__ctx__->get("form/workdir");
    $__out__->write($temp_5);
    $__out__->writeStructure('</td>
					</tr>
					
					<tr>
						<td>');
    $__out__->pushBuffer();
    $__out__->popBuffer();
    $temp_6 = 'macros.xml/top_toolbar';
    $__old_error = $__ctx__->_errorRaised;
    $__ctx__->_errorRaised = false;
    $temp_7 = new ATL_Macro($__tpl__, $temp_6);
    $temp_7 = $temp_7->execute($__tpl__);
    if (PEAR::isError($temp_7)) {
        $__ctx__->_errorRaised = $temp_7;
    }
    $__out__->writeStructure($temp_7);
    if (!$__ctx__->_errorRaised) {
        $__ctx__->_errorRaised = $__old_error;
    }
    $__out__->writeStructure('</td>
					</tr>
					
					<tr>
						<td>
						  <table style="width:100%;margin:0px;">
							  ');
    // TAG tr AT LINE 37
    $_src_tag = "tr"; $_src_line = 37;
    // new loop
    $temp_9 = $__ctx__->get("form/file");
    $temp_8 =& $temp_9;
    $temp_10 = & new ATL_LoopControler($__ctx__, "line", $temp_8);;
    if (PEAR::isError($temp_10->_error)) {
        $__out__->write($temp_10->_error);
    }
    else {
        while ($temp_10->isValid()) {
            $__out__->writeStructure('<tr>
								  <td class="sourcecode" style="background-color:#cccccc;width:20px;" nowrap="nowrap">');
            // TAG td AT LINE 38
            $_src_tag = "td"; $_src_line = 38;
            $temp_11 = $__ctx__->get("line/key");
            $__out__->write($temp_11);
            $__out__->writeStructure('</td>
									<td class="sourcecode" style="background-color:#eeeeee;" nowrap="nowrap">');
            // TAG td AT LINE 39
            $_src_tag = "td"; $_src_line = 39;
            $temp_12 = $__ctx__->get("line/value");
            $__out__->write($temp_12);
            $__out__->writeStructure('</td>
								</tr>');
            $temp_10->next();
        }
    }
    // end loop
    $__out__->writeStructure('
							</table>
						</td>
					</tr>
					<tr>
						<td class="header-small" style="text-align:right">');
    // TAG td AT LINE 45
    $_src_tag = "td"; $_src_line = 45;
    $temp_13 = $__ctx__->get("form/status");
    $__out__->write($temp_13);
    $__out__->writeStructure('</td>
					</tr>
				</table>
			</form>
    </div>
  </body>
</html>');
    return $__out__->toString();
}
?>