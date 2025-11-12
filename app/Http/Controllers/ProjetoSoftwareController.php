<?php
namespace App\Http\Controllers;
use App\Models\ProjetoSoftware;
use App\Http\Requests\StoreProjetoSoftwareRequest;
use App\Http\Requests\UpdateProjetoSoftwareRequest;
use Illuminate\Http\Request;


class ProjetoSoftwareController extends Controller
{
public function index(Request $request){
$q = ProjetoSoftware::query();
if ($search = $request->get('q')) {
$q->where(fn($w)=>$w->where('codigo','like',"%$search%")
->orWhere('titulo','like',"%$search%")
->orWhere('sistema','like',"%$search%"));
}
$projetos = $q->latest()->paginate(15);
return view('projetos_software.index', compact('projetos'));
}


public function create(){ return view('projetos_software.create'); }


public function store(StoreProjetoSoftwareRequest $request){
$projeto = ProjetoSoftware::create($request->validated());
return redirect()->route('projetos.show', $projeto)->with('success','Projeto criado.');
}


public function show(ProjetoSoftware $projeto){
$projeto->load(['apfs.atividades','fiscalizacoes.documentos']);
return view('projetos_software.show', compact('projeto'));
}


public function edit(ProjetoSoftware $projeto){ return view('projetos_software.edit', compact('projeto')); }


public function update(UpdateProjetoSoftwareRequest $request, ProjetoSoftware $projeto){
$projeto->update($request->validated());
return redirect()->route('projetos.show',$projeto)->with('success','Atualizado.');
}


public function destroy(ProjetoSoftware $projeto){
$projeto->delete();
return redirect()->route('projetos.index')->with('success','Removido.');
}
}
