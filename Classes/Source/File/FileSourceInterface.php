<?php

namespace EasyDeployWorkflows\Source\File;


/**
 * Download Interface
 */
interface FileSourceInterface extends \EasyDeployWorkflows\Source\SourceInterface {



	/**
	 * @return string
	 */
	public function getFileName();


}
