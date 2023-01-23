<?php

namespace Gsdk\Form\Element\Util;

class InputAttributes {

	protected $element;

	protected $attributes = [];

	public function __construct($element, $allowed = null) {
		$this->element = $element;
		if ($allowed)
			$this->attributes = $allowed;
	}

	public function allow($name) {
		$this->attributes[] = $name;
	}

	public function getHtml(): string {
		$html = '';
		foreach ($this->attributes as $k) {
			$html .= self::getAttributeHtml($this->element, $k);
		}
		return $html;
	}

	public function withoutName(): string {
		$element = $this->element;

		$attr = [];

		foreach (array_merge(['id', 'class'], $this->attributes) as $k) {
			$value = self::getAttributeValue($element, $k);
			if (null === $value)
				continue;

			$attr[] = $k . '="' . $value . '"';
		}

		if ($element->disabled)
			$attr[] = 'disabled="disabled"';

		if ($element->attr)
			$attr = $element->attr;

		return ' ' . implode(' ', $attr);
	}

	public function __toString() {
		return ' name="' . $this->element->getInputName() . '"'
			. $this->withoutName();
	}

	private static function getAttributeValue($element, $name) {
		$value = $element->$name;

		if (null === $value)
			return null;

		switch ($name) {
			case 'autofocus':
			case 'checked':
			case 'disabled':
			case 'readonly':
			case 'multiple':
			case 'required':
				return $value ? $name : null;
			case 'minlength':
			case 'maxlength':
			case 'size':
			case 'step':
			case 'tabindex':
				return (int)$value;
			case 'autocomplete':
				if (is_string($value))
					return $value;
				else
					return $value ? 'on' : 'off';
			case 'inputmode':
				static $values = ['none', 'text', 'tel', 'url', 'email', 'numeric', 'decimal', 'search'];
				return in_array($value, $values) ? $value : null;
			case 'list':
				if (is_string($value))
					return $value;
				else if (is_array($value)) {
					return self::getDatalistId($element);
				}
			default:
				return $value;
		}
	}

	private static function getAttributeHtml($element, $name): ?string {
		$value = $element->$name;

		if (null === $value)
			return null;

		switch ($name) {
			case 'list':
				if (!is_array($value))
					return null;

				$html = '<datalist id="' . self::getDatalistId($element) . '">';
				foreach ($value as $s) {
					$html .= '<option value="' . $s . '">';
				}
				$html .= '</datalist>';

				return $html;
			default:
				return null;
		}
	}

	private static function getDatalistId($element): string {
		return $element->getElementId() . '_datalist';
	}

}