<?php
/**
* @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Helpers;

use Imagick;

final class Files {

    static function upload_file($files, $path = '/var/www/storage/upload/', $filename = 'file_', $newName = '') {
        $info = pathinfo($files['name']);
        $fileN = pathinfo($filename, PATHINFO_FILENAME);

        if (empty($newName) ) {
            $response['filename'] = $fileN . uniqid('',true) . time() . "." . $info['extension'];
        } else {
            $newFilename = pathinfo($newName, PATHINFO_FILENAME);// try and remove the extension from the filename
            $response['filename'] = $newName . "." . $info['extension'];
        }

        $response['filename'] = str_replace(' ', '-',mb_strtolower($response['filename']));

        if(isset($path) && is_dir($path) == false){
            if(self::createFolder($path) == false){
                throw new \Exception( 'Could not create new folder!',400);
            }
            chmod($path, 0755);
        }

        $movedFile = move_uploaded_file($files['tmp_name'], $path . $response['filename']);
        if($movedFile == true){
            $response['fullpath'] = $path . $response['filename'];
            $response['file'] = $response['filename'];
            chmod($response['fullpath'], 0755);
            return $response;
        }

        return null;
    }

    static function createFolder($folderPath = '') {
        if (!empty($folderPath)) {
            if (is_dir($folderPath) == false) {
                $response = @mkdir($folderPath, 0755, true);
                chmod($folderPath, 0755);
                if ($response === false) {
                    throw new \Exception('Could not create new folder in path.', 400);
                }
            }
        }

        return is_dir($folderPath) ? true : false;
    }

    static function rearrange_files($files) {
        $file_arr = [];
        $file_keys = array_keys($files);
        for ($i = 0; $i < count($files['name']); $i++) {
            foreach ($file_keys as $key) {
                $file_arr[$i][$key] = $files[$key][$i];
            }
        }
        return $file_arr;
    }

    static function upload_files($allowedExtensions = ['.jpg', '.png', '.jpeg', '.webp', '.docx', '.pdf'], $folderRoot = '/var/www/storage/upload/', $folderUrl = '/storage/upload/', $fileInputName = 'file'){
        $files = self::rearrange_files((object)filter_var_array($_FILES, FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING));
        $errors = 0;
        $result = [];
        foreach($files as $file){
            if(empty($file['name'])){
                $errors++;
                $result[] = null;
                continue;
            }

            $extensions = strtolower(strrchr($file['name'],'.'));
            $filename = str_replace($extensions, '', $file['name']);

            if(empty($extensions)){ // we do not have a valid extension so continue to the next
                continue;
            }

            if(isset($file['size']) == false || (isset($file['size']) && $file['size'] <= 9 && $file['size'] != 0)){
                $result[] = ['file' => $filename, 'filename' => $filename, 'path' => $folderUrl];
            }

            if(in_array($extensions, $allowedExtensions) == false){
                $errors++;
                $result = [];
                continue;
            }

            if(round($file['size'] / 19531.3) >= 8192){
                $errors++;
                $result = [];
                continue;
            }

            $response = self::upload_file($file, $folderRoot, 'file', $filename);
            if(file_exists($response['fullpath']) == false){
                $errors++;
                $result = [];
                continue;
            }

            $response['upload'] = $filename;
            $response['path'] = $folderUrl;
            $result = [
                'upload' => $filename,
                'file' => $response['file'],
                'path' => $folderUrl
            ];
        }

        return $result;
    }

    /**
     *
     * @param mixed $allowedExtensions
     * @param mixed $folderRoot
     * @param mixed $folderUrl
     * @param mixed $fileInputName
     * @param mixed $newFilename
     * @return array<array|array{error: string, file: array|string, filename: array|string, "full_path": string, path: mixed|array{error: string, file: mixed, filename: mixed, "full_path": string, path: mixed}>|array{error: string, extension: string, file: array|string, filename: array|mixed|string, "full_path": string, path: mixed}|array{error: string, extension: string, file: array|string, filename: mixed, "full_path": string, path: mixed}}
     */
    static function upload_files_to_folder($allowedExtensions = ['.jpg', '.png', '.jpeg', '.webp', '.docx', '.pdf'], $folderRoot = '/var/www/storage/upload/', $folderUrl = '/storage/upload/', $fileInputName = 'file', $newFilename = 'file'){
        $errors = 0;
        $result = [];
        for($i = 0; $i < count($_FILES); $i++) {
            $file = $_FILES[$fileInputName];
            if(!isset($file['name'])) {
                $errors++;
                $result[] = ['file' => $filename, 'filename' => $filename, 'path' => $folderUrl, 'error' => 'File not uploaded', 'full_path' => $folderRoot . $filename . $extensions];
                continue;
            }

            if(empty($file['name'])){
                $errors++;
                $result[] = ['file' => $filename, 'filename' => $filename, 'path' => $folderUrl, 'error' => 'File not uploaded', 'full_path' => $folderRoot . $filename . $extensions];
                continue;
            }

            $extensions = strtolower(strrchr($file['name'],'.'));
            $filename = str_replace($extensions, '', $file['name']);

            if(empty($extensions)){ // we do not have a valid extension so continue to the next
                continue;
            }

            if(isset($file['size']) == false || (isset($file['size']) && $file['size'] <= 9 && $file['size'] != 0)){
                $result[] = ['file' => $filename, 'filename' => $filename, 'path' => $folderUrl, 'error' => 'File not uploaded', 'full_path' => $folderRoot . $filename . $extensions];
            }

             if(in_array($extensions, $allowedExtensions) == false){
                $errors++;
                $result = [
                    'file' => $filename,
                    'filename' => $response['file_name'],
                    'path' => $folderUrl,
                    'extension' => $extensions,
                    'full_path' => $response['path'] . $response['file_name'],
                    'error' => 'File type not allowed'
                ];
                continue;
            }

            if(round($file['size'] / 19531.3) >= 8192){
                $errors++;
                $result = [
                    'file' => $filename,
                    'filename' => $response['file_name'],
                    'path' => $folderUrl,
                    'extension' => $extensions,
                    'full_path' => $response['path'] . $response['file_name'],
                    'error' => 'File too large'
                ];
                continue;
            }

            $response = self::upload_file($file, $folderRoot, 'file', uniqid('upload-'.($newFilename ?? $filename).'-').'-'.date('dmYHis', time()).$extensions);
            if(file_exists($response['fullpath']) == false){
                $errors++;
                $result = [
                    'file' => $filename,
                    'filename' => $response['file_name'],
                    'path' => $folderUrl,
                    'extension' => $extensions,
                    'full_path' => $response['path'] . $response['file_name'],
                    'error' => 'File not uploaded'
                ];
                continue;
            }

            // Compress the file with GD or ImageMagick
            if(self::compress_image($response['fullpath']) == false){
                $errors++;
                $result = [
                    'file' => $filename,
                    'filename' => $response['file_name'],
                    'path' => $folderUrl,
                    'extension' => $extensions,
                    'full_path' => $response['path'] . $response['file_name'],
                    'error' => 'File not uploaded'
                ];
                continue;
            }

            $response['upload'] = $filename;
            $response['path'] = $folderUrl;
            $result[$fileInputName.$i] = [
                'upload' => $filename,
                'file' => $response['file'],
                'filename' => $response['file_name'],
                'path' => $folderUrl,
                'extension' => $extensions,
                'full_path' => $response['path'] . $response['file_name'],
                'error' => ''
            ];
        }

        if($errors > 0){
            dump('Errors:' . $errors);
        }

        return $result;
    }

    static function delete_file($filepath) : bool {
        if(file_exists($filepath)){
            return @unlink(APP_PATH . $filepath);
        }

        return false;
    }

    static function compress_image($filepath) : bool {
        $image = new Imagick($filepath);
        $image->stripImage();
        $image->writeImage();
        $image->clear();
        $image->destroy();
        return true;
    }

}
