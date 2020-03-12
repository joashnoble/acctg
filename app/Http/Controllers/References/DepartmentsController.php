<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\References\Department;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class DepartmentsController extends Controller
{

    public function index()
    {
        $departments = Department::select(
            'department_id',
            'department_name',
            'department_desc')
            ->where('is_active',TRUE)
            ->where('is_deleted',FALSE)
            ->orderBy('department_name','desc')
            ;
        return Reference::collection($departments->get())
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(),
        [
            'department_name' => 'required'
        ])->validate();

        $department = new Department();
        $department->department_name = $request->input('department_name');
        $department->department_desc = $request->input('department_desc');
        $department->date_created = Carbon::now();
        $department->save();

        return ( new Reference( $department ))
                ->response()
                ->setStatusCode(201);
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);
        return ( new Reference($department))
        ->response()
        ->setStatusCode(200);
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
        [
            'department_name' => 'required'
        ])->validate();
        $department = Department::findOrFail($id);
        $department->department_name = $request->input('department_name');
        $department->department_desc = $request->input('department_desc');
        $department->date_modified = Carbon::now();
        $department->save();

        return ( new Reference( $department ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $department = Department::findOrFail($id);
        $department->is_deleted = 1;
        $department->save();

        return ( new Reference( $department ) )
            ->response()
            ->setStatusCode(200);
    }

    public function checkIfUsed()
    {
        $exists = 'false';
        return $exists;
    }

}
