<?php
/**
 * @author    Martin Takáč <martin@takac.name>
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace TacoCodingStandard\Sniffs\Commenting;

use PEAR_Sniffs_Commenting_FileCommentSniff;


final class FileCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff
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
		   '@author'     => array(
				'required'       => false,
				'allow_multiple' => true,
			),
		   '@copyright'  => array(
				'required'       => false,
				'allow_multiple' => true,
			),
		   '@license'    => array(
				'required'       => true,
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

}
