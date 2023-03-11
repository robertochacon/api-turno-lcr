<?php

namespace App\Http\Controllers;

use App\Turns;
use Illuminate\Http\Request;
use App\Events\UpdateTurns;

class TurnsController extends Controller
{
    public function index()
    {
        $turns = Turns::orderBy('id', 'DESC')->get();
        return response()->json(["data"=>$turns],200);
    }

    public function watch($id){
        try{
            $entity = Turns::find($id);
            return response()->json(["data"=>entity],200);
        }catch (Exception $e) {
            return response()->json(["data"=>"none"],200);
        }
    }

    public function register(Request $request)
    {
        $turns = new Turns(request()->all());
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('/public/entities');
            $turns->logo = $path;
         }
        $turns->save();
        event(new UpdateTurns('register'));
        return response()->json(["data"=>$turns],200);
    }

    public function update(Request $request, $id){
        try{
            $turn = Turns::find($id);
            $turn->update($request->all());
            if($turn->status == 'call'){
                $msg = ['turno'=>$turn->code.$turn->id,'puesto'=>$turn->window];
            }else{
                $msg = 'update';
            }
            event(new UpdateTurns($msg));
            return response()->json(["data"=>"ok"],200);
        }catch (Exception $e) {
            return response()->json(["data"=>"none"],200);
        }
    }

    public function delete($id){
        try{
            $turn = Turns::destroy($id);
            event(new UpdateTurns(['turno'=>$turn->code.$turn->id,'puesto'=>$turn->window]));
            return response()->json(["data"=>"ok"],200);
        }catch (Exception $e) {
            return response()->json(["data"=>"none"],200);
        }
    }
}
