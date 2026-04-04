<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    // Listagem com contagem de instituições
    public function index()
    {
        $plans = Plan::withCount('institutions')
                     ->orderBy('price_monthly', 'asc')
                     ->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('superadmin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric',
            'price_yearly' => 'required|numeric',
            'limit_classes' => 'nullable|integer',
            'limit_students_per_class' => 'nullable|integer',
            'limit_tasks_per_class' => 'nullable|integer',
        ]);

        $data['slug'] = Str::slug($request->name);
        $data['is_free'] = $request->has('is_free');

        if ($data['is_free']) {
            Plan::where('is_free', true)->update(['is_free' => false]);
        }

        Plan::create($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric',
            'price_yearly' => 'required|numeric',
            'limit_classes' => 'nullable|integer',
            'limit_students_per_class' => 'nullable|integer',
            'limit_tasks_per_class' => 'nullable|integer',
        ]);

        $data['slug'] = Str::slug($request->name);
        $data['is_free'] = $request->has('is_free');

        // Trata o Ilimitado (vazio vira null no banco)
        $data['limit_classes'] = $request->filled('limit_classes') ? $request->limit_classes : null;
        $data['limit_students_per_class'] = $request->filled('limit_students_per_class') ? $request->limit_students_per_class : null;
        $data['limit_tasks_per_class'] = $request->filled('limit_tasks_per_class') ? $request->limit_tasks_per_class : null;

        if ($data['is_free']) {
            Plan::where('id', '!=', $plan->id)->where('is_free', true)->update(['is_free' => false]);
        }

        $plan->update($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plano atualizado!');
    }

    public function toggleStatus(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        $status = $plan->is_active ? 'ativado' : 'inativado';
        return back()->with('success', "O plano {$plan->name} foi {$status}!");
    }
}