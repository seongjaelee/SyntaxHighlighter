<?php
/**
 * Syntax highlighting extension for MediaWiki 1.18 using SyntaxHighlighter
 * Copyright (C) 2012 Seong Jae Lee <seongjae@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * @file
 * @ingroup Extensions
 * @author Seong Jae Lee
 * 
 * This extension wraps SyntaxHighlighter: http://alexgorbatchev.com/SyntaxHighlighter/
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die(-1);
}

$wgExtensionCredits['parserhook']['SyntaxHighlighter'] = array(
	'path'		=> __FILE__,
	'name'		=> 'SyntaxHighlighter',
	'description'	=> 'uses alexgorbatchev.com/SyntaxHighlighter',
	'version'	=> '1.0',
	'author'	=> 'Seong Jae Lee'
);

$wgSyntaxHighlighterSyntaxList = array();

$wgHooks['ParserFirstCallInit'][] = 'wfSyntaxHighlighterParserInit';
$wgHooks['ParserAfterTidy'][] = 'wfSyntaxHighlighterParserAfterTidy';

function wfSyntaxHighlighterParserInit(Parser &$parser) {
	$parser->setHook('source', 'wfSyntaxHighlighterRender');
	return true;
}

function wfSyntaxHighlighterRender($input, array $args, Parser $parser, PPFrame $frame) {
	global $wgSyntaxHighlighterSyntaxList;

	$input = str_replace('<', '&lt;', $input);
	$input = str_replace('>', '&gt;', $input);
	
	$lang = 'plain';
	if( isset( $args['lang'] ) && $args['lang'] ) {
		$lang = $args['lang'];
	}

	$syntaxAlias = array(
		'cpp'		=> 'Cpp',
		'c'		=> 'Cpp',
		'csharp'	=> 'CSharp',
		'c-sharp'	=> 'CSharp',
		'css'		=> 'Css',
		'php'		=> 'Php',
		'text'		=> 'Plain',
		'plain'		=> 'Plain',
		'xml'		=> 'Xml',
		'python'	=> 'Python',
		'py'		=> 'Python',
		'diff'		=> 'Diff',
		'js'		=> 'JScript',
		'jscript'	=> 'JScript',
		'javascript'	=> 'JScript',
		'bash'		=> 'Bash',
		'shell'		=> 'Bash'
	);
	
	$alias = 'Plain';
	if( isset( $syntaxAlias[$lang] ) ) {
		$alias = $syntaxAlias[$lang];
	}

	if( count($wgSyntaxHighlighterSyntaxList) == 0) {
		global $wgScriptPath;
		$directory = $wgScriptPath.'/extensions/SyntaxHighlighter';
		$parser->getOutput()->addHeadItem( '
		<script type="text/javascript" src="'.$directory.'/syntaxhighlighter/scripts/shCore.js"></script>
		<script type="text/javascript" src="'.$directory.'/syntaxhighlighter/scripts/shAutoloader.js"></script>
		<link rel="stylesheet" type="text/css" media="screen" href="'.$directory.'/syntaxhighlighter/styles/shCoreMinit.css" />
		');
	}

	if( !in_array( $alias, $wgSyntaxHighlighterSyntaxList ) ) {
		$wgSyntaxHighlighterSyntaxList[$lang] = $alias;
	}

	return '<pre class="brush:'.$lang.'">'.$input.'</pre>';
}

function wfSyntaxHighlighterParserAfterTidy($parser, &$text) {
	global $wgSyntaxHighlighterSyntaxList;
	global $wgScriptPath;

	if( count($wgSyntaxHighlighterSyntaxList) > 0 ) {
		$prefix = $wgScriptPath.'/extensions/SyntaxHighlighter/syntaxhighlighter/scripts/shBrush';
		$script = '<script type="text/javascript">';
		$script = $script . 'SyntaxHighlighter.autoloader(';
		foreach ($wgSyntaxHighlighterSyntaxList as $key => $value) {
			$script = $script . '\''. $key . ' ' . $prefix . $value . '.js\',';
		}
		$script = substr($script, 0, -1);
		$script = $script . '); SyntaxHighlighter.all();';
		$script = $script . '</script>';
		$text = $text . $script;
	}
	return true;
}
?>
