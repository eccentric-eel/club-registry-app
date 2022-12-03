<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Record;

class ExportController extends Controller
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

    public function __invoke(Request $request)
    {
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

    protected function getCategory($query)
    {
        switch ($query) {
            case 'id'     : return 'id';
            case 'surname': return 'surname';
            case 'date'   : return 'created_at';
        }
    }
}
