<?php

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() == 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = $content;
        
        $newContent = str_replace('focus:ring-indigo-500', 'focus:ring-primary', $newContent);
        $newContent = str_replace('bg-indigo-600', 'bg-primary', $newContent);
        $newContent = str_replace('text-indigo-600', 'text-primary', $newContent);
        $newContent = str_replace('hover:text-gray-900', 'hover:text-primary', $newContent);
        
        // for responsive nav link, etc
        $newContent = str_replace('border-indigo-400', 'border-primary', $newContent);
        $newContent = str_replace('text-indigo-700', 'text-primary', $newContent);
        $newContent = str_replace('bg-indigo-50', 'bg-primary/10', $newContent);
        $newContent = str_replace('focus:text-indigo-800', 'focus:text-primary', $newContent);
        $newContent = str_replace('focus:bg-indigo-100', 'focus:bg-primary/20', $newContent);
        $newContent = str_replace('focus:border-indigo-700', 'focus:border-primary', $newContent);
        
        // svg fixes
        $newContent = str_replace('fill="#00ad9a"', 'class="fill-primary"', $newContent);
        $newContent = str_replace('stroke="#00ad9a"', 'class="stroke-primary"', $newContent);
        $newContent = str_replace('fill="#333333"', 'class="fill-secondary"', $newContent);
        $newContent = str_replace('stroke="#333333"', 'class="stroke-secondary"', $newContent);

        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo 'Updated ' . $file->getRealPath() . "\n";
        }
    }
}
