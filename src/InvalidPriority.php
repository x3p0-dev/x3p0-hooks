<?php

/**
 * Invalid priority exception.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-hooks
 */

declare(strict_types=1);

namespace X3P0\Hooks;

use InvalidArgumentException;

/**
 * Thrown when a hook attribute is given a priority that is not an integer, a
 * numeric string, or the `'first'` / `'last'` shorthand.
 */
class InvalidPriority extends InvalidArgumentException implements HookException
{
}
