<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/config/security_headers.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auto_archive.php';
require_once __DIR__ . '/lang.php';

/** @var mysqli $conn */

if (!isset($lang[$currentLanguage])) {
    $currentLanguage = 'en';
}

if (!isset($_SESSION['user'])) {

    header(
        'Location: /correspondence-system/auth/login.php'
    );

    exit;
}

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));
}

if (
    !isset($_SESSION['selected_establishment'])
) {

    $_SESSION['selected_establishment'] = 'ALL';
}

if (
    isset($_POST['selected_establishment'])
) {

    if (
        !isset($_POST['csrf_token'])
        ||
        !hash_equals(
            $_SESSION['csrf_token'],
            (string) $_POST['csrf_token']
        )
    ) {

        die('Invalid Request');
    }

    $_SESSION['selected_establishment'] =
        trim(
            (string) $_POST['selected_establishment']
        );

    header(
        'Location: ' . $_SERVER['PHP_SELF']
    );

    exit;
}

$adminRole =
    $_SESSION['admin_role']
    ?? '';

$currentLanguage =
    $_SESSION['lang']
    ?? 'en';

?>

<link
rel="stylesheet"
href="/correspondence-system/style.css"
>

<style>

body{
margin:0;
font-family:Arial,sans-serif;
background:#eaeaea;
}

.top-header{
display:flex;
justify-content:space-between;
align-items:center;
padding:15px;
background:white;
box-shadow:0 2px 8px rgba(0,0,0,.15);
flex-wrap:wrap;
}

.language-box{
display:flex;
gap:10px;
}

.language-box a{
text-decoration:none;
padding:8px 14px;
border:2px solid #2c3e50;
border-radius:8px;
font-weight:bold;
color:#2c3e50;
}

.language-box a:hover{
background:#2c3e50;
color:white;
}

.right-section{
display:flex;
align-items:center;
gap:15px;
}

.establishment-box{
padding:8px;
border:2px solid #2c3e50;
border-radius:10px;
}

.establishment-box select{
padding:8px;
border-radius:8px;
}

.user-box{
display:flex;
align-items:center;
gap:15px;
padding:10px;
background:white;
border:2px solid #2c3e50;
border-radius:20px;
}

.user-name{
font-weight:bold;
font-size:18px;
color:#2c3e50;
}

.user-role{
font-size:12px;
color:#666;
}

.logout-btn{
background:#e74c3c;
color:white;
padding:8px 12px;
border:none;
border-radius:10px;
cursor:pointer;
}

.logout-btn:hover{
background:#c0392b;
}

.top-box{
margin:20px;
padding:15px;
background:white;
border-radius:10px;
border:3px solid #2c3e50;
}

.menu-box{
display:flex;
justify-content:center;
align-items:center;
gap:15px;
flex-wrap:wrap;
}

.menu-box a{
background:#2c3e50;
color:white;
padding:10px 15px;
border-radius:8px;
text-decoration:none;
}

.menu-box a:hover{
background:#f1c40f;
color:black;
}

.dropdown{
position:relative;
}

.menu-btn{
background:#e74c3c;
color:white;
border:none;
width:70px;
height:40px;
border-radius:20px;
font-size:18px;
cursor:pointer;
}

.dropdown-content{
display:none;
position:absolute;
top:50px;
left:0;
background:white;
min-width:260px;
border-radius:10px;
padding:8px;
box-shadow:0 4px 12px rgba(0,0,0,.2);
z-index:9999;
}

.dropdown-content.show{
display:block;
}

.dropdown-content a{
display:block;
padding:14px;
text-decoration:none;
background:white;
color:#2c3e50;
font-weight:bold;
border-radius:8px;
margin-bottom:6px;
border:1px solid #e5e5e5;
}

.dispatch-dropdown{
background:#ffffff;
position:relative;
display:inline-block;
}

.dispatch-btn{
background:#2c3e50;
color:white;
border:none;
padding:10px 15px;
border-radius:8px;
cursor:pointer;
font-size:14px;
}

