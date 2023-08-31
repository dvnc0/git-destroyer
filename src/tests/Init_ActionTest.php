<?php

use Git_Destroyer\Utils\Unit_Test_Helper;
use Clyde\Tools\Input;
use Clyde\Tools\Printer;
use Clyde\Request\Request;
use Clyde\Request\Request_Response;
use Git_Destroyer\Actions\Init_Action;
use Clyde\Application;
use Clyde\Core\Event_Dispatcher;
use PhpParser\PrettyPrinterAbstract;

/**
 * @covers Git_Destroyer\Actions\Init_Action
 */
class Init_ActionTest extends Unit_Test_Helper {

	public function setup(): void {
		if (!defined('ROOT')) {
			define('ROOT', '/foo/bar');
		}
	}

	public function tearDown():void {
		
	}

	protected function getMockApplication() {
		$Application = $this->getMockBuilder(Application::class)
			->disableOriginalConstructor()
			->getMock();

		$Application->Event_Dispatcher = new class ($Application) extends Event_Dispatcher{};

		return $Application;
	}

	public function testInitCreatesFullConfig(){
		$Mock_Application = $this->getMockApplication();
		$Mock_Input = $this->getMockBuilder(Input::class)
			->disableOriginalConstructor()
			->onlyMethods(['get', 'list'])
			->getMock();

		$Mock_Input->expects($this->exactly(7))
			->method('get')
			->with(
				...$this->withConsecutiveArgs(
					['What is your git username?'],
					['What is the email you use for git?'],
					['What is the repository clone URL? ex: ssh://user@github.com/user/foo'],
					['What is your staging branch'],
					['What is your production branch'],
					['What is your branch prefix?'],
					['What branch should new branches be made from?'],
				)
			)
			->will(
				$this->onConsecutiveCalls(
					'dvnc0',
					'email@email.com',
					'repo/clone/url',
					'staging',
					'live',
					'GD',
					'master'
				)
			);
		
		$Mock_Input->expects($this->exactly(3))
			->method('list')
			->with(
				...$this->withConsecutiveArgs(
					['Does your repository have a staging or testing branch?', ['Yes', 'No']],
					['Does your repository have a live or production branch?', ['Yes', 'No']],
					['Do you use branch prefixes? ex ABC-[TICKET NUMBER]', ['Yes', 'No']],
				)
			)
			->will(
				$this->onConsecutiveCalls(
					['Yes'],
					['Yes'],
					['Yes']
				)
			);

		$Mock_Printer = $this->getMockBuilder(Printer::class)
			->disableOriginalConstructor()
			->onlyMethods(['success'])
			->getMock();

		$Mock_Printer->expects($this->exactly(2))
			->method('success')
			->with(
				...$this->withConsecutiveArgs(
					['Creating config file...'],
					['Creating hooks file...'],
				)
			);

		$Mock_Application->Printer = $Mock_Printer;
		
		$Mock_Init_Action = $this->getMockBuilder(Init_Action::class)
			->disableOriginalConstructor()
			->onlyMethods(['filePutContents', 'checkForConfig', 'getInputInstance'])
			->getMock();

		$Mock_Init_Action->method('checkForConfig')
			->willReturnCallback(function(){return;});

		$Mock_Init_Action->Input = $Mock_Input;

		$config = json_encode(
			[
				'user_name' => 'dvnc0',
				'email' => 'email@email.com',
				'repo_url' => 'repo/clone/url',
				'has_staging' => true,
				'has_live' => true,
				'staging' => 'staging',
				'live' => 'live',
				'uses_prefix' => true,
				'branch_prefix' => 'GD',
				'new_branch' => 'master',
			],
			JSON_PRETTY_PRINT
		);

		$config_hooks = json_encode(
			[
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
			], 
			JSON_PRETTY_PRINT
		);

		$Mock_Init_Action->method('filePutContents')
			->with(
				...$this->withConsecutiveArgs(
					[ROOT . '/git-destroyer-config.json', $config],
					[ROOT . '/git-destroyer-hooks.json', $config_hooks],
				)
			)
			->willReturn(1);
		
		
		$Mock_Request = $this->getMockBuilder(Request::class)
			->disableOriginalConstructor()
			->getMock();


		$Mock_Init_Action->setPrinter($Mock_Printer);
		$result = $Mock_Init_Action->execute($Mock_Request);

		$this->assertTrue($result->success);
		$this->assertInstanceOf(Request_Response::class, $result);
	}

	public function testInitCreatesPartialConfig(){
		$Mock_Application = $this->getMockApplication();
		$Mock_Input = $this->getMockBuilder(Input::class)
			->disableOriginalConstructor()
			->onlyMethods(['get', 'list'])
			->getMock();

		$Mock_Input->expects($this->exactly(4))
			->method('get')
			->with(
				...$this->withConsecutiveArgs(
					['What is your git username?'],
					['What is the email you use for git?'],
					['What is the repository clone URL? ex: ssh://user@github.com/user/foo'],
					['What branch should new branches be made from?'],
				)
			)
			->will(
				$this->onConsecutiveCalls(
					'dvnc0',
					'email@email.com',
					'repo/clone/url',
					'master',
				)
			);
		
		$Mock_Input->expects($this->exactly(3))
			->method('list')
			->with(
				...$this->withConsecutiveArgs(
					['Does your repository have a staging or testing branch?', ['Yes', 'No']],
					['Does your repository have a live or production branch?', ['Yes', 'No']],
					['Do you use branch prefixes? ex ABC-[TICKET NUMBER]', ['Yes', 'No']],
				)
			)
			->will(
				$this->onConsecutiveCalls(
					['No'],
					['No'],
					['No']
				)
			);

		$Mock_Printer = $this->getMockBuilder(Printer::class)
			->disableOriginalConstructor()
			->onlyMethods(['success'])
			->getMock();

		$Mock_Printer->expects($this->exactly(2))
			->method('success')
			->with(
				...$this->withConsecutiveArgs(
					['Creating config file...'],
					['Creating hooks file...'],
				)
			);

		$Mock_Application->Printer = $Mock_Printer;
		
		$Mock_Init_Action = $this->getMockBuilder(Init_Action::class)
			->disableOriginalConstructor()
			->onlyMethods(['filePutContents', 'checkForConfig', 'getInputInstance'])
			->getMock();

		$Mock_Init_Action->method('checkForConfig')
			->willReturnCallback(function(){return;});

		$Mock_Init_Action->Input = $Mock_Input;

		$config = json_encode(
			[
				'user_name' => 'dvnc0',
				'email' => 'email@email.com',
				'repo_url' => 'repo/clone/url',
				'has_staging' => false,
				'has_live' => false,
				'staging' => '',
				'live' => '',
				'uses_prefix' => false,
				'branch_prefix' => '',
				'new_branch' => 'master',
			],
			JSON_PRETTY_PRINT
		);

		$config_hooks = json_encode(
			[
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
			], 
			JSON_PRETTY_PRINT
		);

		$Mock_Init_Action->method('filePutContents')
			->with(
				...$this->withConsecutiveArgs(
					[ROOT . '/git-destroyer-config.json', $config],
					[ROOT . '/git-destroyer-hooks.json', $config_hooks],
				)
			)
			->willReturn(1);
		
		
		$Mock_Request = $this->getMockBuilder(Request::class)
			->disableOriginalConstructor()
			->getMock();


		$Mock_Init_Action->setPrinter($Mock_Printer);
		$result = $Mock_Init_Action->execute($Mock_Request);

		$this->assertTrue($result->success);
		$this->assertInstanceOf(Request_Response::class, $result);
	}
}