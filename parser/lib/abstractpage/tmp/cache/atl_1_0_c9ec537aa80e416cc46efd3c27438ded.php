<?php

function atl_1_0_c9ec537aa80e416cc46efd3c27438ded($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<macros>
	
	');
    $__out__->writeStructure('

</macros>');
    return $__out__->toString();
}
function atl_1_0_c9ec537aa80e416cc46efd3c27438ded_theme($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__out__->writeStructure('<macro>
			.header-large, .header-small {
				background-color: #006600;
				color: #ffffff;
				font-family: Verdana, Helvetica, Arial, sans-serif;
				font-size: 18px;
			}
			
			.header-small {
				font-size: 12px;
			}
			
			html, body {
				background-color:#FFFFFF;
				border:0px none;
				width:100%;
				height:100%;
				margin:0;
				padding:0;
			}
			
			#page {
				width: 100%;
				height:100%;
				border:0px none;
			}
			
			a:link, body a:link {color:#006600;}
			a:visited {color:#006600;}
			a:active {color:#006600;}
	</macro>');
    return $__out__->toString();
}
?>