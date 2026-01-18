<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'page/auth/login.php');
    exit();
}

// Check if user is ADMIN
if (!Auth::isAdmin()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$currentUser = Auth::getUser();

// Set page-specific variables
$page_title = "Kelola Jenis Olahraga";
$page_subtitle = "Manage semua jenis olahraga yang tersedia";

// Query database
require_once '../../config/db.php';
$jenis_list = [];

try {
    $stmt = $pdo->query("SELECT * FROM jenis_olahraga ORDER BY nama ASC");
    $jenis_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error loading jenis: " . $e->getMessage());
}

// Start output buffering for page content
ob_start();
?>

<!-- Header -->
<div class="section-header">
    <h3>Daftar Jenis Olahraga</h3>
    <button onclick="openAddModal()" class="btn-primary">
        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Jenis</span>
    </button>
</div>

<!-- Table -->
<div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 24px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb;">
                <th style="text-align: left; padding: 12px; color: #6b7280; font-weight: 600; font-size: 14px;">No</th>
                <th style="text-align: left; padding: 12px; color: #6b7280; font-weight: 600; font-size: 14px;">Nama Jenis Olahraga</th>
                <th style="text-align: center; padding: 12px; color: #6b7280; font-weight: 600; font-size: 14px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jenis_list)): ?>
            <tr>
                <td colspan="3" style="text-align: center; padding: 20px; color: #9ca3af;">
                    Belum ada jenis olahraga
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($jenis_list as $index => $jenis): ?>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px;"><?php echo $index + 1; ?></td>
                    <td style="padding: 12px;"><?php echo htmlspecialchars($jenis['nama']); ?></td>
                    <td style="padding: 12px; text-align: center;">
                        <button onclick="openEditModal(<?php echo $jenis['id']; ?>, '<?php echo htmlspecialchars($jenis['nama']); ?>')" 
                                style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; margin-right: 8px; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                            Edit
                        </button>
                        <button onclick="openDeleteModal(<?php echo $jenis['id']; ?>, '<?php echo htmlspecialchars($jenis['nama']); ?>')" 
                                style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 13px; transition: all 0.3s ease;">
                            Hapus
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; width: 100%; max-width: 500px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
            <h3 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0;">Tambah Jenis Olahraga</h3>
            <button onclick="closeAddModal()" style="background: none; border: none; font-size: 32px; cursor: pointer; color: #6b7280; padding: 0; line-height: 1;">&times;</button>
        </div>
        <form id="addForm" onsubmit="handleAdd(event)">
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Jenis *</label>
                <input type="text" name="nama" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;">
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeAddModal()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: linear-gradient(to right, #16A34A, #22c55e); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; width: 100%; max-width: 500px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
            <h3 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0;">Edit Jenis Olahraga</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 32px; cursor: pointer; color: #6b7280; padding: 0; line-height: 1;">&times;</button>
        </div>
        <form id="editForm" onsubmit="handleEdit(event)">
            <input type="hidden" id="editId" name="id">
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Jenis *</label>
                <input type="text" id="editNama" name="nama" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;">
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="background: linear-gradient(to right, #16A34A, #22c55e); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete -->
<div id="deleteModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; width: 100%; max-width: 500px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
            <h3 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0;">Hapus Jenis Olahraga?</h3>
        </div>
        <p style="margin-bottom: 24px; color: #6b7280;">Anda akan menghapus: <strong id="deleteNama">-</strong></p>
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button onclick="closeDeleteModal()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
            <button onclick="confirmDelete()" style="background: linear-gradient(to right, #ef4444, #dc2626); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Hapus</button>
        </div>
    </div>
</div>

<?php
// Get buffered content and include layout
$page_content = ob_get_clean();
include '../includes/admin-layout.php';
?>

<script>
let deleteId = null;

function openAddModal() {
    document.getElementById('addForm').reset();
    document.getElementById('addModal').style.display = 'flex';
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openEditModal(id, nama) {
    document.getElementById('editId').value = id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openDeleteModal(id, nama) {
    deleteId = id;
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteId = null;
}

function handleAdd(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('addForm'));
    formData.append('action', 'create');
    formData.append('table', 'jenis_olahraga');
    
    fetch('admin-table-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Jenis olahraga berhasil ditambahkan');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function handleEdit(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('editForm'));
    formData.append('action', 'update');
    formData.append('table', 'jenis_olahraga');
    
    fetch('admin-table-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Jenis olahraga berhasil diperbarui');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function confirmDelete() {
    if (!deleteId) return;
    
    fetch('admin-table-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=delete&id=' + deleteId + '&table=jenis_olahraga'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Jenis olahraga berhasil dihapus');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (e.target === addModal) addModal.style.display = 'none';
    if (e.target === editModal) editModal.style.display = 'none';
    if (e.target === deleteModal) deleteModal.style.display = 'none';
});
</script>
