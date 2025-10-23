<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    public function index() 
    {
        return response()->json(Address::all(), 200);
    }
    
    //Cria um novo endereço
    public function store(Request $request) 
    {
        $request->validate([
            'street' => 'required|string|max:150',
            'number' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
        ]);

        $address = Address::create($request->all());
        return response()->json($address, 201);
    }
    
    //Exibe um endereço específico
    public function show(Address $address) 
    {
        return response()->json($address, 200);
    }
    
    //Atualiza endereço
    public function update(Request $request, Address $address) 
    {
        $address->update($request->all());
        return response()->json($address, 200);
    }
    
    //Exclui endereço
    public function destroy(Address $address) 
    {
        $address->delete();
        return response()->json(null, 204);
    }
}
