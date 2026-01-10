<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            $status = $exception->getStatusCode();

        if ($request->is('admin/*')) {
            return response()->view('backend.errors.error', [
                'code' => $status,
                'message' => $this->getDefaultMessage($status),
//                'message' => $exception->getMessage() ?: $this->getDefaultMessage($status),
            ], $status);
        }

        return response()->view('frontend.errors.error', [
            'code' => $status,
            'message' => $this->getDefaultMessage($status),
//            'message' => $exception->getMessage() ?: $this->getDefaultMessage($status),
        ], $status);
    }

        // Agar boshqa turdagi xatolik bo‘lsa
        return parent::render($request, $exception);
    }

    /**
     * Returns standard messages.
     */
    protected function getDefaultMessage($status)
    {
        return match ($status) {
            403 => 'Ушбу саҳифага рухсат йўқ.',
            404 => 'Саҳифа топилмади.',
            419 => 'Сессия якунланган. Илтимос, қайта уриниб кўринг.',
            429 => 'Жуда кўп сўров юборилди. Бироз кутиб туринг.',
            500 => 'Ички сервер хатоси.',
            503 => 'Сайт вақтинча ишламаяпти.',
            default => 'Номаълум хатолик юз берди.',
        };
    }
}
