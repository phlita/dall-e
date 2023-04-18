<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>DALL-E Image Generator</title>
</head>
<body>
    <form method="post" action="">
        <label for="keywords">Enter your image keywords:</label>
        <input type="text" id="keywords" name="keywords" required>
        <button type="submit" onclick="showLoading();">Generate Image</button>
    </form>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="image">Select image to upload:</label>
        <input type="file" name="image" id="image">
		
        <input type="number" name="n_variations" id="n_variations" min="1" max="6" value="1">
        <input type="text" id="size" name="size" value="1024x1024">
        <button type="submit" onclick="showLoading();">Generate Image Variations</button>
    </form>
    <div id="loading" style="display:none;">Generating image, please wait...</div>
    <script>
        function showLoading() {
            document.getElementById("loading").style.display = "block";
        }
    </script>
<?php
if (isset($_POST['keywords'])) {

     $openai_api_key = 'use your key here';
        $request_url = 'https://api.openai.com/v1/images/generations';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $openai_api_key,
        ];

        $data = [
            'prompt' => $_POST['keywords'],
            'n' => 1,
            'size' => '512x512',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if($err) {
            echo 'Error generating image: ' . $err;
        } else {
            $result = json_decode($response, true);

            if(isset($result['data']) && isset($result['data'][0]['url'])) {
                $image_url = $result['data'][0]['url'];
                echo '<img src="' . $image_url . '">';
            } else {
                echo 'Error generating image.';
            }
        }
} elseif (isset($_FILES['image'])) {
    $openai_api_key = 'use your key here';
    $request_url = 'https://api.openai.com/v1/images/variations';

    $headers = [
        'Content-Type: multipart/form-data',
        'Authorization: Bearer ' . $openai_api_key,
    ];

    $data = [
        'image' => new CURLFile($_FILES['image']['tmp_name']),
        'n' => $_POST['n_variations'],
        'size' => $_POST['size'],
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $request_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo 'Error generating image variations: ' . $err;
    } else {
        $result = json_decode($response, true);

        if (isset($result['data'])) {
            foreach ($result['data'] as $image_data) {
                if (isset($image_data['url'])) {
                    $image_url = $image_data['url'];
                    echo '<img src="' . $image_url . '">';
                }
            }
        } else {
            echo 'Error generating image variations.';
        }
    }
}
echo '<script>document.getElementById("loading").style.display = "none";</script>';
?>
</body>
</html>
