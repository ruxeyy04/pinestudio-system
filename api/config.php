<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

$conn = new mysqli('localhost', 'u677328146_jeffersion', '!0HLzGeW', 'u677328146_pinestudio');

if (mysqli_connect_error()) {
    echo 'error';
}


function parse_multipart_formdata($data)
{
    $fields = [];
    $boundary = substr($data, 0, strpos($data, "\r\n"));

    $parts = array_slice(explode($boundary, $data), 1);
    foreach ($parts as $part) {
        if ($part == "--\r\n") break;
        $part = ltrim($part, "\r\n");
        list($rawHeaders, $body) = explode("\r\n\r\n", $part, 2);
        $rawHeaders = explode("\r\n", $rawHeaders);
        $headers = [];
        foreach ($rawHeaders as $header) {
            list($name, $value) = explode(':', $header);
            $headers[strtolower($name)] = ltrim($value, ' ');
        }
        if (isset($headers['content-disposition'])) {
            $filename = null;
            preg_match(
                '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                $headers['content-disposition'],
                $matches
            );
            list(, $type, $name) = $matches;
            isset($matches[4]) and $filename = $matches[4];

            switch ($name) {
                case 'image_file':
                    $tmpFilePath = tempnam(sys_get_temp_dir(), 'uploaded_file');
                    $fields[$name] = [
                        'name' => $filename,
                        'tmp_name' => $tmpFilePath,
                        'type' => $headers['content-type'] ?? 'image/jpeg'
                    ];
                    file_put_contents($tmpFilePath, $body);
                    break;
                default:
                    $fields[$name] = substr($body, 0, strlen($body) - 2); // Remove trailing \r\n
                    break;
            }
        }
    }
    return $fields;
}
