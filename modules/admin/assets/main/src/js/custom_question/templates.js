/* eslint-disable no-unused-vars */
/* eslint-disable valid-jsdoc */

// DON'T use HTML "required" attribute to prevent confusing errors,
// when switching question types on form submission

function getRadioTemplate() {
    return '<div title="Tételek rendezése" class="item pull-left full_width">' +
        ' <span class="dot"></span> <input type="text" class="form form-control form-control_inline">' +
        ' <span title="Eltávolítás" class="remove_container"><i class="glyphicon glyphicon-remove"></i></span>' +
        ' </div>';
}

function getCheckboxTemplate() {
    return '<div title="Tételek rendezése" class="item pull-left full_width">' +
        ' <span class="square"></span> <input type="text" class="form form-control form-control_inline">' +
        ' <span title="Eltávolítás" class="remove_container"><i class="glyphicon glyphicon-remove"></i></span>' +
        ' </div>';
}

function getSingleSelectDropdownTemplate() {
    return '<div title="Tételek rendezése" class="item pull-left full_width">' +
        ' <input type="text" class="form form-control form-control_inline">' +
        ' <span title="Eltávolítás" class="remove_container"><i class="glyphicon glyphicon-remove"></i></span>' +
        ' </div>';
}
