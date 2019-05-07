<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserItem;
use App\Http\Resources\UserCollection;
use Intervention\Image\Facades\Image as Image;

class UserController extends Controller
{

    public function __construct () {
            $this->middleware('auth:api');
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

    public function updateProfile(UserRequest $req, $id)
    {
        $post = $req->toArray();
        $user = User::findOrFail($id);

        // cara pertama
        // $image = str_replace('data:image/png;base64,', '', $req->photo);
        // $image = str_replace(' ', '+', $image);
        // $imageName = str_random(10).'.'.'png';
        // \File::put(storage_path(). '/upload/profile/' . $imageName, base64_decode($image));

        // cara kedua
        if ($req->base64) {
            $imageName = time().'.' . explode('/', explode(':', substr($req->base64, 0, strpos($req->base64, ';')))[1])[1];

            Image::make($req->base64)->save(public_path('img/profile/').$imageName);
        }

        $post['photo'] = $imageName;
        // dd($post);
        $res =  $user->update($post);

        return ["message" => "User updated"];
    }
}
