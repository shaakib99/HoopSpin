<?php
    /**
     * constants.php contains necessary constant variable to make this
     * script work.
     */
    include 'constant.php';

    /**
     * readTextFileAndReturnAsKeyVal() reads tab separated row from a TXT file
     * it sets left variable as key and right variable as word to be spinned.
     * Both left and right can be space,' and - separated.
     * If uppercase character appears in the key, it makes the character lowercase character, in terms of
     * keeping a common format beetween key and word to spinned.
     * Special case İ and i are handled separately since PHP is not aware of locale.
     */
    function readTextFileAndReturnAsKeyVal(){
        $filePath = $GLOBALS['DEFAULT_TEXT_FILE_PATH'];
        $filePathError = $GLOBALS['FILE_NOT_FOUND_ERROR'];
        $file = fopen($filePath,'r') or die($filePathError);
        $finalArray = array();
        while(!feof($file)){
            $line = fgets($file);
            [$inputText,$spinText] = explode("\t", $line);
            $inputText = mb_ereg_replace('İ', 'i', $inputText);
            $finalArray[mb_strtolower($inputText)] = mb_strtolower($spinText);
        }
        fclose($file);
        return $finalArray;
    }
    /**
     * createReturnData takes necessary parameters to  create return data
     * @param $finalOutput - final version of spinned text.
     * @param $TWC - Total word counted during the spin.
     * @param $NWC - Number of word spinned during the spin.
     * @param $errorMessage - in general error message to display at the frontend.
     */
    function createReturnData($finalOutput, $TWC, $NWC, $errorMessage){
        if($errorMessage){
            $returnData['errorMessage'] = $errorMessage;
            return $returnData;
        }
        if($TWC == 0){
            $returnData['errorMessage'] = $GLOBALS['EMPTY_INPUT_STRING_MESSAGE'];
            return $returnData;
        }
        $returnData['finalString'] = $finalOutput;
        $PCT = $TWC ? ($NWC / $TWC) * 100 : 0;
        $SRST = calculateSRST($PCT);
        $ORST = 100 - $SRST;

        $returnData['otherInfo']['TWC'] = $TWC;
        $returnData['otherInfo']['NWC'] = $NWC;
        $returnData['otherInfo']['PCT'] = round($PCT, $GLOBALS['DECIMAL_POINT']).'%';
        $returnData['otherInfo']['SRST'] = round($SRST,$GLOBALS['DECIMAL_POINT']).'%';
        $returnData['otherInfo']['ORST'] = round($ORST,$GLOBALS['DECIMAL_POINT']).'%';
        return $returnData;
    }
    /**
     * mapWordToSpinText() takes a sentence and a dictionary.
     * @param $sentence - sentence could be a word or more than a word separated by space, ' or -.
     * @param $dictionary - dictionary is the key value pair of the TXT file.
     * Special case İ and i are handled separately for each word since PHP is not aware of locale.
     * All words are treated in lowercase alphabet since every key in the dictionary will be in lowercase.
     */
    function mapWordToSpinText($sentence, $dictionary){
        // Extra space at last of the sentence is need to be added since
        // the whole sentence without the extra space could be in the key.
        $sentence .= ' ';
        $sentence = mb_str_split($sentence);
        $TWC = 0;
        $NWC = 0;
        $finalString = '';
        $alreadySpinned = array();
        for($i = 0; $i < count($sentence); $i++){
            if($sentence[$i] == ' '){
                $finalString .= ' ';
                continue;
            }
            $lastIndex= -1;
            $spinnedWord = '';
            $unspinnedWord = '';
            for($j = 1; $j < count($sentence) - $i + 1; $j++){
                if($sentence[$i + $j] != ' ') continue;
                $slicedArr = array_slice($sentence,$i, $j);
                $word = join('', $slicedArr);
                $word = mb_ereg_replace('İ', 'i', $word);
                $wordInLower = mb_strtolower($word);
                if(!array_key_exists($wordInLower, $dictionary)) continue;
                if(array_key_exists($wordInLower, $alreadySpinned)) continue;
                $lastIndex = $i + $j;
                $spinnedWord = trim($dictionary[$wordInLower]);
                $unspinnedWord = trim($wordInLower);
            }
            if($lastIndex != -1){
                $alreadySpinned[$unspinnedWord] = TRUE;
                $totalWord = count(mb_split('/\s/', $spinnedWord));
                $TWC += $totalWord;
                $NWC += $totalWord;
                if(preg_match($GLOBALS['REG_EXP_TO_MATCH_UPPER_CASE'], $sentence[$i])){
                    $spinnedWord = mb_str_split($spinnedWord);
                    if($spinnedWord[0] == 'i'){
                        $spinnedWord[0] = 'İ';
                    }else{
                        $spinnedWord[0] = mb_strtoupper($spinnedWord[0]);
                    }
                    $spinnedWord = join('',$spinnedWord);
                }
                $finalString .= $spinnedWord;
                $i = $lastIndex - 1;
                continue;
            }
            $TWC += 1;
            $j = $i;
            $word = '';
            while($j < count($sentence) && $sentence[$j] != ' '){
                $word .= $sentence[$j];
                $j++;
            }
            $i = $j - 1;
            $finalString .= '<span style = "background-color:'.$GLOBALS['UNSPINNABLE_TEXT_COLOR'].'">'.$word.'</span>';
        }
        $returnData['finalString'] = preg_replace('/ $/', '', $finalString);
        $returnData['TWC'] = $TWC;
        $returnData['NWC'] = $NWC;

        return $returnData;
    }
    /**
     * spinText() takes the text as input from user
     * @param $inputText is received as POST request from the user.
     */
    function spinText($inputText){
        $dictionary = readTextFileAndReturnAsKeyVal();
        $allSentences = preg_split($GLOBALS['REG_EXP_TO_MATCH_PUNCTUATIONS'],$inputText);
        preg_match_all($GLOBALS['REG_EXP_TO_MATCH_PUNCTUATIONS'], $inputText, $allPunctuations);
        $allPunctuations = $allPunctuations[0];
        $finalOutput = '';
        $puncIndex = 0;
        $TWC = 0;
        $NWC = 0;
        for ($i=0; $i < count($allSentences); $i++) {
            if($TWC > $GLOBALS['MAX_FREE_WORD_SUPPORT'])
                return createReturnData('',0, 0, $GLOBALS['WORD_LIMIT_EXCEEDED_MESSAGE']);
            $sentence = $allSentences[$i];
            $output = mapWordToSpinText($sentence, $dictionary);
            $finalOutput .= $output['finalString'];
            $TWC += $output['TWC'];
            $NWC += $output['NWC'];
            $punctuationMark = $puncIndex < count($allPunctuations) ? $allPunctuations[$puncIndex] : '';
            if($punctuationMark == "\n") $punctuationMark  = '<br>';
            $finalOutput .= $punctuationMark;
            $puncIndex++;
        }
        return createReturnData($finalOutput, $TWC, $NWC, NULL);
    }
    /**
     * calculateSRST() receives PCT as input and calculate SRST from it.
     * @param $PCT - Percentage of changed text in the input text.
     */
    function calculateSRST($PCT){
        $PCTType = gettype($PCT);
        if($PCTType != 'double' && $PCTType != 'integer') throw new Exception("PCT Must be a double or integer");

        if ($PCT >= 0 && $PCT <= 27) return 100 - $PCT;
        elseif ($PCT > 27 && $PCT <= 63){
            $val = 129.514616957027-(2.05509082462253*$PCT);
            $val = $val > 0 ? $val : 0;
            return $val;
        }
        elseif ($PCT > 63 && $PCT <= 100) return 0;
        else throw new Exception("Error Processing PCT. PCT must be within 0, 100");
    }


    $inputString = $_POST['inputString'];
    echo json_encode(spinText($inputString));
?>