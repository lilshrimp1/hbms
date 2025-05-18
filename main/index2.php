<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../Database/database.php';
require_once '../models/Room.php';
require_once '../models/Review.php';
require_once '../models/Reservation.php';

// Initialize database connection
$database = new database();
$conn = $database->getConnection();
Room::setConnection($conn);
Review::setConnection($conn);
Reservation::setConnection($conn);

// Abstract Dashboard class
abstract class Dashboard {
    protected $conn;
    protected $userRole;
    protected $userId;
    protected $today;
    protected $data = [];

    public function __construct($conn, $userRole, $userId) {
        $this->conn = $conn;
        $this->userRole = $userRole;
        $this->userId = $userId;
        $this->today = date('Y-m-d');
        $this->fetchCommonData();
    }

    protected function fetchCommonData() {
    // Room availability stats
    $this->data['available_rooms'] = Room::countByStatus('available');
    $this->data['booked_rooms'] = Room::countByStatus('booked');
    $this->data['maintenance_rooms'] = Room::countByStatus('maintenance');
    $this->data['not_available_rooms'] = Room::countByStatus('not available');

    // Room status
    $this->data['occupied_rooms'] = $this->data['booked_rooms'];
    $this->data['out_of_order_rooms'] = $this->data['maintenance_rooms'];
    $this->data['vacant_ready_rooms'] = $this->data['available_rooms'];
    $this->data['vacant_not_ready_rooms'] = $this->data['not_available_rooms'];

    // Upcoming reservations
    $this->data['upcoming_reservations'] = Reservation::countByStatusAndDate('confirmed', 'check_in', '>=', $this->today);

    // Latest reviews
    $sql_reviews = "SELECT r.id, r.rating, r.comment, r.created_at, u.name as guest_name
                        FROM reviews r
                        JOIN reservations res ON r.reservation_id = res.id  /* <---  REPLACE reservation_id with the CORRECT column name */
                        JOIN users u ON res.user_id = u.id
                        WHERE r.status = 'approved'
                        ORDER BY r.created_at DESC
                        LIMIT 5";
    $stmt_reviews = $this->conn->query($sql_reviews);
    if ($stmt_reviews) {
        $this->data['latest_reviews'] = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $this->data['latest_reviews'] = [];
        error_log("Error fetching latest reviews: " . print_r($this->conn->errorInfo(), true));
    }

    // Review counts
    $this->data['total_visible_reviews'] = Review::countByStatus('approved');
    $this->data['five_star_reviews'] = Review::countByStatusAndRating('approved', 5);
    $this->data['four_star_reviews'] = Review::countByStatusAndRating('approved', 4);
    $this->data['three_star_reviews'] = Review::countByStatusAndRating('approved', 3);
    $this->data['two_star_reviews'] = Review::countByStatusAndRating('approved', 2);
    $this->data['one_star_reviews'] = Review::countByStatusAndRating('approved', 1);
}

    abstract public function getTemplateData();
}

// Super Admin Dashboard
class SuperAdminDashboard extends Dashboard {
    public function __construct($conn, $userRole, $userId) {
        parent::__construct($conn, $userRole, $userId);
        $this->fetchAdminData();
    }

    protected function fetchAdminData() {
        // Total users by role
        $sql_users_by_role = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
        $stmt_users_by_role = $this->conn->query($sql_users_by_role);
        $this->data['user_counts'] = [];
        if ($stmt_users_by_role) {
            $users_by_role = $stmt_users_by_role->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users_by_role as $count) {
                $this->data['user_counts'][strtolower(str_replace(' ', '-', $count['role']))] = $count['count'];
            }
        }

        // Total users
        $sql_total_users = "SELECT COUNT(*) as total_users FROM users";
        $stmt_total_users = $this->conn->query($sql_total_users);
        if ($stmt_total_users) {
            $total_users_data = $stmt_total_users->fetch(PDO::FETCH_ASSOC);
            $this->data['total_users'] = $total_users_data['total_users'] ?? 0;
        } else {
            $this->data['total_users'] = 0;
        }

