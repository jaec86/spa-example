<?php

use Illuminate\Http\Request;

Route::get('/{vue_capture?}', function (Request $request) {
    if ($request->wantsJson()) {
        return response()->json(['message' => 'Ruta no válida'], 404);
    }
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');