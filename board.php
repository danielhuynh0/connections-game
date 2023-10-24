<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Daniel Huynh, Eric Li">
  <meta name="description" content="PHP Connections Group">
  <title>PHP Connections Board</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"  crossorigin="anonymous">       
</head>

<body>
    <div class="container" style="margin-top: 15px;">
        <div class="row">
            <div class="col-xs-12">
            <h1>PHP Connections</h1>
            <h4>Hello <?=$name?>! (<?=$email?>)</h4>
            <h4>Guesses: <?=$score?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    <div class="card-header">
                        Words
                    </div>
                    <div class="card-body">
                    <?php
                        /*$student_one = array("Food"=>"C1", "Food2"=>"C1", "Food3"=>"C1", "Food4"=>"C1",
                            "Drinks"=>"C2", "Drinks2"=>"C2", "Drinks3"=>"C2", "Drinks4"=>"C2",
                            "Plate"=>"C3", "Plate2"=>"C3", "Plate3"=>"C3", "Plate4"=>"C3",
                            "Cup"=>"C4", "Cup2"=>"C4", "Cup3"=>"C4", "Cup4"=>"C4");*/
                        
                        $student_one = $current_game;

                        $student_keys = array_keys($student_one);
                        shuffle($student_keys);

                        $html = "";

                        $count=0;

                        while($count<count($student_one)){
                            $html .= "<div class=\"row\">\n";
                            for($x = 0; $x<4; $x++){
                                $html .= "\t<div class=\"col\">\n \t\t <label>" . $student_keys[$count] . "</label>\n</div>\n";
                                $count++;
                            }
                            $html .= "</div>\n";
                        }

                        echo $html;
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <form action="?command=answer" method="post">
                    <div class="mb-3">
                        <label for="answer" class="form-label">Guess: </label>
                        <input type="text" class="form-control" id="con-answer" name="answer">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>