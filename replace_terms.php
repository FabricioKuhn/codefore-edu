<?php

$paths = [
    __DIR__ . '/resources/views',
    __DIR__ . '/app/Http/Controllers',
];

foreach ($paths as $dir) {
    if (!is_dir($dir)) continue;
    
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() == 'php') {
            $content = file_get_contents($file->getRealPath());
            $newContent = $content;
            
            $newContent = str_replace('Missão', 'Atividade', $newContent);
            $newContent = str_replace('missão', 'atividade', $newContent);
            $newContent = str_replace('Missões', 'Atividades', $newContent);
            $newContent = str_replace('missões', 'atividades', $newContent);

            if ($content !== $newContent) {
                file_put_contents($file->getRealPath(), $newContent);
                echo 'Updated ' . $file->getRealPath() . "\n";
            }
        }
    }
}
