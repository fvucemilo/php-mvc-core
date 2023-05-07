<?php

namespace fvucemilo\phpmvc\Http\Responses;

/**
 * Class Response represents an HTTP response.
 */
class Response
{
    /**
     * Redirects to a given URL.
     *
     * @param string $url The URL to redirect to.
     * @param bool|null $replace Whether to replace the previous page in the browser's history with the new page.
     *                           If null, the browser's default behavior is used.
     * @param int $code The HTTP status code to send with the redirect. Defaults to 302 Found.
     *
     * @return void
     */
    public function redirect(string $url, ?bool $replace = true, int $code = 302): void
    {
        header("Location: $url", $replace, $code);
    }
}