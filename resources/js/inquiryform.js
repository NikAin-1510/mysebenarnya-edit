document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('complaintForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch("/DetailsOwnInquiry/store", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert('Complaint submitted successfully.');
                form.reset();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting the complaint.');
            });
        });
    }
});
