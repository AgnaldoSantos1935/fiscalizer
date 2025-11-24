<?php

namespace App\Http\Controllers;

use App\Models\NotificationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class NotificationEventController extends Controller
{
    public function __construct()
    {
        // Restringe gestão de eventos de notificação a administradores/perfis autorizados
        $this->middleware('can.action:system.admin');
    }

    public function index()
    {
        // Defesa adicional (caso middleware seja alterado nas rotas)
        Gate::authorize('system.admin');
        $events = NotificationEvent::orderBy('codigo')->paginate(15);
        $configEvents = array_keys(config('notification_events.events') ?? []);

        return view('notificacoes.admin.index', compact('events', 'configEvents'));
    }

    public function create()
    {
        Gate::authorize('system.admin');
        $roles = \App\Models\Role::orderBy('nome')->get();
        $users = \App\Models\User::orderBy('name')->limit(200)->get();
        $actions = \App\Models\Action::orderBy('modulo')->orderBy('nome')->get(['id', 'codigo', 'nome', 'modulo']);

        return view('notificacoes.admin.create', compact('roles', 'users', 'actions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('system.admin');
        $data = $request->validate([
            'codigo' => 'required|string|unique:notification_events,codigo',
            'title' => 'required|string',
            'message' => 'nullable|string',
            'channels' => 'nullable|array',
            'enabled' => 'nullable|boolean',
            'priority' => 'nullable|string|in:low,normal,high,critical',
            'recipient_scope' => 'nullable|string|in:intersection,rbac,context,all,roles,users',
            'recipient_roles' => 'nullable|array',
            'recipient_users' => 'nullable|array',
            'should_generate' => 'nullable|boolean',
            'rules' => 'nullable',
            'workflow' => 'nullable',
        ]);

        $data['dominio'] = $this->extractDomain($data['codigo']);
        // Normaliza arrays e JSON
        $data['recipient_roles'] = array_values(array_filter((array) ($data['recipient_roles'] ?? [])));
        $data['recipient_users'] = array_values(array_filter((array) ($data['recipient_users'] ?? [])));
        foreach (['rules', 'workflow'] as $jsonField) {
            $val = $data[$jsonField] ?? null;
            if (is_string($val) && trim($val) !== '') {
                try {
                    $decoded = json_decode($val, true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($decoded)) {
                        $data[$jsonField] = $decoded;
                    }
                } catch (\Throwable $e) {
                    // mantém string original se JSON inválido
                }
            }
        }
        // Valida workflow estruturado (se array)
        if (is_array($data['workflow'] ?? null)) {
            // Normaliza notify para booleano
            $data['workflow'] = array_values(array_map(function ($step) {
                if (isset($step['notify'])) {
                    $step['notify'] = (bool) $step['notify'];
                }

                return $step;
            }, $data['workflow']));
            $wfValidator = Validator::make(
                ['workflow' => $data['workflow']],
                [
                    'workflow' => 'array',
                    'workflow.*.step' => 'required|integer|min:1',
                    'workflow.*.action' => 'required|string',
                    'workflow.*.priority' => 'nullable|string|in:low,normal,high,critical',
                    'workflow.*.responsible' => 'nullable|string',
                ],
                [
                    'workflow.*.step.required' => 'Em cada etapa, o campo Etapa é obrigatório.',
                    'workflow.*.step.integer' => 'Etapa deve ser um número inteiro.',
                    'workflow.*.step.min' => 'Etapa deve ser pelo menos 1.',
                    'workflow.*.action.required' => 'Em cada etapa, informe a Ação (ex.: medicoes.validar_pf).',
                    'workflow.*.priority.in' => 'Prioridade deve ser uma de low|normal|high|critical.',
                ]
            );
            if ($wfValidator->fails()) {
                return back()->withErrors($wfValidator)->withInput();
            }
        }
        NotificationEvent::create($data);

        return redirect()->route('admin.notificacoes.index')->with('success', 'Evento criado com sucesso.');
    }

    public function edit(NotificationEvent $evento)
    {
        Gate::authorize('system.admin');
        $roles = \App\Models\Role::orderBy('nome')->get();
        $users = \App\Models\User::orderBy('name')->limit(200)->get();
        $actions = \App\Models\Action::orderBy('modulo')->orderBy('nome')->get(['id', 'codigo', 'nome', 'modulo']);

        return view('notificacoes.admin.edit', ['evento' => $evento, 'roles' => $roles, 'users' => $users, 'actions' => $actions]);
    }

    public function update(Request $request, NotificationEvent $evento)
    {
        Gate::authorize('system.admin');
        $data = $request->validate([
            'title' => 'required|string',
            'message' => 'nullable|string',
            'channels' => 'nullable|array',
            'enabled' => 'nullable|boolean',
            'priority' => 'nullable|string|in:low,normal,high,critical',
            'recipient_scope' => 'nullable|string|in:intersection,rbac,context,all,roles,users',
            'recipient_roles' => 'nullable|array',
            'recipient_users' => 'nullable|array',
            'should_generate' => 'nullable|boolean',
            'rules' => 'nullable',
            'workflow' => 'nullable',
        ]);
        // Normaliza arrays e JSON
        $data['recipient_roles'] = array_values(array_filter((array) ($data['recipient_roles'] ?? [])));
        $data['recipient_users'] = array_values(array_filter((array) ($data['recipient_users'] ?? [])));
        foreach (['rules', 'workflow'] as $jsonField) {
            $val = $data[$jsonField] ?? null;
            if (is_string($val) && trim($val) !== '') {
                try {
                    $decoded = json_decode($val, true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($decoded)) {
                        $data[$jsonField] = $decoded;
                    }
                } catch (\Throwable $e) {
                    // mantém string original se JSON inválido
                }
            }
        }
        // Valida workflow estruturado (se array)
        if (is_array($data['workflow'] ?? null)) {
            $data['workflow'] = array_values(array_map(function ($step) {
                if (isset($step['notify'])) {
                    $step['notify'] = (bool) $step['notify'];
                }

                return $step;
            }, $data['workflow']));
            $wfValidator = Validator::make(
                ['workflow' => $data['workflow']],
                [
                    'workflow' => 'array',
                    'workflow.*.step' => 'required|integer|min:1',
                    'workflow.*.action' => 'required|string',
                    'workflow.*.priority' => 'nullable|string|in:low,normal,high,critical',
                    'workflow.*.responsible' => 'nullable|string',
                ],
                [
                    'workflow.*.step.required' => 'Em cada etapa, o campo Etapa é obrigatório.',
                    'workflow.*.step.integer' => 'Etapa deve ser um número inteiro.',
                    'workflow.*.step.min' => 'Etapa deve ser pelo menos 1.',
                    'workflow.*.action.required' => 'Em cada etapa, informe a Ação (ex.: medicoes.validar_pf).',
                    'workflow.*.priority.in' => 'Prioridade deve ser uma de low|normal|high|critical.',
                ]
            );
            if ($wfValidator->fails()) {
                return back()->withErrors($wfValidator)->withInput();
            }
        }
        $evento->update($data);

        return redirect()->route('admin.notificacoes.index')->with('success', 'Evento atualizado.');
    }

    public function show(NotificationEvent $evento)
    {
        Gate::authorize('system.admin');

        return view('notificacoes.admin.show', compact('evento'));
    }

    public function destroy(NotificationEvent $evento)
    {
        Gate::authorize('system.admin');
        $evento->delete();

        return redirect()->route('admin.notificacoes.index')->with('success', 'Evento removido.');
    }

    public function importFromConfig(Request $request)
    {
        Gate::authorize('system.admin');
        $defs = config('notification_events.events') ?? [];
        $count = 0;
        foreach ($defs as $codigo => $def) {
            if (! NotificationEvent::where('codigo', $codigo)->exists()) {
                NotificationEvent::create([
                    'codigo' => $codigo,
                    'dominio' => $this->extractDomain($codigo),
                    'title' => $def['title'] ?? $codigo,
                    'message' => $def['message'] ?? null,
                    'channels' => $def['channels'] ?? ['database'],
                    'enabled' => $def['enabled'] ?? true,
                    'priority' => $def['priority'] ?? 'normal',
                    'recipient_scope' => $def['recipient_scope'] ?? 'intersection',
                    'should_generate' => $def['should_generate'] ?? true,
                    'rules' => $def['rules'] ?? null,
                    'workflow' => $def['workflow'] ?? null,
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.notificacoes.index')->with('success', "Importados $count eventos do config.");
    }

    public function syncActions(Request $request)
    {
        Gate::authorize('system.admin');
        Artisan::call('notificacoes:sync', [
            '--wildcards' => true,
        ]);

        return redirect()->route('admin.notificacoes.index')->with('success', 'Actions sincronizadas com sucesso.');
    }

    public function searchUsers(Request $request)
    {
        Gate::authorize('system.admin');
        $q = trim((string) $request->get('q', ''));
        $query = \App\Models\User::query();
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
                if (is_numeric($q)) {
                    $sub->orWhere('id', (int) $q);
                }
            });
        }
        $users = $query->orderBy('name')->limit(20)->get(['id', 'name', 'email']);

        return response()->json($users->map(function ($u) {
            return [
                'id' => $u->id,
                'text' => trim(($u->name ?? ('User #' . $u->id)) . ($u->email ? ' (' . $u->email . ')' : '')),
                'name' => $u->name,
                'email' => $u->email,
            ];
        }));
    }

    private function extractDomain(string $codigo): ?string
    {
        $parts = explode('.', $codigo);

        return count($parts) >= 3 && $parts[0] === 'notificacoes' ? $parts[1] : null;
    }
}
