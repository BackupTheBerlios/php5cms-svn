<?php

$base_dir = dirname(__FILE__).'/';
$library = './';
$output_dir = '../js/';

$additional_outputs = array('../../examples/js/', '../../tests/js/');

$compression_level = 3;

$script_builder = "cscript ESC.wsf -l {$compression_level} -ow OUTPUT INPUTS";

//$doc_builder = "perl JSDoc\jsdoc.pl -d doc INPUTS";

//base javascript functions
$output_files['base.js'] = 
	array(	'prototype/prototype.js', 
			'prototype/compat.js', 
			'prototype/base.js', 
			'extended/base.js',
			'extended/string.js',
			'extended/util.js',
			'extended/array.js',
			'extended/functional.js',
			'prado/prado.js');

//dom functions	
$output_files['dom.js'] =
	array(	'prototype/dom.js', 
			'extended/dom.js',
			'prototype/event.js',
			'extended/event.js',
			'prototype/form.js',
			'prototype/position.js',
			'prototype/string.js',
			'extra/getElementsBySelector.js',
			'extra/behaviour.js'
			);

//effects
$output_files['effects.js'] = 
	array(	'effects/effects.js', 
			'effects/dragdrop.js', 
			'effects/controls.js'
			);
//rico
$output_files['rico.js'] =
	array(	'effects/rico.js' );

//javascript templating
$output_files['template.js'] = 
	array(	'extra/tp_template.js');

//validator
$output_files['validator.js'] =
	array(	'prado/validation.js',
			'prado/validators.js');

$output_files['datepicker.js'] = 
	array(	'prado/datepicker.js');

$all_files = array();
foreach($output_files as $filename => $libs)
{
	$lib_files = array();
	foreach($libs as $lib)
	{
		if(is_file($library.$lib))
		{
			$lib_files[] = $library.$lib;
			$all_files[] = $library.$lib;
		}
		else
			echo '<b>File not found '.$library.$lib.'</b>';
	}
	$output_file = $output_dir.$filename;
echo '<pre>';

	if(count($lib_files) > 0)
	{
		$compressor = str_replace(array('OUTPUT', 'INPUTS'), 
								  array($output_file, implode(' ', $lib_files)), 
								$script_builder);
		$command = str_replace('/','\\', $compressor);
		system($command);
		echo $command;
		post_process($output_file);
		copy_files($additional_outputs, $output_file, $filename);
	}
echo '</pre>';
}

//do docs
/*echo '<pre>';
$docs = str_replace('INPUTS', implode(' ', $all_files),  $doc_builder);
$command = str_replace('/','\\', $docs);
system($command);
echo '</pre>';
*/


//*** post processing, ESC does strange things to try{} catch{} finally{} blocks, and a few others.

function post_process($file)
{
	$contents = file_get_contents($file);
	$contents = str_replace('};catch{', '}catch{', $contents);
	$contents = str_replace('};finally{', '}finally{', $contents);
	$contents = str_replace('};while(', '}while(', $contents);
	$contents = str_replace('}elseif(', '}else if(', $contents);
	$contents = str_replace('elsereturn', 'else return', $contents);
	$contents = str_replace('}Effect.', '};Effect.', $contents);	
	$contents = str_replace('}Element.', '};Element.', $contents);
	$contents = str_replace('}Rico.', '};Rico.', $contents);
	file_put_contents($file, $contents);
}

function copy_files($additional_outputs, $source, $filename)
{
	foreach($additional_outputs as $dir)
		copy($source, $dir.$filename);
}

?>