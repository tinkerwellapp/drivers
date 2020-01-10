<?php

namespace Tinkerwell\ContextMenu;

class Submenu implements \JsonSerializable
{

    protected $label;
    protected $children;

    public function __construct($label, $children = [])
    {
        $this->label = $label;
        $this->children = $children;
    }

    public static function create($label, $children = [])
    {
        return new static($label, $children);
    }

    public function jsonSerialize()
    {
        return [
            'label' => $this->label,
            'submenu' => $this->children,
        ];
    }
}