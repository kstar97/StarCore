<?php

namespace StarCore\Core\Socket;

use StarCore\Exception\SocketException;

class SocketClient extends SocketBase
{
    private int $timeout;

    /**
     * @throws SocketException
     */
    public function __construct(string $host, int $port, int $timeout = 0)
    {
        parent::__construct();

        $this->timeout = $timeout;
        if (socket_connect($this->socket, $host, $port) === false) {
            $this->error();
        }
    }
}