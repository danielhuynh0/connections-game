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

        $current_game = [];

        foreach ($this->categories as $category) {
            $cat_name = $category["category"];
            for ($i = 0; $i < count($category["words"]); $i++) {
                $cur_word = $category["words"][$i];
                $temp_list = [$cat_name, $cur_word];
                array_push($current_game, $temp_list);
            }
        }
    
        return $current_game;
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
        $previous_guesses = $_SESSION["previous_guesses"];
        $categories = $this->getCategories();
        include("/opt/src/trivia/templates/board.php");
    }

    /**
     * Show the welcome page to the user.
     */
    public function showWelcome() {
        include("/opt/src/trivia/templates/welcome.php");
    }

    /**
     * Check the user's answer to a question.
     */
    public function submitCategories() {
        $message = "";
        if (isset($_POST["questionid"]) && is_numeric($_POST["questionid"])) {
            
            $question = $this->getQuestion($_POST["questionid"]);

            if (strtolower(trim($_POST["answer"])) == strtolower($question["answer"])) {
                $message = "<div class=\"alert alert-success\" role=\"alert\">
                Correct!
                </div>";
                $_SESSION["score"] += 5;
            }
            else {
                $message = "<div class=\"alert alert-danger\" role=\"alert\">
                Incorrect! The correct answer was: {$question["answer"]}
                </div>";
            }
        }

        $this->showQuestion($message);
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