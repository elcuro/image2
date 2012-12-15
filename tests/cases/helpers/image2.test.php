<?php
App::import('Helper', 'Image2.Image2');

class Image2Test extends CakeTestCase {

	public $Image2 = null;

	public function startTest() {

		$this->Image2 = new Image2Helper();
	}

	public function endTest() {

		unset($this->Imag2e);
	}

       	public function testGd() {
              
             	$this->assertTrue((extension_loaded('gd') || extension_loaded('gd2')));
       	}

       	public function testSource() {

       		$this->assertIsA($this->Image2->source('img/screenshot.png'), 'object');
        	$this->assertIsA($this->Image2->sizes, 'array');
       	}

	public function testWrongSource() {

		$this->assertIsA($this->Image2->source('img/screenshot1.png'), 'object');
		$this->assertFalse($this->Image2->serverPath);
		$this->assertFalse($this->Image2->sizes);
	}       

       public function testResize() {
              
              $this->assertIsA(
                      $this->Image2->source('img/screenshot.png')
                            ->resizeit(50, 50, false),
                      'object'
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_file = $cache_dir . '0_0_50_50_resize_screenshot.png';
              $sizes = @getimagesize($expected_file);
              $this->assertTrue(file_exists($expected_file));
              $this->assertEqual(50, $sizes[0]);
              $this->assertEqual(50, $sizes[1]);              
       }	

       public function testWatermarkPattern() {

              $source = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'doglovers.jpg';
              $watermark = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'watermark_pattern.png';
              
              $this->assertIsA( 
                     $this->Image2->source($source, true)
                            ->resizeit(600, 500, false)
                            ->watermark($watermark, 'pattern', true),
                     'object'
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_watermark_file = $cache_dir . '850_850_600_500_crop_watermark_pattern.png';
              $expected_image_file = $cache_dir . 'watermark_pattern_png_pattern_0_0_600_500_resize_doglovers.jpg';
              $this->assertTrue(file_exists($expected_image_file));
              $this->assertTrue(file_exists($expected_watermark_file));
       }  

       public function testWatermarkPatternFirstInChain() {

              $source = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'doglovers.jpg';
              $watermark = App::pluginPath('Image2').'webroot'.DS.'img'.DS.'test'.DS.'watermark_pattern.png';
              
              $this->assertIsA( 
                     $this->Image2->source($source, true)
                            ->watermark($watermark, 'pattern', true),
                     'object'
              );
              
              $cache_dir = implode(DS, Configure::read('Image2.cacheDir'));
              $cache_dir = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . $cache_dir .DS;              
              $expected_image_file = $cache_dir . 'watermark_pattern_png_pattern_doglovers.jpg';
              $expected_watermark_file = $cache_dir . '674_652_952_895_crop_watermark_pattern.png';
              $this->assertTrue(file_exists($expected_image_file));
              $this->assertTrue(file_exists($expected_watermark_file));
       }                      	
}

?>