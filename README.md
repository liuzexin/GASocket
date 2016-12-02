# GASocket is a easy(with block) for PHP socket communication.

#Introduction

The `SocketClient` class is used to create a socket communication from server.`SocketServer` class is used to listen the server port and create socket communication for client request.

GASocket mainly use the 'block' to receive callback.`socket_*` function is the core of GASocket.

When you use the GASocket as server, sever receive the `quit` data form clinet, this client connection will disconnect.Or receive the `shutdown` data from client, server will close in listening port and program execution is completed.

#Installation
```
composer require ga/socket dev-master
```
#Usage
```PHP
$soc = new SocketClient([
]);
$soc->sendData = '1231231231' . "\n";
$soc->callback = function($data, $socket) use ($soc){
    echo 'Client receive:' . $data ."\n";
};
$soc->start();
```
