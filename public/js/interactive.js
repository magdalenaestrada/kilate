function create_tr(table_id) {
    let table_body = document.getElementById(table_id);
    let first_tr = table_body.firstElementChild;
    let tr_clone = first_tr.cloneNode(true);
    let row_number = table_body.children.length;

    $(tr_clone).find("span.select2").remove();

    let select = $(tr_clone).find("select");

    select
        .removeClass()
        .addClass(
            "form-control form-control-sm buscador buscador" + row_number
        );

    select.val("");

    table_body.appendChild(tr_clone);

    $(".buscador" + row_number).select2({
        theme: "classic",
    });
}

function clean_first_tr(firstTr) {
    let children = firstTr.children;
    children = Array.isArray(children) ? children : Object.values(children);
    children.forEach((x) => {
        if (x !== firstTr.lastElementChild) {
            let inputElement = x.firstElementChild;
            if (inputElement) {
                if (inputElement.type === "checkbox") {
                    inputElement.checked = false; // Clear the checkbox
                } else {
                    inputElement.value = ""; // Clear other input types
                }
            }
        }
    });
}

function remove_tr(This) {
    if (This.closest("tbody").childElementCount == 1) {
        Swal.fire("ERROR!!!", "No elimine todas las filas", "error");
    } else {
        This.closest("tr").remove();
    }
}
