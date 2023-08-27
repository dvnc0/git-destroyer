<?php

namespace Git_Destroyer\Actions;

use Clyde\Actions\Action_Base;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Clyde\Tools\Emoji;
use Clyde\Tools\Input;
use Git_Destroyer\Utils\Git;
use Git_Destroyer\Utils\Config;
use Exception;

/**
 * @phpstan-import-type ConfigType from Config
 */
class Script_Action extends Action_Base {
	use Action_Trait;

	public function execute(Request $Request): Request_Response {
		$config = $this->getConfig();

		$scripts = $config['hooks']['scripts'];
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