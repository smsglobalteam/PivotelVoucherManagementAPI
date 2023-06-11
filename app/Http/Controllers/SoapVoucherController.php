<?php

namespace App\Http\Controllers;

use App\Models\VoucherModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

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
}
