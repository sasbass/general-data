<?php
if (!function_exists('image_resize_write')) {
    function image_resize_write($src, $dest, $sizeW, $sizeH=0, $r_type = 'fixed'){
        
        if($sizeH == 0) $sizeH = $sizeW;
        
        $format = 'jpg';
        
        $error = 0;
        $errstr = "";   
        if (!$imsize = @getimagesize($src)) {
            $error = 1;
            $errstr = "Error opening source file"; 
            return(
                array(
                    "error" => $error,
                    "errstr" => $errstr
                )
            );
        }

        if ($imsize[2] != 1 && $imsize[2] != 2 && $imsize[2] != 3 && $imsize[2] != 6){
            $error = 2; 
            $errstr = "Unsupported format"; 
            return(
                array(
                    "error" => $error,
                    "errstr" => $errstr
                )
            );
        }
 
        switch ($imsize[2]) {
            case 1: if (!$im = imagecreatefromgif($src)) $error = 1; break;
            case 2: if (!$im = imagecreatefromjpeg($src)) $error = 1; break;
            case 3: if (!$im = imagecreatefrompng($src)) $error = 1; break;
            case 6: if (!$im = imagecreatefromwbmp($src)) $error = 1; break;
        }

        if ($error) {
            $errstr = "Error opening source file";
            return(
                array(
                    "error" => $error,
                    "errstr" => $errstr
                )
            );
        }
        
        $x = $imsize[0];
        $y = $imsize[1];

        switch ($r_type) {
            case 'resize': {

                  //get dimensions
                  $width  = $sizeW;
                  $height = $sizeH;
                  
                  //set ratio
                  if (!isset($height) || $height=="x") {
                    $ratio = $y/$x;
                    $height=$width*$ratio;
                  } elseif (!isset($width) || $width=="x") {
                    $ratio = $x/$y;
                    $width=$height*$ratio;
                  }

                  //generate image
                  $img_dest = imagecreatetruecolor($width,$height);
                  
                  if (@$imgtype!='jpg') {
                    imagealphablending ($img_dest,FALSE);
                    imagesavealpha ($img_dest,TRUE);
                  }

                  $r = imagecopyresampled ($img_dest,$im,0,0,0,0,$width,$height,$x,$y) or die ("");
                  break;
            }
            case 'fixed': case 'crop': {
                //get dimensions
                $width  = $sizeW;
                $height = $sizeH;
                
                if (($width*($y/$x))>$height) {
                    $w = $width;
                    $h = $width*($y/$x);
                    $xs = 0;
                    $ys = - (($h-$height)/2);
                } else {
                    $h = $height;
                    $w = $height*($x/$y);
                    $xs = -(($w-$width)/2);
                    $ys = 0;
                };
                //generate image
                $img_dest = imagecreatetruecolor($width,$height);
                
                if (@$imgtype!='jpg') {
                  imagealphablending ($img_dest,FALSE);
                  imagesavealpha ($img_dest,TRUE);
                }
                
                $r = imagecopyresampled ($img_dest,$im,$xs,$ys,0,0,$w,$h,$x,$y) or die ("");
                break;
            }
            default: case 'max': {
                //get dimensions
                $w = $max = $sizeW;
                $height = $sizeH;
                
                $xoffset = 0;
                $yoffset = 0;
                $woffset = 0;
                $hoffset = 0;
                
                // имаме зададена височина
                // картинката ще трябва задължително да
                // влезе в указаните w/size и h
                // var_dump($imsize);
                // exit;
                $h = $sizeH;
                $rect_w = $w;
                $rect_h = $h;
                
                // преоразмеряваме правилно картинката
                $width = $w;
                $height = round(($rect_w*$y)/$x);
                $by_width = true;
                
                if ($height>$rect_h) {
                    $by_width = false;

                    // височината не съвпада, намаляме и нея и широчината
                    $dest_ratio = $height/$rect_h;
                    $width = round($width/$dest_ratio);
                    $height = round($height/$dest_ratio);
                }
                
                // оправяме отместването на картинката
                if ($by_width) {
                    $yoffset = ($rect_h/2)-($height/2);
                } else {
                    $xoffset = ($rect_w/2)-($width/2);
                }
               
                //generate image
                $img_dest = imagecreatetruecolor((isset($rect_w) ? $rect_w : $width),(isset($rect_h) ? $rect_h : $height));
               
                
                imagealphablending ($img_dest,FALSE);
                imagesavealpha ($img_dest,TRUE);
                $background_color = imagecolorallocatealpha($img_dest, 255, 255, 255, 127);
                
                imagefill($img_dest, 0, 0, $background_color);
                imagecopyresampled($img_dest, $im, $xoffset, $yoffset, $woffset, $hoffset, $width, $height, $x, $y);
                break;
              }
        }

        switch(strtolower($format)){
            case "jpg":
            case "jpeg": 
                if (!imagejpeg($img_dest, $dest)) $this->error = 3;
                break;
            case "png": 
                if (!imagepng($img_dest, $dest)) $this->error = 3;
                break;
            case "gif":
                if (!imagegif($img_dest, $dest)) $this->error = 3;
                break;
            case "bmp":
            case "wbmp":
                if (!imagewbmp($img_dest, $dest)) $this->error = 3;
        }
    }
}