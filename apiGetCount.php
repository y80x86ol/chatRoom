<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * 统计接口
 */
require_once 'packages/phpfastcache/autoload.php';

use phpFastCache\CacheManager;

$cache = CacheManager::Files();

$user_list = $cache->get('user_list');
$room_count = $cache->get('room_count');
$user_list = json_decode($user_list, true);
$room_count = json_decode($room_count, true);

$room = $_GET['room'];


//处理用户列表
foreach ($user_list[$room] as $key => $item) {
    $user_list[$room][$key]['online_time'] = intval((time() - $item['login_time']) / 60) . '分钟';
}
echo json_encode(array(
    'error' => '0',
    'room_count' => $room_count[$room],
    'user_list' => array_values($user_list[$room]),
));
