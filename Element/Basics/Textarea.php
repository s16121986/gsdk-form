<?php

namespace Gsdk\Form\Element;

class Textarea extends Xhtml {

	protected $options = [
		'stripTags' => false,
		'unsafe' => true
	];

	protected $attributes = ['placeholder', 'maxlength', 'required', 'autofocus', 'readonly'];

	protected function prepareValue($value) {
		if (is_scalar($value))
			$value = (string)$value;
		else
			return '';

		if ($this->stripTags)
			$value = strip_tags($value);

		if ($this->unsafe)
			$value = filter_var($value, FILTER_UNSAFE_RAW);

		return trim($value);
	}

	public function getHtml(): string {
		return '<textarea' . $this->attributes() . '>' . $this->getValue() . '</textarea>';
	}

}
