<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use Session;
use App\Componente;


class DocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        
       

        if(!empty(Session::get('componente_id')) && $request){
            $query = trim($request->get('search'));

            $file = Documento::whereComponente_id(Session::get('componente_id') )
            ->where('nombre',  'LIKE', '%' . $query . '%')
            ->orderBy('id', 'asc')
            ->paginate(7);

           $componente = Componente::findOrFail(Session::get('componente_id'));


            return view("documentos.index", ['file' =>$file, 'search' => $query, 'activemenu' => 'componente','componente' =>$componente]);

                
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('documentos.create',['activemenu' => 'componente']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $data= new Documento;
       if($request->file('file')){
           $file=$request->file('file');
           $filename=time().'.'.$file->getClientOriginalExtension();
           $request->file->move('storage/', $filename);

           $data->file= $filename;

       }
       $data->nombre=$request->nombre;
       $data->descripcion=$request->descripcion;
       $data->privacidad=$request->privacidad;
       $data->componente_id = Session::get('componente_id');
       $data->save();
       return redirect('documentosop')->with('create','Se creo correctamente.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=Documento::find($id);
        return view('documentos.details',['activemenu' => 'componente'],compact('data'));
       
    }

    public function download($file){
        return response()->download('storage/'.$file);
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
