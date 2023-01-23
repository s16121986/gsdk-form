<?php

namespace Gsdk\Form\Element\Basics;

class Hidden extends Input {

	protected array $options = [
		'inputType' => 'hidden',
		'nullValue' => ''
	];

	protected function prepareValue($value) {
		if ($this->nullValue === $value)
			$value = null;

		return parent::prepareValue($value);
	}

}