.dispatch-btn:hover{
background:#34495e;
}

.dispatch-menu{
display:none;
position:absolute;
top:45px;
left:0;
background:white;
min-width:220px;
border-radius:10px;
overflow:hidden;
box-shadow:0 5px 15px rgba(0,0,0,.2);
z-index:9999;
}

.dispatch-menu a{
display:block;
padding:12px;
text-decoration:none;
font-weight:bold;
border-bottom:1px solid #eee;
background:white;
color:#2c3e50;
}

.dispatch-menu a:hover{
background:#f2f2f2;
}

.dispatch-menu.show{
display:block;
}

</style>

<div class="top-header">

<div class="language-box">

<a href="/correspondence-system/language.php?lang=en">
English
</a>

<a href="/correspondence-system/language.php?lang=mr">
मराठी
</a>

</div>

<div class="right-section">

<?php if ($adminRole === 'admin') { ?>

<div class="establishment-box">

<form method="post">

<input
type="hidden"
name="csrf_token"
value="<?= htmlspecialchars(
    $_SESSION['csrf_token'],
    ENT_QUOTES,
    'UTF-8'
) ?>"
>

<select
name="selected_establishment"
onchange="this.form.submit()"
>

<option value="ALL">
All Establishments
</option>

<?php

$query = "
SELECT establishment_name
FROM establishments
ORDER BY establishment_name ASC
";

$result = $conn->query($query);

if ($result instanceof mysqli_result) {

    while ($row = $result->fetch_assoc()) {

        $establishmentName =
            (string) $row['establishment_name'];

?>

<option
value="<?= htmlspecialchars(
    $establishmentName,
    ENT_QUOTES,
    'UTF-8'
) ?>"
<?= (
    ($_SESSION['selected_establishment'] ?? '')
    === $establishmentName
)
? 'selected'
: ''
?>
>

<?= htmlspecialchars(
    $establishmentName,
    ENT_QUOTES,
    'UTF-8'
) ?>

</option>

<?php

    }
}

?>

</select>

</form>

</div>

<?php } ?>

<div class="user-box">

<div>

<div class="user-name">

