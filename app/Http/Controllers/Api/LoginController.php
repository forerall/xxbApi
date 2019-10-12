<?php

namespace App\Http\Controllers\Api;

use App\Models\AdminBindMfa;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    private $user;



    public function login(Request $request)
    {

        $userName = $request->input('userName');
        $password = $request->input('password');
        $user = User::where('userName', $userName)->first();
        if ($user && Hash::check($password, $user->password)) {
            $this->user = $user;
            $data['token'] = $user->generateToken();
            $data['checkMfa'] = $this->checkMfa();
            if (!$data['checkMfa']) {
                $arr = $this->createCode();
                $data['secret'] = $arr['secret'];
                $data['pic'] = $arr['pic'];
        }
            return returnJson(200, '登录成功', $data);
        } else {
            return returnJson(422, '登录失败');
        }
    }

    //判断数据库是否有Mfa数据
    private function checkMfa()
    {
        if ($this->user) {
            $adminId = $this->user->adminId;
            $fmaList = AdminBindMfa::where('adminId', $adminId)->first();
            if ($fmaList) {
                return true;
            }
        }
        return false;
    }
    //新建二维码图片
    private function createCode()
    {
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $qrCode = $ga->createSecret();
        $userName = $this->user ? $this->user->userName : '';
        $message = 'xxbAdmin' . $userName;
        $str = 'otpauth://totp/' . $message . '?secret=' . $qrCode . '';
        $qrCodePic = new \QrCodeHelpers($str);
        $arr['pic'] = $qrCodePic->getBase64Pic();
        $arr['secret'] = $qrCode;
        return $arr;
    }

    //验证二维码登录
    public function verifyMfa(Request $request)
    {
        $securityCode = $request->input('securityCode', '');
        $secret = $request->input('secret', '');
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $secretList = AdminBindMfa::where('adminId', Auth::user()->adminId)->first();
        if ($secretList) {
            $secret = $secretList->secret;
        }
        $checkResult = $ga->verifyCode($secret, $securityCode);
        if ($checkResult) {
            if (!$secretList) {
                $adminBindMfa = new AdminBindMfa();
                $adminBindMfa->adminId = Auth::id();
                $adminBindMfa->status = 1;
                $adminBindMfa->ctime = date('Y-m-d H:i:s');
                $adminBindMfa->secret = $secret;
                $adminBindMfa->save();
            }
            return returnJson(200, 'goolge验证成功');
        } else {
            return returnJson(423, 'goolge验证失败');
        }
    }

    public function logout()
    {
        if (Auth::user()) {
            Auth::user()->api_token = null;
            Auth::user()->save();
        }
        return returnJson(200, '注销成功');
    }

}
