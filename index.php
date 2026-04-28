<?php
// Simple in-memory data simulation (in real app, use database)
session_start();

// Initialize sample data
if (!isset($_SESSION['materiels'])) {
    $_SESSION['materiels'] = [
        ['id' => 1, 'nom' => 'Laptop Dell XPS 15', 'type' => 'Ordinateur', 'serie' => 'DX15-2024-001', 'etat' => 'Bon', 'utilisateur' => 'Ahmed Hassan', 'date' => '2024-01-15'],
        ['id' => 2, 'nom' => 'iPhone 14 Pro', 'type' => 'Téléphone', 'serie' => 'IP14-2024-002', 'etat' => 'Bon', 'utilisateur' => 'Sara Mohamed', 'date' => '2024-02-20'],
        ['id' => 3, 'nom' => 'Imprimante HP LaserJet', 'type' => 'Imprimante', 'serie' => 'HP-LJ-2024-003', 'etat' => 'Maintenance', 'utilisateur' => 'Service IT', 'date' => '2023-11-10'],
        ['id' => 4, 'nom' => 'Switch Cisco 24 ports', 'type' => 'Réseau', 'serie' => 'CS-SW-2024-004', 'etat' => 'Bon', 'utilisateur' => 'Salle Serveur', 'date' => '2023-09-05'],
        ['id' => 5, 'nom' => 'Écran Samsung 27"', 'type' => 'Moniteur', 'serie' => 'SS-27-2024-005', 'etat' => 'Défaillant', 'utilisateur' => 'Non assigné', 'date' => '2022-06-18'],
    ];
    $_SESSION['next_id'] = 6;
}

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $new = [
            'id' => $_SESSION['next_id']++,
            'nom' => htmlspecialchars($_POST['nom']),
            'type' => htmlspecialchars($_POST['type']),
            'serie' => htmlspecialchars($_POST['serie']),
            'etat' => htmlspecialchars($_POST['etat']),
            'utilisateur' => htmlspecialchars($_POST['utilisateur']),
            'date' => $_POST['date']
        ];
        $_SESSION['materiels'][] = $new;
        $message = 'success:Matériel ajouté avec succès !';
        $action = 'list';
    } elseif (isset($_POST['edit'])) {
        foreach ($_SESSION['materiels'] as &$m) {
            if ($m['id'] == $_POST['id']) {
                $m['nom'] = htmlspecialchars($_POST['nom']);
                $m['type'] = htmlspecialchars($_POST['type']);
                $m['serie'] = htmlspecialchars($_POST['serie']);
                $m['etat'] = htmlspecialchars($_POST['etat']);
                $m['utilisateur'] = htmlspecialchars($_POST['utilisateur']);
                $m['date'] = $_POST['date'];
                break;
            }
        }
        $message = 'success:Matériel modifié avec succès !';
        $action = 'list';
    }
}

if ($action === 'delete' && $id) {
    $_SESSION['materiels'] = array_filter($_SESSION['materiels'], fn($m) => $m['id'] != $id);
    $_SESSION['materiels'] = array_values($_SESSION['materiels']);
    $message = 'success:Matériel supprimé avec succès !';
    $action = 'list';
}

$edit_item = null;
if ($action === 'edit' && $id) {
    foreach ($_SESSION['materiels'] as $m) {
        if ($m['id'] == $id) { $edit_item = $m; break; }
    }
}

// Stats
$total = count($_SESSION['materiels']);
$bon = count(array_filter($_SESSION['materiels'], fn($m) => $m['etat'] === 'Bon'));
$maintenance = count(array_filter($_SESSION['materiels'], fn($m) => $m['etat'] === 'Maintenance'));
$defaillant = count(array_filter($_SESSION['materiels'], fn($m) => $m['etat'] === 'Défaillant'));

$etat_colors = ['Bon' => '#00c896', 'Maintenance' => '#f5a623', 'Défaillant' => '#e74c3c'];
$type_icons = ['Ordinateur' => '💻', 'Téléphone' => '📱', 'Imprimante' => '🖨️', 'Réseau' => '🌐', 'Moniteur' => '🖥️', 'Autre' => '📦'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GestMat — Gestion de Matériel</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #0a0a0f;
    --surface: #13131a;
    --surface2: #1c1c27;
    --border: #2a2a3a;
    --accent: #6c63ff;
    --accent2: #00c896;
    --accent3: #f5a623;
    --danger: #e74c3c;
    --text: #e8e8f0;
    --text2: #8888aa;
    --mono: 'Space Mono', monospace;
    --sans: 'Syne', sans-serif;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--sans);
    min-height: 100vh;
    background-image: 
        radial-gradient(ellipse at 20% 20%, rgba(108,99,255,0.08) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 80%, rgba(0,200,150,0.06) 0%, transparent 50%);
}

/* HEADER */
.header {
    background: rgba(19,19,26,0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border);
    padding: 0 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
}

.logo {
    font-family: var(--mono);
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--accent);
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logo span { color: var(--accent2); }

