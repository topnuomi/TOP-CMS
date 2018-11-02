<?php
function create_file($dir = './', $fileName = 'index.html') {
    $dirArr = scandir($dir);
    for ($i = 2; $i < count($dirArr); $i++) {
        if (is_dir($dir . $dirArr[$i])) {
            $dirName = $dir . $dirArr[$i] . '/';
            $file = $dirName . $fileName;
            if (!is_file($file)) {
                file_put_contents($file, '');
            }
            create_file($dirName);
        }
    }
}
create_file();