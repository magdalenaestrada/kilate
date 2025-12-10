function create_tr(table_id) {
    let table_body = document.getElementById(table_id);
    let first_tr = table_body.firstElementChild;

    let new_tr = document.createElement("tr");
    new_tr.innerHTML = `
        <td>
            <select name="products[]" class="form-control cart-product buscador" required>
                <option value="">-- Seleccione una opción</option>
                ${Array.from(first_tr.querySelector(".cart-product").options)
                    .filter((opt) => opt.value !== "")
                    .map(
                        (opt) =>
                            `<option value="${opt.value}">${opt.text}</option>`
                    )
                    .join("")}
            </select>
        </td>
        <td><input required name="product_price[]" placeholder="0.00" class="form-control form-control-sm product-price"></td>
        <td><input required name="qty[]"  placeholder="0.00" class="form-control form-control-sm product-qty"></td>
        <td><input name="item_total[]"  placeholder="0.00" class="form-control form-control-sm product-total"></td>
        <td><button class="btn btn-sm btn-danger" onclick="remove_tr(this)" type="button">Quitar</button></td>
    `;

    table_body.appendChild(new_tr);

    $(new_tr).find(".product-total");

    initSelect2($(new_tr).find(".cart-product"));
}

function initSelect2($el) {
    if ($el.hasClass("select2-hidden-accessible")) {
        $el.select2("destroy");
    }

    $el.select2({
        theme: "classic",
        dropdownParent: $("#ModalCreate .modal-content"),
        width: "100%",
        placeholder: "-- Seleccione una opción",
        allowClear: true,
        dropdownCssClass: "select2-dropdown-custom",
        minimumResultsForSearch: 0,
    });
}

$("#ModalCreate").on("shown.bs.modal", function () {
    initSelect2($("#ModalCreate").find(".cart-product, .buscador"));
});

function remove_tr(This) {
    let tbody = This.closest("tbody");

    if (tbody.childElementCount == 1) {
        Swal.fire("ERROR!", "No elimine todas las filas", "error");
        return;
    }

    $(This).closest("tr").remove();
    calculateGrandTotal();
}
