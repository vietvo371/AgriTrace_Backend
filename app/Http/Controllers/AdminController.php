<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct($isHaveUser = true)
    {
        $this->isHaveUser = $isHaveUser;
        $this->guard      = 'admin';
    }

    public function checkRule_get($id)
    {
        // $admin       = $this->admin();
        // if($admin->is_master){
        //     return true;
        // }
        // $groupAdmin  = Position::find($admin->position_id);
        // $listRule    = explode(',', $groupAdmin->list_rule);

        // return in_array($id, $listRule);
    }

    public function checkRule_post($id)
    {
        // $admin       = $this->admin();
        // if($admin->is_master){
        //     return true;
        // }
        // $groupAdmin  = Position::find($admin->position_id);
        // $listRule    = explode(',', $groupAdmin->list_rule);

        // return in_array($id, $listRule);
    }

    public function checkIsMaster()
    {
        $admin = Auth::guard('admin')->user();
        if($admin->is_master){
            return true;
        }
        toastr()->error('Bạn không có quyền truy cập vào trang này');
        throw new CustomException('Bạn không có quyền truy cập vào trang này');
    }
}
