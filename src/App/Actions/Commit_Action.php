<?php

namespace Git_Destroyer\Actions;

use Clyde\Actions\Action_Base;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Clyde\Tools\Input;
use Exception;

/**
 * @phpstan-import-type ConfigType from Config
 */
class Commit_Action extends Action_Base {
	use Action_Trait;

	public function execute(Request $Request): Request_Response {
		$config = $this->getConfig();
		$Git = $this->getGitInstance();

		$add_all = $Request->getArgument('add_all');
		$files = $Request->getArgument('files');

		if ($add_all) {
			$this->Printer->success("Adding all files...");
			$Git->addAllFiles();
		} else if (!empty($files)) {
			$this->Printer->success("Adding files...");
			$files = explode(',', $files);
			$Git->addFiles($files);
		}

		if (!$add_all && empty($files)) {
			$files = $Git->getModifiedFiles();
			$Input = new Input($this->Printer);
			$files[] = "Add All";
			$files_to_add = $Input->multipleChoice("Which files would you like to add?", $files);
			if (in_array("Add All", $files_to_add)) {
				$Git->addAllFiles();
			} else {
				$Git->addFiles($files_to_add);
			}
		}

		$this->checkForUncommittedChanges($Git);
		$this->checkForUntrackedFiles($Git);

		$pre_hooks = $config['hooks']['commit']['pre'];
		$post_hooks = $config['hooks']['commit']['post'];

		$this->runHook($pre_hooks, "Running pre commit hooks...");

		$this->Printer->success("Committing files...");
		$commit_message = $Request->getArgument('message');
		$Git->commit($commit_message);

		if ($Request->getArgument('local_only') === FALSE) {
			$this->Printer->success("Pushing files...");
			$Git->pushToRemote();
		}

		$this->runHook($post_hooks, "Running post commit hooks...");

		return new Request_Response(true, "Files committed");
	}

	protected function runHook(array $hooks, string $message): void {
		$this->Printer->success($message);
		foreach($hooks as $hook) {
			$cmd = $hook;
			$result = [];
			$result_code = 0;
			exec($cmd, $result, $result_code);

			if ($result_code !== 0) {
				throw new Exception("There was an error running hook: $hook");
			}
		}
	}
}