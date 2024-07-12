<?php

namespace Lib\slim;

use Psr\Http\Message\UploadedFileInterface as UploadedFile;

class uploadFile
{
    static public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo('./public/' . $directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}
