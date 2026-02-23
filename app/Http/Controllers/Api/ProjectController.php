<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // If customer, return their assigned projects (via customer_id in projects table)
        if ($user->isCustomer()) {
            $projects = Project::where('customer_id', $user->id)
                ->where('is_active', true)
                ->get();
        } else {
            // For developers, return projects they're members of
            if ($user->isDeveloper()) {
                $projects = $user->projects()->where('is_active', true)->get();
            } else {
                // For admins, return all active projects
                $projects = Project::where('is_active', true)->get();
            }
        }
        
        return response()->json($projects);
    }

    public function show(Project $project)
    {
        $project->load('members');
        return response()->json($project);
    }

    public function members(Project $project)
    {
        $members = $project->members()->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'role' => $member->pivot->role,
            ];
        });

        return response()->json([
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'members' => $members,
            'frontend_developer' => $project->frontendDeveloper(),
            'backend_developer' => $project->backendDeveloper(),
            'server_admin' => $project->serverAdmin(),
        ]);
    }
}
