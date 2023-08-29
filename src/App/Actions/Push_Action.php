<?php

namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;

class Push_Action extends Action_Extender
{

	/**
	 * Execute the action
	 *
	 * @param Request $Request The Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		$this->checkForUncommittedChanges();
		$this->checkForUntrackedFiles();	
		$this->Printer->success("Pulling files...");
		$this->Git->pullFromRemote();

		$this->Printer->success("Pushing files...");
		$this->Git->pushToRemote();

		return new Request_Response(TRUE, "Files committed");
	}
}