<?php
/**
 * Syntax highlighting extension for MediaWiki 1.18 and above using SyntaxHighlighter
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

/**
 * Options:
 * $wgSyntaxHighlighterOptions
 *	An array that goes into SyntaxHighlighter.defaults. Note that array keys and values are ALWAYS strings.
 *	For more information, refer http://alexgorbatchev.com/SyntaxHighlighter/manual/configuration/#syntaxhighlighterdefaults
 *	For example, `$wgSyntaxHighlighterOption['auto-links'] = 'true';`.
 */
$wgSyntaxHighlighterOptions = array();

$wgExtensionCredits['parserhook']['SyntaxHighlighter'] = array(
	'path'		=> __FILE__,
	'name'		=> 'SyntaxHighlighter',
	'description'	=> 'A syntax highlighter extension using alexgorbatchev.com/SyntaxHighlighter',
	'version'	=> '1.2',
	'author'	=> 'Seong Jae Lee >> http://bluebrown.net',
	'url'		=> 'https://www.mediawiki.org/wiki/Extgension:SyntaxHighlighter'
);

$wgHooks['ParserFirstCallInit'][] = 'SyntaxHighlighter::setHooks';

class SyntaxHighlighter {
	var $mSyntaxList = array();
	static protected $hookInstalled = false;

	static function setHooks( $parser ) {
		global $wgHooks;

		$parser->extSyntaxHighlighter = new self();
		if( !SyntaxHighlighter::$hookInstalled ) {
			$wgHooks['ParserAfterTidy'][] = array( $parser->extSyntaxHighlighter, 'addHeadItems' );
			SyntaxHighlighter::$hookInstalled = true;
		}
		$parser->setHook( 'source', array( $parser->extSyntaxHighlighter, 'source' ) );

		return true;
	}

	function source( $input, array $args, Parser $parser ) {

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
			$attribs = $attribs.'; '.$key.':'.'\''.$value.'\'';
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
			'ruby'		=> 'Ruby',
			'rb'		=> 'Ruby',
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

		if( !in_array( $alias, $this->mSyntaxList ) ) {
			$this->mSyntaxList[$lang] = $alias;
		}

		return '<pre class="brush:'.$lang.$attribs.'">'.$input.'</pre>';
	}

	function addHeadItems( Parser &$parser, &$text ) {
		if( $parser->extSyntaxHighlighter !== $this ) {
			return $parser->extSyntaxHighlighter->addHeadItems( $parser, $text );
		}

		if( count($this->mSyntaxList) > 0 ) {
			global $wgScriptPath;
			global $wgSyntaxHighlighterOptions;
			$directory = $wgScriptPath.'/extensions/SyntaxHighlighter';

			$scriptTxt = "\n\r";
			$scriptTxt = $scriptTxt.'<script type="text/javascript" src="'.$directory.'/syntaxhighlighter/scripts/shCore.js"></script>'."\n\r";
			foreach( $this->mSyntaxList as $key => $value ) {
				$scriptTxt = $scriptTxt.'<script type="text/javascript" src="'.$directory.'/syntaxhighlighter/scripts/shBrush'.$value.'.js"></script>'."\n\r";
			}
			$scriptTxt = $scriptTxt.'<script type="text/javascript">'."\n\r";
			foreach( $wgSyntaxHighlighterOptions as $key => $value ) {
				$scriptTxt = $scriptTxt.'SyntaxHighlighter.defaults["'.$key.'"] = '.$value.';'."\n\r";
			}
			$scriptTxt = $scriptTxt.'SyntaxHighlighter.all();'."\n\r";
			$scriptTxt = $scriptTxt.'</script>'."\n\r";
			$scriptTxt = $scriptTxt.'<link rel="stylesheet" type="text/css" media="screen" href="'.$directory.'/syntaxhighlighter/styles/shCoreMinit.css" />'."\n\r";
			$parser->GetOutput()->addHeadItem($scriptTxt);
		}

		return true;
	}
}
?>
