<?php

class TriviaController {

    private $questions = [];

    private $input = [];

    /**
     * Constructor
     */
    public function __construct($input) {
        session_start();
        
        $this->input = $input;
        $this->loadQuestions();
    }

    /**
     * Load questions from a URL, store them as an array
     * in the current object.
     */
    public function loadQuestions() {
        $this->questions = json_decode(
            file_get_contents("http://www.cs.virginia.edu/~jh2jf/data/trivia.json"), true);

        if (empty($this->questions)) {
            die("Something went wrong loading questions");
        }
    }

    /**
     * Get a question
     * 
     * By default, it returns a random question's id and text.  If given
     * a question id, it returns that question's text and answer.
     */
    public function getQuestion($id=null) {
    
        if ($id === null) {
            $id = array_rand($this->questions);
            return [ "id" => $id, "question" => $this->questions[$id]["question"]];
        }
        if (is_numeric($id) && isset($this->questions[$id])) {
            return $this->questions[$id];
        }
        return false;
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
                $this->showQuestion();
                break;
            case "answer":
                $this->answerQuestion();
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
    public function showQuestion($message = "") {
        $name = $_SESSION["name"];
        $email = $_SESSION["email"];
        $score = $_SESSION["score"];
        $question = $this->getQuestion();
        include("/opt/src/trivia/templates/question.php");
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
    public function answerQuestion() {
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