        // Upcoming bookings
        $sql_upcoming = "SELECT r.id, r.check_in, r.check_out, r.status, rm.room_number, rt.name as room_type, u.name as guest_name
                            FROM reservations r
                            JOIN room rm ON r.room_id = rm.id
                            JOIN room_types rt ON rm.type_id = rt.id
                            JOIN users u ON r.user_id = u.id
                            WHERE r.status IN ('pending', 'confirmed')
                            AND r.check_in >= :today
                            ORDER BY r.check_in ASC
                            LIMIT 5";
        $stmt_upcoming = $this->conn->prepare($sql_upcoming);
        $stmt_upcoming->bindValue(':today', $this->today);
        if ($stmt_upcoming->execute()) {
            $this->data['upcoming_bookings'] = $stmt_upcoming->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->data['upcoming_bookings'] = []; // Initialize to avoid potential errors
        }
    }

    public function getTemplateData() {
        return [
            'cards' => [
                [
                    'title' => 'Total Users',
                    'value' => $this->data['total_users'] ?? 0,
                    'col_size' => 'col-md-6'
                ],
                [
                    'title' => 'Admins',
                    'value' => $this->data['user_counts']['admin'] ?? 0,
                    'col_size' => 'col-md-6'
                ],
                [
                    'title' => 'Front Desk',
                    'value' => $this->data['user_counts']['front-desk'] ?? 0,
                    'col_size' => 'col-md-6'
                ],
                [
                    'title' => 'Guests',
                    'value' => $this->data['user_counts']['guest'] ?? 0,
                    'col_size' => 'col-md-6'
                ]
            ],
            'room_status' => [
                'occupied' => $this->data['occupied_rooms'],
                'out_of_order' => $this->data['out_of_order_rooms'],
                'vacant_ready' => $this->data['vacant_ready_rooms'],
                'vacant_not_ready' => $this->data['vacant_not_ready_rooms']
            ],
            'upcoming_bookings' => $this->data['upcoming_bookings'],
            'latest_reviews' => $this->data['latest_reviews'],
            'total_visible_reviews' => $this->data['total_visible_reviews']
        ];
    }
}

// Admin Dashboard
class AdminDashboard extends SuperAdminDashboard {
    public function getTemplateData() {
        return [
            'cards' => [
                [
                    'title' => 'Total Users',
                    'value' => $this->data['total_users'] ?? 0,
                    'col_size' => 'col-md-6'
                ],
                [
                    'title' => 'Front Desk',
                    'value' => $this->data['user_counts']['front-desk'] ?? 0,
                    'col_size' => 'col-md-6'
                ],
                [
                    'title' => 'Guests',
                    'value' => $this->data['user_counts']['guest'] ?? 0,
                    'col_size' => 'col-md-6'
                ]
            ],
            'room_status' => [
                'occupied' => $this->data['occupied_rooms'],
                'out_of_order' => $this->data['out_of_order_rooms'],
                'vacant_ready' => $this->data['vacant_ready_rooms'],
                'vacant_not_ready' => $this->data['vacant_not_ready_rooms']
            ],
            'upcoming_bookings' => $this->data['upcoming_bookings'],
            'latest_reviews' => $this->data['latest_reviews'],
            'total_visible_reviews' => $this->data['total_visible_reviews']
        ];
    }
}

// Front Desk Dashboard
class FrontDeskDashboard extends Dashboard {
    public function __construct($conn, $userRole, $userId) {
        parent::__construct($conn, $userRole, $userId);
        $this->fetchFrontDeskData();
    }

    protected function fetchFrontDeskData() {
        // Today's arrivals and departures
        $this->data['todays_arrivals'] = Reservation::countByStatusAndDate('confirmed', 'check_in', '=', $this->today);
        $this->data['todays_departures'] = Reservation::countByStatusAndDate('checked-in', 'check_out', '=', $this->today);

        // Upcoming bookings
        $sql_upcoming = "SELECT r.id, r.check_in, r.check_out, r.status, rm.room_number, rt.name as room_type, u.name as guest_name
                            FROM reservations r
                            JOIN room rm ON r.room_id = rm.id
                            JOIN room_types rt ON rm.type_id = rt.id
                            JOIN users u ON r.user_id = u.id
                            WHERE r.status IN ('pending', 'confirmed')
                            AND r.check_in >= :today
                            ORDER BY r.check_in ASC
                            LIMIT 5";
        $stmt_upcoming = $this->conn->prepare($sql_upcoming);
        $stmt_upcoming->bindValue(':today', $this->today);
        if ($stmt_upcoming->execute()) {
            $this->data['upcoming_bookings'] = $stmt_upcoming->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->data['upcoming_bookings'] = []; // Initialize to avoid potential errors
        }
    }

