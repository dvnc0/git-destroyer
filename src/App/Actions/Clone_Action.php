<?php

namespace Git_Destroyer\Actions;

use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Git_Destroyer\Actions\Action_Extender;
use Git_Destroyer\Tasks\Clone_Task;

/**
 * @phpstan-import-type ConfigType from Config
 */
class Clone_Action extends Action_Extender
{

	protected function getTask(): Clone_Task {
		return new Clone_Task($this->Application);
	}

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