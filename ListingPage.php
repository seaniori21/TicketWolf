<?php
session_start();
if (isset($_SESSION['primary_id']) && isset($_SESSION['username'])) {
    include('functions/conn_db.php'); // Include the database connection

    // Set the number of rows per page
    $rows_per_page =10;

    // Get the current page number from the query string, default to page 1 if not set
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Calculate the OFFSET based on the current page
    $offset = ($page - 1) * $rows_per_page;

    // Prepare and execute the query to fetch tickets with pagination
    $query = "SELECT * FROM tickets ORDER BY uploaded_at DESC LIMIT $rows_per_page OFFSET $offset";
    $result = $conn->query($query);

    // Get the total number of rows to calculate the total number of pages
    $total_query = 'SELECT COUNT(*) as total FROM tickets';
    $total_result = $conn->query($total_query);
    $total_row = $total_result->fetch_assoc();
    $total_rows = $total_row['total'];
    $total_pages = ceil($total_rows / $rows_per_page);

    // Close the connection when done
    $conn->close();
}else{
    header("Location: LoginPage.php");     
    exit();
}
?>

<?php
// Include shared components
include 'includes/header.php';
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
<div class='header-container'>
    <img src="assets/img/banner_tw.png" alt="Top Right Image" class="header-image">
    <!-- <button type="function" class="btn">LogOut</button> -->
    <a href="functions/logout.php">Logout</a>
</div>
    <div class='data-container'>
    <h2>Ticket Listings</h2>
    <div class='add-border'>
        <div class='ticket-listing-container' >
            <table class="ticket-table">
                <thead>
                    <tr> 
                        <th>Edit</th>
                        <th>View</th>
                        <th>ID</th>
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
                        <th>Record Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($ticket = $result->fetch_assoc()) : ?>
                        <tr>
                            <!-- onclick="window.location.href = '../functions/pull_all_from_form_id.php?form_id=<?php echo htmlspecialchars($ticket['form_id']); ?>';" -->
                            <td> <a href="EditForm.php?form_id=<?php echo htmlspecialchars($ticket['form_id']); ?>">
                                    Edit
                                </a>
                            </td>
                            <td> <a href="ViewForm.php?form_id=<?php echo htmlspecialchars($ticket['form_id']); ?>">
                                    View
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['form_id']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['ticket_today']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['email']); ?></td>
                            <td><?php echo formatPhoneNumber(htmlspecialchars($ticket['phone'])); ?></td>
                            <td><?php echo htmlspecialchars($ticket['vin']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['drivers_license']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['license_plate']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['is_owner']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['registered_in_ny']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['have_registration']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['have_insurance']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['have_title']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['have_owner_license']); ?></td>
                            <td class="uploaded-at">
                                <?php
                                    $date = new DateTime(htmlspecialchars($ticket['uploaded_at']), new DateTimeZone('UTC'));
                                    $date->setTimezone(new DateTimeZone('America/New_York'));
                                    echo $date->format('m/d/Y h:i A');
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>



        </div>
    </div>

        <div class='ticket-listing-container'>
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
