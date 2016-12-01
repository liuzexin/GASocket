<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 16/11/30
 * Time: 上午9:07
 */
namespace ga\socket;
abstract class SocketConnection{

    public $ip = '127.0.0.1';
    public $port = '20000';

    public $callback;

    public $domain = AF_INET;
    public $type = SOCK_STREAM;
    public $protocol = SOL_TCP;


    public $sendTimeOut = 2;
    public $recTimeOut = 2;

    protected static $socket;

    abstract public function start();

    abstract public function send($socket, $data);

    public function stop()
    {
        socket_close($this->getSocket());
    }

    public function __construct($cof_arr = null)
    {
        set_time_limit(0);
        if(empty($cof_arr)){
            return;
        }
        foreach($cof_arr as $key => $value){
            $this->$key = $value;
        }

    }

    protected function getError()
    {
        return socket_strerror(socket_last_error($this->getSocket()));
    }

    public function getSocket()
    {
        if(self::$socket == null){
            if(!self::$socket = socket_create($this->domain, $this->type, $this->protocol)){

                throw new SocketException('Create socket resource fail:' . $this->getError());
            };
        }
        return self::$socket;
    }

    protected function updateSocketOpt()
    {
        socket_set_option($this->getSocket(), SOL_SOCKET, SO_RCVTIMEO, ['sec'=>$this->recTimeOut, 'usec'=>0]);
        socket_set_option($this->getSocket(), SOL_SOCKET, SO_SNDTIMEO, ['sec'=>$this->sendTimeOut, 'usec'=>0]);
    }
}