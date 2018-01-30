<?php

// Check if they passed two arguments, one for the csv with parameter values and the other with the name of the outbound file.
if ($argc !== 3) {
  exit("Please pass two arguments, a comma-separated value file with parameters and the name of the outbound file.\n");
}

// Assign arguments to parameter and outbound file.
$param_file = $argv[1];
$queries_file = $argv[2];

// If the paramter file does not exist, end program.
if (!file_exists($param_file)) {
  exit("Error: File \"$param_file\" not found. \n");
}

// Set variables for program.
$csv = array_map('str_getcsv', file($param_file));
$params = [];
$column_counter = 1;

// Put header in outbound file.
file_put_contents($queries_file, "id,query\n");

// Add each parameter as the single parameter in the query.
foreach ($csv as $value) {
  $query = "$value[0]=$value[1]";
  $row = $column_counter . ",?" . $query . "\n";
  file_put_contents($queries_file, ($row), FILE_APPEND);
  array_push($params, $query);

  echo $row;

  $column_counter++;
}

// Generate query with all parameters included.
$all_args = implode (", ", $params);
file_put_contents($queries_file, ($column_counter . ",?" . $all_args . "\n"), FILE_APPEND);
echo $column_counter . ",?" . $all_args . "\n";
$column_counter++;

// Factorial determines depth of permutation for testing.
$factorial = 1;
$list = $params;

// Runs $factorial + 1 number of times to generate a combination of query strings of up up to $factorial + 1 combinations.
for ($i=0; $i < $factorial; $i++) {

  $tmp_list = $list;

  foreach ($tmp_list as $li) {
    foreach ($params as $param) {

      if (strpos($li, $param) === false) {
        $new = "$li&$param";
        array_push($list, $new);
        $row = $column_counter . ",?" . $new . "\n";
        file_put_contents($queries_file, ($row), FILE_APPEND);

        echo "$row";

        $column_counter++;
      }
    }
  }
}


?>
