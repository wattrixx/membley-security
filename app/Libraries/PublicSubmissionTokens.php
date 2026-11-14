<?php

namespace App\Libraries;

class PublicSubmissionTokens
{
    private const SESSION_KEY = 'public_submission_tokens';
    private const LIFETIME_SECONDS = 3600;
    private const MAX_TOKENS = 10;

    public function issue(string $form): string
    {
        $tokens = $this->activeTokens();
        $token = bin2hex(random_bytes(32));
        $tokens[hash('sha256', $token)] = [
            'form'    => $form,
            'expires' => time() + self::LIFETIME_SECONDS,
        ];

        if (count($tokens) > self::MAX_TOKENS) {
            $tokens = array_slice($tokens, -self::MAX_TOKENS, null, true);
        }

        session()->set(self::SESSION_KEY, $tokens);

        return $token;
    }

    public function isValid(string $form, string $token): bool
    {
        if (preg_match('/\A[a-f0-9]{64}\z/', $token) !== 1) {
            return false;
        }

        $entry = $this->activeTokens()[hash('sha256', $token)] ?? null;

        return is_array($entry)
            && ($entry['form'] ?? null) === $form
            && (int) ($entry['expires'] ?? 0) >= time();
    }

    public function consume(string $token): void
    {
        $tokens = $this->activeTokens();
        unset($tokens[hash('sha256', $token)]);
        session()->set(self::SESSION_KEY, $tokens);
    }

    /**
     * @return array<string, array{form: string, expires: int}>
     */
    private function activeTokens(): array
    {
        $stored = session()->get(self::SESSION_KEY);

        if (! is_array($stored)) {
            return [];
        }

        $now = time();
        $active = [];

        foreach ($stored as $key => $entry) {
            if (
                is_string($key)
                && is_array($entry)
                && is_string($entry['form'] ?? null)
                && (int) ($entry['expires'] ?? 0) >= $now
            ) {
                $active[$key] = [
                    'form'    => $entry['form'],
                    'expires' => (int) $entry['expires'],
                ];
            }
        }

        return $active;
    }
}
