<?php
session_name('ADMIN_SESSION');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminlogin.php');
    exit;
}

require_once 'connection.php';

$tab = $_GET['tab'] ?? 'overview';
$msg = $_GET['msg'] ?? '';

// Stats
$total_reports  = mysqli_fetch_row(mysqli_query($db, "SELECT COUNT(*) FROM garbageinfo"))[0];
$pending        = mysqli_fetch_row(mysqli_query($db, "SELECT COUNT(*) FROM garbageinfo WHERE status='Pending'"))[0];
$completed      = mysqli_fetch_row(mysqli_query($db, "SELECT COUNT(*) FROM garbageinfo WHERE status='Completed'"))[0];
$total_users    = mysqli_fetch_row(mysqli_query($db, "SELECT COUNT(*) FROM usertable"))[0];
$total_messages = mysqli_fetch_row(mysqli_query($db, "SELECT COUNT(*) FROM contact"))[0];

// Search / filter for reports
$search        = isset($_GET['search']) ? mysqli_real_escape_string($db, trim($_GET['search'])) : '';
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($db, $_GET['status']) : '';
$filter_loc    = isset($_GET['location']) ? mysqli_real_escape_string($db, $_GET['location']) : '';

$where = [];
if ($search)        $where[] = "(name LIKE '%$search%' OR email LIKE '%$search%' OR location LIKE '%$search%')";
if ($filter_status) $where[] = "status = '$filter_status'";
if ($filter_loc)    $where[] = "location = '$filter_loc'";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$reports  = mysqli_query($db, "SELECT * FROM garbageinfo $where_sql ORDER BY date DESC");
$messages = mysqli_query($db, "SELECT * FROM contact ORDER BY id DESC");
$users    = mysqli_query($db, "SELECT * FROM usertable ORDER BY id DESC");

$msg_text = [
    'marked'   => ['✅ Report marked as Completed.', 'success'],
    'reverted' => ['🔄 Report reverted to Pending.', 'info'],
    'deleted'  => ['🗑️ Record deleted successfully.', 'warning'],
];

