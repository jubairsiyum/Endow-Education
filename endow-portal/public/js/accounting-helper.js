/**
 * Accounting Module - JavaScript Helper Functions
 * 
 * This file contains reusable JavaScript functions for the accounting module.
 * Include this file in your main layout or specific accounting views.
 * 
 * Dependencies: jQuery, Bootstrap 5 (or Bootstrap 4)
 */

// Transaction Type Handler
function handleTransactionTypeChange() {
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category_id');
    const incomeFields = document.getElementById('income-fields');
    const incomeCategories = document.getElementById('income-categories');
    const expenseCategories = document.getElementById('expense-categories');
    const studentNameInput = document.getElementById('student_name');
    const paymentMethodInput = document.getElementById('payment_method');

    if (!typeSelect) return;

    typeSelect.addEventListener('change', function() {
        const type = this.value;
        
        // Reset category selection
        categorySelect.value = '';
        
        if (type === 'income') {
            // Show income-specific fields and categories
            if (incomeFields) incomeFields.style.display = 'block';
            if (incomeCategories) incomeCategories.style.display = 'block';
            if (expenseCategories) expenseCategories.style.display = 'none';
            
            // Make income fields required
            if (studentNameInput) studentNameInput.required = true;
            if (paymentMethodInput) paymentMethodInput.required = true;
        } else if (type === 'expense') {
            // Hide income-specific fields and show expense categories
            if (incomeFields) incomeFields.style.display = 'none';
            if (incomeCategories) incomeCategories.style.display = 'none';
            if (expenseCategories) expenseCategories.style.display = 'block';
            
            // Remove required attribute and clear values
            if (studentNameInput) {
                studentNameInput.required = false;
                studentNameInput.value = '';
            }
            if (paymentMethodInput) {
                paymentMethodInput.required = false;
                paymentMethodInput.value = '';
            }
        } else {
            // No type selected - hide all
            if (incomeFields) incomeFields.style.display = 'none';
            if (incomeCategories) incomeCategories.style.display = 'none';
            if (expenseCategories) expenseCategories.style.display = 'none';
            
            if (studentNameInput) studentNameInput.required = false;
            if (paymentMethodInput) paymentMethodInput.required = false;
        }
    });

    // Trigger on page load to handle old() values
    typeSelect.dispatchEvent(new Event('change'));
}

// Amount Formatter
function formatAmountInput() {
    const amountInput = document.getElementById('amount');
    if (!amountInput) return;

    amountInput.addEventListener('blur', function() {
        const value = parseFloat(this.value);
        if (!isNaN(value)) {
            this.value = value.toFixed(2);
        }
    });
}

// Confirmation Dialog for Approve/Reject
function confirmAction(action, transactionId, amount) {
    const messages = {
        approve: `Are you sure you want to approve this transaction of ${amount}?`,
        reject: 'Please provide a rejection reason in the modal.',
        delete: `Are you sure you want to delete this transaction? This action cannot be undone.`
    };

    return confirm(messages[action] || 'Are you sure?');
}

// Quick Summary Calculator (Client-side for preview)
function calculateQuickSummary(transactions) {
    let totalIncome = 0;
    let totalExpense = 0;

    transactions.forEach(transaction => {
        if (transaction.status === 'approved') {
            if (transaction.type === 'income') {
                totalIncome += parseFloat(transaction.amount);
            } else if (transaction.type === 'expense') {
                totalExpense += parseFloat(transaction.amount);
            }
        }
    });

    const netProfit = totalIncome - totalExpense;
    const profitMargin = totalIncome > 0 ? (netProfit / totalIncome) * 100 : 0;

    return {
        totalIncome: totalIncome.toFixed(2),
        totalExpense: totalExpense.toFixed(2),
        netProfit: netProfit.toFixed(2),
        profitMargin: profitMargin.toFixed(1)
    };
}

// Date Range Validator
function validateDateRange() {
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');

    if (!startDate || !endDate) return;

    endDate.addEventListener('change', function() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);

            if (end < start) {
                alert('End date must be after start date');
                this.value = '';
            }
        }
    });
}

// Export to CSV (Client-side)
function exportTableToCSV(tableId, filename = 'transactions.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tr');
    const csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(col => {
            // Remove action buttons and badges HTML
            let text = col.textContent.trim();
            // Escape quotes
            text = text.replace(/"/g, '""');
            rowData.push(`"${text}"`);
        });
        
        csv.push(rowData.join(','));
    });

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Print Function
function printSummary() {
    window.print();
}

// AJAX Transaction Approval (Optional - if you want to avoid page reload)
function ajaxApproveTransaction(transactionId) {
    if (!confirm('Are you sure you want to approve this transaction?')) {
        return;
    }

    fetch(`/accounting/transactions/${transactionId}/approve`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Transaction approved successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving the transaction.');
    });
}

// AJAX Transaction Rejection (Optional)
function ajaxRejectTransaction(transactionId, reason) {
    if (!reason || reason.trim() === '') {
        alert('Please provide a rejection reason.');
        return;
    }

    fetch(`/accounting/transactions/${transactionId}/reject`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ rejection_reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Transaction rejected successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rejecting the transaction.');
    });
}

// Initialize Bootstrap Tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Auto-refresh Pending Count (Optional - for real-time updates)
function autoRefreshPendingCount() {
    setInterval(() => {
        fetch('/accounting/api/pending-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.pending-count-badge');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else if (badge) {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching pending count:', error));
    }, 60000); // Refresh every 60 seconds
}

// Initialize all functions on page load
document.addEventListener('DOMContentLoaded', function() {
    handleTransactionTypeChange();
    formatAmountInput();
    validateDateRange();
    initTooltips();
    
    // Uncomment if you want auto-refresh
    // autoRefreshPendingCount();
});

// jQuery-based functions (if you're using jQuery)
if (typeof jQuery !== 'undefined') {
    (function($) {
        // DataTables initialization (if using DataTables)
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('.transactions-table').DataTable({
                    order: [[0, 'desc']], // Sort by date descending
                    pageLength: 20,
                    responsive: true,
                    language: {
                        search: "Filter:",
                        lengthMenu: "Show _MENU_ entries per page"
                    }
                });
            }
        });

        // Select2 initialization (for better category dropdowns)
        $(document).ready(function() {
            if ($.fn.select2) {
                $('#category_id').select2({
                    placeholder: 'Select Category',
                    allowClear: true
                });

                $('#payment_method').select2({
                    placeholder: 'Select Payment Method',
                    allowClear: true
                });
            }
        });

        // Date Range Picker (if using daterangepicker plugin)
        $(document).ready(function() {
            if ($.fn.daterangepicker) {
                $('input[name="daterange"]').daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
            }
        });
    })(jQuery);
}

// Export functions for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        handleTransactionTypeChange,
        formatAmountInput,
        confirmAction,
        calculateQuickSummary,
        validateDateRange,
        exportTableToCSV,
        printSummary,
        ajaxApproveTransaction,
        ajaxRejectTransaction,
        initTooltips,
        autoRefreshPendingCount
    };
}
