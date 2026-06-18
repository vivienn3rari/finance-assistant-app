let expenseChartInstance = null;

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

    expenseChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories.length > 0 ? categories : ['Brak danych'],
            datasets: [{
                data: amounts.length > 0 ? amounts : [1],
                backgroundColor: amounts.length > 0 ? [
                    '#0d6efd', '#6f42c1', '#d63384', '#fd7e14', '#0dcaf0', '#20c997'
                ] : ['#e9ecef'],
                borderWidth: 0,
                hoverOffset: 5
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
                        color: '#212529' 
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