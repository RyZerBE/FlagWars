<?php

namespace matze\flagwars\utils;

class FileUtils {

    /**
     * @param string $path
     */
    public static function delete(string $path): void {
        if (empty($path)) {
            return;
        }
        if (is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($path . DIRECTORY_SEPARATOR . $object) == "dir") {
                        self::delete($path . DIRECTORY_SEPARATOR . $object);
                    } else {
                        @unlink($path . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            if(is_dir($path . "/maps")) {
                foreach (scandir($path . "/maps") as $file) {
                    @unlink($path . "/maps/" . $file);
                }
            }
            reset($objects);
            @rmdir($path);
        }
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public static function copy(string $source, string $destination): void {
        $dir = opendir($source);
        @mkdir($destination);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($source . '/' . $file)) {
                    self::copy($source . '/' . $file, $destination . '/' . $file);
                } else {
                    copy($source . '/' . $file, $destination . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public static function copyAsync(string $source, string $destination): void {
        AsyncExecuter::submitAsyncTask(
            function () use ($source, $destination): void {
                FileUtils::copy($source, $destination);
            }
        );
    }

    /**
     * @param string $path
     */
    public static function deleteAsync(string $path): void {
        AsyncExecuter::submitAsyncTask(
            function () use ($path): void {
                FileUtils::delete($path);
            }
        );
    }
}