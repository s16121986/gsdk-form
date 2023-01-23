<?php

namespace Gsdk\Form;

use Illuminate\Foundation\Http\FormRequest;

class Form {

	use Concerns\Renderable,
		Concerns\HasOptions,
		Concerns\HasElements,
		Concerns\HasExtensions;

	protected Support\HttpAction $httpAction;

	protected Support\Validator $validator;

	protected FormData $data;

	protected array $options = [
		'id' => 'form_data',
		'name' => null,
		'method' => 'get'
	];

	protected bool $submitted = false;

	public function __call(string $name, array $arguments) {
		if (!isset($arguments[0]))
			throw new \ArgumentCountError('Name required');

		return $this->addElement($arguments[0], $name, $arguments[1] ?? []);
	}

	public function __construct($options = null) {
		$this->httpAction = new Support\HttpAction($this);
		$this->validator = new Support\Validator($this);
		$this->data = new FormData($this);

		if (is_string($options))
			$options = ['name' => $options];

		if (is_array($options)) {
			if (!isset($options['id']) && isset($options['name']))
				$options['id'] = 'form_' . $options['name'];

			$this->setOptions($options);
		}
	}

	public function __get($name) {
		return $this->options[$name] ?? null;
	}

	public function getValidator(): Support\Validator {
		return $this->validator;
	}

	public function data($data): static {
		$this->data->add($data);

		return $this;
	}

	public function model($model) {
		$this->data->setModel($model);

		return $this;
	}

	public function getData(): FormData {
		return $this->data;
	}

	public function requestForm(FormRequest $formRequest): static {
		//$this->validator->setRequestForm($formRequest);

		return $this;
	}

	public function rules(array $rules): static {
		$this->validator->setRules($rules);

		return $this;
	}

	public function messages(array $messages): static {
		$this->validator->setMessages($messages);

		return $this;
	}

	public function setSubmitted($flag): static {
		$this->submitted = (bool)$flag;
		return $this;
	}

	public function isSubmitted(): bool {
		return $this->submitted;
	}

	public function isSent(): bool {
		return $this->httpAction->isSent();
	}

	public function isValid(): bool {
		return $this->validator->isValid();
	}

	public function error($error): static {
		$this->validator->addError($error);

		return $this;
	}

	public function errors(): array {
		if (!$this->isSent())
			return [];

		return $this->validator->getErrors();
	}

	public function reset(): void {
		foreach ($this->elements as $element) {
			$element->reset();
		}

		$this->validator->reset();
		$this->submitted = false;
	}

	public function submit(): bool {
		return $this->httpAction->submit();
	}

	public function getRenderer() {
		return new Renderer($this);
	}

	public function __toString(): string {
		return $this->getRenderer()->render();
	}

}