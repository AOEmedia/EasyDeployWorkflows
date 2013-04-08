<?php

namespace EasyDeployWorkflows\Source;



/**
 * Download Source that uses the standard Downloader
 * Following Source Formats are supported:
Local file:

Example Package Path: /home/user/mypackage.tar.gz

Web:

Example Package Path: http://user:password@host.de/path/mypackage.tar.gz

SSH (RSYNC is used to copy)

Example Package Path: ssh://user@host.de:/path/mypackage.tar.gz

SSH to a folder

Example Package Path: ssh://user@host.de:/path/ (all files in that path will be transfered)
 *
 */
class DownloadSource implements DownloadSourceInterface  {

	/**
	 * @var string
	 */
	protected $source;



	public function __construct($source = '') {
		$this->setSourceSpecification($source);
	}



	/**
	 * @param string $source
	 */
	public function setSourceSpecification($source) {
		$this->source = $source;
	}

	/**
	 * @return string
	 */
	public function getSourceSpecification() {
		return $this->source;
	}



	public function getShortExplain() {
		return 'Download from:'.$this->source;
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
