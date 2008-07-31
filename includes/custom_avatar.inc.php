<?php

//require_once('class.cropcanvas.php');




function makeBigAvatar($src,$target,$x,$y,$w,$h) {

    if(($w/$h)!=1)
        $w = $h;
    
    if($w<80) {
        $w = 80;
        $h = 80;
    }
        
    // http://tr.php.net/getimagesize
    
    list($cwidth, $cheight, $ctype, $zzz1_attr) = getimagesize($src);
    
    if($ctype==3)
        $image = imagecreatefrompng($src);
    elseif($ctype==1)
        $image = imagecreatefromgif($src);
    elseif($ctype==2)
        $image = imagecreatefromjpeg($src);
    else
        die("error 8453489530475..".$ctype.'..'.$cwidth);
    
    if($ctype!=2) {
        imageAlphaBlending($image, true);
        imageSaveAlpha($image, true);
    }
    
    // fixed width and height
    $image_p = imagecreatefrompng(dirname(__FILE__)."/../images/blank80.png");
    
    if($ctype!=2) {
        imageAlphaBlending($image_p, true);
        imageSaveAlpha($image_p, true);
    }
    
    if($cwidth<=80&&$cheight<=80) {
        imagecopymerge($image_p, $image, (80-$cwidth)/2, (80-$cheight)/2, 0, 0, $cwidth, $cheight,100);
    
    }
    elseif($cwidth<=80||$cheight<=80) {
        
        if($cwidth>80) {
            
            $nheight *= (80/$cwidth);
            $nwidth = 80;
            
        }
        else {
            
            $nwidth *= (80/$cheight);
            $nheight = 80;
            
        }
        
        imagecopyresampled($image_p, $image, (80-$nwidth)/2, (80-$nheight)/2, 0, 0, $nwidth, $nheight,$cwidth,$cheight);
        
    }
    else {
        
        // no check!
        // TODO: check!
        
        imagecopyresampled($image_p, $image, 0, 0, $x, $y, 80, 80,$w,$h);
        
    }
    
    imagepng($image_p,$target);
    
    imagedestroy($image);
    imagedestroy($image_p);
    
}


// basically resizes 80x80 image to 16x16
function makeSmallAvatar($bigavatar,$target) {
    
    $image = imagecreatefrompng($bigavatar);
    imageAlphaBlending($image, true);
    imageSaveAlpha($image, true);
    
    $image_p = imagecreatefrompng(dirname(__FILE__)."/../images/blank16.png");
    imageAlphaBlending($image_p, true);
    imageSaveAlpha($image_p, true);
    
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, 16, 16, 80, 80);
    
    imagepng($image_p, $target);
    
    imagedestroy($image);
    imagedestroy($image_p);
    
}

// $target_dir  should end with /
function makeFlags($smallavatar,$target_dir) {
    
    if(substr($target_dir,-1,1)!='/')
        $target_dir .= '/';

    $marker_blue = dirname(__FILE__)."/../images/map/marker-blue.png";
    $marker_red = dirname(__FILE__)."/../images/map/marker.png";
    
    $target_blue = $target_dir.'marker-blue.png';
    $target_red = $target_dir.'marker.png';
    
    copy($marker_blue,$target_blue);
    copy($marker_red,$target_red);
    
    $dest_blue = imagecreatefrompng($target_blue); 
    $dest_red = imagecreatefrompng($target_red); 
    ImageAlphaBlending($dest_blue, true);
    ImageAlphaBlending($dest_red, true);
    imageSaveAlpha($dest_blue, true);
    imageSaveAlpha($dest_red, true);
    
    $src = imagecreatefrompng($smallavatar);
    imageAlphaBlending($src, true);
    imageSaveAlpha($src, true);
    
    watermark($dest_blue,$src);
    watermark($dest_red,$src);
    
    imagepng($dest_blue,$target_blue);
    imagepng($dest_red,$target_red);
    
    imagedestroy($dest_blue);
    imagedestroy($dest_red);
    imagedestroy($src);
    
    
}

// helper
function watermark($url,$logo){
    $lwidth  = imagesx($logo);
    $lheight = imagesy($logo);
    $src_x = 9;
    $src_y = 10;
    ImageAlphaBlending($url, true);
    imageSaveAlpha($url, true);
    ImageAlphaBlending($logo, true);
    imageSaveAlpha($logo, true);
    ImageCopyMerge($url,$logo,$src_x,$src_y,0,0,$lwidth,$lheight,100);
}




?>