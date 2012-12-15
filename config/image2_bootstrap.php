<?php
 	// cache dir as array pieces
	// = /app/webroot/uploads/resized
 	Configure::write('Image2.cacheDir', array('uploads', 'resized')); 
        Croogo::hookHelper('*', 'Image2.Image2');
?>