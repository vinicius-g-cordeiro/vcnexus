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

use App\Shared\Request;

final class Files {
    
    function upload_file($files, $path = '/var/www/storage/upload/', $filename, $newName = '') {
        $info = pathinfo($files['name']);
        if (empty($newName)) {
            $response['file_name'] = $filename . uniqid('upload-',true) . time() . "." . $info['extension'];
        } else {
            $response['file_name'] = $newName;
        }

        $response['file_name'] = str_replace(' ', '-',mb_strtolower($response['file_name']));

        if(isset($path) && is_dir($path) == false){
            if($this->createFolder($path) == false){
                throw new \Exception( 'Could not create new folder!',400);
            }
            chmod($path, 0777);
        }

        $movedFile = move_uploaded_file($files['tmp_name'], $path . $response['file_name']);
        if($movedFile == true){
            $response['fullpath'] = $path . $response['file_name'];
            $response['file'] = $response['file_name'];
            chmod($response['fullpath'], 0777);
            return $response;
        }

        return null;
    }

    function createFolder($folderPath = '') {
        if (!empty($folderPath)) {
            if (is_dir($folderPath) == false) {
                $response = @mkdir($folderPath, 0777, true);
                chmod($folderPath, 0777);
                if ($response === false) {
                    throw new \Exception('Could not create new folder in path.', 400);
                }
            }
        }

        return is_dir($folderPath) ? true : false;
    }

    function rearrange_files($files) {
        $file_arr = [];
        $file_keys = array_keys($files);
        for ($i = 0; $i < count($files['name']); $i++) {
            foreach ($file_keys as $key) {
                $file_arr[$i][$key] = $files[$key][$i];
            }
        }
        return $file_arr;
    }

    function upload_files($allowedExtensions = ['.jpg', '.png', '.jpeg', '.webp', '.docx', '.pdf'], $folderRoot = '/var/www/storage/upload/', $folderUrl = '/storage/upload/', $fileInputName = 'file'){
        $request = Request::instance();
        $files = $this->rearrange_files($request->files());
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

            $response = $this->upload_file($file, $folderRoot, 'file', $filename);
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

    function upload_files_to_folder($allowedExtensions = ['.jpg', '.png', '.jpeg', '.webp', '.docx', '.pdf'], $folderRoot = '/var/www/storage/upload/', $folderUrl = '/storage/upload/', $fileInputName = 'file', $newFilename = 'file'){
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

            $response = $this->upload_file($file, $folderRoot, 'file', uniqid('doc-'.($newFilename ?? $filename).'-').'-'.date('dmYHis', time()).$extensions);
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

            // // Compress the file with GD or ImageMagick
            // if($this->compress_image($response['fullpath']) == false){
            //     $errors++;
            //     $result = [
            //         'file' => $filename,
            //         'filename' => $response['file_name'],
            //         'path' => $folderUrl,
            //         'extension' => $extensions,
            //         'full_path' => $response['path'] . $response['file_name'],
            //         'error' => 'File not uploaded'
            //     ];
            //     continue;
            // }

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

    function delete_file($filepath) : bool {
        if(file_exists($filepath)){
            return @unlink(BASE_PATH . $filepath);            
        }

        return false;
    }

    function compress_image($filepath) : bool {
        $image = new \Imagick($filepath);
        $image->stripImage();
        $image->writeImage();
        $image->clear();
        $image->destroy();
        return true;
    }

}