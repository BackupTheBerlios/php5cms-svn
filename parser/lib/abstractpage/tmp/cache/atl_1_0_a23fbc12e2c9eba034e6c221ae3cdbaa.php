<?php

function atl_1_0_a23fbc12e2c9eba034e6c221ae3cdbaa($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<macros>

	');
    $__out__->writeStructure('
	
	');
    // main_menu template requires 'mdate' variable
    $temp_1 = "last_modified";
    $__ctx__->setRef("mdate", $temp_1);
    $__out__->writeStructure(' 
	');
    $__out__->pushBuffer();
    $__out__->popBuffer();
    $temp_2 = 'main_menu';
    $__old_error = $__ctx__->_errorRaised;
    $__ctx__->_errorRaised = false;
    $temp_3 = new ATL_Macro($__tpl__, $temp_2);
    $temp_3 = $temp_3->execute($__tpl__);
    if (PEAR::isError($temp_3)) {
        $__ctx__->_errorRaised = $temp_3;
    }
    $__out__->writeStructure($temp_3);
    if (!$__ctx__->_errorRaised) {
        $__ctx__->_errorRaised = $__old_error;
    }
    $__out__->writeStructure('
	
</macros>');
    return $__out__->toString();
}
function atl_1_0_a23fbc12e2c9eba034e6c221ae3cdbaa_main_menu($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__out__->writeStructure('<div> 
		<a href="/">home</a> | 
		<a href="/products">products</a> |
		<a href="/contact">contact</a> 
		<div>
		  last modified :	<span>');
    // TAG span AT LINE 11
    $_src_tag = "span"; $_src_line = 11;
    $temp_0 = $__ctx__->get("mdate");
    $__out__->write($temp_0);
    $__out__->writeStructure('</span> 
		</div>
	</div>');
    return $__out__->toString();
}
?>