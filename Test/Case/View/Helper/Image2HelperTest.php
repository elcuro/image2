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

       public function tearDown() {
              parent::tearDown();
              unset($this->Image2Helper, $this->View);
       }

}
?>