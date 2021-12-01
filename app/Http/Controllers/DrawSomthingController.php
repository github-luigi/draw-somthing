<?php

namespace App\Http\Controllers;

use App\Model\Palabra\Palabra;
use Illuminate\Http\Request;
use Validator;

class DrawSomthingController extends Controller
{


    public function getWords(Request $request)
    {
        // Validaciones
        $data = $request->all();
        $validator = Validator::make($data, [
            'letters' => 'string|required|min:12|max:12',
            'word_letter_quantity' => 'integer|required'
        ]);
        if($validator->fails()) {
            return view("index", [
                "report" => null,
                "errors" => $validator->errors()
            ]);
        }

        // Contar letras repetidas
        preg_match_all('/(.)\1+/', $data['letters'], $matches);
        $repeatedLetters = array_combine($matches[0], array_map('strlen', $matches[0]));
        $repeatedQuantity = 0;
        foreach ($repeatedLetters ?? [] as $letter => $quantity){
            $repeatedQuantity += $quantity - 1;
        }

        // Consultar palabras
        $words = Palabra::whereRaw("char_length(sin_acentos) = ?", [$data['word_letter_quantity']])->get();

        $report = null;
        if(count($words ?? []) > 0){
            // Validar letras, debe presentar como minimo cantidad de letras de la palabra sin tener en cuenta las repetidas
            $letters = str_split(trim($data['letters']));
            $report = $words->filter(function ($word) use($data, $letters, $repeatedQuantity) {
                $containsQuantity = 0;
                foreach ($letters ?? [] as $letter){
                    if(str_contains($word->sin_acentos, $letter)){
                        $containsQuantity += 1;
                    }
                }
                return $containsQuantity > 0 && $containsQuantity >= (
                        $data['word_letter_quantity'] > $repeatedQuantity ?
                        $data['word_letter_quantity'] - $repeatedQuantity : $repeatedQuantity
                    );
            })->all();
        }

        return view("index", [
            "report" => $report
        ]);
    }

}
