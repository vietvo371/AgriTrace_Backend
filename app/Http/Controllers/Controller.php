<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Traits\ViewJsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ViewJsonResponse;

    protected $isHaveUser;
    protected $guard;

    public function user()
    {
        if (!$this->isHaveUser) {
            return null;
        }

        $user = auth($this->guard)->user();

        if (empty($user)) {
            throw new CustomException('Vui lòng đăng nhập!');
        } else if ($user->is_block) {
            throw new CustomException('Tài khoản của bạn đã bị khóa!');
        }

        return $user;
    }

    public function admin()
    {
        if (!$this->isHaveUser) {
            return null;
        }

        $user = auth($this->guard)->user();

        if (empty($user)) {
            throw new CustomException('Vui lòng đăng nhập!');
        } else if ($user->is_block) {
            throw new CustomException('Tài khoản của bạn đã bị khóa!');
        }

        return $user;
    }
}
