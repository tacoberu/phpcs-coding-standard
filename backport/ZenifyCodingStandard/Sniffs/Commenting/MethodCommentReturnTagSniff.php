<?php

/**
 * This file is part of Zenify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace ZenifyCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff as PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer\Files\File as PHP_CodeSniffer_File;
use ZenifyCodingStandard\Helper\Commenting\MethodDocBlock;


/**
 * Rules:
 * - Getters should have @return tag (except for {@inheritdoc}).
 */
final class MethodCommentReturnTagSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * @var string[]
	 */
	private $getterMethodPrefixes = ['get', 'is', 'has', 'will', 'should'];


	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		return [T_FUNCTION];
	}


	/**
	 * {@inheritdoc}
	 */
	public function process(PHP_CodeSniffer_File $file, $position)
	{
		$methodName = $file->getDeclarationName($position);
		$isGetterMethod = $this->guessIsGetterMethod($methodName);
		if ($isGetterMethod === FALSE) {
			return;
		}

		if (MethodDocBlock::hasMethodDocBlock($file, $position) === FALSE) {
			$file->addError('Getters should have docblock.', $position, '');
			return;
		}

		$commentString = MethodDocBlock::getMethodDocBlock($file, $position);

		if (strpos($commentString, '{@inheritdoc}') !== FALSE) {
			return;
		}

		if (strpos($commentString, '@return') !== FALSE) {
			return;
		}

		$file->addError('Getters should have @return tag (except {@inheritdoc}).', $position, '');
	}


	/**
	 * @param string $methodName
	 * @return bool
	 */
	private function guessIsGetterMethod($methodName)
	{
		foreach ($this->getterMethodPrefixes as $getterMethodPrefix) {
			if (strpos($methodName, $getterMethodPrefix) === 0) {
				if (strlen($methodName) === strlen($getterMethodPrefix)) {
					return TRUE;
				}

				$endPosition = strlen($getterMethodPrefix);
				$firstLetterAfterGetterPrefix = $methodName[$endPosition];

				if (ctype_upper($firstLetterAfterGetterPrefix)) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

}
