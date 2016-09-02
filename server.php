<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials:true");
header("Content-type: text/html; charset=utf-8");

require_once 'libs/request.php';
require_once 'Workerman/Autoloader.php';
$room_config = require_once 'config/room.php';

use Workerman\Worker;
use chart\request\chartRequest;

$global_uid = 0;

//第一次进入清除所有缓存记录
apiCount('clear', 0, 0);

// 当客户端连上来时分配uid，并保存连接，并通知所有客户端
function handle_connection($connection) {
    global $text_worker, $global_uid;
    // 为这个链接分配一个uid
    $connection->uid = ++$global_uid;
    $connection->loginTime = time();
    echo 'connect:' . $connection->uid . '===========';
}

// 当客户端发送消息过来时，转发给所有人
function handle_message($connection, $data) {var_dump($data);
    //校验传入的数据是否正确
    $new_data = chartRequest::handle($data);
    if (!$new_data) {
        return false;
    }

    //增加数据
    $new_data['uid'] = $connection->uid;

    if ($new_data['type'] == 'connect') {
        $connection->roomNum = $new_data['room'];
        $connection->userName = $new_data['user'];
        apiCount('connect', $new_data['room'], $connection->uid, $new_data['user'], $new_data['avatar'] ? $new_data['avatar'] : '', $connection->loginTime);
    }
    //错误的脏数据
    if ($connection->roomNum != $new_data['room']) {
        return false;
    }
    global $text_worker;
    //数据正确，发送消息
    foreach ($text_worker->connections as $conn) {
        if ($conn->roomNum != $new_data['room']) {
            continue;
        }
        $conn->send(json_encode($new_data));
    }
}

// 当客户端断开时，广播给所有客户端
function handle_close($connection) {
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        if ($conn->roomNum != $connection->roomNum) {
            continue;
        }
        $data = array(
            'room' => $connection->roomNum,
            'type' => 'info',
            'user' => $connection->userName,
            'msg' => '系统消息：' . $connection->userName . '退出了聊天室'
        );
        $conn->send(json_encode($data));
    }
    apiCount('logout', $connection->roomNum, $connection->uid);
}

// 创建一个文本协议的Worker监听2347接口
$text_worker = new Worker("websocket://0.0.0.0:2345");

// 只启动1个进程，这样方便客户端之间传输数据
$text_worker->count = 1;

$text_worker->onConnect = 'handle_connection';
$text_worker->onMessage = 'handle_message';
$text_worker->onClose = 'handle_close';

Worker::runAll();

function apiCount($type, $room, $uid, $username = '', $avatar = '1', $login_time = '') {
    $url = "http://dev.test.com/chartRoom/apiCount.php";
    $post_params = array(
        "type" => $type,
        "room" => $room,
        "uid" => $uid,
        "username" => $username,
        "avatar" => $avatar,
        "login_time" => $login_time
    );
    $ch = \curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    //post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_params));
    curl_exec($ch);
    curl_close($ch);
}
