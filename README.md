# X3P0: Hooks

A lightweight, attribute-based hook system for WordPress plugins and themes. Built with PHP 8.1+, it lets you register WordPress actions and filters declaratively with `#[Action]` and `#[Filter]` attributes instead of manual `add_action()`/`add_filter()` plumbing.

[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net)

## Features

- **Attribute-Based Hooks**: Register WordPress actions and filters declaratively with `#[Action]` and `#[Filter]`
- **Hook Any Member**: Attach hooks to methods, properties, or class constants
- **Repeatable Attributes**: Register a single member on multiple hooks
- **Priority Shorthand**: Use integers or the `'first'` / `'last'` keywords
- **Extensible**: Define your own hook attributes by implementing the `Hook` interface
- **Lightweight**: Minimal overhead, no external dependencies
- **Type-Safe**: Full PHP 8.1+ type declarations for better IDE support

## Requirements

- PHP 8.1 or higher
- WordPress (recommended latest version)
- Composer

## Installation

Install via Composer:

```bash
composer require x3p0-dev/x3p0-hooks
```

**Important:** If you're releasing this as part of a theme or plugin bundle, please vendor prefix your installation to avoid conflicts with other plugins/themes.

## Quick Start

Add the `Hookable` trait to a class, mark members with `#[Action]` or
`#[Filter]`, and call `boot()`. The trait reflects the class and wires every
attributed member to WordPress:

```php
use X3P0\Hooks\Action;
use X3P0\Hooks\Filter;
use X3P0\Hooks\Hookable;

final class Assets
{
	use Hookable;

	#[Action('wp_enqueue_scripts')]
	public function enqueue(): void
	{
		wp_enqueue_style('theme', get_stylesheet_uri());
	}

	#[Filter('body_class')]
	public function bodyClass(array $classes): array
	{
		$classes[] = 'x3p0-theme';
		return $classes;
	}
}

// Wire up the hooks.
(new Assets())->boot();
```

The number of arguments passed to the callback is taken from the method's
parameter count, so `bodyClass()` above receives the `$classes` argument
automatically.

## Core Concepts

### The `Hookable` Trait

`Hookable` provides the `boot()` method that does the work. When called, it
reflects the class and registers every method, property, and class constant
marked with a hook attribute. Methods of any visibility are supported —
protected and private methods are bound and registered as closures, so they
work as hook callbacks without being public.

`boot()` throws a `ReflectionException` if the class cannot be reflected. If you
already have a bootable contract in your project, the trait's `boot()` satisfies
it, so a class can both `use Hookable` and implement your own `Bootable`
interface.

### The `#[Action]` and `#[Filter]` Attributes

`#[Action]` registers a member via `add_action()`, and `#[Filter]` registers it
via `add_filter()`. Both accept the hook name and an optional priority:

```php
#[Action('init')]
public function setup(): void
{
	// ...
}

#[Filter('the_content', priority: 20)]
public function content(string $content): string
{
	return $content;
}
```

The attributes are repeatable, so a single member can be attached to several
hooks. Priority accepts an integer or the shorthand `'first'` (`PHP_INT_MIN`) /
`'last'` (`PHP_INT_MAX`):

```php
#[Action('init')]
#[Action('wp_loaded', priority: 'first')]
public function bootstrap(): void
{
	// Runs on both `init` (priority 10) and `wp_loaded` (priority PHP_INT_MIN).
}
```

### Hooking Properties and Constants

Properties and class constants can be hooked too, in which case their value is
returned to the filter. This is a concise way to provide a static filter value:

```php
#[Filter('big_image_size_threshold', priority: 'last')]
protected const THRESHOLD_WIDTH = 3480;

#[Filter('excerpt_length')]
private int $excerptLength = 40;
```

### Custom Hook Attributes

Attributes are matched by the `Hook` interface using
`ReflectionAttribute::IS_INSTANCEOF`, so you can define your own hook attributes
by implementing `Hook` (or extending `Filter` — which is exactly how `Action` is
built), and the trait will pick them up automatically.

The `Hook` interface defines a single method:

```php
namespace X3P0\Hooks;

interface Hook
{
	public function register(callable $callback, int $arguments = 1): void;
}
```

## Best Practices

### Decide When to Boot

This package fires no WordPress hooks of its own — you decide when `boot()`
runs. Booting on an appropriate hook (such as `init` or after your services are
constructed) keeps registration predictable:

```php
add_action('after_setup_theme', function (): void {
	(new Assets())->boot();
});
```

### Keep Hookable Classes Focused

Group related hooks by responsibility (assets, admin, REST, etc.) rather than
collecting unrelated hooks in a single class. This keeps each class small and
its `boot()` cost minimal.

### Vendor Prefix When Necessary

If you're distributing your plugin/theme, consider using a tool like
[PHP-Scoper](https://github.com/humbug/php-scoper) to avoid conflicts.

## License

X3P0 Hooks is licensed under the [GPL-2.0-or-later](LICENSE.md) license.

## Credits

Created and maintained by [Justin Tadlock](https://github.com/justintadlock) under the [X3P0](https://github.com/x3p0-dev) umbrella.

## Support

- [GitHub Issues](https://github.com/x3p0-dev/x3p0-hooks/issues)
- [Packagist](https://packagist.org/packages/x3p0-dev/x3p0-hooks)
