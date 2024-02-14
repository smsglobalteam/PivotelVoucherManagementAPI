<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected $errorCodes = [
        'required' => '-5001',
        'unique' => '-5002',
        'email' => '-5003',
        'min' => '-5004',
        'max' => '-5005',
        'between' => '-5006',
        'in' => '-5007',
        'not_in' => '-5008',
        'numeric' => '-5009',
        'string' => '-5010',
        'date' => '-5011',
        'boolean' => '-5012',
        'confirmed' => '-5013',
        'different' => '-5014',
        'same' => '-5015',
        'size' => '-5016',
        'exists' => '-5017',
        'regex' => '-5018',
        'required_if' => '-5019',
        'required_unless' => '-5020',
        'required_with' => '-5021',
        'required_with_all' => '-5022',
        'required_without' => '-5023',
        'required_without_all' => '-5024',
        'array' => '-5025',
        'integer' => '-5026',
        'digits' => '-5027',
        'digits_between' => '-5028',
        'dimensions' => '-5029',
        'file' => '-5030',
        'image' => '-5031',
        'mimes' => '-5032',
        'mimetypes' => '-5033',
        'json' => '-5034',
        'url' => '-5035',
        'uuid' => '-5036',
    ];


    protected function invalidJson($request, ValidationException $exception)
    {
        $errors = $exception->errors();
        $customErrors = [];
        $returnCodes = [];

        foreach ($errors as $field => $message) {
            $failedRules = array_keys($exception->validator->failed()[$field]);
            $rule = $failedRules[0];

            // Log::debug("Detected rule for $field: $rule");

            $errorCode = $this->errorCodes[strtolower($rule)] ?? '-1000';

            $customErrors[$field] = $message;
            $returnCodes[$field] = $errorCode;
        }

        return response()->json([
            'message' => $exception->getMessage(),
            'errors' => $customErrors,
            'return_code' => $returnCodes,
        ], $exception->status);
    }


    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
