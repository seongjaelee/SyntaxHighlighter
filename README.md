SyntaxHighlighter
=================
A MediaWiki extension allowing source code to be syntax highlighted.

A simple wrapper of [SyntaxHighlighter javascript library](http://alexgorbatchev.com/SyntaxHighlighter) by Alex Gorbatchev.
* http://alexgorbatchev.com/SyntaxHighlighter/

SyntaxHighlighter is released under the terms of the MIT license.

![snapshot english](https://github.com/seongjaelee/SyntaxHighlighter/raw/master/snapshot.png)

Supporting Languages
--------------------
Some of the supported languages and corresponding lang parameters are shown below. For the full list, please refer the [SyntaxHighlighter site](http://alexgorbatchev.com/SyntaxHighlighter/). the lang parameter in the source should be set to one of the aliases. Else, it will automatically render the plain text mode.

C++ : cpp, c
C# : csharp, c-sharp
CSS : css
PHP : php
XML : xml, html, xhtml
Python : python, py
Java : java
Javascript : jscript, js, javascript

Other Parameters
----------------
Besides the lang parameter, it supports all parameters used in SyntaxHighlighter library. In fact, our extension only passes the parameters from the source tag and pass it to SyntaxHighlight. If you want to see the supported parameters, please refer the [SyntaxHighlight configuration page](http://alexgorbatchev.com/SyntaxHighlighter/manual/configuration/).

Usage
-----
On a wiki page, you can use "source" tags.

    <source lang="javascript">
    // SyntaxHighlighter makes your code snippets beautiful without tiring your servers.
    // http://alexgorbatchev.com
    var setArray = function(elems) {
        this.length = 0;
        push.apply(this, elems);
        return this;
    }
    </source>

Installation
------------
The source code is hosted on Github and versioned using the Git tool (not SVN). So, you'll need Git to download or update the sources. Once git is installed, you can download the extension issuing the following command in the extensions/ directory:

    git clone git://github.com/seongjaelee/SyntaxHighlighter.git

That would create directory SyntaxHighlighter/, which contains the needed files, right under extensions/.


If you don't want to be bothered, you can just download the zip file from the following address:

    https://github.com/seongjaelee/SyntaxHighlighter/zipball/master

Unzip the downloaded file in the extensions/ directory, and change the name of directory to SyntaxHighlighter/.


Finally, add the following to LocalSettings.php:

    require_once("$IP/extensions/SyntaxHighlighter/SyntaxHighlighter.php");

Many other syntax highlighting extensions also share source tag, so if you already use one, uncomment it from LocalSettings.php file.

Options
-------
It also provides user-configurable options that goes to [SyntaxHighlighter.defaults](http://alexgorbatchev.com/SyntaxHighlighter/manual/configuration/#syntaxhighlighterdefaults).

For example, you can add the following to the `LocalSettings.php`.

```php
$wgSyntaxHighlighterOptions['smart-tabs'] = 'true';
```

Note that all array values are strings.
