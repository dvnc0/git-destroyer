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
class Clone_Action extends Action_Base {
	use Action_Trait;

	public function execute(Request $Request): Request_Response {
		$config = $this->getConfig();
		$Git = $this->getGitInstance();

		$clone_url = $config['repo_url'];

		$this->Printer->success("Cloning repo...");
		$Git->cloneRepo($clone_url, $Request->getArgument('path'));

		return new Request_Response(true, "Cloned repository");
	}
}