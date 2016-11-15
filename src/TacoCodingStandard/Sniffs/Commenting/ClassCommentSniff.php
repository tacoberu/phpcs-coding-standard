<?php
/**
 * @author    Martin Takáč <martin@takac.name>
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace TacoCodingStandard\Sniffs\Commenting;

use PEAR_Sniffs_Commenting_FileCommentSniff;
use PHP_CodeSniffer_File,
	PHP_CodeSniffer_Tokens;

/**
 * Parses and verifies the doc comments for classes.
 */
class ClassCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff
{


    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = array(
		   '@category'   => array(
				'required'       => false,
				'allow_multiple' => false,
			),
		   '@package'    => array(
				'required'       => false,
				'allow_multiple' => false,
			),
		   '@subpackage' => array(
				'required'       => false,
				'allow_multiple' => false,
			),
		   '@author' => array(
				'required'       => true,
				'allow_multiple' => true,
			),
		   '@copyright'  => array(
				'required'       => false,
				'allow_multiple' => true,
			),
		   '@license'    => array(
				'required'       => false,
				'allow_multiple' => false,
			),
		   '@version'    => array(
				'required'       => false,
				'allow_multiple' => false,
			),
		   '@link'       => array(
				'required'       => false,
				'allow_multiple' => true,
			),
		   '@see'        => array(
				'required'       => false,
				'allow_multiple' => true,
			),
		   '@since'      => array(
				'required'       => false,
				'allow_multiple' => false,
			),
		   '@deprecated' => array(
				'required'       => false,
				'allow_multiple' => false,
			),
	  );



    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens    = $phpcsFile->getTokens();
        $type      = strtolower($tokens[$stackPtr]['content']);
        $errorData = array($type);

        $find   = PHP_CodeSniffer_Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'no');
            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'yes');
        }

        // Try and determine if this is a file comment instead of a class comment.
        // We assume that if this is the first comment after the open PHP tag, then
        // it is most likely a file comment instead of a class comment.
        if ($tokens[$commentEnd]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            $start = ($tokens[$commentEnd]['comment_opener'] - 1);
        } else {
            $start = $phpcsFile->findPrevious(T_COMMENT, ($commentEnd - 1), null, true);
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $start, null, true);
        if ($tokens[$prev]['code'] === T_OPEN_TAG) {
            $prevOpen = $phpcsFile->findPrevious(T_OPEN_TAG, ($prev - 1));
            if ($prevOpen === false) {
                // This is a comment directly after the first open tag,
                // so probably a file comment.
                $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
                return;
            }
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr, 'WrongStyle');
            return;
        }

        // Check each tag.
        $this->processTags($phpcsFile, $stackPtr, $tokens[$commentEnd]['comment_opener']);

    }//end process()


    /**
     * Process the version tag.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param array                $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processVersion(PHP_CodeSniffer_File $phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            if ((strstr($content, 'Release:') === false)) {
                $error = 'Invalid version "%s" in doc comment; consider "Release: <package_version>" instead';
                $data  = array($content);
                $phpcsFile->addWarning($error, $tag, 'InvalidVersion', $data);
            }
        }

    }//end processVersion()


}//end class
