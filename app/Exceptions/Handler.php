<?php

namespace App\Exceptions;

use Throwable;
use App\Helpers\ResponseHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Contracts\Container\BindingResolutionException;
use ErrorException;
use PDOException;

class Handler
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

    /**
     * Exception to response mapping for API requests
     * 
     * @var array<class-string<Throwable>, callable>
     */
    protected $exceptionMap = [];

    /**
     * Constructor - initialize the exception map
     */
    public function __construct()
    {
        $this->initializeExceptionMap();
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $e)
    {
        return $this->handleApiException($e, $request);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return bool|null
     */
    public function report(Throwable $e)
    {
        // Add custom reporting logic here
        
        // For example, don't report validation exceptions
        if ($e instanceof ValidationException) {
            return false;
        }
        
        // Add extra context for database errors
        if ($e instanceof QueryException) {
            // \Log::error('Database error: ' . $e->getMessage(), [
            //     'sql' => $e->getSql(),
            //     'bindings' => $e->getBindings()
            // ]);
        }
        
        return null; // Let Laravel handle default reporting
    }

    /**
     * Initialize the exception to response mapping
     */
    protected function initializeExceptionMap(): void
    {
        // Laravel's built-in exceptions
        $this->exceptionMap = [
            // Authentication & Authorization
            AuthenticationException::class => fn ($e) => 
                ResponseHelper::unauthorized('You must be logged in to access this resource'),
                
            AuthorizationException::class => fn ($e) => 
                ResponseHelper::forbidden($e->getMessage() ?: 'You do not have permission to perform this action'),
                
            // Database & Models
            ModelNotFoundException::class => fn ($e) => 
                ResponseHelper::notFound(class_basename($e->getModel()) . ' not found'),
                
            QueryException::class => fn ($e) => 
                $this->handleQueryException($e),
                
            MassAssignmentException::class => fn ($e) => 
                ResponseHelper::badRequest('Mass assignment error: ' . $e->getMessage()),
                
            RelationNotFoundException::class => fn ($e) => 
                ResponseHelper::badRequest('Relationship error: ' . $e->getMessage()),
                
            JsonEncodingException::class => fn ($e) => 
                ResponseHelper::badRequest('Invalid JSON data: ' . $e->getMessage()),
                
            // Validation & Forms
            ValidationException::class => fn ($e) => 
                ResponseHelper::validationError('Validation failed', $e->errors()),
                
            TokenMismatchException::class => fn ($e) => 
                ResponseHelper::badRequest('CSRF token mismatch. Please try again'),
                
            // HTTP Exceptions
            NotFoundHttpException::class => fn ($e) => 
                ResponseHelper::notFound('The requested resource was not found'),
                
            MethodNotAllowedHttpException::class => fn ($e) => 
                ResponseHelper::badRequest(['allowed' => $e->getHeaders()['Allow'] ?? null], 'Method not allowed'),
                
            AccessDeniedHttpException::class => fn ($e) => 
                ResponseHelper::forbidden($e->getMessage() ?: 'Access denied'),
                
            HttpException::class => fn ($e) => 
                ResponseHelper::json($e->getStatusCode(), $e->getMessage() ?: 'HTTP error'),
                
            // Rate Limiting
            ThrottleRequestsException::class => fn ($e) => 
                ResponseHelper::json(429, 'Too many requests. Please try again later', null, [
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null
                ]),
                
            // Request Size
            PostTooLargeException::class => fn ($e) => 
                ResponseHelper::badRequest('The uploaded file is too large'),
                
            // Container & Service
            BindingResolutionException::class => fn ($e) => 
                ResponseHelper::serverError('Service resolution error'),
                
            // PHP & System
            ErrorException::class => fn ($e) => 
                $this->handleErrorException($e),
                
            PDOException::class => fn ($e) => 
                ResponseHelper::serverError('Database connection error'),
        ];
    }

    /**
     * Handle API exceptions using the exception map
     */
    protected function handleApiException(Throwable $exception, $request)
    {
        // Get the exception class
        $exceptionClass = get_class($exception);
        
        // Check if we have a direct handler for this exception
        if (isset($this->exceptionMap[$exceptionClass])) {
            $response = $this->exceptionMap[$exceptionClass]($exception);
        } else {
            // Default to server error for unhandled exceptions
            $response = ResponseHelper::serverError('Server Error');
        }
        
        // Add debug information for server errors when in debug mode
        if ((config('app.debug') || config('app.env') === 'local') && $response->getStatusCode() >= 500) {
            $responseData = json_decode($response->getContent(), true);
            $responseData['debug'] = $this->getDebugData($exception);
            
            return response()->json($responseData, $response->getStatusCode());
        }
        
        return $response;
    }

    /**
     * Get debug data for an exception
     */
    protected function getDebugData(Throwable $exception): array
    {
        $debugData = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => collect($exception->getTrace())->map(function ($trace) {
                return \Illuminate\Support\Arr::except($trace, ['args']);
            })->all(),
        ];
        
        // Add severity information for ErrorException
        if ($exception instanceof ErrorException) {
            $debugData['severity'] = $this->getErrorSeverityString($exception->getSeverity());
        }
        
        return $debugData;
    }

    /**
     * Handle PHP errors converted to exceptions
     */
    protected function handleErrorException(ErrorException $e)
    {
        // For ErrorException, we'll just return a server error
        // The debug information will be added by handleApiException if needed
        return ResponseHelper::serverError('PHP Error: ' . $e->getMessage());
    }

    /**
     * Convert PHP error severity to string
     */
    protected function getErrorSeverityString($severity)
    {
        return match ($severity) {
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Standards',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
            default => 'Unknown Error'
        };
    }

    /**
     * Handle database query exceptions with more detail
     */
    protected function handleQueryException(QueryException $e)
    {
        $message = $e->getMessage();
        
        // Foreign key constraint failures
        if (str_contains($message, 'foreign key constraint fails')) {
            return ResponseHelper::badRequest('Database relation error: Cannot delete or update a related record');
        }
        
        // Duplicate entry errors
        if (str_contains($message, 'Duplicate entry')) {
            return ResponseHelper::badRequest('A record with this information already exists');
        }
        
        // Column not found errors
        if (str_contains($message, 'Unknown column')) {
            return ResponseHelper::serverError('Database schema error');
        }
        
        // Table not found errors
        if (str_contains($message, 'Base table or view not found')) {
            return ResponseHelper::serverError('Database schema error');
        }
        
        return ResponseHelper::serverError('A database error occurred');
    }
} 