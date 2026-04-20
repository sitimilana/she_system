<?php
$f = 'resources/views/kepala_bagian/home.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/<div class="col-md-4 mb-4">(.*?)<\/div>\s*<\/div>\s*<\/div>/s', '', $c);
file_put_contents($f, $c);
echo "Done.";