function status_badge($s) {
    $color = $s === 'Completed' ? '#27ae60' : '#e67e22';
    return "<span style='background:$color;color:#fff;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600'>$s</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - WasteWise</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; color: #333; }

  /* Sidebar */
  .sidebar {
    position: fixed; top: 0; left: 0;
    width: 240px; height: 100vh;
    background: linear-gradient(180deg, #1a3d2b 0%, #1a6b3c 100%);
    display: flex; flex-direction: column;
    padding: 0; z-index: 100;
  }
  .sidebar-brand {
    padding: 24px 20px;
    border-bottom: 1px solid rgba(255,255,255,.1);
  }
  .sidebar-brand h2 { color: #fff; font-size: 18px; font-weight: 700; }
  .sidebar-brand p  { color: rgba(255,255,255,.6); font-size: 12px; margin-top: 4px; }
  .nav-item {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 20px; color: rgba(255,255,255,.75);
    text-decoration: none; font-size: 14px; font-weight: 500;
    transition: all .2s; border-left: 3px solid transparent;
  }
  .nav-item:hover, .nav-item.active {
    background: rgba(255,255,255,.1);
    color: #fff;
    border-left-color: #4caf7d;
  }
  .nav-icon { font-size: 18px; width: 24px; text-align: center; }
  .sidebar-footer {
    margin-top: auto; padding: 20px;
    border-top: 1px solid rgba(255,255,255,.1);
  }
  .logout-btn {
    display: block; width: 100%; padding: 10px;
    background: rgba(231,76,60,.8); color: #fff; border: none;
    border-radius: 8px; cursor: pointer; font-size: 14px;
    text-align: center; text-decoration: none; font-weight: 600;
    transition: background .2s;
  }
  .logout-btn:hover { background: #e74c3c; }

  /* Main */
  .main { margin-left: 240px; min-height: 100vh; }
  .topbar {
    background: #fff; padding: 16px 28px;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 1px 4px rgba(0,0,0,.08); position: sticky; top: 0; z-index: 50;
  }
  .topbar h1 { font-size: 20px; font-weight: 700; color: #1a3d2b; }
  .admin-badge {
    background: #e8f5e9; color: #1a6b3c;
    padding: 6px 14px; border-radius: 20px;
    font-size: 13px; font-weight: 600;
  }
  .content { padding: 28px; }

  /* Alert */
  .alert {
    padding: 12px 18px; border-radius: 8px;
    margin-bottom: 24px; font-size: 14px; font-weight: 500;
  }
  .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
  .alert.info    { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
  .alert.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

  /* Stats grid */
  .stats-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px; margin-bottom: 32px;
  }
  .stat-card {
    background: #fff; border-radius: 12px;
    padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.07);
    border-top: 4px solid var(--c);
  }
  .stat-card .icon { font-size: 28px; margin-bottom: 12px; }
  .stat-card .value { font-size: 32px; font-weight: 700; color: var(--c); }
  .stat-card .label { font-size: 13px; color: #666; margin-top: 4px; }

  /* Table */
  .table-card {
    background: #fff; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07); overflow: hidden;
  }
  .table-header {
    padding: 20px 24px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; gap: 12px;
  }
  .table-header h3 { font-size: 16px; font-weight: 700; color: #1a3d2b; }
  .filters { display: flex; gap: 8px; flex-wrap: wrap; }
  .filters input, .filters select {
    padding: 8px 12px; border: 1px solid #ddd;
    border-radius: 6px; font-size: 13px; outline: none;
  }
  .filters input:focus, .filters select:focus { border-color: #2d9e5f; }
  .filter-btn {
    padding: 8px 16px; background: #1a6b3c; color: #fff;
    border: none; border-radius: 6px; font-size: 13px;
    cursor: pointer; font-weight: 600;
  }
  table { width: 100%; border-collapse: collapse; }
  th {
    background: #f8faf8; padding: 12px 16px;
    text-align: left; font-size: 12px; font-weight: 700;
    color: #555; text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid #ebebeb;
  }
  td {
    padding: 14px 16px; font-size: 14px;
    border-bottom: 1px solid #f5f5f5; vertical-align: middle;
  }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: #fafffe; }
  .actions { display: flex; gap: 6px; flex-wrap: wrap; }
  .btn-sm {
    padding: 5px 12px; border: none; border-radius: 6px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    text-decoration: none; display: inline-block;
    transition: opacity .2s;
  }
  .btn-sm:hover { opacity: .8; }
  .btn-complete { background: #27ae60; color: #fff; }
  .btn-pending  { background: #e67e22; color: #fff; }
  .btn-delete   { background: #e74c3c; color: #fff; }
  .btn-view     { background: #3498db; color: #fff; }
  .empty-row td { text-align: center; color: #aaa; padding: 40px; }
  .img-thumb {
    width: 48px; height: 48px; object-fit: cover;
    border-radius: 6px; border: 1px solid #ddd;
  }
  .truncate { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

  /* Modal */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.5); z-index: 200;
    align-items: center; justify-content: center;
  }
  .modal-overlay.open { display: flex; }
  .modal {
    background: #fff; border-radius: 12px; padding: 28px;
    max-width: 520px; width: 90%; position: relative;
    max-height: 90vh; overflow-y: auto;
  }
  .modal-close {
    position: absolute; top: 16px; right: 16px;
    background: none; border: none; font-size: 20px;
    cursor: pointer; color: #666;
  }
  .modal h3 { margin-bottom: 16px; color: #1a3d2b; }
  .detail-row { display: flex; gap: 8px; margin-bottom: 10px; font-size: 14px; }
  .detail-label { font-weight: 600; color: #555; min-width: 130px; }
  .detail-img { width: 100%; border-radius: 8px; margin-top: 12px; }

  @media (max-width: 768px) {
    .sidebar { width: 200px; }
    .main { margin-left: 200px; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
  }
</style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <h2>♻️ WasteWise</h2>
    <p>Admin Panel</p>
  </div>
  <a href="admin-dashboard.php?tab=overview" class="nav-item <?= $tab==='overview'?'active':'' ?>">
    <span class="nav-icon">📊</span> Overview
  </a>
  <a href="admin-dashboard.php?tab=reports" class="nav-item <?= $tab==='reports'?'active':'' ?>">
    <span class="nav-icon">🗑️</span> Waste Reports
    <?php if ($pending > 0): ?>
      <span style="margin-left:auto;background:#e67e22;color:#fff;border-radius:10px;padding:2px 8px;font-size:11px"><?= $pending ?></span>
    <?php endif; ?>
  </a>
  <a href="admin-dashboard.php?tab=messages" class="nav-item <?= $tab==='messages'?'active':'' ?>">
    <span class="nav-icon">✉️</span> Messages
    <?php if ($total_messages > 0): ?>
      <span style="margin-left:auto;background:#3498db;color:#fff;border-radius:10px;padding:2px 8px;font-size:11px"><?= $total_messages ?></span>
    <?php endif; ?>
  </a>
  <a href="admin-dashboard.php?tab=users" class="nav-item <?= $tab==='users'?'active':'' ?>">
    <span class="nav-icon">👥</span> Users
  </a>
  <div class="sidebar-footer">
    <a href="admin-logout.php" class="logout-btn">🚪 Logout</a>
  </div>
</aside>

<!-- Main -->
<main class="main">
  <div class="topbar">
    <h1><?= ['overview'=>'Dashboard Overview','reports'=>'Waste Reports','messages'=>'Contact Messages','users'=>'Registered Users'][$tab] ?? 'Dashboard' ?></h1>
    <span class="admin-badge">👤 <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
  </div>
  <div class="content">

    <?php if ($msg && isset($msg_text[$msg])): ?>
      <div class="alert <?= $msg_text[$msg][1] ?>"><?= $msg_text[$msg][0] ?></div>
    <?php endif; ?>

    <!-- ===== OVERVIEW ===== -->
    <?php if ($tab === 'overview'): ?>
      <div class="stats-grid">
        <div class="stat-card" style="--c:#2d9e5f">
          <div class="icon">📋</div>
          <div class="value"><?= $total_reports ?></div>
          <div class="label">Total Reports</div>
        </div>
        <div class="stat-card" style="--c:#e67e22">
          <div class="icon">⏳</div>
          <div class="value"><?= $pending ?></div>
          <div class="label">Pending</div>
        </div>
        <div class="stat-card" style="--c:#27ae60">
          <div class="icon">✅</div>
          <div class="value"><?= $completed ?></div>
          <div class="label">Completed</div>
        </div>
        <div class="stat-card" style="--c:#3498db">
          <div class="icon">👥</div>
          <div class="value"><?= $total_users ?></div>
          <div class="label">Users</div>
        </div>
        <div class="stat-card" style="--c:#9b59b6">
          <div class="icon">✉️</div>
          <div class="value"><?= $total_messages ?></div>
          <div class="label">Messages</div>
        </div>
      </div>

      <!-- Recent Reports -->
      <div class="table-card">
        <div class="table-header">
          <h3>Recent Waste Reports</h3>
          <a href="admin-dashboard.php?tab=reports" style="font-size:13px;color:#2d9e5f;text-decoration:none;font-weight:600">View All →</a>
        </div>
        <table>
          <thead><tr>
            <th>ID</th><th>Name</th><th>Location</th><th>Waste Type</th><th>Date</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
          <?php
          $recent = mysqli_query($db, "SELECT * FROM garbageinfo ORDER BY date DESC LIMIT 10");
          if (mysqli_num_rows($recent) === 0): ?>
            <tr class="empty-row"><td colspan="7">No reports yet.</td></tr>
          <?php else: while ($r = mysqli_fetch_assoc($recent)): ?>
            <tr>
              <td>#<?= $r['Id'] ?></td>
              <td><?= htmlspecialchars($r['name']) ?></td>
              <td><?= htmlspecialchars($r['location']) ?></td>
              <td><?= htmlspecialchars($r['wastetype']) ?></td>
              <td><?= htmlspecialchars($r['date']) ?></td>
              <td><?= status_badge($r['status']) ?></td>
              <td class="actions">
                <?php if ($r['status'] === 'Pending'): ?>
                  <a class="btn-sm btn-complete" href="admin-action.php?action=complete&id=<?= $r['Id'] ?>">✓ Complete</a>
                <?php else: ?>
                  <a class="btn-sm btn-pending" href="admin-action.php?action=pending&id=<?= $r['Id'] ?>">↩ Revert</a>
                <?php endif; ?>
                <a class="btn-sm btn-delete" href="admin-action.php?action=delete_report&id=<?= $r['Id'] ?>"
                   onclick="return confirm('Delete this report?')">🗑</a>
              </td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>

    <!-- ===== REPORTS ===== -->
    <?php elseif ($tab === 'reports'): ?>
      <div class="table-card">
        <div class="table-header">
          <h3>All Waste Reports (<?= mysqli_num_rows($reports) ?>)</h3>
          <form method="GET" class="filters">
            <input type="hidden" name="tab" value="reports">
            <input type="text" name="search" placeholder="Search name/email/location..." value="<?= htmlspecialchars($search) ?>">
            <select name="status">
              <option value="">All Status</option>
              <option <?= $filter_status==='Pending'?'selected':'' ?>>Pending</option>
              <option <?= $filter_status==='Completed'?'selected':'' ?>>Completed</option>
            </select>
            <select name="location">
              <option value="">All Locations</option>
              <?php
              $locs = mysqli_query($db, "SELECT DISTINCT location FROM garbageinfo ORDER BY location");
              while ($l = mysqli_fetch_row($locs)):
              ?>
              <option <?= $filter_loc===$l[0]?'selected':'' ?>><?= htmlspecialchars($l[0]) ?></option>
              <?php endwhile; ?>
            </select>
            <button class="filter-btn" type="submit">Filter</button>
            <a href="admin-dashboard.php?tab=reports" style="padding:8px 12px;font-size:13px;color:#666;text-decoration:none">Clear</a>
          </form>
        </div>
        <table>
          <thead><tr>
            <th>ID</th><th>Photo</th><th>Name</th><th>Contact</th><th>Location</th><th>Type</th><th>Description</th><th>Date</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($reports) === 0): ?>
            <tr class="empty-row"><td colspan="10">No reports found.</td></tr>
          <?php else: while ($r = mysqli_fetch_assoc($reports)): ?>
            <tr>
              <td>#<?= $r['Id'] ?></td>
              <td>
                <?php if ($r['file'] && file_exists($r['file'])): ?>
                  <img src="<?= htmlspecialchars($r['file']) ?>" class="img-thumb"
                       onclick="openModal('<?= htmlspecialchars(addslashes(json_encode($r))) ?>')" style="cursor:pointer">
                <?php else: ?>
                  <span style="color:#bbb;font-size:12px">No image</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($r['name']) ?></td>
              <td style="font-size:12px">
                <?= htmlspecialchars($r['email']) ?><br>
                <span style="color:#888"><?= htmlspecialchars($r['mobile']) ?></span>
              </td>
              <td><?= htmlspecialchars($r['location']) ?></td>
              <td><?= htmlspecialchars($r['wastetype']) ?></td>
              <td><div class="truncate" title="<?= htmlspecialchars($r['locationdescription']) ?>"><?= htmlspecialchars($r['locationdescription']) ?></div></td>
              <td style="font-size:12px;white-space:nowrap"><?= htmlspecialchars($r['date']) ?></td>
              <td><?= status_badge($r['status']) ?></td>
              <td class="actions">
                <button class="btn-sm btn-view" onclick="openModal('<?= htmlspecialchars(addslashes(json_encode($r))) ?>')">👁 View</button>
                <?php if ($r['status'] === 'Pending'): ?>
                  <a class="btn-sm btn-complete" href="admin-action.php?action=complete&id=<?= $r['Id'] ?>">✓ Done</a>
                <?php else: ?>
                  <a class="btn-sm btn-pending" href="admin-action.php?action=pending&id=<?= $r['Id'] ?>">↩</a>
                <?php endif; ?>
                <a class="btn-sm btn-delete" href="admin-action.php?action=delete_report&id=<?= $r['Id'] ?>"
                   onclick="return confirm('Delete this report permanently?')">🗑</a>
              </td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>

    <!-- ===== MESSAGES ===== -->
    <?php elseif ($tab === 'messages'): ?>
      <div class="table-card">
        <div class="table-header"><h3>Contact Messages (<?= mysqli_num_rows($messages) ?>)</h3></div>
        <table>
          <thead><tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Actions</th>
          </tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($messages) === 0): ?>
            <tr class="empty-row"><td colspan="6">No messages yet.</td></tr>
          <?php else: while ($m = mysqli_fetch_assoc($messages)): ?>
            <tr>
              <td>#<?= $m['id'] ?></td>
              <td><?= htmlspecialchars($m['fname'] . ' ' . $m['lname']) ?></td>
              <td><?= htmlspecialchars($m['contactEmail']) ?></td>
              <td><?= htmlspecialchars($m['contactPhone']) ?></td>
              <td><div class="truncate" title="<?= htmlspecialchars($m['comment']) ?>"><?= htmlspecialchars($m['comment']) ?></div></td>
              <td class="actions">
                <a class="btn-sm btn-delete" href="admin-action.php?action=delete_message&id=<?= $m['id'] ?>"
                   onclick="return confirm('Delete this message?')">🗑 Delete</a>
              </td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>

    <!-- ===== USERS ===== -->
    <?php elseif ($tab === 'users'): ?>
      <div class="table-card">
        <div class="table-header"><h3>Registered Users (<?= mysqli_num_rows($users) ?>)</h3></div>
        <table>
          <thead><tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($users) === 0): ?>
            <tr class="empty-row"><td colspan="5">No users registered yet.</td></tr>
          <?php else: while ($u = mysqli_fetch_assoc($users)): ?>
            <tr>
              <td>#<?= $u['id'] ?></td>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <?php
                $vc = $u['status']==='verified' ? '#27ae60' : '#e74c3c';
                echo "<span style='background:$vc;color:#fff;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600'>".htmlspecialchars($u['status'])."</span>";
                ?>
              </td>
              <td class="actions">
                <a class="btn-sm btn-delete" href="admin-action.php?action=delete_user&id=<?= $u['id'] ?>"
                   onclick="return confirm('Delete user <?= htmlspecialchars(addslashes($u['name'])) ?>? This cannot be undone.')">🗑 Delete</a>
              </td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div><!-- /content -->
</main>

<!-- Report Detail Modal -->
<div class="modal-overlay" id="reportModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <h3>Report Details</h3>
    <div id="modalBody"></div>
  </div>
</div>

<script>
function openModal(jsonStr) {
  const r = JSON.parse(jsonStr);
  let html = `
    <div class="detail-row"><span class="detail-label">Report ID:</span> #${r.Id}</div>
    <div class="detail-row"><span class="detail-label">Name:</span> ${r.name}</div>
    <div class="detail-row"><span class="detail-label">Email:</span> ${r.email}</div>
    <div class="detail-row"><span class="detail-label">Phone:</span> ${r.mobile}</div>
    <div class="detail-row"><span class="detail-label">Waste Type:</span> ${r.wastetype}</div>
    <div class="detail-row"><span class="detail-label">Location:</span> ${r.location}</div>
    <div class="detail-row"><span class="detail-label">Description:</span> ${r.locationdescription}</div>
    <div class="detail-row"><span class="detail-label">Date:</span> ${r.date}</div>
    <div class="detail-row"><span class="detail-label">Status:</span> ${r.status}</div>
  `;
  if (r.file) {
    html += `<img src="${r.file}" class="detail-img" alt="Waste photo" onerror="this.style.display='none'">`;
  }
  document.getElementById('modalBody').innerHTML = html;
  document.getElementById('reportModal').classList.add('open');
}
function closeModal() {
  document.getElementById('reportModal').classList.remove('open');
}
document.getElementById('reportModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
</script>
</body>
</html>
