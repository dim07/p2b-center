<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ext', array($this, 'fileExtFilter')),
             new \Twig_SimpleFilter('icon', array($this, 'fileIconFilter')),
        );
    }

    public function fileExtFilter($filepath){
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        return $ext;
    }
    
    public function fileIconFilter($filepath){
        $icon = 'file';
        $ext = strtoupper(pathinfo($filepath, PATHINFO_EXTENSION));
        if ($ext === 'PDF' ) {
            $icon = 'file-pdf-o';
        } elseif ($ext === 'DOC' || $ext === 'DOCX') {
            $icon = 'file-word-o';
        } elseif ($ext === 'XLS' || $ext === 'XLSX') {
            $icon = 'file-excel-o';
        } elseif ($ext === 'ZIP' || $ext === 'RAR') {
            $icon = 'file-zip-o';
        }
        return $icon;
    }
}

