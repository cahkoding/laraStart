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
use Illuminate\Support\Facades\File;

class UserController extends Controller
{

    public function __construct () {
            $this->middleware('auth:api');
            // $this->authorize('isAdmin'); // jika di construct akan kena semua
    }

    public function index()
    {
        $this->authorize('isAdmin');
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
        $this->authorize('isAdmin'); //param2 bisa dipake untuk compare request body
        $user = User::findOrFail($id);
        $user->delete();

        return ["message" => "User $user->name Deleted."];
    }

    public function updateProfile(UserRequest $req, $id)
    {
        $user = User::findOrFail($id);
        // dd($req->toArray());

        // cara pertama
        // $image = str_replace('data:image/png;base64,', '', $req->photo);
        // $image = str_replace(' ', '+', $image);
        // $imageName = str_random(10).'.'.'png';
        // \File::put(storage_path(). '/upload/profile/' . $imageName, base64_decode($image));

        // cara kedua
        $currentPhoto = $user->photo;
        $imageName = $req->photo;
        if ($req->base64) {
            $path = public_path('img/profile/');
            $ext =  explode('/', explode(':', substr($req->base64, 0, strpos($req->base64, ';')))[1])[1];
            $imageName = time().'.'.$ext;

            // destroy old photo
            unlink($path.$currentPhoto);

            File::exists($path) or File::makeDirectory($path, 0777, true, true);
            Image::make($req->base64)->save($path.$imageName);
        }

        if ($req->password) {
            $req->merge(['password' => Hash::make($req->password)]);
        }

        $req->merge(['photo' => $imageName]);
        $res =  $user->update($req->all());
        
        return ["message" => "User updated"];
    }
}
