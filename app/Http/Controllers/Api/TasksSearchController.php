<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Repository\TasksRepository;
use Illuminate\Http\Request;

class TasksSearchController extends Controller
{
    private $tasks;

    public function __construct(Tasks $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $repository = new TasksRepository($this->tasks);

        if ($request->has('coditions')) {
            $repository->selectCoditions($request->get('coditions'));
        }

        if ($request->has('fields')) {
            $repository->selectFilter($request->get('fields'));
        }
        return response()->json($repository->getResult()->paginate('10'), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
