// Excel Upload Functions
function selectExcelFile(event) {
    event.stopPropagation();
    document.getElementById('excelFileInput').click();
}

function toggleManualEntry(event) {
    event.stopPropagation();
    const form = document.getElementById('manualEntryForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function saveManualEntry() {
    const name = document.getElementById('manualName').value.trim();
    const number = document.getElementById('manualNumber').value.trim();
    const platform = document.getElementById('platformSelect').value;
    const role = document.getElementById('roleSelect').value;

    console.log('Manual entry data:', { name, number, platform, role });

    if (!name || !number) {
        showStatusPopup('warning', 'Warning', 'Please fill in both name and number');
        return;
    }

    if (!role) {
        showStatusPopup('warning', 'Warning', 'Please select a role');
        return;
    }

    // Validate number format (10 digits)
    if (!/^[0-9]{10}$/.test(number)) {
        showStatusPopup('warning', 'Warning', 'Please enter a valid 10-digit mobile number');
        return;
    }

    // Send data to server
    fetch('/save-manual-lead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            name: name,
            number: number,
            platform: platform,
            role: role
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showStatusPopup('success', 'Success', 'Lead saved successfully!');
            document.getElementById('manualName').value = '';
            document.getElementById('manualNumber').value = '';
            document.getElementById('platformSelect').value = '';
            document.getElementById('roleSelect').value = '';
            document.getElementById('manualEntryForm').style.display = 'none';
        } else {
            // Show specific error message for duplicates
            if (data.message.includes('already exists')) {
                showStatusPopup('error', 'Error', 'Duplicate Number: ' + data.message);
            } else {
                showStatusPopup('error', 'Error', data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatusPopup('error', 'Error', 'Error saving lead');
    });
}

// Real-time duplicate check for manual entry
function checkDuplicateNumber(number) {
    if (number.length === 10) {
        fetch('/check-duplicate-number', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ number: number })
        })
        .then(response => response.json())
        .then(data => {
            const numberInput = document.getElementById('manualNumber');
            if (data.exists) {
                numberInput.style.borderColor = '#dc3545';
                numberInput.style.backgroundColor = '#f8d7da';
                numberInput.title = 'This number already exists in database';
            } else {
                numberInput.style.borderColor = '#28a745';
                numberInput.style.backgroundColor = '#d4edda';
                numberInput.title = 'Number is available';
            }
        })
        .catch(error => {
            console.error('Error checking duplicate:', error);
        });
    }
}

// File input change handler
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excelFileInput');
    const uploadProgress = document.getElementById('uploadProgress');
    const uploadText = document.getElementById('uploadText');
    const progressFill = document.querySelector('.progress-fill');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const platform = document.getElementById('platformSelect').value;
            const role = document.getElementById('roleSelect').value;

            console.log('File upload data:', { 
                fileName: file.name, 
                fileSize: file.size, 
                platform: platform, 
                role: role 
            });

            // Validate role selection
            if (!role) {
                showStatusPopup('warning', 'Warning', 'Please select a role before uploading file');
                return;
            }

            // Validate file type
            const allowedTypes = ['.xlsx', '.xls', '.csv'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(fileExtension)) {
                showStatusPopup('error', 'Error', 'Please select a valid Excel file (.xlsx, .xls, .csv)');
                return;
            }

            // Show upload progress
            uploadProgress.style.display = 'block';
            uploadText.textContent = 'Uploading ' + file.name + '...';
            progressFill.style.width = '0%';

            // Create form data
            const formData = new FormData();
            formData.append('excel_file', file);
            formData.append('platform', platform);
            formData.append('role', role);

            console.log('Sending request to /admin/leads/upload');

            // Upload file
            fetch('/admin/leads/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                console.log('Upload response status:', response.status);
                progressFill.style.width = '100%';
                return response.json();
            })
            .then(data => {
                console.log('Upload response data:', data);
                setTimeout(() => {
                    uploadProgress.style.display = 'none';
                    if (data.success) {
                        // Show detailed success message including duplicates info
                        showStatusPopup('success', 'Success', data.message);
                        location.reload(); // Refresh page to show new leads
                    } else {
                        showStatusPopup('error', 'Error', data.message);
                    }
                }, 500);
            })
            .catch(error => {
                console.error('Upload error:', error);
                uploadProgress.style.display = 'none';
                showStatusPopup('error', 'Error', 'Error uploading file');
            });

            // Reset file input
            fileInput.value = '';
        });
    }

    // Real-time number validation
    const manualNumberInput = document.getElementById('manualNumber');
    if (manualNumberInput) {
        manualNumberInput.addEventListener('input', function() {
            const number = this.value;
            if (number.length === 10) {
                checkDuplicateNumber(number);
            } else {
                this.style.borderColor = '';
                this.style.backgroundColor = '';
                this.title = '';
            }
        });
    }
});