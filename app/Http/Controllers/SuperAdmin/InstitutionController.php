<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Plan; // <-- IMPORTANTE: Adicionado para não dar erro no edit/store
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $institutions = Institution::query()
            ->when($search, function ($query, $search) {
                $query->where('trading_name', 'like', "%{$search}%")
                      ->orWhere('cnpj', 'like', "%{$search}%");
            })
            ->withCount(['users as active_students' => function ($query) {
                $query->where('role', 'student')->where('is_active', true);
            }])
            ->latest()
            ->paginate(10);

        return view('superadmin.institutions.index', compact('institutions', 'search'));
    }

    public function create()
    {
        // Buscamos os planos para o caso de você querer escolher um no cadastro manual
        $plans = Plan::where('is_active', true)->orderBy('name')->get();
        return view('superadmin.institutions.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trading_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'domain'       => 'nullable|string|unique:institutions,domain',
            'cnpj'         => 'required|string|unique:institutions,cnpj',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string',
            
            // Endereço e Fiscal
            'ie_indicator'           => 'nullable|string',
            'state_registration'     => 'nullable|string',
            'municipal_registration' => 'nullable|string',
            'zip_code'               => 'nullable|string',
            'street'                 => 'nullable|string',
            'number'                 => 'nullable|string',
            'neighborhood'           => 'nullable|string',
            'city'                   => 'nullable|string',
            'state'                  => 'nullable|string|max:2',

            // Cores White Label
            'primary_color'   => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'tertiary_color'  => 'nullable|string',
            
            // Imagens
            'logo_original' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'logo_negative' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'flat_icon'     => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
            'plan_id'       => 'nullable|exists:plans,id',
        ]);

        // LOGICA: Se não veio plan_id, busca o plano marcado como "is_free"
        $defaultPlan = \App\Models\Plan::where('is_free', true)->first();
        $validated['plan_id'] = $request->plan_id ?? ($defaultPlan ? $defaultPlan->id : null);

        // Upload de Arquivos
        foreach (['logo_original', 'logo_negative', 'flat_icon'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('logos', 'public');
            }
        }

        // RESOLVENDO O ERRO 1364: Injetando o campo 'name' obrigatório no array que vai pro banco
        $validated['name'] = $request->trading_name; 

        $validated['slug'] = \Illuminate\Support\Str::slug($request->trading_name);

        Institution::create($validated);

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit(Institution $institution)
    {
        $plans = Plan::where('is_active', true)->orderBy('name')->get();
        return view('superadmin.institutions.edit', compact('institution', 'plans'));
    }

    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'trading_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'cnpj'         => 'required|string|unique:institutions,cnpj,' . $institution->id,
            'domain'       => 'nullable|string|unique:institutions,domain,' . $institution->id,
            'plan_id'      => 'nullable|exists:plans,id',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string',
            
            // Adicionado campos de endereço no update para não perdê-los na edição
            'zip_code'     => 'nullable|string',
            'street'       => 'nullable|string',
            'number'       => 'nullable|string',
            'neighborhood' => 'nullable|string',
            'city'         => 'nullable|string',
            'state'        => 'nullable|string|max:2',

            // Cores
            'primary_color'   => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'tertiary_color'  => 'nullable|string',

            'logo_original' => 'nullable|image|max:2048',
            'logo_negative' => 'nullable|image|max:2048',
            'flat_icon'     => 'nullable|image|max:1024',
        ]);

        // Lógica de Imagens (Substituição)
        foreach (['logo_original', 'logo_negative', 'flat_icon'] as $field) {
            if ($request->hasFile($field)) {
                if ($institution->$field) {
                    Storage::disk('public')->delete($institution->$field);
                }
                
            }
        }

        $validated['name'] = $request->trading_name;

        $institution->update($validated);

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', 'Dados da instituição atualizados com sucesso!');
    }

    public function toggleStatus(Institution $institution)
    {
        $institution->status = !$institution->status;
        $institution->save();

        $statusTexto = $institution->status ? 'desbloqueada' : 'bloqueada';
        
        return redirect()->back()->with('success', "Instituição {$statusTexto} com sucesso!");
    }
}