<?php

namespace Tinkerwell\ContextMenu;

class Label implements \JsonSerializable
{

    protected $label;

    public function __construct($label)
    {
        $this->label = $label;
    }

    public static function create($label)
    {
        return new static($label);
    }

    public function jsonSerialize()
    {
        return [
            'label' => $this->label,
            'enabled' => false,
        ];
    }
}