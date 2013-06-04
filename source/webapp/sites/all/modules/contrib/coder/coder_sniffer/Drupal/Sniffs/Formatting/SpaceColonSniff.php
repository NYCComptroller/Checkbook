<?php
/**
 * Drupal_Sniffs_Formatting_SpaceColonSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks that if ":" is used as operator the spacing is correct.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Drupal_Sniffs_Formatting_SpaceColonSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_INLINE_ELSE);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore colons in switch() constructs.
        if (empty($tokens[$stackPtr]['conditions']) === false) {
            $condition = array_pop($tokens[$stackPtr]['conditions']);
            if ($condition === T_SWITCH) {
                return;
            }
        }

        // Reuse the standard operator sniff now.
        $sniff = new Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff();
        $sniff->process($phpcsFile, $stackPtr);

    }//end process()


}//end class

?>
