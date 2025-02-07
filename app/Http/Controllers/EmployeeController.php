<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;

class EmployeeController extends Controller
{
    // Display the upload form
    public function showHomePage()
    {
        return "//Silence is golden";
    }

    public function showUploadForm()
    {
        return view('employees.upload');
    }

    // Handle the uploaded employee report file
    public function importEmployees(Request $request)
    {
        // Validate the file
        $request->validate([
            'employee_file' => 'required|mimes:xlsx,csv',
        ]);

        try {

            $import = new EmployeesImport();

            // Import the employee data
            Excel::import($import, $request->file('employee_file'));

            // Access emails_to_create from the $import instance
            $emailsToCreate = $import->emails_to_create;
            $emailsToDelete = $import->emails_to_delete;
            $emailsToBlock = $import->emails_to_block;

            // Debugging (if needed)
            //echo "<pre>"; print_r($emailsToCreate); die;

            return redirect()->back()->with('success', 'Employee data imported successfully!')
            ->with('emails_to_delete', $emailsToDelete)
            ->with('emails_to_create', $emailsToCreate)
            ->with('emails_to_block', $emailsToBlock);
    
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing employee data: ' . $e->getMessage());
        }
    }
}
