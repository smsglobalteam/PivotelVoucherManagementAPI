<?php

namespace App\Http\Controllers;

use App\Models\VoucherModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class SoapVoucherController extends Controller
{
    //
    public function SOAPGetAllVouchers(Request $request)
    {
        $vouchers = VoucherModel::get();

        // Create SOAP XML
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $envelope = $xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'SOAP-ENV:Envelope');
        $envelope->setAttribute('xmlns:SOAP-ENV', 'http://schemas.xmlsoap.org/soap/envelope/');
        $body = $xml->createElement('SOAP-ENV:Body');
        $response = $xml->createElement('response');
        $message = $xml->createElement('message', 'All vouchers displayed successfully');
        $results = $xml->createElement('results');

        foreach ($vouchers as $voucher) {
            $voucherNode = $xml->createElement('voucher');
            $voucherNode->appendChild($xml->createElement('id', $voucher->id));
            $voucherNode->appendChild($xml->createElement('voucher_code', $voucher->voucher_code));
            $voucherNode->appendChild($xml->createElement('value', $voucher->value));
            $voucherNode->appendChild($xml->createElement('expiry_date', $voucher->expiry_date));
            $voucherNode->appendChild($xml->createElement('status', $voucher->status));
            $voucherNode->appendChild($xml->createElement('service_reference', $voucher->service_reference));
            $voucherNode->appendChild($xml->createElement('created_by', $voucher->created_by));
            $voucherNode->appendChild($xml->createElement('created_at', $voucher->created_at));
            // Add more properties as needed
            $results->appendChild($voucherNode);
        }

        $response->appendChild($message);
        $response->appendChild($results);
        $body->appendChild($response);
        $envelope->appendChild($body);
        $xml->appendChild($envelope);

        // Set SOAP headers
        $headers = [
            'Content-Type' => 'text/xml; charset=utf-8',
            'Accept' => 'text/xml',
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
            'SOAPAction' => Config::get('soap.action')
        ];

        // Return the SOAP response
        return Response::make($xml->saveXML(), 200, $headers);
    }

    public function SOAPGetVoucherByCode($voucherCode)
    {
        $vouchers = VoucherModel::where('voucher_code', $voucherCode)->get();

        // Create SOAP XML
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $envelope = $xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'SOAP-ENV:Envelope');
        $envelope->setAttribute('xmlns:SOAP-ENV', 'http://schemas.xmlsoap.org/soap/envelope/');
        $body = $xml->createElement('SOAP-ENV:Body');
        $response = $xml->createElement('response');
        $message = $xml->createElement('message', 'All vouchers displayed successfully');
        $results = $xml->createElement('results');

        foreach ($vouchers as $voucher) {
            $voucherNode = $xml->createElement('voucher');
            $voucherNode->appendChild($xml->createElement('id', $voucher->id));
            $voucherNode->appendChild($xml->createElement('voucher_code', $voucher->voucher_code));
            $voucherNode->appendChild($xml->createElement('value', $voucher->value));
            $voucherNode->appendChild($xml->createElement('expiry_date', $voucher->expiry_date));
            $voucherNode->appendChild($xml->createElement('status', $voucher->status));
            $voucherNode->appendChild($xml->createElement('service_reference', $voucher->service_reference));
            $voucherNode->appendChild($xml->createElement('created_by', $voucher->created_by));
            $voucherNode->appendChild($xml->createElement('created_at', $voucher->created_at));
            // Add more properties as needed
            $results->appendChild($voucherNode);
        }

        $response->appendChild($message);
        $response->appendChild($results);
        $body->appendChild($response);
        $envelope->appendChild($body);
        $xml->appendChild($envelope);

        // Set SOAP headers
        $headers = [
            'Content-Type' => 'text/xml; charset=utf-8',
            'Accept' => 'text/xml',
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
            'SOAPAction' => Config::get('soap.action')
        ];

        // Return the SOAP response
        return Response::make($xml->saveXML(), 200, $headers);
    }

    public function SOAPCreateNewVoucher(Request $request)
    {
        $xmlData = $request->getContent();

        $validator = Validator::make(['xml' => $xmlData], [
            'xml' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => 'Invalid XML data',
            ], 400);
        }

        $xml = simplexml_load_string($xmlData);

        $value = (int) $xml->value;
        $expiryDate = isset($xml->expiry_date) ? (string) $xml->expiry_date : null;
        $serviceReference = isset($xml->service_reference) ? (string) $xml->service_reference : null;

        $validator = Validator::make(compact('value', 'expiryDate', 'serviceReference'), [
            'value' => 'required|integer',
            'expiryDate' => 'nullable|date_format:Y-m-d',
            'serviceReference' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => 'Invalid voucher data',
                'validation_errors' => $validator->errors(),
            ], 400);
        }

        function generateVoucherCode($length)
        {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            $max = strlen($characters) - 1;

            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $max)];
            }

            return $code;
        }

        $voucherCodeExists = true;
        $voucherCode = '';

        // Generate a unique voucher code
        while ($voucherCodeExists) {
            $voucherCode = generateVoucherCode(16);

            $voucherCodeExists = VoucherModel::where('voucher_code', $voucherCode)->exists();
        }

        $voucher = VoucherModel::create([
            'voucher_code' => $voucherCode,
            'value' => $value,
            'expiry_date' => $expiryDate,
            'status' => 'active',
            'service_reference' => $serviceReference,
            'created_by' => 1
        ]);

        $responseXml = '<response>';
        $responseXml .= '<message>Voucher created successfully</message>';
        $responseXml .= '<results>';
        $responseXml .= '<voucher>';
        $responseXml .= '<voucher_code>' . $voucher->voucher_code . '</voucher_code>';
        $responseXml .= '<value>' . $voucher->value . '</value>';
        $responseXml .= '<expiry_date>' . $voucher->expiry_date . '</expiry_date>';
        $responseXml .= '<status>' . $voucher->status . '</status>';
        $responseXml .= '<service_reference>' . $voucher->service_reference . '</service_reference>';
        $responseXml .= '<created_by>' . $voucher->created_by . '</created_by>';
        $responseXml .= '</voucher>';
        $responseXml .= '</results>';
        $responseXml .= '</response>';

        return Response::make($responseXml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}