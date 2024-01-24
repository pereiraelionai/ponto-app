<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthTokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Rotas de autenticação JWT Token (Usuário)
Route::post('/login', [AuthTokenController::class, 'login']);
Route::post('/logout', [AuthTokenController::class, 'logout'])->middleware('jwt.auth');
Route::post('/refresh', [AuthTokenController::class, 'refresh']);
Route::post('/me', [AuthTokenController::class, 'me'])->middleware('jwt.auth');

Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::apiResource('/colaboradores', App\Http\Controllers\ColaboradorController::class);
    Route::apiResource('/feriados', App\Http\Controllers\FeriadosController::class);
    Route::post('/colaboradores/consulta-cpf', [App\Http\Controllers\ColaboradorController::class, 'consultaCpf']);
    Route::get('/colaboradores-cargos', [App\Http\Controllers\ColaboradorController::class, 'getCargo']);
    Route::get('/colaboradores-funcoes', [App\Http\Controllers\ColaboradorController::class, 'getFuncao']);
    Route::get('/usuarios', [App\Http\Controllers\UsuariosController::class, 'index']);
    Route::post('/usuarios', [App\Http\Controllers\UsuariosController::class, 'store']);
    Route::post('/usuarios/consulta-usuario', [App\Http\Controllers\UsuariosController::class, 'consultaUsuario']);
});