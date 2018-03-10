<?php

$words = array();
$numWords = 0;

// function to display the page with each state of the game.
function displayPage($image, $guessWord, $nowWord, $guessed, $wrong)
{
    $script = $_SERVER["PHP_SHELF"];

    echo <<<ENDPAGE
    
    <!DOCTYPE html>
    <html>
    <head><title>MyHttpServer</title></head>
    </html>
    
    <body>
    <h2>Hangman</h2>
    <img src="$image">
    <p><strong>Word to guess $guessWord</strong></p>
    <p>Letters already used: $guessed</p>
    
    <form method="post" action="$script">
        <input type="hidden" name="wrong" value="$wrong" />
        <input type="hidden" name="lettersguessed" value="$guessed" />
        <input type="hidden" name="word" value="$nowWord" />
        <p>Your guess</p>
        <input type="text" name="letter" autofocus />
        <input type="submit" value="Guess" />
    </form>
    </body>

ENDPAGE;

}

//load all of the words into an array. close file afterwards.

function loadFile()
{
    global $words;
    global $numWords;
    $input = fopen("./wordlist.txt", "r");

    while (true)
    {
        $str = fgets($input);
        if (!$str)
            break;
        $words[] = rtrim($str);
        $numWords++;
    }

    fclose($input);
}

// randomly select a word from the array, display the initial hang.
function beginGame()
{
    global $words;
    global $numWords;

    $nowWord = rand(0, $numWords - 1);
    $word = $words[$nowWord];

    $len = strlen($word);
    $guessWord = str_repeat('_ ', $len);

    displayPage('h1.png', $guessWord, $nowWord, "", 0);
}

// user reached this function if failed 7 guesses. displays a page indicating game over.
function endGame($word)
{
    echo <<<ENDPAGE
<!DOCTYPE html>
<html>
    <head>
        <title>Hangman</title>
    </head>
<body>
<h1> Game Over. </h1>
<p>Word to guess was : $word</p>
</body>
</html>
ENDPAGE;
}

// user reaches this page if all letters in the word are correctly guessed. game is also over here.
function youWin($word)
{
    echo <<<ENDPAGE
<!DOCTYPE html>
<html>
<head>
<title>Hangman</title>
</head>
<body>
<h1>Congrats! You won!</h1>
<p>You guessed $word correctly!</p>
</body>
</html>
ENDPAGE;
}

// function to check if the letter exists in the word.
function checkLetters($word, $guessed)
{
    $len = strlen($word);
    $guessWord = str_repeat("_ ", $len);

    for ($i = 0; $i < $len; $i++)
    {
        $x = $word[$i];

        if (strstr($guessed, $x))
        {
            $position = 2 * $i;
            $guessWord[$position] = $x;
        }
    }
    return $guessWord;
}

// this function handles the guess. take the input from the user and check if it is a character.
// if not a character, return error, do not increase wrong.
// if a character, send to checkletters and see if it exists.
// depending on the value of wrong, it will display a certain hanged man image.
function handleGuess()
{
    global $words;

    $nowWord = $_POST["word"];
    $word = $words[$nowWord];
    $wrong = $_POST["wrong"];
    $lettersguessed = $_POST["lettersguessed"];
    $guess = $_POST["letter"];
    $letter = $guess[0];

    if(ctype_alpha($letter))
    {
        if(stripos($word, $letter) === false)
        {
            $wrong++;
        }
    }

    else
    {
        echo "Need to input valid character.";
    }

    $lettersguessed = $lettersguessed . $letter;
    $guessWord = checkLetters($word, $lettersguessed);

    if(stripos($guessWord, "_") === false)
    {
        youWin($word);
    }

    else if($wrong == 0)
    {
        displayPage('h1.png', $guessWord, $nowWord, $lettersguessed, $wrong);
    }

    else if($wrong >= 7)
    {
        endGame($word);
    }

    else if($wrong == 1)
    {
        displayPage('h2.png', $guessWord, $nowWord, $lettersguessed, $wrong);
    }

    else if($wrong == 2)
    {
        displayPage('h3.png', $guessWord, $nowWord, $lettersguessed, $wrong);
    }

    else if($wrong == 3)
    {
        displayPage('h4.png', $guessWord, $nowWord, $lettersguessed, $wrong);
    }

    else if($wrong == 4)
    {
        displayPage('h5.png', $guessWord, $nowWord, $lettersguessed, $wrong);
    }

    else if($wrong == 5)
    {
        displayPage('h6.png', $guessWord, $nowWord, $lettersguessed, $wrong);
    }

    else if($wrong == 6)
    {
        displayPage('h7.png', $guessWord, $nowWord, $lettersguessed, $wrong);
    }

}

// load the file and begin the game.
loadFile();
$method = $_SERVER["REQUEST_METHOD"];

if($method == "POST")
{
    handleGuess();
}
else
{
    beginGame();
}

?>
