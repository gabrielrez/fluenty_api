<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return $this->respond(new UserResource($request->user()));
    }

    public function update(UserUpdateRequest $request)
    {
        $request->user()->update($request->validatedForUpdate());

        return $this->respond(new UserResource($request->user()->fresh()));
    }
}
