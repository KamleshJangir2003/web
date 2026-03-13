/* MANUAL ENTRY */
function toggleManualEntry(e) {
    e.stopPropagation();
    const form = document.getElementById('manualEntryForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function saveManualEntry() {
    const name = document.getElementById('manualName').value.trim();
    const number = document.getElementById('manualNumber').value.trim();
    const role = document.getElementById('roleSelect').value;
    const platform = document.getElementById('platformSelect').value;

    if (!name || !number) {
        showStatusPopup('warning', 'Warning', 'Please enter both name and number!');
        return;
    }
    if (!/^[0-9]{10}$/.test(number)) {
        showStatusPopup('warning', 'Warning', 'Mobile number must be exactly 10 digits!');
        return;
    }
    if (!role) {
        showStatusPopup('warning', 'Warning', 'Please select role first!');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('number', number);
    formData.append('role', role);
    formData.append('platform', platform);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('/save-manual-lead', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatusPopup('success', 'Success', 'Lead saved successfully!');
            document.getElementById('manualName').value = '';
            document.getElementById('manualNumber').value = '';
            document.getElementById('manualEntryForm').style.display = 'none';
        } else {
            showStatusPopup('error', 'Error', 'Error saving lead!');
        }
    })
    .catch(() => showStatusPopup('error', 'Error', 'Network error!'));
}
