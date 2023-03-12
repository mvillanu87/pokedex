<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Types;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function getPokemon(Request $request) {
        
        //Metodo que trae un pokemon
        
        //Validacion
        $validar = $this->validatePokemon($request->name, 'name');
        if (!$validar['procesar']) {
            return response()->json(['error' => $validar['comentario']], 400);
        }
        
        //Busca el pokemon
        $pokemon = $this->searchPokemon($request->name);
        
        if ($pokemon->isEmpty()) {
            return response()->json(['error' => 'No existe el pokemon ' . $request->name], 400);
        }
        
        //Trae el pokemon buscado con su tipo
        $resultado = $this->searchPokemonByType('pokemon.pok_name', $request->name);
 
        return response()->json($resultado);

    }
       
    public function getPokemonsByType(Request $request) {
        
        //Metodo que trae pokemon segun su tipo
        
        //Validacion
        $validar = $this->validatePokemon($request->type, 'type');
        if (!$validar['procesar']) {
            return response()->json(['error' => $validar['comentario']], 400);
        }
        
        //Busca el tipo
        $type = $this->searchType($request->type);
        
        if ($type->isEmpty()) {
            return response()->json(['error' => 'No existe el tipo ' . $request->type], 400);
        }
        
        //Trae todos los pokemon de un tipo
        $resultado = $this->searchPokemonByType('types.type_name', $request->type);
        
        if ($resultado->isEmpty()) {
            return response()->json(['error' => 'No existen pokemon del tipo' . $request->type], 400);
        } else {
            return response()->json($resultado);
        }
        
    }
    
    private function validatePokemon($parametro, $nombre) {

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
    
    private function searchPokemon($name) {
        
        //Metodo que busca un pokemon por nombre
        
        $data = Pokemon::where('pok_name', $name)->get();
        return $data;
        
    }
    
    private function searchType($type) {
        
        //Metodo que busca un tipo/equipo/especialidad por nombre
        
        $data = Types::where('type_name', $type)->get();
        return $data;
    }
    
    private function searchPokemonByType($columna, $parametro) {
        
        //Metodo que busca un pokemon por tipo
        
        $data = Pokemon::select('pokemon.pok_id AS id',
                                'pokemon.pok_name AS name', 
                                'pokemon.pok_height AS height',
                                'pokemon.pok_weight AS weight', 
                                'pokemon.pok_base_experience AS base_experience', 
                                'types.type_name AS type')
                ->join('pokemon_types', 'pokemon.pok_id', '=', 'pokemon_types.pok_id')
                ->join('types', 'pokemon_types.type_id', '=', 'types.type_id')
                ->where($columna, $parametro)
                ->get();
        
        return $data;
    }
}