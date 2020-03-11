<?php

namespace App\Http\Controllers\Utilities;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::leftJoin('b_user_groups', 'b_user_groups.user_group_id', '=', 'b_users.user_group_id')
                    ->where('b_users.is_deleted', 0)
                    ->get();
        return Reference::collection($users);
    }

    public function Profile()
    {
        $user = User::findOrFail(Auth::user()->id);
        return ( new Reference( $user ) )
            ->response()
            ->setStatusCode(200);
    }

    public function SaveProfile(Request $request)
    {
        Validator::make($request->all(),
            [
                'username' => 'required|max:255|unique:b_users,username,'.$request->input('id'),
                'old_password' => ['required', function($attribute, $value, $fail) use($request){
                    if (!Hash::check($request->input('old_password'), Auth::user()->password)) {
                        $fail('The old password is incorrect');
                    }
                }],
                'password' => 'nullable|min:6|confirmed',
                'email' => 'required|email|max:255|unique:b_users,email,'.$request->input('id'),
            ]
        )->validate();

        $user = User::findOrFail(Auth::user()->id);
        $user->username = $request->input('username');
        if(!($request->input('password') == "" || $request->input('password') == null))
        {
            $user->password = Hash::make($request->input('password'));
        }
        $user->email = $request->input('email');
        $user->firstname = $request->input('firstname');
        $user->middlename = $request->input('middlename');
        $user->lastname = $request->input('lastname');
        $user->path = $request->input('path');
        $user->modified_datetime = Carbon::now();
        $user->modified_by = Auth::user()->id;

        $user->save();
        return ( new Reference( $user ) )
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {   
        Validator::make($request->all(),
            [
                'username' => 'required|string|max:255|unique:b_users',
                'password' => 'required|string|min:6|confirmed',
                'email' => 'required|string|email|max:255|unique:b_users'
            ]
        )->validate();

        $user = new User();
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));
        $user->email = $request->input('email');
        $user->user_group_id = $request->input('user_group_id');
        $user->firstname = $request->input('firstname');
        $user->middlename = $request->input('middlename');
        $user->lastname = $request->input('lastname');
        $user->created_datetime = Carbon::now();
        $user->created_by = Auth::user()->id;

        $user->save();

        return $this->show($user->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::leftJoin('b_user_groups', 'b_user_groups.user_group_id', '=', 'b_users.user_group_id')
                    ->findOrFail($id);

        return ( new Reference( $user ) )
            ->response()
            ->setStatusCode(200);
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
        Validator::make($request->all(),
            [
                'username' => 'required|string|max:255|unique:b_users,username,'.$id,
                'password' => 'required|string|min:6|confirmed',
                'email' => 'required|string|email|max:255|unique:b_users,email,'.$id
                
            ]
        )->validate();

        $user = User::findOrFail($id);
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));
        $user->email = $request->input('email');
        $user->user_group_id = $request->input('user_group_id');
        $user->firstname = $request->input('firstname');
        $user->middlename = $request->input('middlename');
        $user->lastname = $request->input('lastname');
        $user->modified_datetime = Carbon::now();
        $user->modified_by = Auth::user()->id;

        $user->save();

        //return json based from the resource data
        return $this->show($user->id);
    }

    /**
     * Update the specified resource in storage for deleting.
     * preventing force delete rather update the is_deleted = true
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->is_deleted = 1;
        $user->deleted_datetime = Carbon::now();
        $user->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $user->save();

        return ( new Reference( $user ) )
            ->response()
            ->setStatusCode(200);
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
