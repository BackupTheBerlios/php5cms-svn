<?php

require( '../../../../prepend.php' );

using( 'html.BBCode' );


$bbcode = new BBCode();

try
{
	$bbcode->addTag(
		array(
			'Name'				=> 'b',
			'HtmlBegin'			=> '<span style="font-weight: bold;">',
			'HtmlEnd'			=> '</span>'
		)
	);
	
	$bbcode->addTag(
		array(
			'Name'				=> 'i',
			'HtmlBegin'			=> '<span style="font-style: italic;">',
			'HtmlEnd'			=> '</span>'
		)
	);

	$bbcode->addTag(
		array(
			'Name'				=> 'u',
			'HtmlBegin'			=> '<span style="text-decoration: underline;">',
			'HtmlEnd'			=> '</span>'
		)
	);

	$bbcode->addTag(
		array(
			'Name'				=> 'link',
			'HasParam'			=> true,
			'HtmlBegin'			=> '<a href="%%P%%">',
			'HtmlEnd'			=> '</a>'
		)
	);
	
	$bbcode->addTag(
		array(
			'Name'				=> 'color',
			'HasParam'			=> true,
			'ParamRegex'		=> '[A-Za-z0-9#]+',
			'HtmlBegin'			=> '<span style="color: %%P%%;">',
			'HtmlEnd'			=> '</span>',
			'ParamRegexReplace'	=> array( '/^[A-Fa-f0-9]{6}$/' => '#$0' )
		)
	);
	
	$bbcode->addTag(
		array(
			'Name'				=> 'email',
			'HasParam'			=> true,
			'HtmlBegin'			=> '<a href="mailto:%%P%%">',
			'HtmlEnd'			=> '</a>'
		)
	);
	
	$bbcode->addTag(
		array(
			'Name'				=> 'size',
			'HasParam'			=> true,
			'HtmlBegin'			=> '<span style="font-size: %%P%%pt;">',
			'HtmlEnd'			=> '</span>',
			'ParamRegex'		=> '[0-9]+'
		)
	);
	
	$bbcode->addTag(
		array(
			'Name'				=> 'bg',
			'HasParam'			=> true,
			'HtmlBegin'			=> '<span style="background: %%P%%;">',
			'HtmlEnd'			=> '</span>',
			'ParamRegex'		=> '[A-Za-z0-9#]+'
		)
	);
	
	$bbcode->addTag(
		array(
			'Name'				=> 's',
			'HtmlBegin'			=> '<span style="text-decoration: line-through;">',
			'HtmlEnd'			=> '</span>'
		)
	);
	
	$bbcode->addTag(
		array(
			'Name'				=> 'align',
			'HtmlBegin'			=> '<div style="text-align: %%P%%">',
			'HtmlEnd'			=> '</div>',
			'HasParam'			=> true,
			'ParamRegex'		=> '(center|right|left)'
		)
	);
	
	$bbcode->addAlias( 'url', 'link' );
}
catch ( Exception $e )
{
	die( $e->getMessage() );
}

print $bbcode->parse( '[b]Bold text[/b]
	[i]Italic text[/i]
	[u]Underlinex text[/u]
	[link=http://phpclasses.org/]A link[/link]
	[url=http://phpclasses.org/]Another link[/url]
	[color=red]Red text[/color]
	[email=eurleif@ecritters.biz]Email me![/email]
	[size=20]20-point text[/size]
	[bg=red]Text with a red background[/bg]
	[s]Text with a line through it[/s]
	[align=center]Centered text[/align]'
);
	
?>
