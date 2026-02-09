<?php
require_once 'koneksi.php';
session_start();

// Cek apakah ini adalah request AJAX, dan jika ya, tangani terpisah sepenuhnya
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Pastikan pengguna sudah login untuk request AJAX ini
    if (!isset($_SESSION['id'])) {
        header('Content-Type: application/json'); // Explicitly set content type for AJAX responses
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin. Silakan login kembali.']);
        exit;
    }

    $id_user = $_SESSION['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'add_address') {
            $nama_penerima = htmlspecialchars(trim($_POST['nama_penerima']));
            $no_telepon_alamat = htmlspecialchars(trim($_POST['no_telepon_alamat']));
            $alamat_lengkap = htmlspecialchars(trim($_POST['alamat_lengkap']));
            $desa = htmlspecialchars(trim($_POST['desa']));
            $distrik = htmlspecialchars(trim($_POST['distrik']));
            $kota = htmlspecialchars(trim($_POST['kota']));
            $kode_pos = htmlspecialchars(trim($_POST['kode_pos']));
            $provinsi = htmlspecialchars(trim($_POST['provinsi']));

            if (empty($nama_penerima) || empty($no_telepon_alamat) || empty($alamat_lengkap) || empty($desa) || empty($distrik) || empty($kota) || empty($kode_pos) || empty($provinsi)) {
                header('Content-Type: application/json'); // Explicitly set content type
                echo json_encode(['status' => 'error', 'message' => 'Semua kolom alamat harus diisi.']);
                exit;
            }

            $stmt_address = $conn->prepare("INSERT INTO alamat_pengiriman (id_pengguna, nama_penerima, no_telepon, alamat_lengkap, desa, distrik, kota, kode_pos, provinsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt_address) {
                $stmt_address->bind_param("issssssss", $id_user, $nama_penerima, $no_telepon_alamat, $alamat_lengkap, $desa, $distrik, $kota, $kode_pos, $provinsi);
                if ($stmt_address->execute()) {
                    header('Content-Type: application/json'); // Explicitly set content type
                    echo json_encode(['status' => 'success', 'message' => 'Alamat pengiriman berhasil ditambahkan!']);
                } else {
                    header('Content-Type: application/json'); // Explicitly set content type
                    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan alamat pengiriman: ' . $stmt_address->error]);
                }
                $stmt_address->close();
            } else {
                header('Content-Type: application/json'); // Explicitly set content type
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query alamat: ' . $conn->error]);
            }
            exit; // Pastikan ini dieksekusi dan menghentikan script
        }
        // You can add other AJAX actions here for myaccount.php if needed
    }
    // Jika ada request AJAX tapi bukan action yang dikenali, bisa return error default
    header('Content-Type: application/json'); // Explicitly set content type
    echo json_encode(['status' => 'error', 'message' => 'Aksi AJAX tidak valid.']);
    exit;
}

// --- BAGIAN INI HANYA UNTUK REQUEST NON-AJAX (RENDERING HALAMAN HTML LENGKAP) ---

include 'resource/header.php'; // Ini hanya akan dipanggil jika bukan request AJAX

// Pastikan pengguna sudah login untuk rendering halaman utama
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id'];

$data = [];
$query = $conn->query("SELECT * FROM pengguna WHERE id = $id_user");
if ($query && $query->num_rows > 0) {
    $data = $query->fetch_assoc();
} else {
    $data = [
        'nama_pengguna' => '', 'no_hp' => '', 'email' => '',
        'address' => '', 'jenis_kelamin' => 'lakilaki', 'password' => ''
    ];
}

$success = '';
$error = '';

