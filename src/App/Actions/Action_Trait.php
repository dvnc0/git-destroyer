<?php

namespace Git_Destroyer\Actions;

use Git_Destroyer\Utils\Git;
use Git_Destroyer\Utils\Config;
use Clyde\Tools\Emoji;
use Clyde\Tools\Input;
use Clyde\Application;
use Clyde\Core\Event_Dispatcher;

/**
 * @phpstan-import-type ConfigType from Config
 */
trait Action_Trait {
	/**
	 * @var ConfigType
	 */
	protected array $Config;
	
	public function __construct(Application $Application, Event_Dispatcher $Event_Dispatcher) {
		parent::__construct($Application, $Event_Dispatcher);
		$Config = new Config();
		$this->Config = $Config->loadConfigFromFile(ROOT . '/' . Config::CONFIG_NAME);
	}
	/**
	 * Return an instance of Git
	 *
	 * @return Git
	 */
	protected function getGitInstance(): Git {
		return new Git;
	}

	/**
	 * Return the config
	 * 
	 * @phpstan-return ConfigType
	 * @return ConfigType
	 */
	protected function getConfig(): array {
		return $this->Config;
	}

	/**
	 * Check for uncommitted changes
	 *
	 * @param Git $Git Git class
	 * @return void
	 */
	protected function checkForUncommittedChanges(Git $Git):void {
		if ($Git->hasUncommittedChanges()) {
			$this->Printer->alert(Emoji::WARNING . " You have uncommitted changes!");
			$Input = new Input($this->Printer);
			$continue = $Input->list("Would you like to continue?", ['Yes', 'No']);

			if ($continue[0] === 'No') {
				exit(0);
			}
		}
	}

	/**
	 * Check for untracked files
	 *
	 * @param Git $Git Git class
	 * @return void
	 */
	protected function checkForUntrackedFiles(Git $Git):void {
		if ($Git->hasUntrackedFiles()) {
			$this->Printer->alert(Emoji::WARNING . " You have untracked files:");
			$Input = new Input($this->Printer);
			$untracked = $Git->getUntrackedFiles();
			foreach($untracked as $file) {
				$this->Printer->message("- " . $file);
			}
			$continue = $Input->list("Would you like to continue?", ['Yes', 'No']);

			if ($continue[0] === 'No') {
				$this->Printer->message("Aborting new branch creation!");
				exit(0);
			}
		}
	}
}