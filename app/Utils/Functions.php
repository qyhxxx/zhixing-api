<?php

namespace App\Utils;

class Functions {
    static function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, getcwd().'/cacert.pem');
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return json_decode($res);
    }

    static function generateAdmin() {
        $name = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 4; $i++) {
            $name .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        $password = bcrypt('123456');
        return [
            'name' => $name,
            'password' => $password,
            'authority' => 1
        ];
    }
}