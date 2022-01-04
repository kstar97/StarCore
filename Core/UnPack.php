<?php

namespace StarCore\Core;

class UnPack
{
    const Int = "I";
    const Short = "S";
    const String = "A";

    protected int $binaryLength = 0;
    private string $binary;

    public function __construct($data = null)
    {
        $this->binary = $data ?? "";
        $this->binaryLength = $data ? strlen($data) : 0;
    }

    public function setData($data)
    {
        $this->binary = $data;
        $this->binaryLength = strlen($data);
    }

    public function readInt(): int
    {
        $length = 4;
        $binary = substr($this->binary, 0, $length);
        $data = unpack(self::Int, $binary);

        $this->binary = substr($this->binary, $length);
        $this->binaryLength -= $length;
        return $data[1];
    }

    public function readShort(): int
    {
        $length = 2;
        $binary = substr($this->binary, 0, $length);
        $data = unpack(self::Short, $binary);

        $this->binary = substr($this->binary, $length);
        $this->binaryLength -= $length;
        return $data[1];
    }

    public function readString(int $length): string
    {
        $binary = substr($this->binary, 0, $length);
        $data = unpack(self::String . $length, $binary);

        $this->binary = substr($this->binary, $length);
        $this->binaryLength -= $length;
        return $data[1];
    }
}