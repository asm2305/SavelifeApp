// Main application JavaScript
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Handle donor registration form
    $('#donorForm').submit(function(e) {
        if (!validateDonorForm()) {
            e.preventDefault();
            return false;
        }
    });
    
    // Handle patient search form
    $('#patientSearchForm').submit(function(e) {
        // Add any client-side validation if needed
    });
    
    // Handle center status updates
    $('.center-status-toggle').change(function() {
        const centerId = $(this).data('center-id');
        const needsBlood = $(this).is(':checked') ? 1 : 0;
        const bloodTypes = needsBlood ? $('#bloodTypes-' + centerId).val() : '';
        
        updateCenterStatus(centerId, needsBlood, bloodTypes);
    });
    
    // Initialize map if present
    if (typeof initMap === 'function') {
        initMap();
    }
});

// Validate donor registration form
function validateDonorForm() {
    let isValid = true;
    
    // Validate phone number
    const phone = $('#contactNumber').val();
    if (!/^[0-9]{10,15}$/.test(phone)) {
        alert('Phone number must contain 10-15 digits only');
        isValid = false;
    }
    
    // Validate email if provided
    const email = $('#email').val();
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('Please enter a valid email address');
        isValid = false;
    }
    
    // Validate terms agreement
    if (!$('#terms').is(':checked')) {
        alert('You must agree to the terms and conditions');
        isValid = false;
    }
    
    return isValid;
}

// Update center status via AJAX
function updateCenterStatus(centerId, needsBlood, bloodTypes) {
    $.ajax({
        url: '../api/update_center_status.php',
        method: 'POST',
        data: {
            center_id: centerId,
            needs_blood: needsBlood,
            blood_types: bloodTypes
        },
        success: function(response) {
            if (response.success) {
                // Update UI to reflect new status
                const row = $('#center-' + centerId);
                if (needsBlood) {
                    row.removeClass('table-success').addClass('table-danger');
                    row.find('.status-badge').removeClass('bg-success').addClass('bg-danger').text('Needs Blood');
                } else {
                    row.removeClass('table-danger').addClass('table-success');
                    row.find('.status-badge').removeClass('bg-danger').addClass('bg-success').text('Adequate Supply');
                }
                
                // Show success message
                showAlert('Center status updated successfully', 'success');
            } else {
                showAlert('Error updating center status: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Error updating center status: ' + error, 'danger');
        }
    });
}

// Show alert message
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('#alerts-container').html(alertHtml);
}

// Find nearby centers using geolocation
function findNearbyCenters() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                $.ajax({
                    url: '../api/get_nearby_centers.php',
                    method: 'POST',
                    data: {
                        lat: lat,
                        lng: lng,
                        radius: 20 // kilometers
                    },
                    success: function(response) {
                        if (response.success) {
                            displayNearbyCenters(response.centers);
                        } else {
                            showAlert('Error finding nearby centers: ' + response.message, 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('Error finding nearby centers: ' + error, 'danger');
                    }
                });
            },
            function(error) {
                showAlert('Error getting your location: ' + error.message, 'danger');
            }
        );
    } else {
        showAlert('Geolocation is not supported by your browser', 'danger');
    }
}

// Display nearby centers on the page
function displayNearbyCenters(centers) {
    if (centers.length === 0) {
        $('#nearby-centers-container').html('<p>No nearby centers found within the specified radius.</p>');
        return;
    }
    
    let html = '<div class="list-group">';
    
    centers.forEach(center => {
        const statusClass = center.needs_blood ? 'list-group-item-danger' : 'list-group-item-success';
        const statusText = center.needs_blood ? 'Needs Blood' : 'Adequate Supply';
        
        html += `
            <a href="../centers/map.php?center_id=${center.id}" class="list-group-item ${statusClass}">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>${center.name}</strong>
                    <span>${center.distance} km</span>
                </div>
                <small class="text-muted">${center.location}</small>
                <div class="mt-2">
                    <span class="badge ${center.needs_blood ? 'bg-danger' : 'bg-success'}">
                        ${statusText}
                    </span>
                </div>
            </a>
        `;
    });
    
    html += '</div>';
    $('#nearby-centers-container').html(html);
}
