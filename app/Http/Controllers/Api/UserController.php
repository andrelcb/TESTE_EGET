<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function index()
    {
        $tasks = $this->user->paginate('10');

        return response()->json($tasks, 200);
    }

    public function show($id)
    {
        try {
            $tasks = $this->user->findOrFail($id);
            return response()->json([
                'data' => $tasks
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function store(UserRequest $request)
    {
        $data = $request->all();

        try {
            $data['password'] = bcrypt($data['password']);
            $this->user->create($data);

            return response()->json([
                'data' => [
                    'msg' => 'Usuário cadastrada com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }


    public function update($id, UserRequest $request)
    {
        $data = $request->all();
        try {
            $tasks = $this->user->findOrFail($id);
            $tasks->update($data);
            return response()->json([
                'data' => [
                    'msg' => 'Usuário atualizado com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function destroy($id)
    {
        try {

            $tasks = $this->user->findOrFail($id);
            $tasks->delete();
            return response()->json([
                'data' => [
                    'msg' => 'Usuário removido com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function Tasks($id)
    {
        try {
            $user = $this->user->findOrfail($id);
            return response()->json([
                'data' => $user->tasks
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
