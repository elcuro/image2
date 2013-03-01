<?php

App::uses('Controller', 'Controller');
App::uses('Image2Helper', 'Image2.View/Helper');
App::uses('View', 'View');

class Image2HelperTest extends CakeTestCase {

       /**
        * View instance
        *
        * @var View
        */
       public $View;

       /**
        * Image2Helper instance
        *
        * @var BookmeHelper
        */
       public $Image2Helper;

       public function setUp() {
              parent::setUp();
              $Controller = new Controller();
              $this->View = new View($Controller);
              $this->Image2Helper = new Image2Helper($this->View);
       }
       
       public function testGd() {
              
              $this->assertTrue((extension_loaded('gd') || extension_loaded('gd2')));
       }

       public function testSource() { 

              $this->assertInternalType('object', $this->Image2Helper->source('img/screenshot.png'));
              $this->assertContains('img/screenshot.png', $this->Image2Helper->serverPath);
              $this->assertInternalType('array', $this->Image2Helper->sizes);
       }

       public function testWrongSource() {

              $this->assertInternalType('object', $this->Image2Helper->source('img/screenshot1.png'));
              $this->assertFalse($this->Image2Helper->serverPath);
              $this->assertFalse($this->Image2Helper->sizes);
       }    
       
       public function testResize() {
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->resizeit(50, 50, false)
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file = $cache_dir . '0_0_50_50_resize_screenshot.png';
              $sizes = @getimagesize($expected_file);
              $this->assertTrue(file_exists($expected_file));
              $this->assertEquals(50, $sizes[0]);
              $this->assertEquals(50, $sizes[1]);              
       }
       
       public function testResizeRatio() {
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->resizeit(50, 50)
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file = $cache_dir . '0_0_50_38_resize_screenshot.png';
              $sizes = @getimagesize($expected_file);
              $this->assertTrue(file_exists($expected_file));
              $this->assertEquals(50, $sizes[0]);
              $this->assertEquals(38, $sizes[1]);              
       }   
       
       public function testCrop() {
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->crop(200, 100)
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file = $cache_dir . '0_38_200_100_crop_screenshot.png';
              $sizes = @getimagesize($expected_file);
              $this->assertTrue(file_exists($expected_file));
              $this->assertEquals(200, $sizes[0]);
              $this->assertEquals(100, $sizes[1]);              
       }          
       
       public function testChain() {
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->resizeit(500, 600, false)
                            ->crop(200, 200, false)
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file_resize = $cache_dir . '0_0_500_600_resize_screenshot.png';
              $expected_file_crop = $cache_dir . '150_200_200_200_crop_0_0_500_600_resize_screenshot.png';

              $sizes = @getimagesize($expected_file_resize);
              $this->assertTrue(file_exists($expected_file_resize));
              $this->assertEquals(500, $sizes[0]);
              $this->assertEquals(600, $sizes[1]);       
              
              $sizes = @getimagesize($expected_file_crop);
              $this->assertTrue(file_exists($expected_file_crop));
              $this->assertEquals(200, $sizes[0]);
              $this->assertEquals(200, $sizes[1]);                
       }        
       
       public function testWatermark() {
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->resizeit(500, 500, false)
                            ->watermark('img/croogo.png')
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file = $cache_dir . 'croogo_png_center_0_0_500_500_resize_screenshot.png';
              $sizes = @getimagesize($expected_file);
              $this->assertTrue(file_exists($expected_file));
              $this->assertEquals(500, $sizes[0]);
              $this->assertEquals(500, $sizes[1]);              
       }      
       
       public function testWatermarkAbsolutePath() {
              
              $path = WWW_ROOT.'img'.DS.'croogo.png';
              $this->assertInternalType('object', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->resizeit(333, 333, false)
                            ->watermark($path, 'center', true)
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file = $cache_dir . 'croogo_png_center_0_0_333_333_resize_screenshot.png';
              $sizes = @getimagesize($expected_file);
              $this->assertTrue(file_exists($expected_file));
              $this->assertEquals(333, $sizes[0]);
              $this->assertEquals(333, $sizes[1]);              
       }         
       
       public function testWatermarkOverlay() {
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->resizeit(500, 500, false)
                            ->watermark('img/croogo.png', 'overlay')
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_image_file = $cache_dir . 'croogo_png_overlay_0_0_500_500_resize_screenshot.png';
              $expected_watermark_file = $cache_dir . '0_0_500_500_resize_croogo.png';
              $this->assertTrue(file_exists($expected_image_file));
              $this->assertTrue(file_exists($expected_watermark_file));
       } 

       public function testWatermarkPattern() {

              $source = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'doglovers.jpg';
              $watermark = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'watermark_pattern.png';
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source($source, true)
                            ->resizeit(600, 500, false)
                            ->watermark($watermark, 'pattern', true)
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_watermark_file = $cache_dir . '850_850_600_500_crop_watermark_pattern.png';
              $expected_image_file = $cache_dir . '674_652_952_895_crop_watermark_pattern.png';
              $this->assertTrue(file_exists($expected_image_file), 'image file');
              $this->assertTrue(file_exists($expected_watermark_file), 'watermark file');
       }               

       public function testWatermarkPatternFirstInChain() {

              $source = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'doglovers.jpg';
              $watermark = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'watermark_pattern.png';
              
              $this->assertInternalType('object', 
                      $this->Image2Helper->source($source, true)
                            ->watermark($watermark, 'pattern', true)
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_image_file = $cache_dir . 'watermark_pattern_png_pattern_doglovers.jpg';
              $expected_watermark_file = $cache_dir . '674_652_952_895_crop_watermark_pattern.png';
              $this->assertTrue(file_exists($expected_image_file));
              $this->assertTrue(file_exists($expected_watermark_file));
       }         
       
       public function testImagePath() {
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir_relative = '/'.implode('/', Configure::read('Image2.cacheDir')).'/';
              
              $this->assertEquals($cache_dir_relative.'0_0_220_220_resize_screenshot.png', 
                      $this->Image2Helper->source('img/screenshot.png')
                            ->resizeit(220, 220, false)
                            ->imagePath()
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file = $cache_dir . '0_0_220_220_resize_screenshot.png';
              $sizes = @getimagesize($expected_file);
              $this->assertTrue(file_exists($expected_file));
              $this->assertEquals(220, $sizes[0]);
              $this->assertEquals(220, $sizes[1]);                  
       }

       public function testInlineImage() {

              $source = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'doglovers.jpg';

              $result = $this->Image2Helper->source($source, true)
                     ->resizeit(100, 100)
                     ->inlineImage();
              $this->assertTrue(is_string($result));
              $this->assertContains('data:image/jpeg;base64,', $result);
       }

       public function tearDown() {
              parent::tearDown();
              unset($this->Image2Helper, $this->View);
              ob_flush();
       }

}
?>