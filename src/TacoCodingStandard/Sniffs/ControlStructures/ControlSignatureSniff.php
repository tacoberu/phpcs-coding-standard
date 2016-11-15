<?php

/**
 * Copyright (c) 2016 Martin Takáč (http://martin.takac.name)
 * @credits
 *	- Tomas Votruba (http://tomasvotruba.cz)
 */

namespace TacoCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;


/**
 * Rules:
 * - Same as @see Squiz_Sniffs_ControlStructures_ControlSignatureSniff
 * - This modification allows comments
 * - Statement %s must be in new line.
 */
final class ControlSignatureSniff implements PHP_CodeSniffer_Sniff
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
	 * @var array
	 */
	private $tokens;


	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		return [T_TRY, T_CATCH, T_DO, T_WHILE, T_FOR, T_IF, T_FOREACH, T_ELSE, T_ELSEIF];
	}


	/**
	 * {@inheritdoc}
	 */
	public function process(PHP_CodeSniffer_File $file, $position)
	{
		$this->file = $file;
		$this->position = $position;
		$this->tokens = $tokens = $file->getTokens();

		$this->ensureSingleSpaceAfterKeyword();
		$this->ensureSingleSpaceAfterClosingParenthesis();
		$this->ensureNewlineAfterOpeningBrace();
		$this->ensureElseOrCatchInNewline();
	}



	/**
	 * }EOLelse {
	 * }EOLelseif (...) {
	 * }EOLcatch (...) {
	 */
	private function ensureElseOrCatchInNewline()
	{
		if ($this->tokens[$this->position]['code'] === T_ELSE
				|| $this->tokens[$this->position]['code'] === T_ELSEIF
				|| $this->tokens[$this->position]['code'] === T_CATCH
				) {
			$current = $this->tokens[$this->position];
			if ($closer = $this->file->findPrevious(T_CLOSE_CURLY_BRACKET, ($this->position - 1))) {
				if ($current['line'] == ($this->tokens[$closer]['line'])) {
					$this->file->addError('Statement %s must be in new line.', $closer, 'ElseMustBeInNewLine', [$current['content']]);
				}
			}
		}
	}



	private function ensureSingleSpaceAfterKeyword()
	{
		$found = 1;
		if ($this->tokens[($this->position + 1)]['code'] !== T_WHITESPACE) {
			$found = 0;

		} elseif ($this->tokens[($this->position + 1)]['content'] !== ' ') {
			if (strpos($this->tokens[($this->position + 1)]['content'], $this->file->eolChar) !== FALSE) {
				$found = 'newline';

			} else {
				$found = strlen($this->tokens[($this->position + 1)]['content']);
			}
		}

		if ($found !== 1) {
			$error = 'Expected 1 space after %s keyword; %s found';
			$data = [
				strtoupper($this->tokens[$this->position]['content']),
				$found,
			];
			$this->file->addError($error, $this->position, 'SpaceAfterKeyword', $data);
		}
	}


	private function ensureSingleSpaceAfterClosingParenthesis()
	{
		if (isset($this->tokens[$this->position]['parenthesis_closer']) === TRUE
			&& isset($this->tokens[$this->position]['scope_opener']) === TRUE
		) {
			$closer = $this->tokens[$this->position]['parenthesis_closer'];
			$opener = $this->tokens[$this->position]['scope_opener'];
			$content = $this->file->getTokensAsString(($closer + 1), ($opener - $closer - 1));
			if ($content !== ' ') {
				$error = 'Expected 1 space after closing parenthesis; found "%s"';
				$data = [str_replace($this->file->eolChar, '\n', $content)];
				$this->file->addError($error, $closer, 'SpaceAfterCloseParenthesis', $data);
			}
		}
	}


	private function ensureNewlineAfterOpeningBrace()
	{
		if (isset($this->tokens[$this->position]['scope_opener']) === TRUE) {
			$opener = $this->tokens[$this->position]['scope_opener'];
			$next = $this->file->findNext(T_WHITESPACE, ($opener + 1), NULL, TRUE);
			$found = ($this->tokens[$next]['line'] - $this->tokens[$opener]['line']);
			if ($found !== 1) {
				if ( ! $this->isCommentOnTheSameLine($this->file, $opener)) {
					$error = 'Expected 1 newline after opening brace; %s found';
					$data = [$found];
					$this->file->addError($error, $opener, 'NewlineAfterOpenBrace', $data);
				}
			}

		} elseif ($this->tokens[$this->position]['code'] === T_WHILE) {
			$closerPosition = $this->tokens[$this->position]['parenthesis_closer'];
			$this->ensureZeroSpacesAfterParenthesisCloser($closerPosition);
		}
	}


	/**
	 * @param PHP_CodeSniffer_File $file
	 * @param int $position
	 * @return bool
	 */
	private function isCommentOnTheSameLine(PHP_CodeSniffer_File $file, $position)
	{
		$isComment = $file->findNext(T_COMMENT, ($position + 1), NULL);
		if ($this->tokens[$isComment]['line'] === $this->tokens[$position]['line']) {
			return TRUE;
		}
		return FALSE;
	}


	/**
	 * @param int $closerPosition
	 */
	private function ensureZeroSpacesAfterParenthesisCloser($closerPosition)
	{
		$found = 0;
		if ($this->tokens[($closerPosition + 1)]['code'] === T_WHITESPACE) {
			if (strpos($this->tokens[($closerPosition + 1)]['content'], $this->file->eolChar) !== FALSE) {
				$found = 'newline';

			} else {
				$found = strlen($this->tokens[($closerPosition + 1)]['content']);
			}
		}
		if ($found !== 0) {
			$error = 'Expected 0 spaces before semicolon; %s found';
			$data = [$found];
			$this->file->addError($error, $closerPosition, 'SpaceBeforeSemicolon', $data);
		}
	}

}
