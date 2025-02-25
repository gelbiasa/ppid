<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller as LaravelController;

class BaseController extends LaravelController
{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * Handle common API response format
     *
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiResponse($success = true, $message = '', $data = null, $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Handle exception and redirect with appropriate message
     *
     * @param \Exception $e
     * @param string $redirectRoute
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleException(\Exception $e, $redirectRoute = null)
    {
        if ($e instanceof ValidationException) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }

        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }

    /**
     * Execute an action and handle any exceptions
     *
     * @param \Closure $action
     * @param string $successMessage
     * @param string $successRedirect
     * @param string $errorRedirect
     * @return mixed
     */
    protected function executeAction(\Closure $action, $successMessage = 'Operasi berhasil', $successRedirect = null, $errorRedirect = null)
    {
        try {
            $result = $action();
            
            if (is_array($result) && isset($result['success'])) {
                if ($result['success']) {
                    $message = $result['message'] ?? $successMessage;
                    $redirect = $successRedirect ?: redirect()->back();
                    return redirect($redirect)->with('success', $message);
                } else {
                    $message = $result['message'] ?? 'Operasi gagal';
                    return redirect()->back()->with('error', $message)->withInput();
                }
            }
            
            return $result;
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}