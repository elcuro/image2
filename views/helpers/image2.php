<?php
/**
 *
 * Modification of orignal Image helper boundled with Croogo
 * added crop and cropResize feature
 *
 * @version 1.1
 * @author Josh Hundley
 * @author Jorge Orpinel <jop@levogiro.net> (changes)
 */
class Image2Helper extends Helper {

        public $helpers = array('Html');
        public $cacheDir = 'resized'; // relative to 'img'.DS

        /**
        * Automatically resize (crop) an image and returns formatted IMG tag
        *
        * @param string $path Path to the image file, relative to the webroot/img/ directory.
        * @param integer $width Image of returned image
        * @param integer $height Height of returned image
        * @param string $method resize method (resize, resizeRatio, resizeCrop, crop)
        * @param array    $htmlAttributes Array of HTML attributes.
        * @param boolean $return Wheter this method should return a value or output it. This overrides AUTO_OUTPUT.
        * @param boolean $server_path Local server path to file
        * @return mixed    Either string or echos the value, depends on AUTO_OUTPUT and $return.
        * @access public
        */
        public function resize($path, $width, $height, $method = 'resizeRatio', $htmlAttributes = array(), $return = false, $server_path = false) {
                $types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp"); // used to determine image type
                if(empty($htmlAttributes['alt'])) $htmlAttributes['alt'] = 'thumb';  // Ponemos alt default

                $uploadsDir = 'uploads';

                $fullpath = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$uploadsDir.DS;
                if (!$server_path) {
                        $url = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$path;
                } else {
                        $url = $server_path;
                }

                if (!($size = getimagesize($url))) // $size[0]:width, [1]:height, [2]:type
                        return; // image doesn't exist
                                
                
                switch ($method) {
                        
                        case "resizeRatio":
                                if (($size[1]/$height) > ($size[0]/$width))  {
                                        $width = ceil(($size[0]/$size[1]) * $height);
                                } else {
                                        $height = ceil($width / ($size[0]/$size[1]));
                                }                                
                                $start_x = 0;
                                $start_y = 0;
                                $method_short = 'rr';
                                break;
                                
                        case "resize":
                                $start_x = 0;
                                $start_y = 0;
                                $method_short = 'r';
                                break;
                                
                        case "resizeCrop":
                                $ratio_x = $width / $size[0];
                                $ratio_y = $height / $size[1];
                                if (($ratio_y) > ($ratio_x))  {
                                        $start_x = round(($size[0] - ($width / $ratio_y)) / 2);
                                        $start_y = 0;
                                        $size[0] = round($width / $ratio_y);
                                } else {
                                        $start_x = 0;
                                        $start_y = round(($size[1] - ($height / $ratio_x)) / 2);
                                        $size[1] = round($height / $ratio_x);
                                }
                                $method_short = 'rc';
                                break;
                        
                        case "crop":
                                $start_x = ($size[0] - $width) / 2;
                                $start_y = ($size[1] - $height) / 2;
                                $size[0] = $width;
                                $size[1] = $height;
                                $method_short = 'c';
                                break;
                      
                                
                }

                $relfile = $this->webroot.$uploadsDir.'/'.$this->cacheDir.'/'.$method_short.'_'.$width.'x'.$height.'_'.basename($path); // relative file
                $cachefile = $fullpath.$this->cacheDir.DS.$method_short.'_'.$width.'x'.$height.'_'.basename($path);  // location on server

                if (file_exists($cachefile)) {
                        $csize = getimagesize($cachefile);
                        $cached = ($csize[0] == $width && $csize[1] == $height); // image is cached
                        if (@filemtime($cachefile) < @filemtime($url)) {// check if up to date
                                $cached = false;
                        }
                } else {
                        $cached = false;
                }

                if (!$cached) {
                        $image = call_user_func('imagecreatefrom'.$types[$size[2]], $url);
                        if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {
                                imagecolortransparent($temp, imagecolorallocate($temp, 0, 0, 0));
                                imagecopyresampled ($temp, $image, 0, 0, $start_x, $start_y, $width, $height, $size[0], $size[1]);
                        } else {
                                $temp = imagecreate ($width, $height);
                                imagecopyresized ($temp, $image, 0, 0, $start_x, $start_y, $width, $height, $size[0], $size[1]);
                        }
                        call_user_func("image".$types[$size[2]], $temp, $cachefile);
                        imagedestroy ($image);
                        imagedestroy ($temp);
                } else {
                        //copy($url, $cachefile);
                }

                return $this->output(sprintf($this->Html->tags['image'], $relfile, $this->Html->_parseAttributes($htmlAttributes, null, '', ' ')), $return);
        }
}
?>