<?php

/**
 * Hook exception interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-hooks
 */

declare(strict_types=1);

namespace X3P0\Hooks;

use Throwable;

/**
 * Marker interface implemented by every exception this package throws. Consumers
 * can catch this to handle any X3P0 Hooks failure as a single group.
 */
interface HookException extends Throwable
{
}
