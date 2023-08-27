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
class Push_Action extends Action_Base {
	use Action_Trait;

	public function execute(Request $Request): Request_Response {
		$config = $this->getConfig();
		$Git = $this->getGitInstance();

		$this->checkForUncommittedChanges($Git);
		$this->checkForUntrackedFiles($Git);	
		$this->Printer->success("Pulling files...");
		$Git->pullFromRemote();

		$this->Printer->success("Pushing files...");
		$Git->pushToRemote();

		return new Request_Response(true, "Files committed");
	}
}