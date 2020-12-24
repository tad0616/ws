<?php
/*
 * 創建Server對象，監聽 127.0.0.1:9501端口
 * $serv = new swoole_server(string $host, int $port = 0, int $mode = SWOOLE_PROCESS,int $sock_type = SWOOLE_SOCK_TCP);
 * mode代表server的進程模式，這裡默認為多進程
 * sock_type為服務器類型，這裡默認為TCP類型的，若要改用UDP，則設為 SWOOLE_SOCK_UDP
 * 官方文檔：https://wiki.swoole.com/wiki/page/14.html
 */
$serv = new swoole_server("0.0.0.0", 21);

/*
 * 監聽連接進入事件
 * $serv：伺服器訊息
 * $fd：客戶端訊息
 */
$serv->on('connect', function ($serv, $fd) {
    echo "Client: 建立連接.\n";
});

/*
 * 監聽數據接收事件
 * $serv：伺服器訊息
 * $fd：客戶端訊息
 * $from_id：ID
 * $data：數據
 */
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    echo "Client: 接收到數據.\n";
    var_dump($data);
    // $serv->send($fd, "Server: " . $data);
});

//監聽連接關閉事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: 關閉連接.\n";
});

//啟動服務器
$serv->start();
