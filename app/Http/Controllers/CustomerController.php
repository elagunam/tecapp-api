<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    public function searchCustomer(Request $request){
        if(!$request->filled('token')){
            $response['status'] = false;
            $response['message'] = 'Permiso denegado';
        }

        if(!$request->filled('search')){
            $response['status'] = false;
            $response['message'] = 'Debe indicar un criterio de búsqueda';
        }


        $customerQuery = "SELECT  * FROM customers as c";
        if(is_numeric($request->search)){
            $customerQuery.= " WHERE  c.id = $request->search";
        }else{
            $customerQuery.= " WHERE c.full_name LIKE '%$request->search%'";
        }
        $customerQuery.= " ORDER BY c.full_name";

        $customers = DB::select($customerQuery);

        if(sizeof($customers) < 1){
            $response['status'] = false;
            $response['message'] = 'No se encontraron clientes con los criterios de búsqueda';
            return response()->json($response);
        }

        $response['status'] = true;
        $response['customers'] = $customers;
        $response['message'] = 'Información de clientes localizada';
        return response()->json($response);
    }

    public function saveCustomer(Request $request){
        if(!$request->filled('token')){
            $response['status'] = false;
            $response['message'] = 'Permiso denegado';
        }

        if(!$request->filled(['full_name', 'address', 'birth', 'gender'])){
            $response['status'] = false;
            $response['message'] = 'Complete el formulario correctamente para guardar el cliente';
        }

        $customer['full_name'] = $request->full_name;
        $customer['address'] = $request->address;
        $customer['birth'] = $request->birth;
        $customer['gender'] = $request->gender;

        $customerId = DB::table('customers')->insertGetId($customer);
        if(!$customerId){
            $response['status'] = false;
            $response['message'] = 'No se pudo guardar la información de cliente';
            return $response;
        }

        $response['status'] = true;
        $response['customer_id'] = $customerId;
        $response['message'] = 'Cliente creado correctamente';
        return $response;

    }


    public function getCustomerById(Request $request){
        $customerId = $request->id;


        if(!$request->filled('token')){
            $response['status'] = false;
            $response['message'] = 'Permiso denegado';
        }

        $customerQuery = "SELECT c.*, TIMESTAMPDIFF(YEAR, c.birth, CURDATE()) AS age
        FROM customers as c
        WHERE  c.id = $customerId";

        $customers = DB::select($customerQuery);

        if(sizeof($customers) < 1){
            $response['status'] = false;
            $response['message'] = 'No se encontró información del cliente';
            return response()->json($response);
        }

        $response['status'] = true;
        $response['customer'] = $customers[0];
        $response['message'] = 'Información del cliente localizada';
        return response()->json($response);

    }
    
}
