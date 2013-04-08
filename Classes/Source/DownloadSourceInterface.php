<?php

namespace EasyDeployWorkflows\Source;


/**
 * Download Interface
 */
interface DownloadSourceInterface  {
	/**
	 * @return string - the specification of the download - should be understandable by the Downloader Object
	 */
	public function getSourceSpecification();

	/**
	 * Short info for the log
	 *
	 * @return string
	 */
	public function getShortExplain();

	/**
	 * The filename part of the download source
	 * @return string
	 */
	public function getFileName();
}
