<?php
    namespace App\Custom;
    
    class ImageOptimizer
    {
        public function __construct($pathtoImage)
        {
            $this->optimize($pathtoImage, 50);
        }
        public function optimize($pathtoImage, $quality): void
        {
            $imginfo = getimagesize($pathtoImage);
            $mime = $imginfo['mime'];
            match ($mime) {
                'image/jpeg' => $image = imagecreatefromjpeg($pathtoImage),
                'image/png' => $image = imagecreatefrompng($pathtoImage),
                'image/gif' => $image = imagecreatefromgif($pathtoImage),
                default => $image = imagecreatefromjpeg($pathtoImage),
            };
            imagejpeg($image, $pathtoImage, $quality);

        }
    }
