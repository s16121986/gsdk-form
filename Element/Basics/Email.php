<?php

namespace Gsdk\Form\Element\Basics;

class Email extends Input {

	protected array $options = [
		'inputType' => 'email'
	];

	protected $attributes = ['maxlength', 'autocomplete', 'minlength', 'multiple', 'pattern', 'placeholder', 'readonly', 'size'];

	public function checkValue($value): bool {
		return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
	}

}
