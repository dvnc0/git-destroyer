<?php
namespace Git_Destroyer\Actions;

use Clyde\Actions\Action_Base;
use Clyde\Request\Request_Response;
use Clyde\Request\Request;
use Clyde\Tools\Input;
use Clyde\Tools\Printer;

class Init_Action extends Action_Base {
	/**
	 * creates the config file
	 *
	 * @param Request $Request  Request
	 * @return Request_Response
	 */
	public function execute(Request $Request): Request_Response {
		// execute init
		$Printer = new Printer;
		$Input = new Input($Printer);

		$user_name = $Input->get('What is your git username?');
		$email = $Input->get('What is the email you use for git?');
		$repo = $Input->get('What is the repository clone URL? ex: ssh://user@github.com/user/foo');
		$has_staging = $Input->list('Does your repository have a staging or testing branch?', ['Yes', 'No']);
		$staging_branch = $has_staging[0] === 'Yes' ? $Input->get('What is your staging branch') : '';
		$has_live = $Input->list('Does your repository have a live or production branch?', ['Yes', 'No']);
		$live_branch = $has_live[0] === 'Yes' ? $Input->get('What is your production branch') : '';
		$has_prefix = $Input->list("Do you use branch prefixes? ex ABC-[TICKET NUMBER]", ['Yes', 'No']);
		$branch_prefix = $has_prefix[0] === 'Yes' ? $Input->get('What is your branch prefix?') : '';
		$new_branch = $Input->get("What branch should new branches be made from?");

		$config_file = [
			'user_name' => $user_name,
			'email' => $email,
			'repo_url' => $repo,
			'has_staging' => $has_staging[0] === 'Yes',
			'has_live' => $has_live[0] === 'Yes',
			'staging' => $staging_branch,
			'live' => $live_branch,
			'uses_prefix' => $has_prefix[0] === 'Yes',
			'branch_prefix' => $branch_prefix,
			'new_branch' => $new_branch,
			'hooks' => [
				'new_branch' => [
					'pre' => [],
					'post' => [],
				],
				'commit' => [
					'pre' => [],
					'post' => [],
				],
				'staging' => [
					'pre' => [],
					'post' => [],
				],
				'live' => [
					'pre' => [],
					'post' => [],
				],
				'scripts' => [
					'example' => 'echo "hello world"',
				],
			]
		];

		$Printer->success("Creating config file...");
		$file_name = ROOT . '/git-destroyer-config.json';
		file_put_contents($file_name, json_encode($config_file, JSON_PRETTY_PRINT));
		
		return new Request_Response(true);
	}
}