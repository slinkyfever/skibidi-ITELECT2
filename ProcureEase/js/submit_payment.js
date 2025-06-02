
document.getElementById('paymentForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  fetch('submit_payment.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        closeModal('paymentModal');
        form.reset();
        document.getElementById('gcashInfo').classList.add('hidden');
        document.getElementById('paypalInfo').classList.add('hidden');
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(err => {
      console.error('Submission failed:', err);
      alert('Submission failed. Please try again.');
    });
});

