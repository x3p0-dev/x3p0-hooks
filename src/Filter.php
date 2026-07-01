<?php

/**
 * Filter attribute.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-hooks
 */

declare(strict_types=1);

namespace X3P0\Hooks;

use Attribute;

/**
 * The filter attribute is for registering class constants, methods, or
 * properties as a filter on a WordPress hook using a PHP attribute.
 */
#[Attribute(
	Attribute::IS_REPEATABLE
	| Attribute::TARGET_CLASS_CONSTANT
	| Attribute::TARGET_METHOD
	| Attribute::TARGET_PROPERTY
)]
class Filter implements Hook
{
	/**
	 * Stores the hook callback priority.
	 */
	protected int $priority = 10;

	/**
	 * Sets up the object state.
	 *
	 * @throws InvalidHookName If the hook name is empty.
	 */
	public function __construct(
		protected string $hook,
		int|HookPriority $priority = HookPriority::Normal
	) {
		if (trim($hook) === '') {
			throw new InvalidHookName(
				'Hook name must be a non-empty string.'
			);
		}

		$this->priority = is_int($priority)
			? $priority
			: $priority->toInt();
	}

	/**
	 * Registers the filter hook.
	 */
	public function register(callable $callback, int $arguments = 1): void
	{
		add_filter($this->hook, $callback, $this->priority, $arguments);
	}
}
