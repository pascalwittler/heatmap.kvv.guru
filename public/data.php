<?php

$data_parent_folder_path = realpath(__DIR__ . '/../data') . '/';
$data_folder_paths = array_filter(glob("{$data_parent_folder_path}/*"), 'is_dir');
