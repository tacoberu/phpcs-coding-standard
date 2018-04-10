<?php

/**
 * This file is part of Zenify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace ZenifyCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Sniffs\Sniff as PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer\Files\File as PHP_CodeSniffer_File;


/**
 * Rules:
 * - Class name after new/instanceof should not start with slash
 */
final class ClassNamesWithoutPreSlashSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * @var string[]
	 */
	private $excludedClassNames = [
		'DateTime', 'stdClass', 'splFileInfo', 'Exception'
	];


	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		return [T_NEW, T_INSTANCEOF];
	}


	/**
	 * {@inheritdoc}
	 */
	public function process(PHP_CodeSniffer_File $file, $position)
	{
		$tokens = $file->getTokens();
		$classNameStart = $tokens[$position + 2]['content'];

		if ($classNameStart === '\\') {
			if ($this->isExcludedClassName($tokens[$position + 3]['content'])) {
				return;
			}
			$file->addError('Class name after new/instanceof should not start with slash.', $position, '');
		}
	}


	/**
	 * @param string $className
	 * @return bool
	 */
	private function isExcludedClassName($className)
	{
		if (in_array($className, $this->excludedClassNames)) {
			return TRUE;
		}
		return FALSE;
	}

}
