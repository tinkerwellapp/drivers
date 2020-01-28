<?php

namespace Tinkerwell\ContextMenu;

class Separator implements \JsonSerializable {

	public static function create() {
		return new static();
	}

	public function jsonSerialize() {
		return [
			'type' => 'separator',
		];
	}
}