<?php

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() == 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = $content;
        
        $newContent = str_replace('text-[#00ad9a]', 'text-primary', $newContent);
        $newContent = str_replace('bg-[#00ad9a]', 'bg-primary', $newContent);
        $newContent = str_replace('border-[#00ad9a]', 'border-primary', $newContent);
        $newContent = str_replace('ring-[#00ad9a]', 'ring-primary', $newContent);
        $newContent = str_replace('focus:border-[#00ad9a]', 'focus:border-primary', $newContent);
        $newContent = str_replace('focus:ring-[#00ad9a]', 'focus:ring-primary', $newContent);
        $newContent = str_replace('hover:bg-[#00ad9a]', 'hover:bg-primary', $newContent);
        
        $newContent = str_replace('text-[#008f7f]', 'text-primary', $newContent);
        $newContent = str_replace('bg-[#008f7f]', 'bg-primary', $newContent);
        $newContent = str_replace('border-[#008f7f]', 'border-primary', $newContent);
        $newContent = str_replace('ring-[#008f7f]', 'ring-primary', $newContent);
        $newContent = str_replace('focus:border-[#008f7f]', 'focus:border-primary', $newContent);
        $newContent = str_replace('focus:ring-[#008f7f]', 'focus:ring-primary', $newContent);
        $newContent = str_replace('hover:bg-[#008f7f]', 'hover:bg-primary', $newContent);
        
        $newContent = str_replace('text-codeforce-green', 'text-primary', $newContent);
        $newContent = str_replace('bg-codeforce-green', 'bg-primary', $newContent);
        
        $newContent = str_replace('text-[#333333]', 'text-secondary', $newContent);
        $newContent = str_replace('bg-[#333333]', 'bg-secondary', $newContent);
        $newContent = str_replace('border-[#333333]', 'border-secondary', $newContent);
        $newContent = str_replace('ring-[#333333]', 'ring-secondary', $newContent);
        $newContent = str_replace('focus:border-[#333333]', 'focus:border-secondary', $newContent);
        $newContent = str_replace('focus:ring-[#333333]', 'focus:ring-secondary', $newContent);
        $newContent = str_replace('hover:bg-[#333333]', 'hover:bg-secondary', $newContent);

        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo 'Updated ' . $file->getRealPath() . "\n";
        }
    }
}
