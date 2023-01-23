<?php

namespace Gsdk\Form\Concerns;

use Gsdk\Form\Element\Basics\Input;
use Gsdk\Form\Element\Custom;
use Gsdk\Form\Element\ElementInterface;

trait HasExtensions {

	private static array $defaultNamespaces = [
		'Gsdk\Form\Element\Basics\\',
		'Gsdk\Form\Element\Custom\\',
	];

	private static array $defaultElements = [
		'datetime' => Custom\DateTime::class,
		'daterange' => Custom\DateRange::class,
	];

	private static array $extendedNamespaces = [];

	private static array $extendedElements = [];

	private static array $aliases = [
		'phone' => 'tel',
	];

	private static array $inputTypes = ['email', 'color', 'date', 'file', 'hidden', 'image', 'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'tel', 'text', 'time', 'url', 'week'];

	public static function registerNamespace(string $namespace): void {
		self::$extendedNamespaces[] = $namespace;
	}

	public static function extend(string $type, string $class): void {
		self::$extendedElements[$type] = $class;
	}

	public static function alias(string $name, string $alias): void {
		self::$aliases[$alias] = $name;
	}

	public static function hasExtension(string $alias): bool {
		return isset(self::$aliases[$alias])
			|| isset(self::$extendedElements[$alias])
			|| isset(self::$defaultElements[$alias])
			|| in_array($alias, self::$inputTypes)
			|| self::getElementClass($alias);
	}

	public static function elementFactory($name, $type, $options): ?ElementInterface {
		$class = self::getElementClass($type);
		if (!$class)
			throw new \Exception('Element type [' . $type . '] not defined');

		if ($class === Input::class)
			$options = array_merge($options, ['inputType' => $type]);

		return new $class($name, $options);
	}

	private static function getElementClass($type): ?string {
		if (isset(self::$aliases[$type]))
			$type = self::$aliases[$type];

		if (isset(self::$extendedElements[$type]))
			return new self::$extendedElements[$type];

		$class = self::getNSClass(self::$extendedNamespaces, $type)
			?? self::$defaultElements[$type]
			?? self::getNSClass(self::$defaultNamespaces, $type);

		if ($class)
			return $class;
		else if (in_array($type, self::$inputTypes))
			return Input::class;
		else
			return null;
	}

	private static function getNSClass($namespaces, $type): ?string {
		foreach ($namespaces as $ns) {
			$class = $ns . '\\' . ucfirst($type);
			if (!class_exists($class, true))
				continue;

			self::extend($type, $class);

			return $class;
		}

		return null;
	}

}