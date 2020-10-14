<?php

namespace Alura\Chat;

use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampConnection;
use Ratchet\Wamp\WampServerInterface;

class ChatWamppController implements WampServerInterface
{
    private array $connectionSet;

    public function __construct()
    {
        fwrite(STDOUT, 'Instanciado' . PHP_EOL);
        $this->connectionSet = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $cookieList = $this->parseCookieList($conn->httpRequest);
        $token = $cookieList['token'];

        if ($token !== 'abcdef') {
            $conn->close();
        }

        fwrite(STDOUT, 'Conexão aberta' . PHP_EOL);
        $this->connectionSet[] = $conn;
    }

    private function parseCookieList(RequestInterface $httpHandshakeRequest): array
    {
        $cookieString = $httpHandshakeRequest->getHeaderLine('cookie');
        $cookieStringList = explode(';', $cookieString);
        $cookieList = [];
        foreach ($cookieStringList as $cookieString) {
            [$name, $value] = explode('=', trim($cookieString));
            $cookieList[$name] = $value;
        }

        return $cookieList;
    }

    public function onClose(ConnectionInterface $conn)
    {
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        fwrite(STDERR, 'Erro:' . $e->getTraceAsString());
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
    }

    /**
     * @param WampConnection $conn
     * @param Topic $topic
     * @param string $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        $topic->broadcast($event, [$conn->WAMP->sessionId]);
    }
}
