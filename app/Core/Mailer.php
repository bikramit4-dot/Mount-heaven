<?php

namespace App\Core;

class Mailer
{
    /**
     * Send an HTML email via SMTP using settings from the settings table.
     * Returns true on success, false on any failure (never throws).
     */
    public static function send(string $to, string $subject, string $html): bool
    {
        $host = trim((string) setting('smtp_host'));
        $port = (int) (setting('smtp_port') ?: 587);
        $user = (string) setting('smtp_user');
        $pass = (string) setting('smtp_pass');
        $from = trim((string) (setting('smtp_from') ?: $user));
        $fromName = trim((string) (setting('smtp_from_name')) ?: setting('site_name'));

        if ($host === '' || $from === '') {
            return false; // email not configured
        }

        try {
            $socket = self::connect($host, $port);
            self::expect($socket, [220]);

            self::cmd($socket, 'EHLO ' . self::heloName(), 250);
            if ((int) $port !== 465) {
                // Attempt STARTTLS when the server offers it.
                fwrite($socket, "STARTTLS\r\n");
                $resp = fgets($socket, 1024);
                if ($resp && str_starts_with($resp, '220')) {
                    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                    self::cmd($socket, 'EHLO ' . self::heloName(), 250);
                }
            }

            if ($user !== '') {
                self::cmd($socket, 'AUTH LOGIN', 334);
                self::cmd($socket, base64_encode($user), 334);
                self::cmd($socket, base64_encode($pass), 235);
            }

            self::cmd($socket, 'MAIL FROM:<' . $from . '>', 250);
            self::cmd($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            self::cmd($socket, 'DATA', 354);

            $headers = "From: " . self::encodeHeader($fromName) . " <{$from}>\r\n"
                . "To: <{$to}>\r\n"
                . "Subject: " . self::encodeHeader($subject) . "\r\n"
                . "Date: " . date('r') . "\r\n"
                . "Message-ID: <" . bin2hex(random_bytes(12)) . "@"
                . (parse_url($host, PHP_URL_HOST) ?: 'localhost') . ">\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-Type: text/html; charset=UTF-8\r\n"
                . "Content-Transfer-Encoding: base64\r\n";

            $body = preg_replace('/^\./m', '..', $html); // dot-stuffing
            fwrite($socket, $headers . "\r\n" . chunk_split(base64_encode($body)) . "\r\n.\r\n");
            self::expect($socket, 250);

            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            return true;
        } catch (\Throwable $e) {
            if (isset($socket) && is_resource($socket)) {
                @fclose($socket);
            }
            error_log('[Mailer] ' . $e->getMessage());
            return false;
        }
    }

    /** Notify the school about a new website submission. Silently skips if not configured. */
    public static function notifySchool(string $subject, string $title, array $rows, ?string $extraMessage = null): void
    {
        $to = trim((string) setting('notify_email'));
        if ($to === '' || setting('notify_enabled', '1') !== '1') {
            return;
        }

        $site = e(setting('site_name'));
        $html = '<div style="font-family:Arial,sans-serif;max-width:640px;margin:auto">'
            . '<h2 style="color:#1d3fa8;margin-bottom:4px">' . e($title) . '</h2>'
            . '<p style="color:#666;margin-top:0">New submission from the ' . $site . ' website</p>'
            . '<table cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;font-size:14px">';
        foreach ($rows as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $html .= '<tr>'
                . '<td style="border:1px solid #e3e8f4;background:#f6f8fe;font-weight:bold;width:170px">' . e((string) $label) . '</td>'
                . '<td style="border:1px solid #e3e8f4">' . nl2br(e((string) $value)) . '</td>'
                . '</tr>';
        }
        if ($extraMessage) {
            $html .= '<tr><td colspan="2" style="padding-top:10px">' . $extraMessage . '</td></tr>';
        }
        $html .= '</table></div>';

        self::send($to, $subject, $html);
    }

    // ---------------- internals ----------------

    /** @return resource */
    private static function connect(string $host, int $port)
    {
        $transport = $port === 465 ? 'ssl' : 'tcp';
        $socket = @stream_socket_client(
            "{$transport}://{$host}:{$port}",
            $errno,
            $errstr,
            12,
            STREAM_CLIENT_CONNECT,
            stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'SNI_enabled' => true]])
        );
        if (!$socket) {
            throw new \RuntimeException("SMTP connect failed: {$errstr} ({$errno})");
        }
        stream_set_timeout($socket, 12);
        return $socket;
    }

    /** @param resource $socket */
    private static function cmd($socket, string $command, $expect): void
    {
        fwrite($socket, $command . "\r\n");
        self::expect($socket, $expect);
    }

    /** @param resource $socket */
    private static function expect($socket, $codes): void
    {
        $codes = (array) $codes;
        do {
            $line = fgets($socket, 1024);
            if ($line === false) {
                throw new \RuntimeException('SMTP: no response / connection lost');
            }
            $code = (int) substr($line, 0, 3);
        } while (isset($line[3]) && $line[3] === '-'); // multiline reply
        if (!in_array($code, $codes, true)) {
            throw new \RuntimeException('SMTP unexpected response: ' . trim($line));
        }
    }

    private static function heloName(): string
    {
        return parse_url($_SERVER['HTTP_HOST'] ?? 'localhost', PHP_URL_HOST) ?: 'localhost';
    }

    private static function encodeHeader(string $value): string
    {
        if (preg_match('/[^\x20-\x7e]/', $value)) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }
        return $value;
    }
}
