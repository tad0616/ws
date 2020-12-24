<?php
/*
 * 創建Server對象，監聽 127.0.0.1:8443 端口
 * 官方文檔：https://wiki.swoole.com/wiki/page/14.html
 */
$ws = new swoole_websocket_server("0.0.0.0", 8443);

/*
 * 建立連接 $ws 伺服器
 * $ws：伺服器
 * $request：客戶端訊息
 */
$ws->on('open', function ($ws, $request) {
    var_export($request->data);

    $msg['name'] = $request->get['id'];
    $msg['event'] = 'other';
    $msg['content'] = 'login';
    $ws->push($request->fd, json_encode($msg, 256));
});

/*
 * 接收訊息
 */
$ws->on('message', function ($ws, $request) {
    echo "Message: {$request->data}\n";
    $m = json_decode($request->data, true);

    if ($m['event'] == "race" and $m['startRaceWs']) {
        echo "準備產生資料\n";

        $cars[1]['id'] = 1;
        $cars[2]['id'] = 2;
        $cars[3]['id'] = 3;
        $cars[4]['id'] = 4;
        $cars[5]['id'] = 5;
        $cars[6]['id'] = 6;

        $cars[1]['bottom'] = getBottom();
        $cars[2]['bottom'] = getBottom();
        $cars[3]['bottom'] = getBottom();
        $cars[4]['bottom'] = getBottom();
        $cars[5]['bottom'] = getBottom();
        $cars[6]['bottom'] = getBottom();

        $m['cars'] = $cars;
        $m['roadY'] = getRoadPosition();
    }

    $data = json_encode($m, 256);
    echo "$data\n";
    // 群發
    foreach ($ws->connections as $conn) {
        $ws->push($conn, $data);
    }

    // $ws->push($request->fd, $request->data);
});

//關閉連接
$ws->on('close', function ($ws, $request) {
    echo "close\n";
});

//啟動服務器
$ws->start();

function getBottom($times = 200, $value = 160, $max = 500)
{
    $bottom = [];
    for ($i = 0; $i < $times; $i++) {
        if ($value < 0) {
            $value = 0;
        } else if ($value > $max) {
            $value = $max;
        } else {
            $value += rand(-14, 15);
        }
        $bottom[] = $value;
    }
    return $bottom;
};

function getRoadPosition($times = 200, $value = 60)
{
    $RoadPosition = [];
    for ($i = 0; $i < $times; $i++) {
        $RoadPosition[] = $value * $i;
    }
    return $RoadPosition;
};
