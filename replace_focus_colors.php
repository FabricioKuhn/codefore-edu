<?php

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() == 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = $content;
        
        $newContent = str_replace('focus:ring-codeforce-green', 'focus:ring-primary', $newContent);
        $newContent = str_replace('focus:border-codeforce-green', 'focus:border-primary', $newContent);

        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo 'Updated ' . $file->getRealPath() . "\n";
        }
    }
}
