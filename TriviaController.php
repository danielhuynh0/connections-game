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
            $current_game = [];
            $cats_chosen = array();
            while (count($cats_chosen) < 4) {
                $rand_cat = rand(0, count($this->categories) - 1);
                if (in_array($rand_cat, $cats_chosen)) {
                    continue;
                }
                array_push($cats_chosen, $rand_cat);

                $category = $this->categories[$rand_cat];
                $cat_name = $category["category"];
                for ($i = 0; $i < count($category["words"]); $i++) {
                    $cur_word = $category["words"][$i];
                    $cur_word = strtolower($cur_word);

                    $temp = array($cur_word => $cat_name);
                    array_push($current_game, $temp);
                }
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
            case "logout":
                $this->logout();
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
            $guesses = array_map('strtolower', $guesses);
            
            if(len($guesses) != 4){
                $message = "<div class=\"alert alert-danger\" role=\"alert\">
                Please enter 4 answers!
                </div>";
            } else {
                $current_game = $_SESSION["current_game"];

                $guess_categories = [];
                foreach($guesses as $guess) {
                    if(isset($guess_categories[$current_game[$guess]])) {
                        $guess_categories[$current_game[$guess]] += 1;
                    } else {
                        $guess_categories[$current_game[$guess]] = 1;
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

                if($incorrect == 0) {
                    $message = "<div class=\"alert alert-success\" role=\"alert\">
                    Correct!
                    </div>";
                    $_SESSION["score"] += 1;

                    foreach($guesses as $guess) {
                        unset($_SESSION["current_game"][$guess]);
                    }

                    $previousGuess = [$answer, $incorrect];
                    array_push($_SESSION["previous_guesses"], $previousGuess);

                    if(count($_SESSION["current_game"]) == 0) {
                        $message = "<div class=\"alert alert-success\" role=\"alert\">
                        You win!
                        </div>";
                        unset($_SESSION["current_game"]);
                        
                    }
                } else {
                    $message = "<div class=\"alert alert-danger\" role=\"alert\">
                    Incorrect!
                    </div>";
                    $_SESSION["score"] += 1;
                    $previousGuess = [$answer, $incorrect];
                    array_push($_SESSION["previous_guesses"], $previousGuess);
                }
            }
        } else {
            $message = "<div class=\"alert alert-danger\" role=\"alert\">
            Please enter an answer!
            </div>";
        }

        $this->showCategories($message);
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