<?php

namespace EasyDeployWorkflows\EnvironmentSettings;


/**
 * SettingsMigrationInterface
 */
interface SettingsMigrationInterface  {
	/**
	 * Downloads the given source on the given server in the given parent path
	 *
	 * @return void
	 */
	public function migrate($environmentName);

	/**
	 * For usage in logs
	 *
	 * @return string
	 */
	public function getSettingsValue($settingsSpecification);

}
