<?php

require_once __DIR__.'/vendor/autoload.php';

$token = 'y0__xCa6vnxAhi4qzkgo-f7_RO3_P8OSPxiGeaGVZdlySv4ozj2WQ';

$disk = new Arhitector\Yandex\Disk($token);

function listFiles($disk, $limit) {
    $resources = $disk->getResources()
        ->setLimit($limit)
        ->setSort('modified', true);

    $files = [];
    foreach ($resources as $resource) {
        if ($resource->isFile()) {
            $files[] = [
                'name' => $resource['name'],
                'size' => $resource['size'],
                'path' => $resource->getPath()
            ];
        }
    }
    return $files;
}

function deleteFile($path, $disk) {
    $resource = $disk->getResource($path);
    if ($resource->has()) {
        $resource->delete();
    }
    header('Location: /');
    exit;
}

function uploadFile($file, $disk) {
    $resource = $disk->getResource($file['name']);
    $resource->upload($file['tmp_name'], true);
    header('Location: /');
    exit;
}

function downloadFile($path, $disk) {
    $resource = $disk->getResource($path);
    if ($resource->has()) {
        $resource->download(__DIR__ . '/download/' . $resource['name']);
    }
}

function viewFile($path, $disk) {
    $resource = $disk->getResource($path);

    if ($resource->has() && $resource->isFile()) {
        $resource->setPublish(true);
        $publicUrl = $resource->public_url;
        $publicKey = basename(parse_url($publicUrl, PHP_URL_PATH));
        header("Location: https://yadi.sk/i/$publicKey");
    }
    exit;
}


if (isset($_GET['delete'])) {
    deleteFile($_GET['delete'], $disk);
}

if (isset($_FILES['upload'])) {
    uploadFile($_FILES['upload'], $disk);
}

if (isset($_GET['download'])) {
    downloadFile($_GET['download'], $disk);
}

if (isset($_GET['view'])) {
    viewFile($_GET['view'], $disk);
}

$files = listFiles($disk, 20);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CRUD Яндекс Диск</title>
</head>
<body>
    <h2>Файлы из Яндекс Диска</h2>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="upload" required>
        <button type="submit">Загрузить</button>
    </form>

    <hr>

    <?php if (empty($files)): ?>
        <p>Нет файлов</p>
    <?php else: ?>
        <ul>
            <?php foreach ($files as $file): ?>
                <li>
                    <?= htmlspecialchars($file['name']) ?> (<?= round($file['size'] / 1024, 1) ?> КБ)
                    <a href="?delete=<?= urlencode($file['path']) ?>" onclick="return confirm('Удалить?')">
                        Удалить
                    </a>
                    <a href="?view=<?= urlencode($file['path']) ?>" target="_blank">Просмотр</a>
                    <a href="?download=<?= urlencode($file['path']) ?>">Скачать</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>