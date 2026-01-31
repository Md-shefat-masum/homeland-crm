<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Actions\Auth\LoginAction;
use App\Http\Actions\Auth\RefreshTokenAction;
use App\Http\Actions\Auth\LogoutAction;
use App\Http\Actions\Auth\MeAction;
use App\Http\Actions\Auth\SendPasswordResetCodeAction;
use App\Http\Actions\Auth\ResetPasswordAction;
use App\Http\Actions\Auth\RegisterAction;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, LoginAction $action)
    {
        $result = $action->execute($request->all());

        if (!$result['success']) {
            return response()->json($result, 401);
        }

        return response()->json($result);
    }

    public function me(MeAction $action)
    {
        $result = $action->execute();

        if (!$result['success']) {
            return response()->json($result, 401);
        }

        return response()->json($result);
    }

    public function logout(Request $request, LogoutAction $action)
    {
        $deviceId = $request->input('device_id');
        $result = $action->execute($deviceId);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    public function refresh(RefreshTokenAction $action)
    {
        $result = $action->execute();

        if (!$result['success']) {
            return response()->json($result, 401);
        }

        return response()->json($result);
    }

    public function sendPasswordResetCode(Request $request, SendPasswordResetCodeAction $action)
    {
        $result = $action->execute($request->all());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    public function resetPassword(Request $request, ResetPasswordAction $action)
    {
        $result = $action->execute($request->all());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    public function register(Request $request, RegisterAction $action)
    {
        $result = $action->execute($request->all());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result, 201);
    }
}

