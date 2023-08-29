<?php

namespace Git_Destroyer\Actions;

use Git_Destroyer\Actions\Action_Extender;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Exception;

/**
 * @phpstan-import-type ConfigType from Config
 */
class Commit_Action extends Action_Extender
{

	public function execute(Request $Request): Request_Response {

		$add_all = $Request->getArgument('add_all');
		$files   = $Request->getArgument('files');

		if ($add_all) {
			$this->Printer->success("Adding all files...");
			$this->Git->addAllFiles();
		} else if (!empty($files)) {
			$this->Printer->success("Adding files...");
			$files = explode(',', $files);
			$this->Git->addFiles($files);
		}

		if (!$add_all && empty($files)) {
			$files        = $this->Git->getModifiedFiles();
			$files[]      = "Add All";
			$files_to_add = $this->Input->multipleChoice("Which files would you like to add?", $files);
			if (in_array("Add All", $files_to_add)) {
				$this->Git->addAllFiles();
			} else {
				$this->Git->addFiles($files_to_add);
			}
		}

		$this->checkForUncommittedChanges();
		$this->checkForUntrackedFiles();

		$pre_hooks  = $this->config['hooks']['commit']['pre'];
		$post_hooks = $this->config['hooks']['commit']['post'];

		$this->runHook($pre_hooks, "Running pre commit hooks...");

		$this->Printer->success("Committing files...");
		$commit_message = $Request->getArgument('message');
		$this->Git->commit($commit_message);

		if ($Request->getArgument('local_only') === FALSE) {
			$this->Printer->success("Pushing files...");
			$this->Git->pushToRemote();
		}

		$this->runHook($post_hooks, "Running post commit hooks...");

		return new Request_Response(TRUE, "Files committed");
	}

	protected function runHook(array $hooks, string $message): void {
		$this->Printer->success($message);
		foreach($hooks as $hook) {
			$cmd         = $hook;
			$result      = [];
			$result_code = 0;
			exec($cmd, $result, $result_code);

			if ($result_code !== 0) {
				throw new Exception("There was an error running hook: $hook");
			}
		}
	}
}