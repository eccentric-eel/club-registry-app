<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Record;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $headerKeys = array_keys(config('global.tableHeaders'));
        $rowNumber = 1;

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headerKeys, null, "A{$rowNumber}"); //print next row to excel file

        $rowNumber++;

        $sortCol = array_values(config('global.tableHeaders'))[$request->sortCol];
        $sortDirection = ($request->sortAsc) ? 'asc' : 'desc';

        $startDate = ($request->startDate) ? Carbon::parse($request->startDate) : new Carbon('1970-01-01');
        $endDate   = ($request->endDate)   ? Carbon::parse($request->endDate)   : new Carbon('2100-12-31');
        
        $records = Record::where(function ($query) use ($request) {
            $query->where('firstname', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('surname', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('address', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('accompanying_member_name',   'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('accompanying_member_number', 'LIKE', '%' . $request->queryString . '%')
                  ->orWhere('id', 'LIKE', '%' . $request->queryString . '%');
            })->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate) 
              ->orderBy($sortCol, $sortDirection)
              ->get();

        foreach($records as $record) {
            $dataRow = [];

            foreach (config('global.tableHeaders') as $column) {
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
}