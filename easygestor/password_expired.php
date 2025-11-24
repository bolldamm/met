<?php
  
  require "includes/load_main_components.inc.php";

$message = "";
$title = "Change Password";
$extra_header = "";
$username = "";
$display_var = "";
$required_var = "required";
$default_var = "";

// Handle form submission
if (isset($_POST["old_password_input"], $_POST["new_password"], $_POST["confirm_password"], $_POST["id_usuario"], $_POST["username"], $_POST["virus_scan"])) {
    $id_usuario = intval($_POST["id_usuario"]);
	$username = $_POST["username"];
    $old_password_input = $_POST["old_password_input"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Verify old password
 		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_obtener_login('".generalUtils::escaparCadena($username)."','".generalUtils::escaparCadena($old_password_input)."')");

  // Call stored procedure to check if password was used before
$result = $db->callProcedure("CALL ed_pr_check_old_password('".$id_usuario."','".generalUtils::escaparCadena($new_password)."')");
  // Fetch the first row
$row = mysqli_fetch_assoc($result);
// $row = $db->fetchRow($result); // assuming this fetches a single row as an array
    if ($username !== "" && $db->getNumberRows($resultado) == 0) { 
        $message = "Old password is incorrect.";
    } elseif ($new_password === $old_password_input) {
        $message = "New password cannot be the same as your old password.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $message = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $message = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $message = "Password must contain at least one number.";
    } elseif (!preg_match('/[!@#$%^&*()_\-+=\[\]{};:,.<>?]/', $new_password)) {
        $message = "Password must contain at least one special character.";
    } elseif ($row['password_exists'] == 1) {
    $message = "You cannot reuse an old password.";
    } else {
        // Update password
        $activated = date("Y-m-d"); // today's date in YYYY-MM-DD format
        $db->callProcedure("CALL ed_pr_save_eg_password(".$id_usuario.", '".$new_password."', '".$activated."')");
        // Store old password
        $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_add_old_password('".$id_usuario."','".generalUtils::escaparCadena($old_password_input)."')");
        $message = "Password successfully updated. You will be redirected to the log-in page in five seconds.";
        $extra_header = "<meta http-equiv='refresh' content='5;url=https://www.metmeetings.org/easygestor/logout.php'>";
      	$title = "Password Changed";
      	$default_var = "";
      	$required_var = "required";
    }
} elseif (isset($_POST["id_usuario"])) {
    $id_usuario = intval($_POST["id_usuario"]);
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
        $title = "Password Expired";
    } else {
        $title = "Reset Password";
        $display_var = "style='display:none;'";
        $default_var = "s";
        $required_var = "";
    }
} else {
    $id_usuario = intval($_SESSION["user"]["id"]);
	$username = $_SESSION["user"]["username"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<?= $extra_header ?>
<title><?= $title ?></title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.expired-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    width: 400px;
    text-align: center;
}
.expired-container h2 { color: #c0392b; margin-bottom: 1rem; }
.expired-container p { margin-bottom: 1rem; }
.expired-container form { position: relative; }
.expired-container form input[type="password"],
.expired-container form input[type="text"] {
    width: 100%;
    padding: 0.6rem 2.5rem 0.6rem 0.6rem;
    margin-bottom: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.expired-container form label {
    display: block;
    margin: 0.5rem 0;
    text-align: left;
    font-size: 0.9rem;
}
.expired-container form button {
    width: 100%;
    padding: 0.7rem;
    background: #2980b9;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
}
.expired-container form button:disabled {
    background: #999;
    cursor: not-allowed;
}
.expired-container form button:hover:enabled {
    background: #1f6391;
}
.message { margin-bottom: 1rem; color: red; }
.success { color: green; }
.strength {
    text-align: left;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}
.strength span { display: block; margin: 2px 0; }
.valid { color: green; }
.invalid { color: red; }
#strength-bar {
    width: 100%;
    height: 8px;
    background: #ddd;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}
#strength-bar-fill {
    height: 100%;
    width: 0%;
    background: red;
    border-radius: 4px;
    transition: width 0.3s;
}
.eye-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    user-select: none;
    font-size: 1rem;
}
.invalid-old { color: red; margin-top: 0.2rem; font-size:0.9rem;}
</style>
</head>
<body>
<div class="expired-container">
    <h2><?= $title ?></h2>

    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos($message, 'successfully') !== false ? 'success' : 'message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form action="password_expired.php" method="POST" id="resetForm">
        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>">
		<input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">

        <!-- Old password input -->
        <div <?= $display_var ?> style="position: relative; margin-bottom: 1rem;">
            <input type="password" id="old_password_input" name="old_password_input" placeholder="Enter your old password" value="<?= $default_var ?>" <?= $required_var ?>>
            <span class="eye-icon" id="toggleOld">👁️</span>
        </div>

        <!-- New password -->
        <div style="position: relative; margin-bottom: 1rem;">
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
            <span class="eye-icon" id="toggleNew">👁️</span>
        </div>

        <!-- Strength bar -->
        <div id="strength-bar">
            <div id="strength-bar-fill"></div>
        </div>
        <div class="strength" id="strength-check">
            <span id="length" class="invalid">• At least 8 characters</span>
            <span id="uppercase" class="invalid">• At least one uppercase letter</span>
            <span id="lowercase" class="invalid">• At least one lowercase letter</span>
            <span id="number" class="invalid">• At least one number</span>
            <span id="special" class="invalid">• At least one special character</span>
        </div>

        <!-- Confirm password -->
        <div style="position: relative; margin-bottom: 1rem;">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
            <span class="eye-icon" id="toggleConfirm">👁️</span>
        </div>

        <!-- Checkbox -->
        <label>
            <input type="checkbox" id="virus_scan" name="virus_scan" required>
            I have recently scanned my PC for viruses
        </label>

        <button type="submit" id="submitBtn" disabled>Reset Password</button>
    </form>
</div>

<script>
const oldPasswordInput = document.getElementById("old_password_input");
const passwordInput = document.getElementById("new_password");
const confirmInput = document.getElementById("confirm_password");
const virusScanCheckbox = document.getElementById("virus_scan");
const submitBtn = document.getElementById("submitBtn");
const strengthFill = document.getElementById("strength-bar-fill");

const checks = {
    length: document.getElementById("length"),
    uppercase: document.getElementById("uppercase"),
    lowercase: document.getElementById("lowercase"),
    number: document.getElementById("number"),
    special: document.getElementById("special")
};

function updateStrengthBar(score) {
    const colors = ["red","orange","yellow","lightgreen","green"];
    strengthFill.style.width = (score*20)+"%";
    strengthFill.style.background = colors[score-1] || "red";
}

function validatePassword() {
    const value = passwordInput.value;
    let valid = true;
    let score = 0;

    if(value.length >= 8){checks.length.className="valid";score++;}else{checks.length.className="invalid";valid=false;}
    if(/[A-Z]/.test(value)){checks.uppercase.className="valid";score++;}else{checks.uppercase.className="invalid";valid=false;}
    if(/[a-z]/.test(value)){checks.lowercase.className="valid";score++;}else{checks.lowercase.className="invalid";valid=false;}
    if(/[0-9]/.test(value)){checks.number.className="valid";score++;}else{checks.number.className="invalid";valid=false;}
    if(/[!@#$%^&*()_\-+=\[\]{};:,.<>?]/.test(value)){checks.special.className="valid";score++;}else{checks.special.className="invalid";valid=false;}

    // Check new password differs from old password input
    if (oldPasswordInput.value && value === oldPasswordInput.value) {
        valid = false;
    }

    updateStrengthBar(score);
    submitBtn.disabled = !(valid && virusScanCheckbox.checked && oldPasswordInput.value.length > 0 && confirmInput.value.length > 0);
}

passwordInput.addEventListener("input", validatePassword);
confirmInput.addEventListener("input", validatePassword);
oldPasswordInput.addEventListener("input", validatePassword);
virusScanCheckbox.addEventListener("change", validatePassword);

// Eye icons
document.getElementById("toggleOld").addEventListener("click", ()=>{ oldPasswordInput.type = oldPasswordInput.type==="password"?"text":"password"; });
document.getElementById("toggleNew").addEventListener("click", ()=>{ passwordInput.type = passwordInput.type==="password"?"text":"password"; });
document.getElementById("toggleConfirm").addEventListener("click", ()=>{ confirmInput.type = confirmInput.type==="password"?"text":"password"; });
</script>
</body>
</html>