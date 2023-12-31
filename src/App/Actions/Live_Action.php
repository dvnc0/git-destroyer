<?php

namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Exception;

class Live_Action extends Action_Extender
{
	
	/**
	 * Execute the action
	 *
	 * @param Request $Request The Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		$has_live = $this->config['has_live'];

		if (!$has_live) {
			$this->Printer->error("This repository does not have a live branch");
			return new Request_Response(FALSE, "This repository does not have a live branch");
		}

		$live_branch    = $this->config['live'];
		$current_branch = $this->Git->getCurrentBranch();

		$pre_hooks  = $this->config['hooks']['live']['pre'];
		$post_hooks = $this->config['hooks']['live']['post'];

		$this->checkForUncommittedChanges();
		$this->checkForUntrackedFiles();

		$this->runHook($pre_hooks, "Running pre live hooks...");

		$this->Printer->success("Checking out $live_branch branch...");
		$this->Git->checkoutBranch($live_branch);

		$this->Printer->success("Pulling $live_branch branch...");
		$this->Git->pullFromRemote();

		$this->Printer->success("Merging $current_branch branch into $live_branch branch...");
		$this->Git->mergeBranch($current_branch);

		$this->runHook($post_hooks, "Running post live hooks...");

		$this->Printer->success("Pushing $live_branch branch...");
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