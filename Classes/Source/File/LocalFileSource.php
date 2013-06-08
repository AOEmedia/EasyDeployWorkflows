<?php

namespace EasyDeployWorkflows\Source\File;



/**
 *  Source that uses a local file
 *  (also used for acceptance tests with local fixtures)
 *
 */
class LocalFileSource implements FileSourceInterface  {

	/**
	 * @var string
	 */
	protected $source;

	public function __construct($source = '') {
		$this->setSource($source);
	}

	/**
	 * Downloads the given source on the given server in the given parent path
	 *
	 * @return void
	 */
	public function getDownloadCommand($parentFolder) {
		return 'cp  '.$this->source.' '.$parentFolder;
	}



	/**
	 * @param string $source
	 */
	public function setSource($source) {
		$this->source = $source;
	}



	/**
	 * @return string
	 */
	public function getShortExplain() {
		return 'Copy from:'.$this->source;
	}

	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->getFilenameFromPath($this->source);
	}

	/**
	 * @param $path
	 * @return string
	 */
	protected function getFilenameFromPath($path) {
		$dir = dirname($path).DIRECTORY_SEPARATOR;
		return str_replace($dir,'',$path);
	}
}
