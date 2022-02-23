<?php


function remove_url_get_var($url, $var_name) {
    return preg_replace('/([?&])'.$var_name.'=[^&]+(&|$)/','$1',$url);
}


function json_to_providers_array($json) {
    $rules_obj = json_decode($json, FILE_USE_INCLUDE_PATH);
    $providers_array = $rules_obj['providers'];
    return $providers_array;
}


// Rules from AdGuard format filters
// Specific filters
function get_adg_specific_rules($adg_rule_url) {
    // AdguardFilters
    $filter_raw = file_get_contents($adg_rule_url);

    // Declare $rule_obj as an object of stdClass in the global namespace
    $rule_obj = new stdClass();

    // Iterate over each line in the rule set
    $provider_current_loop = ""; // initialize
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $filter_raw) as $line){
        // from each line, extract the param
        if (!empty($line) && $line[0] == "|" && strpos($line, "=") ) {

            // Get the pattern, provider name, and param from this line
            $line_arr = explode('$', $line);
            $pattern = $line_arr[0];
            $provider = preg_replace('/[^A-Za-z0-9\-]/', '', $pattern); // get a provider name in clean format
            $pattern = preg_replace('/[^A-Za-z0-9.*\/\-]/', '', $pattern); // only keep digits, letters, * and /
            $pattern =  str_replace("/","\/", $pattern); // escape "/"
            $pattern =  "/" . $pattern . "/"; // convert to regex
            $param = explode('=',$line_arr[1])[1];
            echo "pattern: ". $pattern . "\n";
            echo "provider: ". $provider . "\n";
            echo "param: ". $param . "\n";

            // Create a new provider if it's not the same provide as in the last loop
            if ($provider != $provider_current_loop) {
                //$rule_obj->providers->$provider = (object)[];
                $rule_obj->providers->$provider->pattern = $pattern;
                $rule_obj->providers->$provider->rules = array();
            } else {
                // Append the pattern
                $rule_obj->providers->$provider->pattern = $pattern;
            }

            // Append the param
            array_push($rule_obj->providers->$provider->rules, $param);

            // Save a copy of the provider name for the next loop
            $provider_current_loop = $provider;
        }
    }

    $result = json_encode($rule_obj);
    echo $result;
    return $result;
}


// Rules from AdGuard format filters
// General rules
function get_adg_general_rules($adg_rule_url) {
    // AdguardFilters
    $filter_raw = file_get_contents($adg_rule_url);

    // Declare $rule_obj as an object of stdClass in the global namespace
    $rule_obj = new stdClass();
    $rule_obj->providers->general->pattern = "*";
    $rule_obj->providers->general->rules = array();

    // Iterate over each line in the rule set
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $filter_raw) as $line){
        // from each line, extract the param
        if (!empty($line) && $line[0] == "$" && strpos($line, "=") ) {

            // Get the pattern, provider name, and param from this line
            $line_arr = explode('=', $line);
            $param = $line_arr[1];
            echo "param: ". $param . "\n";

            // Append the param
            array_push($rule_obj->providers->general->rules, $param);

            // Save a copy of the provider name for the next loop
        }
    }

    $result = json_encode($rule_obj);
    echo $result;
    return $result;
}


// Clean the URL
function clean_url($url) {
    // Validate URL
    if (
        !isset($url) ||
        (
            !filter_var($url, FILTER_VALIDATE_URL) &&
            !filter_var(base64_decode($url), FILTER_VALIDATE_URL)
        )
    )
    {
        $API_Obj = new stdClass();
        $API_Obj->original_url = $url;
        $API_Obj->cleaned_url = "Error: Invalid URL provided.";
        $result = json_encode($API_Obj);
        echo $result;
        exit;
    }

    // Decode if it's base64 URL
    if (filter_var(base64_decode($url), FILTER_VALIDATE_URL)) {
        $url = base64_decode($url);
    }

    // Get merged rules stored in local
    $merged_rule_filename = "./merged_rules.json";
    $handle = fopen($merged_rule_filename, "r");
    $rules_json = fread($handle, filesize($merged_rule_filename));
    fclose($handle);

    // Clean blacklisted variables in URL
    // Using JSON rules
    $providers_array = json_decode($rules_json, FILE_USE_INCLUDE_PATH);
    foreach($providers_array as $provider) {
        //if (strpos($url, $provider['pattern'])) {
        // echo $provider['pattern'] . "\n";
        if (preg_match($provider['pattern'], $url) || $provider['pattern'] == "*") {
            $rules = $provider['rules'];
            foreach($rules as $rule) {
                $url = remove_url_get_var($url, $rule);
            }
        }
    }

    // Clean the tailing char
    if ($url[-1] == "?" || $url[-1] == "&") {
        $url = substr_replace($url, "", -1);
    }

    return $url;
}

