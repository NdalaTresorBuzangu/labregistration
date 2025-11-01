$(document).ready(function () {
    const $form = $('#product-form');
    const $productId = $('#product_id');
    const $title = $('#product_title');
    const $price = $('#product_price');
    const $categorySelect = $('#product_category');
    const $brandSelect = $('#product_brand');
    const $keywords = $('#product_keywords');
    const $description = $('#product_description');
    const $imageInput = $('#product_image');
    const $imagePreviewWrap = $('#product-image-preview');
    const $imagePreview = $('#product-preview-img');
    const $imagePath = $('#product_image_path');
    const $formTitle = $('#product-form-title');
    const $cancelEdit = $('#product-cancel-edit');
    const $submitBtn = $('#product-submit-btn');
    const $accordion = $('#product-accordion');
    const $emptyState = $('#product-empty-state');
    const $search = $('#product_search');
    const detailModalEl = document.getElementById('productDetailModal');
    const detailModal = detailModalEl ? new bootstrap.Modal(detailModalEl) : null;
    const $modalBody = $('#modal_product_body');
    const $modalTitle = $('#modal_product_title');
    const $modalEditBtn = $('#modal_edit_product');

    let categories = [];
    const brandMap = new Map();
    const productIndex = new Map();
    let searchTimer = null;
    let modalProductId = null;

    function showError(message) {
        Swal.fire('Error', message || 'An unexpected error occurred', 'error');
    }

    function resetForm() {
        $form[0].reset();
        $productId.val('');
        $imagePath.val('');
        $imageInput.prop('required', true);
        $imagePreviewWrap.hide();
        $imagePreview.attr('src', '');
        $formTitle.text('Add Product');
        $submitBtn.text('Save Product');
        $cancelEdit.addClass('d-none');
        updateBrandOptions('', '');
    }

    function populateCategories(selectValue = '') {
        const options = ['<option value="" disabled selected>Select category</option>'];
        categories.forEach(function (category) {
            options.push(`<option value="${category.cat_id}">${category.cat_name}</option>`);
        });
        $categorySelect.html(options.join(''));
        if (selectValue) {
            $categorySelect.val(String(selectValue));
        } else {
            $categorySelect.val('');
        }
        updateBrandOptions($categorySelect.val(), '');
    }

    function updateBrandOptions(categoryId, selectedBrandId) {
        const id = Number(categoryId);
        const brands = brandMap.get(id) || [];
        let options = [];

        if (!categoryId || brands.length === 0) {
            options.push('<option value="" disabled selected>No brands available</option>');
            $brandSelect.prop('disabled', true);
        } else {
            options.push('<option value="" disabled selected>Select brand</option>');
            brands.forEach(function (brand) {
                options.push(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
            });
            $brandSelect.prop('disabled', false);
        }

        $brandSelect.html(options.join(''));

        if (selectedBrandId && brands.some((brand) => Number(brand.brand_id) === Number(selectedBrandId))) {
            $brandSelect.val(String(selectedBrandId));
        }
    }

    function loadCategories() {
        return $.ajax({
            url: '../actions/fetch_category_action.php',
            method: 'POST',
            dataType: 'json'
        }).done(function (response) {
            if (!response.success) {
                showError(response.message || 'Unable to load categories');
                return;
            }
            categories = response.data || [];
            populateCategories();
        }).fail(function (xhr) {
            showError(xhr.responseJSON && xhr.responseJSON.message);
        });
    }

    function loadBrands() {
        return $.ajax({
            url: '../actions/fetch_brand_action.php',
            method: 'GET',
            dataType: 'json'
        }).done(function (response) {
            if (!response.success) {
                showError(response.message || 'Unable to load brands');
                return;
            }

            brandMap.clear();
            (response.data || []).forEach(function (group) {
                const catId = Number(group.category_id);
                brandMap.set(catId, group.brands || []);
            });

            updateBrandOptions($categorySelect.val(), $brandSelect.val());
        }).fail(function (xhr) {
            showError(xhr.responseJSON && xhr.responseJSON.message);
        });
    }

    function renderProducts(groups) {
        $accordion.find('.accordion-item').remove();
        productIndex.clear();

        const hasProducts = groups.some(function (group) {
            return (group.brands || []).some(function (brand) {
                return (brand.products || []).length > 0;
            });
        });

        $emptyState.toggle(!hasProducts);

        groups.forEach(function (group, groupIndex) {
            const categoryId = group.category_id;
            const groupId = `product-cat-${categoryId}`;
            const collapseId = `product-collapse-${categoryId}`;

            let bodyContent = '';

            (group.brands || []).forEach(function (brand) {
                const products = brand.products || [];
                if (products.length === 0) {
                    return;
                }

                bodyContent += `
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="text-uppercase text-muted mb-2 small">${brand.brand_name}</h6>
                        </div>`;

                products.forEach(function (product) {
                    productIndex.set(product.product_id, Object.assign({}, product, {
                        category_name: group.category_name,
                        brand_name: brand.brand_name
                    }));

                    const imageSrc = product.image ? `../${product.image}` : 'https://via.placeholder.com/300x200?text=No+Image';
                    const priceLabel = new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(product.price || 0);

                    bodyContent += `
                        <div class="card mb-3 shadow-sm" data-product-id="${product.product_id}">
                            <div class="row g-0 align-items-center">
                                <div class="col-md-3">
                                    <img src="${imageSrc}" class="img-fluid rounded-start" alt="${product.title}">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="card-title mb-1">${product.title}</h6>
                                            <span class="badge bg-success">${priceLabel}</span>
                                        </div>
                                        <p class="card-text text-muted small mb-2">${product.keywords || 'No keywords'}</p>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary btn-product-view" data-product-id="${product.product_id}">View</button>
                                            <button class="btn btn-sm btn-outline-secondary btn-product-edit" data-product-id="${product.product_id}">Edit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });

                bodyContent += '</div>';
            });

            if (bodyContent === '') {
                bodyContent = '<p class="text-muted fst-italic mb-0">No products in this category yet.</p>';
            }

            const isOpen = hasProducts && groupIndex === 0 ? 'show' : '';

            const item = `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="${groupId}">
                        <button class="accordion-button ${isOpen ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${isOpen ? 'true' : 'false'}" aria-controls="${collapseId}">
                            ${group.category_name}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse ${isOpen}" aria-labelledby="${groupId}" data-bs-parent="#product-accordion">
                        <div class="accordion-body">
                            ${bodyContent}
                        </div>
                    </div>
                </div>`;

            $accordion.append(item);
        });
    }

    function loadProducts(searchTerm = '') {
        return $.ajax({
            url: '../actions/fetch_product_action.php',
            method: 'GET',
            dataType: 'json',
            data: searchTerm ? { search: searchTerm } : {}
        }).done(function (response) {
            if (!response.success) {
                showError(response.message || 'Unable to load products');
                return;
            }
            renderProducts(response.data || []);
        }).fail(function (xhr) {
            showError(xhr.responseJSON && xhr.responseJSON.message);
        });
    }

    function getFormData() {
        const fd = new FormData();
        if ($productId.val()) {
            fd.append('product_id', $productId.val());
        }
        fd.append('title', $title.val().trim());
        fd.append('price', $price.val());
        fd.append('category_id', $categorySelect.val() || '');
        fd.append('brand_id', $brandSelect.val() || '');
        fd.append('keywords', $keywords.val().trim());
        fd.append('description', $description.val().trim());

        if ($imagePath.val()) {
            fd.append('image_path', $imagePath.val());
        }

        if ($imageInput[0].files.length > 0) {
            fd.append('product_image', $imageInput[0].files[0]);
        }

        return fd;
    }

    function populateForm(product) {
        if (!product) {
            return;
        }

        $productId.val(product.product_id);
        $title.val(product.title);
        $price.val(product.price);
        populateCategories(product.category_id);
        updateBrandOptions(product.category_id, product.brand_id);
        $brandSelect.val(String(product.brand_id));
        $brandSelect.prop('disabled', false);
        $keywords.val(product.keywords || '');
        $description.val(product.description || '');

        if (product.image) {
            $imagePreview.attr('src', `../${product.image}`);
            $imagePreviewWrap.show();
            $imagePath.val(product.image);
        } else {
            $imagePreviewWrap.hide();
            $imagePreview.attr('src', '');
            $imagePath.val('');
        }

        $imageInput.prop('required', false);
        $formTitle.text('Edit Product');
        $submitBtn.text('Update Product');
        $cancelEdit.removeClass('d-none');
    }

    function renderModal(product) {
        if (!product) {
            return;
        }

        modalProductId = product.product_id;
        $modalTitle.text(product.title);

        const priceLabel = new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(product.price || 0);
        const imageMarkup = product.image
            ? `<img src="../${product.image}" class="img-fluid rounded mb-3" alt="${product.title}">
               `
            : '<div class="alert alert-secondary">No image uploaded for this product.</div>';

        $modalBody.html(`
            ${imageMarkup}
            <dl class="row mb-0">
                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">${product.category_name}</dd>

                <dt class="col-sm-3">Brand</dt>
                <dd class="col-sm-9">${product.brand_name}</dd>

                <dt class="col-sm-3">Price</dt>
                <dd class="col-sm-9">${priceLabel}</dd>

                <dt class="col-sm-3">Keywords</dt>
                <dd class="col-sm-9">${product.keywords || '—'}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">${product.description || '—'}</dd>
            </dl>
        `);
    }

    $categorySelect.on('change', function () {
        updateBrandOptions($(this).val(), '');
    });

    $cancelEdit.on('click', function () {
        resetForm();
    });

    $imageInput.on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                $imagePreview.attr('src', event.target.result);
                $imagePreviewWrap.show();
            };
            reader.readAsDataURL(file);
        } else {
            if (!$productId.val()) {
                $imagePreviewWrap.hide();
            }
        }
    });

    $form.on('submit', function (event) {
        event.preventDefault();

        if (!$categorySelect.val()) {
            showError('Please select a category');
            return;
        }

        if (!$brandSelect.val()) {
            showError('Please select a brand');
            return;
        }

        if (!$productId.val() && $imageInput[0].files.length === 0) {
            showError('Please upload a product image');
            return;
        }

        const isEdit = Boolean($productId.val());
        const url = isEdit ? '../actions/update_product_action.php' : '../actions/add_product_action.php';
        const formData = getFormData();

        $.ajax({
            url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                $submitBtn.prop('disabled', true).text(isEdit ? 'Updating…' : 'Saving…');
            }
        }).done(function (response) {
            if (!response.success) {
                showError(response.message || 'Unable to save product');
                return;
            }

            Swal.fire('Success', response.message, 'success');
            resetForm();
            loadProducts($search.val().trim());
        }).fail(function (xhr) {
            showError(xhr.responseJSON && xhr.responseJSON.message);
        }).always(function () {
            $submitBtn.prop('disabled', false).text(isEdit ? 'Update Product' : 'Save Product');
        });
    });

    $(document).on('click', '.btn-product-edit', function () {
        const id = Number($(this).data('product-id'));
        const product = productIndex.get(id);
        if (!product) {
            showError('Product could not be found for editing');
            return;
        }
        populateForm(product);
        $('html, body').animate({ scrollTop: $form.offset().top - 80 }, 300);
    });

    $(document).on('click', '.btn-product-view', function () {
        const id = Number($(this).data('product-id'));
        const product = productIndex.get(id);
        if (!product) {
            showError('Product could not be found');
            return;
        }
        renderModal(product);
        if (detailModal) {
            detailModal.show();
        }
    });

    $modalEditBtn.on('click', function () {
        if (!modalProductId) {
            return;
        }
        const product = productIndex.get(modalProductId);
        if (!product) {
            showError('Product could not be found');
            return;
        }
        if (detailModal) {
            detailModal.hide();
        }
        populateForm(product);
        $('html, body').animate({ scrollTop: $form.offset().top - 80 }, 300);
    });

    $search.on('input', function () {
        const value = $(this).val().trim();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            loadProducts(value);
        }, 300);
    });

    $.when(loadCategories(), loadBrands()).done(function () {
        resetForm();
        loadProducts();
    });
});

