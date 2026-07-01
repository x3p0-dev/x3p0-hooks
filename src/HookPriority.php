<?php

/**
 * Hook priority enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-hooks
 */

declare(strict_types=1);

namespace X3P0\Hooks;

/**
 * Named presets for the common hook priorities, usable anywhere an integer
 * priority is accepted. Callbacks run lowest number first, so the cases name the
 * *order* rather than a magnitude: `First` runs before every other callback,
 * `Last` after every other callback, and `Normal` is WordPress's default `10`.
 * Because `First` and `Last` are the integer extremes they act as true bookends;
 * pass a plain integer for any ordering in between.
 *
 * The backing integers are an implementation detail of ordering, so the enum is
 * intentionally not int-backed: `toInt()` maps each case to its value, keeping
 * the platform-dependent extremes (`PHP_INT_MIN` / `PHP_INT_MAX`) out of the
 * public surface.
 */
enum HookPriority
{
	case First;
	case Normal;
	case Last;

	/**
	 * Returns the integer priority this case represents.
	 */
	public function toInt(): int
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext -- valid $this in an enum method; the sniff predates enums.
		return match ($this) {
			self::First  => PHP_INT_MIN,
			self::Normal => 10,
			self::Last   => PHP_INT_MAX
		};
	}
}
