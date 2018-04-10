<?php

/**
 * This file is part of Zenify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace ZenifyCodingStandard\Helper\Commenting;

use PHP_CodeSniffer\Files\File as PHP_CodeSniffer_File;


final class MethodDocBlock
{

	/**
	 * @param PHP_CodeSniffer_File $file
	 * @param int $position
	 * @return bool
	 */
	public static function hasMethodDocBlock(PHP_CodeSniffer_File $file, $position)
	{
		$tokens = $file->getTokens();
		$currentToken = $tokens[$position];
		$docBlockClosePosition = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $position);

		if ($docBlockClosePosition === FALSE) {
			return FALSE;
		}

		$docBlockCloseToken = $tokens[$docBlockClosePosition];
		if ($docBlockCloseToken['line'] === ($currentToken['line'] - 1)) {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param PHP_CodeSniffer_File $file
	 * @param int $position
	 * @return string
	 */
	public static function getMethodDocBlock(PHP_CodeSniffer_File $file, $position)
	{
		if ( ! self::hasMethodDocBlock($file, $position)) {
			return '';
		}

		$commentStart = $file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $position - 1);
		$commentEnd = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $position - 1);
		return $file->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
	}

}
