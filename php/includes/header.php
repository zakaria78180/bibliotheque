<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Gestion de Bibliothèque</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        
        .navbar {
            background-color: #1e40af;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }
        
        .navbar-nav a {
            color: #bfdbfe;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        
        .navbar-nav a:hover,
        .navbar-nav a.active {
            background-color: #1e3a8a;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        
        .btn-secondary {
            background-color: #64748b;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #475569;
        }
        
        .btn-success {
            background-color: #16a34a;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #15803d;
        }
        
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #b91c1c;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        tr:hover {
            background-color: #f8fafc;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.375rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.875rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        select.form-control {
            cursor: pointer;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-form {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .search-form input {
            flex: 1;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: #64748b;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-help {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        
        .form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .required {
            color: #dc2626;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .checkbox-label input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            cursor: pointer;
        }
        
        .info-box {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .info-box h3 {
            color: #1e40af;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }
        
        .info-box p {
            color: #1e3a8a;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .info-box ul {
            margin-left: 1.25rem;
            color: #1e3a8a;
            font-size: 0.875rem;
        }
        
        .info-box li {
            margin-bottom: 0.25rem;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .badge-secondary {
            background-color: #e2e8f0;
            color: #475569;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #64748b;
        }
        
        .empty-state svg {
            margin-bottom: 1rem;
            color: #94a3b8;
        }
        
        .empty-state h3 {
            font-size: 1.125rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            margin-bottom: 1.5rem;
        }
        
        .subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .card-body {
            padding: 0;
        }
        
        /* Dropdown Menu */
        .nav-dropdown {
            position: relative;
        }
        
        .dropdown-toggle {
            cursor: pointer;
        }
        
        .dropdown-toggle::after {
            content: ' ▼';
            font-size: 0.6rem;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 0.375rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 160px;
            z-index: 1000;
            list-style: none;
            padding: 0.5rem 0;
        }
        
        .nav-dropdown:hover .dropdown-menu {
            display: block;
        }
        
        .dropdown-menu li a {
            display: block;
            padding: 0.5rem 1rem;
            color: #1e293b;
            text-decoration: none;
            transition: background 0.2s;
        }
        
        .dropdown-menu li a:hover {
            background: #f1f5f9;
            color: #1e40af;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .navbar-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .navbar-nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .page-header {
                flex-direction: column;
            }
            
            .dropdown-menu {
                position: static;
                box-shadow: none;
                background: #1e3a8a;
            }
            
            .dropdown-menu li a {
                color: #bfdbfe;
            }
            
            .dropdown-menu li a:hover {
                background: #1e40af;
                color: white;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">Bibliothèque</a>
            <ul class="navbar-nav">
                <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Accueil</a></li>
                <li><a href="livres.php" <?php echo in_array(basename($_SERVER['PHP_SELF']), ['livres.php', 'ajouter_livre.php', 'modifier_livre.php']) ? 'class="active"' : ''; ?>>Livres</a></li>
                <li><a href="utilisateurs.php" <?php echo basename($_SERVER['PHP_SELF']) == 'utilisateurs.php' ? 'class="active"' : ''; ?>>Utilisateurs</a></li>
                <li><a href="emprunts.php" <?php echo basename($_SERVER['PHP_SELF']) == 'emprunts.php' ? 'class="active"' : ''; ?>>Emprunts</a></li>
                <li><a href="recherche.php" <?php echo basename($_SERVER['PHP_SELF']) == 'recherche.php' ? 'class="active"' : ''; ?>>Recherche</a></li>
                <li class="nav-dropdown">
                    <a href="#" class="dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['documentation.php', 'tests.php', 'rgpd.php']) ? 'active' : ''; ?>">BTS SIO</a>
                    <ul class="dropdown-menu">
                        <li><a href="documentation.php">Documentation</a></li>
                        <li><a href="tests.php">Tests</a></li>
                        <li><a href="rgpd.php">RGPD</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
