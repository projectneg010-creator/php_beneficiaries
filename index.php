<?php
include 'db.php';

// Handle Add Beneficiary
if (isset($_POST['add'])) {
    $names = $_POST['names'] ?? '';
    $program = $_POST['program'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $school = $_POST['school'] ?? '';
    $date = $_POST['date'] ?? '';

    $stmt = $conn->prepare("INSERT INTO beneficiaries (names, program, amount, school, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $names, $program, $amount, $school, $date);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Update Beneficiary
if (isset($_POST['update'])) {
    $id = $_POST['id'] ?? 0;
    $names = $_POST['names'] ?? '';
    $program = $_POST['program'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $school = $_POST['school'] ?? '';
    $date = $_POST['date'] ?? '';

    $stmt = $conn->prepare("UPDATE beneficiaries SET names=?, program=?, amount=?, school=?, date=? WHERE id=?");
    $stmt->bind_param("ssdssi", $names, $program, $amount, $school, $date, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle Delete Beneficiary
if (isset($_POST['delete'])) {
    $id = $_POST['id'] ?? 0;

    $stmt = $conn->prepare("DELETE FROM beneficiaries WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch beneficiary for editing if ?edit=id provided
$editBeneficiary = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE id=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editBeneficiary = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Beneficiaries Management</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: auto; padding: 20px;}
        input, select { padding: 6px; margin-bottom: 10px; width: 100%; box-sizing: border-box;}
        label { font-weight: bold; display: block; margin-top: 10px; }
        button { padding: 8px 12px; margin-top: 10px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px;}
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left;}
        th { background: #eee; }
        .action-btn { padding: 5px 10px; margin-right: 5px; }
    </style>
</head>
<body>

<h2><?= $editBeneficiary ? "Edit Beneficiary" : "Add New Beneficiary" ?></h2>

<form method="POST" action="">
    <?php if ($editBeneficiary): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($editBeneficiary['id']) ?>">
    <?php endif; ?>

    <label for="names">Names:</label>
    <input type="text" name="names" id="names" required value="<?= $editBeneficiary ? htmlspecialchars($editBeneficiary['names']) : '' ?>">

    <label for="program">Program (A or B):</label>
    <select name="program" id="program" required>
        <option value="">Select Program</option>
        <option value="A" <?= $editBeneficiary && $editBeneficiary['program']=='A' ? 'selected' : '' ?>>A</option>
        <option value="B" <?= $editBeneficiary && $editBeneficiary['program']=='B' ? 'selected' : '' ?>>B</option>
    </select>

    <label for="amount">Amount:</label>
    <input type="number" step="0.01" name="amount" id="amount" required value="<?= $editBeneficiary ? htmlspecialchars($editBeneficiary['amount']) : '' ?>">

    <label for="school">School:</label>
    <input type="text" name="school" id="school" required value="<?= $editBeneficiary ? htmlspecialchars($editBeneficiary['school']) : '' ?>">

    <label for="date">Date:</label>
    <input type="date" name="date" id="date" required value="<?= $editBeneficiary ? htmlspecialchars($editBeneficiary['date']) : '' ?>">

    <button type="submit" name="<?= $editBeneficiary ? 'update' : 'add' ?>">
        <?= $editBeneficiary ? 'Update Beneficiary' : 'Add Beneficiary' ?>
    </button>

    <?php if ($editBeneficiary): ?>
        <a href="<?= $_SERVER['PHP_SELF'] ?>">Cancel Edit</a>
    <?php endif; ?>
</form>

<hr>

<h2>Search Beneficiaries</h2>
<input type="text" id="searchBox" placeholder="Search by any field..." oninput="liveSearch()">

<div id="results">
    <!-- Full table will load here via AJAX -->
</div>

<script>
function liveSearch() {
    const query = document.getElementById('searchBox').value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?search=' + encodeURIComponent(query), true);
    xhr.onload = function () {
        if (this.status === 200) {
            document.getElementById('results').innerHTML = this.responseText;
        } else {
            document.getElementById('results').innerHTML = 'Error loading data.';
        }
    };
    xhr.onerror = function () {
        document.getElementById('results').innerHTML = 'Request error.';
    };
    xhr.send();
}

// Load all on page load
document.addEventListener('DOMContentLoaded', liveSearch);

// Confirm delete and submit hidden form
function deleteUser(id) {
    if (confirm("Are you sure you want to delete this beneficiary?")) {
        // Create and submit a form to delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';

        const inputId = document.createElement('input');
        inputId.name = 'id';
        inputId.value = id;

        const inputDelete = document.createElement('input');
        inputDelete.name = 'delete';
        inputDelete.value = '1';

        form.appendChild(inputId);
        form.appendChild(inputDelete);

        document.body.appendChild(form);
        form.submit();
    }
}

function editUser(id) {
    window.location.href = "<?= $_SERVER['PHP_SELF'] ?>?edit=" + id;
}
</script>

</body>
</html>
