<?php

namespace StarCore\Core\Socket;

use StarCore\Exception\SocketException;

class SocketBase
{
    protected \Socket $socket;

    public function __construct($socket = null)
    {
        $this->socket = $socket ?? socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }

    public function __destruct()
    {
        socket_close($this->socket);
    }

    /**
     * @throws SocketException
     */
    public function write(string $data)
    {
        $length = strlen($data);
        $sent = @socket_write($this->socket, $data);
        if ($sent === false) {
            $this->error();
        }
        if ($length > $sent) {
            $data = substr($data, $sent);
            $this->write($data);
        }
    }

    /**
     * @throws SocketException
     */
    public function read(int $length): string
    {
        $read = @socket_read($this->socket, $length);
        if ($read === false) {
            $this->error();
        }
        $readLength = strlen($read);
        if ($length > $readLength) {
            $read .= $this->read($length - $readLength);
        }
        return $read;
    }

    /**
     * @throws SocketException
     */
    protected function error(): void
    {
        $code = socket_last_error();
        $msg = socket_strerror($code);
        throw new SocketException($msg, $code);
    }
}