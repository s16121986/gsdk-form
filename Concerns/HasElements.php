<?php

namespace Gsdk\Form\Concerns;

use Gsdk\Form\Element\ElementInterface;
use Gsdk\Form\Form;

trait HasElements {

	protected array $elements = [];

	public function hasElement($name): bool {
		return isset($this->elements[$name]);
	}

	public function addElement(ElementInterface|string $element, string $type = null, array $options = []): static {
		if (is_string($element))
			$element = Form::elementFactory($element, $type, $options);

		$element->setParent($this);
		$this->elements[$element->name] = $element;

		return $this;
	}

	public function getElement(string $name) {
		return ($this->elements[$name] ?? null);
	}

	public function getElements(): array {
		return $this->elements;
	}

	public function removeElement(string $name): static {
		unset($this->elements[$name]);

		return $this;
	}

}