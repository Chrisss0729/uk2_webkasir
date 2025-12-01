<?php
if (isset($_GET['pw'])) {
    echo "Password: " . $_GET['pw'] . "<br>";
    echo "Hash: " . password_hash($_GET['pw'], PASSWORD_DEFAULT);
    exit;
}
?>
<form>
    <input type="text" name="pw" placeholder="password">
    <button type="submit">Generate Hash</button>
</form>