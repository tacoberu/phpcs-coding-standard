<?php

/**
 * This file is part of Zenify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace ZenifyCodingStandard\Sniffs\Php;

use PHP_CodeSniffer\Sniffs\Sniff as PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer\Files\File as PHP_CodeSniffer_File;


/**
 * Rules:
 * - Short array syntax should be used, instead of traditional one.
 */
final class ShortArraySyntaxSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		return [T_ARRAY];
	}


	/**
	 * {@inheritdoc}
	 */
	public function process(PHP_CodeSniffer_File $file, $position)
	{
		$tokens = $file->getTokens();
		$currentToken = $tokens[$position];
		if ($currentToken['code'] === T_ARRAY) {
			$file->addError('Short array syntax should be used, instead of traditional one', $position, '');
		}
	}

}
