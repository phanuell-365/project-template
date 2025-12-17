<?php

function file_base64_values($file_name, $type, $data, $index, $path)
{
    // The image stream is base64 encoded so we need to decode it
    // Remove the data:image/png;base64, part
    // Replace the spaces with +
    $imageData = str_replace(array('data:' . $type . ';base64,', ' '), array('', '+'), $data);

    // Decode the base64 encoded image stream
    $imageData = base64_decode($imageData);

    // Use the timestamp as the original image name
    $originalImageName = $file_name;

    // Set the extension
    //    $extension = $type;
    $extension = explode('/', $type)[1];

    // Get the file size
    $fileSize = strlen($imageData);

    // Get the file size unit and convert to kb
    $fileSizeUnit = 'kb';

    // Convert the file size to kb
    $fileSize = $fileSize / 1024;

    // Set the mime type
    $mimeType = $type;

    // Create a new image name
//    $newImageName = $file_name;

    $newImageName = date('YmdHis') . '_' . $index . '_compressed.' . $extension;

    // Set the file path
    $originalFilePath = WRITEPATH . 'uploads/' . $path . $newImageName;

    $filePath = $path . $newImageName;

    // Store the image
    file_put_contents($originalFilePath, $imageData);

    return [
        'original_name' => $originalImageName,
        'extension' => $extension,
        'size' => $fileSize,
        'mime_type' => $mimeType,
        'path' => $filePath,
        'file_name' => $newImageName,
    ];
}
//{
//    // The image stream is base64 encoded so we need to decode it
//    // Remove the data:image/png;base64, part
//    // Replace the spaces with +
//    $imageData = str_replace(array('data:image/png;base64,', ' '), array('', '+'), $imageStream);
//
//    // Decode the base64 encoded image stream
//    $imageData = base64_decode($imageData);
//
//    // Use the timestamp as the original image name
//    $originalImageName = $imgTimestamp . '.png';
//
//    // Set the extension
//    $extension = 'png';
//
//    // Get the file size
//    $fileSize = strlen($imageData);
//
//    // Get the file size unit and convert to kb
//    $fileSizeUnit = 'kb';
//
//    // Convert the file size to kb
//    $fileSize = $fileSize / 1024;
//
//    // Set the mime type
//    $mimeType = 'image/png';
//
//    // Create a new image name
//    $newImageName = $imgTimestamp . '_' . date('YmdHis') . '_compressed.' . $extension;
//
//    // Set the file path
//    $originalFilePath = WRITEPATH . 'uploads/feedback_images/' . $newImageName;
//
//    $filePath = 'feedback_images/' . $newImageName;
//
//    // Store the image
//    file_put_contents($originalFilePath, $imageData);
//
//    return [
//        'original_name' => $originalImageName,
//        'extension' => $extension,
//        'size' => $fileSize,
//        'mime_type' => $mimeType,
//        'path' => $filePath,
//        'file_name' => $newImageName,
//    ];
//}
