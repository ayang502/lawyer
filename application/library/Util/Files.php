<?php
class Util_Files {
    static function genFilePath($caseid, $case_flag, $filename) {
        $arrConfig = Yaf_Registry::get('config');
        $basePath  = $arrConfig['application']['upload']['dir'];
        $file = $basePath . "/" . Util_Tools::getCurrentUser()['id'] . "/{$caseid}/{$case_flag}/{$filename}";
        return $file;
    }
    static function upload($oriFile, $caseid, $caseFlag, $name) {
        $target = self::genFilePath($caseid, $caseFlag, $name);
        $pathinfo = pathinfo($target);
        if (!is_dir($pathinfo["dirname"])){
            $res = mkdir($pathinfo["dirname"], 0755, true);
            var_dump($res);
        }
        self::removefile($target);
        $res = move_uploaded_file($oriFile, $target);
        if ($res) {
            return true;
        }
    }
    static function removeFile($targetFile){
        if (file_exists($targetFile)) {
            $dest = $targetFile . '#' . time();
            copy($targetFile, $dest);
            unlink($targetFile);
        }
    }
}

