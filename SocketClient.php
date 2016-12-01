<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 16/12/1
 * Time: 下午12:12
 */
namespace ga\socket;
class SocketClient extends SocketConnection{

    public $sync = false;
    public $sendData = '';

    public function start()
    {
        if(false === socket_connect($this->getSocket(), $this->ip, $this->port)){
            if($this->getError() != SOCKET_EINPROGRESS && $this->getError() != SOCKET_EWOULDBLOCK){
                throw new SocketException('Can\'t connect the server:' . $this->getError());
            }
        }

        $allData = null;

        $this->send($this->getSocket(), $this->sendData);
        while(true){

            $data = socket_read($this->getSocket(),2048);
            if($data === false){
                if($this->getError() != 11 && $this->getError() != 115) {
                    throw new SocketException('Connection terminated unexpectedly:' . $this->getError());
                }
            }
            if($data == ''){
                break;
            }else{
                $allData .= $data;
            }
            if(!empty($this->callback)){
                $func = $this->callback;
                $func($data, $this->getSocket());
            }
        }


        $this->stop();
        return $allData;
    }


    public function updateSocketOpt()
    {
        parent::updateSocketOpt();
        $this->sync?null:socket_set_nonblock($this->getSocket());
    }

    public function send($socket, $data)
    {
        return socket_write($socket, $data, strlen($data));
    }

    protected function getError()
    {
        $err = socket_last_error($this->getSocket()) ;
        if($err != SOCKET_EWOULDBLOCK  && $err != SOCKET_EINPROGRESS){
            return parent::getError();
        }
        return $err;
    }
}
