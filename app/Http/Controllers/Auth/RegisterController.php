<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Role;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('root');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
     $id_unite= DB::select('select id from roles where role="unite"');
        $id_admin=DB::select('select id from roles where role="gouverneur" or role="admin"');
        $user= User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'=>$data['role'],
        ]);
         if($data['role']==$id_unite[0]->id){
             $unite_user=DB::insert('insert into user_unites (user_id, unite_vaccination_id) values (?, ?)', [$user->id, $data['unite']]);
         }
         else if($data['role']==$id_admin[0]->id || $data['role']==$id_admin[1]->id){
             $admin_user=DB::insert('insert into user_provinces (user_id, province_id) values (?, ?)', [$user->id, $data['province']]);
         }
         return $user;
    }
    public function showRegistrationForm() {
        $role = DB::select('select * from roles where role!="root"');
        $unite=DB::select('select * from unite_vaccinations where uniteParent=""');
        $province=DB::select('select * from provinces');
       $data_table=DB::select('select  u.nomUnite "unite",u.id,count(  distinct co.id) "equipe",count( distinct uu.user_id) "compte" from unite_vaccinations u left join contenirs co on co.unite_vaccination_id=u.id left join membre_equipes me on co.membre_equipe_id=me.id left join user_unites uu on uu.unite_vaccination_id=u.id left join users us on us.id=uu.user_id group by (u.nomUnite)');
        return view ('auth.register', compact('role','unite','province','data_table'));
    }
}
