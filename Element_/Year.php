<?php

namespace Gsdk\Form\Element;

class Year extends Select {

	protected $options = [
		'yearRange' => 100,
		'emptyItem' => false
	];

	public function getItems() {
		if (null === $this->_items) {
			$this->_items = [];
			if (null !== $this->items) {
				foreach ($this->items as $k => $v) {
					$this->initItem($k, $v);
				}
			} else {
				$currentYear = now()->getYear();
				for ($i = 0; $i <= $this->yearRange; $i++) {
					$this->initItem($currentYear - $i, $currentYear - $i);
				}
			}
		}
		return $this->_items;
	}

}
