<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/8
 * Time: 16:15
 */

function showMsg($status, $message = '', $data = array())
{
    $result = array(
        'status' => $status,
        'message' => $message,
        'data' => $data
    );
    exit(json_encode($result));
}
/*
 * 401=token不正确
 * 422=登录失败
 * 423=google验证失败
 * */
function returnJson($status, $message = '', $data = array())
{
    if ($status == 200 && empty($message)) {
        $message = 'success';
    } elseif ($status != 200 && empty($message)) {
        $message = 'fail';
    }
    $result = array(
        'status' => $status,
        'message' => $message,
        'data' => $data
    );
    return $result;
}