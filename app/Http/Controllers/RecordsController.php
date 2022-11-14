<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Record;

class RecordsController extends Controller
{
    private $headersArray;

    public function __construct()
    {
        $this->headersArray = array(
            "First Name"    => "firstname",
            "Surname"       => "surname",
            "Address"       => "address",
            "Guest Type"    => "guest_type",
            "Member Name"   => "accompanying_member_name",
            "Member Number" => "accompanying_member_number",
            "Check In Time" => "created_at",
            "Ticket Number" => "id",
        );
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response(null,204);
        }
    }

    public function logout(Request $request) {
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
    
    //return all records that match the query filters 
    public function exportRecords(Request $request) {

        $category = $this->getCategory($request->category);
        $headerKeys = array_keys($this->headersArray);
        $rowNumber = 1;

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headerKeys, null, "A{$rowNumber}"); //print next row to excel file

        $rowNumber++;

        $records = Record::where($category, 'LIKE', '%' . $request->queryString . '%')->get()->toArray();
        
        foreach($records as $record) {
            $dataRow = [];

            foreach ($this->headersArray as $column) {
                $tmpValue = isset($record[$column]) ? $record[$column] : ' '; //if an attribute doesn't exist, use empty space ' '
                $dataRow += [$column => $tmpValue];
            }

            $sheet->fromArray($dataRow, null, "A{$rowNumber}"); //print next row to excel file
            $rowNumber++;
        }
        
        //------------------------formatting--------------------------------------------
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $rowNumber = 1; //reset previously used counter

        foreach (range('A', $highestColumn) as $col) { //set cell width to auto based on content
            $sheet->getColumnDimension($col)->setAutoSize(true);
        } 

        while ($rowNumber <= $highestRow) { //every other column has a light grey background
            if (($rowNumber % 2 != 0) && ($rowNumber != 1)) {
                $sheet->getStyle("A{$rowNumber}:{$highestColumn}{$rowNumber}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('efefef');
            }

            $rowNumber++;
        }

        //header row styling
        $sheet->getStyle('1:1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('1:1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->getStyle("A1:{$highestColumn}1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('404040');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'export.xlsx');
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

    protected function getCategory($query)
    {
        switch ($query) {
            case 'id'     : return 'id';
            case 'surname': return 'surname';
            case 'date'   : return 'created_at';
        }
    }
}