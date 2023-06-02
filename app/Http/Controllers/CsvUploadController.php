<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Models\User;
use Session;
use App\Jobs\SendEmailAfterCSVProcessing;
use Illuminate\Support\Facades\Queue;
class CsvUploadController extends Controller
{
    public function index()
    {
        return view('csv_upload.index');
    }
	 public function store(Request $request)
    {
		$login_user_name = Auth::user()->user_name;
		$login_user_id = Auth::user()->id;
		$count = $login_user_count = 0;
		$processedData = '';
		$process_data = '';
        if ($request->hasFile('csv_files')) {
            $files = $request->file('csv_files');

            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $tempPath = $file->getRealPath();

                // Read the CSV file and get the data
                $csvData = file_get_contents($tempPath);
                $rows = explode("\n", $csvData);
                $header = str_getcsv(array_shift($rows));
				$header = array_filter($header);
				if(isset($header) && !empty($header)) {
					$column_array = array('first_name','last_name','phone','email','address');
					foreach($header as $header_value) {
						if(isset($header_value) && $header_value != '' && ! in_array($header_value,$column_array)) {
							Session::flash('message', "if Invalid file.Heading name  should be 'first_name','last_name','phone','email','address'!");
							return redirect()->route("file.index");
						}
					}
					
				} else {
					Session::flash('message', "else Invalid file. Should have heading name 'first_name','last_name','phone','email','address'!");
					return redirect()->route("file.index");
				}
                // Process the rows and insert data into the database
				
                foreach ($rows as $row) {
                    $rowData = str_getcsv($row);
					$remove_empty_value = array_filter($rowData);
					if(!empty($remove_empty_value)) {
						$count = $count + 1;
							$data = [];
						foreach ($header as $key => $dbColumn) {
						
							$data[$dbColumn] = $rowData[$key] ?? null;
							// Assuming first name is user name
							if( $dbColumn == 'first_name' && $rowData[$key] == $login_user_name) {
								++$login_user_count;
							}								
						} 
						$process_str = implode(" ",$data);
						$processedData .= $process_str;
						DB::table('csv_uploads')->insert($data);
					}
					
                }   
            }
			$user = User::find($login_user_id);
			//  Due to invalid mail detail getting error so code hide
			// Queue::push(new SendEmailAfterCSVProcessing($user, $processedData));
			Session::flash('message', "CSV files uploaded successfully with total records ".$count." and current login user count ".$login_user_count.".!");
			return redirect()->route("file.index");
        }

        Session::flash('message', "Invalid file.Heading name  should be 'first_name','last_name','phone','email','address'!");
		return redirect()->route("file.index");
    }

}
