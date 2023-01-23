<?php

namespace Gsdk\Form\Concerns;

trait HasOptions {

	public function setOptions($options): static {
		foreach ($options as $k => $v) {
			$this->setOption($k, $v);
		}

		return $this;
	}

	public function setOption($key, $option): static {
		$this->options[$key] = $option;

		return $this;
	}

	public function getOption($key) {
		return $this->options[$key] ?? null;
	}

}