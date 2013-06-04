<?php
/**
 * Drupal_Sniffs_CSS_StyleSortSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks that style properties are sorted alphabetically.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal_Sniffs_CSS_StyleSortSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('CSS');


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_OPEN_CURLY_BRACKET);

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param int                  $stackPtr  The position in the stack where
     *                                        the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Examine style properties until the next curly bracket is encountered.
        $stop = $phpcsFile->findNext(array(T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET), ($stackPtr + 1));
        // Get the first style property.
        $style = $prevStyle = $phpcsFile->findNext(T_STYLE, ($stackPtr + 1), $stop);
        if ($style === false) {
            // No style property found, so stop here.
            return;
        }

        while ($style = $phpcsFile->findNext(T_STYLE, ($style + 1), $stop)) {
            // Check that the style property is alphabetically higher than the
            // previous one.
            if (strcasecmp($tokens[$style]['content'], $tokens[$prevStyle]['content']) < 0
                // Ignore vendor specific properties starting with "-".
                && $tokens[$style]['content']{0} !== '-' && $tokens[$prevStyle]['content']{0} !== '-'
            ) {
                $error = 'Multiple CSS properties should be listed in alphabetical order';
                $phpcsFile->addError($error, $style, 'AlphabeticalOrder');
            }

            $prevStyle = $style;
        }

    }//end process()


}//end class

?>
