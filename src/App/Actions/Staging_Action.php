<?php

namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Exception;

class Staging_Action extends Action_Extender
{
	
	/**
	 * Execute the action
	 *
	 * @param Request $Request The Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		$has_staging = $this->config['has_staging'];

		if (!$has_staging) {
			$this->Printer->error("This repository does not have a staging branch");
			return new Request_Response(FALSE, "This repository does not have a staging branch");
		}

		$staging_branch = $this->config['staging'];
		$current_branch = $this->Git->getCurrentBranch();

		$pre_hooks  = $this->config['hooks']['staging']['pre'];
		$post_hooks = $this->config['hooks']['staging']['post'];

		$this->checkForUncommittedChanges();
		$this->checkForUntrackedFiles();

		$this->runHook($pre_hooks, "Running pre staging hooks...");

		$this->Printer->success("Checking out $staging_branch branch...");
		$this->Git->checkoutBranch($staging_branch);

		$this->Printer->success("Pulling $staging_branch branch...");
		$this->Git->pullFromRemote();

		$this->Printer->success("Merging $current_branch branch into $staging_branch branch...");
		$this->Git->mergeBranch($current_branch);

		$this->runHook($post_hooks, "Running post staging hooks...");

		$this->Printer->success("Pushing $staging_branch branch...");
		$this->Git->pushToRemote();

		$this->Printer->success("Checking out $current_branch branch...");
		$this->Git->checkoutBranch($current_branch);

		return new Request_Response(TRUE, "Changes have been staged");
	}

	/**
	 * Run a hook
	 *
	 * @param array  $hooks   The hooks to run
	 * @param string $message The message to display
	 * @return void
	 */
	protected function runHook(array $hooks, string $message): void {
		$this->Printer->success($message);
		foreach($hooks as $hook) {
			$cmd                    = $hook;
			[$result, $result_code] = $this->execWrapper($cmd);

			if ($result_code !== 0) {
				throw new Exception("There was an error running hook: $hook");
			}
		}
	}
}