<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\Cliente;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class IncidenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['publicCreateForm','publicStore']);
        $this->middleware('rol:admin')->only(['create','store','edit','update','destroy','index','show']);
    }

    /**
     * Index (admin)
     */
    public function index(Request $request)
    {
        $query = Incidencia::with('cliente','empleado');

        if ($q = $request->input('q')) {
            $query->where(function($qq) use ($q) {
                $qq->where('titulo','like',"%$q%")
                   ->orWhere('descripcion','like',"%$q%")
                   ->orWhere('contacto_nombre','like',"%$q%");
            });
        }
        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }
        if ($prov = $request->input('provincia')) {
            $query->where('provincia_codigo', $prov);
        }

        $incidencias = $query->orderBy('created_at','desc')->paginate(20);
        return view('incidencias.index', compact('incidencias'));
    }

    /**
     * Mostrar incidencia (opcional)
     */
    public function show(Incidencia $incidencia)
    {
        return view('incidencias.show', compact('incidencia'));
    }

    /**
     * Listado operario (mis incidencias)
     */
    public function misIncidencias()
    {
        $user = auth()->user();
        $incidencias = Incidencia::where('empleado_id', $user->id)
                        ->orderBy('created_at','desc')
                        ->paginate(20);
        return view('incidencias.index', compact('incidencias'));
    }

    /**
     * Form crear (admin)
     */
    public function create()
    {
        $provincias = method_exists($this,'provincias') ? $this->provincias() : \provincias_espana();
        $clientes = Cliente::orderBy('nombre')->get();
        return view('incidencias.create', compact('provincias','clientes'));
    }

    /**
     * Store (admin)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'cliente_id' => 'nullable|exists:clientes,id',
            'contacto_nombre' => 'required|string|max:255',
            'contacto_telefono' => ['required','string','max:30','regex:/^[0-9\-\+\s\(\)]{6,30}$/'],
            'contacto_email' => 'required|email',
            'direccion' => 'nullable|string|max:255',
            'poblacion' => 'nullable|string|max:100',
            'codigo_postal' => ['nullable','regex:/^\d{5}$/'],
            'provincia_codigo' => ['nullable','digits:2'],
            'fecha_realizacion' => ['nullable','date_format:d/m/Y'],
            'fichero_resumen' => 'nullable|file|max:10240',
            'empleado_id' => 'nullable|exists:empleados,id',
            'estado' => 'nullable|string'
        ]);

        // Garantizar estado por defecto
        $data['estado'] = $request->input('estado', 'P');

        // Rellenar creada_por con usuario autenticado (nombre o email)
        $user = auth()->user();
        $data['creada_por'] = $user ? ($user->nombre ?? $user->email ?? 'Sistema') : 'Sistema';

        if (!empty($data['fecha_realizacion'])) {
            $fecha = Carbon::createFromFormat('d/m/Y', $data['fecha_realizacion']);
            if ($fecha->isPast()) {
                return back()->withErrors(['fecha_realizacion'=>'La fecha de realización debe ser futura.'])->withInput();
            }
            $data['fecha_realizacion'] = $fecha->toDateString();
        }

        if ($request->hasFile('fichero_resumen')) {
            $data['fichero_resumen'] = $request->file('fichero_resumen')->store('incidencias_ficheros','local');
        }

        $inc = Incidencia::create($data);

        return redirect()->route('incidencias.index')->with('success','Incidencia creada.');
    }

    /**
     * Edit (admin)
     */
    public function edit(Incidencia $incidencia)
    {
        $provincias = method_exists($this,'provincias') ? $this->provincias() : \provincias_espana();
        $clientes = Cliente::orderBy('nombre')->get();
        return view('incidencias.edit', compact('incidencia','provincias','clientes'));
    }

    /**
     * Update (admin)
     */
    public function update(Request $request, Incidencia $incidencia)
    {
        $rules = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'contacto_nombre' => 'required|string|max:255',
            'contacto_telefono' => ['required','string','max:30','regex:/^[0-9\-\+\s\(\)]{6,30}$/'],
            'contacto_email' => 'required|email',
            'direccion' => 'nullable|string|max:255',
            'poblacion' => 'nullable|string|max:100',
            'codigo_postal' => ['nullable','regex:/^\d{5}$/'],
            'provincia_codigo' => ['nullable','digits:2'],
            'fecha_realizacion' => ['nullable','date_format:d/m/Y'],
            'fichero_resumen' => 'nullable|file|max:10240',
            'empleado_id' => 'nullable|exists:empleados,id',
            'estado' => 'nullable|string'
        ];
        $data = $request->validate($rules);

        $data['estado'] = $request->input('estado', $incidencia->estado ?? 'P');

        if (!empty($data['fecha_realizacion'])) {
            $fecha = Carbon::createFromFormat('d/m/Y', $data['fecha_realizacion']);
            if ($fecha->isPast()) {
                return back()->withErrors(['fecha_realizacion'=>'La fecha de realización debe ser futura.'])->withInput();
            }
            $data['fecha_realizacion'] = $fecha->toDateString();
        }

        if ($request->hasFile('fichero_resumen')) {
            if ($incidencia->fichero_resumen && Storage::disk('local')->exists($incidencia->fichero_resumen)) {
                Storage::disk('local')->delete($incidencia->fichero_resumen);
            }
            $data['fichero_resumen'] = $request->file('fichero_resumen')->store('incidencias_ficheros','local');
        }

        $incidencia->update($data);
        return redirect()->route('incidencias.index')->with('success','Incidencia actualizada.');
    }

    /**
     * Destroy
     */
    public function destroy(Incidencia $incidencia)
    {
        if ($incidencia->fichero_resumen && Storage::disk('local')->exists($incidencia->fichero_resumen)) {
            Storage::disk('local')->delete($incidencia->fichero_resumen);
        }
        $incidencia->delete();
        return redirect()->route('incidencias.index')->with('success','Incidencia eliminada.');
    }

    /**
     * Descargar fichero resumen
     */
    public function downloadFichero($id)
    {
        $inc = Incidencia::findOrFail($id);
        $user = auth()->user();
        if (! ($user->tipo === 'admin' || ($user->tipo==='operario' && $inc->empleado_id == $user->id)) ) {
            abort(403);
        }
        if (! $inc->fichero_resumen || ! Storage::disk('local')->exists($inc->fichero_resumen)) {
            return back()->withErrors(['file'=>'Fichero no encontrado.']);
        }
        return Storage::disk('local')->download($inc->fichero_resumen);
    }

    /**
     * Formulario público (clientes sin login)
     */
    public function publicCreateForm()
    {
        $provincias = method_exists($this,'provincias') ? $this->provincias() : \provincias_espana();
        return view('incidencias.public_create', compact('provincias'));
    }

    /**
     * Guardar incidencia pública (clientes sin login)
     */
    public function publicStore(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'contacto_nombre' => 'required|string|max:255',
            'contacto_telefono' => ['required','string','max:30','regex:/^[0-9\-\+\s\(\)]{6,30}$/'],
            'contacto_email' => 'required|email',
            'direccion' => 'nullable|string|max:255',
            'poblacion' => 'nullable|string|max:100',
            'codigo_postal' => ['nullable','regex:/^\d{5}$/'],
            'provincia_codigo' => ['nullable','digits:2'],
            'fecha_realizacion' => ['nullable','date_format:d/m/Y'],
            'fichero_resumen' => 'nullable|file|max:10240'
        ]);

        // creada_por -> nombre del contacto o 'Cliente público'
        $data['creada_por'] = $request->input('contacto_nombre', 'Cliente público');
        $data['estado'] = 'P';

        if ($request->hasFile('fichero_resumen')) {
            $data['fichero_resumen'] = $request->file('fichero_resumen')->store('incidencias_ficheros','local');
        }

        Incidencia::create($data);

        return redirect()->route('incidencias.public.create')->with('success','Incidencia enviada. Un administrador la revisará.');
    }

    // Helper provincias
    private function provincias(): array
    {
        return [
          '01'=>'Álava','02'=>'Albacete','03'=>'Alicante','04'=>'Almería','05'=>'Ávila',
          '06'=>'Badajoz','07'=>'Islas Baleares','08'=>'Barcelona','09'=>'Burgos','10'=>'Cáceres',
          '11'=>'Cádiz','12'=>'Castellón','13'=>'Ciudad Real','14'=>'Córdoba','15'=>'A Coruña',
          '16'=>'Cuenca','17'=>'Girona','18'=>'Granada','19'=>'Guadalajara','20'=>'Guipúzcoa',
          '21'=>'Huelva','22'=>'Huesca','23'=>'Jaén','24'=>'León','25'=>'Lérida',
          '26'=>'La Rioja','27'=>'Lugo','28'=>'Madrid','29'=>'Málaga','30'=>'Murcia',
          '31'=>'Navarra','32'=>'Ourense','33'=>'Asturias','34'=>'Palencia','35'=>'Las Palmas',
          '36'=>'Pontevedra','37'=>'Salamanca','38'=>'Santa Cruz de Tenerife','39'=>'Cantabria','40'=>'Segovia',
          '41'=>'Sevilla','42'=>'Soria','43'=>'Tarragona','44'=>'Teruel','45'=>'Toledo',
          '46'=>'Valencia','47'=>'Valladolid','48'=>'Vizcaya','49'=>'Zamora','50'=>'Zaragoza',
          '51'=>'Ceuta','52'=>'Melilla'
        ];
    }
}
