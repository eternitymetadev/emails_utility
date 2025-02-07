<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailService
{

    public static function authenticate()
    {
        try {
            $loginUrl = 'https://api.rediffmailpro.com/eproadminapi/LoginUser';
    
            $credentials = [
                'FormName' => 'existing',
                'login' => 'admin@shadow.express',
                'passwd' => 'Demo@1234$$@1',      
                'output' => 'xml',            
                'remember' => '1'
            ];
    
            // Make POST request
            $response = Http::asForm()->post($loginUrl, $credentials);

            if ($response->successful()) {
            
                $xmlResponse = simplexml_load_string($response->body());
               
                if ($xmlResponse->BODY->Rmail->Status == '0') {
                    return [
                        'Rm' => (string) $xmlResponse->BODY->Rmail->Rm,
                        'Rl' => (string) $xmlResponse->BODY->Rmail->Rl,
                        'Rsc' => (string) $xmlResponse->BODY->Rmail->Rsc,
                        'Rt' => (string) $xmlResponse->BODY->Rmail->Rt
                    ];
                } else {
                    Log::error('Rediff API Authentication Failed: ' . $xmlResponse->Msg);
                    return null;
                }
            } else {
                Log::error('Rediff API Authentication Error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error during Rediff API Authentication: ' . $e->getMessage());
            return null;
        }
    }
    
    // Create a new email for a user via the Rediff API
    public static function createEmail($employeeData,$authData)
    {
        //echo "<pre>";print_r('created');die;
        try {

            //echo "<pre>"; print_r($authData);die;
            
            $officialEmail = $employeeData['official_email_id'];
            $companydomain = substr(strrchr($officialEmail, "@"), 1);
            $userid = substr($officialEmail, 0, strpos($officialEmail, '@'));

            // Prepare request data
            $postData = [
                'domain_name' => $companydomain,
                'fname' => $employeeData['employee_name'],
                'sname' => $employeeData['employee_name'],
                'code' => $employeeData['employee_code'],
                'userid' => $userid,
                'passwd' => $employeeData['mobile_no'],
                'pwd_change_at_firstlogin' => 'N',
                'mobile' => $employeeData['mobile_no'],
                'userSpace' => 0,
                'altemail' => $employeeData['personal_email_id'],
                'status' => 'A',
                'segment' => '1',
                'timezone' => 'Asia/Kolkata',
                'country_code' => '91',
                'add_user.x' => '32',
                'add_user.y' => '11',
                'controller_action' => 'addUser',
                'userdomainname' => $companydomain,
                'login' => 'admin@shadow.express',
                'output' => 'json',
            ];

            $cookies = "Rm=" . $authData['Rm'] . "; Rsc=" . $authData['Rsc'] . "; Rl=" . $authData['Rl'] . "; Rt=" . $authData['Rt'];

            // Send request to Rediff API
            $response = Http::withHeaders([
                'Cookie' => $cookies, // Include session tokens
            ])->asForm()->post('https://api.rediffmailpro.com/eproadminapi/AddUser', $postData);
            
            // Check if response was successful
            
            if ($response->successful()) {
                $jsonResponse = json_decode($response->body(), true);
                //echo "<pre>"; print_r($jsonResponse['Status']);die;
                if($jsonResponse['Status'] == 'Success'){
                    return 1;
                }else{
                    return 0;
                }
            } else {
                Log::error('Failed to create email for employee: ' . $employeeData['employee_code'] . ' - ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error creating email for employee: ' . $employeeData['employee_code'] . ' - ' . $e->getMessage());
            return null;
        }
    }


    // Block the existing email through the Rediff API
    public static function blockEmail($email, $employeeCode, $authData)
    {
        //echo "<pre>";print_r('blocked');die;
        $companydomain = substr(strrchr($email, "@"), 1);
        $userWithoutDomain = strstr($email, '@', true);
    
        $url = 'https://api.rediffmailpro.com/eproadminapi/UpdateUserStatus';
    
        $postData = [
            'controller' => 'Inactive',  // Block the email
            'output' => 'json',
            'login' => 'admin@shadow.express',  // Admin email
            'userids[]' => $userWithoutDomain,
            'employee_codes[]' => $employeeCode,
            'userdomainname' => $companydomain,
            'session_id' => $authData['Rsc']  // Use session ID obtained from authentication
        ];
    
        // Add cookies from authentication data
        $cookies = "Rm={$authData['Rm']}; Rsc={$authData['Rsc']}; Rl={$authData['Rl']}; Rt={$authData['Rt']}";
    
        // Make POST request with cookies
        $response = Http::withHeaders([
            'Cookie' => $cookies,
        ])->asForm()->post($url, $postData);
    
        if ($response->successful()) {
            return true;  // Successfully blocked the email
        } else {
            Log::error('Failed to block email: ' . $email . ' - ' . $response->body());
            return false;
        }
    }
    
    
    // Delete the existing email through the Rediff API
    public static function deleteEmail($emailToDelete, $authData)
    {

        //echo "<pre>"; print_r($authData);die;
        // Endpoint for deleting user
        $deleteUrl = 'https://api.rediffmailpro.com/eproadminapi/DeleteUser';
    
        // Prepare parameters for deletion
        $postData = [
            'del_user' => $emailToDelete,                  
            'controller_action' => 'Delete',              
            'login' => $authData['Rl'],                   
            'output' => 'json',                           
            'session_id' => $authData['Rsc'],              
        ];
    
        // Prepare cookie header
        $cookies = "Rm={$authData['Rm']}; Rsc={$authData['Rsc']}; Rl={$authData['Rl']}; Rt={$authData['Rt']}";
    
        // Make DELETE request
        $response = Http::withHeaders([
            'Cookie' => $cookies,  // Pass authentication cookies
        ])->asForm()->post($deleteUrl, $postData);
    
        // Handle response
        if ($response->successful()) {
            $jsonResponse = json_decode($response->body(), true);
            //echo "<pre>"; print_r($jsonResponse['Status']);die;
            if($jsonResponse['Status'] == 'Success'){
                return 1;
            }else{
                return 0;
            }
    
        } else {
            // Log failure and return error
            Log::error('Failed to delete email: ' . $response->body());
            return [
                'message' => 'Request failed. Unable to delete email.',
                'status' => 'Failure',
                'statuscode' => 500,
                'data' => null
            ];
        }
    }
    

}
