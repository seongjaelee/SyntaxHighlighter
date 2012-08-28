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
	'description'	=> 'A syntax highlighter extension using alexgorbatchev.com/SyntaxHighlighter',
	'version'	=> '1.1',
	'author'	=> 'Seong Jae Lee >> http://bluebrown.net',
	'url'		=> 'https://www.mediawiki.org/wiki/Extgension:SyntaxHighlighter'
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

	$attribs = '';
	foreach( $args as $key => $value ) {
		if( $key == 'lang' ) {
			continue;
		}
		$attribs = $attribs.'; '.$key.':'.$value;
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
		'html'		=> 'Xml',
		'xhtml'		=> 'Xml',
		'xslt'		=> 'Xml',
		'sql'		=> 'Sql',
		'ps'		=> 'PowerShell',
		'powershell' 	=> 'PowerShell',
		'perl'		=> 'Perl',
		'pl'		=> 'Perl',
		'delphi'	=> 'Delphi',
		'python'	=> 'Python',
		'py'		=> 'Python',
		'diff'		=> 'Diff',
		'js'		=> 'JScript',
		'jscript'	=> 'JScript',
		'javascript'	=> 'JScript',
		'bash'		=> 'Bash',
		'shell'		=> 'Bash',
		'java'		=> 'Java'
	);
	
	$alias = 'Plain';
	if( isset( $syntaxAlias[$lang] ) ) {
		$alias = $syntaxAlias[$lang];
	} else {
		$lang = 'plain';
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

	return '<pre class="brush:'.$lang.$attribs.'">'.$input.'</pre>';
}

function wfSyntaxHighlighterParserAfterTidy($parser, &$text) {
	global $wgSyntaxHighlighterSyntaxList;
	global $wgScriptPath;

	if( count($wgSyntaxHighlighterSyntaxList) > 0 ) {
		$scriptTxt = '<script type="text/javascript">SyntaxHighlighter.autoloader(';
		foreach( $wgSyntaxHighlighterSyntaxList as $key => $value ) {
			$scriptTxt = $scriptTxt.'\''.$key.' '.$prefixTxt.$value.'.js\',';
		}
		$scriptTxt = substr($scriptTxt, 0, -1);
		$scriptTxt = $scriptTxt.'); SyntaxHighlighter.all();</script>';
		$text = $text.$scriptTxt;
	}
	return true;
}
?>
