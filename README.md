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
##namespace
`\ga\socket`

##SocketConnection

**Config Params**
`ip` : Connection ip address.
`port` : Port.
`callback` : When client receive the server's data ,this block will call.
`domain`, `type`, `protocol` : According to [`socket_create()`][1] function to set up.(Default is TCP connection configuration).
`sendTimeOut`,`recTimeOut` : According to [`socket_set_option()`][2] function to set up.(), these two params in seconds.

## SocketClient Extends SocketConnection

**Config Params**
`sync` : If `true` it was blocking mode.Default is `false` means it was 'nonblock' mode.  
`sendData` : When connection is successful send the `sendData`.

**EXAMPLE 1:**
```PHP
$soc = new SocketClient([
    'ip' => '127.0.0.1',
    'port' => '30000'
]);
$soc->sendData = 'data' . "\n";
$soc->callback = function($data, $socket) use ($soc){
    echo 'Client receive:' . $data ."\n";
};
$soc->start();
```
**NOTICE**
In the client `$soc` is the same of `$socket`.

##SocketServer Extends SocketConnection
**Config Params**
`singleMode` : The `true` means that the max number of clients is only one.
`maxClients` : The max number of the clients to connect.(If `singleMode` was `true` ,this param is invalid, and it's default value is 10.)

**EXAMPLE 2:**
```PHP
$soc = new SocketServer([
    'singleModel' =>true
]);
$soc->callback = function ($data, $socket) use($soc){

    echo 'server receive:' . $data ."\n";
    $soc->send($socket, 'Server has receive data'."\n\r");
};
$soc->start();
```
**NOTICE**
`$soc` is the server socket variable, `$socket` is the current connected client socket.

[1]:http://php.net/manual/en/function.socket-create.php
[2]:http://php.net/manual/en/function.socket-set-option.php