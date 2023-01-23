<?php

namespace Gsdk\Form\Element\Basics;

class Checkbox extends Input {

	protected array $options = [
		'inputType' => 'checkbox',
		'checkedValue' => 1,
		'uncheckedValue' => 0
	];
	protected $attributes = [];//'indeterminate'

	public function __construct(string $name, array $options = []) {
		if (isset($options['checked']))
			$this->setChecked((bool)$options['checked']);
		else {
			$curValue = $this->getValue();
			$test = [$this->checkedValue, $this->uncheckedValue];
			if (!in_array($curValue, $test))
				$this->setValue($curValue);
		}

		parent::__construct($name, $options);
	}

	public function isValid(): bool {
		return (!$this->required || $this->isChecked());
	}

	public function getValue() {
		return parent::getValue() == $this->checkedValue
			? $this->checkedValue
			: $this->uncheckedValue;
	}

	public function setValue($value) {
		if ($value == $this->checkedValue)
			parent::setValue($this->checkedValue);
		else
			parent::setValue($this->uncheckedValue);

		return $this;
	}

	public function setChecked($flag): static {
		return $this->setValue($flag ? $this->checkedValue : $this->uncheckedValue);
	}

	public function isChecked(): bool {
		return $this->getValue() === $this->checkedValue;
	}

	public function getHtml(): string {
		return '<input type="checkbox"' . $this->attributes . ' value="' . $this->checkedValue . '"' . ($this->isChecked() ? ' checked="checked"' : '') . '>';
	}

}
