<?php

namespace Git_Destroyer\Actions;

use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Git_Destroyer\Actions\Action_Extender;
use Git_Destroyer\Tasks\Clone_Task;

class Clone_Action extends Action_Extender
{
	/**
	 * Gets a Clone_Task instance
	 * 
	 * @return Clone_Task
	 * @codeCoverageIgnore
	 */
	protected function getTask(): Clone_Task {
		return new Clone_Task($this->Application);
	}

	/**
	 * Execute clone_action, runs clone_task
	 * 
	 * @param Request $Request Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		$Runner            = $this->getTaskRunner();
		$Task              = $this->getTask();
		$Task->task_config = [
			'path' => $Request->getArgument('path')
		];
		$Runner->run($Task);

		return new Request_Response(TRUE, "Cloned repository");
	}
}