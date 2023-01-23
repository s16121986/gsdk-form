<?php

namespace Gsdk\Form\Support;

use Gsdk\Form\Form;

class HttpAction {

	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	const METHOD_DELETE = 'delete';
	const METHOD_PUT = 'put';

	private bool $submitResult = false;

	private $sentData;

	private $uploadedData;

	public function __construct(private readonly Form $form) {
	}

	public function isSent(): bool {
		$name = $this->form->getName();

		switch ($this->form->getOption('method')) {
			case self::METHOD_POST:
				return 'POST' === $_SERVER['REQUEST_METHOD']
					&& (!$name || (isset($_POST[$name]) || isset($_FILES[$name])));
			case self::METHOD_GET:
				$data = $name ? ($_GET[$name] ?? null) : $_GET;

				return !empty($data);
		}

		return false;
	}

	public function submit(): bool {
		if (!$this->isSent())
			return false;

		$form = $this->form;
		$validator = $form->getValidator();
		//$validator->validate();

		$form->setSubmitted(true);

		$this->sentData = $this->getSentData();
		$this->uploadedData = $this->getUploadData();

		foreach ($form->getElements() as $element) {
			if ($element->disabled || !$element->isSubmittable())
				continue;

			if ($element->isFileUpload()) {
				if (isset($uploadData[$element->name]))
					$element->setValue($uploadData[$element->name]);

				if (isset($sentData[$element->name]))
					$element->setData($sentData[$element->name]);

				continue;
			}

			// checkbox fields need to set null value
			if (array_key_exists($element->name, $this->sentData)) {
				$value = $this->sentData[$element->name];
				if ($element->checkValue($value))
					$element->setValue($value);
				else if ($element->required)
					$element->setError($validator->elementValidationMessage($element));
			} else if ($element->required)
				$element->setError($validator->elementRequiredMessage($element));
			else
				$element->setValue(null);
		}

		return $form->isValid();
	}

	private function getUploadData() {
		$form = $this->form;
		$files = $_FILES;
		$data = [];
		if ($form->name)
			$files = (isset($files[$form->name]) ? $files[$form->name] : []);

		if (isset($files['tmp_name'])) {
			foreach ($files as $paramName => $v) {
				foreach ($v as $fieldName => $value) {
					if (is_array($value)) {
						foreach ($value as $i => $vv) {
							$data[$fieldName][$i][$paramName] = $vv;
						}
					} else {
						$data[$fieldName][$paramName] = $value;
					}
				}
			}
			$dataTemp = $data;
			$data = [];
			foreach ($dataTemp as $fieldName => $items) {
				if (isset($items['tmp_name'])) {
					if ($items['tmp_name'] && $items['error'] == 0) {
						$data[$fieldName] = $items;
					}
				} else {
					foreach ($items as $item) {
						if ($item['tmp_name'] && $item['error'] == 0) {
							$data[$fieldName][] = $item;
						}
					}
				}
			}
		} else {
			foreach ($files as $fieldName => $v) {
				if ($v['tmp_name']) {
					$data[$fieldName] = [];
					if (is_array($v['tmp_name'])) {
						foreach ($v['tmp_name'] as $i => $tmp_name) {
							if ($v['error'][$i] != 0) {
								continue;
							}
							$data[$fieldName][$i] = [];
							foreach ($v as $paramName => $values) {
								$data[$fieldName][$i][$paramName] = $values[$i];
							}
						}
					} else {
						if ($v['error'] != 0) {
							continue;
						}
						foreach ($v as $paramName => $value) {
							$data[$fieldName][$paramName] = $value;
						}
					}
				}
			}
		}

		return $data;
	}

	private function getSentData() {
		$form = $this->form;
		$data = match ($form->method) {
			'post' => $_POST,
			'get' => $_GET,
			default => $_REQUEST,
		};

		if ($form->getName())
			return (isset($data[$form->getName()]) ? $data[$form->getName()] : []);

		return $data;
	}

}