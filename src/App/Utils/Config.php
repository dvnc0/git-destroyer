<?php

namespace Git_Destroyer\Utils;

use Exception;

/**
 * @phpstan-type ConfigType array{
 *   user_name:string, 
 *   email: string,
 *   repo_url: string,
 *   has_staging: bool,
 *   has_live: bool,
 *   staging: string,
 *   live: string,
 *   uses_prefix: bool,
 *   branch_prefix: string,
 *   new_branch: string,
 *   hooks: array{
 *     new_branch: array{ pre: array<string>, post: array<string> },
 * 	   commit: array{ pre: array<string>, post: array<string> },
 * 	   staging: array{ pre: array<string>, post: array<string> },
 * 	   live: array{ pre: array<string>, post: array<string> },
 *     scripts: array<string, string>
 *   }
 * }
 * 
 * @phpstan-type HookArray array{
 *     new_branch: array{ pre: array<string>, post: array<string> },
 * 	   commit: array{ pre: array<string>, post: array<string> },
 * 	   staging: array{ pre: array<string>, post: array<string> },
 * 	   live: array{ pre: array<string>, post: array<string> },
 *     scripts: array<string, string>
 *   }
 */
class Config
{

	const CONFIG_NAME       = "git-destroyer-config.json";
	const HOOKS_CONFIG_NAME = "git-destroyer-hooks.json";
	/**
	 * load config from file
	 *
	 * @param non-empty-string $file_path the file path
	 * @return ConfigType|false
	 */
	public function loadConfigFromFile(string $file_path): array|bool {
		if (empty($file_path)) {
			throw new Exception("File path cannot be empty");
		}

		if (!file_exists($file_path)) {
			return FALSE;
		}

		$config = json_decode($this->getFileContents($file_path), TRUE);

		$config['hooks'] = $this->getHooksFile();

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception("There was an error parsing the config file");
		}

		return $config;
	}

	/**
	 * Gets the hook config file
	 *
	 * @return array|HookArray
	 */
	protected function getHooksFile(): array {
		$hooks_file = ROOT . '/' . self::HOOKS_CONFIG_NAME;
		if (!file_exists($hooks_file)) {
			return [];
		}

		$hooks = json_decode($this->getFileContents($hooks_file), TRUE);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception("There was an error parsing the hooks file");
		}

		return $hooks;
	}

	/**
	 * Wrapper test helper
	 *
	 * @param string $path path to file
	 * @return string|false
	 * 
	 * @codeCoverageIgnore
	 */
	protected function getFileContents(string $path): string|false {
		return file_get_contents($path);
	}
}