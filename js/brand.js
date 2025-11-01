$(document).ready(function () {
    const $categorySelect = $('#brand_category');
    const $accordion = $('#brand-accordion');
    const $emptyState = $('#brand-empty-state');
    const editModalElement = document.getElementById('editBrandModal');
    const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
    let searchTimer = null;

    function handleAjaxError(xhr) {
        const message = xhr.responseJSON && xhr.responseJSON.message
            ? xhr.responseJSON.message
            : 'An unexpected error occurred. Please try again.';
        Swal.fire('Error', message, 'error');
    }

    function loadCategories() {
        return $.ajax({
            url: '../actions/fetch_category_action.php',
            method: 'POST',
            dataType: 'json'
        }).done(function (response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load categories', 'error');
                return;
            }

            const options = ['<option value="" disabled selected>Select category</option>'];
            response.data.forEach(function (category) {
                options.push(`<option value="${category.cat_id}">${category.cat_name}</option>`);
            });

            $categorySelect.html(options.join(''));
        }).fail(handleAjaxError);
    }

    function renderBrands(groups) {
        $accordion.find('.accordion-item').remove();

        const hasBrands = groups.some(group => (group.brands || []).length > 0);
        $emptyState.toggle(!hasBrands);

        groups.forEach(function (group, index) {
            const groupId = `category-${group.category_id}`;
            const collapseId = `collapse-${group.category_id}`;
            const brands = group.brands || [];

            let brandMarkup = '';

            if (brands.length === 0) {
                brandMarkup = '<p class="text-muted fst-italic mb-0">No brands yet for this category.</p>';
            } else {
                brandMarkup = '<div class="list-group list-group-flush">';
                brands.forEach(function (brand) {
                    brandMarkup += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold">${brand.brand_name}</span>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary btn-edit-brand"
                                    data-brand-id="${brand.brand_id}"
                                    data-brand-name="${brand.brand_name}"
                                    data-category-name="${group.category_name}">
                                    Edit
                                </button>
                                <button class="btn btn-outline-danger btn-delete-brand"
                                    data-brand-id="${brand.brand_id}"
                                    data-brand-name="${brand.brand_name}">
                                    Delete
                                </button>
                            </div>
                        </div>`;
                });
                brandMarkup += '</div>';
            }

            const isOpen = brands.length > 0 && hasBrands ? 'show' : '';

            const item = `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="${groupId}">
                        <button class="accordion-button ${isOpen ? '' : 'collapsed'}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${isOpen ? 'true' : 'false'}"
                            aria-controls="${collapseId}">
                            ${group.category_name}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse ${isOpen}" aria-labelledby="${groupId}">
                        <div class="accordion-body">
                            ${brandMarkup}
                        </div>
                    </div>
                </div>`;

            $accordion.append(item);
        });
    }

    function loadBrands(searchTerm = '') {
        const request = $.ajax({
            url: '../actions/fetch_brand_action.php',
            method: 'GET',
            data: searchTerm ? { search: searchTerm } : {},
            dataType: 'json'
        });

        request.done(function (response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Failed to load brands', 'error');
                return;
            }

            renderBrands(response.data || []);
        }).fail(handleAjaxError);

        return request;
    }

    $('#brand-form').on('submit', function (event) {
        event.preventDefault();

        const formData = {
            name: $('#brand_name').val().trim(),
            category_id: $('#brand_category').val()
        };

        if (!formData.name || !formData.category_id) {
            Swal.fire('Error', 'Brand name and category are required', 'error');
            return;
        }

        $.post('../actions/add_brand_action.php', formData, function (response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to add brand', 'error');
                return;
            }

            Swal.fire('Success', response.message, 'success');
            $('#brand-form')[0].reset();
            loadBrands($('#brand_search').val().trim());
        }, 'json').fail(handleAjaxError);
    });

    $(document).on('click', '.btn-edit-brand', function () {
        const button = $(this);
        $('#edit_brand_id').val(button.data('brand-id'));
        $('#edit_brand_name').val(button.data('brand-name'));
        $('#edit_brand_category').val(button.data('category-name'));

        if (editModal) {
            editModal.show();
        }
    });

    $('#edit-brand-form').on('submit', function (event) {
        event.preventDefault();

        const payload = {
            brand_id: $('#edit_brand_id').val(),
            name: $('#edit_brand_name').val().trim()
        };

        if (!payload.brand_id || !payload.name) {
            Swal.fire('Error', 'Brand name is required', 'error');
            return;
        }

        $.post('../actions/update_brand_action.php', payload, function (response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to update brand', 'error');
                return;
            }

            Swal.fire('Success', response.message, 'success');
            if (editModal) {
                editModal.hide();
            }
            loadBrands($('#brand_search').val().trim());
        }, 'json').fail(handleAjaxError);
    });

    $(document).on('click', '.btn-delete-brand', function () {
        const brandId = $(this).data('brand-id');
        const brandName = $(this).data('brand-name');

        Swal.fire({
            title: 'Delete brand?',
            text: `Are you sure you want to remove "${brandName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.post('../actions/delete_brand_action.php', { brand_id: brandId }, function (response) {
                if (!response.success) {
                    Swal.fire('Error', response.message || 'Unable to delete brand', 'error');
                    return;
                }

                Swal.fire('Deleted', response.message, 'success');
                loadBrands($('#brand_search').val().trim());
            }, 'json').fail(handleAjaxError);
        });
    });

    $('#brand_search').on('input', function () {
        const value = $(this).val().trim();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            loadBrands(value);
        }, 250);
    });

    $.when(loadCategories(), loadBrands()).fail(function () {
        // Failures handled individually
    });
});

