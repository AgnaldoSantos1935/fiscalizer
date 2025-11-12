<?php
namespace App\Http\Controllers;
use App\Models\ProjetoSoftware; use App\Models\Apf;
use App\Http\Requests\StoreApfRequest;
use App\Http\Requests\UpdateApfRequest;


class ApfController extends Controller
{
public function index(ProjetoSoftware $projeto){
$apfs = $projeto->apfs()->latest()->paginate(20);
return view('projetos.index', compact('projeto','apfs'));
}
public function store(StoreApfRequest $request, ProjetoSoftware $projeto){
$data = $request->validated();
$data['projeto_id'] = $projeto->id;
$apf = Apf::create($data);
return back()->with('success','APF adicionada.');
}
public function update(UpdateApfRequest $request, ProjetoSoftware $projeto, Apf $apf){
$apf->update($request->validated());
return back()->with('success','APF atualizada.');
}
public function destroy(ProjetoSoftware $projeto, Apf $apf){
$apf->delete(); return back()->with('success','APF removida.');
}
}
