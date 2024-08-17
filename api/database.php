<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli('localhost', 'root', '', 'pinestudio_db');

date_default_timezone_set('Asia/Manila');
$currentDate = date('Y-m-d H:i:s');

function parse_multipart_formdata($data) {
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
        case 'image':
          $tmpFilePath = tempnam(sys_get_temp_dir(), 'uploaded_file');
          $fields[$name] = [
            'name' => $filename,
            'tmp_name' => $tmpFilePath,
            'type' => $headers['content-type']
          ];
          file_put_contents($tmpFilePath, $body);
          break;
        default:
          $fields[$name] = substr($body, 0, strlen($body) - 2);
          break;
      }
    }
  }
  return $fields;
}