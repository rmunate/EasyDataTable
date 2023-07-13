//SERVERSIDE
var tableSS = $('#datatable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
        url: SERVER + "/novedades/novedadesIndex",
        dataType:"JSON",
        type:"GET"
    },
    columns: [
        {data: "identification"},
        {data: "employee"},
        {data: "novelty_type"},
        {data: "description"},
        {data: "calendar_days"},
        {data: "business_days"},
        {data: "initial_date"},
        {data: "final_date"},
        {data: "action",
            render: function (data, type, row, meta) {
                let btnEdit = '';
                if(data.editar){
                    btnEdit  =  `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }
                return `<div class='btn-group'>${btnEdit}</div>`
            },
            orderable: false
        }
    ],
    language: {
        url: SERVER + "/datatable/Spanish.json"
    }
});

//CLIENTSIDE
var tableSS = $('#datatable').DataTable({
    processing: true,
    responsive: true,
    pagingType: "full_numbers",
    data: data, //Aqui estara la variable con el valor retornado desde el Servidor.
    columns: [
        {data: "identification"},
        {data: "employee"},
        {data: "novelty_type"},
        {data: "description"},
        {data: "calendar_days"},
        {data: "business_days"},
        {data: "initial_date"},
        {data: "final_date"},
        {data: "action",
            render: function (data, type, row, meta) {
                let btnEdit = '';
                if(data.editar){
                    btnEdit  =  `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }
                return `<div class='btn-group'>${btnEdit}</div>`
            },
            orderable: false
        }
    ],
    language: {
        url: SERVER + "/datatable/Spanish.json"
    }
});