.nav { display: flex; gap: 0.5rem; }

.nav a {
    color: var(--text2);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.nav a:hover, .nav a.active {
    color: var(--text);
    background: var(--surface2);
}

/* MAIN */
.main { max-width: 1200px; margin: 0 auto; padding: 2rem; }

/* STATS */
.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s;
}

.stat-card:hover { transform: translateY(-2px); }

.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}

.stat-card:nth-child(1)::before { background: var(--accent); }
.stat-card:nth-child(2)::before { background: var(--accent2); }
.stat-card:nth-child(3)::before { background: var(--accent3); }
.stat-card:nth-child(4)::before { background: var(--danger); }

.stat-number {
    font-family: var(--mono);
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.3rem;
}

.stat-label {
    font-size: 0.8rem;
    color: var(--text2);
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

/* TOOLBAR */
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}

.toolbar h2 {
    font-size: 1.3rem;
    font-weight: 800;
    letter-spacing: -0.02em;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-family: var(--sans);
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    border: none;
    transition: all 0.2s;
    letter-spacing: 0.02em;
}

.btn-primary {
    background: var(--accent);
    color: white;
}

.btn-primary:hover { background: #5a52e0; transform: translateY(-1px); }

.btn-success {
    background: var(--accent2);
    color: #0a0a0f;
}

.btn-success:hover { background: #00b085; }

.btn-warning {
    background: rgba(245,166,35,0.15);
    color: var(--accent3);
    border: 1px solid rgba(245,166,35,0.3);
}

.btn-warning:hover { background: rgba(245,166,35,0.25); }

.btn-danger {
    background: rgba(231,76,60,0.15);
    color: var(--danger);
    border: 1px solid rgba(231,76,60,0.3);
}

.btn-danger:hover { background: rgba(231,76,60,0.25); }

.btn-ghost {
    background: transparent;
    color: var(--text2);
    border: 1px solid var(--border);
}

.btn-ghost:hover { color: var(--text); border-color: var(--text2); }

/* TABLE */
.table-container {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}

table { width: 100%; border-collapse: collapse; }

thead {
    background: var(--surface2);
    border-bottom: 1px solid var(--border);
}

th {
    padding: 1rem 1.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text2);
}

td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
}

tr:last-child td { border-bottom: none; }

tr:hover td { background: rgba(108,99,255,0.03); }

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
}

.badge-bon { background: rgba(0,200,150,0.15); color: var(--accent2); }
.badge-maintenance { background: rgba(245,166,35,0.15); color: var(--accent3); }
.badge-defaillant { background: rgba(231,76,60,0.15); color: var(--danger); }

.actions { display: flex; gap: 0.5rem; }

/* FORM */
.form-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 2rem;
    max-width: 700px;
}

.form-title {
    font-size: 1.3rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.2rem;
    margin-bottom: 1.5rem;
}

.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group.full { grid-column: 1 / -1; }

label {
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text2);
}

input, select {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    font-family: var(--sans);
    font-size: 0.9rem;
    padding: 0.7rem 1rem;
    transition: border-color 0.2s;
    outline: none;
}

input:focus, select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(108,99,255,0.1);
}

select option { background: var(--surface2); }

.form-actions { display: flex; gap: 0.75rem; }

/* MESSAGE */
.message {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    animation: slideIn 0.3s ease;
}

.message-success {
    background: rgba(0,200,150,0.15);
    border: 1px solid rgba(0,200,150,0.3);
    color: var(--accent2);
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.type-icon { font-size: 1.1rem; margin-right: 0.3rem; }

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text2);
}

.empty-state h3 { font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--text); }

@media (max-width: 768px) {
    .stats { grid-template-columns: repeat(2, 1fr); }
    .form-grid { grid-template-columns: 1fr; }
    table { font-size: 0.8rem; }
    th, td { padding: 0.75rem 1rem; }
}
</style>
</head>
<body>

<header class="header">
    <div class="logo">⬡ Gest<span>Mat</span></div>
    <nav class="nav">
        <a href="?action=list" class="<?= $action === 'list' ? 'active' : '' ?>">📋 Inventaire</a>
        <a href="?action=add" class="<?= $action === 'add' ? 'active' : '' ?>">➕ Ajouter</a>
    </nav>
</header>

<main class="main">

<?php if ($message): 
    [$type, $text] = explode(':', $message, 2);
?>
<div class="message message-<?= $type ?>">✓ <?= $text ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>

<!-- STATS -->
<div class="stats">
    <div class="stat-card">
        <div class="stat-number"><?= $total ?></div>
        <div class="stat-label">Total Matériels</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color:var(--accent2)"><?= $bon ?></div>
        <div class="stat-label">En Bon État</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color:var(--accent3)"><?= $maintenance ?></div>
        <div class="stat-label">En Maintenance</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color:var(--danger)"><?= $defaillant ?></div>
        <div class="stat-label">Défaillants</div>
    </div>
</div>

