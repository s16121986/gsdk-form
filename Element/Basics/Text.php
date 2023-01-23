<?php

namespace Gsdk\Form\Element;

class Text extends AbstractInput {

	protected $options = [
		'inputType' => 'text'
	];

	protected $attributes = ['autocomplete', 'list', 'maxlength', 'minlength', 'pattern', 'placeholder', 'readonly', 'required', 'size', 'spellcheck'];

	protected function prepareValue($value) {
		return trim(filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW));
	}

}
