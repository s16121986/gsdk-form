<?php

namespace Gsdk\Form\Support;

use Gsdk\Form\Form;

class Validator {

	private array $rules = [];

	private array $messages = [];

	private array $errors = [];

	public function __construct(private readonly Form $form) { }

	public function setRules(array $rules): void {
		$this->rules = $rules;
	}

	public function setMessages(array $messages): void {
		$this->messages = $messages;
	}

	public function getErrors(): array {
		$errors = $this->errors;

		foreach ($this->form->getElements() as $element) {
			if (!$element->isValid())
				$errors[$element->name] = $element->getError();
		}

		return $errors;
	}

	public function addError($error): void {
		$this->errors[] = $error;
	}

	public function isValid(): bool {
		if (!empty($this->errors))
			return false;

		foreach ($this->form->getElements() as $element) {
			if (!$element->isValid())
				return false;
		}

		return true;
	}

	public function reset(): void {
		$this->errors = [];
	}

	public function validateElements() {
		foreach ($this->form->getElements() as $element) {
			if ($element->disabled || !$element->isSubmittable())
				continue;

			if ($element->isFileUpload())
				$this->validateFileElement();
			else {
				$element->setValue($value);
			}
		}
	}

	public function validate($sentData) {


		if ($this->rules)
			$request->validate($this->rules, $this->messages);
	}

	public function elementRequiredMessage($element): string {
		if ($element->requiredMessage)
			return $element->requiredMessage;
		else if ($this->messages[$element->name])
			return $this->messages[$element->name];
		else
			return 'Element required';
	}

	public function elementValidationMessage($element): string {
		if ($element->requiredMessage)
			return $element->requiredMessage;
		else if ($this->messages[$element->name])
			return $this->messages[$element->name];
		else
			return 'Element required';
	}
}