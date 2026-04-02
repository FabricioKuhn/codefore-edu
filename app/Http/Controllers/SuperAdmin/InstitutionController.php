namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstitutionController extends Controller
{
    public function index()
    {
        // Lista todas as instituições paginadas
        $institutions = Institution::latest()->paginate(10);
        return view('superadmin.institutions.index', compact('institutions'));
    }

    public function create()
    {
        return view('superadmin.institutions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trading_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|unique:institutions,cnpj',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            
            // Cores White Label
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'tertiary_color' => 'nullable|string',
            
            // Imagens
            'logo_original' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'logo_negative' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'flat_icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
        ]);

        // Lógica de Upload de Arquivos
        if ($request->hasFile('logo_original')) {
            $validated['logo_original'] = $request->file('logo_original')->store('logos', 'public');
        }
        if ($request->hasFile('logo_negative')) {
            $validated['logo_negative'] = $request->file('logo_negative')->store('logos', 'public');
        }
        if ($request->hasFile('flat_icon')) {
            $validated['flat_icon'] = $request->file('flat_icon')->store('logos', 'public');
        }

        Institution::create($validated);

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit(Institution $institution)
    {
        return view('superadmin.institutions.edit', compact('institution'));
    }

    // O método update seria muito parecido com o store, atualizando os dados e substituindo as logos se enviadas.
}