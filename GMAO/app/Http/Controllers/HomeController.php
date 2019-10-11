<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Activite;
use App\Ointervention;
use App\Mpreventive;
use App\Equipement;
use App\Notification;

use Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       

        $notifications = Notification::where('touser',Auth::user()->role)->whereDate('created_at', '=', date('Y-m-d'))->get();
        $nbrtechniciens = User::where('role',"Technicien")->count();
        $nbrchefsec = User::where('role',"Chef secteur")->count();
        $users = User::all();
        $activities = Activite::orderBy('created_at', 'desc')->get();;
        $diall = Ointervention::all()->count();
        $dinc = Ointervention::where('etat',"demandée")->count();
        $dir = Ointervention::where('etat',"refusée")->count();
        $diec = Ointervention::where('etat',"En cours")->count();
        $dieav = Ointervention::where('etat',"En attente de validation")->count();
        $dit = Ointervention::where('etat',"terminé")->count();
        $diperc = round( ( $dinc / $diall ) * 100 , 2) ;
        $dirperc = round( ( $dir / $diall ) * 100 , 2);
        $diecperc = round( ( $diec / $diall ) * 100 + ( $dieav / $diall ) * 100, 2);
        $ditperc = round( ( $dit / $diall ) * 100, 2);
        //echo $diperc ;

        return view('home')->with('notifications',$notifications)->with('ditperc',$ditperc)->with('diecperc',$diecperc)->with('dirperc',$dirperc)->with('diperc',$diperc)->with('users',$users)->with('activities',$activities)->with('nbrtechniciens',$nbrtechniciens)->with('nbrchefsec',$nbrchefsec);
    }
    public function indextechnicien()
    {
        $notifications = Notification::where('iduser', Auth::user()->id)->get();
        $equipements = Equipement::all();
        $today = date('Y-m-d');
        $ointerventions = Ointervention::where('destinateur',Auth::user()->id)
                                        ->where('etat',"=","demandée")
                                        ->get();
        $mpreventives = Mpreventive::where('executeur',Auth::user()->id)
                            ->where('date_prochaine',$today)
                            ->where('etat',"=","En cours")
                            ->get();
        
        return view('homet')->with('equipements',$equipements)->with('ointerventions',$ointerventions)->with('mpreventives',$mpreventives);
    }
    
}
