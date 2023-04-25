<!DOCTYPE html>
 <html>
 <head>
 <meta charset="UTF-8" />
 <title>图片生成器</title> 
 <style> textarea{ font-size: 16px; line-height: 22px; display: grid; grid-template-areas: ‘sbox text mic cam’ ‘. text . .’ 'foot foot foot foot’; grid-template-columns: 48px auto 32px 52px; grid-template-rows: auto; place-items: start; justify-content: stretch; padding-right: 0; max-height: none; min-height: 124px; height: auto; min-width: 580px; max-width: 596px; margin: auto; left: 0; right: 0; } </style> </head>
 <body>
 <center>
 <form method="post" action="">
 <input type="text" id="api_key" name="api_key" required placeholder="输入你的API Key"><br />
 <input type="number" id="image_count" name="image_count" required placeholder="输入你要生成的图片张数"><br />
 <select id="image_size" name="image_size" required>
 <option value="">选择图片输出尺寸</option>
 <option value="512x512">512x512</option>
 <option value="1024x1024">1024x1024</option>
 <option value="2048x2048">2048x2048</option>
 </select><br />
 <textarea type="text" id="keywords" name="keywords" required placeholder="输入你的描述词"></textarea><br />
 <button type="submit" onclick="showLoading();">OK</button>
 </form>
 <div id="loading" style="display:none;">马上就来</div>
 </center>
 <script>
    function showLoading() {        document.getElementById("loading").style.display = "block";    }
</script>
<?php
if(isset($_POST['keywords']) && isset($_POST['api_key']) && isset($_POST['image_count']) && isset($_POST['image_size'])) {
    $openai_api_key = $_POST['api_key'];
    $request_url = 'https://api.openai.com/v1/images/generations';
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openai_api_key,
    ];

    $data = [
        'prompt' => $_POST['keywords'],
        'n' =>  intval($_POST['image_count']),//$_POST['image_count'],
        'size' => $_POST['image_size'],
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
        echo '哦哦' . $err;
    } else {
        $result = json_decode($response, true);

        if(isset($result['data']) && isset($result['data'][0]['url'])) {
            $image_url = $result['data'][0]['url'];
            echo '<img src="' . $image_url . '">';
        } else {
            //echo '哦或';
			var_dump($result);
        }
    }
}
echo '<script>document.getElementById("loading").style.display = "none";</script>';
?>
</body> </html>