<!-- TABLE -->
<div class="toolbar">
    <h2>Inventaire des Matériels</h2>
    <a href="?action=add" class="btn btn-primary">+ Ajouter Matériel</a>
</div>

<div class="table-container">
    <?php if (empty($_SESSION['materiels'])): ?>
    <div class="empty-state">
        <h3>Aucun matériel enregistré</h3>
        <p>Commencez par ajouter votre premier équipement.</p>
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Matériel</th>
                <th>Type</th>
                <th>N° Série</th>
                <th>État</th>
                <th>Utilisateur</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($_SESSION['materiels'] as $m): ?>
            <tr>
                <td><span style="font-family:var(--mono);color:var(--text2);font-size:0.8rem">#<?= str_pad($m['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
                <td><strong><?= $m['nom'] ?></strong></td>
                <td>
                    <?= $type_icons[$m['type']] ?? '📦' ?>
                    <?= $m['type'] ?>
                </td>
                <td><span style="font-family:var(--mono);font-size:0.8rem;color:var(--text2)"><?= $m['serie'] ?></span></td>
                <td>
                    <span class="badge badge-<?= strtolower(str_replace(['é','Défaillant'], ['e','defaillant'], $m['etat'])) ?>">
                        <?= $m['etat'] === 'Bon' ? '●' : ($m['etat'] === 'Maintenance' ? '◐' : '○') ?>
                        <?= $m['etat'] ?>
                    </span>
                </td>
                <td><?= $m['utilisateur'] ?></td>
                <td style="color:var(--text2);font-size:0.85rem"><?= $m['date'] ?></td>
                <td>
                    <div class="actions">
                        <a href="?action=edit&id=<?= $m['id'] ?>" class="btn btn-warning" style="padding:0.4rem 0.8rem;font-size:0.8rem">✏️</a>
                        <a href="?action=delete&id=<?= $m['id'] ?>" class="btn btn-danger" style="padding:0.4rem 0.8rem;font-size:0.8rem" onclick="return confirm('Supprimer ce matériel ?')">🗑️</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php elseif ($action === 'add'): ?>

<div class="form-card">
    <div class="form-title">➕ Ajouter un Matériel</div>
    <form method="POST">
        <div class="form-grid">
            <div class="form-group full">
                <label>Nom du Matériel *</label>
                <input type="text" name="nom" placeholder="ex: Laptop Dell XPS 15" required>
            </div>
            <div class="form-group">
                <label>Type *</label>
                <select name="type" required>
                    <option value="">Sélectionner...</option>
                    <option>Ordinateur</option>
                    <option>Téléphone</option>
                    <option>Imprimante</option>
                    <option>Réseau</option>
                    <option>Moniteur</option>
                    <option>Autre</option>
                </select>
            </div>
            <div class="form-group">
                <label>Numéro de Série *</label>
                <input type="text" name="serie" placeholder="ex: SN-2024-001" required>
            </div>
            <div class="form-group">
                <label>État *</label>
                <select name="etat" required>
                    <option value="">Sélectionner...</option>
                    <option>Bon</option>
                    <option>Maintenance</option>
                    <option>Défaillant</option>
                </select>
            </div>
            <div class="form-group">
                <label>Utilisateur / Localisation</label>
                <input type="text" name="utilisateur" placeholder="ex: Ahmed Hassan">
            </div>
            <div class="form-group full">
                <label>Date d'acquisition</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="add" class="btn btn-success">✓ Enregistrer</button>
            <a href="?action=list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>

<?php elseif ($action === 'edit' && $edit_item): ?>

<div class="form-card">
    <div class="form-title">✏️ Modifier — <?= $edit_item['nom'] ?></div>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
        <div class="form-grid">
            <div class="form-group full">
                <label>Nom du Matériel *</label>
                <input type="text" name="nom" value="<?= $edit_item['nom'] ?>" required>
            </div>
            <div class="form-group">
                <label>Type *</label>
                <select name="type" required>
                    <?php foreach (['Ordinateur','Téléphone','Imprimante','Réseau','Moniteur','Autre'] as $t): ?>
                    <option <?= $t === $edit_item['type'] ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Numéro de Série *</label>
                <input type="text" name="serie" value="<?= $edit_item['serie'] ?>" required>
            </div>
            <div class="form-group">
                <label>État *</label>
                <select name="etat" required>
                    <?php foreach (['Bon','Maintenance','Défaillant'] as $e): ?>
                    <option <?= $e === $edit_item['etat'] ? 'selected' : '' ?>><?= $e ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Utilisateur / Localisation</label>
                <input type="text" name="utilisateur" value="<?= $edit_item['utilisateur'] ?>">
            </div>
            <div class="form-group full">
                <label>Date d'acquisition</label>
                <input type="date" name="date" value="<?= $edit_item['date'] ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="edit" class="btn btn-primary">✓ Mettre à jour</button>
            <a href="?action=list" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>

<?php endif; ?>

</main>
</body>
</html>
