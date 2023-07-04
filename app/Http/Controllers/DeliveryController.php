<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeliveryController extends Controller
{
    public function createPackage(Request $request)
    {
        $address = $request->validate(['address'=>'required']);
        $customer = $this->getCustomer($request);

        try {
            $validatedPackageData = $request->validate([
                'width' => 'required',
                'height' => 'required',
                'length' => 'required',
                'weight' => 'required'
            ]);

            $validatedPackageData['customer_id'] = $customer->id;
            $package = Package::create($validatedPackageData);

            return $this->sendDelivery($customer, $package, $address['address']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create package',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getCustomer(Request $request){
        try {
            $validatedCustomerData = $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'middle_name' => 'required',
                'phone_number' => ['required', 'regex:/^\+380(50|66|95|99|67|68|96|97|98|63|93)\d{7}$/'],
                'email' => 'required|email',
            ]);

            $customer = Customer::where('email', $validatedCustomerData['email'])->first();

            if (!$customer) {
                $customer = Customer::create($validatedCustomerData);
            }
            return $customer;
        }catch(\Exception $e){
            return response()->json([
                'error' => 'Failed to create customer',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendDelivery(Customer $customer, Package $package, String $address){
        $url = "https://novaposhta.test/api/delivery";
        $data = [
            'customer_name' => "$customer->last_name $customer->first_name $customer->middle_name",
            'phone_number' => $customer->phone_number,
            'email' => $customer->email,
            'sender_address' => config('app.sender_address'),
            'delivery_address' => $address,
            'package'=>$package,
        ];
        $response = Http::post($url, $data);
        $responseData = $response->json();
        return response()->json($responseData);
    }
}
