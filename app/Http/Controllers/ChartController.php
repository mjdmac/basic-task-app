<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\User;


class ChartController extends Controller
{
    protected $users;
    protected $tasks;

    public function __construct(User $users, Task $tasks)
    {
        $this->users = $users;
        $this->tasks = $tasks;
    }


    public function getData()
    {

        $rows = $this->tasks->join('users', 'tasks.created_by', '=', 'users.id')
                            ->select(\DB::raw('users.name as label, count(tasks.status) as data'))
                            ->where('tasks.status', 'Complete')
                            ->groupBy('users.name')
                            ->get();

        $data = [];
        $labels = [];
        $colors = [];


        foreach($rows as $row){
            array_push($data, $row->data);
            array_push($labels, $row->label);
            if(!in_array($this->pickColor(), $colors)){
                array_push($colors, $this->pickColor());
            }

        }


        $filtered = [
                        'labels' => $labels,
                        'data' => $data,
                        'colors' => $colors
                    ];

        return $filtered;
    }

    public function renderChart()
    {
        // return $this->getData();
       return Inertia::render('Task/Chart', ['data' => $this->getData()]);
    }

    public function pickColor()
    {
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        return $color;
    }
}
