<?php

namespace Alura\Chat;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ds\Set;

class ChatController implements MessageComponentInterface
{
    private Set $connections;

    public function __construct()
    {
        fwrite(STDOUT, 'Servidor inicializado' . PHP_EOL);
        $this->connections = new Set();
    }

    function onOpen(ConnectionInterface $conn)
    {
        fwrite(STDOUT, 'Nova conexão aberta: ' . $conn->resourceId . PHP_EOL);
        $this->connections->add($conn);
    }

    function onClose(ConnectionInterface $conn)
    {
        fwrite(STDOUT, $conn->resourceId . ' fechando conexão...' . PHP_EOL);
        $this->connections->remove($conn);
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        fwrite(STDERR, sprintf('Erro %s com conexão %d%s', $e->getTraceAsString(), $conn->resourceId, PHP_EOL));
    }

    public function onMessage(ConnectionInterface $from, MessageInterface $msg)
    {
        /** @var ConnectionInterface $connection */
        foreach ($this->connections as $connection) {
            if ($connection->resourceId !== $from->resourceId) {
                $connection->send($msg);
            }
        }
    }
}
