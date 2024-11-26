<div class="container">
    <h2>Feedback</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Rating</th>
                <th>Therapist's Name</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Initialize feedback array or fetch data from the database
            $feedback_data = [];
            if (isset($_POST['submit'])) {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $rating = $_POST['rating'];
                $therapists = $_POST['therapists'];
                $comments = $_POST['comments'];

                // Push feedback into array (replace this with database insert and fetch logic)
                $feedback_data[] = [
                    'name' => $name,
                    'email' => $email,
                    'rating' => $rating,
                    'therapists' => $therapists,
                    'comments' => $comments
                ];
            }

            // Display feedback in the table
            if (!empty($feedback_data)) {
                foreach ($feedback_data as $feedback) {
                    echo "<tr>
                        <td>{$feedback['name']}</td>
                        <td>{$feedback['email']}</td>
                        <td>{$feedback['rating']}</td>
                        <td>{$feedback['therapists']}</td>
                        <td>{$feedback['comments']}</td>
                    </tr>";
                }
            } else {
                echo '<tr><td colspan="5">No feedback submitted yet.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Simplified Table Styling -->
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ccc;
    }
    th {
        background-color: #f2f2f2;
        color: #333;
    }
    td {
        background-color: #fff;
        color: #333;
    }
    tr:nth-child(even) td {
        background-color: #f9f9f9;
    }
    tr:hover td {
        background-color: #f1f1f1;
    }
    tbody tr:first-child td {
        border-top: none;
    }
    tbody tr:last-child td {
        border-bottom: none;
    }
    h2 {
        text-align: center;
        font-size: 26px;
        color: #333;
        margin-bottom: 20px;
    }
</style>
