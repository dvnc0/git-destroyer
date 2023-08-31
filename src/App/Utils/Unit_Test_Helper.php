<?php

namespace Git_Destroyer\Utils;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\Constraint;

// phpcs:ignoreFile
class Unit_Test_Helper extends TestCase
{

	/**
	 * replace deprecated onConsecutiveCalls
	 * 
	 * @param array $args array of args to use as returns
	 * @return array
	 * 
	 * @see https://gist.github.com/ziadoz/370fe63e24f31fd1eb989e7477b9a472
	 */
	public function withConsecutiveArgs(array ...$args): array {
		$callbacks = [];
		$count     = count(max($args));

		for ($index = 0; $index < $count; $index++) {
			$returns = [];

			foreach ($args as $arg) {
				if (! array_is_list($arg)) {
					throw new \InvalidArgumentException('Every array must be a list');
				}

				if (! isset($arg[$index])) {
					throw new \InvalidArgumentException(sprintf('Every array must contain %d parameters', $count));
				}

				$returns[] = $arg[$index];
			}

			$callbacks[] = $this->callback(new class ($returns) {
				public function __construct(protected array $returns) {
				}

				public function __invoke(mixed $actual): bool {
					if (count($this->returns) === 0) {
						return TRUE;
					}

					$next = array_shift($this->returns);
					if ($next instanceof Constraint) {
						$next->evaluate($actual);
						return TRUE;
					}

					return $actual === $next;
				}
			});
		}

		return $callbacks;
	}
}