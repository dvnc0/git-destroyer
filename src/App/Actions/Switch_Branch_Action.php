<?php
namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Exception;

class Switch_Branch_Action extends Action_Extender {

	public function execute(Request $Request): Request_Response {
		$this->checkForUncommittedChanges();
		$this->checkForUntrackedFiles();

		$branch_name = $Request->getArgument('branch_name');

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