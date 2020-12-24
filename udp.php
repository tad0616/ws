<?php
//創建Server對象，監聽 127.0.0.1:9503端口，類型為SWOOLE_SOCK_UDP
$serv = new Swoole\Server("127.0.0.1", 21, SWOOLE_BASE, SWOOLE_SOCK_UDP);
//監聽數據發送事件
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    //發送給客戶端 用sendto
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server " . $data);
    var_dump($data);
});
//啟動服務器
$serv->start();
