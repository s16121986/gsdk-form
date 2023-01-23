<?php

namespace Gsdk\Form\Element;

class Radio extends Xhtml {

	protected $_items = null;

	protected function initItem($value, $text = '') {
		if (is_array($text)) {
			$value = $text;
			$text = null;
		}
		$this->_items[] = new Select\Item($value, $text);
	}

	private static function getValueId($value) {
		if (is_string($value) && preg_match('/\d+/', $value)) {
			$value = (int)$value;
		}
		return $value;
	}

	public function isEmpty(): bool {
		return (null === $this->getValue());
	}

	public function getItems() {
		if (null === $this->_items) {
			$this->_items = [];
			$itemsTemp = [];
			$itemsData = $this->items;

			if (is_iterable($itemsData)) {
				foreach ($itemsData as $k => $v) {
					$this->initItem($k, $v);
				}
			}

			foreach ($itemsTemp as $v) {
				$this->initItem($v);
			}
		}
		return $this->_items;
	}

	public function checkValue($value) {
		return true;
	}

	public function getValue() {
		$value = parent::getValue();
		if (null === $value && false === $this->emptyItem && $this->_items) {
			return $this->_items[0]->value;
		}
		return $value;
	}

	protected function prepareValue($value) {
		return self::getValueId($value);
	}

	public function getValuePresentation() {
		$selected = $this->getValue();
		foreach ($this->getItems() as $item) {
			if ($item->value == $selected)
				return $item->getPresentation();
		}
		return '';
	}

	public function isSelected($value): bool {
		$value = self::getValueId($value);
		return ($this->getValue() === $value);
	}

	public function getHtml(): string {
		$html = '<div class="field-radio-box">';//'<select' . $this->attrToString() . '>';
		$i = 0;
		foreach ($this->getItems() as $item) {
			$isSel = $this->isSelected($item->value);
			$html .= '<div class="radio-item">'
				. '<input type="radio" id="' . $this->id . '_' . $i . '" name="' . $this->getInputName() . '" value="' . self::escape($item->value) . '"' . ($isSel ? ' checked' : '') . '>'
				. '<label for="' . $this->id . '_' . $i . '">' . $item->text . '</label>'
				. '</div>';
			$i++;
		}
		$html .= '</div>';
		return $html;
	}

}
