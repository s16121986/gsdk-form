<?php

namespace Gsdk\Form\Element;

class Tree extends Select {

	protected $options = [
		'valueInList' => true,
		'allowFolder' => true,
		'valueIndex' => 'id',
		'textIndex' => 'name',
		'parentIndex' => 'parent_id',
		'treeIndent' => '&nbsp;&nbsp;&nbsp;&nbsp;',
		'emptyItem' => false
	];

	protected function getOptionsHtml() {
		$html = '';
		if (false !== $this->emptyItem)
			$html .= '<option value="' . self::EMPTY_VALUE . '">' . $this->emptyItem . '</option>';

		$html .= $this->tree();
		return $html;
	}

	private function tree($parentId = null, $level = 0) {
		$html = '';
		foreach ($this->getItems() as $item) {
			if ($item->{$this->parentIndex} != $parentId) {
				continue;
			}
			$html .= '<option value="' . self::escape($item->value) . '"' . ($this->isSelected($item->value) ? ' selected' : '') . '>'
				. self::indentpad($this->treeIndent, $level)
				. $item->text
				. '</option>';
			$html .= $this->tree($item->value, $level + 1);
		}
		return $html;
	}

	private static function indentpad($indent, $count) {
		$str = '';
		for ($i = 0; $i < $count; $i++) {
			$str .= $indent;
		}
		return $str;
	}

}