<?php

namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Exception;

/**
 * @phpstan-import-type ConfigType from Config
 */
class Script_Action extends Action_Extender {

	public function execute(Request $Request): Request_Response {
		$scripts = $this->config['hooks']['scripts'];
		$script_name = $Request->getArgument('name');

		if (empty($scripts[$script_name])) {
			throw new Exception("Script $script_name not found");
		}

		$this->Printer->success("Running script $script_name");
		$cmd = $scripts[$script_name];
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		print(implode("\n", $result) . "\n");

		if ($result_code !== 0) {
			throw new Exception("There was an error running script $script_name");
		}
		$this->Printer->success("Script $script_name ran successfully");
		return new Request_Response(true, "Script $script_name ran successfully");
	}
}