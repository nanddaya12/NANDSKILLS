<?php

namespace App\Services;

class Google2FA
{
    public static function generateSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    public static function verifyKey(string $secret, string $key, int $window = 1): bool
    {
        $timestamp = floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            if (self::calculateCode($secret, $timestamp + $i) === $key) {
                return true;
            }
        }

        return false;
    }

    protected static function calculateCode(string $secret, int $timeSlice): string
    {
        $secretKey = self::base32Decode($secret);

        // Pack time slice to 64-bit binary
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);

        // Hash time with secret key
        $hmac = hash_hmac('sha1', $time, $secretKey, true);

        // Offset calculation
        $offset = ord($hmac[19]) & 0xf;

        // Dynamic truncation
        $hashpart = substr($hmac, $offset, 4);

        // Unpack value
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7fffffff;

        $modulo = pow(10, 6);
        $code = strval($value % $modulo);

        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    protected static function base32Decode(string $base32): string
    {
        $base32 = strtoupper($base32);
        if (!preg_match('/^[A-Z2-7]+$/', $base32)) {
            return '';
        }

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $map = array_flip(str_split($chars));

        $binary = '';
        foreach (str_split($base32) as $char) {
            $binary .= str_pad(decbin($map[$char]), 5, '0', STR_PAD_LEFT);
        }

        $bytes = str_split($binary, 8);
        $decoded = '';
        foreach ($bytes as $byte) {
            if (strlen($byte) === 8) {
                $decoded .= chr(bindec($byte));
            }
        }

        return $decoded;
    }
}
