<?php

namespace Gsdk\Form\Concerns;

trait Renderable {

	public function render() {
		$view = $this->getOption('view');
		if ($view)
			return view($view, ['form' => $this]);

		return $this->renderElements();
	}

	public function renderElements(...$elements): string {
		if (empty($elements))
			$elements = array_keys($this->elements);
		else if (is_array($elements[0]))
			$elements = $elements[0];

		$html = '';
		foreach ($elements as $k) {
			$element = $this->getElement($k);
			if ($element && $element->isRenderable() && !$element->isRendered())
				$html .= $this->renderElement($element);
		}

		return $html;
	}

	public function renderElement(string|ElementInterface $element): string {
		if (is_string($element) && !($element = $this->getElement($element)))
			return '';

		if (in_array($element->type, ['hidden']) && !$element->label)
			return $element->render();

		$html = '';
		$error = null;
		$cls = 'form-field field-' . $element->type . ' field-' . $element->name . '';
		if ($this->isSubmitted() && !$element->isValid() && !($element instanceof self)) {
			$cls .= ' field-invalid';
			$error = $element->getError();
		}

		if ($element->required)
			$cls .= ' field-required';

		$html .= '<div class="' . $cls . '">';
		$renderData = [
			'label' => '',
			'input' => $element->render(),
			'hint' => ($element->hint ? '<div class="form-element-hint">' . $element->hint . '</div>' : ''),
			'error' => ($error && is_string($error) ? '<span class="error">' . $error . '</span>' : '')
		];

		if ($element->label)
			$renderData['label'] = $element->renderLabel();

		$renderTpl = ($element->renderTpl ?: '%label%%input%%error%%hint%');
		foreach ($renderData as $k => $v) {
			$renderTpl = str_replace('%' . $k . '%', $v, $renderTpl);
		}
		$html .= $renderTpl;
		$html .= '</div>';

		return $html;
	}

}