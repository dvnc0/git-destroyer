<?php

namespace Git_Destroyer\Utils;

use Exception;
use Git_Destroyer\Utils\Config;

/**
 * @phpstan-import-type ConfigType from Config
 */
class Git {

	/**
	 * @var Config
	 */
	protected Config $Config;

	public function __construct() {
		$this->Config = new Config();
	}
	/**
	 * check if repo has untracked filed
	 *
	 * @return boolean
	 */
	public function hasUntrackedFiles(): bool {
		$cmd = "git ls-files --others --exclude-standard";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error listing untracked files");
		}

		return !empty($result);
	}

	public function getUntrackedFiles(): array {
		$cmd = "git ls-files --others --exclude-standard";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		return $result;
	}

	public function hasUncommittedChanges(): bool {
		$cmd = "git diff --name-only";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error listing uncommitted changes");
		}

		return !empty($result);
	}

	/**
	 * creates a new branch
	 *
	 * @param non-empty-string $branch_name
	 * @return boolean
	 */
	public function createNewBranch(string $branch_name): bool {

		$config = $this->Config->loadConfigFromFile(ROOT . '/' . Config::CONFIG_NAME);

		if ($config['uses_prefix']) {
			if(strpos($branch_name, $config['branch_prefix']) === false) {
				$branch_name = $config['branch_prefix'] . '-' . $branch_name;
			}
		}

		$master_branch = $config['new_branch'];
		
		$cmd = "git fetch --all && git checkout $master_branch && git pull origin $master_branch  && git checkout -b $branch_name";

		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error creating the new branch");
		}

		return true;
	}

	public function addAllFiles(): bool {
		$cmd = "git add .";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error adding all files");
		}

		return true;
	}

	public function addFiles(array $files):bool {
		foreach ($files as $file) {
			$cmd = "git add $file";
			$result = [];
			$result_code = 0;
			exec($cmd, $result, $result_code);
	
			if ($result_code !== 0) {
				throw new Exception("There was an error adding file $file");
			}
		}
		return true;
	}

	public function commit(string $commit_message): bool {
		$cmd = "git commit -m \"$commit_message\"";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error committing");
		}

		return true;
	}

	public function pullFromRemote(): bool {
		$cmd = "git pull origin HEAD";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error pulling from remote");
		}

		return true;
	}

	public function pushToRemote(): bool {
		$this->pullFromRemote();
		$cmd = "git push origin HEAD";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error pushing to remote");
		}

		return true;
	}

	public function getModifiedFiles(): array {
		$cmd = "git diff --name-only";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error getting modified files");
		}

		// add untracked files to the result
		$untracked_files = $this->getUntrackedFiles();
		$result = array_merge($result, $untracked_files);

		return $result;
	}

	public function cloneRepo(string $repo_url, string $path = '.'): bool {
		$cmd = "git clone $repo_url $path";
		$result = [];
		$result_code = 0;
		exec($cmd, $result, $result_code);

		if ($result_code !== 0) {
			throw new Exception("There was an error cloning the repo");
		}

		return true;
	}
}