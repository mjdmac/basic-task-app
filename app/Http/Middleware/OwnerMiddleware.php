<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class OwnerMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = $request->segments()[1];
        $task = Task::find($id);

        if ($task->created_by !== $this->auth->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
