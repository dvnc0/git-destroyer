<?php
namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;

/**
 * @phpstan-import-type ConfigType from Config
 */
class New_Branch_Action extends Action_Extender {

	/**
	 * creates the config file
	 *
	 * @param Request $Request  Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		
		$this->Printer->success("Checking for uncommitted changes...");
		$this->checkForUncommittedChanges();
		
		$this->Printer->success("Checking for untracked files...");
		$this->checkForUntrackedFiles();

		$this->Printer->success("Running hooks...");
		$pre_hooks = $this->config['hooks']['new_branch']['pre'];
		$post_hooks = $this->config['hooks']['new_branch']['post'];

		foreach ($pre_hooks as $hook) {
			$this->Printer->message("Running pre hook: " . $hook);
			$this->runHook($hook);
		}

		$this->Printer->success("Creating new branch...");
		$branch_name = $Request->getArgument('branch_name');
		$this->Git->createNewBranch($branch_name);
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