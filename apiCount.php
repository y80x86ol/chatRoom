<?php

/*
 * 统计接口
 */
require_once 'packages/phpfastcache/autoload.php';

use phpFastCache\CacheManager;

$cache = CacheManager::Files();

$type = $_POST['type'];
$room = $_POST['room'];
$uid = $_POST['uid'];
$username = $_POST['username'];
$avatar = $_POST['avatar'];
$login_time = $_POST['login_time'];


switch ($type) {
    case 'connect':
        $user_list = $cache->get('user_list');
        $room_count = $cache->get('room_count');
        $user_list = json_decode($user_list, true);
        $room_count = json_decode($room_count, true);

        if (is_null($user_list)) {
            $user_list = array();
        }
        if (is_null($room_count)) {
            $room_count = array();
            $room_count[$room] = 0;
        }


        $user_list[$room][$uid] = array(
            'uid' => $uid,
            'username' => $username,
            'avatar' => $avatar,
            'login_time' => $login_time
        );
        $room_count[$room] ++;

        $cache->set('user_list', json_encode($user_list));
        $cache->set('room_count', json_encode($room_count));

        break;
    case 'logout':

        $user_list = $cache->get('user_list');
        $room_count = $cache->get('room_count');

        $user_list = json_decode($user_list, true);
        $room_count = json_decode($room_count, true);

        unset($user_list[$room][$uid]);
        $room_count[$room] --;

        $cache->set('user_list', json_encode($user_list));
        $cache->set('room_count', json_encode($room_count));

        break;
    case 'clear':
        $cache->set('user_list', '');
        $cache->set('room_count', '');
        break;
    default:
        break;
}

echo 'hello';
