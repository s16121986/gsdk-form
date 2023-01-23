<?php

namespace Gsdk\Form\Element;

interface ElementInterface {
	public function render(): string;

	public function getInputName(): string;
}