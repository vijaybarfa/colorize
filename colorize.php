<?php
/**
 * colorize
 * Jesse Baird
 * 9/21/2010
 * 
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * simple php class to find colors in an iamge and replace them
 * 
 * http://stackoverflow.com/questions/456044/can-i-swap-colors-in-image-using-gd-library-in-php
 * 
 **/
 class colorize{
    
    private $images=array();
    
    const CACHE_PATH='./cache/';
    
    /**
     * colorizer::__construct()
     * 
     * @param mixed $images array of iamges and paths
     * @return void
     */
    public function __construct($images=array()){
        foreach($images as $img){
            $this->images[basename($img)]=$this->createImage($img);
        }
    }
    
    
    /**
     * colorizer::replace()
     * 
     * find all colors in images and replace them 
     * 
     * @param mixed $find
     * @param mixed $replace
     * @param string $fileNamePrefix
     * @return void
     */
    public function replace($find=000000,$replace=000000,$fileNamePrefix=''){
        $frgb=$rrgb=array();
        if(is_array($find)){
            foreach($find as $c){
                $frgb[]=$this->hexToRGB($c);
            }    
        }else{
            $frgb[]=$this->hexToRGB($find);
        }
        
        if(is_array($replace)){
            foreach($replace as $c){
                $rrgb[]=$this->hexToRGB($c);
            }    
        }else{
            $rrgb[]=$this->hexToRGB($replace);
        }
        
        foreach($this->images as $filename=>$img){
            
            foreach($frgb as $index => $f){
                //get the current replace color in find with the index or the last color in the array
                $r = (isset($rrgb[$index]))?$rrgb[$index]:$rrgb[(count($rrgb)-1)];
                
                $index = imagecolorclosest($img,$f['r'],$f['g'],$f['b']);//find
                imagecolorset($img,$index,$r['r'],$r['g'],$r['b']);//replace
            }
            
            $this->saveImage((($fileNamePrefix!='')?$fileNamePrefix.'_':'').$filename,$img);
            
            imagedestroy($img);
        }
    }
    
    /**
     * colorizer::hexToRGB()
     * 
     * turn #333 to rgb code
     * 
     * @param mixed $hex
     * @return array
     */
    private function hexToRGB($hex){
        $hex = str_replace("#", "", $hex);
		$color = array();
		
		if(strlen($hex) == 3) {
			$color['r'] = hexdec(substr($hex, 0, 1) . $r);
			$color['g'] = hexdec(substr($hex, 1, 1) . $g);
			$color['b'] = hexdec(substr($hex, 2, 1) . $b);
		}
		else if(strlen($hex) == 6) {
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
		}
		
		return $color;
    }
    
    /**
     * colorizer::getExtention()
     * 
     * @param mixed $file
     * @return string
     */
    private function getExtention($file){
        $parts=pathinfo($file);
        return strtolower($parts['extension']);
    }
    
    /**
     * colorizer::createImage()
     * 
     * @param mixed $filename
     * @return iamge object
     */
    private function createImage($filename){
        $ret='';
        switch($this->getExtention($filename)){
            case 'png':
                $ret = imagecreatefrompng($filename);
                break;
            case 'jpg':
            case 'jpeg':
                $ret = imagecreatefromjpeg($filename);
                break;
            case 'bmp':
                $ret = imagecreatefrombmp($filename);
                break;
            case 'gif':
                $ret = imagecreatefromgif($filename);
                break;
            default:
                throw new Exception('unknown file extension');
                break;
        }
        return $ret;
        
    }
    
    private function saveImage($filename,$img){
               
        switch($this->getExtention($filename)){
            case 'png':
                imagepng($img,self::CACHE_PATH.$filename);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($img,self::CACHE_PATH.$filename);
                break;
            case 'bmp':
                imgbmp($img,v::CACHE_PATH.$filename);
                break;
            case 'gif':
                imagegif($img,self::CACHE_PATH.$filename);
                break;
            default:
                throw new Exception('unknown file extension');
                break;
        }
    }
    
    
 }
 
?>