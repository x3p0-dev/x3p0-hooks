<?php

/**
 * Hookable trait.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-hooks
 */

declare(strict_types=1);

namespace X3P0\Hooks;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * A trait for defining attribute-based actions and filters with class members.
 * A class can `use Hookable` and call `registerHookCallbacks()` from its own
 * lifecycle method (e.g., `boot()`) to have its `#[Action]`/`#[Filter]` members
 * wired to WordPress automatically. The trait is unopinionated about lifecycle:
 * it exposes no public method, so the consuming class owns when registration
 * runs and under what name.
 */
trait Hookable
{
	/**
	 * Stores the instance of the reflected class.
	 */
	protected ReflectionClass $reflector;

	/**
	 * Registers the object's actions and filters by reflecting its members
	 * and wiring every `#[Action]`/`#[Filter]` attribute to WordPress.
	 *
	 * @throws ReflectionException
	 */
	protected function registerHookCallbacks(): void
	{
		$this->registerMethodCallbacks();
		$this->registerPropertyCallbacks();
		$this->registerConstantCallbacks();
	}

	/**
	 * Returns the reflection of the current class.
	 */
	protected function getReflector(): ReflectionClass
	{
		if (! isset($this->reflector)) {
			$this->reflector = new ReflectionClass($this);
		}

		return $this->reflector;
	}

	/**
	 * Registers callbacks for methods with attributes that have the `Hook`
	 * contract as actions or filters.
	 *
	 * @throws ReflectionException
	 */
	protected function registerMethodCallbacks(): void
	{
		// Grab methods of any visibility, excluding the constructor.
		$methods = array_filter(
			$this->getReflector()->getMethods(
				ReflectionMethod::IS_PUBLIC
				| ReflectionMethod::IS_PROTECTED
				| ReflectionMethod::IS_PRIVATE
			),
			fn($method) => ! $method->isConstructor()
		);

		foreach ($methods as $method) {
			$attributes = $method->getAttributes(
				Hook::class,
				ReflectionAttribute::IS_INSTANCEOF
			);

			foreach ($attributes as $attribute) {
				// Register a bound closure rather so that
				// protected and private methods can be used as
				// hook callbacks.
				$attribute->newInstance()->register(
					$method->getClosure($this),
					$method->getNumberOfParameters()
				);
			}
		}
	}

	/**
	 * Registers callbacks for properties with attributes that have the
	 * `Hook` contract as actions or filters.
	 */
	protected function registerPropertyCallbacks(): void
	{
		foreach ($this->getReflector()->getProperties() as $property) {
			$attributes = $property->getAttributes(
				Hook::class,
				ReflectionAttribute::IS_INSTANCEOF
			);

			foreach ($attributes as $attribute) {
				$attribute->newInstance()->register(
					fn() => $property->getValue($this)
				);
			}
		}
	}

	/**
	 * Registers callbacks for constants with attributes that have the
	 * `Hook` contract as actions or filters.
	 */
	protected function registerConstantCallbacks(): void
	{
		foreach ($this->getReflector()->getReflectionConstants() as $constant) {
			$attributes = $constant->getAttributes(
				Hook::class,
				ReflectionAttribute::IS_INSTANCEOF
			);

			foreach ($attributes as $attribute) {
				$attribute->newInstance()->register(
					fn() => $constant->getValue()
				);
			}
		}
	}
}