    public function getTemplateData() {
        return [
            'cards' => [
                [
                    'title' => 'Arrivals Today',
                    'value' => $this->data['todays_arrivals'],
                    'col_size' => 'col-md-4'
                ],
                [
                    'title' => 'Departures Today',
                    'value' => $this->data['todays_departures'],
                    'col_size' => 'col-md-4'
                ],
                [
                    'title' => 'Upcoming Reservations',
                    'value' => $this->data['upcoming_reservations'],
                    'col_size' => 'col-md-4'
                ]
            ],
            'room_status' => [
                'occupied' => $this->data['occupied_rooms'],
                'out_of_order' => $this->data['out_of_order_rooms'],
                'vacant_ready' => $this->data['vacant_ready_rooms'],
                'vacant_not_ready' => $this->data['vacant_not_ready_rooms']
            ],
            'upcoming_bookings' => $this->data['upcoming_bookings']
        ];
    }
}

// Guest Dashboard
class GuestDashboard extends Dashboard {
    public function __construct($conn, $userRole, $userId) {
        parent::__construct($conn, $userRole, $userId);
        $this->fetchGuestData();
    }

    protected function fetchGuestData() {
        // Reservation history
        $sql_history = "SELECT r.id, r.check_in, r.check_out, r.status, rm.room_number, rt.name as room_type
                            FROM reservations r
                            JOIN room rm ON r.room_id = rm.id
                            JOIN room_types rt ON rm.type_id = rt.id
                            WHERE r.user_id = :user_id
                            ORDER BY r.check_in DESC";
        $stmt_history = $this->conn->prepare($sql_history);
        $stmt_history->bindValue(':user_id', $this->userId);
        if ($stmt_history->execute()) {
            $this->data['reservation_history'] = $stmt_history->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->data['reservation_history'] = []; // Initialize to avoid potential errors
        }

        // Upcoming bookings
        $sql_upcoming = "SELECT r.id, r.check_in, r.check_out, r.status, rm.room_number, rt.name as room_type
                            FROM reservations r
                            JOIN room rm ON r.room_id = rm.id
                            JOIN room_types rt ON rm.type_id = rt.id
                            WHERE r.user_id = :user_id AND r.status IN ('pending', 'confirmed')
                            AND r.check_in >= :today
                            ORDER BY r.check_in ASC";
        $stmt_upcoming = $this->conn->prepare($sql_upcoming);
        $stmt_upcoming->bindValue(':user_id', $this->userId);
        $stmt_upcoming->bindValue(':today', $this->today);
        if ($stmt_upcoming->execute()) {
            $this->data['upcoming_bookings'] = $stmt_upcoming->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->data['upcoming_bookings'] = []; // Initialize to avoid potential errors
        }
    }

    public function getTemplateData() {
        return [
            'table' => [
                'title' => 'Reservation History',
                'headers' => ['Room', 'Check-in', 'Check-out', 'Status'],
                'rows' => array_map(function ($history) {
                    return [
                        htmlspecialchars($history['room_number']),
                        htmlspecialchars($history['check_in']),
                        htmlspecialchars($history['check_out']),
                        htmlspecialchars($history['status'])
                    ];
                }, $this->data['reservation_history'] ?? []) // Added null check for safety
            ],
            'room_status' => [
                'occupied' => $this->data['occupied_rooms'],
                'out_of_order' => $this->data['out_of_order_rooms'],
                'vacant_ready' => $this->data['vacant_ready_rooms'],
                'vacant_not_ready' => $this->data['vacant_not_ready_rooms']
            ],
            'upcoming_bookings' => $this->data['upcoming_bookings']
        ];
    }
}

// Instantiate dashboard based on user role
$userRole = $_SESSION['role'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

switch ($userRole) {
    case 'Super Admin':
        $dashboard = new SuperAdminDashboard($conn, $userRole, $userId);
        break;
    case 'Admin':
        $dashboard = new AdminDashboard($conn, $userRole, $userId);
        break;
    case 'Front Desk':
        $dashboard = new FrontDeskDashboard($conn, $userRole, $userId);
        break;
    case 'Guest':
        $dashboard = new GuestDashboard($conn, $userRole, $userId);
        break;
    default:
        header("Location: ../auth/login.php");
        exit();
}

// Get template data
$template_data = $dashboard->getTemplateData();

// Include template
include 'dashboard.php';
?>