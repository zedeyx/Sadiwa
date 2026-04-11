<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">SADIWA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="map.php">Interactive Map</a>
                </li>
    			<li class="nav-item">
                    <a class="nav-link" href="risk_analysis.php">Risk Analysis</a>
                </li>
               
                <li class="nav-item">
                    <a class="nav-link" href="churches.php">Churches</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">Reports</a>
                
                <?php if (hasRole('Contributor')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="submit_report.php">Submit Report</a>
                </li>
                <?php endif; ?>
                <?php if (hasRole('Admin')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                        Admin
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="manage_churches.php">Manage Churches</a></li>
                        <li><a class="dropdown-item" href="approve_reports.php">Approve Reports</a></li>
                    </ul>
                </li>

                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="navbar-text me-3 fs-4">
                        Welcome, 
                        <strong class="text-info">
                          <strong class="text-info">
                                <span class="navbar-text me-3 fs-5">

                                    <!-- USERNAME -->
                                    <strong class="text-info">
                                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                                    </strong>

                                    <!-- ROLE -->
                                    <span class="badge bg-secondary ms-1">
                                        <?php echo htmlspecialchars($_SESSION['role']); ?>
                                    </span>

                                </span>
                            </strong>
                        </strong>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>