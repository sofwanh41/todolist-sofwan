<?php
include "koneksi.php";
$isi = $tgl_awal = $tgl_akhir = '';
$notif = '';
if (isset($_GET['id'])) {
    $stmt = $mysqli->prepare("SELECT * FROM kegiatan WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    if ($data) {
        $isi = $data['isi'];
        $tgl_awal = $data['tgl_awal'];
        $tgl_akhir = $data['tgl_akhir'];
    }
    $stmt->close();
}
if (isset($_POST['simpan'])) {
    if (isset($_POST['id'])) {
        $stmt = $mysqli->prepare("UPDATE kegiatan SET isi = ?, tgl_awal = ?, tgl_akhir = ?
WHERE id = ?");
        $stmt->bind_param(
            "sssi",
            $_POST['isi'],
            $_POST['tgl_awal'],
            $_POST['tgl_akhir'],
            $_POST['id']
        );
        $stmt->execute();
        $stmt->close();
        $notif = "updated";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO kegiatan (isi, tgl_awal, tgl_akhir, status) 
VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $_POST['isi'], $_POST['tgl_awal'], $_POST['tgl_akhir']);
        $stmt->execute();
        $stmt->close();
        $notif = "added";
    }
    // Redirect setelah simpan
    header('Location: index.php?notif=' . $notif);
    exit();
}
if (isset($_GET['aksi'])) {
    if ($_GET['aksi'] == 'hapus') {
        $stmt = $mysqli->prepare("DELETE FROM kegiatan WHERE id = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $stmt->close();
        $notif = "deleted";
    } elseif ($_GET['aksi'] == 'ubah_status') {
        $stmt = $mysqli->prepare("UPDATE kegiatan SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $_GET['status'], $_GET['id']);
        $stmt->execute();
        $stmt->close();
        $notif = "status_updated";
    }
    // Redirect setelah aksi
    header('Location: index.php?notif=' . $notif);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-
T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>To Do List</title>
</head>

<body>
    <div class="container mt-5">
        <h3>
            To Do List Sofwan
            <small class="text-muted">
                Catat semua hal yang akan kamu kerjakan disini.
            </small>
        </h3>
        <hr>
        <form class="form row" method="POST" action="">
            <?php if (isset($_GET['id'])) : ?>
                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
            <?php endif; ?>
            <div class="col">
                <label for="inputIsi" class="form-label fw-bold">
                    Kegiatan
                </label>
                <input type="text" class="form-control" name="isi" id="inputIsi" placeholder="Kegiatan" value="">
            </div>
            <div class="col">
                <label for="inputTanggalAwal" class="form-label fw-bold">
                    Tanggal Awal
                </label>
                <input type="date" class="form-control" name="tgl_awal" id="inputTanggalAwal" value="tgl_awal">
            </div>
            <div class="col">
                <label for="inputTanggalAkhir" class="form-label fw-bold">
                    Tanggal Akhir
                </label>
                <input type="date" class="form-control" name="tgl_akhir" id="inputTanggalAkhir" value="tgl_akhir">
            </div>
            <div class="col mt-3">
                <button type="submit" class="btn btn-primary rounded-pill px-3 mt-3" name="simpan">Simpan</button>
            </div>
        </form>
        <table class="table table-hover mt-5">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Kegiatan</th>
                    <th scope="col">Awal</th>
                    <th scope="col">Akhir</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($mysqli, "SELECT * FROM kegiatan ORDER BY status, 
tgl_awal");
                $no = 1;
                while ($data = mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <th scope="row"><?php echo $no++ ?></th>
                        <td><?php echo $data['isi'] ?></td>
                        <td><?php echo $data['tgl_awal'] ?></td>
                        <td><?php echo $data['tgl_akhir'] ?></td>
                        <td>
                            <?php
                            if ($data['status'] == '1') {
                            ?>
                                <a class="btn btn-success rounded-pill px-3" href="index.php?id=<?php
                                                                                                echo $data['id'] ?>&aksi=ubah_status&status=0">Sudah</a>
                            <?php
                            } else {
                            ?>
                                <a class="btn btn-warning rounded-pill px-3" href="index.php?id=<?php
                                                                                                echo $data['id'] ?>&aksi=ubah_status&status=1">Belum</a>
                            <?php
                            }
                            ?>
                        </td>
                        <td>
                            <a class="btn btn-info rounded-pill px-3" data-bs-toggle="modal" databs-target="#ubahModal" data-id="<?php echo $data['id']; ?>" data-isi="<?php echo
                                                                                                                                                                        $data['isi']; ?>" data-tgl_awal="<?php echo $data['tgl_awal']; ?>" datatgl_akhir="<?php echo $data['tgl_akhir']; ?>">
                                Ubah
                            </a>
                            <a class="btn btn-danger rounded-pill px-3" href="index.php?id=<?php
                                                                                            echo $data['id'] ?>&aksi=hapus">Hapus</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- Modal Ubah -->
    <div class="modal fade" id="ubahModal" tabindex="-1" arialabelledby="ubahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ubahModalLabel">Ubah Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" arialabel="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ubahIsi" class="form-label">Kegiatan</label>
                            <input type="text" class="form-control" name="isi" id="ubahIsi" placeholder="Kegiatan">
                        </div>
                        <div class="mb-3">
                            <label for="ubahTanggalAwal" class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" name="tgl_awal" id="ubahTanggalAwal">
                        </div>
                        <div class="mb-3">
                            <label for="ubahTanggalAkhir" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="tgl_akhir" id="ubahTanggalAkhir">
                        </div>
                        <!-- Hidden input untuk ID -->
                        <input type="hidden" name="id" id="ubahId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bsdismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="simpan">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        let notif = "<?php echo $notif; ?>";
        switch (notif) {
            case 'added':
                alert('Data berhasil ditambahkan!');
                break;
            case 'updated':
                alert('Data berhasil diperbarui!');
                break;
            case 'deleted':
                alert('Data berhasil dihapus!');
                break;
        }
        var ubahModal = document.getElementById('ubahModal');
        ubahModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var isi = button.getAttribute('data-isi');
            var tgl_awal = button.getAttribute('data-tgl_awal');
            var tgl_akhir = button.getAttribute('data-tgl_akhir');
            ubahModal.querySelector('#ubahId').value = id;
            ubahModal.querySelector('#ubahIsi').value = isi;
            ubahModal.querySelector('#ubahTanggalAwal').value = tgl_awal;
            ubahModal.querySelector('#ubahTanggalAkhir').value = tgl_akhir;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html