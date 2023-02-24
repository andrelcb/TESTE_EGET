<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\TasksRequest;
use App\Jobs\AssignTask;
use App\Jobs\taskComplete as JobsTaskComplete;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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

            $tasks = $this->tasks->create($data);

            if (isset($data['users']) && count($data['users'])) {
                $tasks->user()->sync($data['users']);
            }

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

            if (isset($data['users']) && count($data['users'])) {
                $tasks->user()->sync($data['users']);
            }
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

    public function Users($id)
    {
        try {
            $tasks = $this->tasks->findOrfail($id);
            return response()->json([
                'data' => $tasks->user
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function toAssignTask($id, Request $request)
    {
        $data = $request->all();
        Validator::make($data, [
            'users' => 'required',
        ])->validate();

        try {
            $tasks = $this->tasks->findOrFail($id);

            if (isset($data['users']) && count($data['users'])) {
                if (!$tasks->user()->wherePivot('users_idusers', $data['users'])->wherePivot('tasks_idtasks', $tasks->id)->exists()) {
                    $tasks->user()->attach($data['users']);
                    foreach ($tasks->user as $value) {
                        AssignTask::dispatch($tasks, $value);
                    }
                } else {
                    $message = new ApiMessages('A tarefa ja está atribuida ao usuario.');
                    return response()->json($message->getMessage(), 401);
                }
            }
            return response()->json([
                'data' => [
                    'msg' => 'Tarefa atribuida com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function changeStatusTask($id, Request $request)
    {
        $data = $request->all();
        Validator::make($data, [
            'status' => 'required |string|min:2',
        ])->validate();

        try {
            $tasks = $this->tasks->findOrFail($id);
            $tasks->status = $data['status']; // EM, CO, CA
            $tasks->update();

            if ($data['status'] == 'CO') {
                foreach ($tasks->user as $value) {
                    JobsTaskComplete::dispatch($tasks, $value);
                }
            }

            return response()->json([
                'data' => [
                    'msg' => 'Tarefa concluida com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
