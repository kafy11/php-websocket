<?php
namespace ServerWS\libs;

class Connection {
    private $conn;
    function __construct($conn){
        $this->conn = $conn;
        $this->id = $this->getIdFromQuery();
    }

    public function getId(){
        return $this->id;
    }

    public function getResourceId(){
        return $this->conn->resourceId;
    }

    public function getConnection(){
        return $this->conn;
    }

    private function getIdFromQuery() {
        parse_str($this->conn->httpRequest->getUri()->getQuery(),$params);
        if(isset($params['id'])){
            $v_id = $params['id'];

            if(is_numeric($v_id)){
                return $v_id;
            }
        }

        $this->conn->close();
        return false;
    }

    public function send($msg) {
        $msg = json_encode($msg);
        $this->conn->send($msg);
    }

    public function close(){
        $this->conn->close();
    }
}