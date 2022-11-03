<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RecordsController extends Controller
{
    //use vue router to display records page
    public function index()
    {        
        return view('index');
    }

    //return all records from DB
    public function queryRecords(Request $request)
    {
        switch ($request->category) {
            case 'id':
                $category = 'id';
                break;
            case 'surname':
                $category = 'surname';
                break;
            case 'date':
                $category = 'created_at';
                break;
        }

        return Record::where($category, 'LIKE', '%' . $request->queryString . '%')->paginate(100);
    }
    
    //return all records that match the query filters 
    public function exportRecords(Request $request) {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $records = $this->queryRecords($request);
        

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'export.xlsx');
    }

    //store new record in DB. return uniquely generated ticket ID
    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required',
            'sname' => 'required',
            'address' => 'required',
            'guestType' => 'required|integer|between:1,2',
            'accompName' => 'required_if:guestType,==,2',
            'accompNum' => 'required_if:guestType,==,2',
        ]);

        $record = new Record;

        $record->firstname = $request->fname;
        $record->surname = $request->sname;
        $record->address = $request->address;
        $record->guest_type = $request->guestType;
        $record->accompanying_member_name = $request->accompName;
        $record->accompanying_member_number = $request->accompNum;

        $record->save();
    }

    //delete particular record from DB
    public function destroy($id)
    {
        //
    }
}