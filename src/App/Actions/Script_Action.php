<?php

namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Exception;

class Script_Action extends Action_Extender
{

	/**
	 * Execute the action
	 *
	 * @param Request $Request The Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		$scripts     = $this->config['hooks']['scripts'];
		$script_name = $Request->getArgument('name');

		if (empty($scripts[$script_name])) {
			throw new Exception("Script $script_name not found");
		}

		$this->Printer->success("Running script $script_name");
		$cmd                    = $scripts[$script_name];
		[$result, $result_code] = $this->execWrapper($cmd);

		$this->printArrayToConsole($result);

		if ($result_code !== 0) {
			throw new Exception("There was an error running script $script_name");
		}
		$this->Printer->success("Script $script_name ran successfully");
		return new Request_Response(TRUE, "Script $script_name ran successfully");
	}

	/**
	 * Print to console
	 *
	 * @param array $array array to print out
	 * @return void
	 * @codeCoverageIgnore
	 */
	protected function printArrayToConsole(array $array): void {
		foreach ($array as $line) {
			$this->Printer->message($line);
		}
	}
}