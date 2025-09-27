<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$search = trim($search);

$stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE
    names LIKE CONCAT('%', ?, '%') OR
    program LIKE CONCAT('%', ?, '%') OR
    amount LIKE CONCAT('%', ?, '%') OR
    school LIKE CONCAT('%', ?, '%') OR
    date LIKE CONCAT('%', ?, '%')
");
$stmt->bind_param("sssss", $search, $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1' width='100%'>
            <thead>
                <tr>
                    <th>Names</th>
                    <th>Program</th>
                    <th>Amount</th>
                    <th>School</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['names']) . "</td>
                <td>" . htmlspecialchars($row['program']) . "</td>
                <td>" . htmlspecialchars($row['amount']) . "</td>
                <td>" . htmlspecialchars($row['school']) . "</td>
                <td>" . htmlspecialchars($row['date']) . "</td>
                <td>
                    <button onclick=\"editUser('{$row['id']}')\">Edit</button>
                    <button onclick=\"deleteUser('{$row['id']}')\">Delete</button>
                </td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No results found.</p>";
}

$stmt->close();
?>
