<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\TasksRequest;
use App\Models\Tasks;

class TasksController extends Controller
{
    private $tasks;

    public function __construct(Tasks $tasks)
    {
        $this->tasks = $tasks;
    }

    public function index()
    {
        $tasks = $this->tasks->paginate('10');

        return response()->json($tasks, 200);
    }

    public function show($id)
    {
        try {
            $tasks = $this->tasks->findOrFail($id);
            return response()->json([
                'data' => $tasks
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function store(TasksRequest $request)
    {
        $data = $request->all();
        try {

            $this->tasks->create($data);
            return response()->json([
                'data' => [
                    'msg' => 'Tarefa cadastrada com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }


    public function update($id, TasksRequest $request)
    {
        $data = $request->all();
        try {
            $tasks = $this->tasks->findOrFail($id);
            $tasks->update($data);
            return response()->json([
                'data' => [
                    'msg' => 'Tarefa atualizado com sucesso!'
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

            $tasks = $this->tasks->findOrFail($id);
            $tasks->delete();
            return response()->json([
                'data' => [
                    'msg' => 'Tarefa removida com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
