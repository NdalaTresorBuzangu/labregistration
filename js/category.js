$(document).ready(function() {
    function fetchCategories(search = '', sortColumn = '', sortOrder = '') {
        $.ajax({
            url: '../actions/fetch_category_action.php',
            method: 'POST',
            data: { search, sortColumn, sortOrder },
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    Swal.fire('Error', response.message || 'Failed to load categories', 'error');
                    return;
                }

                const data = response.data || [];
                let rows = '';
                data.forEach(cat => {
                    rows += `<tr>
                        <td>${cat.cat_id}</td>
                        <td><input type="text" class="form-control edit-name" data-id="${cat.cat_id}" value="${cat.cat_name}"></td>
                        <td>
                            <button class="btn btn-success btn-sm update" data-id="${cat.cat_id}">Update</button>
                            <button class="btn btn-danger btn-sm delete" data-id="${cat.cat_id}">Delete</button>
                        </td>
                    </tr>`;
                });
                $('#category-table tbody').html(rows);
            }
        });
    }

    fetchCategories();

    $('#add-category-form').submit(function(e) {
        e.preventDefault();
        let name = $('#category_name').val().trim();
        if(name === '') return;

        $.post('../actions/add_category_action.php', { name }, function(response) {
            Swal.fire(response.success ? 'Success!' : 'Error', response.message, response.success ? 'success' : 'error');
            if(response.success) {
                $('#category_name').val('');
                fetchCategories();
            }
        }, 'json');
    });

    $(document).on('click', '.update', function() {
        let id = $(this).data('id');
        let name = $(this).closest('tr').find('.edit-name').val().trim();
        if(name === '') return;

        $.post('../actions/update_category_action.php', { id, name }, function(response) {
            Swal.fire(response.success ? 'Success!' : 'Error', response.message, response.success ? 'success' : 'error');
            if(response.success) fetchCategories();
        }, 'json');
    });

    $(document).on('click', '.delete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the category!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed) {
                $.post('../actions/delete_category_action.php', { id }, function(response) {
                    Swal.fire(response.success ? 'Deleted!' : 'Error', response.message, response.success ? 'success' : 'error');
                    if(response.success) fetchCategories();
                }, 'json');
            }
        });
    });

    $('#search').on('input', function() {
        let keyword = $(this).val();
        fetchCategories(keyword);
    });

    $(document).on('click', '.sort', function() {
        let column = $(this).data('column');
        let order = $(this).data('order');
        fetchCategories($('#search').val(), column, order);
        $(this).data('order', order === 'asc' ? 'desc' : 'asc');
    });
});

