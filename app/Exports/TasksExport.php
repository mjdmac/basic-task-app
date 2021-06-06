<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TasksExport implements FromCollection, WithEvents, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        // for MYSQL code
        // $data = Task::join('users', 'tasks.created_by', '=', 'users.id')
        // ->select(\DB::raw('users.name as User, tasks.id as Task_Id, tasks.parent as Parent_Task_Id,
        //                    tasks.name as Task_Name, tasks.status as Status,
        //                    date_format(tasks.created_at, "%d %M %Y") as Date_Created,
        //                    date_format(tasks.deleted_at, "%d %M %Y") as Date_Deleted'))
        // ->where('tasks.created_by', auth()->user()->id)
        // ->orderBy('tasks.status')
        // ->withTrashed()
        // ->get();

        //for POSTGRESQL
        $data = Task::join('users', 'tasks.created_by', '=', 'users.id')
        ->select(\DB::raw('users.name as User, tasks.id as Task_Id, tasks.parent as Parent_Task_Id,
                           tasks.name as Task_Name, tasks.status as Status,
                           TO_CHAR(tasks.created_at, "DD/MM/YYYY") as Date_Created,
                           TO_CHAR(tasks.deleted_at, "DD/MM/YYYY") as Date_Deleted'))
        ->where('tasks.created_by', auth()->user()->id)
        ->orderBy('tasks.status')
        ->withTrashed()
        ->get();


        $data = $data->each(function ($d) {
            $d->setAppends([]);
          });

        return $data;
    }

    public function headings(): array
    {
        return ['User', 'Task Id', 'Parent Task Id',  'Task Name', 'Status', 'Date Created', 'Date Deleted'];
    }

    public function registerEvents(): array
    {
    return [
        AfterSheet::class  => function(AfterSheet $event) {
            $cellRange = 'A1:F1';
            $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(17);

        },
    ];
    }
}
