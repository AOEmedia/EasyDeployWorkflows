<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Logger\Logger;
use EasyDeployWorkflows\Tasks;


class CreateMissingFolder extends Tasks\Common\RunCommand {

	/**
	 * @var string
	 */
	protected $folder;

	/**
	 * @param string $folder
	 */
	public function setFolder($folder) {
		$this->folder = $folder;
	}

	/**
	 * @return string
	 */
	public function getCommand() {
		return "mkdir -p " . $this->folder;
	}

	/**
	 * @return boolean
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->folder)) {
			throw new InvalidConfigurationException('Folder not set');
		}

		return true;
	}
}
