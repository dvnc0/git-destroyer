<?php

namespace Git_Destroyer\Utils;

use Exception;
use Git_Destroyer\Utils\Config;

/**
 * @phpstan-import-type ConfigType from Config
 */
class Git
{

	/**
	 * @var Config
	 */
	protected Config $Config;

	/**
	 * construct
	 */
	public function __construct() {
		$this->Config = new Config();
	}

	/**
	 * check if repo has untracked files
	 *
	 * @return boolean
	 */
	public function hasUntrackedFiles(): bool {
		$cmd                    = "git ls-files --others --exclude-standard";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error listing untracked files");
		}

		return !empty($result);
	}

	/**
	 * get untracked files
	 *
	 * @return array<string>
	 */
	public function getUntrackedFiles(): array {
		$cmd                    = "git ls-files --others --exclude-standard";
		[$result, $result_code] = $this->execWrapper($cmd);

		return $result;
	}

	/**
	 * check if repo has uncommitted changes
	 *
	 * @return boolean
	 */
	public function hasUncommittedChanges(): bool {
		$cmd                    = "git diff --name-only";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error listing uncommitted changes");
		}

		return !empty($result);
	}

	/**
	 * creates a new branch
	 *
	 * @param non-empty-string $branch_name the branch name to create
	 * @return boolean
	 */
	public function createNewBranch(string $branch_name): bool {

		$config = $this->Config->loadConfigFromFile(ROOT . '/' . Config::CONFIG_NAME);

		if ($config['uses_prefix']) {
			if(strpos($branch_name, $config['branch_prefix']) === FALSE) {
				$branch_name = $config['branch_prefix'] . '-' . $branch_name;
			}
		}

		$master_branch = $config['new_branch'];
		
		$cmd = "git fetch --all && git checkout $master_branch && git pull origin $master_branch  && git checkout -b $branch_name";

		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error creating the new branch");
		}

		return TRUE;
	}

	/**
	 * add all files to the commit
	 * 
	 * @return boolean
	 */
	public function addAllFiles(): bool {
		$cmd                    = "git add .";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error adding all files");
		}

		return TRUE;
	}

	/**
	 * Files to add to the commit
	 * 
	 * @param array<string> $files files to add
	 * @return boolean
	 */
	public function addFiles(array $files):bool {
		foreach ($files as $file) {
			$cmd         = "git add $file";
			$result      = [];
			$result_code = 0;
			exec($cmd, $result, $result_code);
	
			if ($result_code !== 0) {
				throw new Exception("There was an error adding file $file");
			}
		}
		return TRUE;
	}

	/**
	 * Commit with message
	 * 
	 * @param non-empty-string $commit_message message for commit
	 * @return boolean
	 */
	public function commit(string $commit_message): bool {
		$cmd                    = "git commit -m \"$commit_message\"";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error committing");
		}

		return TRUE;
	}

	/**
	 * pull from remote
	 * 
	 * @return boolean
	 */	
	public function pullFromRemote(): bool {
		$cmd                    = "git pull origin HEAD";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error pulling from remote");
		}

		return TRUE;
	}

	/**
	 * push to remote
	 * 
	 * @return boolean
	 */
	public function pushToRemote(): bool {
		$this->pullFromRemote();
		$cmd                    = "git push origin HEAD";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error pushing to remote");
		}

		return TRUE;
	}

	/**
	 * get modified files
	 * 
	 * @return array<string>
	 */
	public function getModifiedFiles(): array {
		$cmd                    = "git diff --name-only";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error getting modified files");
		}

		$untracked_files = $this->getUntrackedFiles();
		$result          = array_merge($result, $untracked_files);

		return $result;
	}

	/**
	 * clone a repo
	 * 
	 * @param non-empty-string $repo_url repo url
	 * @param non-empty-string $path     file path
	 * 
	 * @return bool
	 */
	public function cloneRepo(string $repo_url, string $path = '.'): bool {
		$cmd                    = "git clone $repo_url $path";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0) {
			throw new Exception("There was an error cloning the repo");
		}

		return TRUE;
	}

	/**
	 * get current branch
	 * 
	 * @return non-empty-string
	 */
	public function getCurrentBranch(): string {
		$cmd                    = "git rev-parse --abbrev-ref HEAD";
		[$result, $result_code] = $this->execWrapper($cmd);

		if ($result_code !== 0 || empty($result)) {
			throw new Exception("There was an error getting the current branch");
		}

		return $result[0];
	}

	/**
	 * checkout a branch
	 *
	 * @param non-empty-string $branch_name the branch name to checkout
	 * @return boolean
	 */
	public function checkoutBranch(string $branch_name): bool {
		$cmd                    = "git checkout $branch_name";
		[$result, $result_code] = $this->execWrapper($cmd);
		
		if ($result_code !== 0) {
			throw new Exception("There was an error checking out branch $branch_name");
		}

		return TRUE;
	}

	/**
	 * merge a branch
	 *
	 * @param non-empty-string $branch_name branch name to merge
	 * @return boolean
	 */
	public function mergeBranch(string $branch_name): bool {
		$cmd                    = "git merge --no-edit -m \"Merging changes from $branch_name\" $branch_name";
		[$result, $result_code] = $this->execWrapper($cmd);
		
		if ($result_code !== 0) {
			throw new Exception("There was an error merging branch $branch_name");
		}

		return TRUE;
	}

	/**
	 * Testing wrapper for exec
	 * 
	 * @param non-empty-string $cmd command to run
	 * @return array{0:array, 1:int}
	 */
	protected function execWrapper($cmd): array {
		$result      = [];
		$result_code = 0;
		
		exec($cmd, $result, $result_code);

		return [$result, $result_code];
	}
}