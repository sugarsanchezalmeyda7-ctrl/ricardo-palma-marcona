<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

$projectRoot = dirname(__DIR__);
$imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$groups = [];

$addImage = static function (string $groupName, string $relativePath, string $fileName) use (&$groups, $imageExtensions): void {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extension, $imageExtensions, true)) {
        return;
    }

    $segments = array_map('rawurlencode', explode('/', str_replace('\\', '/', $relativePath)));
    $groups[$groupName][] = [
        'src' => implode('/', $segments),
        'name' => pathinfo($fileName, PATHINFO_FILENAME),
    ];
};

$rootFiles = scandir($projectRoot) ?: [];
foreach ($rootFiles as $fileName) {
    $filePath = $projectRoot . DIRECTORY_SEPARATOR . $fileName;
    if (is_file($filePath)) {
        $addImage('Comunidad', $fileName, $fileName);
    }
}

foreach ($rootFiles as $directoryName) {
    $directoryPath = $projectRoot . DIRECTORY_SEPARATOR . $directoryName;
    if (!is_dir($directoryPath) || str_starts_with($directoryName, '.')) {
        continue;
    }

    $files = scandir($directoryPath) ?: [];
    foreach ($files as $fileName) {
        $filePath = $directoryPath . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($filePath)) {
            $addImage($directoryName, $directoryName . '/' . $fileName, $fileName);
        }
    }
}

ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);
foreach ($groups as &$images) {
    usort($images, static fn (array $first, array $second): int => strnatcasecmp($first['name'], $second['name']));
}
unset($images);

echo json_encode($groups, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);