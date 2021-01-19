<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConverterController extends Controller
{
    public $data;

    public function csvToJson(Request $request)
    {
        // dd($request);
        // dd($request->hasFile('csvfile'));
        // dd($request->file('csvfile')->extension());

        if (!$request->hasFile('csvfile')) {
            // dd('hello');
            return response()->json([
                'error' => 'No file uploaded'
            ], 400);
        }

        $extension = $request->file('csvfile')->extension();
        if (!in_array(strtolower($extension), ['csv', 'txt', 'xls'])) {
            return response()->json([
                'error' => 'Invalid file uploaded. Only csv file is allowed'
            ], 400);
        }

        $validateColumnNames = ['name', 'class', 'school'];

        $file = $request->file('csvfile');
        // if (in_array($extension, ['csv','txt','xls'])) {
        $column_names = array();
        $final_data = array();
        $errors = array();

        $file_data = file_get_contents($file);
        $data_array = array_map("str_getcsv", explode("\n", $file_data));

        //Extract the column names for each row
        $labels = array_shift($data_array);
        foreach ($labels as $label) {
            $column_names[] = strtolower($label);
        }

        //Check if the needed column names are available
        for ($i = 0; $i < count($validateColumnNames); $i++) {
            if (!in_array(strtolower($validateColumnNames[$i]), $column_names)) {
                array_push($errors, $validateColumnNames[$i] . ' column not found');
            }
        }

        //If a column doesn't exist, throw an error
        if (!empty($errors)) {
            return response()->json([
                'error' => $errors
            ], 400);
            // dd($errors);
        }

        //Extract data from the csv then combine with the column names as keys for each object returned
        $count = count($data_array) - 1;
        for ($j = 0; $j < $count; $j++) {
            $data = array_combine($column_names, $data_array[$j]);
            $final_data[$j] = $data;
        }

        $this->data = json_encode($final_data);
        return $this->data;
    }

    function random_strings($length_of_string)
    {
        // String of all alphanumeric character 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shufle the $str_result and returns substring 
        // of specified length 
        return substr(str_shuffle($str_result), 0, $length_of_string);
    }

    public function jsonToCsv(Request $request)
    {
        header('Content-type: text/plain; charset=UTF-8');

        if ($request->hasFile('jsonfile')) {
            $json = $request->file('jsonfile');
            $extension = $json->extension();
            $json = file_get_contents($json);
            if (!in_array(strtolower($extension), ['json'])) {
                return response()->json([
                    'error' => 'Invalid file uploaded. Only json file is allowed'
                ], 400);
            }
        } else {
            $json = '
            [
                { "name": "MG", "class": "SS1", "school": "GSS ENEKA" },
                { "name": "MGI", "class": "SS2", "school": "PRIME GATE" },
                { "name": "Mimi", "class": " JSS2", "school": " PH POLY" }
            ]';
        }


        $fcsv = fopen($this->random_strings(4) . '.csv', 'w');
        $array = json_decode($json, true);
        $csv = '';

        $header = false;
        foreach ($array as $line) {
            if (empty($header)) {
                $header = array_keys($line);
                fputcsv($fcsv, $header);
                $header = array_flip($header);
            }

            $line_array = array();

            foreach ($line as $value) {
                print_r($value);
                array_push($line_array, $value);
            }
            fputcsv($fcsv, $line_array);
        }

        //close CSV file after write
        fclose($fcsv);
        echo "<br /><br />";
        echo "Success";
    }
}
