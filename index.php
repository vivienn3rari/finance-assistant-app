<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDV FinanceApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const getPreferredTheme = () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                return savedTheme;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };
        document.documentElement.setAttribute('data-bs-theme', getPreferredTheme());
    </script>

    <style>
        body { font-family: 'Poppins', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; background-color: #f4f7f6; color: #2b3452; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease; }
        .card:hover { box-shadow: 0 15px 50px rgba(0,0,0,0.08); }
        .navbar-custom { background-color: #ffffff; box-shadow: 0 2px 20px rgba(0,0,0,0.03); border-bottom: 1px solid #edf2f7; }
        .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #e2e8f0; background-color: #f8fafc; transition: all 0.3s ease; }
        .form-control:focus, .form-select:focus { box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15); border-color: #667eea; background-color: #fff; }
        .table-hover tbody tr { transition: all 0.2s; vertical-align: middle; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; transform: scale(1.01); }
        .custom-brand-text { color: #2b3452; }
        
        [data-bs-theme="dark"] body { background-color: #121212; color: #e0e0e0; }
        [data-bs-theme="dark"] .card { background-color: #1e1e1e; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        [data-bs-theme="dark"] .card:hover { box-shadow: 0 15px 50px rgba(0,0,0,0.3); }
        [data-bs-theme="dark"] .navbar-custom { background-color: #1e1e1e; border-bottom: 1px solid #2d2d2d; }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select { background-color: #2b2b2b; border-color: #3d3d3d; color: #e0e0e0; }
        [data-bs-theme="dark"] .form-control:focus, [data-bs-theme="dark"] .form-select:focus { background-color: #2b2b2b; border-color: #667eea; }
        [data-bs-theme="dark"] .input-group-text { background-color: #2b2b2b !important; border-color: #3d3d3d !important; color: #a0aab2;}
        [data-bs-theme="dark"] .table-hover tbody tr:hover { background-color: #2a2a2a; }
        [data-bs-theme="dark"] .text-muted { color: #a0aab2 !important; }
        [data-bs-theme="dark"] .custom-brand-text { color: #e0e0e0; }
        [data-bs-theme="dark"] .bg-white { background-color: #2b2b2b !important; border-color: #3d3d3d !important; }
        [data-bs-theme="dark"] .btn-light { background-color: #2b2b2b; border-color: #3d3d3d; color: #e0e0e0; }
        [data-bs-theme="dark"] .btn-light:hover { background-color: #3d3d3d; color: #fff; }

        .balance-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 12px; padding: 10px 20px; font-weight: 500; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3); }
        .btn-outline-danger { border-radius: 8px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom mb-5 py-3">
        <div class="container">
            <a class="navbar-brand fw-bold custom-brand-text" href="#">
                <i class="bi bi-wallet2 text-primary me-2"></i> CDV FinanceApp
            </a>
            <div class="d-flex align-items-center">
                <span class="me-4 text-muted d-none d-md-inline">Witaj, <b class="custom-brand-text"><?= htmlspecialchars($_SESSION['username']) ?></b></span>
                <button id="themeToggleBtn" class="btn btn-light rounded-circle shadow-sm me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>
                <a href="logout.php" class="btn btn-light rounded-pill border shadow-sm px-4">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Wyloguj</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card p-4 mb-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle me-2 text-primary"></i>Nowa operacja</h5>
                    <form id="financeForm">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Typ transakcji</label>
                            <select class="form-select" id="type" required>
                                <option value="income">💰 Dochód</option>
                                <option value="expense">🛒 Wydatek</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Kwota (PLN)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4"><i class="bi bi-cash"></i></span>
                                <input type="number" step="0.01" class="form-control border-start-0 rounded-end-4" id="amount" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Kategoria</label>
                            <input type="text" class="form-control" id="category" placeholder="Np. Jedzenie, Czynsz..." required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">Data</label>
                            <input type="date" class="form-control" id="date" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-save me-2"></i> Zapisz operację</button>
                    </form>
                </div>

                <div class="card p-4">
                    <h5 class="fw-bold text-center mb-4"><i class="bi bi-pie-chart me-2 text-primary"></i>Struktura wydatków</h5>
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card balance-card p-4 mb-4 d-flex flex-row justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Dostępne środki</h6>
                        <h1 class="fw-bold m-0" style="letter-spacing: -1px;"><span id="totalBalance">0.00</span> PLN</h1>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="bi bi-credit-card-fill"></i>
                    </div>
                </div>
                
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0"><i class="bi bi-card-list me-2 text-primary"></i>Historia operacji</h5>
                        <select id="filterType" class="form-select shadow-sm" style="border-radius: 8px; min-width: 140px; cursor: pointer;" onchange="loadTransactions()">
                            <option value="all">Wszystkie</option>
                            <option value="income">Zyski</option>
                            <option value="expense">Wydatki</option>
                        </select>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless">
                            <thead class="text-muted small border-bottom">
                                <tr>
                                    <th class="py-3">DATA</th>
                                    <th class="py-3">TYP</th>
                                    <th class="py-3">KATEGORIA</th>
                                    <th class="py-3">KWOTA</th>
                                    <th class="py-3 text-end">AKCJA</th>
                                </tr>
                            </thead>
                            <tbody id="transactionList">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let expenseChartInstance = null;

        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');

        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill text-warning';
            } else {
                themeIcon.className = 'bi bi-moon-fill';
            }
            
            if (expenseChartInstance !== null) {
                loadTransactions(); 
            }
        };

        setTheme(document.documentElement.getAttribute('data-bs-theme') || 'light');

        themeToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });

        function loadTransactions() {
            fetch('get.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalBalance').innerText = data.balance.toFixed(2);
                
                const tbody = document.getElementById('transactionList');
                tbody.innerHTML = ''; 
                const filterValue = document.getElementById('filterType').value;

                let expensesData = {}; 

                if(data.transactions.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Brak operacji. Dodaj swoją pierwszą transakcję.</td></tr>';
                }

                data.transactions.forEach(t => {
                    if (t.type === 'expense') {
                        expensesData[t.category] = (expensesData[t.category] || 0) + parseFloat(t.amount);
                    }

                    if (filterValue !== 'all' && t.type !== filterValue) return;

                    const isIncome = t.type === 'income';
                    
                    const typeBadge = isIncome 
                        ? '<span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-arrow-up-circle-fill me-1"></i> Dochód</span>' 
                        : '<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill"><i class="bi bi-arrow-down-circle-fill me-1"></i> Wydatek</span>';
                    
                    const amountStyle = isIncome ? 'text-success' : 'custom-brand-text'; 
                    const sign = isIncome ? '+' : '-';

                    const row = `<tr>
                        <td class="text-muted py-3">${t.date}</td>
                        <td class="py-3">${typeBadge}</td>
                        <td class="fw-medium py-3"><i class="bi bi-tag text-muted me-2"></i>${t.category}</td>
                        <td class="fw-bold fs-6 py-3 ${amountStyle}">${sign}${t.amount} zł</td>
                        <td class="py-3 text-end">
                            <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteTransaction(${t.id})" title="Usuń">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>`;
                    
                    tbody.innerHTML += row;
                });

                updateChart(expensesData);
            })
            .catch(error => console.error('Błąd:', error));
        }

        function updateChart(data) {
            const ctx = document.getElementById('expenseChart').getContext('2d');
            
            if (expenseChartInstance) {
                expenseChartInstance.destroy();
            }

            const categories = Object.keys(data);
            const amounts = Object.values(data);
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

            expenseChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categories.length > 0 ? categories : ['Brak danych'],
                    datasets: [{
                        data: amounts.length > 0 ? amounts : [1],
                        backgroundColor: amounts.length > 0 ? [
                            '#667eea', '#764ba2', '#ff6b6b', '#feca57', '#48dbfb', '#1dd1a1'
                        ] : (isDark ? ['#2b2b2b'] : ['#edf2f7']),
                        borderWidth: isDark ? 2 : 0,
                        borderColor: isDark ? '#1e1e1e' : '#fff',
                        hoverOffset: 10
                    }]
                },
                options: { 
                    responsive: true,
                    cutout: '75%', 
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                usePointStyle: true, 
                                padding: 20,
                                color: isDark ? '#e0e0e0' : '#2b3452' 
                            } 
                        }
                    }
                }
            });
        }

        function deleteTransaction(id) {
            if(confirm('Czy na pewno chcesz usunąć?')) {
                let formData = new FormData();
                formData.append('id', id);

                fetch('delete.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') loadTransactions(); 
                    else alert('Błąd: ' + data.message);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', loadTransactions);

        document.getElementById('financeForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            let submitBtn = this.querySelector('button[type="submit"]');
            let originalContent = submitBtn.innerHTML;
            submitBtn.disabled = true; 
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Zapisywanie...';

            let formData = new FormData();
            formData.append('type', document.getElementById('type').value);
            formData.append('amount', document.getElementById('amount').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('date', document.getElementById('date').value);

            fetch('add.php', { method: 'POST', body: formData })
            .then(response => response.json()) 
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('financeForm').reset(); 
                    loadTransactions(); 
                } else alert('Błąd: ' + data.message);
            })
            .catch(error => console.error('Błąd:', error))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            });
        });
    </script>
</body>
</html>