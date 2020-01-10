<?php

namespace Tinkerwell\ContextMenu;

class OpenURL implements \JsonSerializable
{

    protected $label;
    protected $url;

    public function __construct($label, $url)
    {
        $this->label = $label;
        $this->url = $url;
    }

    public static function create($label, $url)
    {
        return new static($label, $url);
    }

    public function jsonSerialize()
    {
        return [
            'label' => $this->label,
            'action' => 'url',
            'url' => $this->url,
        ];
    }
}