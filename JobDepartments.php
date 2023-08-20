<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Add a department</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the DEPARTMENTS table exists. */
  VerifyDepartmentsTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the DEPARTMENTS table. */
  $department_name = htmlentities($_POST['NAME']);
  $department_num_employees = htmlentities($_POST['NUM_EMPLOYEES']);
  $department_is_hiring = htmlentities($_POST['IS_HIRING']);
  $department_salary = htmlentities($_POST['SALARY']);

  if (strlen($department_name) && strlen((string)$department_num_employees) && strlen($department_salary)) {
    AddDepartment($connection, $department_name, $department_num_employees, $department_is_hiring, $department_salary);
  }
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>NUM OF EMPLOYEES</td>
      <td>IS HIRING?</td>
      <td>SALARY</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="number" name="NUM_EMPLOYEES" />
      </td>
      <td>
        <input type="radio" name="IS_HIRING" value="true" id="TRUE">
          <label for="TRUE"> True</label><br>
        <input type="radio" name="IS_HIRING" value="false" id="FALSE">
          <label for="FALSE"> False</label><br>
      </td>
      <td>
        <input type="text" inputmode="decimal" pattern="[0-9]*[.,]?[0-9]*" name="SALARY" />
      </td>
      <td>
        <input type="submit" value="Add Data" />
      </td>
    </tr>
  </table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>NUM OF EMPLOYEES</td>
    <td>IS HIRING?</td>
    <td>SALARY</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM DEPARTMENTS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>",$query_data[4], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add a department to the table. */
function AddDepartment($connection, $name, $num_employees, $hiring, $salary) {
   $n = mysqli_real_escape_string($connection, $name);
   $e = mysqli_real_escape_string($connection, $num_employees);
   $h = ($hiring === 'true') ? 1 : 0;
   $s = mysqli_real_escape_string($connection, $salary);

   $query = "INSERT INTO DEPARTMENTS (NAME, NUM_EMPLOYEES, IS_HIRING, SALARY) VALUES ('$n', '$e', '$h', '$s');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding department data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyDepartmentsTable($connection, $dbName) {
  if(!TableExists("DEPARTMENTS", $connection, $dbName))
  {
     $query = "CREATE TABLE DEPARTMENTS (
        ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        NAME VARCHAR(45),
        NUM_EMPLOYEES int,
        IS_HIRING BINARY,
        SALARY float)";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
