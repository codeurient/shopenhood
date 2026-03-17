<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
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
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof TokenMismatchException) {
            $redirect = $this->redirectForExpiredSession($request);

            if ($redirect !== null) {
                return $redirect;
            }
        }

        return parent::render($request, $e);
    }

    /**
     * Redirect to the appropriate login page when the session has expired on a logout route.
     */
    protected function redirectForExpiredSession(Request $request): ?RedirectResponse
    {
        if ($request->is('logout')) {
            return redirect()->route('login')->with('status', 'Your session expired. You have been logged out.');
        }

        if ($request->is('admin/logout')) {
            return redirect()->route('admin.login')->with('status', 'Your session expired. You have been logged out.');
        }

        return null;
    }
}
