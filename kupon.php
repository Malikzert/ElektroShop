<?php
session_start();
include 'koneksi.php'; // koneksi ke database

// Redirect ke login jika belum login
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$loggedInUserId = (int)$_SESSION['id'];
?>
<?php include 'resource/header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5">
    <h3 class="mb-4 text-center">🎁 Kupon Tersedia: Diskon 50%</h3>

    <?php
    // Ambil kupon aktif dengan diskon 50%
    $query = "SELECT * FROM kupon_diskon WHERE diskon = 50 AND status = 'aktif' AND CURDATE() BETWEEN start_date AND end_date";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Campaign</th>
                        <th>Kode Kupon</th>
                        <th>Diskon</th>
                        <th>Periode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($kupon = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($kupon['id']) ?></td>
                            <td><?= htmlspecialchars($kupon['campaign_name']) ?></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($kupon['code']) ?></span></td>
                            <td><?= htmlspecialchars($kupon['diskon']) ?>%</td>
                            <td><?= htmlspecialchars($kupon['start_date']) ?> s/d <?= htmlspecialchars($kupon['end_date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Tidak ada kupon diskon 50% yang tersedia saat ini.</div>
    <?php endif; ?>
</div>

<?php include 'resource/footer.php'; ?>
