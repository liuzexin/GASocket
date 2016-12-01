<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 16/11/29
 * Time: 下午6:20
 */

namespace ga\socket;

class SocketServer extends SocketConnection{

    public $singleMode = false;


    private $clients = [];
    public $maxClient = 10;

    public function __construct($cof_arr = null)
    {
        parent::__construct($cof_arr);
    }

    public function send($socket, $data)
    {
        return socket_write($socket, $data, strlen($data));
    }

    public function start()
    {
        $this->updateSocketOpt();

        if(!$res = socket_bind($this->getSocket(),$this->ip, $this->port)){
            throw new SocketException('Bind socket error:'. $this->getError() . "\n");
        }
        if(!$res = socket_listen($this->getSocket(), SOMAXCONN)){
            throw new SocketException('Listen port error:'. $this->getError() . "\n");
        }

        if($this->singleMode){
            $this->singleRun();
        }else{
            $this->multiRun();
        }
    }

    private function singleRun(){
        do {

            if (($newSocket = @socket_accept($this->getSocket())) === false) {
                throw new SocketException('Accepts a connection on a socket error:' . $this->getError());
            }
            $msg = "Start connection.\n";

            $this->send($newSocket, $msg);

            do {
                if (false === ($data = @socket_read($newSocket, 2048, PHP_NORMAL_READ))) {
                    throw new SocketException("Read failed: reason: " . $this->getError() . "\n");
                }
                if (empty($data = trim($data))) {
                    continue;
                }
                elseif ($data == 'quit') {
                    break;
                }
                elseif ($data == 'shutdown') {
                    socket_close($newSocket);
                    break 2;
                }

                if(!empty($this->callback)){
                    $func = $this->callback;
                    $func($data, $newSocket);
                }

            } while (true);

            socket_close($newSocket);

        } while (true);

        $this->stop();
    }

    private function multiRun(){

        $read[] = $this->getSocket();
        do {
            $num_changed = socket_select($read, $NULL, $NULL, 0, 10);
            if($num_changed) {


                if (in_array($this->getSocket(), $read)) {

                    if (count($this->clients) < $this->maxClient) {

                        $this->clients[] = socket_accept($this->getSocket());
                    }

                }
            }

            foreach($this->clients as $key => $client) {

                if(in_array($client, $read)) {
                    if (false === ($data = @socket_read($client, 2048, PHP_NORMAL_READ))) {
                        socket_close($client);
                        unset($this->clients[$key]);
                    }
                    if (empty($data = trim($data))) {
                        continue;
                    }
                    elseif ($data == 'quit') {
                        socket_close($client);
                        unset($this->clients[$key]);
                        break;
                    }
                    elseif ($data == 'shutdown') {
                        break 2;
                    }
                    else{
                        if(!empty($this->callback)){
                            $func = $this->callback;
                            $func($data, $client);
                        }
                    }
                }
            }

            $read = $this->clients;
            $read[] = $this->getSocket();

        } while (true);

        $this->stop();
    }

}
