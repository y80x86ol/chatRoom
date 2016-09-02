<?php

/*
 * 请求类
 */

namespace chart\request;

class chartRequest {

    public static function handle($data_json) {
        $data = json_decode(trim($data_json), true);
        if (!isset($data['room']) || !isset($data['type']) || !isset($data['user']) || !isset($data['msg'])) {
            return false;
        }
        if (empty($data['room']) || empty($data['user']) || empty($data['msg'])) {
            return false;
        }

        switch ($data['type']) {
            case 'connect':
                $new_data = $data;
                break;
            case 'msg':
                $new_data = $data;
                break;
            case 'info':
                $new_data = $data;
                break;
            case 'notice':
                $new_data = $data;
                break;
            case 'default':
                $new_data = $data;
        }

        //数据过滤
        $new_data['msg'] = self::filter($data['msg']);

        return $new_data;
    }

    public static function filter($data) {
        return trim(htmlspecialchars($data));
    }

}
