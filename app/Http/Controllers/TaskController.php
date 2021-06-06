<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Banner;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{
    use Banner;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = request()->get('status');

        $data = Task::with('subtasks')
                ->where('created_by', auth()->user()->id)
                ->whereNull('parent')
                ->where(function($q) use ($status) {
                    if(request()->has('status')){
                        $q->where('status', $status);
                    }
                })
                ->reorder('order')
                ->get();

        return Inertia::render('Task/Task', ['data' => $data]);
    }

    public function allTasks()
    {
        $data = Task::with('owner')
                    ->whereNull('parent')
                    ->latest()
                    ->get();

        return Inertia::render('Dashboard', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val = Validator::make($request->all(), [
                            'name' => 'required',
                        ]);

        if ($val->fails()) {
            $this->flash($val->errors()->first(), 'danger');
            return back();
        }

        Task::create($request->all());

        $this->flash('Task created', 'success');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $status = request()->get('status');
        if(request()->has('status')){
           $task->subtasks = Task::with('subtasks')
                        ->where('parent', $task->id)
                        ->where('status', $status)
                        ->orderBy('order')
                        ->get();
        }else{
            $task->subtasks;
        }

        return Inertia::render('Task/Task', ['data' => $task]);
    }

    public function singleTask($id)
    {
        $data = Task::with('owner')
                    ->where('id', $id)
                    ->latest()
                    ->first();

        foreach($data->subtasks as $task){
            $task->owner;
        }

        return Inertia::render('Dashboard', ['data' => $data ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {

        $val = Validator::make($request->all(), [
                        'name' => 'required',
                    ]);

        if ($val->fails()) {
            $this->flash($val->errors()->first(), 'danger');
            return back();
        }
        $task->update($request->all());

        if($request->get('status') != 'Custom'){
            $task->update(['custom_status' => null]);
        }

        $this->flash('Task updated', 'success');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

         $d = Task::find($id);
         $d->delete();

         $this->flash('Task deleted', 'success');

        return redirect()->back();

    }

    public function recycleBin()
    {
        $data = Task::where('created_by', auth()->user()->id)
                    ->onlyTrashed()
                    ->orderBy('deleted_at', 'desc')
                    ->get();

        return Inertia::render('Task/RecycleBin', ['data' => $data ]);
    }

    public function restoreTask($id)
    {
        $task = Task::withTrashed()->find($id);

        $task->restore();

        $this->flash('Task restored', 'success');

        return redirect()->back();
    }

    public function orderByDrag(Request $request)
    {
        $task = Task::find($request->get('item'));
        $task->update(['order' => $request->get('order')]);

        return back();
    }

    public function exportTasks()
    {
        if(request()->has('type') ){
            if(request()->get('type') == 'xlsx'){

                return Excel::download(new TasksExport, auth()->user()->name.'-tasks.xlsx');

            }elseif(request()->get('type') == 'csv'){

                return Excel::download(new TasksExport, auth()->user()->name.'-tasks.csv');

            }elseif(request()->get('type') == 'json'){

                $model = new TasksExport;
                $data = $model->collection();
                $filename = auth()->user()->name.'-tasks.json';
                $handle = fopen($filename, 'w+');
                fputs($handle, $data->toJson(JSON_PRETTY_PRINT));
                fclose($handle);
                $headers = array('Content-type'=> 'application/json');
                return response()->download($filename, $filename, $headers);
            }
        }

        return back();

    }
}
