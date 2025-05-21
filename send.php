<?php
$botToken = "7720902826:7720902826:AAEK57GNutoXcCEYyTcWeySwRt829nxPhDI";
$chatId = "-1002651451203";

function sendToTelegram($type, $fileField, $caption) {
    global $botToken, $chatId;

    $url = "https://api.telegram.org/bot$botToken/send" . ucfirst($type);
    $file = new CURLFile($_FILES[$fileField]['tmp_name'], $_FILES[$fileField]['type'], $_FILES[$fileField]['name']);

    $post_fields = [
        'chat_id' => $chatId,
        $type => $file,
        'caption' => $caption
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    curl_close($ch);
    echo $output;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'] ?? '';

    if (isset($_FILES['photo'])) {
        sendToTelegram('photo', 'photo', $caption);
    } elseif (isset($_FILES['video'])) {
        sendToTelegram('video', 'video', $caption);
    } else {
        echo "هیچ فایلی ارسال نشده.";
    }
} else {
    echo "فقط POST پشتیبانی می‌شود.";
}