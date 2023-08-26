/**
 * ESTE CODIGO ES EXCLUSIVO PARA USARLO EN LOS PROYECTOS QUE EMPLEEN EL TEMA METRONIC
 * THIS CODE IS EXCLUSIVE FOR USE IN PROJECTS THAT USE THE METRONIC THEME
 * https://keenthemes.com/metronic
 * https://preview.keenthemes.com/html/metronic/docs/general/datatables/server-side (See For More Settings)
 */


/* ES: Se requiere crear una variable global en la cual se aloje una función autoinvocada. */
/* EN: It is required to create a global variable in which a self-invoking function is hosted. */
var KTDatatablesServerSide = function () {

    
    /* ES: Variables que se emplearan durante la ejecucion de la funcion autoinvocada. */
    /* EN: Variables that will be used during the execution of the self-invoking function. */
    var table;
    var dt;

    /* ES: Funcion privada para la construccion de la datatable */
    /* EN: Private function for datatable construction. */
    var initDatatable = function () {
        dt = $("#datatable").DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: false,
            pagingType: "full_numbers",
            ajax: {
                /* ES: Asegúrate de que la ruta (web.php) sea de tipo GET */
                /* EN: Ensure that the route (web.php) is of type GET */
                url: baseUrl + "/modulo/datatable",
                dataType: "JSON",
                type: "GET",
            },
            columns: [
                /* ES: El nombre debe ser el mismo que el del arreglo asociativo devuelto desde el BackEnd */
                /* EN: The name must be the same as that of the associative array returned from the BackEnd */
                { data: "identification" },
                { data: "employee" },
                { data: "novelty_type" },
                { data: "description" },
                { data: "calendar_days" },
                { data: "business_days" },
                { data: "initial_date" },
                { data: "final_date" },
                {
                    data: "action",
                    render: function (data, type, row, meta) {
                        let btnEdit = "";
                        if (data.editar) {
                            btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                                <i class="fa flaticon-edit-1"></i>
                                            </button>`;
                        }
                        return `<div class='btn-group'>${btnEdit}</div>`;
                    },
                    orderable: false,
                },
            ],
            /* ES: Recuerda que puedes aplicar la configuración de idioma de tu región. */
            /* EN: Remember that you can apply the language configuration of your region. */
            "oLanguage": {
                "sUrl": "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
            }
        });

        table = dt.$;
    }

    /* ES: Aqui puedes definir cual será el input de busqueda (official docs reference: https://datatables.net/reference/api/search()) */
    /* EN: Here you can define which will be the search input.(official docs reference: https://datatables.net/reference/api/search()) */
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }

    /* ES: Metodo para la inicializacion de la datatable. */
    /* EN: Method for the initialization of the datatable. */
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
        }
    }
}();

/* ES: Cuando cargue el documento se llamará la variable con su funcion, algo importante es que tambien se podria usar el metodo de Jquery */
/* EN: When the document loads, the variable will be called with its function; something important is that you could also use the jQuery method. */
// KTUtil.onDOMContentLoaded() == $(document).ready()
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});