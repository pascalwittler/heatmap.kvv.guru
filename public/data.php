<?php

function yield_next_line($file_resource)
{
    while (($line = fgets($file_resource)) !== false) {
        yield $line;
    }
}

function get_csv_data(string $file_path): array
{
    $csv_file_resource = fopen($file_path, 'r');

    $keys = [];
    $csv_data = [];

    foreach (yield_next_line($csv_file_resource) as $line) {
        if (empty($keys)) {
            $keys = str_getcsv($line);
            continue;
        }

        if (str_contains($line, 'de:08212')) {
            $csv_data[] = array_combine($keys, str_getcsv($line));
        }
    }

    fclose($csv_file_resource);

    return $csv_data;
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
