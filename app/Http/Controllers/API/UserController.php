<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserItem;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{

    public function __construct () {
            $this->middleware('api');
    }

    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(5);
        return new UserCollection($users);
    }

    public function store(UserRequest $req)
    {

        $params = array(
            'name' => $req->name,
            'email' => $req->email,
            'type' => $req->type,
            'bio' => $req->bio,
            'password' => Hash::make($req->password)
        );

        return User::create($params);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserItem($user);
    }

    public function update(UserRequest $req, $id)
    {
        $user = User::findOrFail($id);
        $res =  $user->update($req->all());

        return ["message" => "User updated"];
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return ["message" => "User $user->name Deleted."];
    }
}
