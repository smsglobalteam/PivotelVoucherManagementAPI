<?php

namespace App\Mail;

use mPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $value;
    public $first_name;
    public $last_name;

    public function __construct($request)
    {
        //
        $this->value = $request;
        $this->first_name = $request->first_name;
        $this->last_name = $request->last_name;
    }

    public function build()
    {
        // $mpdf = new mPDF();
        // $mpdf->WriteHTML(view('emails.application-submitted-pdf', [
        //     'value' => $this->value,
        // ])->render());

        // $pdfContent = $mpdf->Output('', 'S');

        return $this->subject('Application Submitted - ' .$this->first_name .' '. $this->last_name)
            ->markdown('emails.application-submitted-mail', [
                'value' => $this->value,
            ]);
            // ->attachData($pdfContent, 'Application - ' . $this->last_name . '.pdf', [
            //     'mime' => 'application/pdf',
            // ]);
    }
}