<?php
if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die(-1);
}

$wgExtensionCredits['other'][] = array(
	'path'		=> __FILE__,
	'name'		=> 'SyntaxHighlighter',
	'description'	=> 'uses alexgorbatchev.com/SyntaxHighlighter',
	'version'	=> '1.0',
	'author'	=> 'Seong Jae Lee'
);

$wgSyntaxHighlighterSyntaxList = array();

$wgHooks['ParserFirstCallInit'][] = 'wfSyntaxHighlighterParserInit';
$wgHooks['ParserAfterTidy'][] = 'wfSyntaxHighlighterParserAfterTidy';
$wgHooks['BeforePageDisplay'][] = 'wfSyntaxHighlighterBeforePageDisplay';

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

function wfSyntaxHighlighterBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
	return true;
}

?>
