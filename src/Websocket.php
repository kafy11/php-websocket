<?php
namespace ServerWS;

use ServerWS\libs\Connection;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface {
    protected $clients; 

    public function onOpen(ConnectionInterface $con) {
        $conn = new Connection($con);
        if($conn->getId()){
            $this->clients[$conn->getResourceId()] = $conn;
        }
    }

    private function sendMessage($to, $msg){
        foreach($this->clients as $client){
            if($client->getId() == $to){
                unset($msg['to']);
                $msg['from'] = $to;

                $client->send($msg);
            }
        }
    }

    public function onMessage(ConnectionInterface $con, $msg) {
        $conn = new Connection($con);
        try{
            $msg = json_decode($msg, true);
            if(isset($msg['to'])){
                $to = $msg['to'];
                $this->sendMessage($to, $msg);
            } else {
                throw new \Exception('Nenhum destino definido');
            }
        } catch (\Exception $e) {
            $conn->send([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function onClose(ConnectionInterface $con) {
        $resourceId = $con->resourceId;
        unset($this->clients[$resourceId]);
    }

    public function onError(ConnectionInterface $con, \Exception $e) {
        $conn = new Connection($con);
        $conn->close();
    }
}
