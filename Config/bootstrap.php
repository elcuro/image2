<?php
// path to resized (modified) files as array from webroot 
Configure::write('Image2.cacheDir', array('uploads', 'resized')); // = /app/webroot/uploads/resized

// hook helper
Croogo::hookHelper('*', 'Image2.Image2');
?>