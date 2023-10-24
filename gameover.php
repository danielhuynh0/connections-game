<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Daniel Huynh, Eric Li">
  <meta name="description" content="PHP Connections Group">
  <title>PHP Connections - Game Over!</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"  crossorigin="anonymous">       
</head>

<body>
    <div class="container" style="margin-top: 15px;">
        <div class="row">
            <div class="col-xs-12">
            <h1>PHP Connections</h1>
            <h4>Hello <?=$name?>! (<?=$email?>)</h4>
            <?php
                if(isset($_SESSION["win"]) && $_SESSION["win"] == true) {
                    echo "<h4>You win!</h4>";
                } else {
                    echo "<h4>You lose!</h4>";
                }
            ?>
            <h4>Total Guesses: <?=$score?></h4>
            </div>
        </div>
        <div>
            <a href="?command=logout">Logout</a>
        </div>
        <div>
            <a href="?command=playagain">Play Again</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>