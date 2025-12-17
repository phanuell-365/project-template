<?php

function file_upload_values($file, $path)
{
//    try {
        // check if the file is valid
        if ($file->isValid() && ! $file->hasMoved()) {
            // get the original file name
            $originalName = $file->getClientName();

            // get the file extension
            $extension = $file->guessExtension();

            // get the file size
            $size = $file->getSizeByUnit('kb');

            // get the file mimeType type
            $mimeType = $file->getClientMimeType();

            // create a new file name
            $newName = $file->getRandomName();

            // move the file to the uploads directory
            $file->move(WRITEPATH . 'uploads/' . $path, $newName);
            $filePath = $path . $newName;

            // return the file details
            return [
                'original_name' => $originalName,
                'extension' => $extension,
                'size' => $size,
                'mime_type' => $mimeType,
                'path' => $filePath,
                'file_name' => $newName,
            ];
        }

        return false;
//    } catch (\Exception $e) {
//        throw new \Exception($e->getMessage());
//    }
}

