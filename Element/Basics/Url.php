<?php

namespace Gsdk\Form\Element;

class Url extends Text {

	protected $options = [
		'inputType' => 'url'
	];

	public function checkValue($value): bool {
		return '' === $value || filter_var($value, FILTER_VALIDATE_URL);
	}


}
