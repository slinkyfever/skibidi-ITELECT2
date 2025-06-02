document.addEventListener('DOMContentLoaded', () => {
    const supplierForm = document.getElementById('supplierForm');
    const governmentForm = document.getElementById('governmentForm');

    if (supplierForm) {
        supplierForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitForm('supplier');
        });
    }

    if (governmentForm) {
        governmentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitForm('government');
        });
    }
});

function submitForm(role) {
    let formData;
    let url = '';

    if (role === 'supplier') {
        formData = new FormData(document.getElementById('supplierForm'));
        url = 'submit_supplier.php';
    } else if (role === 'government') {
        formData = new FormData(document.getElementById('governmentForm'));
        url = 'submit_government.php';
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === 'submitted') {
            alert('Form submitted successfully! Please wait for admin approval.');
            window.location.href = 'login.php';
        } else {
            alert('' + data);
        }
    });
}

