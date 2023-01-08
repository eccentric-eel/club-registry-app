<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Record;
use Carbon\Carbon;

class RecordController extends Controller
{
    protected $colMap = [ 'firstname',
                          'surname',
                          'address', 
                          'guest_type',
                          'accompanying_member_name', 
                          'accompanying_member_number',
                          'created_at',
                          'id' ];

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
 
        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response(null, 204);
        }

        return response(null, 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
    }

    public function index()
    {      
        return view('index');
    }
    public function records()
    {
        return (Auth::check()) ? view('index') : redirect('/admin');
    }

    public function queryRecords(Request $request) //return all records from DB
    {
        $sortCol = $this->colMap[$request->sortCol];
        $sortDirection = ($request->sortAsc) ? 'asc' : 'desc';

        $startDate = ($request->startDate) ? Carbon::parse($request->startDate) : new Carbon('1970-01-01');
        $endDate   = ($request->endDate)   ? Carbon::parse($request->endDate)   : new Carbon('2100-12-31');
        
        return Record::where(function ($query) use ($request) {
            $query->where('firstname', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('surname', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('address', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('accompanying_member_name',   'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('accompanying_member_number', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('id', 'LIKE', '%' . $request->queryString . '%');
            })->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate) 
              ->orderBy($sortCol, $sortDirection)
              ->paginate(100);
    }

    public function singleRecord(Request $request) //return only specified record
    {
        return Record::find($request->recordID);
    }

    public function store(Request $request) //store new record in DB. return uniquely generated ticket ID
    {
        $request->validate([
            'fname'      => 'required',
            'sname'      => 'required',
            'address'    => 'required',
            'guestType'  => 'required|integer|between:1,2',
            'accompName' => 'required_if:guestType,==,2',
            'accompNum'  => 'required_if:guestType,==,2',
        ]);

        $record = new Record;

        $record->firstname                  = $request->fname;
        $record->surname                    = $request->sname;
        $record->address                    = $request->address;
        $record->guest_type                 = $request->guestType;
        $record->accompanying_member_name   = $request->accompName;
        $record->accompanying_member_number = $request->accompNum;

        $record->save();

        return $record->id;
    }
    
    public function deleteRecord(Request $request) //soft delete specified record
    {
        Record::find($request->recordID)->delete();

        return response('record deleted', 200);
    }
}