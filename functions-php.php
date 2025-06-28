<?php
function ifixandrepair_theme_support() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'ifixandrepair_theme_support');

function ifixandrepair_styles() {
    wp_enqueue_style('theme-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'ifixandrepair_styles');

// Add AJAX endpoints for form submission
add_action('wp_ajax_submit_repair_form', 'handle_repair_form_submission');
add_action('wp_ajax_nopriv_submit_repair_form', 'handle_repair_form_submission');

function handle_repair_form_submission() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'repair_form_nonce')) {
        wp_die('Security check failed');
    }
    
    // Sanitize and save form data
    $form_data = array(
        'customer_name' => sanitize_text_field($_POST['customer_name']),
        'customer_phone' => sanitize_text_field($_POST['customer_phone']),
        'customer_email' => sanitize_email($_POST['customer_email']),
        'device_brand' => sanitize_text_field($_POST['device_brand']),
        'device_model' => sanitize_text_field($_POST['device_model']),
        'issue' => sanitize_text_field($_POST['issue']),
        'issue_description' => sanitize_textarea_field($_POST['issue_description']),
        'quoted_price' => floatval($_POST['quoted_price']),
        'qr_review' => sanitize_text_field($_POST['qr_review']),
        'accessory' => sanitize_text_field($_POST['accessory']),
        'submission_date' => current_time('mysql')
    );
    
    // Save to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'repair_requests';
    
    $wpdb->insert($table_name, $form_data);
    
    wp_send_json_success('Form submitted successfully');
}

// Create database table on theme activation
function create_repair_requests_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'repair_requests';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        customer_name tinytext NOT NULL,
        customer_phone varchar(20) NOT NULL,
        customer_email varchar(100) NOT NULL,
        device_brand varchar(50) NOT NULL,
        device_model varchar(100) NOT NULL,
        issue varchar(100) NOT NULL,
        issue_description text,
        quoted_price decimal(10,2) NOT NULL,
        qr_review varchar(10) NOT NULL,
        accessory varchar(50),
        submission_date datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'create_repair_requests_table');

// Add admin menu
add_action('admin_menu', 'ifixandrepair_admin_menu');

function ifixandrepair_admin_menu() {
    add_menu_page(
        'Repair Requests',
        'Repair Requests',
        'manage_options',
        'repair-requests',
        'ifixandrepair_admin_page',
        'dashicons-tools',
        30
    );
}

function ifixandrepair_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'repair_requests';
    $requests = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submission_date DESC");
    
    echo '<div class="wrap">';
    echo '<h1>Repair Requests</h1>';
    
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        export_repair_requests_csv($requests);
        return;
    }
    
    echo '<a href="?page=repair-requests&export=csv" class="button button-primary" style="margin-bottom: 20px;">Export to CSV</a>';
    
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Date</th><th>Name</th><th>Phone</th><th>Email</th><th>Device</th><th>Issue</th><th>Price</th><th>QR Review</th><th>Accessory</th></tr></thead>';
    echo '<tbody>';
    
    foreach ($requests as $request) {
        echo '<tr>';
        echo '<td>' . $request->submission_date . '</td>';
        echo '<td>' . $request->customer_name . '</td>';
        echo '<td>' . $request->customer_phone . '</td>';
        echo '<td>' . $request->customer_email . '</td>';
        echo '<td>' . $request->device_brand . ' ' . $request->device_model . '</td>';
        echo '<td>' . $request->issue . ($request->issue_description ? ' - ' . $request->issue_description : '') . '</td>';
        echo '<td>$' . $request->quoted_price . '</td>';
        echo '<td>' . $request->qr_review . '</td>';
        echo '<td>' . $request->accessory . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
}

function export_repair_requests_csv($requests) {
    $filename = 'repair_requests_' . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // CSV headers
    fputcsv($output, array(
        'Date', 'Name', 'Phone', 'Email', 'Device Brand', 'Device Model', 
        'Issue', 'Issue Description', 'Quoted Price', 'QR Review', 'Accessory'
    ));
    
    // CSV data
    foreach ($requests as $request) {
        fputcsv($output, array(
            $request->submission_date,
            $request->customer_name,
            $request->customer_phone,
            $request->customer_email,
            $request->device_brand,
            $request->device_model,
            $request->issue,
            $request->issue_description,
            $request->quoted_price,
            $request->qr_review,
            $request->accessory
        ));
    }
    
    fclose($output);
    exit;
}
?>