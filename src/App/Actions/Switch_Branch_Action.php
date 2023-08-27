<?php
namespace Git_Destroyer\Actions;

use Clyde\Actions\Action_Base;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Exception;

class Switch_Branch_Action extends Action_Base {
	use Action_Trait;

	public function execute(Request $request): Request_Response {
		$Git = $this->getGitInstance();

		$this->checkForUncommittedChanges($Git);
		$this->checkForUntrackedFiles($Git);

		$branch_name = $request->getArgument('branch_name');

		$cmd = "git checkout $branch_name";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error switching to branch $branch_name");
		}

		return new Request_Response(true, "Switched to branch $branch_name");
	}
}