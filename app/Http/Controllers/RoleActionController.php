<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleActionController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('system.admin');

        $roles = Role::orderBy('nome')->get();
        $selectedRoleId = (int) ($request->query('role_id') ?? ($roles->first()->id ?? 0));
        $selectedRole = $selectedRoleId ? Role::with('actions')->find($selectedRoleId) : null;

        $actions = Action::orderBy('modulo')->orderBy('nome')->get()
            ->groupBy('modulo');

        return view('rbac.roles_actions', compact('roles', 'selectedRole', 'actions'));
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('system.admin');

        $ids = collect($request->input('actions', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $role->actions()->sync($ids);

        return redirect()
            ->route('rbac.roles_actions.index', ['role_id' => $role->id])
            ->with('success', 'Permissões atualizadas com sucesso.');
    }
}
