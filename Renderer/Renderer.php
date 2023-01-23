<?php


public function renderErrors() {
	if (empty($this->errors))
		return '';

	$s = '<div class="form-errors">';
	//$s .= '<div class="label"><span>!</span> Ошибки:</div>';
	$s .= '<ul>';
	foreach ($this->errors as $v) {
		$s .= '<li>' . $v . '</li>';
	}
	$s .= '</ul>';
	$s .= '</div>';

	return $s;
}

public function report() {
	if ($this->successMessage) {
		return '<div class="form-success"><span>!</span>' . $this->successMessage . '</div>';
	} else {
		return $this->renderErrors();
	}
	return '';
}