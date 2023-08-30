<?php

namespace Git_Destroyer\Actions;

use Clyde\Actions\Action_Base;
use Git_Destroyer\Utils\Git;
use Git_Destroyer\Utils\Config;
use Clyde\Tools\Emoji;
use Clyde\Tools\Input;
use Clyde\Application;
use Clyde\Core\Event_Dispatcher;
use Clyde\Tasks\Task_Runner;
use Exception;

/**
 * @phpstan-import-type ConfigType from \Git_Destroyer\Utils\Config
 */
abstract class Action_Extender extends Action_Base
{
	/**
	 * @var ConfigType|false
	 */
	protected array|bool $config;

	/**
	 * @var Input
	 */
	protected Input $Input;

	/**
	 * @var Git
	 */
	protected Git $Git;
	
	/**
	 * construct
	 *
	 * @param Application      $Application      Application
	 * @param Event_Dispatcher $Event_Dispatcher Event Dispatcher
	 */
	public function __construct(Application $Application, Event_Dispatcher $Event_Dispatcher) {
		parent::__construct($Application, $Event_Dispatcher);
		$Config       = $this->getConfigInstance();
		$this->config = $Config->loadConfigFromFile(ROOT . '/' . Config::CONFIG_NAME);
		$this->Git    = $this->getGitInstance();
		$this->Input  = $this->getInputInstance();

		$this->checkForConfig();
	}

	/**
	 * Check for config file
	 *
	 * @return void
	 */
	public function checkForConfig(): void {
		if ($this->config === FALSE) {
			$this->Printer->alert(Emoji::WARNING . " No config file found!");
			[$create_init] = $this->Input->list("Would you like to create one?", ['Yes', 'No']) ?: ['No'];

			if ($create_init === 'Yes') {
				$this->Event_Dispatcher->dispatch('run:init', []);
			} else {
				throw new Exception("No config file found!");
			}
		}
	}

	/**
	 * Return an instance of Config
	 *
	 * @return Config
	 * @codeCoverageIgnore
	 */
	protected function getConfigInstance(): Config {
		return new Config;
	}

	/**
	 * Return an instance of Input
	 *
	 * @return Input
	 * @codeCoverageIgnore
	 */
	protected function getInputInstance(): Input {
		return new Input($this->Printer);
	}

	/**
	 * Return an instance of Git
	 *
	 * @return Git
	 * @codeCoverageIgnore
	 */
	protected function getGitInstance(): Git {
		return new Git;
	}

	/**
	 * Return the config
	 * 
	 * @return ConfigType|false
	 * @codeCoverageIgnore
	 */
	protected function getConfig(): array|bool {
		return $this->config;
	}

	/**
	 * Return the Task Runner
	 *
	 * @return Task_Runner
	 * @codeCoverageIgnore
	 */
	protected function getTaskRunner(): Task_Runner {
		return new Task_Runner($this->Application);
	}

	/**
	 * Check for uncommitted changes
	 *
	 * @return void
	 */
	protected function checkForUncommittedChanges():void {
		if ($this->Git->hasUncommittedChanges()) {
			$this->Printer->alert(Emoji::WARNING . " You have uncommitted changes!");
			$continue = $this->Input->list("Would you like to continue?", ['Yes', 'No']);

			if ($continue[0] === 'No') {
				$this->exitNow(0);
			}
		}
	}

	/**
	 * Check for untracked files
	 *
	 * @return void
	 */
	protected function checkForUntrackedFiles():void {
		if ($this->Git->hasUntrackedFiles()) {
			$this->Printer->alert(Emoji::WARNING . " You have untracked files:");
			$untracked = $this->Git->getUntrackedFiles();
			foreach($untracked as $file) {
				$this->Printer->message("- " . $file);
			}
			$continue = $this->Input->list("Would you like to continue?", ['Yes', 'No']);

			if ($continue[0] === 'No') {
				$this->Printer->message("Aborting new branch creation!");
				$this->exitNow(0);
			}
		}
	}

	/**
	 * @param non-empty-string $cmd command to run
	 * @return array{0:array, 1:int}
	 * @codeCoverageIgnore
	 */
	protected function execWrapper($cmd): array {
		$result      = [];
		$result_code = 0;
		
		exec($cmd, $result, $result_code);

		return [$result, $result_code];
	}

	/**
	 * Exit now
	 *
	 * @param int<0,1> $code exit code
	 * @return void
	 * @codeCoverageIgnore
	 */
	protected function exitNow(int $code) {
		exit($code);
	}
}