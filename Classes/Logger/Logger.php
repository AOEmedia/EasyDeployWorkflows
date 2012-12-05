<?php

namespace EasyDeployWorkflows\Logger;

/**
 * Simple Singleton that is used for Logging the deployment
 */
class Logger {
	/**
	 * @var Logger
	 */
	protected static $instance;

	/**
	 * @var string
	 */
	protected $logFile;

	/**
	 * @var ScreenBackend
	 */
	protected $screenBackend;

	/**
	 * @var int
	 */
	protected $logIndentLevel = 0;

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_WARNING = "WARNING";

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_ERROR = "ERROR";

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_INFO = "INFO";

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_SUCCESS = "OK";

	/**
	 * @var string
	 */
	const MESSAGE_TYPE_COMMANDOUTPUT = "COMMANDOUTPUT";

	/**
	 * creating new object is denied (singleton pattern)
	 *  use \EasyDeployWorkflows\Logger\Logger::getInstance() instead
	 */
	private function __construct()	{
		if (!empty($_SERVER['SCRIPT_NAME'])) {
			if (substr($_SERVER['SCRIPT_NAME'], 0, 1) === '/') {
				$deployScript = $_SERVER['SCRIPT_NAME'];
			}
			else {
				$deployScript = $_SERVER['PWD'].'/'.$_SERVER['SCRIPT_NAME'];
			}
			$this->setLogFile(dirname($deployScript).'/deploy-log.txt');
		}
		$this->injectScreenBackend(new ScreenBackend());
	}

	public function injectScreenBackend(ScreenBackend $screenBackend) {
		$this->screenBackend = $screenBackend;
	}


	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	public function logToScreen($message, $type) {
		$messageIndented = $this->indentMessage($message);
		switch ($type) {
			case self::MESSAGE_TYPE_ERROR:
				$this->screenBackend->output(PHP_EOL.$messageIndented,'red');
			break;
			case self::MESSAGE_TYPE_SUCCESS:
				$this->screenBackend->output(' '.$message,'green');
			break;
			case self::MESSAGE_TYPE_WARNING:
				$this->screenBackend->output(PHP_EOL.$messageIndented,'blue');
			break;

			default:
				$this->screenBackend->output(PHP_EOL.$messageIndented,'gray');
			break;
		}
	}


	public function logToFile($message, $type) {
		if (empty($this->logFile)) {
			return;
		}
		$messageIndented = $this->indentMessage($type.': '.$message).PHP_EOL;
		file_put_contents($this->logFile, $messageIndented, FILE_APPEND);
	}

	/**
	 * @param string $message
	 * @param string $type
	 */
	public function log($message, $type=self::MESSAGE_TYPE_INFO) {
		switch ($type) {
			case self::MESSAGE_TYPE_INFO:
				$this->logToScreen($message,$type);
				$this->logToFile($message,$type);
				break;
			case self::MESSAGE_TYPE_COMMANDOUTPUT:
				$this->logToFile($message,$type);
				break;
			default:
				$this->logToScreen($message,$type);
				$this->logToFile($message,$type);
				break;
		}
	}

	public function logLogFileMessage() {
		if (!empty($this->logFile)) {
			$this->logIndentLevel = 0;
			$this->logToScreen('Check the Logfile for errors: "'.$this->logFile.'"'.PHP_EOL,self::MESSAGE_TYPE_ERROR);
		}
	}

	/**
	 * sets indent level up - so that messages are nicer formated
	 */
	public function addLogIndentLevel() {
		$this->logIndentLevel++;
	}

	/**
	 * sets indent level down - so that messages are nicer formated
	 */
	public function removeLogIndentLevel() {
		$this->logIndentLevel--;
		if ($this->logIndentLevel < 0) {
			$this->logIndentLevel = 0;
		}
	}

	/**
	 * Improve the string for visual output
	 * Adds an extra line before first level messages and indents the message with tabs
	 *
	 * @param $message
	 * @return string
	 */
	protected function indentMessage($message) {
		$message = str_repeat("\t",$this->logIndentLevel).$message;
		if ($this->logIndentLevel == 0) {
			$message = PHP_EOL.$message;
		}
		return $message;
	}


	/**
	 * @param string $logFile
	 */
	public function setLogFile($logFile)
	{
		$this->logFile = $logFile;
	}

	/**
	 * @return string
	 */
	public function getLogFile()
	{
		return $this->logFile;
	}


}