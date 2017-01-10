<?php
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'Textbook.php' );
	$wgMessagesDirs['Textbook'] = __DIR__ . '/i18n';
	return true;
} else {
	die( 'This version of the Textbook extension requires MediaWiki 1.25+' );
}
