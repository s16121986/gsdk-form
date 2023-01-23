<?php

namespace Gsdk\Form;

class Label {

	protected $options = [
		'requiredLabel' => ''
	];

	protected $element;

	public function __construct($options = []) {
		$this->setOptions($options);
	}

	public function __set($name, $value) {
		$this->setOption($name, $value);
	}

	public function __get($name) {
		if (isset($this->options[$name]))
			return $this->options[$name];
		//if (isset(self::$_default[$name])) return self::$_default[$name];
		return null;
	}

	public function setFor($for) {
		$this->options['for'] = $for;
		return $this;
	}

	public function setOptions($options) {
		foreach ($options as $k => $v) {
			$this->setOption($k, $v);
		}
		return $this;
	}

	public function setOption($key, $option) {
		$this->options[$key] = $option;
		return $this;
	}

	public function setElement($element) {
		$this->element = $element;
		$this->setFor($element->id);
		return $this;
	}

	protected function attrAsString() {
		$attr = [];
		$class = ['form-element-label'];
		if ($this->element)
			$class[] = $this->element->class;

		if ($this->element && $this->element->getForm() && $this->element->getForm()->isSubmitted() && !$this->element->isValid())
			$class[] = 'invalid-field-label';

		$attr[] = 'class="' . implode(' ', $class) . '"';
		return ' ' . implode(' ', $attr);
	}

	public function render() {
		return '<label for="' . $this->for . '"' . $this->attrAsString() . '>'
			. $this->text
			. ($this->requiredLabel && $this->element && $this->element->required ? ' <span class="required-label">' . $this->requiredLabel . '</span>' : '')
			. '</label>';
	}

}