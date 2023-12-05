<?php

namespace Application\Functions;

class ProfileImageHandler
{
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png'];
    const MAX_SIZE = 2 * 1024 * 1024; // 2MB
    const UPLOAD_DIRECTORY = '/ProfileImages/';

    /**
     * Uploads an image to the server
     *
     * @param array $file The file to upload
     * @param int $userId The id of the user
     * @param array $message The message to return (both success and error messages)
     * @return bool true if the image was uploaded successfully, false if not
     */
    public static function uploadImage(array $file, int $userId, array &$message = []): bool
    {
        $uploadDir = dirname(__DIR__) . self::UPLOAD_DIRECTORY;

        // If the type is not png or jpeg, return false
        if (!in_array($file['type'], self::ALLOWED_IMAGE_TYPES)) {
            $message[] = "Only jpeg and png files are allowed.";
            return false;
        }
        // If the size is bigger than 2MB, return false
        if ($file['size'] > self::MAX_SIZE) {
            $message[] =  "File bigger than " . self::MAX_SIZE / 1024 / 1024 . "MB.";
            return false;
        }

        // Get the file extension, and build the file name and target file path
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $userId . '.' . $extension;
        $targetFile = $uploadDir . $fileName;

        self::deleteFilesByName($uploadDir, (string)$userId); // Delete all files with the same name as the user id
        if (move_uploaded_file($file['tmp_name'], $targetFile)) { // If the file was uploaded successfully
            $message[] = "Image was uploaded successfully."; // Save some message
            if (file_exists($targetFile)) { // If the file exists, return true (extra check)
                return true;
            } else {
                $message[] = "Failed unexpectedly";
            }
        }
        return false;
    }

    /**
     * Gets the file extension of a file by its file name
     *
     * @param string $fileNameWithoutExtension The file name without the extension
     * @return string The file extension
     */
    public static function getFileExtensionByFileName(string $fileNameWithoutExtension): string {
        // Scan the directory for files
        $files = scandir(dirname(__DIR__) . self::UPLOAD_DIRECTORY);

        foreach ($files as $file) {
            // Check if the file name matches the input file name (ignoring the extension)
            if (pathinfo($file, PATHINFO_FILENAME) === $fileNameWithoutExtension) {
                // Return the file extension
                return pathinfo($file, PATHINFO_EXTENSION);
            }
        }

        // Return an empty string if no matching file is found
        return '';
    }


    /**
     * Deletes all files in a directory with a given name
     * @param string $directory The directory to search in
     * @param string $filenameWithoutExtension The file name without the extension
     * @return string A message with the result of the operation
     */
    public static function deleteFilesByName(string $directory, string $filenameWithoutExtension) {
        // Scan the directory for files
        $files = scandir($directory);

        $deletedFiles = [];
        $hasErrors = false;

        foreach ($files as $file) {
            // Check if the current file has the same name as the input, ignoring the extension
            if (pathinfo($file, PATHINFO_FILENAME) === $filenameWithoutExtension) {
                $fullPath = $directory . '/' . $file;

                // Check if it's a file and not a directory
                if (is_file($fullPath)) {
                    // Attempt to delete the file
                    if (unlink($fullPath)) {
                        $deletedFiles[] = $fullPath;
                    } else {
                        $hasErrors = true;
                        // TODO add log of error or handle it in some way
                    }
                }
            }
        }
        // This is not really
        if (empty($deletedFiles) && !$hasErrors) {
            // No matching files found
            return "No files found to delete.";
        } elseif ($hasErrors) {
            // Some files were not deleted
            return "Some files could not be deleted: " . implode(', ', $deletedFiles);
        } else {
            // All matching files deleted successfully
            return "Deleted files: " . implode(', ', $deletedFiles);
        }
    }
}
