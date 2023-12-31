<?php

function cut_stop_id(string $stop_id)
{
    return implode(':', array_slice(explode(':', $stop_id), 0, 3));
}

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
        if (empty($line)) {
            continue;
        }

        if (empty($keys)) {
            $keys = str_getcsv(str_replace("﻿", '', $line));
            continue;
        }

        if (str_contains($line, 'de:08212')) {
            $csv_data[] = array_combine($keys, str_getcsv($line));
        }
    }

    fclose($csv_file_resource);

    return $csv_data;
}

function write_data_json(string $file_path, array $stops, array $dimensions)
{
    file_put_contents(
        $file_path,
        json_encode([
            'stops' => array_values($stops),
            'dimensions' => $dimensions,
        ])
    );
}

function calculate_dimensions(array $stops)
{
    return [
        'lat' => [
            'min' => min(array_column($stops, 'lat')),
            'max' => max(array_column($stops, 'lat')),
        ],
        'lon' => [
            'min' => min(array_column($stops, 'lon')),
            'max' => max(array_column($stops, 'lon')),
        ],
        'departures' => [
            'min' => min(array_column($stops, 'departures')),
            'max' => max(array_column($stops, 'departures')),
        ],
    ];
}

function process_stops(array &$stops, array $stops_data)
{
    foreach ($stops_data as $stop) {
        $stops[cut_stop_id($stop['stop_id'])] = [
            'id'         => cut_stop_id($stop['stop_id']),
            'name'       => $stop['stop_name'],
            'lat'        => (float) $stop['stop_lat'],
            'lon'        => (float) $stop['stop_lon'],
            'departures' => 0,
        ];
    }
}

function accumulate_stop_times(array &$stops, $stop_times_data)
{
    foreach ($stop_times_data as $stop_time) {
        $stops[cut_stop_id($stop_time['stop_id'])]['departures'] += 1;
    }
}

$data_parent_folder_path = realpath(__DIR__ . '/../data') . '/';
$data_folder_paths = array_filter(glob("{$data_parent_folder_path}/*"), 'is_dir');

$data = [];

foreach ($data_folder_paths as $data_folder_path) {
    $year = basename($data_folder_path);

    if (file_exists("{$data_folder_path}/data.json")) {
        $data[$year] = json_decode(file_get_contents("{$data_folder_path}/data.json"));
        continue;
    }

    $stops_data = get_csv_data("{$data_folder_path}/stops.txt");
    $stop_times_data = get_csv_data("{$data_folder_path}/stop_times.txt");

    $stops = [];
    process_stops($stops, $stops_data);
    accumulate_stop_times($stops, $stop_times_data);

    $dimensions = calculate_dimensions($stops);

    write_data_json("{$data_folder_path}/data.json", $stops, $dimensions);

    $data[$year] = [
        'stops' => array_values($stops),
        'dimensions' => $dimensions,
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
