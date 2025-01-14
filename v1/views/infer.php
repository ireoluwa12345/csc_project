<?php
$json = file_get_contents("php://input");
$data = json_decode($json, true);
$image_64 = preg_replace('/^data:image\/\w+;base64,/', '', $data['image']);

$api_key = getenv("ROBOFLOW_API");
$model_endpoint = "lpr-pwcdv/3";

$url = "https://detect.roboflow.com/" . $model_endpoint
    . "?api_key=" . $api_key
    . "&name=image.jpg";

// Setup + Send HTTP request
$options = array(
    'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => $image_64
    )
);

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$responseData = json_decode($result, true);

// Extract bounding box from response
if (!empty($responseData['predictions'])) {
    $prediction = $responseData['predictions'][0];
    $x = $prediction['x'];
    $y = $prediction['y'];
    $width = $prediction['width'];
    $height = $prediction['height'];

    $imageData = base64_decode($image_64);
    $image = imagecreatefromstring($imageData);

    $x1 = max(0, $x - ($width / 2));
    $y1 = max(0, $y - ($height / 2));
    $cropWidth = min(imagesx($image), $width);
    $cropHeight = min(imagesy($image), $height);

    $cropped = imagecrop($image, ['x' => $x1, 'y' => $y1, 'width' => $cropWidth, 'height' => $cropHeight]);
    if ($cropped === false) {
        echo json_encode("Failed to crop image");
        return;
    }

    $resized = imagescale($cropped, 300, 150);

    // Apply image filters
    imagefilter($resized, IMG_FILTER_CONTRAST, -20);
    imagefilter($resized, IMG_FILTER_BRIGHTNESS, 10);

    // Convert the resized image to a base64 string
    ob_start(); // Start output buffering
    imagejpeg($resized); // Output the image as a JPEG to the buffer
    $imageDataBase64 = base64_encode(ob_get_clean()); // Get the buffer contents and encode them as base64

    echo json_encode([
        "image" => 'data:image/jpeg;base64,' . $imageDataBase64,
        "status" => "success"
    ]);

    // Free memory
    imagedestroy($image);
    imagedestroy($cropped);
    imagedestroy($resized);
} else {
    echo json_encode("No license plate detected");
}
