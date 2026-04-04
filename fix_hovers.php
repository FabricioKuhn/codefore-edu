<?php

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() == 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = $content;
        
        // Remove known fixed colors
        $newContent = preg_replace('/hover:bg-(green|emerald|codeforce-green)-[0-9]+/', '', $newContent);
        $newContent = str_replace('hover:bg-[#009688]', '', $newContent);
        
        // Normalize hover class for bg-primary links (since primary-button was already done)
        // Let's replace "hover:bg-opacity-90 transition", "transition hover:bg-primary" etc
        
        $newContent = str_replace('hover:bg-opacity-90 transition', 'transition-all duration-200 hover:brightness-90', $newContent);
        $newContent = str_replace('transition shadow-md', 'shadow-md transition', $newContent); // just normalise
        $newContent = str_replace('hover:bg-primary transition', 'transition-all duration-200 hover:brightness-90', $newContent);
        
        // Replace file input hovers
        $newContent = str_replace('hover:file:bg-primary', 'hover:file:brightness-90 transition-all duration-200', $newContent);
        
        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo 'Updated ' . $file->getRealPath() . "\n";
        }
    }
}
