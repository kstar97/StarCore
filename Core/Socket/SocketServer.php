<?php

namespace StarCore\Core\Socket;

use StarCore\Core\Pack;
use StarCore\Exception\SocketException;

class SocketServer extends SocketBase
{
    private array $clients = [];

    /**
     * @throws SocketException
     */
    public function __construct(string $host, int $port)
    {
        parent::__construct();

        if (socket_bind($this->socket, $host, $port) === false) {
            $this->error();
        }
        if (socket_listen($this->socket) === false) {
            $this->error();
        }

        $this->accept();
    }

    private function accept()
    {
        while (true) {
            $socket = socket_accept($this->socket);
            $client = new SocketBase($socket);
            echo "get new socket" . PHP_EOL;
            $data = $client->read(5);
            echo "client say:" . $data;
            $pack = new Pack();
            $pack->writeInt(0);
            $client->write($pack->getBinary());
            $this->clients[] = $client;
        }
    }
}