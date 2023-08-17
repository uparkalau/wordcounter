
# Word Counter Web App

The Word Counter Web App is a simple tool that allows you to count the occurrences of specific words in a randomly generated text. It can be useful for educational purposes to understand basic text processing and search algorithms.

## Getting Started

1. Clone the repository to your local machine:
   ```
   git clone https://github.com/uparkalau/wordcounter.git
   ```

2. Navigate to the project directory:
   ```
   cd wordcounter
   ```

## Usage

### index.php

1. Open `index.php` in your web browser.
2. Fill in the word fields (`word1`, `word2`, and `word3`) with the words you want to count.
3. Optionally, provide the desired text length and a custom random text. If not provided, default values will be used.
4. Click the "Count Words" button.
5. The page will display the matched words highlighted in different colors and their occurrences.

### word_counter.php

The `word_counter.php` script provides the backend logic for counting words in the generated text.

1. The `BasicTextGenerator` class generates random text based on the provided word list.
2. The `WordCounter` class counts occurrences of words using the Knuth-Morris-Pratt algorithm.
3. The script reads POST data from the form, generates random text, and counts word occurrences.
4. Matched words are highlighted with different colors.

## Notes

- The project uses PHP and HTML for the user interface and backend logic.
- The Knuth-Morris-Pratt algorithm is used for efficient word counting.
- jQuery and AJAX are utilized for asynchronous form submission without page reload.
- Bootstrap is used for styling the form and layout.

## License

This project is licensed under the MIT License. Feel free to use, modify, and distribute it as needed.

```
