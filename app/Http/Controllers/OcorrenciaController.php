<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OcorrenciaController extends Controller
{
    public function index()
    {
        return view('ocorrencias.index');
    }
    public function create()
    {
        //return view('ocorrencias.create');
    }
    public function store(Request $request)
    {
        //
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function destroy($id)
    {
        //
    }

}
