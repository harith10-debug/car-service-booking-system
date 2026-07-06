<?php

// Vercel serverless runtime has a read-only filesystem.
// Laravel needs writable temporary folders for views/cache/log-free runtime files.
$temporaryDirectories = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
];

foreach ($temporaryDirectories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

require __DIR__ . '/../public/index.php';