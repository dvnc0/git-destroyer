<?php
namespace Git_Destroyer\Actions;

use Clyde\Actions\Action_Base;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Clyde\Tools\Emoji;
use Clyde\Tools\Input;
use Git_Destroyer\Utils\Git;
use Git_Destroyer\Utils\Config;

/**
 * @phpstan-import-type ConfigType from Config
 */
class New_Branch_Action extends Action_Base {

	use Action_Trait;

	/**
	 * creates the config file
	 *
	 * @param Request $Request  Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		$Git = $this->getGitInstance();
		$config = $this->getConfig();
		
		$this->Printer->success("Checking for uncommitted changes...");
		$this->checkForUncommittedChanges($Git);
		
		$this->Printer->success("Checking for untracked files...");
		$this->checkForUntrackedFiles($Git);

		$this->Printer->success("Running hooks...");
		$pre_hooks = $config['hooks']['new_branch']['pre'];
		$post_hooks = $config['hooks']['new_branch']['post'];

		foreach ($pre_hooks as $hook) {
			$this->Printer->message("Running pre hook: " . $hook);
			$this->runHook($hook);
		}

		$this->Printer->success("Creating new branch...");
		$branch_name = $Request->getArgument('branch_name');
		$Git->createNewBranch($branch_name);
		$this->Printer->success("New branch created!");

		$this->Printer->success("Running hooks...");
		foreach ($post_hooks as $hook) {
			$this->Printer->message("Running post hook: " . $hook);
			$this->runHook($hook);
		}

		return new Request_Response(true);
	}

	/**
	 * Run a hook
	 *
	 * @param string $hook the script/hook to run
	 * @return void
	 */
	protected function runHook(string $hook): void {
		$this->Printer->message("Running pre hook: " . $hook);
		$result = [];
		$result_code = 0;
		exec($hook, $result, $result_code);

		if ($result_code !== 0) {
			$this->Printer->error("There was an error running: " . $hook);
			exit(1);
		}
	}
}