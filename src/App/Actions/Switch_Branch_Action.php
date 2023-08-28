<?php
namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;

class Switch_Branch_Action extends Action_Extender {

	/**
	 * Execute the action
	 *
	 * @param Request $Request The Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		$this->checkForUncommittedChanges();
		$this->checkForUntrackedFiles();

		$branch_name = $Request->getArgument('branch_name');

		$this->Printer->success("Switching to branch $branch_name");
		$this->Git->checkoutBranch($branch_name);
		$this->Git->pullFromRemote();

		return new Request_Response(true, "Switched to branch $branch_name");
	}
}