<?= htmlspecialchars(
    (string) $_SESSION['user'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</div>

<div class="user-role">

Role :
<?= htmlspecialchars(
    $adminRole,
    ENT_QUOTES,
    'UTF-8'
) ?>

<br>

Establishment :

<?php

echo htmlspecialchars(
    (
        $adminRole === 'admin'
        ? (
            $_SESSION['selected_establishment']
            ?? 'ALL'
        )
        : (
            $_SESSION['establishment']
            ?? ''
        )
    ),
    ENT_QUOTES,
    'UTF-8'
);

?>

<br>

Last Login :

<?= htmlspecialchars(
    (string) (
        $_SESSION['last_login']
        ?? 'N/A'
    ),
    ENT_QUOTES,
    'UTF-8'
) ?>

</div>

</div>

<form
method="post"
action="/correspondence-system/auth/logout.php"
>

<input
type="hidden"
name="csrf_token"
value="<?= htmlspecialchars(
    $_SESSION['csrf_token'],
    ENT_QUOTES,
    'UTF-8'
) ?>"
>

<button
type="submit"
class="logout-btn"
>

Logout

</button>

</form>

</div>

</div>

</div>

<div class="top-box">

<div class="menu-box">

<div class="dropdown">

<button
class="menu-btn"
id="menuButton"
>

☰

</button>

<div
class="dropdown-content"
id="menuContent"
>

<a href="/correspondence-system/auth/change_password.php">
🔑 Change Password
</a>

<a href="/correspondence-system/outward/transactions.php">
💰 Amount Transactions
</a>

<a href="/correspondence-system/archive/archive.php">
📦 Archive Records
</a>

<?php if ($adminRole === 'admin') { ?>

<a href="/correspondence-system/admin/activity_log.php">
📋 Activity Log
</a>

<a href="/correspondence-system/admin/change_inward_id.php">
✏️ Change Inward ID
</a>

<a href="/correspondence-system/admin/change_outward_id.php">
✏️ Change Outward ID
</a>

<a href="/correspondence-system/admin/import_data.php">
📥 Import Old Data
</a>

<a href="/correspondence-system/admin/establishments.php">
🏢 Manage Establishments
</a>

<a href="/correspondence-system/admin/add_user.php">
👤 Add User
</a>

<?php } ?>

</div>

</div>

<a href="/correspondence-system/dashboard.php">

<?= htmlspecialchars(
    (string) $lang[$current]['dashboard'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</a>

<?php

if (
    $adminRole !== 'admin'
    ||
    (
        $_SESSION['selected_establishment']
        ?? 'ALL'
    ) !== 'ALL'
) {

?>

<a href="/correspondence-system/inward/add.php">

<?= htmlspecialchars(
    (string) $lang[$current]['inward_title'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</a>

<a href="/correspondence-system/outward/add.php">

<?= htmlspecialchars(
    (string) $lang[$current]['outward_title'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</a>

<?php } ?>

<a href="/correspondence-system/inward/list.php">

<?= htmlspecialchars(
    (string) $lang[$current]['inward'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</a>

<a href="/correspondence-system/outward/list.php">

<?= htmlspecialchars(
    (string) $lang[$current]['outward'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</a>

<div class="dispatch-dropdown">

<button class="dispatch-btn">

<?= (
    $currentLanguage === 'mr'
)
? ' पाठवणी ▼'
: ' Dispatch ▼'
?>

</button>

<div class="dispatch-menu">

<a href="/correspondence-system/dispatch/add.php?lang=<?= urlencode($currentLanguage) ?>">

<?= (
    $currentLanguage === 'mr'
)
? '➕ पाठवणी जोडा'
: '➕ Add Dispatch'
?>

</a>

<a href="/correspondence-system/dispatch/list.php?lang=<?= urlencode($currentLanguage) ?>">

<?= (
    $currentLanguage === 'mr'
)
? '📋 पाठवणी यादी'
: '📋 Dispatch List'
?>

</a>

</div>

</div>

<a href="/correspondence-system/outward/balance.php">

<?= htmlspecialchars(
    (string) $lang[$current]['outward_balance'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</a>

</div>

</div>

<script>

const menuButton =
document.getElementById(
    "menuButton"
);

const menuContent =
document.getElementById(
    "menuContent"
);

if (menuButton && menuContent) {

    menuButton.addEventListener(
        "click",
        function (e) {

            e.stopPropagation();

            menuContent.classList.toggle(
                "show"
            );
        }
    );

    document.addEventListener(
        "click",
        function () {

            menuContent.classList.remove(
                "show"
            );
        }
    );
}

let inactiveTime = 10 * 60 * 1000;

let logoutTimer;

function resetTimer() {

    clearTimeout(logoutTimer);

    logoutTimer = setTimeout(
        function () {

            alert(
                "Session expired due to inactivity"
            );

            window.location.href =
                "/correspondence-system/auth/login.php?expired=1";

        },
        inactiveTime
    );
}

[
    "mousemove",
    "click",
    "keydown",
    "scroll",
    "touchstart"
].forEach(function(event){

    document.addEventListener(
        event,
        resetTimer
    );
});

resetTimer();

const dispatchBtn =
document.querySelector(".dispatch-btn");

const dispatchMenu =
document.querySelector(".dispatch-menu");

if (dispatchBtn && dispatchMenu) {

    dispatchBtn.addEventListener(
        "click",
        function (e) {

            e.stopPropagation();

            dispatchMenu.classList.toggle(
                "show"
            );
        }
    );

    document.addEventListener(
        "click",
        function () {

            dispatchMenu.classList.remove(
                "show"
            );
        }
    );
}

</script>
