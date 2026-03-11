<?php
// Printable view for Inventory — triggers browser print dialog so user can save as PDF
session_start();
include 'connection.php';
include 'auth_check.php';

// Fetch items and department
$sql = "SELECT i.*, d.dept_name FROM item_tbl i LEFT JOIN department_tbl d ON i.dept_id = d.dept_id ORDER BY i.item_name ASC";
$rows = [];
if ($result = $conn->query($sql)) {
    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }
    $result->free();
}
$conn->close();

// Simple date for filename suggestion
$now = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Print - SCC Inventory</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        /* Print friendly styles */
        @page { size: A4; margin: 20mm; }
        body { font-family: "Helvetica", Arial, sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #333; padding: 6px; font-size: 12px; }
        .table th { background: #f2f2f2; }
        .no-print { display: none; }
        @media print {
            .no-print { display: none!important; }
        }
    </style>
</head>
<body>
    <div style="text-align:center; margin-bottom:10px;">
        <h2>SCC Inventory</h2>
        <div>Inventory List — Generated on <?php echo htmlspecialchars($now); ?></div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width:5%">Item Id</th>
                <th style="width:20%">Item Name</th>
                <th style="width:35%">Description</th>
                <th style="width:10%">Quantity</th>
                <th style="width:10%">Type</th>
                <th style="width:20%">Department</th>
                <th style="width:20%">Date Created</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (count($rows) === 0) {
            echo '<tr><td colspan="7" style="text-align:center;">No records found</td></tr>';
        } else {
            $i = 1;
            foreach ($rows as $row) {
                $id = htmlspecialchars($row['item_id'] ?? $i);
                $name = htmlspecialchars($row['item_name'] ?? '');
                $desc = htmlspecialchars($row['item_description'] ?? '');
                $qty = htmlspecialchars($row['quantity'] ?? '');
                $type = htmlspecialchars($row['item_type'] ?? '');
                $dept = htmlspecialchars($row['dept_name'] ?? 'No Department');
                $date = htmlspecialchars($row['date_register'] ?? '');
                echo "<tr>";
                echo "<td>" . $i . "</td>";
                echo "<td>" . $name . "</td>";
                echo "<td>" . $desc . "</td>";
                echo "<td>" . $qty . "</td>";
                echo "<td>" . $type . "</td>";
                echo "<td>" . $dept . "</td>";
                echo "<td>" . $date . "</td>";
                echo "</tr>";
                $i++;
            }
        }
        ?>
        </tbody>
    </table>

    <div style="margin-top:12px;">
        <button onclick="window.print();" class="btn btn-primary no-print">Print / Save as PDF</button>
        <a href="inventory.php" class="btn btn-secondary no-print">Back</a>
    </div>

    <script>
        // Trigger print dialog on load so the user can save as PDF
        window.addEventListener('load', function(){
            // small delay to ensure styles are applied
            setTimeout(function(){ window.print(); }, 500);
        });
    </script>
</body>
</html>
