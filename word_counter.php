<?php

// Define the TextGenerator interface
interface TextGenerator {
    public function generate(int $length): string;
}

// Implement the BasicTextGenerator class
class BasicTextGenerator implements TextGenerator {
    // Private property to store the list of words
    private array $wordList;

    /**
     * Constructor for the BasicTextGenerator class.
     *
     * @param array $wordList An array containing the list of words for generating text.
     */
    public function __construct(array $wordList) {
        // Initialize the wordList property with the provided list of words
        $this->wordList = $wordList;
    }

    /**
     * Generates random text based on the word list and specified length.
     *
     * @param int $length The desired length of the generated text.
     * @return string The generated random text.
     */
    public function generate(int $length): string {
        // Initialize an empty string to store the generated text
        $text = '';

        // Get the total count of words in the word list
        $wordCount = count($this->wordList);

        // Iterate to generate the specified length of text
        for ($i = 0; $i < $length; $i++) {
            // Generate a random index to select a word from the word list
            $randomWordIndex = mt_rand(0, $wordCount - 1);

            // Get the randomly selected word from the word list
            $randomWord = $this->wordList[$randomWordIndex];

            // Append the randomly selected word to the generated text
            $text .= $randomWord;
        }

        // Return the generated random text
        return $text;
    }
}


class WordCounter {
    private array $wordCounts = [];
    private array $wordPositions = [];

    /**
	 * Constructor for the WordCounter class.
	 *
	 * @param array $wordList An array containing the list of words to be counted.
	 */
	public function __construct(private array $wordList) {
	    // Initialize word counts and positions for each word in the wordList
	    foreach ($this->wordList as $word) {
	        // Initialize the word count to 0 for the current word
	        $this->wordCounts[$word] = 0;
	        
	        // Initialize an empty array to store word positions for the current word
	        $this->wordPositions[$word] = [];
	    }
	}
	
	/**
	 * Preprocesses the word list by invoking the preprocessWord method for each word.
	 *
	 * @return void
	 */
	public function preprocessWordList(): void {
	    // Iterate through each word in the wordList
	    foreach ($this->wordList as $word) {
	        // Invoke the preprocessWord method for the current word
	        $this->preprocessWord($word);
	    }
	}
	
	/**
	 * Preprocesses a word for efficient searching using the Knuth-Morris-Pratt algorithm.
	 *
	 * @param string $word The word to be preprocessed.
	 * @return void
	 */
	private function preprocessWord(string $word): void {
	    // Calculate the length of the word
	    $length = strlen($word);
	    
	    // Initialize the position array for the word with a sentinel value
	    $this->wordPositions[$word][0] = -1;
	    
	    // Initialize the value of k to -1
	    $k = -1;
	
	    // Iterate through each character in the word
	    for ($i = 1; $i < $length; $i++) {
	        // Check for mismatches and update k using the Knuth-Morris-Pratt algorithm
	        while ($k >= 0 && $word[$k + 1] !== $word[$i]) {
	            $k = $this->wordPositions[$word][$k];
	        }
	        
	        // Increment k if the characters match
	        if ($word[$k + 1] === $word[$i]) {
	            $k++;
	        }
	        
	        // Store the value of k in the wordPositions array
	        $this->wordPositions[$word][$i] = $k;
	    }
	}

    /**
	 * Counts occurrences of words in the given text.
	 *
	 * @param string $text The input text in which to count word occurrences.
	 * @return void
	 */
	public function countWords(string $text): void {
	    // Convert the input text to lowercase for case-insensitive comparison
	    $text = strtolower($text);
	
	    // Iterate through each word in the wordCounts array
	    foreach ($this->wordCounts as $word => $count) {
	        // Calculate the length of the current word
	        $wordLength = strlen($word);
	        
	        // Initialize the position of the current word
	        $wordPosition = -1;
	
	        // Iterate through each character in the input text
	        for ($i = 0; $i < strlen($text); $i++) {
	            // Check if the current character matches the next character of the word
	            while ($wordPosition >= 0 && $word[$wordPosition + 1] !== $text[$i]) {
	                // Move to the previous position in the word
	                $wordPosition = $this->wordPositions[$word][$wordPosition];
	            }
	            
	            // If the characters match, move to the next position in the word
	            if ($word[$wordPosition + 1] === $text[$i]) {
	                $wordPosition++;
	            }
	
	            // Check if the entire word has been matched
	            if ($wordPosition === $wordLength - 1) {
	                // Increment the word count and update the position in the word
	                $this->wordCounts[$word]++;
	                $wordPosition = $this->wordPositions[$word][$wordPosition];
	            }
	        }
	    }
	}

    public function getWordCounts(): array {
        return $this->wordCounts;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wordList = [$_POST['word1'], $_POST['word2'], $_POST['word3']];
    $generator = new BasicTextGenerator($wordList);

// Generate random text based on user input or default value
    $text_length = isset($_POST['text_length']) ? (int)$_POST['text_length'] : 50;
    if (isset($_POST['random_text']) && !empty($_POST['random_text'])) {
        $randomText = $_POST['random_text'];
    } else {
        $randomText = $generator->generate($text_length);
    }

    $wordCounter = new WordCounter($wordList);
    $wordCounter->preprocessWordList();
    $wordCounter->countWords($randomText);
    $wordCounts = $wordCounter->getWordCounts();

    
    // Create an associative array of matched words and their corresponding colors
    $matchedWords = [];
    $highlightColors = [
        $_POST['word1'] => 'yellow',
        $_POST['word2'] => 'lightgreen',
        $_POST['word3'] => 'lightblue'
    ];

    // Extract matched words and highlight them with different colors
    foreach ($wordList as $word) {
        if ($wordCounts[$word] > 0) {
            $matchedWords[$word] = $highlightColors[$word];
        }
    }

    echo "<p>Matched words in random text:</p>";
    
    // Highlight matched words with their respective colors
    $highlightedText = preg_replace_callback('/(' . implode('|', array_keys($matchedWords)) . ')/i',
        function ($matches) use ($matchedWords) {
            $color = $matchedWords[$matches[1]];
            return '<span style="background-color: ' . $color . ';">' . $matches[1] . '</span>';
        },
        $randomText);

    echo "<pre>$highlightedText</pre>";
	
	echo "<h3>Word Counts:</h3>";
	foreach ($wordCounts as $word => $count) {
        echo "$word appears $count times.<br>";
    }
}

?>