<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Activite;
use File;

use Illuminate\Support\Carbon;
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::where( 'email',"!=", Auth::user()->email )->get();
        return view('users.index')->with('users',$users);
    }
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        //
        $search = "resutat";
        $searchuser = $request->input('searchuser');
        $users = User::where( 'name',"like", '%'.$searchuser.'%' )->get();
        if (count($users) > 0 ){
            $search = NULL;
        }
        $activite = new Activite();
        $activite->iduser = Auth::user()->id;
        $activite->description = "chercher pour l'utilisateur ".$searchuser;
        $activite->save();
        return view('users.index')->with('users',$users)->with('search', $search )->with('searchuser', $searchuser );
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('users.ajout');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'usermail' => 'required',
            'userpw' => 'required',
            'usermat' => 'required',
            'role' => 'required',
        ]);
            
        
        //
        $user = new User();
        $user->name = $request->input("username") ;
        $user->matricule = $request->input("usermat") ;
        $user->email = $request->input("usermail");
        $user->password = Hash::make($request->input("userpw"));
        $user->role =  $request->input("role");
        $user->save();
        $activite = new Activite();
        $activite->iduser = Auth::user()->id;
        $activite->description = "Ajouter l'utilisateur ".$request->input("username");
        $activite->save();
        return redirect("/user/add")->with('adduser',"success");
    }
    public function modprofile(Request $request){
      
      
        $user = User::find(Auth::user()->id);
        $user->name = $request->input("username") ;
        $user->matricule = $request->input("usermat") ;
        $user->email = $request->input("usermail");

        if ( $user->password != $request->input("userpw") ){
            $user->password = Hash::make($request->input("userpw"));
        }
        
        $user->description =  $request->input("description");
        $user->birthdate =  $request->input("birthdate");
        $user->phone =  $request->input("phone");
        $avatar = $request->file('avatar');
        if ($avatar != NULL){
            $avatarname = uniqid().".".File::extension($avatar->getClientOriginalName());
            //uniqid() is php function to generate uniqid but you can use time() etc.
            $destinationPath = 'uploads/profile';
            $avatar->move($destinationPath,$avatarname);
            $user->avatar = $avatarname;
        }
        $user->save();
        $activite = new Activite();
        $activite->iduser = Auth::user()->id;
        $activite->description = "modifier les cordonnes de l'utilisateur ".$request->input("username");
        $activite->save();
        return redirect("/profile")->with('adduser',"success");
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
        $user = User::find($id);
        
        return view('users.modif')->with('user',$user);
    }
    public function profile(){
        $activities = Activite::where('iduser',Auth::user()->id)->get();
        return view('users.profile')->with('activities',$activities);
    }
    public function profilemod(){
        
        return view('users.profilemod'); 
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
        $user =User::find($id);
        $user->name = $request->input("username") ;
        $user->matricule = $request->input("usermat") ;
        $user->email = $request->input("usermail");
        $user->password = Hash::make($request->input("userpw"));
        $user->role =  $request->input("role");
        $user->save();
        $activite = new Activite();
        $activite->iduser = Auth::user()->id;
        $activite->description = "modifier les cordonnes de l'utilisateur ".$request->input("username");
        $activite->save();
        return redirect("/user/".$user->id)->with('adduser',"success");
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
        $user =User::find($id);
        $username = $user->name;
        $user->delete();
        $activite = new Activite();
        $activite->iduser = Auth::user()->id;
        $activite->description = "supprimer l'utilisateur ".$username;
        $activite->save();
        return redirect("/users")->with('adduser',"deleted");
    }
}
