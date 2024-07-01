<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the entered password
    $password = $_POST['password'];

    // Check if the password is correct
    if ($password === 'your_password') {
        // Redirect to the desired page
        header('Location: /path/to/your/page.php');
        exit;
    } else {
        // Display an error message
        echo 'Invalid password';
    }
}
?>
<form method="POST" action="">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password">
    <button type="submit">Submit</button>
</form>