<?php

namespace Gsdk\Form\Element\Basics;

use Gsdk\Form\Element\Concerns\AbstractElement;

class Input extends AbstractElement {

	protected array $options = [
		'inputType' => 'text'
	];

//$this->attributes->allow('inputmode');

	public function getHtml(): string {
		return '<input type="' . $this->inputType . '"'
			. $this->attributes
			. ' value="' . self::escape($this->getValue()) . '">'
			. $this->attributes->getHtml();
	}

}

/*<input type="button">
<input type="checkbox">
<input type="color">
<input type="date">
<input type="datetime-local">
<input type="email">
<input type="file">
<input type="hidden">
<input type="image">
<input type="month">
<input type="number">
<input type="password"> ['autocomplete', 'maxlength', 'minlength', 'pattern', 'placeholder', 'readonly', 'required', 'size']
<input type="radio">
<input type="range"> ['autocomplete', 'list', 'max', 'min', 'step'];
<input type="reset">
<input type="search">
<input type="submit">
<input type="tel"> ['autocomplete', 'maxlength', 'minlength', 'pattern', 'placeholder', 'readonly', 'required', 'size']
<input type="text">
<input type="time"> ['autocomplete', 'list', 'readonly', 'step']
<input type="url">
<input type="week">*/