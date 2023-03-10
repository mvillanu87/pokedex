<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PokemonController extends Controller
{
    public function getPokemon(Request $request) {
        
        //Metodo que trae un pokemon
        
        //Validacion
        $validar = $this->validarPokemon($request->name, 'name');
        if (!$validar['procesar']) {
            return response()->json(['error' => $validar['comentario']], 400);
        }
                
        $data = Pokemon::where('pok_name', $request->name)->get();
        
        return response()->json($data);
        
    }
       
    public function getPokemonsByType(Request $request) {
        
        //Metodo que trae pokemon segun su tipo
        
        //Validacion
        $validar = $this->validarPokemon($request->type, 'type');
        if (!$validar['procesar']) {
            return response()->json(['error' => $validar['comentario']], 201);
        }
                
        $data = Pokemon::select('pokemon.pok_id AS id',
                                'pokemon.pok_name AS name', 
                                'pokemon.pok_height AS height',
                                'pokemon.pok_weight AS weight', 
                                'pokemon.pok_base_experience AS base_experience', 
                                'types.type_name AS type')
                ->join('pokemon_types', 'pokemon.pok_id', '=', 'pokemon_types.pok_id')
                ->join('types', 'pokemon_types.type_id', '=', 'types.type_id')
                ->where('types.type_name', $request->type)
                ->get();

        return response()->json($data);
        
    }
    
    private function validarPokemon($parametro, $nombre) {

        //Metodo para validar parametros del request GET personalizados
        
        $procesar = true;
        $mensaje = '';

        if (is_null($parametro)) {
            $procesar = false;
            $mensaje = 'Debe Ingresar el parametro ' . $nombre;
        }
        
        if (!is_null($parametro) && !ctype_alpha($parametro)) {
            $procesar = false;
            $mensaje = 'El parametro ' . $nombre . ' debe ser solo letras';
        }
        
        return ['procesar' => $procesar, 'comentario' => $mensaje];
    }
}