<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv='Content-Type' content='text/html'>
    <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">
    <title>Word-Spinner</title>
</head>
<body>
    <div class="container">
        <div class="boxes">
            <div class="boxarea">
                <p class="title">Input Text</p>
                <textarea name="box1" id="box1" cols="30" rows="10" class="box" placeholder="Please paste your text here
(It's free up to 50 words)"></textarea>
            </div>
            <div class="boxarea">
                <p class="title">Output Text</p>
                <div contenteditable id="box2" class="box"></div>
            </div>
        </div>
        <button class='spin-button' id='spin-button-id'>Spin</button>
        <div id='show-related-info'></div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="index.js?v=<?php echo time(); ?>"></script>
</body>
</html>