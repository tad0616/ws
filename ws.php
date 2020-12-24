<?php
$ws = new swoole_websocket_server("127.0.0.1", 3999);
//服務的基本設定
$ws->set(array(
    'worker_num' => 2,
    'reactor_num' => 8,
    'task_worker_num' => 1,
    'dispatch_mode' => 2,
    'debug_mode' => 1,
    'daemonize' => true,
    'heartbeat_check_interval' => 60,
    'heartbeat_idle_time' => 600,
));

$ws->on('connect', function ($ws, $fd) {
    echo "client:$fd Connect." . PHP_EOL;
});

//測試receive
$ws->on("receive", function (swoole_server $ws, $fd, $from_id, $data) {
    echo "receive#{$from_id}: receive $data " . PHP_EOL;
});

$ws->on('open', function ($wser, $req) {
    echo "server#{$wser->worker_pid}: handshake success with fd#{$req->fd}" . PHP_EOL;;
    echo PHP_EOL;
});

$ws->on('message', function ($wser, $frame) {
    echo "message: " . $frame->data . PHP_EOL;
    $msg = json_decode($frame->data, true);
    switch ($msg['type']) {
        case 'login':
            $wser->push($frame->fd, "歡迎歡迎~");
            break;
        default:
            break;
    }
    $msg['fd'] = $frame->fd;
    $wser->task($msg);
});

$ws->on("workerstart", function ($wser, $workerid) {
    echo "workerstart: " . $workerid . PHP_EOL;
    echo PHP_EOL;
});

$ws->on("task", "on_task");

$ws->on("finish", function ($ws, $task_id, $data) {
    return;
});

$ws->on('close', function ($wser, $fd, $from_id) {
    echo "connection close: " . $fd . PHP_EOL;
    echo PHP_EOL;
});

$ws->start();

function on_task($ws, $task_id, $from_id, $data)
{
    switch ($data['type']) {
        case 'login':
            $send_msg = "說:我來了~";
            break;
        default:
            $send_msg = "說:{$data['msg']['speak']}";
            break;
    }
    foreach ($ws->connections as $conn) {
        if ($conn != $data['fd']) {
            if (strpos($data['msg']['name'], "遊客") === 0) {
                $name = $data['msg']['name'] . "_" . $data['fd'];
            } else {
                $name = $data['msg']['name'];
            }
        } else {
            $name = "我";
        }
        $ws->push($conn, $name . $send_msg);
    }
    return;
}
function on_finish($ws, $task_id, $data)
{
    return true;
}
