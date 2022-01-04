<?php

namespace StarCore\Core;

class Pack
{
    const Int = "I";
    const Short = "S";
    const String = "A";

    protected int $binaryLength = 0;
    private string $binary = "";

    public function writeInt(int $data): void
    {
        $this->binaryLength += 4;
        $this->binary .= pack(self::Int, $data);
    }

    public function writeShort(int $data): void
    {
        $this->binaryLength += 2;
        $this->binary .= pack(self::Short, $data);
    }

    public function writeString(string $data): void
    {
        $length = strlen($data);
        $this->binaryLength += $length;
        $this->binary .= pack(self::String . $length, $data);
    }

    public function getBinary(): string
    {
        return $this->binary;
    }
}