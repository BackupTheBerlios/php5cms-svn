<?php

function atl_1_0_d219cd540a31b7f9f1d210172a3607ea($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<macros>

		
		<--
		
		-->
	  ');
    $__out__->writeStructure('
		
		
		<-- 
		  Defines a toolbar macro
		-->
	  ');
    $__out__->writeStructure('

		
</macros>');
    return $__out__->toString();
}
function atl_1_0_d219cd540a31b7f9f1d210172a3607ea_header($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $temp_1 = $__ctx__->get("form/top-toolbar");
    $temp_0 =& $temp_1;
    if (!PEAR::isError($temp_0) && $temp_0) {
        // TAG macro AT LINE 13
        $_src_tag = "macro"; $_src_line = 13;
        // new loop
        $temp_3 = $__ctx__->get("form/top-toolbar");
        $temp_2 =& $temp_3;
        $temp_4 = & new ATL_LoopControler($__ctx__, "button", $temp_2);;
        if (PEAR::isError($temp_4->_error)) {
            $__out__->write($temp_4->_error);
        }
        else {
            while ($temp_4->isValid()) {
                $__out__->writeStructure('
		');
                $temp_4->next();
            }
        }
        // end loop
    }
    return $__out__->toString();
}
function atl_1_0_d219cd540a31b7f9f1d210172a3607ea_top_toolbar($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $temp_6 = $__ctx__->get("form/top-toolbar");
    $temp_5 =& $temp_6;
    if (!PEAR::isError($temp_5) && $temp_5) {
        $temp_7 = $__ctx__->get("form/action");
        $__out__->writeStructure('
					 
			<form');
        $action =& $temp_7;
        $__out__->writeStructure(' action="');
        $__out__->write($action);
        $__out__->writeStructure('" method="post">
				<input type="hidden" name="phpcmsaction" value="FILEMANAGER"/>
				<input type="hidden" name="working_dir" value="'. $__ctx__->getToString("form/workdir") .'"/>
				');
        // TAG span AT LINE 27
        $_src_tag = "span"; $_src_line = 27;
        // new loop
        $temp_9 = $__ctx__->get("form/top-toolbar");
        $temp_8 =& $temp_9;
        $temp_10 = & new ATL_LoopControler($__ctx__, "button", $temp_8);;
        if (PEAR::isError($temp_10->_error)) {
            $__out__->write($temp_10->_error);
        }
        else {
            while ($temp_10->isValid()) {
                $temp_12 = $__ctx__->get("button/id");
                $__out__->writeStructure('
				');
                $temp_11 =& $temp_12;
                if (!PEAR::isError($temp_11) && $temp_11) {
                    $__out__->writeStructure('<input type="image" name="action['. $__ctx__->getToString("button/id") .']" src="/scripts/cms/phpcms-1.2.0/parser/gif/filemanager/'. $__ctx__->getToString("button/icon") .'" border="0" width="16" height="16" title="'. $__ctx__->getToString("button/title") .'"/>');
                }
                $__out__->writeStructure('
				');
                $temp_10->next();
            }
        }
        // end loop
        $__out__->writeStructure('
			</form>
							 
		');
    }
    return $__out__->toString();
}
?>