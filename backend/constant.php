<?php
$DEFAULT_TEXT_FILE_PATH = '../backend/my.txt';
$FILE_NOT_FOUND_ERROR = "Could not open file. File not found";
$EMPTY_INPUT_STRING_MESSAGE = 'Empty input string has been passed. Please pass a valid input string';
$UNSPINNABLE_TEXT_COLOR = 'yellow';
$DECIMAL_POINT = 2;
$REG_EXP_TO_MATCH_PUNCTUATIONS = '/[^'."'".'\pL\pN\- ]/u';
$REG_EXP_TO_MATCH_UPPER_CASE = '/^\p{Lu}.*/u';
$MAX_FREE_WORD_SUPPORT = 900;
$WORD_LIMIT_EXCEEDED_MESSAGE = 'Maximum word limit exceded. Maximum word can be spinned is '.$MAX_FREE_WORD_SUPPORT;
?>