<?php

$easyDeployAbstractServerClass =
								is_file(EASYDEPLOY_WORKFLOW_ROOT . '../EasyDeploy/Classes/AbstractServer.php')
								? EASYDEPLOY_WORKFLOW_ROOT . '../EasyDeploy/Classes/AbstractServer.php'
								: EASYDEPLOY_WORKFLOW_ROOT . 'Tests/Stubs/EasyDeploy/Classes/AbstractServer.php';

require_once $easyDeployAbstractServerClass;



/**
 * @author Chetan Thapliyal <chetan.thapliyal@aoe.com>
 */
class AbstractServerTaskTest extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function getServersByDefaultReturnsIterableValue() {
		$task = $this->getMockForAbstractClass('EasyDeployWorkflows\Tasks\AbstractServerTask');

		/** @var \EasyDeployWorkflows\Tasks\AbstractServerTask $task */
		$servers = $task->getServers();
		$isIterable = is_array($servers) || ($servers instanceof \Traversable);
		$this->assertTrue($isIterable, 'Return value from `AbstractTask::getServers()` must be iterable.');
	}

	/**
	 * Test setting single server.
	 *
	 * @test
	 */
	public function addServerByName() {
		$task = $this->getMockForAbstractClass('EasyDeployWorkflows\Tasks\AbstractServerTask', array(), '', TRUE, TRUE, TRUE, array('getServer'));
		$task
				->expects($this->any())
				->method('getServer')
				->with($this->anything())
				->will($this->returnValue($this->getMockForAbstractClass('EasyDeploy_AbstractServer')));

		/** @var \EasyDeployWorkflows\Tasks\AbstractServerTask $task */
		$task->addServersByName(array('testServer'));

		$servers = $task->getServers();
		$isIterable = is_array($servers) || ($servers instanceof \Traversable);

		$this->assertTrue($isIterable, 'Return value from `AbstractTask::getServers()` must be iterable.');
		$this->assertCount(1, $servers, 'Invalid return value from `AbstractTask::getServers()`. Must be 1.');
	}

	/**
	 * Test setting multiple servers.
	 *
	 * @test
	 */
	public function addServersByName() {
		$task = $this->getMockForAbstractClass('EasyDeployWorkflows\Tasks\AbstractServerTask', array(), '', TRUE, TRUE, TRUE, array('getServer'));
		$task
				->expects($this->any())
				->method('getServer')
				->with($this->anything())
				->will($this->returnValue($this->getMockForAbstractClass('EasyDeploy_AbstractServer')));

		/** @var \EasyDeployWorkflows\Tasks\AbstractServerTask $task */
		$task->addServersByName(array('testServer1', 'testServer2'));

		$servers = $task->getServers();
		$isIterable = is_array($servers) || ($servers instanceof \Traversable);

		$this->assertTrue($isIterable, 'Return value from `AbstractTask::getServers()` must be iterable.');
		$this->assertCount(2, $servers, 'Invalid return value from `AbstractTask::getServers()`. Must be 2.');
	}
}
