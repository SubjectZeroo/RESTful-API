<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Mail\UserCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules =[
            'name' => 'required',
            'email' => 'required|min:6|unique:users',
            'password' => 'required|min:6|confirmed',

        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['password'] = bcrypt($request->password);

        $data['verified'] = User::UNVERIFIED_USER;

        $data['verification_token'] = User::generateVerificationCode();

        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);


        return response()->json(['data' => $user], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
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
    public function update(Request $request, User $user)
    {


        $rules =[
            'email' => 'min:6|unique:users,email,'. $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        if($request->has('name')) {
            $user->name = $request->name;
        }

        if($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        if($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse('Solo los usuarios verficados  pueden modificar el campo admin', 409);
            }
            $user->admin = $request->admin;
        }

        // if (!$user->isDirty()) {
        //     return $this->errorResponse('Nesecitas especificar un valor diferente para actualizar' , 422);
        // }

        $user->save();

        // return response()->json(['data' => $user], 200);
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
    }


    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;

        $user->verification_token = null;

        $user->save();

        return $this->showMessage('La cuenta se verifico satisfactoriamente');
    }

    public function resend(User $user)
    {
       if ($user->isVerified()) {
           return $this->errorResponse('Este usuarios esta verificado', 409);
       }



       retry(5,function() use($user) {
            Mail::to($user)->send(new UserCreated($user));
       }, 100);

       return $this->showMessage('El email de verificacion se reenvio');

    }
}
