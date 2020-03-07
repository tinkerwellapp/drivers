<?php

namespace Tinkerwell\ContextMenu;

class RunCode implements \JsonSerializable {

	protected $label;
	protected $code;

	public function __construct($label, $code) {
		$this->label = $label;
		$this->code = $code;
	}

	public static function create($label, $code) {
		return new static($label, $code);
	}

	public function jsonSerialize() {
		return [
			'label' => $this->label,
			'action' => 'run-code',
			'code' => $this->code,
		];
	}
}
