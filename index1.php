<?php
// Veritabanı bağlantısı
$host = 'localhost';
$dbname = 'motor_takip';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

// Silme işlemi
if (isset($_GET['sil_id'])) {
    $sil_id = $_GET['sil_id'];
    $silQuery = "DELETE FROM el_aletleri WHERE id = :id";
    $silStmt = $conn->prepare($silQuery);
    $silStmt->bindParam(':id', $sil_id);

    if ($silStmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Düzenleme işlemi
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $gelis_tarihi = !empty($_POST['gelis_tarihi']) ? $_POST['gelis_tarihi'] : null;
    $firma = $_POST['firma'];
    $motor_tanimi = $_POST['motor_tanimi'];
    $aciklama = $_POST['aciklama'];
    $tamir_durumu = $_POST['tamir_durumu'];
    $expertiz_tarihi = !empty($_POST['expertiz_tarihi']) ? $_POST['expertiz_tarihi'] : null;
    $teklif_tarihi = !empty($_POST['teklif_tarihi']) ? $_POST['teklif_tarihi'] : null;
    $onay_tarihi = !empty($_POST['onay_tarihi']) ? $_POST['onay_tarihi'] : null;
    $hazir_olma_tarihi = !empty($_POST['hazir_olma_tarihi']) ? $_POST['hazir_olma_tarihi'] : null;
    $fatura_tarihi = !empty($_POST['fatura_tarihi']) ? $_POST['fatura_tarihi'] : null;
    $teslim_tarihi = !empty($_POST['teslim_tarihi']) ? $_POST['teslim_tarihi'] : null;
    $gecikme_aciklamasi = $_POST['gecikme_aciklamasi'];

    $updateQuery = "UPDATE el_aletleri SET
                    gelis_tarihi = :gelis_tarihi,
                    firma = :firma,
                    motor_tanimi = :motor_tanimi,
                    aciklama = :aciklama,
                    tamir_durumu = :tamir_durumu,
                    expertiz_tarihi = :expertiz_tarihi,
                    teklif_tarihi = :teklif_tarihi,
                    onay_tarihi = :onay_tarihi,
                    hazir_olma_tarihi = :hazir_olma_tarihi,
                    fatura_tarihi = :fatura_tarihi,
                    teslim_tarihi = :teslim_tarihi,
                    gecikme_aciklamasi = :gecikme_aciklamasi
                    WHERE id = :id";

    $stmt = $conn->prepare($updateQuery);
    $stmt->execute([
        ':gelis_tarihi' => $gelis_tarihi,
        ':firma' => $firma,
        ':motor_tanimi' => $motor_tanimi,
        ':aciklama' => $aciklama,
        ':tamir_durumu' => $tamir_durumu,
        ':expertiz_tarihi' => $expertiz_tarihi,
        ':teklif_tarihi' => $teklif_tarihi,
        ':onay_tarihi' => $onay_tarihi,
        ':hazir_olma_tarihi' => $hazir_olma_tarihi,
        ':fatura_tarihi' => $fatura_tarihi,
        ':teslim_tarihi' => $teslim_tarihi,
        ':gecikme_aciklamasi' => $gecikme_aciklamasi,
        ':id' => $id
    ]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Sayfa bilgisi
$limit = 30; // Sayfa başına gösterilecek kayıt sayısı
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit; // Hangi kayıttan başlanacağını hesapla

// Toplam kayıt sayısını al
$countQuery = "SELECT COUNT(*) FROM el_aletleri WHERE 1";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn(); // Toplam kayıt sayısını al
$totalPages = ceil($totalRecords / $limit); // Toplam sayfa sayısını hesapla


// Veri ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['id'])) {
    try {
        // Boş tarih alanlarını NULL olarak ayarla ve tarih formatını kontrol et
        $gelis_tarihi = !empty($_POST['gelis_tarihi']) ? date('Y-m-d', strtotime($_POST['gelis_tarihi'])) : null;
        $expertiz_tarihi = !empty($_POST['expertiz_tarihi']) ? date('Y-m-d', strtotime($_POST['expertiz_tarihi'])) : null;
        $teklif_tarihi = !empty($_POST['teklif_tarihi']) ? date('Y-m-d', strtotime($_POST['teklif_tarihi'])) : null;
        $onay_tarihi = !empty($_POST['onay_tarihi']) ? date('Y-m-d', strtotime($_POST['onay_tarihi'])) : null;
        $hazir_olma_tarihi = !empty($_POST['hazir_olma_tarihi']) ? date('Y-m-d', strtotime($_POST['hazir_olma_tarihi'])) : null;
        $fatura_tarihi = !empty($_POST['fatura_tarihi']) ? date('Y-m-d', strtotime($_POST['fatura_tarihi'])) : null;
        $teslim_tarihi = !empty($_POST['teslim_tarihi']) ? date('Y-m-d', strtotime($_POST['teslim_tarihi'])) : null;

        // SQL sorgusu
        $stmt = $conn->prepare("INSERT INTO el_aletleri (gelis_tarihi, firma, motor_tanimi, aciklama, tamir_durumu, expertiz_tarihi, teklif_tarihi, onay_tarihi, hazir_olma_tarihi, fatura_tarihi, teslim_tarihi, gecikme_aciklamasi) 
                                VALUES (:gelis_tarihi, :firma, :motor_tanimi, :aciklama, :tamir_durumu, :expertiz_tarihi, :teklif_tarihi, :onay_tarihi, :hazir_olma_tarihi, :fatura_tarihi, :teslim_tarihi, :gecikme_aciklamasi)");

        // Verileri bağla ve sorguyu çalıştır
        $stmt->execute([
            ':gelis_tarihi' => $gelis_tarihi,
            ':firma' => $_POST['firma'],
            ':motor_tanimi' => $_POST['motor_tanimi'],
            ':aciklama' => $_POST['aciklama'],
            ':tamir_durumu' => $_POST['tamir_durumu'],
            ':expertiz_tarihi' => $expertiz_tarihi,
            ':teklif_tarihi' => $teklif_tarihi,
            ':onay_tarihi' => $onay_tarihi,
            ':hazir_olma_tarihi' => $hazir_olma_tarihi,
            ':fatura_tarihi' => $fatura_tarihi,
            ':teslim_tarihi' => $teslim_tarihi,
            ':gecikme_aciklamasi' => $_POST['gecikme_aciklamasi']
        ]);

        // Sayfayı yenile
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}

// Filtreleme değişkenlerini al
$firma_filter = isset($_GET['firma']) ? $_GET['firma'] : '';
$motor_tanimi_filter = isset($_GET['motor_tanimi']) ? $_GET['motor_tanimi'] : '';

// Sayfalama için veri çekme sorgusu
$query = "SELECT * FROM el_aletleri WHERE 1";
$params = [];

if (!empty($firma_filter)) {
    $query .= " AND firma LIKE :firma";
    $params[':firma'] = "%$firma_filter%";
}

if (!empty($motor_tanimi_filter)) {
    $query .= " AND motor_tanimi LIKE :motor_tanimi";
    $params[':motor_tanimi'] = "%$motor_tanimi_filter%";
}

// Verileri ID'ye göre artan sırada sıralayın
$query .= " ORDER BY id ASC"; // Artan sırada sıralama

$query .= " LIMIT $limit OFFSET $offset"; // Sayfalama eklenmiş sorgu
$stmt = $conn->prepare($query);
$stmt->execute($params);
$veriler = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Tamir durumlarını veritabanından çek
$tamirDurumlariQuery = "SELECT * FROM tamir_durumlari";
$tamirDurumlariStmt = $conn->prepare($tamirDurumlariQuery);
$tamirDurumlariStmt->execute();
$tamirDurumlari = $tamirDurumlariStmt->fetchAll(PDO::FETCH_ASSOC);

// Firmaları veritabanından çek
$firmalarQuery = "SELECT * FROM firmalar";
$firmalarStmt = $conn->prepare($firmalarQuery);
$firmalarStmt->execute();
$firmalar = $firmalarStmt->fetchAll(PDO::FETCH_ASSOC);


// Motor tanımı verilerini veritabanından çek
$motorTanimiQuery = "SELECT * FROM motor_tanimi_tablo";
$motorTanimiStmt = $conn->prepare($motorTanimiQuery);
$motorTanimiStmt->execute();
$motorTanimlari = $motorTanimiStmt->fetchAll(PDO::FETCH_ASSOC);



?>


<?php
// Güncelleme işlemi için veriyi çekme
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM el_aletleri WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>El Aletleri Yönetim Paneli</title>
    <style>
        body { display: flex; font-family: Arial, sans-serif; }
        .sidebar { width: 300px; padding: 20px; background: #f4f4f4; }
        .content { flex: 1; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: left; }
        button { cursor: pointer; }
        .pagination { margin-top: 20px; }
        .pagination a { margin-right: 5px; text-decoration: none; }
        .pagination button { padding: 5px 10px; cursor: pointer; }
        .disabled { color: grey; cursor: not-allowed; }


        body { display: flex; font-family: Arial, sans-serif; }
        .sidebar { width: 300px; padding: 20px; background: #f4f4f4; }
        .content { flex: 1; padding: 20px; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            table-layout: fixed; /* Sabit hücre genişlikleri sağlar */
        }
        th, td { 
            border: 1px solid black; 
            padding: 8px; 
            text-align: left; 
            overflow: hidden; /* Taşan metni gizler */
            text-overflow: ellipsis; /* Taşan metni "..." ile gösterir */
            white-space: nowrap; /* Metni tek satırda tutar */
        }
        th { background-color: #f4f4f4; }
        button { cursor: pointer; }
        .pagination { margin-top: 20px; }
        .pagination a { margin-right: 5px; text-decoration: none; }
        .pagination button { padding: 5px 10px; cursor: pointer; }
        .disabled { color: grey; cursor: not-allowed; }


        table th.id-column, table td.id-column {
        width: 50px !important; /* Genişliği zorunlu kılar */
        text-align: center; /* Metni ortalar */
    }

        table th.actions-column, table td.actions-column {
        width: 150px; /* İşlemler sütununun genişliği */ 
        text-align: center; /* Metni ortalar */
    }

    .content select {
        font-size: 12px; /* Yazı boyutunu küçült */
        padding: 2px 4px; /* İç boşlukları küçült */
        height: 25px; /* Yüksekliği ayarla */
        width: 120px; /* Genişliği ayarla */
        border-radius: 4px; /* Köşeleri yuvarla */
    }






    </style>


</head>
<body>
<!-- Yeni kayıt formu -->

<div class="sidebar">
    <h2>Yeni Kayıt Ekle</h2>
    <form method="POST" style="display: flex; flex-direction: column; gap: 10px;">
        <label>Geliş Tarihi:</label>
        <input type="date" name="gelis_tarihi" required>

        <label>Firma:</label>
<select name="firma" required>
    <option value="">Seçiniz</option>
    <?php foreach ($firmalar as $firma): ?>
        <option value="<?= htmlspecialchars($firma['firma_adi']) ?>" 
            <?= isset($data['firma']) && $data['firma'] == $firma['firma_adi'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($firma['firma_adi']) ?>
        </option>
    <?php endforeach; ?>
</select>


<label>Motor Tanımı:</label>
<select name="motor_tanimi" required>
    <option value="">Seçiniz</option>
    <?php foreach ($motorTanimlari as $motor): ?>
        <option value="<?= htmlspecialchars($motor['motor_tanimi']) ?>" 
            <?= isset($data['motor_tanimi']) && $data['motor_tanimi'] == $motor['motor_tanimi'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($motor['motor_tanimi']) ?>
        </option>
    <?php endforeach; ?>
</select>

        <label>Açıklama:</label>
        <textarea name="aciklama"></textarea>

        <label>Tamir Durumu:</label>
<select name="tamir_durumu">
    <option value="">Seçiniz</option>
    <?php foreach ($tamirDurumlari as $durum): ?>
        <option value="<?= htmlspecialchars($durum['durum']) ?>" 
            <?= isset($data['tamir_durumu']) && $data['tamir_durumu'] == $durum['durum'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($durum['durum']) ?>
        </option>
    <?php endforeach; ?>
</select>
        <label>Expertiz Tarihi:</label>
        <input type="date" name="expertiz_tarihi">
        <label>Teklif Tarihi:</label>
        <input type="date" name="teklif_tarihi">
        <label>Onay Tarihi:</label>
        <input type="date" name="onay_tarihi">
        <label>Hazır Olma Tarihi:</label>
        <input type="date" name="hazir_olma_tarihi">
        <label>Fatura Tarihi:</label>
        <input type="date" name="fatura_tarihi">
        <label>Teslim Tarihi:</label>
        <input type="date" name="teslim_tarihi">
        <label>Gecikme Açıklaması:</label>
        <textarea name="gecikme_aciklamasi"></textarea>
        <button type="submit" style="background: linear-gradient(135deg, #4CAF50, #45A049); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; transition: background 0.3s;">Ekle</button>    
    </form>

<!-- Güncelleme formu -->
<?php if (isset($data)): ?>
    <h2>Güncelle</h2>
    <form method="POST" action="?" style="display: flex; flex-direction: column; gap: 10px;">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <label>Geliş Tarihi:</label>
        <input type="date" name="gelis_tarihi" value="<?= htmlspecialchars($data['gelis_tarihi']) ?>" required>

        <label>Firma:</label>
<select name="firma" required>
    <option value="">Seçiniz</option>
    <?php foreach ($firmalar as $firma): ?>
        <option value="<?= htmlspecialchars($firma['firma_adi']) ?>" 
            <?= isset($data['firma']) && $data['firma'] == $firma['firma_adi'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($firma['firma_adi']) ?>
        </option>
    <?php endforeach; ?>
</select>

        <label>Motor Tanımı:</label>
<select name="motor_tanimi" required>
    <option value="">Seçiniz</option>
    <?php foreach ($motorTanimlari as $motor): ?>
        <option value="<?= htmlspecialchars($motor['motor_tanimi']) ?>" 
            <?= isset($data['motor_tanimi']) && $data['motor_tanimi'] == $motor['motor_tanimi'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($motor['motor_tanimi']) ?>
        </option>
    <?php endforeach; ?>
</select>


        <label>Açıklama:</label>
        <textarea name="aciklama"><?= htmlspecialchars($data['aciklama']) ?></textarea>

        <label>Tamir Durumu:</label>
<select name="tamir_durumu">
    <option value="">Seçiniz</option>
    <?php foreach ($tamirDurumlari as $durum): ?>
        <option value="<?= htmlspecialchars($durum['durum']) ?>" 
            <?= isset($data['tamir_durumu']) && $data['tamir_durumu'] == $durum['durum'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($durum['durum']) ?>
        </option>
    <?php endforeach; ?>
</select>

        <label>Expertiz Tarihi:</label>
        <input type="date" name="expertiz_tarihi" value="<?= htmlspecialchars($data['expertiz_tarihi']) ?>">

        <label>Teklif Tarihi:</label>
        <input type="date" name="teklif_tarihi" value="<?= htmlspecialchars($data['teklif_tarihi']) ?>">

        <label>Onay Tarihi:</label>
        <input type="date" name="onay_tarihi" value="<?= htmlspecialchars($data['onay_tarihi']) ?>">

        <label>Hazır Olma Tarihi:</label>
        <input type="date" name="hazir_olma_tarihi" value="<?= htmlspecialchars($data['hazir_olma_tarihi']) ?>">

        <label>Fatura Tarihi:</label>
        <input type="date" name="fatura_tarihi" value="<?= htmlspecialchars($data['fatura_tarihi']) ?>">

        <label>Teslim Tarihi:</label>
        <input type="date" name="teslim_tarihi" value="<?= htmlspecialchars($data['teslim_tarihi']) ?>">

        <label>Gecikme Açıklaması:</label>
        <textarea name="gecikme_aciklamasi"><?= htmlspecialchars($data['gecikme_aciklamasi']) ?></textarea>

        <button type="submit" style="background: linear-gradient(135deg, #2196F3, #1E88E5); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; transition: background 0.3s;">Güncelle</button>
    </form>
    
<?php endif; ?>



</div>


<div class="content">
    <h2>El Aletleri Listesi</h2>
    <form method="GET">
    <label>Firma:</label>
<select name="firma" required>
    <option value="">Seçiniz</option>
    <?php foreach ($firmalar as $firma): ?>
        <option value="<?= htmlspecialchars($firma['firma_adi']) ?>" 
            <?= isset($data['firma']) && $data['firma'] == $firma['firma_adi'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($firma['firma_adi']) ?>
        </option>
    <?php endforeach; ?>
</select>


<label>Motor Tanımı:</label>
<select name="motor_tanimi" required>
    <option value="">Seçiniz</option>
    <?php foreach ($motorTanimlari as $motor): ?>
        <option value="<?= htmlspecialchars($motor['motor_tanimi']) ?>" 
            <?= isset($data['motor_tanimi']) && $data['motor_tanimi'] == $motor['motor_tanimi'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($motor['motor_tanimi']) ?>
        </option>
    <?php endforeach; ?>
</select>

        <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: 0.3s;">Filtrele</button>
<a href="<?= $_SERVER['PHP_SELF'] ?>" style="text-decoration: none;">
    <button type="button" style="background-color: #f44336; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: 0.3s;">Temizle</button>
</a>
    </form>

    <table>
    <tr>
        <th class="id-column">ID</th><th>Geliş Tarihi</th><th>Firma</th><th>Motor Tanımı</th><th>Açıklama</th><th>Tamir Durumu</th><th>Expertiz Tarihi</th><th>Teklif Tarihi</th><th>Onay Tarihi</th><th>Hazır Olma Tarihi</th><th>Fatura Tarihi</th><th>Teslim Tarihi</th><th>Gecikme Açıklaması</th><th class="actions-column">İşlemler</th>
    </tr>
    <?php foreach ($veriler as $veri): ?>
        <tr>
            <td class="id-column"><?= htmlspecialchars($veri['id']) ?></td>
            <td><?= date('d-m-Y', strtotime($veri['gelis_tarihi'])) ?></td>
            <td><?= htmlspecialchars($veri['firma']) ?></td>
            <td><?= htmlspecialchars($veri['motor_tanimi']) ?></td>
            <td><?= htmlspecialchars($veri['aciklama']) ?></td>
            <td><?= htmlspecialchars($veri['tamir_durumu']) ?></td>
            <td><?= date('d-m-Y', strtotime($veri['expertiz_tarihi'])) ?></td>
            <td><?= date('d-m-Y', strtotime($veri['teklif_tarihi'])) ?></td>
            <td><?= date('d-m-Y', strtotime($veri['onay_tarihi'])) ?></td>
            <td><?= date('d-m-Y', strtotime($veri['hazir_olma_tarihi'])) ?></td>
            <td><?= date('d-m-Y', strtotime($veri['fatura_tarihi'])) ?></td>
            <td><?= date('d-m-Y', strtotime($veri['teslim_tarihi'])) ?></td>
            <td><?= htmlspecialchars($veri['gecikme_aciklamasi']) ?></td>
            <td>
                <div class="actions-column" style="display: flex; gap: 10px;">
                    <a href="?sil_id=<?= $veri['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?')">
                        <button type="button" style="background-color: #e74c3c; color: white; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.3s;">Sil</button>
                    </a>
                    <a href="?id=<?= $veri['id'] ?>">
                        <button type="button" style="background-color: #3498db; color: white; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.3s;">Düzenle</button>
                    </a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
    <!-- Sayfalama -->
    <div class="pagination">
        <a href="?page=1" class="<?= $page == 1 ? 'disabled' : '' ?>"><button type="button">İlk</button></a>
        <a href="?page=<?= $page - 1 ?>" class="<?= $page == 1 ? 'disabled' : '' ?>"><button type="button">Geri</button></a>
        <span>Sayfa: <?= $page ?> / <?= $totalPages ?></span>
        <a href="?page=<?= $page + 1 ?>" class="<?= $page == $totalPages ? 'disabled' : '' ?>"><button type="button">İleri</button></a>
        <a href="?page=<?= $totalPages ?>" class="<?= $page == $totalPages ? 'disabled' : '' ?>"><button type="button">Son</button></a>
    </div>
</div>

</body>
</html>
