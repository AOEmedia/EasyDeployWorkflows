<?php

namespace EasyDeployWorkflows\Source\File;
use EasyDeployWorkflows\Source\SourceInterface;

/**
 * Download Interface
 */
interface FileSourceInterface extends SourceInterface {

	/**
	 * @return string
	 */
	public function getFileName();

	/**
	 * @return string
	 */
	public function getFileNameWithOutExtension();

}
