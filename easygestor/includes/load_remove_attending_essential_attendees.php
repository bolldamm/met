<?php

function removeAttendingEssentialAttendeesOld($db, $current_id_conferencia) {
    // Step 1: Get all essential attendees
    $resultado = $db->callProcedure("CALL ed_pr_essential_attendee_list()");

    // Step 2: Loop through each essential attendee
    while ($row = $db->getData($resultado)) {
        $attendee_id = $row["id_usuario_web"];

        // Step 3: Check if attendee is already in the current conference
        $check_result = $db->callProcedure("CALL ed_pr_get_conference_user('" . $attendee_id . "', '" . $current_id_conferencia . "')");

        // If the attendee is found in the current conference, delete from essential list
        if ($db->getNumberRows($check_result) > 0) {
            $db->callProcedure("CALL ed_pr_essential_attendee_delete('" . $attendee_id . "')");
        }
    }

    // Step 4: Get the remaining number of essential attendees
    $count_result = $db->callProcedure("CALL ed_pr_essential_attendee_count()");
    $count_row = $db->getData($count_result);

    return isset($count_row['total_attendees']) ? intval($count_row['total_attendees']) : 0;
}

function removeAttendingEssentialAttendees($db, $current_id_conferencia) {
    // echo "Starting function: removeAttendingEssentialAttendees<br>";

    // Step 1: Get all essential attendees
    // echo "Calling: CALL ed_pr_essential_attendee_list()<br>";
    $resultado = $db->callProcedure("CALL ed_pr_essential_attendee_list()");
    if (!$resultado) {
        // echo "Error: Failed to get essential attendees<br>";
        return -1;
    }

    // Step 2: Loop through each essential attendee
    while ($row = $db->getData($resultado)) {
        $attendee_id = $row["id_usuario_web"];
        // echo "Checking attendee ID: $attendee_id<br>";

        // Step 3: Check if attendee is already in the current conference
        $query = "CALL ed_pr_get_conference_user('$attendee_id', '$current_id_conferencia')";
        // echo "Calling: $query<br>";
        $check_result = $db->callProcedure($query);

        if (!$check_result) {
            // echo "Error: Failed to check conference user for attendee ID: $attendee_id<br>";
            continue;
        }

        $num_rows = $db->getNumberRows($check_result);
        // echo "Attendee $attendee_id is in conference $current_id_conferencia: " . ($num_rows > 0 ? "YES" : "NO") . "<br>";

        // If the attendee is found in the current conference, delete from essential list
        if ($num_rows > 0) {
            $delete_query = "CALL ed_pr_essential_attendee_delete('$attendee_id')";
            // echo "Deleting attendee $attendee_id from essential list. Calling: $delete_query<br>";
            $delete_result = $db->callProcedure($delete_query);

            if (!$delete_result) {
                // echo "Error: Failed to delete essential attendee ID: $attendee_id<br>";
            }
        }
    }

    // Step 4: Get the remaining number of essential attendees
    // echo "Calling: CALL ed_pr_essential_attendee_count()<br>";
    $count_result = $db->callProcedure("CALL ed_pr_essential_attendee_count()");

    if (!$count_result) {
        // echo "Error: Failed to count remaining essential attendees<br>";
        return -1;
    }

    $count_row = $db->getData($count_result);
    $total = isset($count_row['total_attendees']) ? intval($count_row['total_attendees']) : 0;

    // echo "Remaining essential attendees: $total<br>";
    // echo "Finished function: removeAttendingEssentialAttendees<br>";

    return $total;
}

?>
