<?php

/**
 * This file is part of Zenify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace ZenifyCodingStandard\Sniffs\Naming;

use PHP_CodeSniffer\Sniffs\Sniff as PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer\Files\File as PHP_CodeSniffer_File;


/**
 * Rules:
 * - Interface should have suffix "Interface"
 */
final class InterfaceNameSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * @var PHP_CodeSniffer_File
	 */
	private $file;

	/**
	 * @var int
	 */
	private $position;


	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		return [T_INTERFACE];
	}


	/**
	 * {@inheritdoc}
	 */
	public function process(PHP_CodeSniffer_File $file, $position)
	{
		$this->file = $file;
		$this->position = $position;

		$interfaceName = $this->getInterfaceName();
		if ((strlen($interfaceName) - strlen('Interface')) === strrpos($interfaceName, 'Interface')) {
			return;
		}

		$file->addError('Interface should have suffix "Interface".', $position, '');
	}


	/**
	 * @return string|FALSE
	 */
	private function getInterfaceName()
	{
		$namePosition = $this->file->findNext(T_STRING, $this->position);
		if ( ! $namePosition) {
			return FALSE;
		}

		return $this->file->getTokens()[$namePosition]['content'];
	}

}
