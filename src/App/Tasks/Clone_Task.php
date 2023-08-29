<?php

namespace Git_Destroyer\Tasks;

use Clyde\Tasks\Task_Base;
use Clyde\Tasks\Task_Response;
use Git_Destroyer\Utils\Config;
use Git_Destroyer\Utils\Git;

class Clone_Task extends Task_Base
{

	public string $task_message = "Cloning repo... ";

	protected array|bool $config;

	protected Git $Git;

	public array $task_config;

	protected function init() {
		$Config       = new Config;
		$this->config = $Config->loadConfigFromFile(ROOT . '/' . Config::CONFIG_NAME);
		$this->Git    = new Git;
	}

	public function execute(): Task_Response {
		$this->init();
		$this->Git->cloneRepo($this->config['repo_url'], $this->task_config['path']);
		return new Task_Response(TRUE, "Cloned repository");
	}
}