<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Build a standardized JSON response
     *
     * @param int $status HTTP status code
     * @param string $message Response message
     * @param mixed $data Response data (optional)
     * @param array $errors Error details (optional)
     * @return \Illuminate\Http\JsonResponse
     */
    public static function json($status, $message, $data = null, $errors = null)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Return a success response (200 OK)
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, $message = 'Success')
    {
        return self::json(200, $message, $data);
    }

    /**
     * Return a created response (201 Created)
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function created($data = null, $message = 'Resource created successfully')
    {
        return self::json(201, $message, $data);
    }

    /**
     * Return a accepted response (202 Accepted)
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function accepted($data = null, $message = 'Accepted')
    {
        return self::json(202, $message, $data);
    }

    /**
     * Return a bad request response (400 Bad Request)
     *
     * @param mixed $errors Validation errors(optional)
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function badRequest($errors = null, $message = 'Bad request')
    {
        return self::json(400, $message, null, $errors);
    }

    /**
     * Return an unauthorized response (401 Unauthorized)
     *
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized($message = 'Unauthorized')
    {
        return self::json(401, $message);
    }

    /**
     * Return a forbidden response (403 Forbidden)
     *
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function forbidden($message = 'Forbidden')
    {
        return self::json(403, $message);
    }

    /**
     * Return a not found response (404 Not Found)
     *
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound($message = 'Resource not found')
    {
        return self::json(404, $message);
    }

    /**
     * Return a validation error response (422 Unprocessable Entity)
     *
     * @param array $errors Validation errors
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function validationError($message = 'Validation failed', $errors = [])
    {
        return self::json(422, $message, null, $errors);
    }

    /**
     * Return a server error response (500 Internal Server Error)
     *
     * @param mixed $details Error details
     * @param string $message Error message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function serverError($details = null, $message = 'Server error')
    {
        return self::json(500, $message);
    }
} 