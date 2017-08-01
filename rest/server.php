<?php

/*
	About this file:
	
	This is a simple dummy server for testing the Moodle Opaque plugin using REST and JSON.
	
	Each endpoint has a description of the input/output it expects and how it is supposed to function.
	Since this is just a dummy server, only really basic data is being passed
	& this server doesn't actually implement a lot of what is intended.
	
	You can also uncomment the bottom two lines with "file_put_contents" to log all incoming requests for debugging.
*/

/* Helper class for routing http requests */
require('./Route.php');

/* Helper functions for the request functions */
function perror() {
	$result = array("error" => "Invalid Path.");
	echo json_encode($result);
}

function setifset($param,$key) {
	if(isset($param[$key])) {
		return $param[$key];
	} else {
		return null;
	}
}

/*
	GET /info
	
	This function is used to test whether Moodle can connect to this question engine.
	Any data that is returned is simply displayed to the administrator.

	Data must be in the following json format:
	{ "engineinfo": { DATA } }
*/
$getEngineInfo = function() {
	$engineinfo = array(
					"engineinfo" => array(
						"name" => "Qengine",
						"usedmemory" => memory_get_usage(),
						"otherinfo" => "moreinfo"
					)
				);
				
	echo json_encode($engineinfo);
};

/*
	GET /question/<base>/<qid>/<version>
	
	This function is not used in Moodle.
	It can be used to get general information about a question.

	Data must be in the following json format:
	{ "questionmetadata": { DATA } }
*/
$getQuestionMetadata = function() {
	/* get path parts & validate the path */
	$path = explode('/', $_SERVER['PATH_INFO']);

	if(count($path) !== 5) {
		perror();
		return;
	}

	// $path[0] "", $path[1] "question"
	$base = $path[2];
	$qid = $path[3];
	$version = $path[4];
	
	/**********************************************
		at this point, you would get any information related to the question using $base, $qid, $version
		but, since this is just a dummy server, we'll just respond with nonsense
	**********************************************/

	$data = array("dummydata" => "whatever", "moredata" => array("morestuff" => 99));

	$questioninfo = array(
						"questionmetadata" => $data
					);
				
	echo json_encode($questioninfo);
};

