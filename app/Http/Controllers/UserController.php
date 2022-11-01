<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index() {
        // return User::get();

        $users = User::query();

        // $users->with(['roles' => function($q){ $q->with('permissions'); }]);

        if (request()->has('search')) {
            $users->whereColumnContains(request('search'));
        }

        if (request()->has('order_by')) {
            [$column, $sort] = explode(",", request('order_by'));
            $users->orderBy($column, $sort);
        } else {
            $users->orderBy('created_at', 'DESC');
        }

        return request()->has('page') ?
            $users->paginate(intval(request('rows')))->appends(request()->all()) :
            $users->get();
    }
}
