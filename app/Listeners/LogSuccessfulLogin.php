<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    public function __construct(protected Request $request) {}

    public function handle(Login $event): void
    {
        $userAgent = $this->request->userAgent();
        $parsed = $this->parseUserAgent($userAgent);

        $ipAddress = $this->request->ip();
        $isSuspicious = $this->checkIfSuspicious($event->user->id, $ipAddress);

        LoginHistory::create([
            'user_id' => $event->user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device' => $parsed['device'],
            'browser' => $parsed['browser'],
            'platform' => $parsed['platform'],
            'country' => null,
            'city' => null,
            'is_suspicious' => $isSuspicious,
            'logged_in_at' => now(),
        ]);
    }

    protected function parseUserAgent(?string $userAgent): array
    {
        $result = [
            'device' => 'Unknown',
            'browser' => 'Unknown',
            'platform' => 'Unknown',
        ];

        if (! $userAgent) {
            return $result;
        }

        if (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) {
            $result['device'] = 'Mobile';
        } elseif (preg_match('/Tablet/i', $userAgent)) {
            $result['device'] = 'Tablet';
        } else {
            $result['device'] = 'Desktop';
        }

        if (preg_match('/Chrome\/[\d.]+/i', $userAgent) && ! preg_match('/Edg/i', $userAgent)) {
            $result['browser'] = 'Chrome';
        } elseif (preg_match('/Firefox\/[\d.]+/i', $userAgent)) {
            $result['browser'] = 'Firefox';
        } elseif (preg_match('/Safari\/[\d.]+/i', $userAgent) && ! preg_match('/Chrome/i', $userAgent)) {
            $result['browser'] = 'Safari';
        } elseif (preg_match('/Edg\/[\d.]+/i', $userAgent)) {
            $result['browser'] = 'Edge';
        } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
            $result['browser'] = 'Internet Explorer';
        }

        if (preg_match('/Windows/i', $userAgent)) {
            $result['platform'] = 'Windows';
        } elseif (preg_match('/Macintosh|Mac OS/i', $userAgent)) {
            $result['platform'] = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $result['platform'] = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $result['platform'] = 'Android';
        } elseif (preg_match('/iPhone|iPad|iOS/i', $userAgent)) {
            $result['platform'] = 'iOS';
        }

        return $result;
    }

    protected function checkIfSuspicious(int $userId, string $ipAddress): bool
    {
        $recentLogins = LoginHistory::forUser($userId)
            ->recent(7)
            ->count();

        if ($recentLogins === 0) {
            return false;
        }

        $knownIps = LoginHistory::forUser($userId)
            ->recent(30)
            ->pluck('ip_address')
            ->unique()
            ->toArray();

        if (! in_array($ipAddress, $knownIps) && count($knownIps) > 0) {
            return true;
        }

        $loginsLastHour = LoginHistory::forUser($userId)
            ->where('logged_in_at', '>=', now()->subHour())
            ->count();

        if ($loginsLastHour >= 5) {
            return true;
        }

        return false;
    }
}
