<?php

namespace Gsdk\Form\Element\Concerns;

use Gsdk\Form\Element\ElementInterface;
use Gsdk\Form\Form;

abstract class AbstractElement implements ElementInterface {

	private static array $defaultOptions = [
		'required' => false,
		'disabled' => false,
		'readable' => true,
		'render' => true,
		'rules' => null, //validation rules
		'requiredText' => ''
	];

	protected array $options = [];

	protected ?AbstractParent $parent;

	protected Label $label;

	protected mixed $value;

	protected ?string $error;

	protected bool $rendered = false;

	abstract protected function getHtml(): string;

	public function __construct(private readonly string $name, array $options = []) {
		$options = array_merge(static::$defaultOptions, $options);

		$value = $options['value'] ?? null;
		unset($options['value']);

		$this->options = array_merge($this->options, $options);

		$this->setValue($value);

		$this->label = new Label(array_merge($this->options, [
			'text' => $this->options['label'] ?? null
		]));
		$this->label->setElement($this);
	}

	public function __get($name) {
		return match ($name) {
			'value' => $this->getValue(),
			'id' => $this->getId(),
			'inputName' => $this->getInputName(),
			default => $this->options[$name] ?? null,
		};
	}

	public function setParent($parent): static {
		$this->parent = $parent;

		return $this;
	}

	public function setForm($form): static {
		return $this->setParent($form);
	}

	public function getForm(): ?Form {
		if (null === $this->parent)
			return null;
		else if ($this->parent instanceof Form)
			return $this->parent;
		else
			return $this->parent->getForm();
	}

	public function getId(): string {
		if (isset($this->options['id']))
			return $this->options['id'];

		$parts = [];
		if ($this->parent && $this->parent->getId())
			$parts[] = $this->parent->getId();

		$parts[] = $this->name;

		return $this->options['id'] = implode('_', $parts);
	}

	public function getInputName(): string {
		if (isset($this->options['inputName']))
			return $this->options['inputName'];

		else if (!$this->parent)
			return $this->options['inputName'] = $this->name;

		if ($this->parent instanceof ElementInterface)
			$name = $this->parent->getInputName();
		else
			$name = $this->parent->getName();

		if ($name && $this->name)
			$name .= '[' . $this->name . ']';
		else
			$name = $this->name;

		return $this->options['inputName'] = $name;
	}

	public function getLabel(): Label {
		return $this->label;
	}

	public function checkValue($value): bool {
		return true;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $this->prepareValue($value);
	}

	public function getError(): ?string {
		return $this->error;
	}

	public function setError(string $error): static {
		$this->error = $error;
		return $this;
	}

	public function isEmpty(): bool {
		return empty($this->getValue());
	}

	public function isValid(): bool {
		if ($this->disabled)
			return true;
		else if ($this->error)
			return false;
		else
			return !$this->required || !$this->isEmpty();
	}

	public function isSubmittable(): bool {
		return true;
	}

	public function isFileUpload(): bool {
		return false;
	}

	public function reset(): static {
		$this->value = null;
		$this->error = null;
		$this->rendered = false;
		return $this;
	}

	public function render(): string {
		$this->rendered = true;

		return $this->getHtml();
	}

	public function setRendered(bool $flag): static {
		$this->rendered = $flag;

		return $this;
	}

	public function isRendered(): bool {
		return $this->rendered;
	}

	protected static function escape($val) {
		if (is_array($val))
			$val = implode(',', $val);
		else if (is_float($val))
			return str_replace(',', '.', $val);

		return str_replace('"', '&quot;', $val);
	}

	protected function prepareValue($value) {
		if (is_null($value))
			return null;

		return match ($this->options['cast'] ?? 'default') {
			'int', 'integer' => (int)$value,
			'string' => (string)$value,
			'bool', 'boolean' => (bool)$value,
			'real', 'float', 'double' => $this->fromFloat($value),
			'decimal' => number_format($value, explode(':', $this->options['cast'], 2)[1], '.', ''),
			default => $value,
		};
	}

	protected function fromFloat($value): float {
		return match ((string)$value) {
			'Infinity' => INF,
			'-Infinity' => -INF,
			'NaN' => NAN,
			default => (float)$value,
		};
	}

}