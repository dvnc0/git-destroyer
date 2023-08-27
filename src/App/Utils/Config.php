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
 */
class Config {

	const CONFIG_NAME = "git-destroyer-config.json";
	/**
	 * load config from file
	 *
	 * @param non-empty-string $file_path
	 * @return ConfigType
	 */
	public function loadConfigFromFile(string $file_path): array {
		if (empty($file_path)) {
			throw new Exception("File path cannot be empty");
		}

		if (!file_exists($file_path)) {
			throw new Exception("Config file does not exist, please run git-destroyer init");
		}

		$config = json_decode(file_get_contents($file_path), true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception("There was an error parsing the config file");
		}

		return $config;
	}
}