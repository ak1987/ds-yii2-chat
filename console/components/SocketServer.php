<?php

namespace app\console\components;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use app\models\Messages;

class SocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $chatters;
    protected $users;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage; // Для хранения технической информации об присоединившихся клиентах используется технология SplObjectStorage, встроенная в PHP
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true); //для приема сообщений в формате json
        switch ($data['action']) {
            case 'set-user':
                $this->chatters[$from->resourceId] = $data['user'];
                $userName = $this->chatters[$from->resourceId];
                $response['msg'] = "User $userName joined chat";
                $response['type'] = 2;
                $this->sendMessageToAll(json_encode($response));
                break;

            case 'send-message':
                $response['msg'] = $data['msg'];
                $response['user'] = $this->chatters[$from->resourceId];
                $response['type'] = 1;
                $this->sendMessageToAll(json_encode($response));
                // save msg
                $msg = new Messages();
                $msg->text = $data['msg'];
                $msg->user_name = $response['user'];
                $msg->timestamp = time();
                $msg->type = 1;
                $msg->save();
                break;

            case 'get-users':
                $response['msg'] = [];
                foreach ($this->chatters as $chatter) {
                    array_push($response['msg'], $chatter);
                }
                $response['type'] = 3;
                $from->send(json_encode($response));
                break;
            case 'kick-user':
                $userName = $data['user'];
                $key = $this->getUserID($userName);
                if ($key && $key != $from->resourceId) {
                    $this->clients->detach($this->users[$key]);
                    unset($this->users[$key]);
                    unset($this->chatters[$key]);
                    $response['msg'] = "User $userName kicked";
                    $response['type'] = 2;
                    $from->send(json_encode($response));
                }
                break;
        }

        echo $from->resourceId . "\n";//id, присвоенное подключившемуся клиенту
    }

    public function onClose(ConnectionInterface $conn)
    {
        $userName = $this->chatters[$conn->resourceId];
        unset($this->chatters[$conn->resourceId]);
        unset($this->users[$conn->resourceId]);
        $response['msg'] = "User $userName left chat";
        $response['type'] = 2;
        $this->sendMessageToAll(json_encode($response));
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function sendMessageToAll($msg)
    {
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    private function getUserID($username)
    {
        foreach ($this->chatters as $key => $chatterName) {
            if ($chatterName == $username) {
                return $key;
            }
        }
    }
}