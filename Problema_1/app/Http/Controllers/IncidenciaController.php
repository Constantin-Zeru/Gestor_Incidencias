<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\Cliente;
use App\Models\Empleado;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class IncidenciaController extends Controller
{
    public function __construct()
    {
        // Public endpoints: publicCreateForm, publicStore
        $this->middleware('auth')->except(['publicCreateForm','publicStore']);
        // Admin-only for most management actions (show is handled separately)
        $this->middleware('rol:admin')->only(['create','store','edit','update','destroy','index','cambiarEstado']);
    }

    /**
     * Index (admin) - listado con filtros
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
        $provincias = $this->provincias();

        return view('incidencias.index', compact('incidencias','provincias'));
    }

    /**
     * Mostrar una incidencia (autenticado).
     * Admin ve todo. Operario ve solo las suyas.
     */
    public function show(Incidencia $incidencia)
    {
        $user = auth()->user();

        if ($user->tipo === 'admin') {
            return view('incidencias.show', compact('incidencia'));
        }

        if ($user->tipo === 'operario' && $incidencia->empleado_id == $user->id) {
            return view('incidencias.show', compact('incidencia'));
        }

        abort(403, 'No tienes permiso para ver esta incidencia.');
    }

    /**
     * Listado operario (mis incidencias) - con los mismos filtros que index
     */
    public function misIncidencias(Request $request)
    {
        $user = auth()->user();

        $query = Incidencia::with('cliente','empleado')->where('empleado_id', $user->id);

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
        $provincias = $this->provincias();

        return view('incidencias.index', compact('incidencias','provincias'));
    }

    /**
     * Form crear (admin)
     */
    public function create()
    {
        $provincias = $this->provincias();
        $clientes = Cliente::orderBy('nombre')->get();
        // Empleados tipo operario para mostrar en select
        $empleados = Empleado::where('tipo','operario')->orderBy('nombre')->get();

        return view('incidencias.create', compact('provincias','clientes','empleados'));
    }

    /**
     * Store (admin) - guardar incidencia
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'cliente_id' => 'required|exists:clientes,id',
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

        // Mapear estados para cumplir CHECK de la tabla
        $data['estado'] = $this->mapEstadoInput($request->input('estado', ''));

        // creada_por = 'admin' ya que viene de admin autenticado
        $data['creada_por'] = 'admin';

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
        $provincias = $this->provincias();
        $clientes = Cliente::orderBy('nombre')->get();
        $empleados = Empleado::where('tipo','operario')->orderBy('nombre')->get();

        return view('incidencias.edit', compact('incidencia','provincias','clientes','empleados'));
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
            'estado' => 'nullable|string',
            'cliente_id' => 'required|exists:clientes,id',
        ];
        $data = $request->validate($rules);

        $data['estado'] = $this->mapEstadoInput($request->input('estado', $incidencia->estado ?? ''));

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
     * Destroy (admin)
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
     * Descargar fichero resumen (auth). El controller valida permisos.
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

        // Construir nombre de descarga amigable
        $clienteName = optional($inc->cliente)->nombre ?? 'cliente';
        $ext = pathinfo($inc->fichero_resumen, PATHINFO_EXTENSION) ?: 'dat';
        $safeName = Str::slug($clienteName, '_');
        $downloadName = "incidencia_{$inc->id}_{$safeName}.{$ext}";

        return Storage::disk('local')->download($inc->fichero_resumen, $downloadName);
    }

    /**
     * Cambiar estado (admin)
     */
    public function cambiarEstado(Request $request, Incidencia $incidencia)
    {
        $request->validate(['estado' => 'required|string']);
        $incidencia->estado = $this->mapEstadoInput($request->input('estado'));
        $incidencia->save();

        return redirect()->back()->with('success','Estado actualizado.');
    }

    /**
     * Operario: completar tarea (añadir anotaciones posteriores y opcionalmente subir fichero)
     */
    public function completarTarea(Request $request, Incidencia $incidencia)
    {
        $user = auth()->user();
        if ($user->tipo !== 'operario' || $incidencia->empleado_id != $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'anotaciones_posteriores' => 'nullable|string',
            'fichero_resumen' => 'nullable|file|max:10240'
        ]);

        if ($request->hasFile('fichero_resumen')) {
            if ($incidencia->fichero_resumen && Storage::disk('local')->exists($incidencia->fichero_resumen)) {
                Storage::disk('local')->delete($incidencia->fichero_resumen);
            }
            $incidencia->fichero_resumen = $request->file('fichero_resumen')->store('incidencias_ficheros','local');
        }

        if (!empty($data['anotaciones_posteriores'])) {
            $incidencia->anotaciones_posteriores = trim(($incidencia->anotaciones_posteriores ?? '') . "\n\n" . $data['anotaciones_posteriores']);
        }

        $incidencia->estado = 'finalizada';
        $incidencia->save();

        return redirect()->route('incidencias.mis')->with('success','Tarea marcada como completada.');
    }

    /**
     * Formulario público para clientes (sin login)
     */
    public function publicCreateForm()
    {
        $provincias = $this->provincias();
        return view('incidencias.public_create', compact('provincias'));
    }

    /**
     * Guardar incidencia pública (cliente sin login)
     * Valida el CIF + teléfono y crea incidencia asignada a ese cliente.
     */
    public function publicStore(Request $request)
    {
        $data = $request->validate([
            'cif' => 'required|string',
            'telefono' => 'required|string',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'direccion' => 'nullable|string|max:255',
            'poblacion' => 'nullable|string|max:100',
            'codigo_postal' => ['nullable','regex:/^\d{5}$/'],
            'provincia_codigo' => ['nullable','digits:2'],
            'fecha_realizacion' => ['nullable','date_format:d/m/Y'],
            'fichero_resumen' => 'nullable|file|max:10240'
        ]);

        $normalizePhone = function($p) {
            return preg_replace('/\D+/', '', $p);
        };

        $cif = $request->input('cif');
        $tel = $normalizePhone($request->input('telefono'));

        $cliente = Cliente::where('cif', $cif)->get()->first(function($c) use ($tel, $normalizePhone) {
            return $normalizePhone($c->telefono) === $tel;
        });

        if (! $cliente) {
            return back()->withErrors(['cif' => 'CIF y teléfono no coinciden con ningún cliente registrado.'])->withInput();
        }

        $incData = [
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'cliente_id' => $cliente->id,
            'contacto_nombre' => $cliente->nombre,
            'contacto_telefono' => $cliente->telefono,
            'contacto_email' => $cliente->email,
            'direccion' => $data['direccion'] ?? $cliente->direccion ?? null,
            'poblacion' => $data['poblacion'] ?? null,
            'codigo_postal' => $data['codigo_postal'] ?? null,
            'provincia_codigo' => $data['provincia_codigo'] ?? null,
            'creada_por' => 'cliente',
            'estado' => 'pendiente'
        ];

        if ($request->hasFile('fichero_resumen')) {
            $incData['fichero_resumen'] = $request->file('fichero_resumen')->store('incidencias_ficheros','local');
        }

        Incidencia::create($incData);

        // Redirige al home/login con mensaje (no al formulario de nuevo envío)
        return redirect()->route('home')->with('success','Incidencia enviada. Un administrador la revisará.');
    }

    /* --------------------
     | Helper privado
     * -------------------- */

    /**
     * Mapear entrada (P/A/EP/F o etiquetas) a los valores que exige CHECK en BD.
     */
    private function mapEstadoInput($input)
    {
        $map = [
            'p' => 'pendiente',
            'a' => 'asignada',
            'ep' => 'en_proceso',
            'f' => 'finalizada',
            'pendiente' => 'pendiente',
            'asignada' => 'asignada',
            'en_proceso' => 'en_proceso',
            'finalizada' => 'finalizada',
        ];

        $in = strtolower(trim((string)$input));
        if ($in === '') return 'pendiente';
        return $map[$in] ?? $in;
    }

    /**
     * Provincias helper (puedes extraerlo a app/Helpers/provincias.php)
     */
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
