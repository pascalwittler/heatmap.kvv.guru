<?php

function yield_next_line($file_resource)
{
    while (($line = fgets($file_resource)) !== false) {
        yield $line;
    }
}

$data_parent_folder_path = realpath(__DIR__ . '/../data') . '/';
$data_folder_paths = array_filter(glob("{$data_parent_folder_path}/*"), 'is_dir');

$data = [];

foreach ($data_folder_paths as $data_folder_path) {
    $year = basename($data_folder_path);

    $data[$year] = [
        'stops' => [],
        'dimensions' => [],
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