/*
	POST /session
	
	This function:
		starts a quiz session:
			questionSession - used to store quiz resources to avoid excessive file/database calls
		returns session data:
			progressInfo - a string to display any relevant data, for example, how many attempts have been made
		returns any web content needed to display the quiz question:
			CSS - a string containing literal CSS that will be loaded on the quiz page
			XHTML - a string containing literal HTML that creates the question (so an HTML form)
			resources - an array of objects containing data about where it can get any extra resources, like javascript files or images
				content - url of the file
				encoding - if any, the encoding of the file
				filename - the name of the file
				mimeType - the mime type of the file
*/
$start = function() {
	/* specific php hack, php only populates $_POST with form data, json data must be grabbed manually */
	$rest_json = file_get_contents("php://input");
	$_POST = json_decode($rest_json, true);
	
	$params = array_combine($_POST["initialParamNames"], $_POST["initialParamValues"]);

	/* mandatory */
	
	$qid = setifset($_POST,"questionID");
	$version = setifset($_POST,"questionVersion");
	$base = setifset($_POST,"questionBaseURL");
	$randomseed = setifset($params,"randomseed");
	
	/* optional */
	$userid = setifset($params,"userid");
	$language = setifset($params,"language");
	$pkey = setifset($params,"passKey");
	$pbehaviour = setifset($params,"preferredbehaviour");
	
	/* extra unknown */
	$attempt = setifset($params,"attempt");
	$navigatorVersion = setifset($params,"navigatorVersion");
	
	/* moodle uses these options for quiz reviews, they're all related to changing the HTML the student sees when reviewing */
	$display_readonly = setifset($params,"display_readonly");
	$display_marks = setifset($params,"display_marks");
	$display_markdp = setifset($params,"display_markdp");
	$display_correctness = setifset($params,"display_correctness");
	$display_feedback = setifset($params,"display_feedback");
	$display_generalfeedback = setifset($params,"display_generalfeedback");

	/* this is used to determine whether the engine should not send resources that moodle already has cached for this question */
	$cachedresources = setifset($_POST,"cachedResources"); // array
	
	/**********************************************
		at this point, you should:
		
		create a session with questionSession (the session ID)
			* it's recommended that the sessionID be: (base):(question id):(question version):(random seed)
			  that way, if the session dies, process() function below can process the next step (sessionID now stores needed data to get question data)
			  if the random seed has no effect on the question data you are storing in session, then omit that part
			* creating a session is optional, it's just for avoiding excessive file or database calls to get question data
		
		assemble progressInfo
		
		get and assemble css + html
			* use %%RESOURCES%% on any resource's src path (Moodle fills that in with the path to wherever it stores the resources you send)
			* use %%IDPREFIX%% on any html input name, 
			this is used to distinguish this question from others, in case moodle displays several questions on one page
		
		get and assemble resources
			* check $cachedresources for resources that don't need to be sent again
			
		store whatever html, css, & resources you want in the session
		
		since, this is just a dummy server, we'll just send a really basic example
	**********************************************/
	
	$data = array(
		"CSS" => ".opaque_test { background-color: cyan }",
		"XHTML" => '<div class="opaque_test">
                        <h2>
                            <span>
                            	Hello <img src="%%RESOURCES%%world.gif" alt="world" />!
                            </span>
                        </h2>
                        <p>
                        	This is a question generated by a simple test quiz engine using the Moodle Opaque open protocol.
                        </p>
                        <p>
                        	Any form values in this HTML, will be sent to the quiz engine\'s  process(). In this example, four inputs (tries,rand,answer,submit||finish) will send their name and value attributes. Enter "(Random seed) + 5" in the answer for a correct response. The question can be submitted multiple times with the Submit button and completed with the Finish button.
                        </p>
                        <input type="hidden" name="%%IDPREFIX%%tries" value=1>
                        <p>
                        	Random Seed: <input type="number" name="%%IDPREFIX%%rand" value="' . $randomseed . '" readonly>
                        </p>
                        <p>
                        	Answer: <input type="text" name="%%IDPREFIX%%answer" value="" placeholder="Answer">
                        </p>
                        <p>
                        	<input type="submit" name="%%IDPREFIX%%submit" value="Submit">
                        	<input type="submit" name="%%IDPREFIX%%finish" value="Finish">
						</p>
					</div>',
		"progressInfo" => "Question started.",
		"questionSession" => $base . ":" . $qid . ":" . $version . ":" . $randomseed,
		"resources" => array(
			array(
				"content" => base64_encode(file_get_contents('world.gif')),
				"encoding" => "base64",
				"filename" => "world.gif",
				"mimeType" => "image/gif"
			)
		)
	);

	echo preg_replace('/ +/',' ',preg_replace('/\\t/', '',json_encode($data)));
};

/*
	POST /session/sid
	
	This function receives name & value pairs from the HTML question form. It uses that data to process a step in this question.
	
	It returns the same data as "POST /session", except it also includes "result" & "questionEnd". "result" can be empty string if no result is available yet.
*/

