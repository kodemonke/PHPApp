<?php
$Leads = json_decode('[{"programID":"7226","id":"10354285"},{"programID":"7226","id":"10354285"},{"programID":"7236","id":"10354285"},{"programID":"7226","id":"1035885"}]');

$junk = array_map("unserialize", array_unique(array_map("serialize", $Leads)));
echo "<br/>";
echo json_encode($junk);

?>