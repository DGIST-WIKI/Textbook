<?php
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'Textbook.php' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Textbook'] = __DIR__ . '/i18n';
	return true;
} else {
	die( 'This version of the TvPot extension requires MediaWiki 1.25+' );
}

