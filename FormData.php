<?php

namespace Gsdk\Form;

class FormData {

	private $model

	public function __construct(private readonly Form $form) { }

	public function __get(string $name) {
		$element = $this->form->getElement($name);
		if ($element)
			return $element->getValue();
		else
			return null;
	}

	public function setModel($model): void {
		$this->model = $model;
		foreach ($this->form->getElements() as $element) {
			$element->setValue($model->{$element->name});
		}
	}

	public function add($data): void {
		$data = (object)$data;
		foreach ($this->form->getElements() as $element) {
			if (isset($data->{$element->name}))
				$element->setValue($data->{$element->name});
		}
	}

	public function save() {
		if (!$this->model)
			throw new \Exception('Model not defined');

		$this->model->fill($this->toArray());
		$this->model->save();
	}

	public function toArray(): array {
		$data = [];

		foreach ($this->form->getElements() as $element) {
			if ($element->disabled || !$element->readable || ($element->isEmpty() && !$element->isEmptyAllowed()))
				continue;

			$data[$element->name] = $element->getValue();
		}

		return $data;
	}

	public function getNotEmpty(): array {
		return array_filter($this->toArray());
	}
}