// Handle POST requests REGULER (untuk update profil, bukan AJAX add_address)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nama = $_POST['nama_pengguna'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $gender = $_POST['jenis_kelamin'];

    if (!empty($_POST['new_password'])) {
        $current_password_md5 = md5($_POST['current_password']);
        if ($current_password_md5 == $data['password']) {
            $new_pass_md5 = md5($_POST['new_password']);
            $stmt = $conn->prepare("UPDATE pengguna SET
                nama_pengguna=?,
                no_hp=?,
                email=?,
                address=?,
                jenis_kelamin=?,
                password=?
                WHERE id=?");
            $stmt->bind_param("ssssssi", $nama, $no_hp, $email, $address, $gender, $new_pass_md5, $id_user);
            if ($stmt->execute()) {
                $_SESSION['nama_pengguna'] = $nama;
                $success = "Profil dan password berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui profil: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Password lama salah.";
        }
    } else {
        $stmt = $conn->prepare("UPDATE pengguna SET
            nama_pengguna=?,
            no_hp=?,
            email=?,
            address=?,
            jenis_kelamin=?
            WHERE id=?");
        $stmt->bind_param("sssssi", $nama, $no_hp, $email, $address, $gender, $id_user);
        if ($stmt->execute()) {
            $_SESSION['nama_pengguna'] = $nama;
            $success = "Profil berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui profil: " . $stmt->error;
        }
        $stmt->close();
    }

    // Ambil ulang data setelah update
    $query = $conn->query("SELECT * FROM pengguna WHERE id = $id_user");
    $data = $query->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ... (Styling Anda) ... */
        .modal-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); display: none; justify-content: center; align-items: center;
            z-index: 999;
        }
        .modal-box {
            background: #fff; padding: 30px; border-radius: 10px; width: 60%;
        }
        .alert {
            position: fixed; top: 20px; right: 20px; padding: 15px 20px; border-radius: 5px;
            color: white; font-weight: bold; z-index: 1000;
            opacity: 0; transform: translateY(-20px); transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .alert.show {
            opacity: 1; transform: translateY(0);
        }
        .alert-success { background: green; }
        .alert-error { background: red; }
        /* Hover effect for buttons */
        button {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        button:hover {
            filter: brightness(0.9);
            cursor: pointer;
        }
        button.edit-profile:hover {
            background-color: #c2185b; /* darker pink */
        }
        button.save-changes:hover {
            background-color: #d81b60; /* slightly different pink */
        }
        .modal-box .close-btn {
            position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #aaa; text-decoration: none;
        }
        .modal-box .close-btn:hover {
            color: #555;
        }
    </style>
</head>
<body>

<main style="display: flex; padding: 40px 20px; background: #ffc0cb; min-height: 80vh;">
    <aside style="width: 25%; padding-right: 20px;">
        <h3>Manage My Account</h3>
        <ul style="list-style: none; margin-top: 10px; padding:0;">
            <li style="color:#e91e63; font-weight:bold;"><a href="myaccount.php" style="text-decoration:none; color:#e91e63;">My Profile</a></li>
            <li><a href="my_address_ship.php" style="text-decoration:none; color:#000;">My Address Ship</a></li> </ul>
        <h3 style="margin-top: 20px;">My Orders</h3>
        <ul style="list-style: none; margin-top: 10px; padding:0;">
            <li><a href="cancel.php" style="text-decoration:none; color:#000;">My Cancellations</a></li>
            <li><a href="my_reviews.php" style="text-decoration:none; color:#000;">My Rating</a></li>
            <li><a href="my_history.php" style="text-decoration:none; color:#000;">My History</a></li> </ul>
        <h3 style="margin-top: 20px;">My Wishlist</h3>
        <ul style="list-style: none; margin-top: 10px; padding:0;">
            <li><a href="guarantee.php" style="text-decoration:none; color:#000;">Guarantee</a></li>
        </ul>
    </aside>

    <section style="width: 60%; background: white; padding: 30px; border-radius: 10px;">
        <h2 style="color:#e91e63; margin-bottom: 20px;">Your Profile</h2>
        <form>
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Name</label>
                    <input type="text" value="<?= htmlspecialchars($data['nama_pengguna'] ?? '') ?>" readonly style="width:100%; padding:10px;">
                </div>
                <div style="flex: 1;">
                    <label>Number</label>
                    <input type="text" value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>" readonly style="width:100%; padding:10px;">
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" readonly style="width:100%; padding:10px;">
                </div>
                <div style="flex: 1;">
                    <label>Address</label>
                    <input type="text" value="<?= htmlspecialchars($data['address'] ?? '') ?>" readonly style="width:100%; padding:10px;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label>Gender</label>
                <select disabled style="width:100%; padding:10px;">
                    <option value="lakilaki" <?= ($data['jenis_kelamin'] ?? '') == 'lakilaki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="perempuan" <?= ($data['jenis_kelamin'] ?? '') == 'perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
        </form>

        <div style="display: flex; justify-content: flex-start; gap: 10px; margin-top: 10px;">
            <button type="button" onclick="openEditProfileModal()" class="edit-profile" style="padding: 10px 20px; background: #e91e63; color: white; border: none; border-radius: 6px;">Edit Profile</button>
            <button type="button" onclick="openAddAddressModal()" class="add-address" style="padding: 10px 20px; background: #e91e63; color: white; border: none; border-radius: 6px;">Add Shipping Address</button>
        </div>
    </section>

</main>

<div id="editProfileModal" class="modal-bg">
    <div class="modal-box" style="width: 75%; background: white; padding: 30px; border-radius: 10px;">
        <a href="#" class="close-btn" onclick="closeEditProfileModal()" style="position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #aaa; text-decoration: none;">&times;</a>
        <h2 style="color:#e91e63; margin-bottom: 20px;">Edit Your Profile</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Name</label>
                    <input type="text" name="nama_pengguna" value="<?= htmlspecialchars($data['nama_pengguna'] ?? '') ?>" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
                <div style="flex: 1;">
                    <label>Number</label>
                    <input type="text" name="no_hp" value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
                <div style="flex: 1;">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($data['address'] ?? '') ?>" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label>Gender</label>
                <select name="jenis_kelamin" required style="width:100%; padding:10px; margin-bottom: 15px;">
                    <option value="lakilaki" <?= ($data['jenis_kelamin'] ?? '') == 'lakilaki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="perempuan" <?= ($data['jenis_kelamin'] ?? '') == 'perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 10px;">Password Changes</h3>
            <input type="password" name="current_password" placeholder="Current Password" style="width:100%; padding:10px; margin-bottom: 10px;" autocomplete="new-password">
            <input type="password" name="new_password" id="new_password" placeholder="New Password" style="width:100%; padding:10px; margin-bottom: 10px;" autocomplete="new-password">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" style="width:100%; padding:10px; margin-bottom: 20px;" autocomplete="new-password">
            <small id="password_match_status" style="color: red; display: block; margin-top: -10px; margin-bottom: 10px;"></small>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEditProfileModal()" style="padding: 10px 20px; background: #ccc; border: none; border-radius: 6px;">Cancel</button>
                <button type="submit" class="save-changes" id="saveProfileBtn" style="padding: 10px 20px; background: hotpink; color: white; border: none; border-radius: 6px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="addAddressModal" class="modal-bg">
    <div class="modal-box" style="width: 75%; background: white; padding: 30px; border-radius: 10px;">
        <a href="#" class="close-btn" onclick="closeAddAddressModal()" style="position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #aaa; text-decoration: none;">&times;</a>
        <h2 style="color:#e91e63; margin-bottom: 20px;">Add New Shipping Address</h2>
        <form id="addAddressForm">
            <input type="hidden" name="action" value="add_address">
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Recipient Name</label>
                    <input type="text" id="nama_penerima" name="nama_penerima" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
                <div style="flex: 1;">
                    <label>Phone Number</label>
                    <input type="text" id="no_telepon_alamat" name="no_telepon_alamat" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label>Full Address</label>
                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required style="width:100%; padding:10px; margin-bottom: 15px;"></textarea>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Province</label>
                    <input type="text" id="provinsi" name="provinsi" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
                <div style="flex: 1;">
                    <label>City</label>
                    <input type="text" id="kota" name="kota" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>District</label>
                    <input type="text" id="distrik" name="distrik" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
                <div style="flex: 1;">
                    <label>Village</label>
                    <input type="text" id="desa" name="desa" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
                <div style="flex: 1;">
                    <label>Postal Code</label>
                    <input type="text" id="kode_pos" name="kode_pos" required style="width:100%; padding:10px; margin-bottom: 15px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="cancel-btn" onclick="closeAddAddressModal()" style="padding: 10px 20px; background: #ccc; border: none; border-radius: 6px;">Cancel</button>
                <button type="submit" class="save-changes" id="saveAddressBtn" style="padding: 10px 20px; background: hotpink; color: white; border: none; border-radius: 6px;">Save Address</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($success) && !empty($success)): ?>
    <div class="alert alert-success" id="successAlert" style="display:none;"><?= $success ?></div>
<?php endif; ?>
<?php if (isset($error) && !empty($error)): ?>
    <div class="alert alert-error" id="errorAlert" style="display:none;"><?= $error ?></div>
<?php endif; ?>

<script>
    // --- Centralized showAlert function for consistent UI ---
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        document.body.appendChild(alertDiv);
        setTimeout(() => { alertDiv.classList.add('show'); }, 10); // Trigger transition

        setTimeout(() => {
            alertDiv.classList.remove('show'); // Start fade out
            setTimeout(() => alertDiv.remove(), 500); // Remove from DOM after transition
        }, 5000);
    }

    // --- Modal Functions ---
    function openEditProfileModal() {
        document.getElementById('editProfileModal').style.display = 'flex';
        // Reset password fields and status when opening
        document.getElementById('new_password').value = '';
        document.getElementById('confirm_password').value = '';
        document.getElementById('password_match_status').textContent = '';
    }

    function closeEditProfileModal() {
        document.getElementById('editProfileModal').style.display = 'none';
    }

    function openAddAddressModal() {
        document.getElementById('addAddressModal').style.display = 'flex';
        // Clear form fields when opening
        document.getElementById('addAddressForm').reset();
    }

    function closeAddAddressModal() {
        document.getElementById('addAddressModal').style.display = 'none';
    }

    // --- Initial Page Load Alert Display Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        const successAlertElement = document.getElementById('successAlert');
        const errorAlertElement = document.getElementById('errorAlert');

        if (successAlertElement && successAlertElement.textContent.trim() !== '') {
            showAlert(successAlertElement.textContent, 'success');
            successAlertElement.remove(); // Remove the hidden element after processing
        }
        if (errorAlertElement && errorAlertElement.textContent.trim() !== '') {
            showAlert(errorAlertElement.textContent, 'error');
            errorAlertElement.remove(); // Remove the hidden element after processing
        }
    });

    // --- Password Match Validation ---
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordMatchStatus = document.getElementById('password_match_status');
    const saveProfileBtn = document.getElementById('saveProfileBtn');

    if (newPassword && confirmPassword && passwordMatchStatus && saveProfileBtn) {
        function validatePasswordMatch() {
            if (newPassword.value === '' && confirmPassword.value === '') {
                passwordMatchStatus.textContent = '';
                saveProfileBtn.disabled = false;
            } else if (newPassword.value !== confirmPassword.value) {
                passwordMatchStatus.textContent = 'New passwords do not match!';
                saveProfileBtn.disabled = true;
            } else {
                passwordMatchStatus.textContent = '';
                saveProfileBtn.disabled = false;
            }
        }
        newPassword.addEventListener('keyup', validatePasswordMatch);
        confirmPassword.addEventListener('keyup', validatePasswordMatch);
    }


    // --- AJAX for Add Address Form Submission ---
    const addAddressForm = document.getElementById('addAddressForm');
    if (addAddressForm) {
        addAddressForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            // Add custom header to identify as an AJAX request in PHP
            const headers = new Headers();
            headers.append('X-Requested-With', 'XMLHttpRequest');

            fetch('myaccount.php', { // Send request to the same file
                method: 'POST',
                body: formData,
                headers: headers // Include the custom header
            })
            .then(response => {
                // First, check if the HTTP response itself was successful (e.g., 200 OK)
                if (!response.ok) {
                    // If not OK, try to read the response as text for debugging
                    return response.text().then(text => {
                        throw new Error(`HTTP error! Status: ${response.status}. Response body (first 200 chars): ${text.substring(0, 200)}...`);
                    });
                }
                // Then, check the Content-Type header to ensure it's JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, it's likely an unexpected HTML output from PHP
                    // Read as text to log the unexpected content
                    return response.text().then(text => {
                        throw new TypeError("Oops, we didn't get JSON! Instead received: " + contentType + ". Body (first 200 chars): " + text.substring(0, 200) + "...");
                    });
                }
            })
            .then(data => {
                // Display the alert message based on the JSON response
                showAlert(data.message, data.status);
                if (data.status === 'success') {
                    closeAddAddressModal(); // Close the modal on success
                    // Option 1: Reload the page to display updated address list (if my_address_ship.php is linked/loaded on reload)
                    window.location.href = 'my_address_ship.php'; // Redirect to My Address Ship page
                    // Option 2 (more advanced): If addresses are displayed on myaccount.php,
                    // you'd typically make another AJAX call to fetch and render the updated list
                    // without a full page reload.
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan tak terduga. Silakan coba lagi. Detail: ' + error.message, 'error');
            });
        });
    }
</script>

<?php include 'resource/footer.php'; ?>
</body>
</html>