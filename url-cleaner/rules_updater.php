<?php
// Create a cron job to run this script to update "merged_rules.json"

// Functions
require_once ("functions.php");

// Rules from external source: AdguardFilters
$adg_general_rule_json = get_adg_general_rules("https://github.com/AdguardTeam/AdguardFilters/raw/master/TrackParamFilter/sections/general_url.txt");
$adg_specific_rule_json = get_adg_specific_rules("https://github.com/AdguardTeam/AdguardFilters/raw/master/TrackParamFilter/sections/specific.txt");
$yzw_specific_rule_json = get_adg_specific_rules("https://github.com/yingziwu/ublock-rules/raw/master/src/trackparam.txt");

// Rules from local file "local_rules.json"
$local_rule_filename = "./local_rules.json";
$handle = fopen($local_rule_filename, "r");
$local_rule_json = fread($handle, filesize($local_rule_filename));
fclose($handle);

// Merge rules
$rules_json = json_encode(
    array_merge(
        json_to_providers_array($adg_general_rule_json),
        json_to_providers_array($adg_specific_rule_json),
        json_to_providers_array($yzw_specific_rule_json),
        json_to_providers_array($local_rule_json)
    )
);

// Write to file
$merged_rule_file = fopen("merged_rules.json", "w") or die("Unable to open file!");
fwrite($merged_rule_file, $rules_json);
fclose($merged_rule_file);
