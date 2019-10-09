<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use App\Equipement;
use App\Activite;
use Auth;
use File;


class EquipementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         //
         $equipements = Equipement::all();
         return view('Equipements.index')->with('equipements',$equipements);
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
         //
         $equipements = Equipement::where("name",'like','%'.$request->input("searchequipement").'%')->get();
         return view('Equipements.index')->with('equipements',$equipements);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('equipements.ajout');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $photo = $request->file('photo');
        $document = $request->file('document');
   
        //Display File Name
        //echo 'File Name: '.$file->getClientOriginalName();
        //Display File Extension
        //echo 'File Extension: '.$file->getClientOriginalExtension();
        //Display File Real Path
        //echo 'File Real Path: '.$file->getRealPath();
        //Display File Size
        //echo 'File Size: '.$file->getSize();
        //Display File Mime Type
        //echo 'File Mime Type: '.$file->getMimeType();
        //Move Uploaded File
        $photoname = uniqid().".".File::extension($photo->getClientOriginalName());
        //uniqid() is php function to generate uniqid but you can use time() etc.
        $destinationPath = 'uploads/photos';
        $photo->move($destinationPath,$photoname);

        $documentname = uniqid().".".File::extension($document->getClientOriginalName());
        //uniqid() is php function to generate uniqid but you can use time() etc.
        $destinationPath = 'uploads/documents';
        $document->move($destinationPath,$documentname);
        
        //
        $equipement = new Equipement();
        $equipement->name=$request->input("name");
        $equipement->marque=$request->input("marque");
        $equipement->modele=$request->input("modele");
        $equipement->description=$request->input("description");
        $equipement->numero=$request->input("numero");
        $equipement->emplacement=$request->input("emplacement");
        $equipement->photo=$photoname;
        $equipement->document = $documentname;
        $equipement->save();

        return redirect('/equipements');
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $equipement = Equipement::find($id);
        return view('Equipements.equipement')->with('equipement',$equipement); 
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
        $equipement = Equipement::find($id);
        return view('Equipements.modifier')->with('equipement',$equipement); 
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
        $photo = $request->file('photo');
        $document = $request->file('document');
        $equipement = Equipement::find($id);
        $equipement->name=$request->input("name");
        $equipement->marque=$request->input("marque");
        $equipement->modele=$request->input("modele");
        $equipement->description=$request->input("description");
        $equipement->numero=$request->input("numero");
        $equipement->emplacement=$request->input("emplacement");
        if ($photo != NULL){
               
                $photoname = uniqid().".".File::extension($photo->getClientOriginalName());
                //uniqid() is php function to generate uniqid but you can use time() etc.
                $destinationPath = 'uploads/photos';
                $photo->move($destinationPath,$photoname);
                $equipement->photo=$photoname;
        }

        if ($document != NULL){  
               
                $documentname = uniqid().".".File::extension($document->getClientOriginalName());
                //uniqid() is php function to generate uniqid but you can use time() etc.
                $destinationPath = 'uploads/documents';
                $document->move($destinationPath,$documentname);
                $equipement->document = $documentname;
        
        }
        
        $equipement->update();
        $activite = new Activite();
        $activite->iduser = Auth::user()->id;
        $activite->description = "modifier l'equipement ".$request->input("name");
        $activite->save();
        return redirect("/equipement/".$equipement->id);
       

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
        $equipement = Equipement::find($id);
        $equipement->delete();
        return redirect('/equipements');
    }
}
