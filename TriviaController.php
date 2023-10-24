<?php

class TriviaController {

    private $categories = [];

    private $input = [];

    /**
     * Constructor
     */
    public function __construct($input) {
        session_start();
        
        $this->input = $input;
        $this->loadCategories();
    }

    /**
     * Load questions from a URL, store them as an array
     * in the current object.
     */
    public function loadCategories() {
        $this->categories = json_decode(
            file_get_contents("https://www.cs.virginia.edu/~jh2jf/data/categories.json"), true);

        if (empty($this->categories)) {
            die("Something went wrong loading questions");
        }
    }


    /*
        Load list of categories and each of the words in the category to set up the current game board
    */
    public function getCategories() {

       if(isset($_SESSION["current_game"])) {
            return $_SESSION["current_game"];
        }
        else {
            $current_game_w = [];
            while (count($current_game_w) < 16) {
                $rand_cat = rand(0, count($this->categories) - 1);
                $category = $this->categories[$rand_cat];
                $cat_name = $category["category"];
                for ($i = 0; $i < count($category["words"]); $i++) {
                    $cur_word = $category["words"][$i];
                    $cur_word = strtolower($cur_word);

                    $temp = array($cur_word, $cat_name);
                    if (in_array($temp, $current_game_w)) {
                        continue;
                    }
                    array_push($current_game_w, $temp);
                }
            }
            shuffle($current_game_w);
            $current_game=array();
            for($x=0; $x<16; $x++){
                $current_game[($x+1)] = $current_game_w[$x];
                //$temp2 = array(($x+1) => ($current_game_w[$x]));
                //array_push($current_game, $temp2);
            }
            $_SESSION["current_game"] = $current_game;
            return $current_game;
        }
    }

    /**
     * Run the server
     * 
     * Given the input (usually $_GET), then it will determine
     * which command to execute based on the given "command"
     * parameter.  Default is the welcome page.
     */
    public function run() {
        // Get the command
        $command = "welcome";
        if (isset($this->input["command"]))
            $command = $this->input["command"];

        switch($command) {
            case "login":
                $this->login();
            case "question":
                $this->showCategories();
                break;
            case "answer":
                $this->submitCategories();
                break;
            case "playagain":
                $_SESSION["win"] = false;
                $_SESSION["previous_guesses"] = [];
                $_SESSION["score"] = 0;
                $this->showCategories();
                break;
            case "logout":
                $this->logout();
                break;
            default:
                $this->showWelcome();
                break;
        }
    }

    /**
     * Show a question to the user.  This function loads a
     * template PHP file and displays it to the user based on
     * properties of this object.
     */
    public function showCategories($message = "") {
        $name = $_SESSION["name"];
        $email = $_SESSION["email"];
        $score = $_SESSION["score"];
        if(isset($_SESSION["previous_guesses"])) {
            $previous_guesses = $_SESSION["previous_guesses"];
        }
        else {
            $_SESSION["previous_guesses"] = [];
            $previous_guesses = [];
        }
        $current_game = $this->getCategories();
        include("board.php");
    }

    /**
     * Show the welcome page to the user.
     */
    public function showWelcome() {
        include("welcome.php");
    }

    /**
     * Check the user's answer to a question.
     */
    public function submitCategories() {
        $message = "";
        $answer = "";

        if(isset($_POST["answer"])) {
            $answer = $_POST["answer"];

            $guesses = explode(" ", $answer);
            //$guesses = array_map('strtolower', $guesses);
            
            if(count($guesses) != 4){
                $message = "<div class=\"alert alert-danger\" role=\"alert\">
                Please enter 4 answers!
                </div>";
                $this->showCategories($message);
                return;
            } else {
                $current_game = $_SESSION["current_game"];

                $guess_categories = [];
                foreach($guesses as $guess) {
                    if(!array_key_exists($guess, $current_game)){
                        $message = "<div class=\"alert alert-danger\" role=\"alert\">
                        Please enter words on the screen!
                        </div>";
                        $this->showCategories($message);
                        return;
                    }
                    if(isset($guess_categories[$current_game[$guess][1]])) {
                        $guess_categories[$current_game[$guess][1]] += 1;
                    } else {
                        $guess_categories[$current_game[$guess][1]] = 1;
                    }
                }

                $incorrect = 0;
                $max_guesses = 0;
                $max_cat = "";
                foreach($guess_categories as $cat => $num) {
                    if($num > $max_guesses) {
                        $max_guesses = $num;
                        $max_cat = $cat;
                    }
                }

                foreach($guess_categories as $cat => $num) {
                    if($cat != $max_cat) {
                        $incorrect += $num;
                    }
                }

                $guess_categories=[];

                $_SESSION["score"] += 1;
                $ans_string="You guessed [";
                foreach($guesses as $guess){
                    $ans_string .= $_SESSION["current_game"][$guess][0];
                    $ans_string .= ", ";
                }
                $ans_string = substr($ans_string, 0, -2);
                $ans_string .= "]. ";

                if($incorrect == 0) {
                    $message = "<div class=\"alert alert-success\" role=\"alert\">
                    Correct!
                    </div>";

                    $ans_string .= "That was correct!";

                    foreach($guesses as $guess) {
                        unset($_SESSION["current_game"][$guess]);
                    }

                    array_unshift($_SESSION["previous_guesses"], $ans_string);

                    if(count($_SESSION["current_game"]) == 0) {
                        $message = "<div class=\"alert alert-success\" role=\"alert\">
                        You win!
                        </div>";
                        unset($_SESSION["current_game"]);
                        $_SESSION["win"] = true;
                        $name = $_SESSION["name"];
                        $email = $_SESSION["email"];
                        $score = $_SESSION["score"];
                        include("gameover.php");
                        
                    } else {
                        $this->showCategories($message);
                    }
                } else {
                    $message = "<div class=\"alert alert-danger\" role=\"alert\">
                    Incorrect!";

                    if($incorrect>2){
                        $message .= " You are way off. </div>";
                        $ans_string .= "Many were wrong.";
                    }
                    if($incorrect == 2){
                        $message .= " You are 2 off. </div>";
                        $ans_string .= "Two were wrong.";
                    }
                    if($incorrect == 1){
                        $message .= " You are 1 off. </div>";
                        $ans_string .= "One was wrong.";
                    }
                    array_unshift($_SESSION["previous_guesses"], $ans_string);
                    $this->showCategories($message);
                }
            }
        } else {
            $message = "<div class=\"alert alert-danger\" role=\"alert\">
            Please enter an answer!
            </div>";
            $this->showCategories($message);
        }
    }

    /**
     * Handle user registration and log-in
     */
    public function login() {

        if(isset($_POST["fullname"])) {
            $_SESSION["name"] = $_POST["fullname"];
        }

        if(isset($_POST["email"])) {
            $_SESSION["email"] = $_POST["email"];
        }

        $_SESSION["score"] = 0;

    }

    /**
     * Log out the user
     */
     public function logout() {
        session_destroy();

        session_start();

    }

}