$process = function() {
	/* specific php hack, php only populates $_POST with form data, json data must be grabbed manually */
	$rest_json = file_get_contents("php://input");
	$_POST = json_decode($rest_json, true);
	
	/* get path parts & validate the path */
	$path = explode('/', $_SERVER['PATH_INFO']);

	if(count($path) !== 3) {
		perror();
		return;
	}

	// $path[0] "", $path[1] "session"
	$questionSession = $path[2];
	
	$params = array_combine($_POST["names"], $_POST["values"]);
	
	$tries = setifset($params,"tries");
	$rand = setifset($params,"rand");
	$answer = setifset($params,"answer");
	$button = setifset($params,"submit") ? setifset($params,"submit") : setifset($params,"finish");
	
	/**********************************************
		at this point, you should:
		
		evaluate question based on names/values
		
		grab, use, and possibly update whatever you want from the session
		
		assemble "result" if possible & determine if "questionEnd" is true/false
		
		assemble new "progressInfo", "XHTML", "CSS", "resources" the same way you did in start()
		
		send the response
		
		since, this is just a dummy server, we'll just send a really basic example
	**********************************************/
	
	if(empty($answer))
	{
		$ans = "No answer was provided.";
		$result = '';
	}
	else
	{
		if($answer == ($rand + 5))
		{
			$ans = "Your answer is correct!";
			$marks = 3;

			$result = array(
				"actionSummary" => "Summary of actions the student took.",
				"answerLine" => "Answered with ' . $answer . '",
				"attempts" => '. $tries . ',
				"customResults" => [],
				"questionLine" => "Summary of the question that was asked.",
				"scores" => array(
					array(
						"axis" => "",
						"marks" => $marks
					)
				)
			);
		}
		else
		{
			$ans = "Your answer is not correct!";
			$marks = 0;

			$result = '';
		}
	}
	
	if(isset($params["-finish"]) || isset($params["finish"]))
	{
		$ended = true;
		$buttons = '';
	}
	else
	{
		$ended = false;
		$buttons = '<input type="submit" name="%%IDPREFIX%%submit" value="Submit">
					<input type="submit" name="%%IDPREFIX%%finish" value="Finish">';
					
	}
	
	$data = array(
		"CSS" => ".opaque_test { background-color: cyan }",
		"XHTML" => '<div class="opaque_test">
                        <h2>
                            <span>
                            	Hello <img src="%%RESOURCES%%world.gif" alt="world" />!
                            </span>
                        </h2>
                        <p>
                        	' . $ans . '
                        </p>
                        <input type="hidden" name="%%IDPREFIX%%tries" value=' . ($tries + 1) . '>
                        <p>
                        	Random Seed: <input type="number" name="%%IDPREFIX%%rand" value="' . $rand . '" readonly>
                        </p>
                        <p>
                        	Answer: <input type="text" name="%%IDPREFIX%%answer" placeholder="' . $answer . '">
                        </p>
                        <p>
                        	' . $buttons . '
						</p>
					</div>',
		"progressInfo" => $tries . " submits",
		"questionEnd" => $ended,
		"results" => $result,
		"resources" => array()
	);

	echo preg_replace('/ +/',' ',preg_replace('/\\t/', '',json_encode($data)));
};

/*
	DELETE /session/sid
	
	This function destroys the requested question session
*/


$stop = function() {
	/* get path parts & validate the path */
	$path = explode('/', $_SERVER['PATH_INFO']);

	if(count($path) !== 3) {
		perror();
		return;
	}

	// $path[0] "", $path[1] "session"
	$questionSession = $path[2];
	
	/* 
		at this point, you would destroy the session
	*/
	
	echo '';
};

/* this route is for handling 404 errors */
$error = function() {
	http_response_code(404);
	$result = array("error" => "Unsupported API call.");
	
	echo json_encode($result);
};

/* handle the routes */
$app = new Route($_SERVER['REQUEST_METHOD'],$_SERVER['PATH_INFO']);

$app->get("/info",$getEngineInfo);
$app->get("/question/.*",$getQuestionMetadata);
$app->post("/session",$start);
$app->post("/session/.*",$process);
$app->delete("/session/.*",$stop);
$app->all(".*",$error);

/* log the requests for debugging*/
file_put_contents('request.log', $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['PATH_INFO'] . "\n",FILE_APPEND);
file_put_contents('request.log',file_get_contents('php://input') . "\n\n",FILE_APPEND);




