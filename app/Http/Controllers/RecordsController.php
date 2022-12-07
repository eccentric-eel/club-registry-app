<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Record;

class RecordsController extends Controller
{
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
        if (Auth::check()) {
            return view('index');
        }  

        return redirect('/admin');
    }

    //return all records from DB
    public function queryRecords(Request $request)
    {
        $category = $this->getCategory($request->category);

        return Record::where($category, 'LIKE', '%' . $request->queryString . '%')->paginate(100);
    }
 
    //store new record in DB. return uniquely generated ticket ID
    public function store(Request $request)
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

    
    public function destroy($id) //delete record
    {

    }

    protected function getCategory($query)
    {
        switch ($query) {
            case 'id'     : return 'id';
            case 'surname': return 'surname';
            case 'date'   : return 'created_at';
        }
    }
}