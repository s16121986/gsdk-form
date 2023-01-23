<?php

namespace Gsdk\Form;

class Fieldset {

	protected $values = [];

	protected $parent = null;

	public function __set($name, $value) {
		$this->setOption($name, $value);
	}

	public function __get($name) {
		switch ($name) {
			case 'name':
				return $this->getName();
			case 'id':
				return $this->getId();
			case 'data':
				return $this->getData();
			case 'errors':
				return $this->getErrors();
		}

		if (isset($this->options[$name]))
			return $this->options[$name];

		return $this->getElement($name);
	}

	public function __construct($options = null) {
		if (is_array($options))
			$this->setOptions($options);
	}

	public function setOption($key, $option): static {
		switch ($key) {
			case 'elements':
				foreach ($option as $k => $el) {
					if (is_array($el)) {
						$type = (isset($el['type']) ? $el['type'] : $this->defaultType);
						$this->addElement($k, $type, $el);
					}
				}
				break;
			default:
				$this->options[$key] = $option;
		}

		return $this;
	}

	public function getName() {
		return $this->options['name'];
	}

	public function setParent($parent) {
		$this->parent = $parent;
		return $this;
	}

	public function getForm() {
		if ($this->parent)
			return ($this->parent instanceof Form ? $this->parent : $this->parent->getForm());

		return null;
	}

	public function getValue($key) {
		if (isset($this->values[$key]))
			return $this->values[$key];

		return (isset($this->elements[$key]) ? $this->elements[$key]->getValue() : null);
	}

	public function setValue($key, $value = null) {
		if (isset($this->elements[$key]))
			return $this->elements[$key]->setValue($value);

		$this->values[$key] = $value;
		return true;
	}

	public function getData() {
		$data = [];

		foreach ($this->elements as $element) {
			if ($element->disabled || !$element->readable)
				continue;

			switch ($element->type) {
				case 'label':
					break;
				case 'password':
				case 'file':
				case 'image':
					if (!$element->isEmpty())
						$data[$element->name] = $element->getValue();
					break;
				default:
					$data[$element->name] = $element->getValue();
			}
		}

		foreach ($this->values as $k => $v) {
			$data[$k] = $v;
		}

		return $data;
	}

	public function getFilledData(): array {
		$data = [];

		foreach ($this->elements as $element) {
			if ($element->disabled || !$element->readable || $element->isEmpty())
				continue;

			switch ($element->type) {
				case 'label':
					break;
				default:
					$data[$element->name] = $element->getValue();
			}
		}

		foreach ($this->values as $k => $v) {
			$data[$k] = $v;
		}

		return $data;
	}

	public function setData($data) {
		foreach ($this->elements as $element) {
			if (isset($data[$element->name])) {
				$element->setValue($data[$element->name]);
			}
		}
		return $this;
	}

	public function hasUpload(): bool {
		foreach ($this->getElements() as $element) {
			if ($element->isFileUpload()) {
				return true;
			}
		}
		return false;
	}

	public function isValid(): bool {
		foreach ($this->elements as $element) {
			if (!$element->isValid())
				return false;
		}
		return true;
	}

	public function isSubmitted(): bool {
		if (($form = $this->getForm()))
			return $form->isSubmitted();

		return false;
	}


}