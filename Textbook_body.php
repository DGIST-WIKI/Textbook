<?php

class Textbook {
    /**
	   * Bind the textbookHook function to the <textbook /> tag
	   * @param Parser $parser
	   * @return bool true
     */
    public static function onParserSetup( Parser $parser ) {
      $parser->setFunctionHook( 'textbook', 'Textbook::textbookHook' );
      $parser->setFunctionHook( 'textbook_section', 'Textbook::textbookSectionHook' );
      return true;
    }

    /**
     * save section object.
     * @param OutputPage &$out
     * @param ParserOutput $parseroutput
     * @return bool true
     */
    public static function onOutputPageParserOutput( OutputPage &$out, ParserOutput $parseroutput ) {
      $dbw = wfGetDB( DB_MASTER );
      $displayTitle = $parseroutput->getDisplayTitle();
      $dbw->update(
        'textbook_section',
        array( 'txbsec_section_info' => json_encode( $parseroutput->getSections() ) ),
        array( 'txbsec_title' => $displayTitle )
      );
      return true;
    }

    public static function onParserAfterTidy( Parser &$parser, &$text ) {
      if ($parser->textbookList) {
        $id = $parser->getTitle()->getArticleId();
        $dbw = wfGetDB( DB_MASTER );
        $list = join(',', array_map(function ($item) use ($dbw) {
          return $dbw->addQuotes($item);
        }, $parser->textbookList));
        $dbw->delete(
          'textbook_section',
          array(
            'txbsec_page' => $id,
            'txbsec_title NOT IN (' . $list . ')'
          )
        );
      }
    }

    /**
     * {{#textbook}}
     * @param Parser $parser
     * @return string
     */
    public static function textbookHook( Parser &$parser, $title = '' ) {
	    $hookOptions = Textbook::extractOptions( array_slice(func_get_args(), 2) );
      $id = $parser->getTitle()->getArticleId();
      $dbw = wfGetDB( DB_MASTER );
      $parser->textbookList = array();
      $dbw->upsert(
        'textbook',
        array(
          'txb_page' => $id,
          'txb_title' => $title,
          'txb_author' => $hookOptions['author']
        ),
        array( 'txb_page' ),
        array(
          'txb_title' => $title,
          'txb_author' => $hookOptions['author']
        )
      );
      return;
    }

    /**
     * {{#textbook_section}}
     * @param Parser $parser
     * @return string
     */
    public static function textbookSectionHook( Parser $parser, $title = '' ) {
      $hookOptions = Textbook::extractOptions( array_slice(func_get_args(), 2) );
      $dbw = wfGetDB( DB_MASTER );
      $id = $parser->getTitle()->getArticleId();
      $textbookInfo = $dbw->selectRow(
        'textbook',
        array( 'txb_id', 'txb_title' ),
        array( 'txb_page' => $id ),
        __METHOD__,
        array()
      );
      if ($textbookInfo) {
        $parser->textbookList[] = $title;
        $dbw->upsert(
          'textbook_section',
          array(
            'txbsec_page' => $id,
            'txbsec_textbook' => ($textbookInfo->txb_id),
            'txbsec_title' => $title,
            'txbsec_number' => $hookOptions['no']
          ),
          array( 'txbsec_title' ),
          array(
            'txbsec_page' => $id,
            'txbsec_textbook' => ($textbookInfo->txb_id),
            'txbsec_number' => $hookOptions['no']
          )
        );
        return '*[[' . $title . ']]';
      } else {
        return "Error\n\n";
      }
    }


    function addTables( DatabaseUpdater $updater ) {
      $updater->addExtensionTable( 'textbook', __DIR__ . '/table_textbook.sql' );
      $updater->addExtensionTable( 'textbook_section', __DIR__ . '/table_textbook_section.sql' );
      return true;
    }



    /**
     * Converts an array of values in form [0] => "name=value" into a real
     * associative array in form [name] => value. If no = is provided,
     * true is assumed like this: [name] => true
     *
     * @param array string $options
     * @return array $results
     */
    public static function extractOptions( array $options ) {
    	$results = array();

    	foreach ( $options as $option ) {
    		$pair = explode( '=', $option, 2 );
    		if ( count( $pair ) === 2 ) {
    			$name = trim( $pair[0] );
    			$value = trim( $pair[1] );
    			$results[$name] = $value;
    		}

    		if ( count( $pair ) === 1 ) {
    			$name = trim( $pair[0] );
    			$results[$name] = true;
    		}
    	}
    	//Now you've got an array that looks like this:
    	//  [foo] => "bar"
    	//	[apple] => "orange"
    	//	[banana] => true

    	return $results;
    }
}
