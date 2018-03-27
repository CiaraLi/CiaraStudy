<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileUpload
 *
 * @author iong
 */
class Fileupload {

    static function checkfile($file, $types, $max_size) {
        $allowed = explode('|', $postfixs = str_replace(' ', '', $types));
        $filetdetail = pathinfo($file['name']);
        if (empty($filetdetail['extension']) || !in_array($filetdetail['extension'], $allowed)) {
            return -1;
        } else if ($max_size < $file['size']) {
            return -2;
        }
    }

    static function copyfile($tmpname, $newpath, $name = "") {
        $newpath = iconv('UTF-8', 'GBK', $newpath);
        if (file_exists($tmpname) && self::createDir($newpath)) {
            $filename = pathinfo($name);
            $newefile = pathinfo($newpath);
            $oldfile = pathinfo($tmpname);
            if (empty($name)) {
                $name = empty($newefile['basename']) ? "/" . $oldfile['basename'] : "";
            } else {
                $profix = empty($filename['extension']) ? '.' . $oldfile['extension'] : "";
                $name = "/$name" . $profix;
            }
            copy($tmpname, $newpath . iconv('UTF-8', 'GBK', $name));
            return true;
        } else {
            return false;
        }
    }

    static function save($tmpname, $path, $filename = '', $profix = "") {
        if (self::createDir($path)) {
            if (empty($filename)) {
                $filename = time() . rand(1000, 9999);
            }
            if (empty($profix)) {
                $filetdetail = pathinfo($tmpname);
                $profix = empty($filetdetail['extension']) ? "tmp" : $filetdetail['extension'];
            }
            $fullname = "$filename.$profix";
            $move = move_uploaded_file($tmpname, $path . $fullname);
            if ($move) {
                return $path . $fullname;
            } else {
                return -1;
            }
        }
        return -2;
    }

    static function createDir($paths) {
        $dir = explode("/", trim($paths, "."));
        $path = '.';
        $ref = true;
        foreach ($dir as $key => $value) {
            $path .= $value . "/";
            if (!is_dir($path)) {
                $ref = $ref && mkdir($path, 0777);
            }
        }
        return $ref;
    }

    static function unlinkFile($aimUrl) {
        if (is_dir($aimUrl)) {
            return self::delDirAndFile($aimUrl);
        } else if (file_exists($aimUrl)) {
            return unlink($aimUrl);
        } else {
            return false;
        }
    }

    static function delDirAndFile($dirName) {
        if (is_dir($dirName) && $handle = opendir("$dirName")) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dirName/$item")) {
                        self:: delDirAndFile("$dirName/$item");
                    } else {
                        unlink("$dirName/$item");
                    }
                }
            }
            closedir($handle);
            return rmdir($dirName);
        } else {
            return false;
        }
    }

    static function saveBlob($tmpname, $path, $filename = '', $blonNum = 0, $totalBlob = "") {
        if (self::createDir($path)) {
            if (empty($filename)) {
                $filename = time() . rand(1000, 9999);
            }
            $fullname = $filename . "_$totalBlob-$blonNum";
            $move = move_uploaded_file($tmpname, $path . $fullname);
            if ($move) {
                if (!empty($totalBlob) && $totalBlob == $blonNum) {
                    $ref = self::fileMerge($path, $filename, $totalBlob);
                    return $ref ? $path . $filename : false;
                } else {
                    return $path . $fullname;
                }
            } else {
                return -1;
            }
        }
        return -2;
    }

    //判断是否是最后一块，如果是则进行文件合成并且删除文件块
    static function fileMerge($filePath, $fileName, $totalBlobNum) {
        $blob = null;
        $success = 0;
        for ($i = 1; $i <= intval($totalBlobNum); $i++) {
            $fullname = $filePath . $fileName . "_$totalBlobNum-$i";
            $time = 0;
            while (!is_file($fullname) && $time < 10) {
                $time++;
                sleep(1);
                if (is_file($fullname)) {
                    break;
                }
            }
            if (is_file($fullname)) {
                $blob = file_get_contents($fullname);
                $write = empty($blob) ? false : file_put_contents($filePath . '/' . $fileName, $blob, FILE_APPEND);
                if ($write) {
                    $success++;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        if ($success == $totalBlobNum) {
            self::delBlobFile($filePath . $fileName, $totalBlobNum);
            return true;
        } else {
            return false;
        }
    }

    static function delBlobFile($filePath, $totalBlobNum) {
        for ($i = 1; $i <= intval($totalBlobNum); $i++) {
            self::unlinkFile($filePath . "_$totalBlobNum-$i");
        }
    }

    static function readStream($filename) {
        $filebinary='';
        if (is_file($filename)) {
            $fp = fopen($filename, 'r');
            $filebinary = fread($fp, filesize($filename));
            fclose($fp);
        }
        return $filebinary;
    }

     function downloaStream($fpath, $name = '') {
        set_time_limit(0);  //大文件在读取内容未结束时会被超时处理，导致下载文件不全。   
        $file_pathinfo = pathinfo($fpath);
        $file_name = empty($name) ? $file_pathinfo['basename'] : $name;
        $file_extension = $file_pathinfo['extension'];
        $handle = fopen($fpath, "rb");
        if (FALSE === $handle)
            exit("Failed to open the file");
        $filesize = filesize($fpath);

        header("Content-type:video/mpeg4"); //更具不同的文件类型设置header输出类型  
        header("Accept-Ranges:bytes");
        header("Accept-Length:" . $filesize);
        header("Content-Disposition: attachment; filename=" . $file_name);

        $contents = '';
        while (!feof($handle)) {
            $contents = fread($handle, 8192);
            echo $contents;
            @ob_flush();  //把数据从PHP的缓冲中释放出来  
            flush();      //把被释放出来的数据发送到浏览器  
        }
        fclose($handle);
    }
}
