<?php
session_start();
if (isset($_SESSION['primary_id']) && isset($_SESSION['username'])) {
    include('../functions/conn_db.php'); // Include the database connection

    // Set the number of rows per page
    $rows_per_page =10;

    // Get the current page number from the query string, default to page 1 if not set
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Calculate the OFFSET based on the current page
    $offset = ($page - 1) * $rows_per_page;

    // Prepare and execute the query to fetch counter with pagination
    $query = "SELECT * FROM counter ORDER BY uploaded_at DESC LIMIT $rows_per_page OFFSET $offset";
    $result = $conn->query($query);

    // Get the total number of rows to calculate the total number of pages
    $total_query = 'SELECT COUNT(*) as total FROM counter';
    $total_result = $conn->query($total_query);
    $total_row = $total_result->fetch_assoc();
    $total_rows = $total_row['total'];
    $total_pages = ceil($total_rows / $rows_per_page);

    // Close the connection when done
    $conn->close();
}else{
    header("Location: index.php");     
    exit();
}
?>

<?php
// Include shared components
include '../includes/header.php';
include '../includes/navbar.php'
?>

<?php
// Function to format phone number
function formatPhoneNumber($phone) {
    // Remove any non-numeric characters
    $phone = preg_replace('/\D/', '', $phone);

    // Format the phone number as (XXX) - XXX - XXXX
    if (strlen($phone) == 10) {
        return '(' . substr($phone, 0, 3) . ') - ' . substr($phone, 3, 3) . ' - ' . substr($phone, 6);
    }

    // If the phone number is not valid (less or more than 10 digits), return the original
    return $phone;
}
?>


<div class='white-container'>
    
    <div class='data-container'>
    <h2>Line Placement Management Page</h2>
    <div class='add-border'>
        <div class='counter-listing-container' >
            <table class="counter-table">
                <thead>
                    <tr> 
                        <th>Edit</th>
                        <th>View</th>
                        <th>ID</th>
                        <th>Record Date</th>
                        <th>Ticket Today</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>VIN</th>
                        <th>Driver's License</th>
                        <th>License Plate</th>
                        <th>Is Owner</th>
                        <th>Registered in NY</th>
                        <th>Have Registration</th>
                        <th>Has Insurance</th>
                        <th>Has Title</th>
                        <th>Has Owner License</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php while ($counter = $result->fetch_assoc()) : ?>
                        <tr>
                            <!-- onclick="window.location.href = '../functions/pull_all_from_form_id.php?form_id=<?php echo htmlspecialchars($counter['form_id']); ?>';" -->
                            <td> <a href="editform.php?form_id=<?php echo htmlspecialchars($counter['form_id']); ?>">
                                    Edit
                                </a>
                            </td>
                            <td> <a href="viewform.php?form_id=<?php echo htmlspecialchars($counter['form_id']); ?>">
                                    View
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($counter['form_id']); ?></td>
                            <td class="uploaded-at">
                                <?php
                                    $date = new DateTime(htmlspecialchars($counter['uploaded_at']), new DateTimeZone('UTC'));
                                    $date->setTimezone(new DateTimeZone('America/New_York'));
                                    echo $date->format('m/d/Y h:i A');
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($counter['counter_today']); ?></td>
                            <td><?php echo htmlspecialchars($counter['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($counter['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($counter['email']); ?></td>
                            <td><?php echo formatPhoneNumber(htmlspecialchars($counter['phone'])); ?></td>
                            <td><?php echo htmlspecialchars($counter['vin']); ?></td>
                            <td><?php echo htmlspecialchars($counter['drivers_license']); ?></td>
                            <td><?php echo htmlspecialchars($counter['license_plate']); ?></td>
                            <td><?php echo htmlspecialchars($counter['is_owner']); ?></td>
                            <td><?php echo htmlspecialchars($counter['registered_in_ny']); ?></td>
                            <td><?php echo htmlspecialchars($counter['have_registration']); ?></td>
                            <td><?php echo htmlspecialchars($counter['have_insurance']); ?></td>
                            <td><?php echo htmlspecialchars($counter['have_title']); ?></td>
                            <td><?php echo htmlspecialchars($counter['have_owner_license']); ?></td>
                            
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>



        </div>
    </div>

        <div class='counter-listing-container'>
            <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                    <?php endif; ?>

                    <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Next</a>
                    <?php endif; ?>
            </div>
        </div>
    </div>
</div>
