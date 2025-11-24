<!DOCTYPE html>
<html>
<head>
    <title>Hive Cleanout</title>
</head>
<body>
    <h2>Enter emails which must not be deleted (one per line)</h2>

    <!-- Form to input array1 items -->
    <form action="" method="POST">
        <textarea name="array1" rows="5" cols="30" placeholder="Enter one item per line"></textarea><br>
        <button type="submit">Cleanout Hive</button>
    </form>

    <?php
	
	require "../classes/databaseConnection.php";
	require "../database/connection.php";
	
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the user input from the textarea
        $array1 = preg_split('/\r\n|\r|\n/', trim($_POST['array1']));
		
		$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_members_for_newsletter()";
		$result=$db->callProcedure($codeProcedure);
		
		// Initialize array2
		$array2 = [];

		// Fetch the result and populate the first array
		if ($result->num_rows > 0) {
    		while ($row = $result->fetch_assoc()) {
        		$array2[] = $row['correo_electronico'];  // Assuming the column is named 'correo_electronico'
    		}
		}

        // Define the hardcoded array2
        // $array2 = array("Dog", "Elephant", "Frog");

        // Merge the user-provided array1 with array2
        $mergedArray = array_merge($array1, $array2);

        // Convert the merged array into a JSON string
        $mergedArrayJson = json_encode($mergedArray);
    ?>

    <!-- Automatically submit the merged array to https://hive.metmeetings.org/hive-cleanout5.php via POST -->
    <form id="sendArrayForm" action="https://hive.metmeetings.org/hive-cleanout5.php" method="POST">
        <input type="hidden" name="mergedArray" value='<?php echo $mergedArrayJson; ?>'>
        <script>
            // Automatically submit the form after the array is merged
            document.getElementById('sendArrayForm').submit();
        </script>
    </form>

    <?php
    }
    ?>
</body>
</html>
