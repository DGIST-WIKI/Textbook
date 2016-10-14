<?php
class Textbook {
    /**
	 * Bind the doTextbook function to the <textbook /> tag
	 * @param Parser $parser
	 * @return bool true
     */
    public static function onParserSetup( Parser $parser ) {
       	$parser->setHook( 'textbook', Array( 'Textbook', 'doTextbook') );
        return true;
    }
    /**
     * @param string $input The text inside <textbook /> tag
     * @param array $args
     * @param Parser $parser
     * @param PPFrame $frame
     * @return string
     */
    function doTextbook( $input, array $args, Parser $parser, PPFrame $frame ) {
        $input = htmlspecialchars($input);
        return Html::rawElement('b', array(), 'THIS IS TEXTBOOK!!!!');
    }
}
