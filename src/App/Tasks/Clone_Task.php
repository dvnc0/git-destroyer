<?php

namespace Git_Destroyer\Tasks;

use Clyde\Tasks\Task_Base;
use Clyde\Tasks\Task_Response;
use Git_Destroyer\Utils\Config;
use Git_Destroyer\Utils\Git;

class Clone_Task extends Task_Base
{

	/**
	 * @@var non-empty-string
	 */
	public string $task_message = "Cloning repo... ";

	/**
	 * @var array|bool
	 */
	protected array|bool $config;

	/**
	 * @var Git
	 */
	protected Git $Git;

	/**
	 * @var array
	 */
	public array $task_config;

	/**
	 * Init the task
	 *
	 * @return void
	 */
	protected function init() {
		$Config       = new Config;
		$this->config = $Config->loadConfigFromFile(ROOT . '/' . Config::CONFIG_NAME);
		$this->Git    = new Git;
	}

	/**
	 * Execute the task
	 *
	 * @return Task_Response
	 */
	public function execute(): Task_Response {
		$this->init();
		$this->Git->cloneRepo($this->config['repo_url'], $this->task_config['path']);
		return new Task_Response(TRUE, "Cloned repository");
	}
}