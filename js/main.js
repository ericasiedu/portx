var system_object, search;
var Menu= {
    process: function (itm) {
        eval("this." + itm + "()");
    },
    gateInAuxData:function () {
        swal('', 'hi');
    }
}


var Conn={
    get:function (){
        this.prot="GET";
        this.open(arguments[0]);
        this.send();
    },
    post:function (){
        this.prot="POST";
        this.open(arguments[0]);
        return this;
    },
    open:function (){
        var url="/route";
        if (arguments[0]){
            url=arguments[0];
        }
        if (xobj.readyState == 0){
            this.xobj=xobj;
        }
        else {
            this.xobj=fobj;
        }
        this.xobj.open(this.prot,url,true);
    },
    send:function(){
        var arg=arguments[0];
        var data;
        if (!arg){
            data=null;
        }
        else{
            data=arg;
            Conn.xobj.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        }
        Conn.xobj.send(data);
        Conn.xobj.onreadystatechange=function(){
            if (Conn.xobj.readyState >= 0 && Conn.xobj.readyState <4){
                document.body.style.cursor="progress";
            }
            if (Conn.xobj.readyState == 4){
                document.body.style.cursor="auto";
                try{
                    jsn=JSON.parse(Conn.xobj.responseText);
                }
                catch(e){
                    console.log(Conn.xobj.responseText);
                }
                Menu.process(Conn.itm);
            }
        }
    },
    menu:function(itm){
        this.itm=itm;
        return this
    }
}
var FConn={
    get:function (){
        this.prot="GET";
        this.open(arguments[0]);
        this.send();
    },
    post:function (){
        this.prot="POST";
        this.open(arguments[0]);
        return this;
    },
    open:function (){
        this.url="/route";
        if (arguments[0]){
            this.url=arguments[0];
        }
    },
    send:function(){
        /*  if (falrt.readyState == 0){*/
        falrt.open(this.prot,this.url,true);
        var arg=arguments[0];
        var data;
        if (!arg){
            data=null;
        }
        else{
            data=arg;
            falrt.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        }
        falrt.send(data);
        falrt.onreadystatechange=function(){
            if (falrt.readyState >= 0 && falrt.readyState <4){
                document.body.style.cursor="progress";
            }
            if (falrt.readyState == 4){
                document.body.style.cursor="auto";
                try{
                    fstr=JSON.parse(falrt.responseText);
                }
                catch(e){
                    console.log(falrt.responseText);
                }
                Menu.process(FConn.itm);
            }
        }
        /*  }*/
    },
    menu:function(itm){
        this.itm=itm;
        return this
    }

}

var Voyage={
    iniTable:function () {
        var editor = new $.fn.dataTable.Editor({
            ajax: "/api/voyage/table",
            table: "#voyage",
            fields: [{
                label: "Reference:",
                name: "voyage.reference",
                attr: {
                    class: "form-control",
                    maxlength: 20
                }
            }, {
                label: "Rotation Number:",
                name: "voyage.rotation_number",
                attr: {
                    class: "form-control",
                    maxlength: 20
                }
            }, {
                label: "Vessel:",
                name: "voyage.vessel_id",
                attr: {
                    maxlength: 100,
                    list: "vessels",
                    class: "form-control"
                }
            }, {
                label: "Shipping Line:",
                name: "voyage.shipping_line_id",
                attr: {
                    maxlength: 150,
                    list: "agents",
                    class: "form-control"
                }
            }, {
                label: "Arrival Draft:",
                name: "voyage.arrival_draft",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Gross Tonnage:",
                name: "voyage.gross_tonnage",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Voyage Status:",
                name: "voyage.voyage_status_id",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Estimated Arrival:",
                name: "voyage.estimated_arrival",
                type: "datetime",
                def: function () {
                    return new Date();
                },
                format: "YYYY-MM-DD HH:mm",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Actual Arrival:",
                name: "voyage.actual_arrival",
                type: "datetime",
                def: function () {
                    return new Date();
                },
                format: "YYYY-MM-DD HH:mm",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Estimated Departure:",
                name: "voyage.estimated_departure",
                type: "datetime",
                def: function() { return new Date();},
                format: "YYYY-MM-DD HH:mm",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Actual Departure:",
                name: "voyage.actual_departure",
                type: "datetime",
                def: function() {
                    return new Date();
                },
                format: "YYYY-MM-DD HH:mm",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Previous Port:",
                name: "voyage.prev_port_id",
                attr: {
                    class: "form-control",
                    list: 'ports'
                }
            }, {
                label: "Next Port:",
                name: "voyage.next_port_id",
                attr: {
                    class: "form-control",
                    list: 'ports'
                }
            }, {
                label: "Entry Status:",
                name: "voyage.entry_status",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Entry Date:",
                name: "voyage.entry_date",
                type: "datetime",
                def: function () {
                    return new Date();
                },
                format: "YYYY-MM-DD HH:mm",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Job Number:",
                name: "voyage.gcnet_job_number",
                attr: {
                    class: "form-control",
                    maxlength: 20
                }
            }, {
                label: "Gate Open:",
                name: "voyage.gate_open",
                type: "datetime",
                def: function () {
                    return new Date();
                },
                format: "YYYY-MM-DD HH:mm",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Gate Close:",
                name: "voyage.gate_close",
                type: "datetime",
                def: function () {
                    return new Date();
                },
                format: "YYYY-MM-DD HH:mm",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Discharge From:",
                name: "voyage.discharge_from",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }, {
                label: "Discharge To:",
                name: "voyage.discharge_to",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            if (action === 'remove') {
                var status = json.cancelled;
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Voyage', 'One or more containers in the Voyage has been gated in or flagged, Voyage can’t be deleted.');
                }
            }
        });


        $('#voyage').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/voyage/table",
                type: "POST"
            },
            serverSide: true,
            order: [[ 14, 'desc' ]],
            columns: [
                {data: "voyage.reference"},
                {data: "voyage.rotation_number"},
                {data: "vnam"},
                {data: "ship"},
                {data: "voyage.arrival_draft"},
                {data: "voyage.gross_tonnage"},
                {data: "voyage.voyage_status_id"},
                {data: "voyage.estimated_arrival"},
                {data: "voyage.actual_arrival"},
                {data: "voyage.estimated_departure"},
                {data: "voyage.actual_departure", visible: false},
                {data: "ppor", visible: false},
                {data: "npor", visible: false},
                {data: "voyage.entry_status", visible: false},
                {data: "voyage.entry_date", visible: false},
                {data: "voyage.gcnet_job_number", visible: false},
                {data: "voyage.gate_open", visible: false},
                {data: "voyage.gate_close", visible: false},
                {data: "voyage.discharge_from", visible: false},
                {data: "voyage.discharge_to", visible: false}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Voyage')
        });
    }
}

var TableRfresh = {
    freshTable: function (table_id) {
        $('#' + table_id).DataTable().ajax.reload();
    }
}

var Modaler = {
    dModal: function (header, html, size) {
        app.modaler({
            title: header,
            html: html,
            type: 'center',
            size: size ? size : 'sm',
            confirmVisible: false
        });
    }
}

var EventModal = {
    dModal: function (header, html, size, id) {
        app.modaler({
            title: header,
            html: html,
            type: 'center',
            size: size ? size : 'sm',
            confirmVisible: false,
            // isModal: true,
            footerVisible: false,
            modalId: id,
            // onShown: event,
        });
    }
}

var CondModal = {
    cModal: function (header, html, size) {
        app.modaler({
            title: header,
            html: html,
            type: 'center',
            size: size ? size : 'lg',
            confirmVisible: false
        });
    }
}

var Container= {
    gate_flag: function(flag){
        $.ajax({
            type:"POST",
            url:"/api/container/flag",
            data: {ctid: flag},
            success: function (data) {
                $('#statusHeader').text('FLAGGED');
                $('#containerStatus').text('Container Flagged');
                TableRfresh.freshTable('container');
            },
            error: function () {
                $('#statusHeader').text('ERROR');
                $('#containerStatus').text('Something Went Wrong');
            }
        });


    },
    gate_unflag: function(unflag){
        $.ajax({
            type:"POST",
            url:"/api/container/unflag",
            data: {ctid: unflag},
            success: function (data) {
                $('#statusHeader').text('UNFLAGGED');
                $('#containerStatus').text('Container Unflagged');
                TableRfresh.freshTable('container');
            },
            error: function () {
                $('#statusHeader').text('ERROR');
                $('#containerStatus').text('Something Went Wrong');
            }
        });

    },
    get_export_voyage: function(){
        var trade = $('#containerType').val();
        if (trade == 21){

            $.ajax({
                url:"/api/container/get_export_voyage",
                type:"POST",
                data:{},
                success: function (data) {
                    var result = $.parseJSON(data);
                    var voyage = result.ref;
                    $('#voyageID').val(voyage);
                },
                error: function () {
                    alert("something went wrong");
                }
            })
        }

    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/container/table",
            table: "#container",
            fields: [ {
                label: "Trade Type:",
                name: "container.trade_type_code",
                type:"select",
                attr:{
                    id: "containerType",
                    onchange:"Container.get_export_voyage()",
                    class: "form-control"
                },
                options:[
                    { label: "IMPORT", value: 11 },
                    { label: "EXPORT", value: 21 }
                ]
            },{
                label: "ID:",
                name: "container.id",
                attr: {
                    class: "form-control",
                    maxlength: 11
                }
            },{
                label: "Number:",
                name: "container.number",
                attr: {
                    class: "form-control",
                    maxlength: 11
                }
            }, {
                label: "SOC:",
                name:"container.soc_status",
                attr: {
                    id: 'soc_status',
                    class: "form-control"
                } ,
                type: "select",
                options: [
                    {label: "YES", value: "YES"},
                    {label: "NO", value: "NO"}
                ]
            }, {
                label: "BL Number:",
                name: "container.bl_number",
                attr: {
                    class: "form-control",
                    maxlength: 20
                }
            }, {
                label:"Booking Number:",
                name: "container.book_number",
                attr: {
                    class: "form-control",
                    maxlength: 20,
                }
            },
                {
                    label:"Voyage",
                    name: "container.voyage",
                    attr:{
                        id:"voyageID",
                        class: "form-control",
                        list:"voyages",
                    }
                },
                {
                    label: "Shipping Line:",
                    name: "container.shipping_line_id",
                    attr: {
                        id:"shippingLineID",
                        class: "form-control",
                        list: "lines"
                    }
                }, {
                    label: "IMDG Code:",
                    name: "container.imdg_code_id",
                    attr: {
                        class: "form-control",
                        list: "imdg"
                    }
                }, {
                    label: "Full Status:",
                    name: "container.full_status",
                    type: "select",
                    attr: {
                        id: "full_stat",
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ],
                    def: "YES"
                }, {
                    label: "OOG:",
                    name: "container.oog_status",
                    type: "select",
                    attr: {
                        id: "oog",
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ],
                    /* def: "YES" */
                }, {
                    label: "Seal Number 1:",
                    name: "container.seal_number_1",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Seal Number 2:",
                    name: "container.seal_number_2",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "ICL Seal Number 1",
                    name: "container.icl_seal_number_1",
                    attr:{
                        class: "form-control",
                    }
                },{
                    label: "ICL Seal Number 2",
                    name: "container.icl_seal_number_2",
                    attr:{
                        class: "form-control"
                    }
                },{
                    label: "ISO Type Code:",
                    name: "container.iso_type_code",
                    attr: {
                        class: "form-control",
                        list: "types"
                    }
                }, {
                    label: "Agency:",
                    name: "container.agency_id",
                    attr: {
                        class: "form-control",
                        list: "agents"
                    }
                }, {
                    label: "POL:",
                    name: "container.pol",
                    attr: {
                        class: "form-control",
                        list: "ports"
                    }
                }, {
                    label: "POD:",
                    name: "container.pod",
                    attr: {
                        class: "form-control",
                        list: "ports"
                    }
                }, {
                    label: "FPOD:",
                    name: "container.fpod",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Tonnage Weight Metric:",
                    name: "container.tonnage_weight_metric",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Tonnage Freight:",
                    name: "container.tonnage_freight",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Content of Goods:",
                    name: "container.content_of_goods",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Importer Address:",
                    name: "container.importer_address",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        editor.field('container.book_number').hide();
        editor.field('container.id').hide();
        editor.dependent('container.trade_type_code', function (val) {
            
            if (val == 21) {
                editor.field('container.icl_seal_number_1').val('');
                editor.field('container.icl_seal_number_2').val('');
                editor.field('container.bl_number').val('');
                editor.field('container.importer_address').val('');
            }
            else {
                editor.field('container.book_number').val('');
            }
            return val == 21 ?
                {
                    show: ['container.book_number'],
                    hide: ['container.voyage',
                        'container.icl_seal_number_1', 'container.icl_seal_number_2']
                } :
                {
                    hide: ['container.book_number',],
                    show: ['container.voyage',
                        'container.icl_seal_number_1', 'container.icl_seal_number_2']
                }
        });
        editor.dependent('container.trade_type_code', function (val) {
            return val != 11 ?
                {hide:['container.importer_address','container.bl_number']} :
                {show: ['container.importer_address','container.bl_number']}
        });


        editor.on('initEdit', function () {
            editor.field('container.icl_seal_number_1').hide();
            editor.field('container.icl_seal_number_2').hide();
        });

        editor.on('postSubmit', function ( e, json, data, action ) {
            if (action == 'create') {
                if (json.data.length > 0) {
                    var container_id = json.data[0].DT_RowId.substring(4);

                    $.ajax({
                        type: 'POST',
                        url: '/api/container/add_info',
                        data: {ctid: container_id},
                        success: function (data) {
                        },
                        error: function () {
                            $('#myModalLabel').text('ERROR');
                            $('#gate_container').text('Something Went Wrong');
                        }
                    });
                }
            }
        });

        editor.on('submitComplete', function (e, json, data, action) {
            if (action === 'remove') {
                var status = json.cancelled;
                if (status.length > 0) {
                    Modaler.dModal('Container Error', 'Cannot delete container. Container is in used');
                }
            }
        });

        $('#container').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/container/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [
                { "searchable": false, "targets": 24 }
            ],
            columns: [
                { data: "container.number" },
                { data: "container.bl_number" },
                { data: "container.book_number",visible:false},
                { data:"container.voyage"},
                { data: "container.seal_number_1" },
                { data: "container.seal_number_2",visible:false },
                { data: "container.icl_seal_number_1",visible:false},
                { data: "container.icl_seal_number_2",visible:false},
                { data: "container.iso_type_code" },
                { data: "container.soc_status" },
                { data: "container.shipping_line_id" },
                { data: "container.agency_id",visible:false },
                { data: "container.pol",visible:false },
                { data: "container.pod", visible: false },
                { data: "container.fpod", visible: false },
                { data: "container.tonnage_weight_metric", visible: false },
                { data: "container.tonnage_freight", visible: false },
                { data: "trade_type.name", visible: false },
                { data: "container.content_of_goods", visible: false },
                { data: "container.importer_address", visible: false },
                { data: "container.imdg_code_id", visible: false },
                { data: "container.oog_status", visible: false },
                { data: "container.full_status", visible: false },
                { data:"container.gate_status"},
                { data: null,
                    render: function (data, type, row) {
                        switch (data.container.status) {
                            case "0":
                                return "<a href='#' data-toggle='modal' data-target='#modal-small' id='status_flag' value='' onclick='Container.gate_flag(\"" + data.container.id + "\")'>Flag</a>";
                            case "1":
                                return "<a href='#' data-toggle='modal' data-target='#modal-small' id='status_unflag' value='' onclick='Container.gate_unflag(\"" + data.container.id + "\")'>Unflag</a>";
                            default:
                                return "";
                        }
                    }
                }
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Container')
        });
    }
}

var Port = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/port/table",
            table: "#port",
            template: '#customForm',
            fields: [ {
                label: "Code:",
                name: "code",
                attr: {
                    class: "form-control",
                    maxlength: 10
                }
            }, {
                label: "Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Port In Use', 'Port can not be deleted.');
                }
            }
        });

        $('#port').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/port/table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                { data: "code" },
                { data: "name" },
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Port')
        });
    }
}


var GateIn = {
    conditionGate: function (id, number) {
        add_gatein_condition.create({
            title: 'Gate Condition Container',
            buttons: 'Add',
        });

        var container = document.getElementById('container_no');
        container.value = number;
        var gate_record = document.getElementById('gate_record_id');
        gate_record.value = id;
    },

    conditionAlert: function (id, container) {
        var header = container;
        var body = "<div class=\"col-md-12\"><table id=\"container_condition\" class=\"display table-responsive\">" +
            "<thead><tr><th>Container Section </th><th>Damage Type </th><th>Damage Severity </th><th>Note </th></tr></thead>" +
            "</table></div>";

        CondModal.cModal(header, body);

        ContainerCondition.iniTable(id, container);
    },

    loadTradeTypeContainers: function () {
        var el = document.getElementById('trade_select');
        var type = el.value;
        var consig = document.getElementById('consignee');
        var book_number = document.getElementById('book_numberID');

        if (type == 21) {
            consig.style.display = 'block';
            book_number.style.display = 'block';

        }
        else {
            consig.style.display = 'none';
            book_number.style.display = 'none';

        }
        

        var request = new XMLHttpRequest();
        request.open("POST", "/api/container/get_trade_containers", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function () {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);
                var datalist = document.getElementById('containers');
                datalist.innerHTML = '';
                for (var data in response) {
                    var option = document.createElement('option');
                    option.innerText = response[data];
                    datalist.appendChild(option);
                }
            }
            else {
                $('#myModalLabel').text('ERROR');
                $('#gate_container').text('Something Went Wrong');
            }
        };
        request.send("type=" + type);
    },

    getTradetypeInfo: function(gate_record){

        $.ajax({
            type: "POST",
            url:"/api/container/get_trade_type_info",
            data:{
                gid: gate_record
            },
            success: function(data){
                var result = $.parseJSON(data);
                if (result == null) {
                    return;
                }
                var trade_type = result.trade_type_code;
                $('#trade_select').val(trade_type);

                if ($('#trade_select').val() == 21) {
                    $('#consignee').show();
                    $('#container').prop('disabled',false);
                    var checked = false;
                    GateIn.fieldChecked(checked);
                    $('#book_number').show();

                    GateIn.loadTradeTypeContainers();
                }
                else {
                    $('#consignee').hide();
                    $('#container').prop('disabled',false);

                    var checked = true;
                    GateIn.fieldChecked(checked);
                    $('#book_number').hide();
                }
            },
            error: function(){
                alert("something went wrong");
            }
        });
            
    },

    getContainerInfo: function (number) {
        var container = $('#container').val();
        if (container == undefined)
            container = number;
        $.ajax({
            type: 'POST',
            url: '/api/container/get_container_info',
            data: {ctid: container},
            success: function (data) {
                var result = $.parseJSON(data);

                if (result == null) {
                    return;
                }

                var seal1 = result.seal_number_1;
                var seal2 = result.seal_number_2;
                var soc = result.soc_status;
                var line = result.name;
                var full_status = result.full_status;
                var oog_stat = result.oog_status;
                var imdg = result.iname;
                var reference = result.reference;
                var code = result.code;
                var book_number = result.book_number;
                var trade_type = result.trade;

                $('#full_stat').val(full_status);
                $('#oog').val(oog_stat);
                $('#soc_status').val(soc);
                $('#imdg').val(imdg);
                $('#voyage').val(reference);
                $('#seal_number1').val(seal1);
                $('#seal_number2').val(seal2);
                $('#line').val(line);
                $('#code').val(code);
              

                if ($('#trade_select').val() == 21) {
                    $('#consignee').show();
                    $('#container').prop('disabled',false);
                    $('#code').prop('disabled',true);
                    var checked = false;
                    GateIn.fieldChecked(checked);
                    $('#code').prop('disabled',true);
                    $('#book_numberID').show();           
                    GateIn.loadTradeTypeContainers();
                    GateIn.invoiceContainer(container);
                         trade_type == "IMPORT" ? $('#book_number').val("") :   $('#book_number').val(book_number);
                    trade_type == "IMPORT" ? $('#book_number').prop('disabled',false) :$('#book_number').prop('disabled',true); 
                }
                else {
                    $('#consignee').hide();
                    $('#container').prop('disabled',false);
                    $('#code').prop('disabled',true);
                    GateIn.invoiceContainer(container);
                    var checked = true;
                    GateIn.fieldChecked(checked);
                    $('#book_numberID').hide();
                }

            },
            error: function () {
                $('#myModalLabel').text('ERROR');
                $('#gate_container').text('Something Went Wrong');
            }
        });
    },

    getContainerEditInfo: function (gate_record) {
  
        $.ajax({
            type: 'POST',
            url: '/api/container/get_container_edit_info',
            data: {gid: gate_record},
            success: function (data) {
                var result = $.parseJSON(data);

                if (result == null) {
                    return;
                }

                var seal1 = result.seal_number_1;
                var seal2 = result.seal_number_2;
                var soc = result.soc_status;
                var line = result.name;
                var full_status = result.full_status;
                var oog_stat = result.oog_status;
                var imdg = result.iname;
                var reference = result.reference;
                var code = result.code;
                var book_number = result.book_number;
                

                $('#full_stat').val(full_status);
                $('#oog').val(oog_stat);
                $('#soc_status').val(soc);
                $('#imdg').val(imdg);
                $('#voyage').val(reference);
                $('#seal_number1').val(seal1);
                $('#seal_number2').val(seal2);
                $('#line').val(line);
                $('#code').val(code);
                $('#book_number').val(book_number);

                $('#code').prop('disabled',true);
                $('#trade_select').prop('disabled',true);

                if ($('#trade_select').val() == 70) {
                    $('#imdgID').hide();
                }
                else{
                    $('#imdgID').show();
                }

            },
            error: function () {
                $('#myModalLabel').text('ERROR');
                $('#gate_container').text('Something Went Wrong');
            }
        });
    },

    gateContainer: function (id, number) {
        var container = number;
        var result;

        var url = "/api/gate_in/gate_container";
        var gate_container = document.getElementById('gate_container');
        var header = '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function () {
            if (request.readyState == 4 && request.status == 200) {
                result = JSON.parse(request.responseText);
                switch (result.st) {
                    case 116:
                        header = 'Container Already Gated In';
                        body = "Container Number " + container + " is already Gated In";
                        Modaler.dModal(header, body);
                        break;
                    case 117:
                        header = 'Container Flagged';
                        body = "Container Number " + container + " is flagged";
                        Modaler.dModal(header, body);
                        break;
                    case 118:
                        header = "NOT CONDITIONED";
                        body = "The container needs to be conditioned before gating in";
                        Modaler.dModal(header, body);
                        break;
                    case 211:
                        header = "GATED IN";
                        body = "Container Number " + container + " gated in";
                        Modaler.dModal(header, body);
                        TableRfresh.freshTable('gate_in');
                        break;
                    default:
                        gate_container.innerText = "Gate-In Failed!";
                        break;
                }
            }
            else {
                gate_container.innerText = "Gate-In Failed!";
            }
        };
        request.send("data=" + id);
    },

    fieldChecked: function(checked){
        $('#imdg').prop('disabled',checked);
        $('#soc_status').prop('disabled',checked);
        $('#full_stat').prop('disabled',checked);
        $('#oog').prop('disabled',checked);
        $('#line').prop('disabled',checked);
    },

    invoiceContainer: function(gate_record){
        $.ajax({
            url: "/api/container/get_invoiced_container",
            type: "POST",
            data: {
                gid: gate_record
            },
            success: function(data){
                var result = $.parseJSON(data);
                if (result == null) {
                    return;
                }
                GateIn.getContainerEditInfo(gate_record);

                if(result.iner){
                    $('#container').prop('disabled',true);
                    $('#trade_select').prop('disabled',true);
                    var checked = true;
                    GateIn.fieldChecked(checked);
                }
                else{
                    var trade_type = $('#trade_select').val();
                    if (trade_type == 21 || trade_type == 70) {
                        $('#consignee').show();
                        $('#container').prop('disabled',false);
                        var checked = false;
                        GateIn.fieldChecked(checked);
                        $('#book_numberID').show();
                        GateIn.loadTradeTypeContainers();
                    }
                    else {
                        $('#consignee').hide();
                        $('#container').prop('disabled',false);
                        var checked = true;
                        GateIn.fieldChecked(checked);
                        $('#book_numberID').hide();
                    }
                }

            },
            error: function(){
                alert("something went wrong");
            }
        });
    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax: "/api/gate_in/table",
            table: "#gate_in",
            template: '#customForm',
            fields: [{
                label: "Trade Type:",
                name: "trade",
                type: "select",
                attr: {
                    id: "trade_select",
                    onchange: "GateIn.loadTradeTypeContainers()",
                    class: "form-control",
                    disabled: true
                },
                options: [
                    {label: "IMPORT", value: 11},
                    {label: "EXPORT", value: 21}
                ],
            }, {
                label: "Container:",
                name: "gate_record.container_id",
                attr: {
                    maxlength: 11,
                    list: "containers",
                    id: 'container',
                    onselect: "GateIn.getContainerInfo()",
                    onkeyup: "GateIn.getContainerInfo()",
                    class: "form-control",
                    disabled: true
                }
            }, {
                name: "gate_record.type",
                def: 1,
                hidden: true
            },{
                label: "Booking Number:",
                name: "book_number",
                attr:{
                    id: "book_number",
                    class: "form-control",
                    maxLength: 20
                }
            },
                {
                    label: "SOC:",
                    name: "soc",
                    type: "select",
                    attr: {
                        id: 'soc_status',
                        class: "form-control",
                        disabled: true
                    },
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ]
                },
                {
                    label: "Voyage:",
                    name: "voyage",
                    attr: {
                        list: "voyages",
                        id: "voyage",
                        class: "form-control",
                        disabled: true
                    }
                }, {
                    label: "Shipping Line:",
                    name: "shipping_line",
                    attr: {
                        list: "Shipping Line",
                        id: "line",
                        class: "form-control",
                        disabled: true,
                        list: "lines"
                    }
                }, {
                    label: "Depot:",
                    name: "gate_record.depot_id",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Gate:",
                    name: "gate_record.gate_id",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "ISO Type Code:",
                    name: "iso_code",
                    attr: {
                        id: "code",
                        class: "form-control",
                        disabled: true,
                        list: "iso_code"
                    }
                }, {
                    label: "IMDG:",
                    name: "imdg",
                    attr: {
                        id: "imdg",
                        class: "form-control",
                        list: "imdgs",
                        disabled: true,
                        list: "imdgs"
                    },
                }, {
                    label: "Full Status:",
                    name: "full_status",
                    type: "select",
                    attr: {
                        id: "full_stat",
                        class: "form-control",
                        disabled: true
                    },
                    options: [
                        {label: "YES (Laden)", value: 1},
                        {label: "NO", value: 0}
                    ]
                }, {
                    label: "OOG:",
                    name: "oog",
                    type: "select",
                    attr: {
                        id: "oog",
                        class: "form-control",
                        disabled: true
                    },
                    options: [
                        {label: "NO", value: 0},
                        {label: "YES", value: 1}
                    ]
                }, {
                    label: "Seal Number 1:",
                    name: "seal_number_1",
                    attr: {
                        id: "seal_number1",
                        class: "form-control",
                        maxlength: 20
                    },
                }, {
                    label: "Seal Number 2:",
                    name: "seal_number_2",
                    attr: {
                        id: "seal_number2",
                        class: "form-control",
                        maxlength: 20
                    },
                }, {
                    label: "Special Seal:",
                    name: "gate_record.special_seal",
                    attr: {
                        class: "form-control",
                        maxlength: 20
                    }
                },
                {
                    label: "Vehicle:",
                    name: "gate_record.vehicle_id",
                    attr: {
                        id: "vehicleID",
                        list: "vehicles",
                        class: "form-control"
                    }
                }, {
                    label: "Driver:",
                    name: "gate_record.driver_id",
                    attr: {
                        id:"driverID",
                        list: "drivers",
                        class: "form-control"
                    }
                }, {
                    label: "Trucking Company:",
                    name: "gate_record.trucking_company_id",
                    attr: {
                        id: "truckID",
                        list: "companies",
                        class: "form-control"
                    }
                }, {
                    label: "Consignee:",
                    name: "gate_record.consignee",
                    attr: {
                        id: 'consignee',
                        class: "form-control",
                        list:"customers_name",
                        maxlength: 255,
                    }
                }, {
                    label: "External Reference:",
                    name: "gate_record.external_reference",
                    fieldInfo: "Use Gate In Condition screen for damage details",
                    attr: {
                        id: "external_ref",
                        class: "form-control",
                        maxlength: 50,
                    }
                }, {
                    label: "Condition:",
                    name: "gate_record.cond",
                    type: "select",
                    attr: {
                        id: 'cond',
                        class: "form-control",
                        onchange: "GateIn.conditionCall()"
                    },
                    options: [
                        {label: "SOUND", value: "SOUND"},
                        {label: "NOT SOUND", value: "NOT SOUND"}
                    ]
                }, {
                    label: "Note:",
                    name: "gate_record.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Status:",
                    name: "gate_record.status",
                    attr: {
                        id: "full_stat",
                        class: "form-control"
                    }
                },
                {
                    label: "System Waybill",
                    name: "gate_record.sys_waybill",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "EIR/Waybill No:",
                    name: "gate_record.waybill",
                    attr: {
                        class: "form-control",
                        maxlength: 20,
                    }
                }, {
                    label: "User",
                    name: "gate_record.user_id",
                    attr: {
                        class: "form-control",
                    }
                }, {
                    label: "Date:",
                    name: "gate_record.date",
                    type: "datetime",
                    def: function () {
                        return new Date();
                    },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        editor.field('gate_record.sys_waybill').hide();
        editor.field('gate_record.status').hide();
        editor.field('gate_record.type').hide();
        editor.field('gate_record.user_id').hide();
        

        expressEditor = new $.fn.dataTable.Editor({
            ajax: "/api/gate_in/table",
            table: "#gate_in",
            fields: [{
                label: "Trade Type:",
                name: "trade",
                type: "select",
                attr: {
                    id: "trade_select",
                    class: "form-control"
                },
                options: [
                    {label: "EXPORT", value: 21}
                ]
            }, {
                label: "Container:",
                name: "gate_record.container_id",
                attr: {
                    maxlength: 11,
                    id: 'exportID',
                    class: "form-control"
                }
            }, {
                label: "Booking Number.",
                name: "book_number",
                attr: {
                    class: "form-control",
                    id: "book_numberID",
                    maxlength: 20,
                }
            },
                {
                    label: "Agency",
                    name: "agent",
                    attr: {
                        class: "form-control",
                        list: "agents",
                        id: "agentID",
                        maxlength: 150
                    }
                },
                {
                    label: "Content of Goods",
                    name: "goods_content",
                    type: "textarea",
                    attr: {
                        class: "form-control",
                        id: "goodsID"
                    }
                }, {
                    label: "Type:",
                    name: "gate_record.type",
                    def: 1
                },
                {
                    label: "SOC:",
                    name: "soc",
                    type: "select",
                    attr: {
                        id: 'soc_status',
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ]
                },
                {
                    label: "Depot:",
                    name: "gate_record.depot_id",
                    type: "select",
                    def: '3',
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Gate:",
                    name: "gate_record.gate_id",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Shipping Line:",
                    name: "shipping_line_id",
                    attr: {
                        id: "shippingLineID",
                        class: "form-control",
                        list: "lines"
                    }
                }, {
                    label: "ISO Type Code:",
                    name: "iso_code",
                    attr: {
                        id: "iso_code",
                        class: "form-control",
                        list: "iso_code"
                    }
                }, {
                    label: "IMDG:",
                    name: "imdg",
                    attr: {
                        id: "imdg",
                        class: "form-control",
                        list: "imdgs"
                    },
                }, {
                    label: "Full Status:",
                    name: "full_status",
                    type: "select",
                    attr: {
                        id: "full_stat",
                        class: "form-control"
                    },
                    options: [
                        {label: "YES (Laden)", value: 1},
                        {label: "NO", value: 0}
                    ],
                    def: 1
                }, {
                    label: "OOG:",
                    name: "oog",
                    type: "select",
                    attr: {
                        id: "oog",
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: 0},
                        {label: "YES", value: 1}
                    ]
                }, {
                    label: "Seal Number 1:",
                    name: "seal_number_1",
                    attr: {
                        id: "seal_number1",
                        class: "form-control",
                        maxlength: 20
                    },
                }, {
                    label: "Seal Number 2:",
                    name: "seal_number_2",
                    attr: {
                        id: "seal_number2",
                        class: "form-control",
                        maxlength: 20
                    },
                }, {
                    label: "Special Seal:",
                    name: "gate_record.special_seal",
                    attr: {
                        class: "form-control",
                        maxlength: 20,
                        id: "specialSealID"
                    }
                },
                {
                    label: "Vehicle:",
                    name: "gate_record.vehicle_id",
                    attr: {
                        list: "vehicles",
                        class: "form-control",
                        id: "vehicleID"
                    }
                }, {
                    label: "Driver:",
                    name: "gate_record.driver_id",
                    attr: {
                        list: "drivers",
                        class: "form-control",
                        id: "driverID"
                    }
                }, {
                    label: "Trucking Company:",
                    name: "gate_record.trucking_company_id",
                    attr: {
                        list: "companies",
                        class: "form-control",
                        id: "truckID"
                    }
                }, {
                    label: "Consignee:",
                    name: "gate_record.consignee",
                    attr: {
                        list:"customers_name",
                        id: 'consignee',
                        class: "form-control",
                        maxlength: 255
                    }
                }, {
                    label: "External Reference:",
                    name: "gate_record.external_reference",
                    fieldInfo: "Use Gate In Condition screen for damage details",
                    attr: {
                        id: "external_ref",
                        class: "form-control",
                        maxlength: 50
                    }
                }, {
                    label: "Condition:",
                    name: "gate_record.cond",
                    type: "select",
                    attr: {
                        id: 'cond',
                        class: "form-control",
                        onchange: "GateIn.conditionCall()"
                    },
                    options: [
                        {label: "SOUND", value: "SOUND"},
                        {label: "NOT SOUND", value: "NOT SOUND"}
                    ]
                }, {
                    label: "Note:",
                    name: "gate_record.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Status:",
                    name: "gate_record.status",
                    attr: {
                        id: "full_stat",
                        class: "form-control"
                    }
                }, {
                    label: "EIR/Waybill No:",
                    name: "gate_record.waybill",
                    attr: {
                        class: "form-control",
                        maxlength: 20
                    }
                }, {
                    label: "User",
                    name: "gate_record.user_id",
                    attr: {
                        class: "form-control",
                    }
                }, {
                    label: "Date:",
                    name: "gate_record.date",
                    type: "datetime",
                    def: function () {
                        return new Date();
                    },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        expressEditor.field('gate_record.status').hide();
        expressEditor.field('gate_record.type').hide();
        expressEditor.field('gate_record.user_id').hide();


        emptyEditor = new $.fn.dataTable.Editor({
            ajax: "/api/gate_in/table",
            table: "#gate_in",
            fields: [{
                label: "Trade Type:",
                name: "trade",
                type: "select",
                attr: {
                    id: "trade_select",
                    class: "form-control"
                },
                options: [
                    {label: "EMPTY", value: 70}
                ]
            }, {
                label: "Container:",
                name: "gate_record.container_id",
                attr: {
                    maxlength: 11,
                    id: 'emptyID',
                    class: "form-control"
                }
            }, 
                {
                    label: "Agency",
                    name: "agent",
                    attr: {
                        class: "form-control",
                        list: "agents",
                        id: "agentID",
                        maxlength: 150
                    }
                },
                 {
                    label: "Type:",
                    name: "gate_record.type",
                    def: 1
                },
                {
                    label: "SOC:",
                    name: "soc",
                    type: "select",
                    attr: {
                        id: 'soc_status',
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ]
                },
                {
                    label: "Depot:",
                    name: "gate_record.depot_id",
                    type: "select",
                    def: '2',
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Gate:",
                    name: "gate_record.gate_id",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Shipping Line:",
                    name: "shipping_line_id",
                    attr: {
                        id: "shippingLineID",
                        class: "form-control",
                        list: "lines"
                    }
                }, {
                    label: "ISO Type Code:",
                    name: "iso_code",
                    attr: {
                        id: "iso_code",
                        class: "form-control",
                        list: "iso_code"
                    }
                }, {
                    label: "Full Status:",
                    name: "full_status",
                    type: "select",
                    attr: {
                        id: "full_stat",
                        class: "form-control"
                    },
                    options: [
                        {label: "YES (Laden)", value: 1},
                        {label: "NO", value: 0}
                    ],
                    def: 0
                }, {
                    label: "OOG:",
                    name: "oog",
                    type: "select",
                    attr: {
                        id: "oog",
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: 0},
                        {label: "YES", value: 1}
                    ],
                    def: 0
                }, {
                    label: "Seal Number 1:",
                    name: "seal_number_1",
                    attr: {
                        id: "seal_number1",
                        class: "form-control",
                        maxlength: 20
                    },
                }, {
                    label: "Seal Number 2:",
                    name: "seal_number_2",
                    attr: {
                        id: "seal_number2",
                        class: "form-control",
                        maxlength: 20
                    },
                }, {
                    label: "Activity Type:",
                    name: "activity_type",
                    type: "select",
                    attr: {
                        id: "seal_number2",
                        class: "form-control",
                    },
                    options: [
                        {label: "Positioning", value: "Positioning"},
                        {label: "Drop-off", value: "Drop-off"}
                    ]
                }, {
                    label: "Special Seal:",
                    name: "gate_record.special_seal",
                    attr: {
                        class: "form-control",
                        maxlength: 20,
                        id: "specialSealID"
                    }
                },
                {
                    label: "Vehicle:",
                    name: "gate_record.vehicle_id",
                    attr: {
                        list: "vehicles",
                        class: "form-control",
                        id: "vehicleID"
                    }
                }, {
                    label: "Driver:",
                    name: "gate_record.driver_id",
                    attr: {
                        list: "drivers",
                        class: "form-control",
                        id: "driverID"
                    }
                }, {
                    label: "Trucking Company:",
                    name: "gate_record.trucking_company_id",
                    attr: {
                        list: "companies",
                        class: "form-control",
                        id: "truckID"
                    }
                }, {
                    label: "Consignee:",
                    name: "gate_record.consignee",
                    attr: {
                        list:"customers_name",
                        id: 'consignee',
                        class: "form-control",
                        maxlength: 255
                    }
                }, {
                    label: "Condition:",
                    name: "gate_record.cond",
                    type: "select",
                    attr: {
                        id: 'cond',
                        class: "form-control",
                        onchange: "GateIn.conditionCall()"
                    },
                    options: [
                        {label: "SOUND", value: "SOUND"},
                        {label: "NOT SOUND", value: "NOT SOUND"}
                    ]
                }, {
                    label: "Note:",
                    name: "gate_record.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Status:",
                    name: "gate_record.status",
                    attr: {
                        id: "full_stat",
                        class: "form-control"
                    }
                }, {
                    label: "EIR/Waybill No:",
                    name: "gate_record.waybill",
                    attr: {
                        class: "form-control",
                        maxlength: 20
                    }
                }, {
                    label: "User",
                    name: "gate_record.user_id",
                    attr: {
                        class: "form-control",
                    }
                }, {
                    label: "Date:",
                    name: "gate_record.date",
                    type: "datetime",
                    def: function () {
                        return new Date();
                    },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        emptyEditor.field('gate_record.status').hide();
        emptyEditor.field('gate_record.type').hide();
        emptyEditor.field('gate_record.user_id').hide();


        editor.on('initEdit', function() {
            if(editor.field('trade').val() == 21){
                editor.field('trade').enable();
            }
        });


        var container_check = false;

        expressEditor.on('preSubmit', function (e, o, action) {
            if (action !== 'remove') {
                var content = this.field('goods_content');
                var book_number = this.field('book_number');
                var agent = this.field('agent');
                var seal_no1 = this.field('seal_number_1');
                var seal_no2 = this.field('seal_number_2');
                var imdg = this.field('imdg');
                var consignee = this.field('gate_record.consignee');
                var regex = new RegExp("[a-z A-Z 0-9 -]+$");
                var container_no = this.field('gate_record.container_id');
                var vehicle = this.field('gate_record.vehicle_id');
                var driver = this.field('gate_record.driver_id');
                var truck = this.field('gate_record.trucking_company_id');
                var iso = this.field('iso_code');
                var shipping_line = this.field('shipping_line_id');

                if (!consignee.val()) {
                    consignee.error("Empty field");
                    var consignees = document.querySelector('#consignee');
                    consignees.scrollIntoView();
                }
                if (!truck.val()) {
                    truck.error('Empty field');
                    var truckID = document.querySelector('#truckID');
                    truckID.scrollIntoView();
                }
                if (!driver.val()) {
                    driver.error('Empty field');
                    var driverID = document.querySelector('#driverID');
                    driverID.scrollIntoView();
                }
                if (!vehicle.val()) {
                    vehicle.error('Empty field');
                    var vehicleID = document.querySelector('#vehicleID');
                    vehicleID.scrollIntoView();
                }
                if (!seal_no1.val()) {
                    seal_no1.error("Empty field");
                    var seal_number1 = document.querySelector('#seal_number1');
                    seal_number1.scrollIntoView();
                }
                if (!seal_no2.val()) {
                    seal_no2.error("Empty field");
                    var seal_number2 = document.querySelector('#seal_number2');
                    seal_number2.scrollIntoView();
                }
                if (!imdg.val()) {
                    imdg.error("Empty field");
                    var imdgs = document.querySelector('#imdg');
                    imdgs.scrollIntoView();
                }
                if (!iso.val()) {
                    iso.error('Empty field');
                    var iso_code = document.querySelector('#iso_code');
                    iso_code.scrollIntoView();
                }
                if (!shipping_line.val()) {
                    shipping_line.error("Empty field");
                    var shipping_line_id = document.querySelector('#shippingLineID');
                    shipping_line_id.scrollIntoView();
                }
                if (!content.val()) {
                    content.error("Empty field");
                    var contents = document.querySelector('#goodsID');
                    contents.scrollIntoView();
                }
                if (!agent.val()) {
                    agent.error("Empty field");
                    var agentID = document.querySelector('#agentID');
                    agentID.scrollIntoView();
                }
                if (!book_number.val()) {
                    book_number.error("Empty field");
                    var book_numberID = document.querySelector('#book_numberID');
                    book_numberID.scrollIntoView();
                }
                else if (!regex.test(book_number.val())) {
                    book_number.error("Booking Number must not contain symbols.");
                    var book_numberID1 = document.querySelector('#book_numberID');
                    book_numberID1.scrollIntoView();
                }
                if (container_no.error() || container_number_error) {
                    if (container_no.val().trim() == '') {
                        container_no.error('Empty field');
                    }
                    var exports = document.querySelector('#exportID');
                    exports.scrollIntoView();
                }

                if (this.inError() || !container_check) {
                    return false;
                }
            }
        });

        emptyEditor.on('initSubmit', function (e, o, action) {
            if (action !== 'remove') {
                var agent = this.field('agent');
                var seal_no1 = this.field('seal_number_1');
                var seal_no2 = this.field('seal_number_2');
                var consignee = this.field('gate_record.consignee');
                var regex = new RegExp("[a-z A-Z 0-9 -]+$");
                var container_no = this.field('gate_record.container_id');
                var vehicle = this.field('gate_record.vehicle_id');
                var driver = this.field('gate_record.driver_id');
                var truck = this.field('gate_record.trucking_company_id');
                var iso = this.field('iso_code');
                var shipping_line = this.field('shipping_line_id');

                if (!container_no.val()) {
                    container_no.error("Empty field");
                    var container_nos = document.querySelector('#emptyID');
                    container_nos.scrollIntoView();
                }
                if (!consignee.val()) {
                    consignee.error("Empty field");
                    var consignees = document.querySelector('#consignee');
                    consignees.scrollIntoView();
                }
                if (!truck.val()) {
                    truck.error('Empty field');
                    var truckID = document.querySelector('#truckID');
                    truckID.scrollIntoView();
                }
                if (!driver.val()) {
                    driver.error('Empty field');
                    var driverID = document.querySelector('#driverID');
                    driverID.scrollIntoView();
                }
                if (!vehicle.val()) {
                    vehicle.error('Empty field');
                    var vehicleID = document.querySelector('#vehicleID');
                    vehicleID.scrollIntoView();
                }
                if (!seal_no1.val()) {
                    seal_no1.error("Empty field");
                    var seal_number1 = document.querySelector('#seal_number1');
                    seal_number1.scrollIntoView();
                }
                if (!seal_no2.val()) {
                    seal_no2.error("Empty field");
                    var seal_number2 = document.querySelector('#seal_number2');
                    seal_number2.scrollIntoView();
                }
             
                if (!iso.val()) {
                    iso.error('Empty field');
                    var iso_code = document.querySelector('#iso_code');
                    iso_code.scrollIntoView();
                }
                if (!shipping_line.val()) {
                    shipping_line.error("Empty field");
                    var shipping_line_id = document.querySelector('#shippingLineID');
                    shipping_line_id.scrollIntoView();
                }
             
                if (!agent.val()) {
                    agent.error("Empty field");
                    var agentID = document.querySelector('#agentID');
                    agentID.scrollIntoView();
                }
          
                if (container_no.error() || container_number_error) {
                    if (container_no.val().trim() == '') {
                        container_no.error('Empty field');
                    }
                    var empty = document.querySelector('#emptyID');
                    empty.scrollIntoView();
                }
             
                if (this.inError()) {
                    return false;
                }
            }
        });


        editor.on('initCreate', function () {
            editor.field("gate_record.container_id").enable();
            editor.field('gate_record.container_id').input().attr('id', 'container');
            editor.field("trade").enable();
        });

        editor.on('open', function () {
            $('#container').focusout(function () {
                var number = editor.field('gate_record.container_id').val();
                GateIn.getContainerInfo(number);
            });
        });

        editor.on('preSubmit', function (e, o, action) {
            var trade_type = this.field('trade').val();

            var error_check = false;

            if (action === 'create' && trade_type == 21) {
                var seal_no1 = this.field('seal_number_1');
                var seal_no2 = this.field('seal_number_2');
                var container_number = $('#container').val();
                var booking_number = this.field('book_number');
                var consignee = this.field('gate_record.consignee');
                var vehicle = this.field('gate_record.vehicle_id');
                var driver = this.field('gate_record.driver_id');
                var truck = this.field('gate_record.trucking_company_id');
                var shipping_line = this.field('shipping_line');

                $.ajax({
                    url:"/api/container/update_export_seal",
                    type: "POST",
                    data: {
                        sno1: seal_no1.val(),
                        sno2: seal_no2.val(),
                        ctno: container_number,
                        trad: trade_type,
                        bkno: booking_number.val(),
                        cons: consignee.val(),
                        shid: shipping_line.val(),
                        vech: vehicle.val(),
                        drvr: driver.val(),
                        trk: truck.val()
                    },
                    success: function(data){
                        var data = JSON.parse(data);
                        if (data.st == 164) {
                            if (data.err.seal1) {
                                seal_no1.error("Empty field");
                                var seal_number1 = document.querySelector('#seal_number1');
                                seal_number1.scrollIntoView();
                            }
                            else if (data.err.sea3 == 'senu0') {
                                seal_no1.error("Seal number 1 cannot be NA");
                                var seal_number1 = document.querySelector('#seal_number1');
                                seal_number1.scrollIntoView();
                            }
                            else if (data.err.sea1 == 'senu1') {
                                seal_no1.error("Seal number 1 must not contain symbols");
                                var seal_number1 = document.querySelector('#seal_number1');
                                seal_number1.scrollIntoView();
                            }
                            if (data.err.seal2) {
                                seal_no2.error("Empty field");
                                var seal_number2 = document.querySelector('#seal_number2');
                                seal_number2.scrollIntoView();
                            }
                            if (data.err.seal4 == 'senum') {
                                seal_no2.error("Seal number 2 cannot be NA");
                                var seal_number2 = document.querySelector('#seal_number2');
                                seal_number2.scrollIntoView();
                            }
                            else if (data.err.sea2 == 'senu2') {
                                seal_no2.error("Seal number 2 must not contain symbols");
                                var seal_number2 = document.querySelector('#seal_number2');
                                seal_number2.scrollIntoView();
                            }
                            if (data.err.bknu) {
                                booking_number.error("Empty field");
                                var book_num = document.querySelector('#book_numberID');
                                book_num.scrollIntoView();
                            }
                            else if (data.err.bkerr == 'bkn_err') {
                                booking_number.error("Booking Number must not contains symbols");
                                var book_num = document.querySelector('#book_numberID');
                                book_num.scrollIntoView();
                            }
                            if (data.err.conss) {
                                consignee.error("Empty field");
                                var consig = document.querySelector('#consignee');
                                consig.scrollIntoView();
                            }
                            if (data.err.ship) {
                                shipping_line.error("Empty field");
                                var liner = document.querySelector('#line');
                                liner.scrollIntoView();
                            }
                            else if(data.err.eship == 'ersh'){
                                shipping_line.error("Shipping Line does not exist");
                                var liner = document.querySelector('#line');
                                liner.scrollIntoView();
                            }
                            if (data.err.vch) {
                                vehicle.error("Empty field");
                                var vehicl = document.querySelector('#vehicleID');
                                vehicl.scrollIntoView();
                            }
                            if (data.err.drv) {
                                driver.error("Empty field");
                                var drive = document.querySelector('#driverID');
                                drive.scrollIntoView();
                            }
                            if (data.err.truk) {
                                truck.error("Empty field");
                                var trucker = document.querySelector('#truckID');
                                trucker.scrollIntoView();
                            }
                        
                        }
                        else if (data.st == 265) {
                            error_check = true;
                            return error_check;
                        }
                    },
                    error: function(){
                        alert('something went wrong');
                    }
                });
                
            }

            if (action === 'edit' && (trade_type == 21 || trade_type == 70)) {
                var table = $('#gate_in').DataTable();
                var rowData = table.row({selected: true}).data();
                var container_number = rowData['gate_record']['container_id'];
                var number = container_number.toString();
                var gate_record = rowData['gid'];
                
                var container_no = this.field('gate_record.container_id');
                var seal_no1 = this.field('seal_number_1');
                var seal_no2 = this.field('seal_number_2');
                var imdg = this.field('imdg');
                var shipping_line = this.field('shipping_line');
                var oog_status = this.field('oog');
                var full_status = this.field('full_status');
                var soc_status = this.field('soc');
                var book_number = this.field('book_number');
                
                $.ajax({
                    url:"/api/container/edit_export_container",
                    type:"POST",
                    async: false,
                    data:{
                        ctno: container_no.val(),
                        imdg: imdg.val(),
                        fstat: full_status.val(),
                        soc: soc_status.val(),
                        seal1: seal_no1.val(),
                        seal2: seal_no2.val(),
                        shid: shipping_line.val(),
                        oog: oog_status.val(),
                        rctno: number,
                        bkno: book_number.val() !='' ? book_number.val() : '',
                        gid: gate_record,
                        trad: trade_type
                    },
                    success: function(data){
                        var data = JSON.parse(data);
                        if (data.st == 161) {
                            if (data.err.seal1) {
                                seal_no1.error("Empty field");
                                var seal_number1 = document.querySelector('#seal_number1');
                                seal_number1.scrollIntoView();
                            }
                            else if (data.err.sea1 == 'senu1') {
                                seal_no1.error("Seal number 1 must not contain symbols");
                                var seal_number1 = document.querySelector('#seal_number1');
                                seal_number1.scrollIntoView();
                            }
                            if (data.err.seal2) {
                                seal_no2.error("Empty field");
                                var seal_number2 = document.querySelector('#seal_number2');
                                seal_number2.scrollIntoView();
                            }
                            else if (data.err.sea2 == 'senu2') {
                                seal_no2.error("Seal number 2 must not contain symbols");
                                var seal_number2 = document.querySelector('#seal_number2');
                                seal_number2.scrollIntoView();
                            }
                            if (data.err.bknu) {
                                book_number.error("Empty field");
                                var book_num = document.querySelector('#book_numberID');
                                book_num.scrollIntoView();
                            }
                            else if (data.err.bkerr == 'bkn_err') {
                                book_number.error("Booking Number must not contains symbols");
                                var book_num = document.querySelector('#book_numberID');
                                book_num.scrollIntoView();
                            }
                            if (data.err.img) {
                                if (!imdg.val()) {
                                    imdg.error("Empty field");
                                }
                                else {
                                    imdg.error("IMDG Code does not exist");
                                }
                                var imdgs = document.querySelector('#imdg');
                                imdgs.scrollIntoView();
                            }
                            if (data.err.iso) {
                                if (!iso.val()) {
                                    iso.error("Empty field");
                                }
                                else {
                                    iso.error("ISO Code does not exist");
                                }
                                var iso_code = document.querySelector('#iso_code');
                                iso_code.scrollIntoView();
                            }
                            if (data.err.sline) {
                                if (!imdg.val()) {
                                    shipping_line.error("Empty field");
                                }
                                else {
                                    shipping_line.error("Shipping Line does not exist");
                                }
                                var shipping_line_id = document.querySelector('#line');
                                shipping_line_id.scrollIntoView();
                            }
                            if(data.err.cnerr){
                                container_no.error("Empty field");
                                var container = document.querySelector('#container');
                                container.scrollIntoView();
                            }
                            else if (data.err.cner == 'ctn_err') {
                                container_no.error("Container number cannot contain symbols");
                                var container = document.querySelector('#container');
                                container.scrollIntoView();
                            }
                            if(data.err.clen){
                                container_no.error("Container number must not be less that 11 charactors.");
                                var container = document.querySelector('#container');
                                container.scrollIntoView();
                            }
                            if (data.err.trter) {
                                container_no.error("Container is not EXPORT trade type");
                                var container = document.querySelector('#container');
                                container.scrollIntoView();
                            }
                            if(data.err.flag){
                                container_no.error("Container has been flagged");
                                var container = document.querySelector('#container');
                                container.scrollIntoView(); 
                            }
                        
                    }
                    else if (data.st == 261) {
                        error_check = true;
                    }
                },
                error: function(){
                    alert("Something went wrong");
                }
            });
            return error_check;
            }
            
        });

    
        var container_number_error = false;

        expressEditor.on('initSubmit', function (e, o, action) {
            container_number_error = false;
            var trade_type = this.field('trade');
            var container_no = this.field('gate_record.container_id');
            var agent = this.field('agent');
            var content = this.field('goods_content');
            var book_number = this.field('book_number');
            var full_status = this.field('full_status');
            var iso = this.field('iso_code');
            var oog = this.field('oog');
            var soc = this.field('soc');
            var seal_no1 = this.field('seal_number_1');
            var seal_no2 = this.field('seal_number_2');
            var imdg = this.field('imdg');
            var consignee = this.field('gate_record.consignee');
            var shipping_line = this.field('shipping_line_id');
            var special_seal = this.field('gate_record.special_seal');
            $.ajax({
                url: "/api/container/add_export_container",
                type: "POST",
                async: false,
                data: {
                    trty: trade_type.val(),
                    ctno: container_no.val(),
                    agnt: agent.val(),
                    ctnt: content.val(),
                    imdg: imdg.val(),
                    bkno: book_number.val(),
                    fstat: full_status.val(),
                    iso: iso.val(),
                    oog: oog.val(),
                    soc: soc.val(),
                    seal1: seal_no1.val(),
                    seal2: seal_no2.val(),
                    cons: consignee.val(),
                    shid: shipping_line.val(),
                    spel: special_seal.val()
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    if (data.st == 160) {
                        if (data.err.cons) {
                            consignee.error("Empty field");
                            var consignees = document.querySelector('#consignee');
                            consignees.scrollIntoView();
                        }
                        if (data.err.cser) {
                            consignee.error(data.err.cser);
                            var consignees = document.querySelector('#consignee');
                            consignees.scrollIntoView();
                        }
                        if (data.err.seal1) {
                            seal_no1.error("Empty field");
                            var seal_number1 = document.querySelector('#seal_number1');
                            seal_number1.scrollIntoView();
                        }
                        else if (data.err.sea1 == 'senu1') {
                            seal_no1.error("Seal number 1 must not contain symbols");
                            var seal_number1 = document.querySelector('#seal_number1');
                            seal_number1.scrollIntoView();
                        }
                        if (data.err.seal2) {
                            seal_no2.error("Empty field");
                            var seal_number2 = document.querySelector('#seal_number2');
                            seal_number2.scrollIntoView();
                        }
                        else if (data.err.sea2 == 'senu2') {
                            seal_no2.error("Seal number 2 must not contain symbols");
                            var seal_number2 = document.querySelector('#seal_number2');
                            seal_number2.scrollIntoView();
                        }
                        if (data.err.img) {
                            if (!imdg.val()) {
                                imdg.error("Empty field");
                            }
                            else {
                                imdg.error("IMDG Code does not exist");
                            }
                            var imdgs = document.querySelector('#imdg');
                            imdgs.scrollIntoView();
                        }
                        if (data.err.sline) {
                            if (!imdg.val()) {
                                shipping_line.error("Empty field");
                            }
                            else {
                                shipping_line.error("Shipping Line does not exist");
                            }
                            var shipping_line_id = document.querySelector('#shippingLineID');
                            shipping_line_id.scrollIntoView();
                        }
                        if (data.err.iso) {
                            if (!iso.val()) {
                                iso.error("Empty field");
                            }
                            else {
                                iso.error("ISO Code does not exist");
                            }
                            var iso_code = document.querySelector('#iso_code');
                            iso_code.scrollIntoView();
                        }
                        if (data.err.agnt) {
                            if (!agent.val()) {
                                agent.error("Empty field");
                            }
                            else {
                                agent.error("Agent does not exist");
                            }
                            var agentID = document.querySelector('#agentID');
                            agentID.scrollIntoView();
                        }
                        if (data.err.bnum) {
                            book_number.error("Empty field");
                            var book_numberID = document.querySelector('#book_numberID');
                            book_numberID.scrollIntoView();
                        }
                        else if (data.err.bkn == 'bokn') {
                            book_number.error("Booking number must not contain symbols.");
                            var book_numberID = document.querySelector('#book_numberID');
                            book_numberID.scrollIntoView();
                        }
                        if (data.err.spesc == "spelss") {
                            special_seal.error("Special Seal must not contain symbols.");
                            var specialSealID = document.querySelector('#specialSealID');
                            specialSealID.scrollIntoView();
                        }
                        if (data.err.cnum != undefined) {
                            if (data.err.cnum1 == "ex") {
                                container_no.error("Container already exist.");
                            }
                            if (data.err.clens == "len") {
                                container_no.error("Container number must not be less that 11 charactors.");
                            }
                            if (data.err.cnum == "sym") {
                                container_no.error("Container number must not contain symbols.");
                            }
                            if (container_no.error() || data.err.cnum == "empty") {
                                if (container_no.val().trim() == '') {
                                    container_no.error('Empty field');
                                }
                            }
                           
                            var exports = document.querySelector('#exportID');
                            exports.scrollIntoView();
                            container_number_error = true;
                        }
                        if(data.err.trte == "trer"){
                            container_no.error("Container is not EXPORT trade type");
                            var container = document.querySelector('#exportID');
                            container.scrollIntoView();
                        }
                        if(data.err.isoer == "iso_er"){
                            iso.error("Container ISO Code is "+ data.err.isod);
                            var iso_code = document.querySelector('#iso_code');
                            iso_code.scrollIntoView();
                        }
                        if(data.err.flag == "flagged"){
                            container_no.error("Container has been flagged");
                            var container = document.querySelector('#exportID');
                            container.scrollIntoView();
                        }
                    }
                    else if (data.st == 260) {
                        container_check = true;
                    }

                },
                error: function () {
                    alert("Something went wrong");
                }
            });

            return false;
        });

        emptyEditor.on('preSubmit', function (e, o, action) {
            container_number_error = false;
            var trade_type = this.field('trade');
            var container_no = this.field('gate_record.container_id');
            var agent = this.field('agent');
            var full_status = this.field('full_status');
            var iso = this.field('iso_code');
            var oog = this.field('oog');
            var soc = this.field('soc');
            var seal_no1 = this.field('seal_number_1');
            var seal_no2 = this.field('seal_number_2');
            var activity_type = this.field('activity_type');
            var consignee = this.field('gate_record.consignee');
            var shipping_line = this.field('shipping_line_id');
            var special_seal = this.field('gate_record.special_seal');
            $.ajax({
                url: "/api/container/add_empty_container",
                type: "POST",
                async: false,
                data: {
                    trty: trade_type.val(),
                    ctno: container_no.val(),
                    agnt: agent.val(),
                    fstat: full_status.val(),
                    iso: iso.val(),
                    oog: oog.val(),
                    soc: soc.val(),
                    seal1: seal_no1.val(),
                    seal2: seal_no2.val(),
                    activity: activity_type.val(),
                    cons: consignee.val(),
                    shid: shipping_line.val(),
                    spel: special_seal.val()
                },
                success: function (data) {
                    var data = JSON.parse(data);
                    if (data.st == 160) {
                        if (data.err.cons) {
                            consignee.error("Empty field");
                            var consignees = document.querySelector('#consignee');
                            consignees.scrollIntoView();
                        }
                        if (data.err.cser) {
                            container_no.error(data.err.cser);
                            var empty = document.querySelector('#emptyID');
                            empty.scrollIntoView();
                        }
                        if (data.err.seal1) {
                            seal_no1.error("Empty field");
                            var seal_number1 = document.querySelector('#seal_number1');
                            seal_number1.scrollIntoView();
                        }
                        else if (data.err.sea1 == 'senu1') {
                            seal_no1.error("Seal number 1 must not contain symbols");
                            var seal_number1 = document.querySelector('#seal_number1');
                            seal_number1.scrollIntoView();
                        }
                        if (data.err.seal2) {
                            seal_no2.error("Empty field");
                            var seal_number2 = document.querySelector('#seal_number2');
                            seal_number2.scrollIntoView();
                        }
                        else if (data.err.sea2 == 'senu2') {
                            seal_no2.error("Seal number 2 must not contain symbols");
                            var seal_number2 = document.querySelector('#seal_number2');
                            seal_number2.scrollIntoView();
                        }
                        if (data.err.img) {
                            if (!imdg.val()) {
                                imdg.error("Empty field");
                            }
                            else {
                                imdg.error("IMDG Code does not exist");
                            }
                            var imdgs = document.querySelector('#imdg');
                            imdgs.scrollIntoView();
                        }
                        if (data.err.sline) {
                            if (!imdg.val()) {
                                shipping_line.error("Empty field");
                            }
                            else {
                                shipping_line.error("Shipping Line does not exist");
                            }
                            var shipping_line_id = document.querySelector('#shippingLineID');
                            shipping_line_id.scrollIntoView();
                        }
                        if (data.err.iso) {
                            if (!iso.val()) {
                                iso.error("Empty field");
                            }
                            else {
                                iso.error("ISO Code does not exist");
                            }
                            var iso_code = document.querySelector('#iso_code');
                            iso_code.scrollIntoView();
                        }
                        if (data.err.agnt) {
                            if (!agent.val()) {
                                agent.error("Empty field");
                            }
                            else {
                                agent.error("Agent does not exist");
                            }
                            var agentID = document.querySelector('#agentID');
                            agentID.scrollIntoView();
                        }
                        if (data.err.bnum) {
                            book_number.error("Empty field");
                            var book_numberID = document.querySelector('#book_numberID');
                            book_numberID.scrollIntoView();
                        }
                        else if (data.err.bkn == 'bokn') {
                            book_number.error("Booking number must not contain symbols.");
                            var book_numberID = document.querySelector('#book_numberID');
                            book_numberID.scrollIntoView();
                        }
                        if (data.err.spesc == "spelss") {
                            special_seal.error("Special Seal must not contain symbols.");
                            var specialSealID = document.querySelector('#specialSealID');
                            specialSealID.scrollIntoView();
                        }
                        if (data.err.cnum != undefined) {
                            if (data.err.cnum1 == "ex") {
                                container_no.error("Container already exist.");
                            }
                            if (data.err.clens == "len") {
                                container_no.error("Container number must not be less that 11 charactors.");
                            }
                            if (data.err.cnum == "sym") {
                                container_no.error("Container number must not contain symbols.");
                            }
                            if (container_no.error() || data.err.cnum == "empty") {
                                if (container_no.val().trim() == '') {
                                    container_no.error('Empty field');
                                }
                            }
                            var exports = document.querySelector('#exportID');
                            exports.scrollIntoView();
                            container_number_error = true;
                        }


                    }
                    if (data.st == 260) {
                        $.ajax({
                            url: "/api/container/add_empty_activity",
                            type: "POST",
                            async: false,
                            data: {
                                ctno: container_no.val(),
                                activity: activity_type.val(),
                            },
                            success: function (data) {
                                var data = JSON.parse(data);
                                console.log(data);
                                if (data.st != 360) {
                                    alert("Something went wrong");
                                }
                            },
                        });
                    }
                    else if (data.st != 260) {
                        return true;
                    }

                },
                error: function () {
                    alert("Something went wrong");
                }
            });

        });

        add_gatein_condition = new $.fn.dataTable.Editor({
            ajax: "/api/gate_condition/table",
            fields: [
                {
                    label: "ID",
                    name: "gate_record_container_condition.gate_record",
                    attr: {
                        class: "form-control",
                        id: "gate_record_id"
                    }
                },
                {
                    label: "Container:",
                    name: "container",
                    attr: {
                        id: "container_no",
                        maxlength: 11,
                        list: "gcontainers",
                        class: "form-control",
                        disabled: true
                    },
                },
                {
                    label: "Container Section:",
                    name: "gate_record_container_condition.container_section",
                    type: "select",
                    options: [
                        {label: "Roof Bows", value: "1"},
                        {label: "Floor", value: "2"},
                        {label: "Roof", value: "3"},
                        {label: "Corner Castings", value: "4"},
                        {label: "Lashing Points", value: "5"},
                        {label: "Side Panels", value: "6"},
                        {label: "Bottom Side Rail", value: "7"},
                        {label: "Stiffened", value: "8"},
                        {label: "Cross Members", value: "9"},
                        {label: "Top Side Rail", value: "10"},
                        {label: "Front End Wall", value: "11"},
                        {label: "Front End Frame including Posts", value: "12"},
                        {label: "Door Hinges", value: "13"},
                        {label: "Lining", value: "14"},
                        {label: "Door Panels", value: "15"},
                        {label: "Door Frame including Pots", value: "16"},
                        {label: "Gooseneck Tunnel", value: "17"},
                        {label: 'Door Locking Bars', value: "18"}
                    ],
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Damage Type:",
                    name: "gate_record_container_condition.damage_type",
                    type: "select",
                    attr: {
                        id: 'damage_type',
                        class: "form-control"
                    },
                    options: [
                        {label: "Pushed Out", value: "1"},
                        {label: "B-Broken", value: "2"},
                        {label: "Hole", value: "3"},
                        {label: "Pushed In", value: "4"},
                        {label: "Missing", value: "5"},
                        {label: "Stained", value: "6"},
                        {label: "Cut", value: "7"},
                        {label: "Dent", value: "8"},
                        {label: "Bent", value: "9"},
                        {label: 'Corrosion', value: "10"}
                    ]
                }, {
                    label: "Damage Severity:",
                    name: "gate_record_container_condition.damage_severity",
                    type: "select",
                    attr: {
                        id: 'damage_severity',
                        class: "form-control"
                    },
                    options: [
                        {label: "LOW", value: "LOW"},
                        {label: "MEDIUM", value: "MEDIUM"},
                        {label: 'HIGH', value: "HIGH"}
                    ]
                }, {
                    label: "Note:",
                    name: "gate_record_container_condition.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        editor.on('edit', function(){
            var table = $('#gate_in').DataTable();
            var rowData = table.row({selected: true}).data();
            var container_number = rowData['gate_record']['container_id'];
            var number = container_number.toString();

            var container_number_field = this.field('gate_record.container_id').val();
            if(number != container_number_field){
                $.ajax({
                    url: "/api/container/update_container_gate_status",
                    type: "POST",
                    data:{
                        cnum: container_number_field,
                        num: number
                    },
                    success: function(data){
    
                    },
                    error: function(){
                        alert('something went wrong');
                    }
                });
            }
          
        });

        add_gatein_condition.on('submitSuccess', function () {
            TableRfresh.freshTable('gate_in');
        });

        add_gatein_condition.field('gate_record_container_condition.gate_record').hide();

        editor.on('create', function () {
            var container_no = $('#container').val();
            var seal_number1 = $('#seal_number1').val();
            var seal_number2 = $('#seal_number2').val();

            $.ajax({
                url: '/api/container/update_container_seals',
                type: 'POST',
                data: {
                    ctno: container_no,
                    seal1: seal_number1,
                    seal2: seal_number2
                },
                success: function (data) {

                },
                error: function () {
                    alert('something went wrong');
                }
            });

        
        });


        editor.on('submitComplete', function (e, json, data, action) {
            if (action === 'remove') {
                var status = json.cancelled;
                if (status.length > 0) {
                    Modaler.dModal('Gate In Deletion Error', 'Cannot delete gate in record. It has already been gated in.');
                }
            }
        });

        $('#gate_in').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/gate_in/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [{type: 'date', 'targets': [16]}, {"searchable": false, "targets": 20}
            ],
            order: [[10, 'desc']],
            columns: [
                {data: "gid", visible: false},
                {data: "ctnum"},
                {data: "isoc"},
                {data: "vyref"},
                {data: "typ", visible: false},
                {data: "dpgt"},
                {data: "dpname", visible: false},
                {data: "vhnm"},
                {data: "vhdr"},
                {data: "trnm"},
                {data: "gate_record.date"},
                {data: "icsl1", visible: false},
                {data: "icsl2", visible: false},
                {data: "seal1", visible: false},
                {data: "seal2", visible: false},
                {data: "gate_record.special_seal", visible: false},
                {data: "good", visible: false},
                { data: "booking.act", visible:false },
                {data: "gate_record.cond"},
                {data: "gate_record.note", visible: false},
                {
                    data: null,
                    render: function (data, type, row) {

                        var gated_record = "";

                        if (data.gate_record.cond == "NOT SOUND") {
                            gated_record += "<a href='#' onclick='GateIn.conditionGate(" + data.id + ", \"" + data.ctnum + "\")' class='check_cond'>Add condition</a><br/>";
                            gated_record += '<a id="check_cond" class="display_box" href="#" onclick="GateIn.conditionAlert(' + data.id + ',' + '\'' + data.ctnum + '\'' + ')">Check Condition</a><br/>';

                        }
                        if (data.stat == 0) {
                            gated_record += "<a href='#'  onclick='GateIn.gateContainer(" + data.id + ", \"" + data.ctnum + "\")'>Gate In</a></br>";
                        }
                        if (data.stat == 1) {
                            gated_record += "<a href='/api/GateWaybill/portxPdf/" + data.id + "' target='_blank' class='way_bill'>Waybill</a></br>";
                        }

                        return gated_record;
                    }
                },
                {data: "gate_record.consignee", visible: false},
                {data: "gate_record.external_reference", visible: false},
                {data: "gate_record.pdate", visible: false},
                {data: "gate_record.user_id", visible: false},
            ],
            select: true,
            buttons: GateInHelper.permissionButtonBuilder(editor, expressEditor, 'Gate In',)

        });

        editor.on('onInitEdit', function () {
            var table = $('#gate_in').DataTable();
            var rowData = table.row({selected: true}).data();
            var container_number = rowData['gate_record']['container_id'];
            var gate_record_id = rowData['gid'];
            var number = container_number.toString();        
           
           
            GateIn.getTradetypeInfo(gate_record_id);
            GateIn.invoiceContainer(gate_record_id);
        });
    }
}


var GateOut= {

    conditionGate: function(id, number){
        gate_condition.create( {
            title: 'Gate Out Condition Container',
            buttons: 'Add',
        } );

        var container = document.getElementById('container_no');
        container.value = number;
        var gate_record = document.getElementById('gate_record_id');
        gate_record.value = id;
    },

    conditionAlert:function(id, container) {
        $('#container_number').text(container);

        var header = container;
        var body = "<table id=\"container_condition\" class=\"display table-responsive\">" +
            "<thead><tr><th>Container Section </th><th>Damage Type </th><th>Damage Severity </th><th>Note </th></tr></thead>" +
            "</table>";

        CondModal.cModal(header, body);

        ContainerCondition.iniTable(id,container);
    },

    gateContainerOut: function(id, number) {
        var container = number;
        var result;

        var url = "/api/gate_out/gate_container_out";
        var gate_container = document.getElementById('gate_container');
        var header = '';
        var body = '';
        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if(request.readyState == 4 && request.status == 200) {
                result = JSON.parse(request.responseText);
                if (result.st == 132){
                    header = "Container Flagged";
                    body = "Container Number " + container + " is flagged";
                    Modaler.dModal(header,body);
                }
                else if (result.st == 137) {
                    header = "Container Unconditioned";
                    body = "The container needs to be conditioned before gating out";
                    Modaler.dModal(header,body);
                }
                else if (result.st == 138) {
                    header = "Container Error";
                    body = "Container already gated out";
                    Modaler.dModal(header,body);
                }
                else if (result.st == 131){
                    header = "Deferral Date Exceeded";
                    body = "Container defer date exceeded it's date";
                    Modaler.dModal(header,body);
                }
                else if (result.st == 133){
                    header = 'Unpaid Invoice';
                    body = "Container has unpaid invoices: " + result.num;
                    Modaler.dModal(header,body);
                }
                else if (result.st == 134){
                    header = 'Container Not Invoiced';
                    body = "Container has not been invoiced";
                    Modaler.dModal(header,body);
                }
                else if (result.st == 135){
                    header = 'Uninvoiced Actvities Pending';
                    body = "Container has uninvoiced activities";
                    Modaler.dModal(header,body);
                }
                else if (result.st == 136){
                    header = 'Storage Changes Pending';
                    body = "Container has uncharged storage days. Invoice Numbder: " + result.num;
                    Modaler.dModal(header,body);
                }
                else if (result.st == 139){
                    header = 'Container Gate Out Error';
                    body = "Container in stack, cannot gate out";
                    Modaler.dModal(header,body);
                }
                else if (result.st == 232){
                    header = "Gated Out";
                    body = "Container Number " + container + " gated out";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('gate_out');
                }
            }
            else {
                gate_container.innerText = "Gate-Out Failed!";
            }
        };

        request.send("id=" + id);

        var data_container = number;
        var data_url = "/api/gate_out/get_container_list";
        var data_request = new XMLHttpRequest();
        data_request.open("POST", data_url, true);
        data_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        data_request.onload = function() {
            if(data_request.readyState == 4 && data_request.status == 200) {
                var response = JSON.parse(data_request.responseText);
                var datalist = document.getElementById('container_out');
                datalist.innerHTML = '';
                for(var data in response) {
                    var option = document.createElement('option');
                    option.innerText = response[data];
                    datalist.appendChild(option);
                }
            }
        };
        data_request.send("data=" + data_container);
    },

    getDrivers: function(number){
        var driver_list = document.getElementById('driver_out');
        driver_list.innerHTML = '';
        var vehicle_list = document.getElementById('vehicle_out');
        vehicle_list.innerHTML = '';

        var container = $('#container').val();
        if (container == undefined)
            container = number;
        if (container) {
            $.ajax({
                type: 'POST',
                url: '/api/gate_out/get_let_pass_details',
                data: {num: container},
                success: function (data) {
                    var result = $.parseJSON(data);
                    var drivers = result.drv;
                    var vehicles = result.veh;

                    if (drivers != undefined && vehicles != undefined && drivers.length > 0 && vehicles.length > 0) {
                        var driver_list = document.getElementById('driver_out');
                        driver_list.innerHTML = '';
                        for (var driver in drivers) {
                            var option = document.createElement('option');
                            option.innerText = drivers[driver];
                            driver_list.appendChild(option);
                        }

                        var vehicle_list = document.getElementById('vehicle_out');
                        vehicle_list.innerHTML = '';
                        for (var vehicle in vehicles) {
                            var option = document.createElement('option');
                            option.innerText = vehicles[vehicle];
                            vehicle_list.appendChild(option);
                        }
                    }

                },
                error: function () {
                    alert('Could not get Let Pass details.');
                }
            });
        }
    },

    

    getAct: function (number) {
        var act = document.getElementById('act');
        // driver_list.innerHTML = '';
        // var vehicle_list = document.getElementById('vehicle_out');
        // vehicle_list.innerHTML = '';

        var container = $('#container').val();
        if (container == undefined)
            container = number;
        if (container) {
            $.ajax({
                type: 'POST',
                url: '/api/gate_out/getAct',
                data: {num: container},
                success: function (data) {
                    var result = $.parseJSON(data);
                    var actData = result.act;
                    // var vehicles = result.veh;

                    if (actData != undefined ) {
                        act.value = actData;
                    }

                },
                error: function () {
                    alert('Could not get ACT for container.');
                }
            });
        }
    },

    getTrucks:function(number){
        var container = $('#container').val();
        if (container == undefined)
            container = number;
        if(container){
            $.ajax({
                url:"/api/gate_out/get_trucks",
                type:"POST",
                data:{
                    cnum:container
                },
                success:function(data){
                    var result = JSON.parse(data);
                    $('#vehicle').val(result);
                }
            });
        }
       
    },
    


    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/gate_out/table",
            table: "#gate_out",
            template:"#customForm",
            fields: [{
                label: "Container:",
                name: "gate_record.container_id",
                attr: {
                    class: "form-control",
                    list:"container_out",
                    onselect:"GateOut.getDrivers();GateOut.getAct();GateOut.getTrucks()",
                    onkeyup: "GateOut.getDrivers();GateOut.getAct();GateOut.getTrucks()",
                    onchange:"GateOut.getDrivers();GateOut.getAct();GateOut.getTrucks()",
                    maxlength: 11,
                    id: "container"
                }
            },{
                label: "Trade Type:",
                name: "trade_type.name"
            },{
                label: "Type:",
                name: "gate_record.type",
                def: 2
            },{
                label: "User",
                name: "gate_record.user_id",
                attr: {
                    class: "form-control",
                }
            },
                {
                    label: "Gate:",
                    name: "gate_record.gate_id",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Depot:",
                    name: "gate_record.depot_id",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Truck:",
                    name: "gate_record.vehicle_id",
                    attr: {
                        list:"vehicle_out",
                        id:"vehicle",
                        class: "form-control"
                    }
                }, {
                    label: "Trucking Company:",
                    name: "gate_record.trucking_company_id",
                    attr: {
                        maxlength: 100,
                        list: "companies",
                        class: "form-control"
                    }
                }, {
                    label: "Driver:",
                    name: "gate_record.driver_id",
                    attr: {
                        list:"driver_out",
                        class: "form-control"
                    }
                },{
                    label:"ACT:",
                    name:"booking.act",
                    type:"select",
                    options:[
                        {label:"Evacuation to Port", value:"Evacuation to Port"},
                        {label:"Evacuation to Depot", value:"Evacuation to Depot"},
                        {label:"Pickup (EXP)'", value:"Pickup (EXP)'"},
                        {label:"N/A", value:"N/A"},
                    ],
                    attr:{
                        class:"form-control",
                        id: "act",
                    }
                },{
                    label:"Condition:",
                    name:"gate_record.cond",
                    type:"select",
                    options:[
                        {label:"SOUND", value:"SOUND"},
                        {label:"NOT SOUND", value:"NOT SOUND"}
                    ],
                    attr:{
                        class:"form-control"
                    }
                }, {
                    label: "Gate Out Date:",
                    name: "gate_record.date",
                    type: "datetime",
                    def: function() { return new Date();},
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }
            ]
        });

        editor.on('open', function() {
            GateOut.getDrivers();
        });

        editor.dependent("gate_record.depot_id", function(val) {
        // editor.dependent("trade_type.name", function(val) {
            return val == 2 ? { show: "booking.act" } : { hide: "booking.act" };
            // console.log(val);
        });

        editor.field('gate_record.type').hide();
        editor.field('gate_record.user_id').hide();

        gate_condition = new $.fn.dataTable.Editor( {
            ajax: "/api/gate_condition/table",
            template: '#customForm',
            fields: [{
                label:"ID",
                name:"gate_record_container_condition.gate_record",
                attr:{
                    class:"form-control",
                    id:"gate_record_id"
                }
            },{
                label: "Container:",
                name: "container_number",
                attr: {
                    maxlength: 11,
                    list: "gcontainers",
                    class: "form-control",
                    id:"container_no",
                    disabled: true
                }
            }, {
                label: "Container Section:",
                name:"gate_record_container_condition.container_section",
                type: "select",
                options: [
                    {label: "Roof Bows", value: "1"},
                    {label: "Floor", value: "2"},
                    {label: "Roof", value: "3"},
                    {label: "Corner Castings", value: "4"},
                    {label: "Lashing Points", value: "5"},
                    {label: "Side Panels", value: "6"},
                    {label: "Bottom Side Rail", value: "7"},
                    {label: "Stiffened", value: "8"},
                    {label: "Cross Members", value: "9"},
                    {label: "Top Side Rail", value: "10"},
                    {label: "Front End Wall", value: "11"},
                    {label: "Front End Frame including Posts", value: "12"},
                    {label: "Door Hinges", value: "13"},
                    {label: "Lining", value: "14"},
                    {label: "Door Panels", value: "15"},
                    {label: "Door Frame including Pots", value: "16"},
                    {label: "Gooseneck Tunnel", value: "17"},
                    {label: 'Door Locking Bars', value: "18"}
                ],
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Damage Type:",
                name:"gate_record_container_condition.damage_type",
                type: "select",
                attr: {
                    id: 'damage_type',
                    class: "form-control"
                },
                options: [
                    {label: "Pushed Out", value: "1"},
                    {label: "B-Broken", value: "2"},
                    {label: "Hole", value: "3"},
                    {label: "Pushed In", value: "4"},
                    {label: "Missing", value: "5"},
                    {label: "Stained", value: "6"},
                    {label: "Cut", value: "7"},
                    {label: "Dent", value: "8"},
                    {label: "Bent", value: "9"},
                    {label: 'Corrosion', value: "10"}
                ]
            }, {
                label: "Damage Severity:",
                name:"gate_record_container_condition.damage_severity",
                type: "select",
                options: [
                    {label: "LOW", value: "LOW"},
                    {label: "MEDIUM", value: "MEDIUM"},
                    {label: 'HIGH', value: "HIGH"}
                ],
                attr: {
                    id: 'damage_severity',
                    class: "form-control"
                }
            }, {
                label: "Note:",
                name: "gate_record_container_condition.note",
                type: "textarea",
                attr: {
                    class: "form-control"
                }
            }]
        });


        gate_condition.on('submitSuccess', function () {
            TableRfresh.freshTable('gate_out');
        });
        gate_condition.field('gate_record_container_condition.gate_record').hide();

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            if (action === 'remove') {
                var status = json.cancelled;
                if (status.length > 0) {
                    Modaler.dModal("Deletion", "Container cannot be deleted. Container already gated out");
                }
            }
            if(action === 'create'){
               $gate_record_id = data['id'];
               $.ajax({
                    url:"/api/container/update_trade_type",
                    type: "POST",
                    data:{
                        id: $gate_record_id
                    },
                    success: function(){
                        TableRfresh.freshTable('gate_out');
                    },
                    error: function(){
                        alert("something went wrong");
                    }
               });
            }
        });

        $('#gate_out').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/gate_out/table",
                type:"POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [15] }, { "searchable": false, "targets": 19 } ],
            order: [[ 9, 'desc' ]],
            columns: [
                { data: "ctnum"},
                { data: "isoc"},
                { data: "vyref"},
                { data: "trade_type.name", visible:false},
                { data: "dpgt" },
                { data: "dpname",visible:false},
                { data: "gate_record.vehicle_id"},
                { data: "gate_record.driver_id" },
                { data: "gate_record.trucking_company_id" },
                { data: "gate_record.date"},
                { data: "icsl1", visible:false},
                { data: "icsl2", visible:false},
                { data: "seal1", visible:false},
                { data: "seal2", visible:false},
                { data: "spsl", visible:false},
                { data:"good",visible:false},
                { data: "booking.act"},
                { data: "gate_record.cond"},
                {data: "note",visible:false},
                { data:  null,
                    render: function (data, type, row) {
                        var gated_out = "";
                        if (data.gate_record.cond == "NOT SOUND"){
                            gated_out += "<a href='#' onclick='GateOut.conditionGate(" + data.id + ", \"" + data.ctnum + "\")'>Add condition</a><br/>";
                            gated_out += "<a id='check_cond' href='#' onclick='GateOut.conditionAlert(" + data.id + ", \"" + data.ctnum + "\")'>Check condition</a><br/>";
                        }
                        if (data.stat == 0){
                            gated_out  +=  "<a href='#' class='gate_out' data-toggle=\"modal\" data-target=\"#modal-small\" onclick='GateOut.gateContainerOut(" + data.id + ", \"" + data.ctnum + "\")'>Gate Out</a><br/>";
                        }
                        if (data.stat == 1){
                            gated_out  += "<a href='/api/GateWaybill/portxPdf/" + data.id + "' target='_blank' class='way_bill'>Waybill</a><br/>";
                        }
                        return gated_out;
                    }
                },
                { data: "cons",visible:false},
                { data: "id", visible:false},
                { data: "pdate", visible: false },
                { data: "gate_record.user_id", visible: false },
            ],
            select: true,
            buttons: GateOutHelper.permissionButtonBuilder(editor,'Gate Out')
        });

        editor.on('onInitCreate', function () {
            var table = $('#gate_out').DataTable();
            var rowData = table.row( { selected: true } ).data();
            editor.field("gate_record.container_id").enable();

        } );

        editor.on('initEdit',function () {
           editor.field("gate_record.container_id").disable();
        });



    }
}

var Country = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/country/table",
            table: "#country",
            template: "#customForm",
            fields: [
                {
                    label: "Code:",
                    name: "code",
                    attr: {
                        class: "form-control",
                        maxlength:2
                    }
                },
                {
                    label: "Country Name:",
                    name: "name",
                    attr: {
                        class: "form-control",
                        maxlength:100
                    }
                }
            ]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Country', 'Country In Use Cannot Be Deleted.');
                }
            }
        });

        $('#country').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/country/table",
                type: "POST"
            },
            serverSide: true,
            columns: [
                {data: "code"},
                {data: "name"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Country')
        });

    }
}

var VehicleDriver= {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/driver/table",
            table: "#vehicle_driver",
            template: "#customForm",
            fields: [
                {
                    label: "License:",
                    name: "vehicle_driver.license",
                    attr: {
                        class: "form-control",
                        maxlength: 20
                    }
                },
                {
                    label: "Name:",
                    name: "vehicle_driver.name",
                    attr: {
                        class: "form-control",
                        maxlength: 200
                    }
                },
                {
                    label: "Trucking Company:",
                    name: "vehicle_driver.trucking_company_id",
                    attr: {
                        list: "trucking",
                        class: "form-control"
                    }
                }
            ]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Drivers', 'Drivers In Use Cannot Be Deleted.');
                }
            }
        });

        $('#vehicle_driver').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/driver/table",
                type: 'POST'
            },
            serverSide: true,
            columns: [
                {data: "vehicle_driver.license"},
                {data: "vehicle_driver.name"},
                {data: "trucking_company.name"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Driver')
        });

    }
}

var ShippingLineAgents = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/shipping_line_agents/table",
            table: "#ship_agent",
            template: "#customForm",
            fields: [
                {
                    label: "Shipping Line:",
                    name: "shipping_line_agent.line_id",
                    attr: {
                        list: "ship_agents",
                        class: "form-control"
                    }
                },
                {
                    label: "Code:",
                    name: "shipping_line_agent.code",
                    attr: {
                        class: "form-control",
                        maxlength: 10
                    }
                },
                {
                    label: "Name:",
                    name: "shipping_line_agent.name",
                    attr: {
                        class: "form-control",
                        maxlength: 150
                    }
                },
                {
                    label: "Date:",
                    name: "shipping_line_agent.date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }
            ]
        });


        $('#ship_agent').DataTable({
            dom: "Bfrtip",
            ajax: {
                url:"/api/shipping_line_agents/table",
                type:"POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [3] } ],
            order: [[ 3, 'desc' ]],
            columns: [
                {data: "shipping_line.name"},
                {data: "shipping_line_agent.code"},
                {data: "shipping_line_agent.name"},
                {data: "shipping_line_agent.date"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Shipping Agent')
        });

    }
}

var Vessel = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/vessel/table",
            table: "#vessel",
            template: "#customForm",
            fields: [
                {
                    label: "Code:",
                    name: "vessel.code",
                    attr: {
                        class: "form-control",
                        maxlength: 20
                    }
                },
                {
                    label: "Name:",
                    name: "vessel.name",
                    attr: {
                        class: "form-control",
                        maxlength: 100
                    }
                },
                {
                    label: "Length Over All:",
                    name: "vessel.length_over_all",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Net Tonnage:",
                    name: "vessel.net_tonnage",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Gross Tonnage:",
                    name: "vessel.gross_tonnage",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Dead weight tonnage:",
                    name: "vessel.dead_weight_tonnage",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Teu Capacity:",
                    name: "vessel.teu_capacity",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Imo Number:",
                    name: "vessel.imo_number",
                    attr: {
                        class: "form-control",
                        maxlength: 20
                    }
                },
                {
                    label: "Vessel Type:",
                    name: "vessel.type_id",
                    attr: {
                        list: "vessel_type",
                        class: "form-control"
                    }
                },
                {
                    label: "Registry Port",
                    name: "vessel.registry_port_id",
                    attr: {
                        list: "ports",
                        class: "form-control"
                    }
                },
                {
                    label: "Country:",
                    name: "vessel.country_id",
                    attr: {
                        list: "country",
                        class: "form-control"
                    }
                },
                {
                    label: "Year Built:",
                    name: "vessel.year_built",
                    attr: {
                        class: "form-control",
                        maxlength: 4
                    }
                }
            ]
        });

        $("#vessel").DataTable({
            dom: "Bfrtip",
            ajax: {
                url:"/api/vessel/table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                {data: "vessel.code"},
                {data: "vessel.name"},
                {data: "vessel.length_over_all"},
                {data: "vessel.net_tonnage"},
                {data: "vessel.gross_tonnage"},
                {data: "vessel.dead_weight_tonnage"},
                {data: "vessel.teu_capacity"},
                {data: "vessel.imo_number"},
                {data: "vessel_type.name" },
                {data: "port.name"},
                {data: "country.name", visible: false},
                {data: "vessel.year_built", visible: false}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Vessel')
        });
    }
}

var ShippingLine = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/shipping_line/table",
            table: "#shipping_line",
            template: "#customForm",
            fields: [
                {
                    label: "Code:",
                    name: "code",
                    attr: {
                        class: "form-control",
                        maxlength: 10
                    }
                },
                {
                    label: "Name:",
                    name: "name",
                    attr: {
                        class: "form-control",
                        maxlength: 100
                    }
                }
            ]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Shipping Line', 'Shipping Line In Use Cannot Be Deleted.');
                }
            }
        });

        $('#shipping_line').DataTable({
            dom: "Bfrtip",
            ajax: {
                url:"/api/shipping_line/table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                {data: "code"},
                {data: "name"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Shipping Line')
        });

    }
}

var Vehicle = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/vehicle/table",
            table: "#vehicle",
            template: '#customForm',
            fields: [ {
                label: "Number:",
                name: "vehicle.number",
                attr: {
                    class: "form-control",
                    maxlength: 20
                }
            }, {
                label: "Description:",
                name: "vehicle.description",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }, {
                label: "Type:",
                name: "vehicle.type_id",
                attr: {
                    list: "types",
                    class: "form-control"
                }
            }, {
                label: "Trucking Company:",
                name: "vehicle.trucking_company_id",
                attr: {
                    maxlength: 100,
                    list: "companies",
                    class: "form-control"
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Vehicle', 'Vehicles In Use Cannot Be Deleted.');
                }
            }
        });

        $('#vehicle').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/vehicle/table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                { data: "vehicle.number" },
                { data: "vehicle.description" },
                { data: "vehicle.type_id" },
                { data: "vehicle.trucking_company_id"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Vehicle')
        });
    }
}

var InvoiceNote={
    initTable: function(invoice_id){

          edit_note = new $.fn.dataTable.Editor( {
            ajax: "/api/invoice_note/table",
            table: "#view_notes",
            fields: [
              
              {
                    label: "Note:",
                    name: "invoice_note.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        edit_note.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        $('#view_notes').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/invoice_note/table",
                type: "POST",
                data: {
                    "record": invoice_id
                }
            },
            searching:false,
            serverSide: true,
            columns: [
                { data: "numb" },
                { data: "invoice_note.note" },
                { data: "ntype" },
                { data: "user" }
            ],
            select: true,
            buttons: [
                { extend: "edit", editor: edit_note,formTitle:"Edit Note", className:"btn btn-primary" }
            ]
        });
    }
}

var SuppsInvoiceNote={
    initTable: function(invoice_id){

          edit_note = new $.fn.dataTable.Editor( {
            ajax: "/api/supp_invoice_note/table",
            table: "#view_notes",
            fields: [
              
              {
                    label: "Note:",
                    name: "supplementary_note.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        edit_note.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        $('#view_notes').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/supp_invoice_note/table",
                type: "POST",
                data: {
                    "record": invoice_id
                }
            },
            searching:false,
            serverSide: true,
            columns: [
                { data: "numb" },
                { data: "supplementary_note.note" },
                { data: "ntype" },
                { data: "user" }
            ],
            select: true,
            buttons: [
                { extend: "edit", editor: edit_note,formTitle:"Edit Note", className:"btn btn-primary" }
            ]
        });
    }
}

var ProformaInvoiceNote={
    initTable: function(invoice_id){

          edit_note = new $.fn.dataTable.Editor( {
            ajax: "/api/proforma_invoice_note/table",
            table: "#view_notes",
            fields: [
              
              {
                    label: "Note:",
                    name: "proforma_invoice_note.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        edit_note.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        $('#view_notes').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/proforma_invoice_note/table",
                type: "POST",
                data: {
                    "record": invoice_id
                }
            },
            searching:false,
            serverSide: true,
            columns: [
                { data: "numb" },
                { data: "proforma_invoice_note.note" },
                { data: "ntype" },
                { data: "user" }
            ],
            select: true,
            buttons: [
                { extend: "edit", editor: edit_note,formTitle:"Edit Note", className:"btn btn-primary" }
            ]
        });
    }
}

var ContainerCondition = {
    iniTable: function (id,container) {

        edit_condition = new $.fn.dataTable.Editor( {
            ajax: "/api/gate_condition/table",
            table: "#container_condition",
            fields: [
                {
                    label:"ID",
                    name:"gate_record_container_condition.gate_record",
                    attr:{
                        class:"form-control",
                        id:"gate_record_id"
                    }
                },
                {
                    label: "Container:",
                    name: "container",
                    attr: {
                        id: "containerNo",
                        maxlength: 11,
                        list: "gcontainers",
                        class: "form-control",
                        disabled: true
                    },
                },
                {
                    label: "Container Section:",
                    name:"gate_record_container_condition.container_section",
                    type: "select",
                    options: [
                        {label: "Roof Bows", value: "1"},
                        {label: "Floor", value: "2"},
                        {label: "Roof", value: "3"},
                        {label: "Corner Castings", value: "4"},
                        {label: "Lashing Points", value: "5"},
                        {label: "Side Panels", value: "6"},
                        {label: "Bottom Side Rail", value: "7"},
                        {label: "Stiffened", value: "8"},
                        {label: "Cross Members", value: "9"},
                        {label: "Top Side Rail", value: "10"},
                        {label: "Front End Wall", value: "11"},
                        {label: "Front End Frame including Posts", value: "12"},
                        {label: "Door Hinges", value: "13"},
                        {label: "Lining", value: "14"},
                        {label: "Door Panels", value: "15"},
                        {label: "Door Frame including Pots", value: "16"},
                        {label: "Gooseneck Tunnel", value: "17"},
                        {label: 'Door Locking Bars', value: "18"}
                    ],
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "Damage Type:",
                    name:"gate_record_container_condition.damage_type",
                    type: "select",
                    attr: {
                        id: 'damage_type',
                        class: "form-control"
                    },
                    options: [
                        {label: "Pushed Out", value: "1"},
                        {label: "B-Broken", value: "2"},
                        {label: "Hole", value: "3"},
                        {label: "Pushed In", value: "4"},
                        {label: "Missing", value: "5"},
                        {label: "Stained", value: "6"},
                        {label: "Cut", value: "7"},
                        {label: "Dent", value: "8"},
                        {label: "Bent", value: "9"},
                        {label: 'Corrosion', value: "10"}
                    ]
                }, {
                    label: "Damage Severity:",
                    name:"gate_record_container_condition.damage_severity",
                    type: "select",
                    attr: {
                        id: 'damage_severity',
                        class: "form-control"
                    } ,
                    options: [
                        {label: "LOW", value: "LOW"},
                        {label: "MEDIUM", value: "MEDIUM"},
                        {label: 'HIGH', value: "HIGH"}
                    ]
                }, {
                    label: "Note:",
                    name: "gate_record_container_condition.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        edit_condition.field('gate_record_container_condition.gate_record').hide();

        edit_condition.on('open', function () {

            $('#containerNo').val(container);
            $('#gate_record_id').val(id);

            $('.modal').remove();
            $('body').removeClass('modal-open').css('padding-right', '0px');
            $('.modal-backdrop').hide();

        });

        $('#container_condition').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/gate_condition/table",
                type: "POST",
                data: {
                    "record": id
                }
            },
            searching:false,
            serverSide: true,
            columns: [
                { data: "sect" },
                { data: "dmgn" },
                { data: "gate_record_container_condition.damage_severity" },
                { data: "gate_record_container_condition.note" }
            ],
            select: true,
            buttons: [
                { extend: "edit", editor: edit_condition, className:"btn btn-primary" }
            ]
        });
    }
}

var Activity = {
    is_proforma:false,
    invoicedActivity: function(logged_id){
        $.ajax({
            url:"/api/depot_overview/acitivty_invoiced",
            type: "POST",
            data: {
                logg: logged_id,
                prof: Activity.is_proforma ? 1 : 0
            },
            success: function(data){
                var result = $.parseJSON(data);
                if(result.err){
                    $('#activity').prop("disabled",true);
                }
                else{
                    $('#activity').prop("disabled",false);
                }
            },
            error: function(){
                alert("something went wrong");
            }
        });
    }
}

var YardPlan = {

    moveToYard:function(number,container_id){
         $.ajax({
                url: "/api/yard_planning/get_container",
                type: "POST",
                data: {cid: container_id},
                success: function (data) {
                    var result = $.parseJSON(data);
                    $('#containerID').val(result.cnum);
                },
                error: function () {
                    alert("something went wrong");
                }
            });

            yardPlanEditor.create({
                title: 'Add To Yard',
                buttons: 'Add',
            });
    },

    manageYard:function(container_id){
        var error_check = false;
        var url = "/api/yard_planning/check_stack";
        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function () {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 281) {
                    $.ajax({
                        url: "/api/yard_planning/get_stack_info",
                        type: "POST",
                        data: {cid: container_id},
                        success: function (data) {
                            var result = $.parseJSON(data)
                            $('#containerID').val(result.cnum);
                            $('#stackID').val(result.stk);
                            $('#bayID').val(result.bay);
                            $('#rowID').val(result.row);
                            $('#tierID').val(result.tier);
                            $('#reeferID').val(result.reft);
                            $('#equipmentID').val(result.eqip);
                        },
                        error: function () {
                            alert('Something Went Wrong');
                        }
                    });

                    yardPlanEditor
                        .title('Edit Yard Stack')
                        .buttons({
                            "label": "Update", "fn": function () {
                                var stack = $('#stackID').val();
                                var bay = this.field('bay');
                                var row = $('#rowID').val();
                                var tier = $('#tierID').val();
                                var reefer = $('#reeferID').val();
                                var stacker = this.field('stack');
                                var tier_row = this.field('tier');
                                var equipment = this.field('equipment_no');

                                $.ajax({
                                    url: "/api/yard_planning/update_stack",
                                    type: "POST",
                                    data: {
                                        cid:container_id,
                                        stk: stack,
                                        bay: bay.val(),
                                        row: row,
                                        tier: tier,
                                        refs: reefer,
                                        eqip: equipment.val(),
                                        isyd:true
                                    },
                                    success: function (data) {
                                        var result = JSON.parse(data);
                                        if (result.st==176) {
                                            stacker.error("Stack "+result.stk+" does not exist");
                                        }
                                            if(result.st == 177){
                                                stacker.error("Container already at Stack position");
                                            }
                                            if (result.st == 170) {
                                                if (result.berr == "empty") {
                                                    bay.error("Bay field cannot be empty");
                                                }
                                                if (result.eqer == "empty") {
                                                    equipment.error("Equipment Number field cannot be empty");
                                                }
                                            }
                                            if (result.st == 161) {
                                                equipment.error("Reach Stacker equipment Number does not exist");
                                            }
                                            if (result.st == 163) {
                                                equipment.error("Reach Stacker must be "+result.equipment_no+" EMPTY type");
                                            }
                                            if (result.st == 154) {
                                                stacker.error("Container is DG and must be place at Stack DG");
                                            }
                                            else if (result.st == 155) {
                                                stacker.error("Container cannot be place at Stack DG");
                                            }
                                            else if (result.st == 156) {
                                                stacker.error("Stack "+stack+" of bay "+bay+" of row "+row+" is full");
                                               
                                            }

                                            if (result.st == 158) {
                                                stacker.error("Cannot place "+ result.ttyp +" container on top of "+result.ftyp+" container")
                                            }
                                            if (result.st == 159) {
                                                stacker.error("Cannot place "+result.size+" container on top of "+result.fsiz+" container")
                                            }
                                            if (result.st == 160) {
                                                stacker.error("Cannot place "+result.hgt+" container on top of "+result.fhgt+" container")
                                            }
                                             if (result.st ==157) {
                                                tier_row.error('Cannot use tier '+ tier +' whiles tier '+result.tier1+' is vacant');
                                            
                                            }
                                            else if(result.st == 170){
                                                tier_row.error('Cannot use tier '+ tier +' whiles tier '+result.tier2+' is vacant');
                                            }
              
                                            if(result.st == 298){
                                                yardPlanEditor.close();
                                                TableRfresh.freshTable('yard_plan');
                                            }
                                    },
                                    error: function () {
                                        alert('something went wrong');
                                    }
                                });
                            }
                        })
                        .edit();
                        TableRfresh.freshTable('yard_plan');
                } 
            }
            else {
                alert('something went wrong');

            }
        };
        request.send("data=" + container_id);
    },

    loadBays:function(){
        var stack = document.getElementById('stackID');
        var stacker = stack.value;

        if (stacker == "A") {
            YardPlan.baysList(15);
        }
        else if(stacker == "H"){
            YardPlan.baysList(17);
        }

        switch (stacker) {
            case "A":
            case "Q":
                YardPlan.baysList(15);
                break;
            case "H":
            case "I":
                YardPlan.baysList(9);
                break;
            case "G":
                YardPlan.baysList(11);
                break;
            case "J":
            case "DG":
            case "R":
                YardPlan.baysList(8);
                break;
            case "K":
            case "L":
                YardPlan.baysList(10);
                break;
            case "F":
                YardPlan.baysList(26);
                break;
            case "M":
                YardPlan.baysList(18);
                break;
            case "N":
            case "P":
                YardPlan.baysList(16);
                break;
            case "O":
                YardPlan.baysList(12);
                break;
            default:
                YardPlan.baysList(17);
                break;
        }
        
    },

    baysList:function(stack){
        var datalist = document.getElementById('bays');
        datalist.innerHTML = '';
        for (var i = 1; i <= stack; i++) {
            var option = document.createElement('option');
            if (i > 1) {
                var option_value = i%2;
                if (option_value == 0) {
                    continue;
                }
            }
            option.innerText = i;
            datalist.appendChild(option);
        }
    },

    approveStack:function(id){
        $.ajax({
            url:"/api/yard_planning/approve_stack",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 262){
                    Modaler.dModal('Stack Approval','Container has been successfully stacked and confirmed');
                    TableRfresh.freshTable('yard_plan');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    removeFromStack:function(id){
        $.ajax({
            url:"/api/yard_planning/remove_stack_container",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if (result.st == 129) {
                    Modaler.dModal('Removal Error','Cannot remove container from stack due to other containers on top of it. Container at position '+result.stk + result.bay +result.row + result.tier +'');
                }
                else if(result.st == 263){
                    Modaler.dModal('Removal Assignment',"Container has been successfully assigned");
                    TableRfresh.freshTable('yard_plan');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    approveRemoval:function(id){
        $.ajax({
            url:"/api/yard_planning/approve_removal",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 265){
                    Modaler.dModal('Container Removal','Container has been successfully removed from stack');
                    TableRfresh.freshTable('yard_plan');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    pendingApproval:function(id) {
        $.ajax({
            url:"/api/yard_planning/validate_position",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 127){
                    var header = "Pending Approval Error";
                    var body = "Container has not been positioned yet";
                    Modaler.dModal(header,body);
                }
                else if(result.st == 128){
                    var header = "Pending Approval Error";
                    var body = "Container has not been moved to examination area yet";
                    Modaler.dModal(header,body);
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    moveToExamination:function(id){
        $.ajax({
            url:"/api/yard_planning/move_examination",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if (result.st == 180) {
                    Modaler.dModal("Move Examination Error","Cannot move container which is not IMPORT trade type");
                }
                else if(result.st == 270){
                    Modaler.dModal('Examination','Container has been moved to examination area');
                    TableRfresh.freshTable('yard_plan');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    approveExamination:function(id,yard_id){
        $.ajax({
            url:"/api/yard_planning/approve_examination",
            type:"POST",
            data:{
                id:id,
                yid:yard_id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 268){
                    Modaler.dModal('Examination Approval','Container examination move has been approved');
                    TableRfresh.freshTable('yard_plan');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    moveToTruck:function(id,container_id){
        var data_list = $('#vehicle_list');
        data_list.html("");
        $.ajax({
            url:"/api/yard_planning/vehicles",
            type:"POST",
            data:{
                cid:container_id
            },
            success:function(data){
                var result = JSON.parse(data);
                    for(var i=0; i <= result.length; i++){
                        var options = document.createElement('option');
                        options.innerText = result[i].lnse;
                        data_list.append(options);
                    }
            },
            error:function(){
                alert('something went wrong');
            }
        });

        $.ajax({
            url:"/api/yard_planning/validate_move",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 180){
                    Modaler.dModal('Move Onto Truck Error','Container has not been invoiced and paid for');
                }
                else if(result.st == 183){
                    Modaler.dModal('Move Onto Truck Error','Letpass has not been created for container');
                }
                else if(result.st == 184){
                    Modaler.dModal('Move Onto Truck Error','Truck not gated in');
                }
                 else if(result.st == 282){
                    moveEditor
                    .title("Select Truck")
                    .buttons({
                        "label": "Move", "fn": function(){
                            var vehicle = this.field('vehicle_number');

                            $.ajax({
                                url:"/api/yard_planning/move_ontruck",
                                type:"POST",
                                data:{
                                    id:id,
                                    vchl:vehicle.val()
                                },
                                success:function(data){
                                    var result = JSON.parse(data);
                                    if (result.st==188) {
                                        vehicle.error("Vehicle field cannot be empty");
                                    }
                                    else if(result.st==189){
                                        vehicle.error("Vehicle already selected for another container");
                                    }
                                    else if(result.st == 283){
                                        moveEditor.close();
                                        TableRfresh.freshTable('yard_plan');
                                    }
                                },
                                error:function(){
                                    alert("something went wrong");
                                }
                            });
                        }
                    }).edit();
                    TableRfresh.freshTable('yard_plan');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    approveMoveTruck:function(id,gate_record) {
        $.ajax({
            url:"/api/yard_planning/approve_move",
            type:"POST",
            data:{
                id:id,
                gid:gate_record
            },
            success:function(data){
                var result = JSON.parse(data);
                 if(result.st == 267){
                    Modaler.dModal('Truck','Container moved on to truck approved');
                    TableRfresh.freshTable('yard_plan');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    iniTable:function(){
        yardPlanEditor =  new $.fn.dataTable.Editor({
            ajax: "/api/yard_planning/yard_table",
            fields: [ {
                label: "Container:",
                name: "container_id",
                attr: {
                    class: "form-control",
                    id: "containerID",
                    disabled: true,
                }
            },
            {
                label: "Stack:",
                name: "stack",
                attr: {
                    list:"stack_list",
                    onchange:"YardPlan.loadBays()",
                    class: "form-control",
                    id:"stackID"
                }
            },{
                label: "Bay:",
                name: "bay",
                // def:1,
                attr: {
                    list:"bays",
                    class: "form-control",
                    id:"bayID"
                }
            },{
                label: "Row:",
                name: "row",
                attr: {
                    class: "form-control",
                    id:"rowID"
                },
                type: "select",
                options:[
                    {label:"A", value:"A"},
                    {label:"B", value:"B"},
                    {label:"C", value:"C"},
                    {label:"D", value:"D"}
                ]
            },{
                label: "Tier:",
                name: "tier",
                attr: {
                    class: "form-control",
                    id:"tierID"
                },
                type: "select",
                options:[
                    {label:"1",value:1},
                    {label:"2",value:2},
                    {label:"3",value:3}
                ]
            },{
                label: "Equipment Number:",
                name: "equipment_no",
                attr: {
                    class: "form-control",
                    id:"equipmentID",
                    list:"equipments"
                }
            },{
                label: "Reefer Status:",
                name: "reefer_status",
                attr: {
                    class: "form-control",
                    id:"reeferID"
                },
                def:0,
                type: "select",
                options:[
                    {label:"YES",value:1},
                    {label:"NO",value:0}
                ]
            },{
                label: "Assigned by:",
                name: "assigned_by",
                attr:{
                    class:"form-control"
                }
            },{
                label: "Yard Activity:",
                name: "yard_activity",
                attr:{
                    class:"form-control"
                }
            },{
                label: "Stack Time:",
                name: "stack_time",
                attr:{
                    class:"form-control"
                }
            }]
        });

        moveEditor = new $.fn.dataTable.Editor({
             ajax: "/api/yard_planning/yard_table",
             fields:[{
                label:"Vehicle Number",
                name:"vehicle_number",
                attr:{
                    class: "form-control",
                    id: "truckID", 
                    list:"vehicle_list"
                }
             }]
        }); 



        yardPlanEditor.on('submitSuccess', function () {
            TableRfresh.freshTable('yard_plan');
        });

        yardPlanEditor.field('stack_time').hide();
        yardPlanEditor.field('yard_activity').hide();
        yardPlanEditor.field('assigned_by').hide();

        $('#yard_plan').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/yard_planning/table",
                type: "POST",
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [15] }, { "searchable": false, "targets": 16 } ],
            order: [[ 15, 'desc' ]],
            columns: [
                { data: "cid", visible:false },
                { data: "acty"},
                { data: "emx", visible:false },
                { data: "trty", visible:false },
                { data: "sts" },
                { data: "stk" },
                { data: "pos" },
                { data: "cnum" },
                { data: "size" },
                { data: "opr", visible:false },
                { data: "owr", visible:false },
                { data: "rfs", visible:false },
                { data:"mins" },
                { data: "veh" },
                { data: "eqno" },
                { data: "date", visible:false },
                {data: null,
                    render: function (data, type, row) {

                        var gated_record = "";

                        if(data.ytat == 1){
                            gated_record += "Pending Gate Out<br/>"
                        }
                        if((data.ytat == 1)&& (data.trty == "EXPORT")){
                            gated_record += "<a href='#' onclick='YardPlan.moveToYard(\""+ data.cnum + "\", " + data.cid + ")' class='depot_cont'>Move To Stack</a><br/>";
                        }
                        else if((data.yid == "NOT STACKED") && (data.ytat == 0) ){
                            gated_record += "<a href='#' onclick='YardPlan.moveToYard(\""+ data.cnum + "\", " + data.cid + ")' class='depot_cont'>Move To Stack</a><br/>";
                            gated_record += "<a href='#' onclick='YardPlan.moveToExamination(\""+ data.gid + "\")' class='depot_cont'>Move To Examination</a><br/>"
                        }
                        if ((data.posi == 0) && (data.actv == "ASSIGN")) {
                            gated_record += "<a href='#' onclick='YardPlan.pendingApproval(\""+ data.yard_id + "\")' class='depot_cont'>Pending Approval</a><br/>" 
                        }
                        else if((data.posi == 1) && (data.appr == 0) && (data.actv == "ASSIGN")){
                            gated_record += "<a href='#' onclick='YardPlan.approveStack(\""+ data.yard_id + "\")' class='depot_cont'>Approve</a><br/>"
                        }
                        else if((data.posi == 1) && (data.appr == 0) &&(data.actv == "REMOVE")){
                            gated_record += "<a href='#' onclick='YardPlan.approveRemoval(\""+ data.yard_id + "\")' class='depot_cont'>Approve Removal</a><br/>"
                        }
                        else if ((data.posi == 0) && ((data.actv == "REMOVE") || (data.actv == "EXAMINATION") || (data.actv == "ON TRUCK"))) {
                            gated_record += "<a href='#' onclick='YardPlan.pendingApproval(\""+ data.yard_id + "\")' class='depot_cont'>Pending Approval</a><br/>" 
                        }
                        if ((data.posi == 1) && (data.actv == "EXAMINATION")) {
                            gated_record += "<a href='#' onclick='YardPlan.approveExamination(\""+ data.gid + "\", "+data.yard_id+")' class='depot_cont'>Approve Examination Move</a><br/>" 
                        }
                        /*if ((data.ytat == 0) && (data.actv == "ON TRUCK")) {
                            gated_record += "<a href='#' onclick='YardPlan.moveToTruck(\""+ data.yard_id + "\")' class='depot_cont'>Move To Truck</a><br/>"
                        }
                        else*/
                         if ((data.posi == 1) && (data.actv == "ON TRUCK")) {
                            gated_record += "<a href='#' onclick='YardPlan.approveMoveTruck(\""+ data.yard_id + "\", "+data.gid+")' class='depot_cont'>Approve Move On To Truck</a><br/>" 
                        }
                        if(data.appr ==1){
                            gated_record += "<a href='#' onclick='YardPlan.moveToTruck(\""+ data.yard_id + "\", "+data.cid+")' class='depot_cont'>Move To Truck</a><br/>"
                            gated_record += "<a href='#' onclick='YardPlan.manageYard(\""+ data.cid + "\")' class='depot_cont'>Manage Yard</a><br/>"
                            gated_record += "<a href='#' onclick='YardPlan.removeFromStack(\""+ data.yard_id + "\")' class='depot_cont'>Remove From Stack</a><br/>"
                            gated_record += "<span class='position'><strong>Positioned</strong></span><br/>";
                        }
                        return gated_record;
                    }
                }
            ],
            select: true,
            buttons: [
                { extend:"colvis", className:"btn btn-primary"}
            ]
        });

    }
}

var ReachStack={
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/reach_stack/table",
            table: "#reach_stack",
            fields: [ {
                label: "Equipment Number:",
                name: "equipment_no",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Type:",
                name: "type",
                type: "select",
                options:[
                    {label:"LADEN", value:"LADEN"},
                    {label:"EMPTY", value:"EMPTY"},
                ],
                attr: {
                    class: "form-control"
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Reach Stacker', 'Reach Stacker In Use Cannot Be Deleted.');
                }
            }
        });

        $('#reach_stack').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/reach_stack/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [{ "targets": 1 } ],
            columns: [
                { data: "equipment_no" },
                { data: "type" }
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Reach Stacker')
        });
    }
}

var OperatorView={
    positionContainer:function(yard_id){
        $.ajax({
            url:"/api/operator_view/position_container",
            type:"POST",
            data:{
                id:yard_id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 260){
                    Modaler.dModal('Operator','Container has been positioned successfully');
                    TableRfresh.freshTable('operator_view_tbl');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    manageYard:function(container_id){
        var error_check = false;
        var url = "/api/yard_planning/check_stack";
        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function () {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 281) {
                    $.ajax({
                        url: "/api/yard_planning/get_stack_info",
                        type: "POST",
                        data: {cid: container_id},
                        success: function (data) {
                            var result = $.parseJSON(data)
                            $('#containerID').val(result.cnum);
                            $('#stackID').val(result.stk);
                            $('#bayID').val(result.bay);
                            $('#rowID').val(result.row);
                            $('#tierID').val(result.tier);
                            $('#reeferID').val(result.reft);
                            $('#equipmentID').val(result.eqip);
                        },
                        error: function () {
                            alert('Something Went Wrong');
                        }
                    });

                    OperatorTable
                        .title('Edit Position')
                        .buttons({
                            "label": "Update", "fn": function () {
                                var stack = $('#stackID').val();
                                var bay = this.field('bay');
                                var row = $('#rowID').val();
                                var tier = $('#tierID').val();
                                var reefer = $('#reeferID').val();
                                var stacker = this.field('stack');
                                var tier_row = this.field('tier');
                                var equipment = this.field('equipment_no');

                                $.ajax({
                                    url: "/api/yard_planning/update_stack",
                                    type: "POST",
                                    data: {
                                        cid:container_id,
                                        stk: stack,
                                        bay: bay.val(),
                                        row: row,
                                        tier: tier,
                                        refs: reefer,
                                        eqip: equipment.val(),
                                        isyd:false
                                    },
                                    success: function (data) {
                                        var result = JSON.parse(data);
                                            if (result.st==176) {
                                                stacker.error("Stack "+result.stk+" does not exist");
                                            }
                                            if(result.st == 177){
                                                stacker.error("Container already at Stack position");
                                            }
                                            if (result.st == 170) {
                                                if (result.berr == "empty") {
                                                    bay.error("Bay field cannot be empty");
                                                }
                                                if (result.eqer == "empty") {
                                                    equipment.error("Equipment Number field cannot be empty");
                                                }
                                            }
                                            if (result.st == 161) {
                                                equipment.error("Reach Stacker equipment Number does not exist");
                                            }
                                            if (result.st == 163) {
                                                equipment.error("Reach Stacker must be "+result.equipment_no+" EMPTY type");
                                            }
                                            if (result.st == 154) {
                                                stacker.error("Container is DG and must be place at Stack DG");
                                            }
                                            else if (result.st == 155) {
                                                stacker.error("Container cannot be place at Stack DG");
                                            }
                                            else if (result.st == 156) {
                                                stacker.error("Stack "+stack+" of bay "+bay+" of row "+row+" is full");
                                               
                                            }

                                            if (result.st == 158) {
                                                stacker.error("Cannot place "+ result.ttyp +" container on top of "+result.ftyp+" container")
                                            }
                                            if (result.st == 159) {
                                                stacker.error("Cannot place "+result.size+" container on top of "+result.fsiz+" container")
                                            }
                                            if (result.st == 160) {
                                                stacker.error("Cannot place "+result.hgt+" container on top of "+result.fhgt+" container")
                                            }
                                             if (result.st ==157) {
                                                tier_row.error('Cannot use tier '+ tier +' whiles tier '+result.tier1+' is vacant');
                                            
                                            }
                                            else if(result.st == 170){
                                                tier_row.error('Cannot use tier '+ tier +' whiles tier '+result.tier2+' is vacant');
                                            }
              
                                            if(result.st == 298){
                                                OperatorTable.close();
                                                TableRfresh.freshTable('operator_view_tbl');
                                            }
                                    },
                                    error: function () {
                                        alert('something went wrong');
                                    }
                                });
                            }
                        })
                        .edit();

                        TableRfresh.freshTable('operator_view_tbl');
                } 
            }
            else {
                alert('something went wrong');

            }
        };
        request.send("data=" + container_id);

     
    },

    loadBays:function(){
        var stack = document.getElementById('stackID');
        var stacker = stack.value;

        if (stacker == "A") {
            YardPlan.baysList(15);
        }
        else if(stacker == "H"){
            YardPlan.baysList(17);
        }

        switch (stacker) {
            case "A":
            case "Q":
                YardPlan.baysList(15);
                break;
            case "H":
            case "I":
                YardPlan.baysList(9);
                break;
            case "G":
                YardPlan.baysList(11);
                break;
            case "J":
            case "DG":
            case "R":
                YardPlan.baysList(8);
                break;
            case "K":
            case "L":
                YardPlan.baysList(10);
                break;
            case "F":
                YardPlan.baysList(26);
                break;
            case "M":
                YardPlan.baysList(18);
                break;
            case "N":
            case "P":
                YardPlan.baysList(16);
                break;
            case "O":
                YardPlan.baysList(12);
                break;
            default:
                YardPlan.baysList(17);
                break;
        }
        
    },

    baysList:function(stack){
        var datalist = document.getElementById('bays');
        datalist.innerHTML = '';
        for (var i = 1; i <= stack; i++) {
            var option = document.createElement('option');
            option.innerText = i;
            datalist.appendChild(option);
        }
    },

    removeContainer:function(yard_id) {
        $.ajax({
            url:"/api/operator_view/remove_container",
            type:"POST",
            data:{
                id:yard_id,
                ctyp:true
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 264){
                    Modaler.dModal('Operator','Container has been successfully remove from stack');
                    TableRfresh.freshTable('operator_view_tbl');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    moveToExamination:function(id){
        $.ajax({
            url:"/api/operator_view/remove_container",
            type:"POST",
            data:{
                id:id,
                ctyp:false
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 264){
                    Modaler.dModal('Operator','Container has been moved to examination area');
                    TableRfresh.freshTable('operator_view_tbl');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    moveOnToTruck:function(id){
        $.ajax({
            url:"/api/operator_view/move_truck",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 265){
                    Modaler.dModal('Operator','Container has been moved onto truck');
                    TableRfresh.freshTable('operator_view_tbl');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    iniTable:function(){
        OperatorTable =  new $.fn.dataTable.Editor({
            ajax: "/api/yard_planning/yard_table",
            fields: [ {
                label: "Container:",
                name: "container_id",
                attr: {
                    class: "form-control",
                    id: "containerID",
                    disabled: true,
                }
            },
            {
                label: "Stack:",
                name: "stack",
                attr: {
                    onchange:"YardPlan.loadBays()",
                    class: "form-control",
                    id:"stackID",
                    list:"stack_list",
                }
            },{
                label: "Bay:",
                name: "bay",
                def:1,
                attr: {
                    list:"bays",
                    class: "form-control",
                    id:"bayID"
                }
            },{
                label: "Row:",
                name: "row",
                attr: {
                    class: "form-control",
                    id:"rowID"
                },
                type: "select",
                options:[
                    {label:"A", value:"A"},
                    {label:"B", value:"B"},
                    {label:"C", value:"C"},
                    {label:"D", value:"D"}
                ]
            },{
                label: "Tier:",
                name: "tier",
                attr: {
                    class: "form-control",
                    id:"tierID"
                },
                type: "select",
                options:[
                    {label:"1",value:1},
                    {label:"2",value:2},
                    {label:"3",value:3}
                ]
            },{
                label: "Equipment Number:",
                name: "equipment_no",
                attr: {
                    class: "form-control",
                    id:"equipmentID",
                    list:"equipments"
                }
            },{
                label: "Reefer Status:",
                name: "reefer_status",
                attr: {
                    class: "form-control",
                    id:"reeferID"
                },
                def:0,
                type: "select",
                options:[
                    {label:"YES",value:1},
                    {label:"NO",value:0}
                ]
            },{
                label: "Assigned by:",
                name: "assigned_by",
                attr:{
                    class:"form-control"
                }
            },{
                label: "Yard Activity:",
                name: "yard_activity",
                attr:{
                    class:"form-control"
                }
            },{
                label: "Stack Time:",
                name: "stack_time",
                attr:{
                    class:"form-control"
                }
            }]
        });

        OperatorTable.on('submitSuccess', function () {
            TableRfresh.freshTable('operator_view_tbl');
        });

        OperatorTable.field('stack_time').hide();
        OperatorTable.field('yard_activity').hide();
        OperatorTable.field('assigned_by').hide();
        OperatorTable.field('reefer_status').hide();

        $('#operator_view_tbl').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/operator_view/table",
                type: "POST",
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [8] }, { "searchable": false, "targets": 7 },{ "searchable": false, "targets": 9 } ],
            order: [[ 8, 'desc' ]],
            columns: [
                { data: "cnum" },
                { data: "emx"},
                { data: "size" },
                { data: "opr" },
                { data: "owr" },
                { data: "rfs" },
                { data: "stk" },
                { data: null,
                    render:function(data,type,row){
                        return data.stk+""+data.bay+""+data.row+""+data.tier;
                    } },
                { data: "date" },
                {data: null,
                    render: function (data, type, row) {
                        var position = "";
                        if (data.activity == "ASSIGN") {
                            position += "<a href='#' onclick='OperatorView.manageYard(\""+ data.cid + "\")' class='depot_cont'>Manage Stack</a><br/>";
                            position += "<a href='#' onclick='OperatorView.positionContainer(\"" + data.yard_id + "\")' class='depot_cont'>Position</a><br/>";
                        }
                        else if(data.activity == "REMOVE"){
                            position += "<a href='#' onclick='OperatorView.manageYard(\""+ data.cid + "\")' class='depot_cont'>Manage Stack</a><br/>";
                            position += "<a href='#' onclick='OperatorView.removeContainer(\"" + data.yard_id + "\")' class='depot_cont'>Remove</a><br/>";
                        }
                        if(data.activity == "EXAMINATION"){
                            position += "<a href='#' onclick='OperatorView.moveToExamination(\"" + data.yard_id + "\")' class='depot_cont'>Move To Examination</a><br/>";
                        }
                        if(data.activity == "ON TRUCK"){
                            position += "<a href='#' onclick='OperatorView.moveOnToTruck(\"" + data.yard_id + "\")' class='depot_cont'>Move To Truck</a><br/>";
                        }

                        return position;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend:"colvis", className:"btn btn-primary"}
            ]
        });
    }
}

var ExaminationArea={

    activityAlert: function (id, container) {

        $('#container_number').text(container);

        var header = container;
        var body = "<div class=\"col-md-12\"><table id=\"container_activity\" class=\"display table-responsive\">" +
            "<thead><tr><th>Activity </th><th>Note</th><th>User</th><th>Date</th><th>ID</th></tr></thead>" +
            "</table></div>";
        CondModal.cModal(header, body);

        ExaminationArea.iniActivityTable(id,container);
    },

    iniActivityTable: function (id,container) {

        addActivity = new $.fn.dataTable.Editor({
            ajax: "/api/depot_overview/activity_table",
            fields:[
                {
                    label:"Container:",
                    name:"container_log.container_id",
                    attr:{
                        class:"form-control",
                        id:"containerID",
                        disabled: true
                    },
                    def: id
                },
                {
                    label:"Activity",
                    name:"container_log.activity_id",
                    attr:{
                        class:"form-control",
                        list: "activity_list"
                    }
                },{
                    label:"Note:",
                    name:"container_log.note",
                    type:"textarea",
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "User:",
                    name: "container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label:"Date:",
                    name:"container_log.date",
                    type:"datetime",
                    def:function () { return new Date(); },
                    format:"YYYY-MM-DD HH:mm",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        actEditor = new $.fn.dataTable.Editor({
            ajax: "/api/depot_overview/activity_table",
            table: "#container_activity",
            fields: [
                {
                    label: "Container:",
                    name: "container_log.container_id",
                    attr: {
                        class: "form-control",
                        id: "containerID",
                        disabled: true
                    },
                    def: container
                },
                {
                    label: "Activity:",
                    name: "container_log.activity_id",
                    attr: {
                        class: "form-control",
                        list: "activity_list"
                    }
                }, {
                    label: "Note:",
                    name: "container_log.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "User:",
                    name: "container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        actEditor.field('container_log.user_id').hide();
        actEditor.field('container_log.container_id').hide();
        addActivity.field('container_log.user_id').hide();
        addActivity.field('container_log.container_id').hide();

        addActivity.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        actEditor.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        addActivity.on('submitSuccess', function () {
            TableRfresh.freshTable('container_activity');
        });

        $('#container_activity').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/depot_overview/activity_table",
                type: "POST",
                data: {
                    ctid: id
                }
            },
            columnDefs: [ { type: 'date', 'targets': [3] } ],
            order: [[ 3, 'asc' ]],
            serverSide: true,
            columns: [
                { data: "name" },
                { data: "container_log.note" },
                { data: "container_log.user_id" },
                { data: "container_log.date" },
                { data: "container_log.activity_id", visible:false }
            ],
            select: true,
            buttons: [
                { extend: "create", editor:addActivity, className:"btn btn-primary"},
                { extend: "edit", editor: actEditor, className:"btn btn-primary" },
                { extend: "remove", editor: actEditor, className: "btn btn-primary"}
            ]
        });


    },

    moveToStack:function(container_id){
        $.ajax({
            url: "/api/yard_planning/get_container",
            type: "POST",
            data: {cid: container_id},
            success: function (data) {
                var result = $.parseJSON(data);
                $('#containerID').val(result.cnum);
            },
            error: function () {
                alert("something went wrong");
            }
        });

        yardPlanEditor.create({
            title: 'Add To Yard',
            buttons: 'Add',
        });
    },

    moveToDepot:function(id){
        $.ajax({
            url:"/api/examination_area/depot_move",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 280){
                    Modaler.dModal('Examination','Container has been move to Depot');
                    TableRfresh.freshTable('examination');
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    iniTable:function(){ 
        
        yardPlanEditor =  new $.fn.dataTable.Editor({
            ajax: "/api/yard_planning/yard_table",
            fields: [ {
                label: "Container:",
                name: "container_id",
                attr: {
                    class: "form-control",
                    id: "containerID",
                    disabled: true,
                }
            },
            {
                label: "Stack:",
                name: "stack",
                attr: {
                    onchange:"YardPlan.loadBays()",
                    class: "form-control",
                    id:"stackID",
                    list:"stack_list"
                }
            },{
                label: "Bay:",
                name: "bay",
                def:1,
                attr: {
                    list:"bays",
                    class: "form-control",
                    id:"bayID"
                }
            },{
                label: "Row:",
                name: "row",
                attr: {
                    class: "form-control",
                    id:"rowID"
                },
                type: "select",
                options:[
                    {label:"A", value:"A"},
                    {label:"B", value:"B"},
                    {label:"C", value:"C"},
                    {label:"D", value:"D"}
                ]
            },{
                label: "Tier:",
                name: "tier",
                attr: {
                    class: "form-control",
                    id:"rowID"
                },
                type: "select",
                options:[
                    {label:"1",value:1},
                    {label:"2",value:2},
                    {label:"3",value:3}
                ]
            },{
                label: "Equipment Number:",
                name: "equipment_no",
                attr: {
                    class: "form-control",
                    id:"equipmentID",
                    list:"equipments"
                }
            },{
                label: "Reefer Status:",
                name: "reefer_status",
                attr: {
                    class: "form-control",
                    id:"reeferID"
                },
                def:0,
                type: "select",
                options:[
                    {label:"YES",value:1},
                    {label:"NO",value:0}
                ]
            },{
                label: "Assigned by:",
                name: "assigned_by",
                attr:{
                    class:"form-control"
                }
            },{
                label: "Yard Activity:",
                name: "yard_activity",
                attr:{
                    class:"form-control"
                }
            },{
                label: "Stack Time:",
                name: "stack_time",
                attr:{
                    class:"form-control"
                }
            }]
        });

        yardPlanEditor.on('submitSuccess', function () {
            TableRfresh.freshTable('examination');
        });

        yardPlanEditor.field('stack_time').hide();
        yardPlanEditor.field('yard_activity').hide();
        yardPlanEditor.field('assigned_by').hide();

        $('#examination').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/examination_area/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [9] }, { "searchable": false, "targets": 9 } ],
            order: [[ 8, 'desc' ]],
            columns: [
                { data: "cnum" },
                { data: "code" },
                { data: "blnum" },
                { data: "veh" },
                { data: "drv" },
                { data: "cons" },
                { data: "tknam" },
                { data: "user"},
                { data: "date"},
                {data: null,
                    render: function (data, type, row) {

                        var gated_record = "";
                        gated_record  +=  '<a class="view_act" href="#" onclick="ExaminationArea.activityAlert(' + data.cid + ',' + '\'' + data.cnum + '\'' + ')">Manage Container</a><br>';
                        gated_record  +=  '<a class="view_act" href="#" onclick="ExaminationArea.moveToDepot(' + data.gid + ')">Move to Depot</a><br>';
                        gated_record  +=  '<a class="view_act" href="#" onclick="ExaminationArea.moveToStack(' + data.cid + ')">Move to Stack</a><br>';


                        return gated_record;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend:"colvis", className:"btn btn-primary"}
            ]
        });

    }
}

var Stack={
    iniTable:function(){
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/stack/table",
            table: "#stack_table",
            fields: [{
                label: "Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength: 10
                }
            },{
                label: "Stack Type:",
                name: "stack_type",
                type: "select",
                options:[
                    {label:"General Goods", value:1},
                    {label:"DG", value:2},
                ],
                attr: {
                    class: "form-control",
                    maxlength: 10
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Stack', 'Stack In Use Cannot Be Deleted.');
                }
            }
        });

        $('#stack_table').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/stack/table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                { data: "name" },
                { data: "stack_type" },
                { data: "date",visible:false }
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,' Stack')
        });

    }
}

var BareChasis = {
    truckGateIn:function(id){
        $.ajax({
            url:"/api/bare_chasis/gatein_truck",
            type:"POST",
            data:{
                id:id
            },
            success:function(data){
                let result = JSON.parse(data);
                if (result.st == 273) {
                    Modaler.dModal('Truck Gate In','Truck has been successfully gated in');
                    TableRfresh.freshTable('truck');
                }
            },
            error:function(){

            }
        });
    },

    loadTruck:function(){
        var letpass = document.getElementById('letID');
        var vehicle_datalist = document.getElementById('vehicle_list');
        vehicle_datalist.innerHTML = "";
        
        $.ajax({
            url:"/api/bare_chasis/get_trucks",
            type:"POST",
            data:{
                lno:letpass.value
            },
            success:function(data){
                var result = JSON.parse(data);
                for(let i=0; i < result.length; i++){
                    var option = document.createElement('option');
                    option.innerText = result[i];
                    vehicle_datalist.appendChild(option);
                    
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });

    },

    loadDrivers:function(){
        var letpass = document.getElementById('letID');
        var driver_datalist = document.getElementById('driver_list');
        driver_datalist.innerHTML = "";
        
        $.ajax({
            url:"/api/bare_chasis/get_drivers",
            type:"POST",
            data:{
                lno:letpass.value
            },
            success:function(data){
                var result = JSON.parse(data);
                for(let i=0; i < result.length; i++){
                    var option = document.createElement('option');
                    option.innerText = result[i];
                    driver_datalist.appendChild(option);
                    
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    loadContainer:function(){
        var letpass = document.getElementById('letID');
        var container_datalist = document.getElementById('container_list');
        container_datalist.innerHTML = "";
        
        $.ajax({
            url:"/api/bare_chasis/get_containers",
            type:"POST",
            data:{
                lno:letpass.value
            },
            success:function(data){
                var result = JSON.parse(data);
                for(let i=0; i < result.length; i++){
                    var option = document.createElement('option');
                    option.innerText = result[i];
                    container_datalist.appendChild(option);
                    
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    },

    iniTable:function(){

        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/bare_chasis/table",
            table: "#truck",
            fields: [ {
                label: "Letpass Number:",
                name: "letpass_no",
                attr: {
                    onblur:"BareChasis.loadTruck();BareChasis.loadDrivers();BareChasis.loadContainer()",
                    class: "form-control",
                    maxlength: 10,
                    id:"letID"
                }
            },{
                label: "Container Number",
                name: "container_id",
                attr: {
                    class: "form-control",
                    list:"container_list"
                }
            }, {
                label: "Vehicle Number:",
                name: "vehicle_number",
                attr: {
                    class: "form-control",
                    list:"vehicle_list"
                }
            },
            {
                label: "Vehicle Driver:",
                name: "vehicle_driver",
                attr: {
                    class: "form-control",
                    list:"driver_list"
                }
            },{
                label: "Letpass ID:",
                name: "letpass_id",
                attr: {
                    class: "form-control"
                }
            }]
        });  
        
        editor.field('letpass_id').hide();
        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Bare Chasis', 'Record can not be deleted.');
                }
            }
        });

        
        

        $('#truck').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/bare_chasis/table",
                type:"POST",
                data: function (d) {
                    d.stdt = $('#start_date').val();
                    d.eddt = $('#end_date').val();
                }
            },
            serverSide: true,
            columnDefs: [ { "searchable": false, "targets": 8 } ],
            order: [[ 7, 'desc' ]],
            columns: [
                { data: "vehicle_number" },
                { data: "vehicle_driver"},
                { data: "container_id"},
                { data:"letpass_no"},
                { data:"offload_time"},
                { data:"onload_time"},
                { data:"gate_status"},
                { data:"date"},
                { data: null,
                    render:function (data, type, row) {
                        var truck = '';
                        if (data.gstat == "") {
                            truck += "<a href='#' onclick='BareChasis.truckGateIn(\""+ data.id + "\")' class='depot_cont'>Gate In</a><br/>";
                        }
                        if (data.gstat=='GATED IN') {
                            truck += "Pending Onload To Truck<br/>";
                        }
                        return truck;
                    }}
            ],
            select: true,
            buttons: TruckButtons.permissionButtonBuilder(editor,' Truck')
        });

        $('#start_date, #end_date').on('change', function () {
            $('#truck').DataTable().ajax.reload();
    });
    }
}

var DepotOver = {
    activityAlert: function (id, container) {

        $('#container_number').text(container);

        var header = container;
        var body = "<div class=\"col-md-12\"><table id=\"container_activity\" class=\"display table-responsive\">" +
            "<thead><tr><th>ID</th><th>Activity </th><th>Note</th><th>User</th><th>Date</th></tr></thead>" +
            "</table></div>";
        CondModal.cModal(header, body);

        DepotOver.iniActivityTable(id,container);
    },

    iniActivityTable: function (id,container) {

        addActivity = new $.fn.dataTable.Editor({
            ajax: "/api/depot_overview/activity_table",
            fields:[
                {
                    label:"Container:",
                    name:"container_log.container_id",
                    attr:{
                        class:"form-control",
                        id:"containerID",
                        disabled: true
                    },
                    def: id
                },
                {
                    label:"Activity",
                    name:"container_log.activity_id",
                    attr:{
                        class:"form-control",
                        list: "activity_list"
                    }
                },{
                    label:"Note:",
                    name:"container_log.note",
                    type:"textarea",
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "User:",
                    name: "container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label:"Date:",
                    name:"container_log.date",
                    type:"datetime",
                    def:function () { return new Date(); },
                    format:"YYYY-MM-DD HH:mm",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        actEditor = new $.fn.dataTable.Editor({
            ajax: "/api/depot_overview/activity_table",
            table: "#container_activity",
            fields: [
                {
                    label: "Container:",
                    name: "container_log.container_id",
                    attr: {
                        class: "form-control",
                        id: "containerID",
                        disabled: true
                    },
                    def: container
                },
                {
                    label: "Activity:",
                    name: "container_log.activity_id",
                    attr: {
                        class: "form-control",
                        list: "activity_list",
                        id: "activity"
                    }
                }, {
                    label: "Note:",
                    name: "container_log.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "User:",
                    name: "container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        actEditor.field('container_log.user_id').hide();
        actEditor.field('container_log.container_id').hide();
        addActivity.field('container_log.user_id').hide();
        addActivity.field('container_log.container_id').hide();

        addActivity.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });


        actEditor.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        addActivity.on('submitSuccess', function () {
            TableRfresh.freshTable('container_activity');
        });

        $('#container_activity').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/depot_overview/activity_table",
                type: "POST",
                data: {
                    ctid: id
                }
            },
            columnDefs: [ { type: 'date', 'targets': [3] } ],
            order: [[ 3, 'asc' ]],
            serverSide: true,
            columns: [
                { data: "loged"},
                { data: "name" },
                { data: "container_log.note" },
                { data: "container_log.user_id" },
                { data: "container_log.date" },
                { data: "container_log.activity_id", visible:false }
            ],
            select: true,
            buttons: [
                { extend: "create", editor:addActivity, className:"btn btn-primary"},
                { extend: "edit", editor: actEditor, className:"btn btn-primary" },
                { extend: "remove", editor: actEditor, className: "btn btn-primary"}
            ]
        });

        actEditor.on('onInitEdit', function () {
            var table = $('#container_activity').DataTable();
            var rowData = table.row({selected: true}).data();
            var container_log_id = rowData['loged'];
            Activity.invoicedActivity(container_log_id);
        });
    },

    addContainerInfo:function(container_number, id) {
        var url = "/api/depot_overview/check_info";

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function () {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 271) {
                    $.ajax({
                        url: "/api/depot_overview/get_container_info",
                        type: "POST",
                        data: {cntr: container_number},
                        success: function (data) {
                            var result = $.parseJSON(data)
                            $('#containerID').val(result.ctnum);
                            $('#load_status').val(result.ldst);
                            $('#goods').val(result.good);
                        },
                        error: function () {
                            $('#container_number').text('ERROR');
                            $('#container_depot').text('Something Went Wrong');
                        }
                    });


                    depotInfo
                        .title('Edit Container Info')
                        .buttons({
                            "label": "Update", "fn": function () {

                                var container_number = $('#containerID').val();
                                var load_status = $('#load_status').val();
                                var goods = $('#goods').val();


                                $.ajax({
                                    url: "/api/depot_overview/update_container_info",
                                    type: "POST",
                                    data: {
                                        ctnr: container_number,
                                        ldst: load_status,
                                        good: goods
                                    },
                                    success: function (data) {
                                        var result = JSON.parse(data);
                                        var header = "";
                                        var body = "";
                                        if (result.st == 112){
                                            header = "Container Error Info";
                                            body = "Container has been already invoiced";
                                            Modaler.dModal(header,body);
                                        }
                                    },
                                    error: function () {
                                        alert('something went wrong');
                                    }
                                });
                                this.close();

                            }
                        })
                        .edit();
                } else {
                    $.ajax({
                        url: "/api/depot_overview/get_invoice_cost",
                        type: "POST",
                        data: {cntr: container_number},
                        success: function (data) {
                            var result = $.parseJSON(data);
                            $('#containerID').val(result.cntr);
                        },
                        error: function () {
                            $('#container_number').text('ERROR');
                            $('#container_depot').text('Something Went Wrong');
                        }
                    });

                    depotInfo.create({
                        title: 'Add Container Info',
                        buttons: 'Add'
                    });
                }
            }
            else {
                $('#container_number').text('ERROR');
                $('#container_depot').text('Something Went Wrong');
            }
        };
        request.send("data=" + id);
    },

    moveToUcl:function(container_id){
        $.ajax({
           url:"/api/ucl/move_to_ucl",
           type:"POST",
           data:{
               cnum:container_id
           },
            success:function(data){
               if (data){
                   var result = JSON.parse(data);
                   var header;
                   var body;

                   if (result.st == 1520){
                       header = "UCL Error";
                       body = "Container information  not added";
                       Modaler.dModal(header,body);
                   }

                   if (result.st == 1510){
                       header = "UCL Error";
                       body = "User does not have permission to move container to UCL";
                       Modaler.dModal(header,body);
                   }
                   if (result.st == 1521){
                       header = "UCL Error";
                       body = "Container cannot be moved to UCL";
                       Modaler.dModal(header,body);
                   }
                   if (result.st == 2100){
                       header = "UCL Depot";
                       body = "Container has been moved to UCL";
                       Modaler.dModal(header,body);
                       TableRfresh.freshTable('depot_over');
                   }
               }
            },
            error:function () {
                alert("something went wrong");
            }
        });

    },

    iniTable: function (can_move_ucl) {
        depotInfo = new $.fn.dataTable.Editor({
            ajax: "/api/depot_overview/info_table",
            fields:[
                {
                    label:"Container:",
                    name:"container_id",
                    attr:{
                        class:"form-control",
                        id:"containerID",
                        disabled: true
                    }
                },
                {
                    label: "Load Status:",
                    name: "load_status",
                    attr: {
                        id: "load_status",
                        class: "form-control"
                    },
                    type:"select",
                    options:[
                        {label: "FCL", value: "FCL"},
                        {label: "LCL", value: "LCL"}
                    ],
                },{
                    label: "Goods:",
                    name: "goods",
                    attr: {
                        class: "form-control",
                        id: "goods"
                    },
                    type:"select",
                    options:[
                        {label: "General Goods", value: "General Goods"},
                        {label: "Engines/Spares Parts", value: "Engines/Spares Parts"},
                        {label: "Vehicle", value: "Vehicle"},
                        {label: "DG I", value: "DG I"},
                        {label: "DG II", value: "DG II"}
                    ],
                }
            ]
        });

        $('#depot_over').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/depot_overview/table",
                type: "POST",
                data:function(d){
                    d.trade_type = $('#trade_type').val();
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [15] }, { "searchable": false, "targets": 13 } ],
            order: [[ 15, 'desc' ]],
            columns: [
                { data: "cnum" },
                { data: "iso" },
                { data: "blnum" },
                { data: "bknum" },
                { data: "depot" },
                { data: "gate" },
                { data: "veh" },
                { data: "drv" },
                { data: "tknam",visible:false},
                { data: "cons" },
                { data: "ref", visible:false},
                { data: "cond", visible:false},
                {data: "note",visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var gated_record = "";

                        gated_record += "<a href='#' onclick='DepotOver.addContainerInfo(\""+ data.cnum + "\", " + data.cid + ")' class='depot_cont'>Container Info</a><br/>"

                        gated_record  +=  '<a class="view_act" href="#" onclick="DepotOver.activityAlert(' + data.cid + ',' + '\'' + data.cnum + '\'' + ')">Manage Activity</a><br>';

                        if (can_move_ucl){
                            gated_record  +=  '<a class="view_act" href="#" onclick="DepotOver.moveToUcl(' + data.cid + ')">Move to Ucl</a><br>';
                        }


                        return gated_record;

                    }
                },
                { data: "eir", visible:false },
                { data: "date", visible:false },
                { data: "pdate", visible: false },
                { data: "user", visible: false },
                { data: "spsl", visible:false}
            ],
            select: true,
            buttons: [
                { extend:"colvis", className:"btn btn-primary"}
            ]
        });

        $('#trade_type').on('change', function () {
            $('#depot_over').DataTable().ajax.reload();
        });


    }
}

var ProformaDepotOver = {
    activityAlert: function (id, container) {

        $('#container_number').text(container);

        var header = container;
        var body = "<div class=\"col-md-12\"><table id=\"container_activity\" class=\"display table-responsive\">" +
            "<thead><tr><th>ID</th><th>Activity </th><th>Note</th><th>User</th><th>Date</th></tr></thead>" +
            "</table></div>";
        CondModal.cModal(header, body);

        ProformaDepotOver.iniActivityTable(id, container);
    },

    iniActivityTable: function (id, container) {
        addActivity = new $.fn.dataTable.Editor({
            ajax: "/api/proforma_depot_overview/activity_table",
            fields: [
                {
                    label: "Container:",
                    name: "proforma_container_log.container_id",
                    attr: {
                        class: "form-control",
                        id: "containerID",
                        disabled: true
                    },
                    def: id
                },
                {
                    label: "Activity",
                    name: "proforma_container_log.activity_id",
                    attr: {
                        class: "form-control",
                        list: "activity_list"
                    }
                }, {
                    label: "Note:",
                    name: "proforma_container_log.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "User:",
                    name: "proforma_container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Date:",
                    name: "proforma_container_log.date",
                    type: "datetime",
                    def: function () {
                        return new Date();
                    },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }
            ]
        });

        actEditor = new $.fn.dataTable.Editor({
            ajax: "/api/proforma_depot_overview/activity_table",
            table: "#container_activity",
            fields: [
                {
                    label: "Container:",
                    name: "proforma_container_log.container_id",
                    attr: {
                        class: "form-control",
                        id: "containerID",
                        disabled: true
                    },
                    def: container
                },
                {
                    label: "Activity:",
                    name: "proforma_container_log.activity_id",
                    attr: {
                        class: "form-control",
                        list: "activity_list",
                        id: "activity"
                    }
                }, {
                    label: "Note:",
                    name: "proforma_container_log.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "User:",
                    name: "proforma_container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        actEditor.field('proforma_container_log.user_id').hide();
        actEditor.field('proforma_container_log.container_id').hide();
        addActivity.field('proforma_container_log.user_id').hide();
        addActivity.field('proforma_container_log.container_id').hide();

        addActivity.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        actEditor.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        addActivity.on('submitSuccess', function () {
            TableRfresh.freshTable('container_activity');
        });

        actEditor.on('onInitEdit', function () {
            var table = $('#container_activity').DataTable();
            var rowData = table.row({selected: true}).data();
            var container_log_id = rowData['loged'];
            Activity.is_proforma = 1;
            Activity.invoicedActivity(container_log_id);
        });

        $('#container_activity').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/proforma_depot_overview/activity_table",
                type: "POST",
                data: {
                    ctid: id
                }
            },
            columnDefs: [{type: 'date', 'targets': [3]}],
            order: [[4, 'asc']],
            serverSide: true,
            columns: [
                { data: "loged"},
                {data: "name"},
                {data: "proforma_container_log.note"},
                {data: "proforma_container_log.user_id"},
                {data: "proforma_container_log.date"},
                {data: "proforma_container_log.activity_id", visible: false}
            ],
            select: true,
            buttons: [
                {extend: "create", editor: addActivity, className: "btn btn-primary"},
                {extend: "edit", editor: actEditor, className: "btn btn-primary"},
                {extend: "remove", editor: actEditor, className: "btn btn-primary"}
            ]
        });
    },

    addContainerInfo: function (container_number, id) {
        var url = "/api/proforma_depot_overview/check_info";

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function () {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 271) {
                    $.ajax({
                        url: "/api/proforma_depot_overview/get_container_info",
                        type: "POST",
                        data: {cntr: container_number},
                        success: function (data) {
                            var result = $.parseJSON(data)
                            $('#containerID').val(result.ctnum);
                            $('#load_status').val(result.ldst);
                            $('#goods').val(result.good);
                        },
                        error: function () {
                            $('#container_number').text('ERROR');
                            $('#container_depot').text('Something Went Wrong');
                        }
                    });


                    depotInfo
                        .title('Edit Container Info')
                        .buttons({
                            "label": "Update", "fn": function () {

                                var container_number = $('#containerID').val();
                                var load_status = $('#load_status').val();
                                var goods = $('#goods').val();


                                $.ajax({
                                    url: "/api/proforma_depot_overview/update_container_info",
                                    type: "POST",
                                    data: {
                                        ctnr: container_number,
                                        ldst: load_status,
                                        good: goods
                                    },
                                    success: function (data) {
                                    },
                                    error: function () {
                                        alert('something went wrong');
                                    }
                                });
                                this.close();
                            }
                        })
                        .edit();
                } else {
                    depotInfo.create({
                        title: 'Add Container Info',
                        buttons: 'Add'
                    });
                    $('#containerID').val(container_number);
                }
            }
            else {
                $('#container_number').text('ERROR');
                $('#container_depot').text('Something Went Wrong');
            }
        };
        request.send("data=" + id);
    },

    moveToUcl: function (container_id) {

        $.ajax({
            url: "/api/ucl/move_to_ucl",
            type: "POST",
            data: {
                cnum: container_id
            },
            success: function (data) {
                if (data) {
                    var result = JSON.parse(data);
                    var header;
                    var body;

                    if (result.st == 1520) {
                        header = "UCL Error";
                        body = "Container information  not added";
                        Modaler.dModal(header, body);
                    }

                    if (result.st == 1510) {
                        header = "UCL Error";
                        body = "User does not have permission to move container to UCL";
                        Modaler.dModal(header, body);
                    }
                    if (result.st == 1521) {
                        header = "UCL Error";
                        body = "Container cannot be moved to UCL";
                        Modaler.dModal(header, body);
                    }
                    if (result.st == 2100) {
                        header = "UCL Depot";
                        body = "Container has been moved to UCL";
                        Modaler.dModal(header, body);
                        TableRfresh.freshTable('depot_over');
                    }
                }
            },
            error: function () {
                alert("something went wrong");
            }
        });

    },

    iniTable: function (can_move_ucl) {
        depotInfo = new $.fn.dataTable.Editor({
            ajax: "/api/proforma_depot_overview/info_table",
            fields: [
                {
                    label: "Container:",
                    name: "container_id",
                    attr: {
                        class: "form-control",
                        id: "containerID",
                        disabled: true
                    }
                },
                {
                    label: "Load Status:",
                    name: "load_status",
                    attr: {
                        id: "load_status",
                        class: "form-control"
                    },
                    type: "select",
                    options: [
                        {label: "FCL", value: "FCL"},
                        {label: "LCL", value: "LCL"}
                    ],
                }, {
                    label: "Goods:",
                    name: "goods",
                    attr: {
                        class: "form-control",
                        id: "goods"
                    },
                    type: "select",
                    options: [
                        {label: "General Goods", value: "General Goods"},
                        {label: "Engines/Spares Parts", value: "Engines/Spares Parts"},
                        {label: "Vehicle", value: "Vehicle"},
                        {label: "DG I", value: "DG I"},
                        {label: "DG II", value: "DG II"}
                    ],
                }
            ]
        });

        $('#depot_over').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/proforma_depot_overview/table",
                type: "POST",
                data: function (d) {
                    d.trade_type = $('#trade_type').val();
                }
            },
            serverSide: true,
            columnDefs: [{type: 'date', 'targets': [15]}, {"searchable": false, "targets": 13}],
            order: [[15, 'desc']],
            columns: [
                {data: "cnum"},
                {data: "type"},
                {data: "bln"},
                {data: "bkn"},
                {data: "depot"},
                {data: "gate"},
                {data: "veh"},
                {data: "drv"},
                {data: "tknam"},
                {data: "cons"},
                {data: "ref", visible: false},
                {data: "cond", visible: false},
                {data: "note",},
                {
                    data: null,
                    render: function (data, type, row) {

                        var gated_record = "";

                        gated_record += "<a href='#' onclick='ProformaDepotOver.addContainerInfo(\"" + data.cnum + "\", " + data.cid + ")' class='depot_cont'>Container Info</a><br/>"

                        gated_record += '<a class="view_act" href="#" onclick="ProformaDepotOver.activityAlert(' + data.cid + ',' + '\'' + data.cnum + '\'' + ')">Manage Activity</a><br>';

                        if (can_move_ucl) {
                            gated_record += '<a class="view_act" href="#" onclick="ProformaDepotOver.moveToUcl(' + data.cid + ')">Move to Ucl</a><br>';
                        }


                        return gated_record;

                    }
                },
                {data: "eir", visible: false},
                {data: "date", visible: false},
                {data: "pdate", visible: false},
                {data: "user", visible: false},
                {data: "spsl", visible: false}
            ],
            select: true,
            buttons: [
                {extend: "colvis", className: "btn btn-primary"}
            ]
        });

        $('#trade_type').on('change', function () {
            $('#depot_over').DataTable().ajax.reload();
        });


    }
}

var Customer = {

    addBillingGroup:function(id){

        var header = "Manage Billing Group";
        var body = "<div class=\"col-md-12\"><table id=\"billing_table\" class=\"display table-responsive\">" +
            "<thead><tr><th>Billing Group Name </th></tr></thead>" +
            "</table></div>";
        CondModal.cModal(header, body);

        Customer.initBillingTable(id);

    },

    initBillingTable:function(id){
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/customer/billing_group_table",
            table: "#billing_table",
            fields: [ {
                label: "Customer:",
                name: "customer_billing.customer_id",
                attr: {
                    class: "form-control",
                },
                def:id
            }, {
                label: "Billing Group:",
                name: "customer_billing.billing_group",
                type:"select",
                attr: {
                    class: "form-control"
                }
            }]
        });

        editor.field("customer_billing.customer_id").hide();

        editor.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        $('#billing_table').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/customer/billing_group_table",
                type: "POST",
                data: {
                    bgid: id
                }
            },
            serverSide: true,
            columns: [
                {data: "customer_billing.billing_group"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Customer')
        });
    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/customer/table",
            table: "#customer",
            template: '#customForm',
            fields: [ {
                label: "Code:",
                name: "code",
                attr: {
                    class: "form-control",
                    maxlength:15
                }
            }, {
                label: "Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength:50
                }
            }, {
                label: "Address Line 1:",
                name: "address_line_1",
                attr: {
                    class: "form-control",
                    maxlength:50
                }
            }, {
                label: "Address Line 2:",
                name: "address_line_2",
                attr: {
                    class: "form-control",
                    maxlength:50
                }
            }, {
                label: "Address Line 3:",
                name: "address_line_3",
                attr: {
                    class: "form-control",
                    maxlength:50
                }
            }, {
                label: "Telephone:",
                name: "telephone",
                attr: {
                    class: "form-control",
                    maxlength:20
                }
            }, {
                label: "Email:",
                name: "email",
                attr: {
                    class: "form-control",
                    maxlength:50
                }
            }, {
                label: "Fax:",
                name: "fax",
                attr: {
                    class: "form-control",
                    maxlength:50
                }
            }, {
                label: "Credit Limit:",
                name: "credit_limit",
                attr: {
                    class: "form-control"
                }
            }]
        });

        $('#customer').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/customer/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [{ "searchable": false, "targets": 9 } ],
            columns: [
                { data: "code" },
                { data: "name" },
                { data: "address_line_1" },
                { data: "address_line_2" },
                { data: "address_line_3" },
                { data: "telephone" },
                { data: "email" },
                { data: "fax" },
                { data: "credit_limit", visible: false},
                {
                    data: null,
                    render: function (data, type, row) {

                        var billing_group = "";

                        billing_group += "<a href='#' onclick='Customer.addBillingGroup(" + data.id + ")' class='depot_cont'>Manage Billing Group</a><br/>"

                        return billing_group;

                    }
                }
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Customer')
        });
    }
}

var CustomerBillingGroups = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/customer/customer_billing_table",
            table: "#customer_billing_group",
            template:"#customForm",
            fields: [
                {
                    label:"Name",
                    name:"customer_billing_group.name",
                    attr:{
                        class:"form-control",
                        maxlength: 50
                    }
                },
                {
                    label: "Extra Free Rent Days",
                    name: "customer_billing_group.extra_free_rent_days",
                    attr:{
                        class:"form-control",
                        maxlength: 3
                    }
                },
                {
                    label:"Trade Type",
                    name:"customer_billing_group.trade_type",
                    type:"select",
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "Tax Type",
                    name: "customer_billing_group.tax_type",
                    type:"select",
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "Waiver Type",
                    name: "waiver_type",
                    type:"select",
                    options:[
                        {label:"Waiver percent", value:1},
                        {label:"Waiver Amount", value:2}
                    ],
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "Waiver (%)",
                    name: "customer_billing_group.waiver_pct",
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "Waiver (Amount)",
                    name: "customer_billing_group.waiver_amount",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        editor.field('customer_billing_group.waiver_amount').hide();

        editor.dependent('waiver_type', function (val) {
            if (val == 1) {
                editor.field('customer_billing_group.waiver_amount').val('');
            }
            else{
                editor.field('customer_billing_group.waiver_pct').val('');
            }
            return val == 2 ?
                {   show:['customer_billing_group.waiver_amount'],
                    hide: ['customer_billing_group.waiver_pct']
                } :
                {   show: ['customer_billing_group.waiver_pct'],
                    hide: ['customer_billing_group.waiver_amount']
                };
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Billing Group', 'Billing Group In Use Cannot Be Deleted.');
                }
            }
        });


        $('#customer_billing_group').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/customer/customer_billing_table",
                type: "POST"
            },
            serverSide: true,
            columns:[
                {data: "customer_billing_group.name"},
                {data:"trade_type"},
                {data: "customer_billing_group.extra_free_rent_days"},
                {data: "tax"},
                {data: "customer_billing_group.waiver_pct"},
                {data: "customer_billing_group.waiver_amount"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Customer Billing Group')
        });


    }
}

var DepotActivity = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/depot_activity/table",
            table: "#depotActivity",
            template: '#depotForm',
            fields: [  {
                label: "Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxLength: 100
                }
            }, {
                    label: "Billable",
                    name: "billable",
                    def: 1,
                    attr: {
                        class: "form-control",
                        disabled:true
                    }
                }, {
                label: "Default",
                name: "is_default",
                type: "select",
                def: 0,
                attr: {
                    class: "form-control",
                },
                options: [
                    {label: "NO", value: "0"},
                    {label: "YES", value: "1"}
                ],
            }]
        });

        editor.field('billable').hide();


        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Activities', 'Activities In Use Cannot Be Deleted.');
                }
            }
            if (action === "edit"){
                if (status.length > 0) {
                    Modaler.dModal('Unable To Edit Activities', 'This Activity\'s name can not be edited.');
                }
            }
        });

        $('#depotActivity').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/depot_activity/table",
                type: "POST"
            },
            serverSide: true,
            // order: [[ 0, 'asc' ]],
            columns: [
                { data: "name" },
                { data: "deft" }
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Depot Activity')
        });
    }
}

var Agency = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/agency/table",
            table: "#agency",
            template: '#customForm',
            fields: [ {
                label: "Code:",
                name: "code",
                attr: {
                    class: "form-control",
                    maxlength:15
                }
            }, {
                label: "Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength:150
                }
            }, {
                label: "Address Line 1:",
                name: "address_line_1",
                attr: {
                    class: "form-control",
                    maxlength:250
                }
            }, {
                label: "Address Line 2:",
                name: "address_line_2",
                attr: {
                    class: "form-control",
                    maxlength:150
                }
            }, {
                label: "Address Line 3:",
                name: "address_line_3",
                attr: {
                    class: "form-control",
                    maxlength:100
                }
            }, {
                label: "Telephone:",
                name: "telephone",
                attr: {
                    class: "form-control",
                    maxlength:20
                }
            }, {
                label: "Email:",
                name: "email",
                attr: {
                    class: "form-control",
                    maxlength:100
                }
            }, {
                label: "Fax:",
                name: "fax",
                attr: {
                    class: "form-control",
                    maxlength:50
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Agency', 'Agency In Use Cannot Be Deleted.');
                }
            }
        });

        $('#agency').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/agency/table",
                type: "POST"
            },
            serverSide: true,
            columns: [
                { data: "code" },
                { data: "name" },
                { data: "address_line_1" },
                { data: "address_line_2" },
                { data: "address_line_3" },
                { data: "telephone" },
                { data: "email" },
                { data: "fax" }
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Agency')
        });
    }
}

var TruckingCompany = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/trucking_company/table",
            table: "#company",
            template: '#customForm',
            fields: [ {
                label: "Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Trucking Company', 'Trucking Company In Use Cannot Be Deleted.');
                }
            }
        });

        $('#company').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/trucking_company/table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                { data: "name" }
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Trucking Company')
        });
    }
}

var LetPass = {

    driverEdit:function(id,number){

        var header = number;
        var body = "<div class=\"col-md-12\"><table id=\"driver_edit\" class=\"display table-responsive\">" +
            "<thead><tr><th>License </th><th>Driver Name</th></tr></thead>" +
            "</table></div>";
        CondModal.cModal(header, body);

        EditDriver.initTable(id);
    },

    iniTable: function () {
        var invoice = '';
        var containers = [];
        var selected_containers = [];

        $('#container-tab').removeClass('active');
        $('#driver-tab').removeClass('active');
        $('#letpass-tab').removeClass('active');

        $('#container-tab').on('click', function() {

        });

        $('#invoice-button').on('click', function() {
            var container_list = document.getElementById('container');
            container_list.innerHTML = '';
            // check invoice
            if ($('#invoice').val().trim() == '') {
                $('#invoice-error').text('Please enter an invoice number');
                return;
            }
            else {
                var inv_num = $('#invoice').val().trim();

                var invoice_check = false;

                $.ajax({
                    url: "/api/let_pass/get_unpaid_invoices",
                    type: "POST",
                    async: false,
                    data: {invn: inv_num},
                    success: function (data) {
                        var response = $.parseJSON(data);

                        if (response.st == 252) {
                            var invoice_count = response['inv'].length;


                            if (invoice_count > 0) {
                                var invoices = response['inv']
                                ;
                                var invoiceList = invoices.join(', ');

                                $('#invoice-error').text('The following invoices are not paid: ' + invoiceList + '.');

                                return;
                            }
                        }
                        else {

                            $('#invoice-error').text('Invalid invoice number.');

                            return;
                        }

                        containers = response['cont'];
                        if (containers.length == 0) {
                            $('#invoice-error').text('There are currently no containers available under this invoice for a let pass.');

                            return;
                        }

                        invoice_check = true;
                    },
                    error: function () {
                        Modaler.dModal('Error', 'Something went wrong');
                    }
                });

                if (invoice_check && containers.length > 0) {
                    //parse containers
                    var container_list = document.getElementById('container');
                    container_list.innerHTML = '';
                    var number = 1;
                    for (var container in containers) {
                        var label = document.createElement('label');
                        label.setAttribute('class', 'custom-control custom-control-lg custom-checkbox');

                        var span1 = document.createElement('span');
                        span1.setAttribute('class', 'custom-control-indicator');

                        var num = containers[container].num;
                        var id = containers[container].id;

                        var span2 = document.createElement('span');
                        span2.setAttribute('class', 'custom-control-description');
                        span2.innerText = num;

                        var checbox = document.createElement('input');
                        checbox.setAttribute('class', 'custom-control-input');
                        checbox.setAttribute('type', 'checkbox');
                        checbox.setAttribute('value', id);
                        checbox.setAttribute('id', 'containerNo' + number++);

                        label.appendChild(checbox);
                        label.appendChild(span1);
                        label.appendChild(span2);

                        var list = document.createElement('li');
                        list.appendChild(label);

                        container_list.appendChild(list);

                        invoice = $('#invoice').val().trim();
                        //activate next panel
                        $('#invoice-error').text('');
                        $('#container-tab').addClass('active');
                        $('#invoice-view').removeClass('show');
                        $('#invoice-view').removeClass('active');
                        $('#container-view').addClass('show');
                        $('#container-view').addClass('active');
                        $('#invoice-tab').removeClass('active');
                    }
                }
            }
        });

        $('#container-button').on('click', function() {
            $('#drivers_error').text("");
            selected_containers = [];
            var container_list = document.getElementById('container');
            var boxes = container_list.getElementsByTagName('input');
            $('#selection_error').text('');
            for (input in boxes) {
                if (boxes[input].checked == true) {
                    selected_containers.push(boxes[input].value);
                }
            }

            if (selected_containers.length == 0){
                $('#selection_error').text('Container must be selected');
            }
            else {
                $.ajax({
                    url: "/api/let_pass/check_container",
                    type: "POST",
                    data: {
                        cnsl: JSON.stringify(selected_containers)
                    },
                    success: function (data) {
                        if (data) {
                            var result = JSON.parse(data);

                            if (result.st == 253) {
                                $('#driver-tab').addClass('active');
                                $('#container-view').removeClass('show');
                                $('#container-view').removeClass('active');
                                $('#driver-view').addClass('show');
                                $('#driver-view').addClass('active');
                                $('#container-tab').removeClass('active');
                            }
                            else if (result.st == 153) {
                                $('#selection_error').text("The following containers are flagged and cannot be let passed: " + result.flct.join(','));
                            }
                            else if (result.st == 154) {
                                $('#selection_error').text("The following containers are already gated out. Please restart the process. " + result.gtct.join(','));
                            }
                            else if (result.st == 155) {
                                $('#selection_error').text("The following containers already have a let pass. Please remove them from the selection, or delete the let pass. " + result.lpct.join(','));
                            }
                            else {
                                $('#selection_error').text("An unknown error has occured.");
                            }
                        }
                    },
                    error: function () {
                        alert("something went wrong");
                    }
                });

            }

            $('#letpass_wrapper').remove();
            var letpass_form_input = '<div id="letpass_wrapper" class="row letpass_form"></div>';
            $('#wrapper_input').append(letpass_form_input);

            var driver_input = '<div id="input_outer" class="input_m">'+
                              '<div class="letpas_input">'+
                                '<p><input class="form-control" placeholder="Vehicle License Plate" maxlength="18"></p>'+
                                '<p><span class="vehicle_error" style="color:red;"></span></p>'+
                               '</div>'+
                               '<div class="letpas_input">'+
                               '<p><input class="form-control" placeholder="Driver Name"/></p>'+
                               '<p><span class="driver_error" style="color:red;"></span></p>'+
                               '</div>'+
                               '<div id="f_button" class="input_button">'+
                               '<button style="margin-right:5px" onclick="LetPass.addbutton(event)">Add</button>'+
                               '<button style="margin-right:10px" disabled>Remove</button>'+
                            '</div>'+
                            '</div>';
                            $('#letpass_wrapper').append(driver_input);
        });

        $('#driver-button').on('click', function() {
           
            var driver_form = document.getElementById('drivers');
            var textbox_groups = driver_form.getElementsByClassName('input_m');
            var drivers = [];
            var is_complete = true;

            var repeated_vehicle ="";

            for (pair in textbox_groups)
            {
                if (isNaN(pair)) {
                    break;
                }
                var driver_input = textbox_groups[pair].getElementsByTagName('input');
                var error_span = textbox_groups[pair].getElementsByClassName('driver_error');
                var vehicle_error = textbox_groups[pair].getElementsByClassName('vehicle_error');

                $(error_span).text("");
                $(vehicle_error).text("");

                var driver = {
                    license: driver_input[0].value.trim(),
                    name: driver_input[1].value.trim()
                };

                const vehicle_patterns = /^\(?([A-Za-z]{2})\)?[-. ]?([0-9]{3,4})[-. ]?([A-Za-z0-9]{1,2})$/g;
                let vehicle = driver.license.match(vehicle_patterns);
            
                if (driver.license == ""){
                    $(vehicle_error).text("Please Enter The Vehicle's License plate");
                    is_complete = false;
                }
                else if(!vehicle){
                    $(vehicle_error).text("Vehicle's License number is not valid");
                     is_complete = false;
                }
                else if(driver.license == repeated_vehicle){
                    $(vehicle_error).text("Vehicle number repeated");
                    is_complete = false;
                }
                if (driver.name == ""){
                    $(error_span).text("Please Enter The Driver's Name");
                     is_complete = false;
                }
                if(driver.name != "" && driver.license != "") {
                    drivers.push(driver);
                }
                repeated_vehicle = vehicle;
            }

            if (!is_complete) {
                return;
            }

            if (invoice.trim() != "" && selected_containers.length > 0 && drivers.length > 0) {

                $.ajax({
                    url: "/api/let_pass/generate",
                    type: "POST",
                    data: {
                        ctsl: JSON.stringify(selected_containers),
                        invn: invoice,
                        drvs: JSON.stringify(drivers)
                    },
                    success: function (data) {
                        if (data) {
                            var result = JSON.parse(data);

                            if (result.st == 153) {
                                $('#drivers_error').text("The following containers are flagged and cannot be let passed: " + result.flct.join(','));
                            }
                            else if (result.st == 154) {
                                $('#drivers_error').text("The following containers are already gated out. Please restart the process. " + result.gtct.join(','));
                            }
                            else if (result.st == 155) {
                                $('#drivers_error').text("The following containers already have a let pass. Please remove them from the selection, or delete the let pass. " + result.lpct.join(','));
                            }
                            else if (result.st == 156) {
                                $('#drivers_error').text("The invoice number has not been provided.");
                            }
                            else if (result.st == 157) {
                                $('#drivers_error').text("No containers has been selected.");
                            }
                            else if (result.st == 158) {
                                $('#drivers_error').text("No driver details have been provided.");
                            }
                            else if (result.st == 159) {
                                $('#drivers_error').text("The following invoices have not been paid. " + result.inv.join(','));
                            }
                            else if (result.st == 251) {
                                $('#letpass_link').html('<a href="/api/let_pass/show_let_pass/' + result.ltps + '" target="_blank">View Let Pass</a>');
                                $('#letpass-tab').addClass('active');
                                $('#driver-view').removeClass('show');
                                $('#driver-view').removeClass('active');
                                $('#letpass-view').addClass('show');
                                $('#letpass-view').addClass('active');
                                $('#driver-tab').removeClass('active');
                                $('#invoice-tab').removeAttr('href', '#invoice-view');
                            }
                            else {
                                $('#drivers_error').text("An unknown error has occured.");
                            }
                        }
                    },
                    error: function () {
                        alert("something went wrong");
                    }
                });

            }
        });


        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/let_pass/table",
            table: "#let_pass_record",
            formOptions: {
                main: {
                    focus: null
                }
            },
            fields: [
                {
                    label:"Date:",
                    name:'letpass.date',
                    type: "datetime",
                    def: function() { return new Date();},
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }
            ]
        });


        $('#let_pass_record').DataTable( {
            dom: "Bfrtip",
            table:"#let_pass_record",
            ajax: {
                url:"/api/let_pass/table",
                type:"POST"
            },
            serverSide: true,
            columnDefs: [ { "searchable": false, "targets": 4 } ],
            order: [[ 2, 'desc' ]],
            columns: [
                { data: "lnum" },
                { data: "invd"},
                { data:"date"},
                { data:"stat"},
                { data: null,
                    render:function (data, type, row) {

                        var let_pass = '';
                        let_pass += '<a href="/api/let_pass/show_let_pass/'+ data.lnum + '" target="_blank">View</a><br/>';
                        let_pass  +=  '<a href="#" onclick="LetPass.driverEdit(' + data.id + ',' + '\'' + data.lnum+ '\'' + ')">Edit Driver</a><br>';
                        return let_pass;
                    }}
            ],
            select: true,
            buttons: LetPassHelper.permissionButtonBuilder(editor,'Let Pass Date',)
        });
    },

    addbutton:function(e){
       e.preventDefault();
        var new_input = '<div id="row_input" class="input_m"><div class="letpas_input">'+
        '<p><input class="form-control" placeholder="Vehicle License Plate" maxlength="18"></p>'+
        '<p><span class="vehicle_error" style="color:red;"></span></p>'+
    '</div>'+
    '<div class="letpas_input">'+
        '<p><input class="form-control" placeholder="Driver Name"/></p>'+
        '<p><span class="driver_error" style="color:red;"></span></p>'+
     '</div>'+
     '<div id="f_button" class="input_button">'+
     '<button style="margin-right:6px" onclick="LetPass.addbutton(event)">Add</button>'+
     '<button style="margin-right:10px" onclick="LetPass.removebutton(event)">Remove</button>'+
    '</div>'+
     '</div>';
     $('#letpass_wrapper').append(new_input);
    },

    removebutton:function(e){
        e.preventDefault();
        $('#row_input').remove();
    }

}

var Invoicing = {

    is_proforma:false,

    allBoxesChecked: function (boxes) {
        let verify = true;

        boxes.forEach(box => {
            if (box.checked == false)
                verify = false;
        });

        return verify;
    },

    initialize: function () {

        var checkTrade, checkB, checkCurrency, checkTax;
        var customers;
        var voyage_id = 1;
        var selectedContainers = [];


        if ($('#trade_type').val() == 11){
            $('.voyage').hide();
        }
        else if ($('#trade_type').val() == 21){
            $('.voyage').show();
        }

        $('#trade_type').on('change', function () {

            if ($('#trade_type').val() == 11){
                $('#label_number').text('BL Number');
                $('.voyage').hide();
            }
            else if ($('#trade_type').val() == 21){
                $('#label_number').text('Booking Number');
                $('.voyage').show();
            }
            else if ($('#trade_type').val() == 70){
                $('#label_number').text('Booking Number');
                $('.voyage').hide();
            }
            else if ($('#trade_type').val() == 13){
                $('#label_number').text('BL Number');
                $('.voyage').hide();
                $('#tax_type').val(4)
            }
            $('#b_number').val("");
            $('#voyage_id').val("");
            $('#error_label').text('');
            $('#v_error_label').text('');

        });

        $('#customer_id, #trade_type').on('change keyup mouseup input focusout', function () {
            var customer = $('#customer_id').val();
            var trade_type =  $('#trade_type').val();
            if (customer.trim()){
                $.ajax({
                    url: "/api/invoice/get_billing_group",
                    type: "POST",
                    async: false,
                    data: {
                        cust: customer,
                        trty: trade_type
                    },
                    success: function (data) {
                        var billing_group = JSON.parse(data);
                        if (billing_group.st == 2211){
                            $('.billing').show();
                            $('#billing_group').text(billing_group.name);
                        }
                        else {
                            $('.billing').hide();
                        }
                    },
                    error: function () {
                        $('#manualHeader').text('ERROR');
                        $('#manualAlert').text('Something Went Wrong');
                    }
                });
            }
            else  {
                $('.billing').hide();
            }

        });

        let selectAllCheck;
        let containerChecks;


        $('#next').on('click', function() {

            $('#error_label').text('');
            $('#customer_error').text('');
            $('#v_error_label').text('');
            $('#selection_error').text('');
            $('#error_tax_label').text('');

            selectedContainers = [];


            var trade_type = $('#trade_type').val();
            checkTrade = trade_type;

            var b_number = $('#b_number').val();
            checkB = b_number;
            var customer = $('#customer_id').val();
            var tax_type = $('#tax_type').val();

            var failed = false;

            if (customer == '') {
                $('#customer_error').text('Customer field cannot be empty');
                failed = true;
            }

            if (trade_type == '13' && $('#boe_number').val() == '') {
                $('#error_boe_label').text('TRANSIT trade requires BOE Number');
                failed = true;
            }

            var voyage = $('#voyage_id').val();


            if (b_number == '') {
                $('#error_label').text('Cannot enter empty field');
                failed = true;
            }
            else if (voyage == '' && $('#trade_type').val() == 21) {
                $('#v_error_label').text('Voyage must be set for export trades');
                failed = true;
            }

            if (failed)
                return;

            var voyageCheck = true;

            if ($('#trade_type').val() == 21) {
                voyageCheck = false;

                $.ajax({
                    url: "/api/invoice/check_voyage",
                    type: "POST",
                    async: false,
                    data: {vygi: voyage, tdty: trade_type},
                    success: function (data) {
                        var result = $.parseJSON(data);
                        if (result.st == 2111) {
                            voyageCheck = true;
                            voyage_id = Number(result.vyg);
                        }
                        else if(result.st == 1118){
                            $('#v_error_label').text("Actual Arrival or Actual Departure dates have not been set for this voyage.");
                        }
                        else if (result.st == 1113){
                            $('#v_error_label').text("Wrong Trade Type");
                        }
                        else if (result.st == 1114){
                            $('#v_error_label').text("Voyage Not Found");
                        }
                    },
                    error: function () {
                        $('#manualHeader').text('ERROR');
                        $('#manualAlert').text('Something Went Wrong');
                    }
                });
            }

            if (voyageCheck) {
                $.ajax({
                    url: "/api/invoice/get_containers",
                    type: "POST",
                    data: {
                        bnum: b_number,
                        tdty: trade_type,
                        cust: customer,
                        vygi: voyage,
                        txty: tax_type,
                        prof: Invoicing.is_proforma ? 1 : 0,
                    },
                    success: function (data) {
                        var result = $.parseJSON(data);
                        var number_type = trade_type == "11" ? "BL Number " : "Booking Number ";
                        if (result.st == 1110) {
                            number_type += b_number + " does not exist.";
                            $('#error_label').text(number_type);
                        }
                        if (result.st == 1111) {
                            $('#customer_error').text('Customer does not exist');
                        }
                        if (result.st == 1112) {
                            $('#error_label').text("Actual Arrival and Actual Departure not set for " + result.vyg);
                        }
                       if (result.st == 1119){
                            $('#error_tax_label').text("There are no tax created for this tax type");
                        }
                        if (result.st == 2110) {
                            if (result.uninv != undefined && result.uninv.length > 0) {
                                var available_containers = result.uninv;
                                if (result.cntr != undefined && result.cntr.length > 0) {
                                    available_containers = available_containers.filter(function (container) {
                                        return result.cntr.indexOf(container) < 0;
                                    })
                                }

                                if (available_containers.length > 0) {
                                    var container_list = document.getElementById('container');
                                    container_list.innerHTML = '';
                                    var number = 1;
                                    for (var container in available_containers) {
                                        var label = document.createElement('label');
                                        label.setAttribute('class', 'custom-control custom-control-lg custom-checkbox');

                                        var span1 = document.createElement('span');
                                        span1.setAttribute('class', 'custom-control-indicator');

                                        var span2 = document.createElement('span');
                                        span2.setAttribute('class', 'custom-control-description');
                                        span2.innerText = available_containers[container];

                                        var checbox = document.createElement('input');
                                        checbox.setAttribute('class', 'custom-control-input');
                                        checbox.setAttribute('type', 'checkbox');
                                        checbox.setAttribute('value', available_containers[container]);
                                        checbox.setAttribute('id', 'containerNo' + number++);

                                        label.appendChild(checbox);
                                        label.appendChild(span1);
                                        label.appendChild(span2);

                                        var list = document.createElement('li');
                                        list.appendChild(label);

                                        container_list.appendChild(list);
                                    }

                                    $('#home-left').removeClass('active');
                                    $('#home-left').removeClass('show');
                                    $('#homes').removeClass('active');
                                    $('#profile-left').addClass('active');
                                    $('#profile-left').addClass('show');
                                    $('#profiles').addClass('active');
                                }
                                else {
                                    var containers = result.cntr.join(', ');
                                    $('#error_label').text("Container numbers " + containers + " do not have info.");
                                }

                                if (available_containers.length > 1) {
                                    document.getElementById('select-all-panel').style.display = 'block';
                                    selectAllCheck = document.getElementById('select-all');
                                    containerChecks = document.querySelectorAll('#container .custom-control-input');

                                    selectAllCheck.addEventListener('change', function (e) {
                                        let checkedStatus = e.target.checked;
                                        if (checkedStatus) {
                                            containerChecks.forEach(checkbox => {
                                                checkbox.checked = true;
                                            });
                                        } else {
                                            containerChecks.forEach(checkbox => {
                                                if (checkbox.checked)
                                                    checkbox.checked = false;
                                            });
                                        }
                                    }, false);

                                    containerChecks.forEach(checkbox => {
                                        checkbox.addEventListener('change', function (e) {
                                            if (e.target.checked && Invoicing.allBoxesChecked(containerChecks))
                                                selectAllCheck.checked = true;
                                            else
                                                selectAllCheck.checked = false;
                                        })
                                    });
                                }
                            }
                            else {
                                $('#error_label').text("Containers under "+number_type+" have not either been gated in or have been invoiced");
                            }
                        }
                    },
                    error: function () {
                        $('#manualHeader').text('ERROR');
                        $('#manualAlert').text('Something Went Wrong');
                    }
                });
            }
        });

        var charges = 0;
        var ucl_check = false;

        $('#preview_button').on('click',function (e) {
            $('#preview_button').attr("disabled", true);
            var datePattern = new RegExp("\\d{4}-\\d{2}-\\d{2}");
            var customer = $('#customer_id').val();

            var date = new Date();
            var dd = (date.getDate() < 10 ? '0' : '') + date.getDate();
            var MM = ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1);
            var yyyy = date.getFullYear();
            var  currentDate = yyyy + "-" + MM + "-" + dd;
            var pDateValue = document.getElementById('upto_date').value;

            if (!datePattern.test(pDateValue)) {
                Modaler.dModal('ERROR', 'Wrong Paid-Up-To Date');
            }
            else {

                if (!(pDateValue >= currentDate)) {
                    Modaler.dModal('ERROR', 'Invalid Date');
                }
                else {

                    var customers_id = document.getElementById('customer_id').value;
                    var b_number = $('#b_number').val();
                    var boe_number = $('#boe_number').val();
                    var do_number = $('#do_number').val();
                    var release_instructions = $('#release option:selected').val();

                    var container_list = document.getElementById('container');

                    var tax_type = document.getElementById('tax_type').value;
                    var invoice_currency = document.getElementById('currency').value;
                    var note = document.getElementById('note').value;
                    inputs = container_list.getElementsByTagName('input');


                    var invoice_number = '';

                    var trade = $('#trade_type').val();

                    //ajax request for inserting data into invoice

                    var list = [];
                    for (input in inputs) {
                        if (inputs[input].checked == true) {
                            list.push(inputs[input].value.split(" ")[0]);
                        }
                    }

                    $("#storage_button").attr("disabled", true);

                    $.ajax({
                        url: "/api/invoice_preview/previewInvoice",
                        type: "POST",
                        data: {
                            bnum: b_number,
                            dnum: do_number,
                            bonum: boe_number,
                            rel: release_instructions,
                            tax: tax_type,
                            curr: invoice_currency,
                            note: note,
                            pdate: pDateValue,
                            trty: trade,
                            voy: voyage_id,
                            cust: customers_id,
                            prof:Invoicing.is_proforma ? 1 : 0,
                            cont: JSON.stringify(list)
                        },
                        success: function (data) {
                            let parsedData = JSON.parse(data);
                            if( ActivityCheckCharges.checkCharges(parsedData) && parsedData.st == 2844){
                                $('#preview-left .removable').remove();

                            document.getElementById('company-name').innerHTML = parsedData.companyName;
                            document.getElementById('company-address').innerHTML = parsedData.companyLocation;

                            let contactString = parsedData.companyPhone + "  ||  " + parsedData.companyMail + "  ||  " 
                                    + parsedData.companyWeb;
                            document.getElementById('company-contacts').innerHTML = contactString;

                            document.getElementById('invoice-date').innerHTML = parsedData.invoiceDate;
                            document.getElementById('invoice-no').innerHTML = parsedData.invoiceNumber;
                            document.getElementById('paid-up-to').innerHTML = parsedData.paidUpTo;
                            document.getElementById('tin').innerHTML = parsedData.tin;
                            
                            if (trade == '11' || trade == '13') {
                                document.getElementById('importer-td').innerHTML = parsedData.importerAddress;
                                document.getElementById('agency-td').innerHTML = parsedData.agency;
                                document.getElementById('release-instructions-td').innerHTML = parsedData.releaseInstructions;
                                document.getElementById('customer-td').innerHTML = parsedData.customer;

                                document.getElementById('vessel-td').innerHTML = parsedData.vessel;
                                document.getElementById('voyage-no-td').innerHTML = parsedData.voyageNumber;
                                document.getElementById('arrival-date-td').innerHTML = parsedData.arrivalDate;
                                document.getElementById('departure-date-td').innerHTML = parsedData.departureDate;
                                document.getElementById('rotation-number-td').innerHTML = parsedData.rotationNumber;

                                document.getElementById('bl-number-td').innerHTML = parsedData.bNumber;
                                document.getElementById('boe-number-td').innerHTML = parsedData.boeNumber;
                                document.getElementById('do-number-td').innerHTML = parsedData.doNumber;
                                document.getElementById('release-date-td').innerHTML = '';
                                document.getElementById('containers-td').innerHTML = parsedData.containers;
                            } else if (trade == '21') {
                                document.getElementById('shipper-td').innerHTML = parsedData.shipper;
                                document.getElementById('ship-line-td').innerHTML = parsedData.shippingLine;
                                document.getElementById('exp-customer-td').innerHTML = parsedData.customer;

                                document.getElementById('exp-vessel-td').innerHTML = parsedData.vessel;
                                document.getElementById('exp-voyage-no-td').innerHTML = parsedData.voyageNumber;
                                document.getElementById('booking-number-td').innerHTML = parsedData.bNumber;
                                document.getElementById('booking-date-td').innerHTML = parsedData.bookingDate;

                                document.getElementById('exp-containers-td').innerHTML = parsedData.containers;

                            } else if (trade == '70') {
                                document.getElementById('emp-shipper-td').innerHTML = parsedData.shipper;
                                document.getElementById('emp-ship-line-td').innerHTML = parsedData.shippingLine;
                                document.getElementById('emp-customer-td').innerHTML = parsedData.customer;

                                document.getElementById('emp-booking-number-td').innerHTML = parsedData.bNumber;
                                document.getElementById('emp-booking-date-td').innerHTML = parsedData.bookingDate;

                                document.getElementById('emp-containers-td').innerHTML = parsedData.containers;

                            }

                            let midInfo; 
                            switch (trade) {
                                case '11':
                                    midInfo = document.querySelector('.mid-info.import');
                                    break;
                                    
                                case '21':
                                    midInfo = document.querySelector('.mid-info.export');
                                    break;

                                case '70':
                                    midInfo = document.querySelector('.mid-info.empty');
                                    break;

                                default:
                                    midInfo = document.querySelector('.mid-info.import');
                                    break;

                            }
                            console.log(trade);

                            $(midInfo).css('position', 'static');
                            let previewLeft = document.getElementById('preview-left');
                            console.log(midInfo.cloneNode(true));
                            previewLeft.insertBefore(midInfo.cloneNode(true), document.querySelector('div.business'));
                            previewLeft.insertBefore(document.createElement('br'), document.querySelector('div.business'));

                            let fragment = document.createDocumentFragment();
    
                                parsedData.activities.forEach(activity => {
                                    let row = document.createElement('tr');
                                    $(row).attr('class', 'removable');
    
                                    let descriptionData = document.createElement('td');
                                    descriptionData.innerHTML = activity.description;
    
                                    let quantityData = document.createElement('td');
                                    quantityData.innerHTML = activity.qty;
                                    
    
                                    let costData = document.createElement('td');
                                    costData.setAttribute('class', 'table-money');
                                    costData.innerHTML = activity.cost;
    
                                    let totalCostData = document.createElement('td');
                                    totalCostData.setAttribute('class', 'table-money');
                                    totalCostData.innerHTML = activity.total_cost;
    
                                    row.append(descriptionData, quantityData, costData, totalCostData);
                                    fragment.append(row);
                                });
    
                                let secondFragment = document.createDocumentFragment();
    
                                parsedData.taxDetails.forEach(taxDetail => {
                                    let emptyData = document.createElement('td');
                                    let row = document.createElement('tr');
                                    $(row).attr('class', 'removable');
    
                                    let taxName = document.createElement('th');
                                    taxName.setAttribute('colspan', '2');
                                    taxName.innerHTML = taxDetail.details;
    
                                    let taxAmount = document.createElement('td');
                                    taxAmount.setAttribute('class', 'table-money');
                                    taxAmount.innerHTML = taxDetail.amount;
    
                                    row.append(emptyData, taxName, taxAmount);
                                    secondFragment.append(row);
    
                                });
    
    
                                let mainTableBody = document.getElementById('main-table');
                                console.log(mainTableBody);
    
                                mainTableBody.insertBefore(fragment, (mainTableBody.rows)[0]);
    
                                mainTableBody.insertBefore(secondFragment, (mainTableBody.rows)[mainTableBody.rows.length - 2]);
    
                                document.getElementById('subtotal').innerHTML = parsedData.subtotal;
    
                                document.getElementById('total-tax').innerHTML = parsedData.totalTax;
                                document.getElementById('total-amount').innerHTML = parsedData.totalAmount;
    
                                let thirdFragment = document.createDocumentFragment();
                                let count = 0;
    
                                parsedData.containerDetails.forEach(containerDetail => {
                                    let row = document.createElement('tr');
                                    $(row).attr('class', 'removable');
    
                                    let number = document.createElement('td');
                                    number.innerHTML = ++count;
    
                                    let containerNumber = document.createElement('td');
                                    containerNumber.innerHTML = containerDetail.number;
    
                                    let isoType = document.createElement('td');
                                    isoType.innerHTML = containerDetail.code;
    
                                    let containerType = document.createElement('td');
                                    containerType.innerHTML = containerDetail.containerType;
    
                                    let goodDescription = document.createElement('td');
                                    goodDescription.innerHTML = containerDetail.goods;
    
                                    row.append(number, containerNumber, isoType, containerType, goodDescription);
                                    thirdFragment.append(row);
                                });
    
                                document.getElementById('container-list').append(thirdFragment);
    
                                midInfo.style.position = 'absolute';
                           
                                $('#messages-left').removeClass('active');
                                $('#messages-left').removeClass('show');
                                $('#charge-link').removeClass('active');
                                $('#charge-link').removeAttr('href', '#messages-left');
                                $('#preview-left').addClass('active');
                                $('#preview-left').addClass('show');
                                $('#preview-link').addClass('active');
                                $('#preview-link').attr('href', '#preview-left');
                            }
                        },
                        error: function () {
                            $('#manualHeader').text('ERROR');
                            $('#manualAlert').text('Something Went Wrong');
                        }
                    });
                }
            }
        });

        $('#invoice_button').on('click',function (e) {

            $('#invoice_button').attr("disabled", true);

            var datePattern = new RegExp("\\d{4}-\\d{2}-\\d{2}");
            var customer = $('#customer_id').val();

            var date = new Date();
            var dd = (date.getDate() < 10 ? '0' : '') + date.getDate();
            var MM = ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1);
            var yyyy = date.getFullYear();
            var  currentDate = yyyy + "-" + MM + "-" + dd;
            var pDateValue = document.getElementById('upto_date').value;

            if (!datePattern.test(pDateValue) && ucl_check == false) {
                Modaler.dModal('ERROR', 'Wrong Paid-Up-To Date');
            }
            else {

                if (!(pDateValue >= currentDate) && ucl_check == false) {
                    Modaler.dModal('ERROR', 'Invalid Date');
                }
                else {

                    var customers_id = document.getElementById('customer_id').value;
                    var b_number = $('#b_number').val();
                    var boe_number = $('#boe_number').val();
                    var do_number = $('#do_number').val();
                    var release_instructions = $('#release option:selected').val();

                    var container_list = document.getElementById('container');

                    var tax_type = document.getElementById('tax_type').value;
                    var invoice_currency = document.getElementById('currency').value;
                    var note = document.getElementById('note').value;
                    inputs = container_list.getElementsByTagName('input');


                    var invoice_number = '';

                    var trade = $('#trade_type').val();

                    //ajax request for inserting data into invoice

                    var list = [];
                    for (input in inputs) {
                        if (inputs[input].checked == true) {
                            list.push(inputs[input].value.split(" ")[0]);
                        }
                    }
                    

                    $("#storage_button").attr("disabled", true);

                    $.ajax({
                        url: "/api/invoice/add_invoice",
                        type: "POST",
                        data: {
                            bnum: b_number,
                            dnum: do_number,
                            bonum: boe_number,
                            rel: release_instructions,
                            tax: tax_type,
                            curr: invoice_currency,
                            note: note,
                            pdate: pDateValue != "" ? pDateValue : null,
                            trty: trade,
                            voy: voyage_id,
                            cust: customers_id,
                            prof:Invoicing.is_proforma ? 1 : 0,
                            cont: JSON.stringify(list)
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (ActivityCheckCharges.checkCharges(result) && result.st == 2211) {
                                invoice_number = result.invn;
                                if (Invoicing.is_proforma) {
                                    if ($('#trade_type').val() == 11) {
                                        $('#invoice_link').html('<a href="/api/proforma_import_invoice/show_import/' + invoice_number + '" target="_blank">View Import Invoice</a>');
                                    }
                                    if ($('#trade_type').val() == 13) {
                                        $('#invoice_link').html('<a href="/api/proforma_transit_invoice/show_transit/' + invoice_number + '" target="_blank">View Transit Invoice</a>');
                                    }
                                    if ($('#trade_type').val() == 21) {
                                        $('#invoice_link').html('<a href="/api/proforma_export_invoice/show_export/' + invoice_number + '" target="_blank">View Export Invoice</a>');
                                    }
                                    if ($('#trade_type').val() == 70) {
                                        $('#invoice_link').html('<a href="/api/proforma_empty_invoice/show_export/' + invoice_number + '" target="_blank">View Empty Invoice</a>');
                                    }
                                }
                                else {
                                    if ($('#trade_type').val() == 11) {
                                        $('#invoice_link').html('<a href="/api/import_invoice/show_import/' + invoice_number + '" target="_blank">View Import Invoice</a>');
                                    }

                                    if ($('#trade_type').val() == 13) {
                                        $('#invoice_link').html('<a href="/api/transit_invoice/show_transit/' + invoice_number + '" target="_blank">View Transit Invoice</a>');
                                    }

                                    if ($('#trade_type').val() == 21) {
                                        $('#invoice_link').html('<a href="/api/export_invoice/show_export/' + invoice_number + '" target="_blank">View Export Invoice</a>');
                                    }

                                    if ($('#trade_type').val() == 70) {
                                        $('#invoice_link').html('<a href="/api/empty_invoice/show_empty/' + invoice_number + '" target="_blank">View Empty Invoice</a>');
                                    }
                                }

                                $('#preview-left').removeClass('active');
                                $('#preview-left').removeClass('show');
                                $('#preview-link').removeClass('active');
                                $('#preview-link').removeAttr('href', '#messages-left');
                                $('#invoice-left').addClass('active');
                                $('#invoice-left').addClass('show');
                                $('#invoice-link').addClass('active');
                                $('#invoice-link').attr('href', '#invoice-left');
                                $('#homes').removeAttr('href', '#home-left');
                            }
                            else {
                                $("#storage_button").attr("disabled", false);
                            }
                        },
                        error: function () {
                            $('#manualHeader').text('ERROR');
                            $('#manualAlert').text('Something Went Wrong');
                        }
                    });
                }
            }
        });


        $('#container-invoice').on('click',function (e) {
            e.preventDefault();
            $('#container-invoice').attr("disabled", true);

            selectedContainers = [];
            checkCurrency = document.getElementById('currency').value;
            var container_list = document.getElementById('container');
            var boxes = container_list.getElementsByTagName('input');
            for (input in boxes) {
                if (boxes[input].checked == true) {
                    selectedContainers.push(boxes[input].value.split(" ")[0]);
                }
            }

            if (selectedContainers.length == 0){
                $('#selection_error').text('Container must be selected');
            }
            else{
                $.ajax({
                    url:"/api/invoice/container_check",
                    type:"POST",
                    data:{
                        ctnr: JSON.stringify(selectedContainers),
                        trty: checkTrade == 13 ? 11 : checkTrade,
                        bnum: checkB,
                        curr: checkCurrency,
                        prof:Invoicing.is_proforma ? 1 : 0,
                    },
                    success:function (data) {
                        if (data) {
                            console.log(data);
                            var result = JSON.parse(data);

                            var header, body;

                            if(result.st == 1432){
                                header = "Container Depot Info Error";
                                body = "Containers depot info has not been added";
                                Modaler.dModal(header,body);
                            }

                            if (result.st == 1560){
                                header = "Container Depot Error";
                                body = "Containers selected are both in Depot and UCL";
                                Modaler.dModal(header,body);
                            }

                            if (result.st == 1570){

                                ucl_check = true;

                                var customers_id = document.getElementById('customer_id').value;
                                var b_number = $('#b_number').val();
                                var boe_number = $('#boe_number').val();
                                var do_number = $('#do_number').val();
                                var release_instructions = $('#release option:selected').val();

                                var container_list = document.getElementById('container');

                                var tax_type = document.getElementById('tax_type').value;
                                var invoice_currency = document.getElementById('currency').value;
                                var note = document.getElementById('note').value;
                                inputs = container_list.getElementsByTagName('input');

                                var invoice_number = '';

                                var trade = $('#trade_type').val();

                                //ajax request for inserting data into invoice

                                var list = [];
                                for (input in inputs) {
                                    if (inputs[input].checked == true) {
                                        list.push(inputs[input].value.split(" ")[0]);
                                    }
                                }


                                $.ajax({
                                    url: "/api/invoice_preview/previewInvoice",
                                    type: "POST",
                                    data: {
                                        bnum: b_number,
                                        dnum: do_number,
                                        bonum: boe_number,
                                        rel: release_instructions,
                                        tax: tax_type,
                                        curr: invoice_currency,
                                        note: note,
                                        trty: trade,
                                        voy: voyage_id,
                                        cust: customers_id,
                                        prof:Invoicing.is_proforma ? 1 : 0,
                                        cont: JSON.stringify(list)
                                    },
                                    success: function (data) {
                                        let parsedData = JSON.parse(data);
                                        if( ActivityCheckCharges.checkCharges(parsedData) && parsedData.st == 2844){
                                            $('#preview-left .removable').remove();
            
                                            document.getElementById('company-name').innerHTML = parsedData.companyName;
                                            document.getElementById('company-address').innerHTML = parsedData.companyLocation;
                
                                            let contactString = parsedData.companyPhone + "  ||  " + parsedData.companyMail + "  ||  " 
                                                    + parsedData.companyWeb;
                                            document.getElementById('company-contacts').innerHTML = contactString;
                
                                            document.getElementById('invoice-date').innerHTML = parsedData.invoiceDate;
                                            document.getElementById('invoice-no').innerHTML = parsedData.invoiceNumber;
                                            document.getElementById('paid-up-to').innerHTML = parsedData.paidUpTo;
                                            document.getElementById('tin').innerHTML = parsedData.tin;
                                            
                                            if (trade == '11') {
                                                document.getElementById('importer-td').innerHTML = parsedData.importerAddress;
                                                document.getElementById('agency-td').innerHTML = parsedData.agency;
                                                document.getElementById('release-instructions-td').innerHTML = parsedData.releaseInstructions;
                                                document.getElementById('customer-td').innerHTML = parsedData.customer;
                
                                                document.getElementById('vessel-td').innerHTML = parsedData.vessel;
                                                document.getElementById('voyage-no-td').innerHTML = parsedData.voyageNumber;
                                                document.getElementById('arrival-date-td').innerHTML = parsedData.arrivalDate;
                                                document.getElementById('departure-date-td').innerHTML = parsedData.departureDate;
                                                document.getElementById('rotation-number-td').innerHTML = parsedData.rotationNumber;
                
                                                document.getElementById('bl-number-td').innerHTML = parsedData.bNumber;
                                                document.getElementById('boe-number-td').innerHTML = parsedData.boeNumber;
                                                document.getElementById('do-number-td').innerHTML = parsedData.doNumber;
                                                document.getElementById('release-date-td').innerHTML = '';
                                                document.getElementById('containers-td').innerHTML = parsedData.containers;
                                            } else if (trade == '21') {
                                                document.getElementById('shipper-td').innerHTML = parsedData.shipper;
                                                document.getElementById('ship-line-td').innerHTML = parsedData.shippingLine;
                                                document.getElementById('exp-customer-td').innerHTML = parsedData.customer;
                
                                                document.getElementById('exp-vessel-td').innerHTML = parsedData.vessel;
                                                document.getElementById('exp-voyage-no-td').innerHTML = parsedData.voyageNumber;
                                                document.getElementById('booking-number-td').innerHTML = parsedData.bNumber;
                                                document.getElementById('booking-date-td').innerHTML = parsedData.bookingDate;
                
                                                document.getElementById('exp-containers-td').innerHTML = parsedData.containers;
                
                                            }
                
                                            let midInfo = trade == '11' ? document.querySelector('.mid-info.import') : document.querySelector('.mid-info.export');
                                            console.log(document.querySelector('div.business'));
                
                                            $(midInfo).css('position', 'static');
                                            let previewLeft = document.getElementById('preview-left');
                                            console.log(midInfo.cloneNode(true));
                                            previewLeft.insertBefore(midInfo.cloneNode(true), document.querySelector('div.business'));
                                            previewLeft.insertBefore(document.createElement('br'), document.querySelector('div.business'));
                
                                            let fragment = document.createDocumentFragment();
                
                                            parsedData.activities.forEach(activity => {
                                                let row = document.createElement('tr');
                                                $(row).attr('class', 'removable');
                
                                                let descriptionData = document.createElement('td');
                                                descriptionData.innerHTML = activity.description;
                
                                                let quantityData = document.createElement('td');
                                                quantityData.innerHTML = activity.qty;
                                                
                
                                                let costData = document.createElement('td');
                                                costData.setAttribute('class', 'table-money');
                                                costData.innerHTML = activity.cost;
                
                                                let totalCostData = document.createElement('td');
                                                totalCostData.setAttribute('class', 'table-money');
                                                totalCostData.innerHTML = activity.total_cost;
                
                                                row.append(descriptionData, quantityData, costData, totalCostData);
                                                fragment.append(row);
                                            });
                
                                            let secondFragment = document.createDocumentFragment();
                
                                            parsedData.taxDetails.forEach(taxDetail => {
                                                let emptyData = document.createElement('td');
                                                let row = document.createElement('tr');
                                                $(row).attr('class', 'removable');
                
                                                let taxName = document.createElement('th');
                                                taxName.setAttribute('colspan', '2');
                                                taxName.innerHTML = taxDetail.details;
                
                                                let taxAmount = document.createElement('td');
                                                taxAmount.setAttribute('class', 'table-money');
                                                taxAmount.innerHTML = taxDetail.amount;
                
                                                row.append(emptyData, taxName, taxAmount);
                                                secondFragment.append(row);
                
                                            });
                
                
                                            let mainTableBody = document.getElementById('main-table');
                                            console.log(mainTableBody);
                
                                            mainTableBody.insertBefore(fragment, (mainTableBody.rows)[0]);
                
                                            mainTableBody.insertBefore(secondFragment, (mainTableBody.rows)[mainTableBody.rows.length - 2]);
                
                                            document.getElementById('subtotal').innerHTML = parsedData.subtotal;
                
                                            document.getElementById('total-tax').innerHTML = parsedData.totalTax;
                                            document.getElementById('total-amount').innerHTML = parsedData.totalAmount;
                
                                            let thirdFragment = document.createDocumentFragment();
                                            let count = 0;
                
                                            parsedData.containerDetails.forEach(containerDetail => {
                                                let row = document.createElement('tr');
                                                $(row).attr('class', 'removable');
                
                                                let number = document.createElement('td');
                                                number.innerHTML = ++count;
                
                                                let containerNumber = document.createElement('td');
                                                containerNumber.innerHTML = containerDetail.number;
                
                                                let isoType = document.createElement('td');
                                                isoType.innerHTML = containerDetail.code;
                
                                                let containerType = document.createElement('td');
                                                containerType.innerHTML = containerDetail.containerType;
                
                                                let goodDescription = document.createElement('td');
                                                goodDescription.innerHTML = containerDetail.goods;
                
                                                row.append(number, containerNumber, isoType, containerType, goodDescription);
                                                thirdFragment.append(row);
                                            });
                
                                            document.getElementById('container-list').append(thirdFragment);
                
                                            midInfo.style.position = 'absolute';
                                            $('#error_label').remove();
                                            $('#profile-left').removeClass('active');
                                            $('#profile-left').removeClass('show');
                                            $('#profiles').removeClass('active');
                                            $('#profiles').removeAttr('href', '#profile-left');
                                            $('#preview-left').addClass('active');
                                            $('#preview-left').addClass('show');
                                            $('#preview-link').addClass('active');
                                            $('#preview-link').attr('href', '#preview-left');
                                        }
                                    },
                                    error: function () {
                                        $('#manualHeader').text('ERROR');
                                        $('#manualAlert').text('Something Went Wrong');
                                    }
                                });

                                return;
                            }

                            if (result.st == 2112) {
                                $('#error_label').remove();
                                $('#profile-left').removeClass('active');
                                $('#profile-left').removeClass('show');
                                $('#profiles').removeClass('active');
                                $('#profiles').removeAttr('href', '#profile-left');
                                $('#charge-link').attr('href', '#messages-left');
                                $('#messages-left').addClass('active');
                                $('#messages-left').addClass('show');
                                $('#charge-link').addClass('active');
                                return;
                            }
                            else if (result.st == 1115) {
                                var flagged = result.flag.join(",  ");
                                header = 'Error';
                                body = 'Container  <a href="/user/container">' + flagged + '&nbsp' + '</a> ' +
                                    'has been flagged.';
                                Modaler.dModal(header, body);
                            }
                            else if (result.st == 1116) {
                                var invoice = result.invs;
                                if (invoice[0] == "PAID") {

                                    header = invoice[0];
                                    body = 'A paid Invoice <a href="/user/invoice">' + invoice[1] + '</a> ' +
                                        'exist for the same selection of containers for this BL Number.';
                                    Modaler.dModal(header, body);

                                } else if (invoice[0] == "UNPAID") {

                                    header = invoice[0];
                                    body = 'An unpaid Invoice <a href="/user/invoice">' + invoice[1] + '</a> ' +
                                        'exist for the same selection of containers for' +
                                        ' this BL Number. Please cancel it first.';
                                    Modaler.dModal(header, body);
                                }
                            }

                            $('#error_label').remove();
                            $('#invoice').attr('disabled', 'true');
                            $('#invoice-link').removeAttr('href');
                            $('#charge-link').removeAttr('href');
                        }
                    },
                    error: function () {
                        alert("something went wrong");
                    }
                });
            }



        });
    }
}

var SupplementaryInvoice = {
    is_proforma : false,

    allBoxesChecked: function (boxes) {
        let verify = true;

        boxes.forEach(box => {
            if (box.checked == false)
                verify = false;
        });

        return verify;
    },

    iniTable: function () {

        var suppContainers = [];

        let selectAllCheck;
        let containerChecks;

        $('#supp_next').on('click', function () {

            $('#selection_error').text('');
            suppContainers = [];

            var sup_invoice_number = $('#sup_invoice_number').val();
            $('#supp_invoice').removeAttr('disabled');


            if (sup_invoice_number == ''){
                $('#error_label').text('Field cannot be empty');
            }
            else{

                $.ajax({
                    url:"/api/supp_invoice/sup_containers",
                    type:"POST",
                    data:{invn: sup_invoice_number},
                    success: function (data) {
                        var result = $.parseJSON(data);

                        if (result.st == 216){
                            $('#error_label').text("Incorrect Invoice Number");
                        }
                        else if (result.st == 217){
                            $('#error_label').text("Invoice has been cancelled");
                        }
                        else if (result.st == 218){
                            $('#error_label').text("Invoice has expired");
                        }
                        else if (result.st == 220){
                            $('#error_label').text("Unpaid invoice");
                        }
                        else if (result.st == 130){
                            $('#error_label').text("There no container(s) available to be invoiced");
                        }
                        else if (result.st == 2000){
                            var container_list = document.getElementById('container');
                            container_list.innerHTML = '';
                            var list_containers = result.cntr;
                            var number = 1;
                            for (var container in list_containers){

                                var label = document.createElement('label');
                                label.setAttribute('class', 'custom-control custom-control-lg custom-checkbox');

                                var span1 = document.createElement('span');
                                span1.setAttribute('class', 'custom-control-indicator');

                                var span2 = document.createElement('span');
                                span2.setAttribute('class', 'custom-control-description');
                                span2.innerText = list_containers[container];

                                var checbox = document.createElement('input');
                                checbox.setAttribute('class','custom-control-input');
                                checbox.setAttribute('type','checkbox');
                                checbox.setAttribute('value',list_containers[container]);
                                checbox.setAttribute('id','containerNo'+ number++);
                                
                                label.appendChild(checbox);
                                label.appendChild(span1);
                                label.appendChild(span2);

                                var list = document.createElement('li');
                                list.appendChild(label);

                                container_list.appendChild(list);

                                $('#home-left').removeClass('active');
                                $('#home-left').removeClass('show');
                                $('#homes').removeClass('active');
                                $('#profile-selection').addClass('active');
                                $('#profile-selection').addClass('show');
                                $('#profiles').addClass('active');
                                $('#profiles').attr('href','#profile-selection');
                                $('#error_label').text("");
                            }
                        }

                        if (list_containers.length > 1) {
                            document.getElementById('select-all-panel').style.display = 'block';
                            selectAllCheck = document.getElementById('select-all');
                            containerChecks = document.querySelectorAll('#container .custom-control-input');

                            selectAllCheck.addEventListener('change', function (e) {
                                let checkedStatus = e.target.checked;
                                // console.log(checkedStatus);
                                if (checkedStatus) {
                                    containerChecks.forEach(checkbox => {
                                        checkbox.checked = true;
                                    });
                                } else {
                                    containerChecks.forEach(checkbox => {
                                        if (checkbox.checked)
                                            checkbox.checked = false;
                                    });
                                }
                            }, false);

                            containerChecks.forEach(checkbox => {
                                checkbox.addEventListener('change', function (e) {
                                    if (e.target.checked && Invoicing.allBoxesChecked(containerChecks))
                                        selectAllCheck.checked = true;
                                    else
                                        selectAllCheck.checked = false;
                                })
                            });
                        }
                    },
                    error:function () {
                        alert('something went wrong');
                    }
                });
            }
        });


        var charges = 0;


        $('#supp_invoice').on('click',function (e) {
            e.preventDefault();

            var date = new Date();
            var dd = (date.getDate() < 10 ? '0' : '') + date.getDate();
            var MM = ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1);
            var yyyy = date.getFullYear();

            var  currentDate= yyyy + "-" + MM + "-" + dd;
            var note = $('#sup_note').val();
            var sup_invoice_number = $('#sup_invoice_number').val();


            suppContainers = [];
            var container_list = document.getElementById('container');
            var boxes = container_list.getElementsByTagName('input');
            for (input in boxes) {
                if (boxes[input].checked == true) {
                    suppContainers.push(boxes[input].value.split(" ")[0]);
                }
            }



            if (suppContainers.length == 0){
                $('#selection_error').text('Container must be selected');
            }
            else{
                $.ajax({
                    url:"/api/supp_invoice/check_date",
                    type:"POST",
                    data:{
                        cdate: currentDate,
                        sinv:sup_invoice_number,
                        cntrs: JSON.stringify(suppContainers),
                        prof: SupplementaryInvoice.is_proforma ? 1 : 0,
                    },
                    success: function (data) {
                        var result = $.parseJSON(data);
                            var header, body;


                        if (result.st == 1126){
                            header = "UCL Error";
                            body = "No activity added to UCL container";
                            Modaler.dModal(header,body);
                        }

                            if(result.st == 1240) {

                                var flag = result.flag;
                                var result_number = flag.join(",  ");
                                header = 'Error';
                                body = 'Container  <a href="/user/container">' + result_number + '&nbsp' + '</a> ' +
                                    'has been flagged.';
                                Modaler.dModal(header, body);
                                $('#error_label').remove();
                                $('#supp_invoice').attr('disabled', 'true');
                                $('#invoice-link').removeAttr('href');
                                $('#charge-link').removeAttr('href');
                            }
                            if (result.st == 1241){
                                header = "Unpaid Invoices";
                                body = 'An unpaid Invoice <a href="/user/invoice">' + result.unsup[0] + '</a> ' +
                                    'exist for the same selection of containers for' +
                                    ' this BL/BK Number. Please cancel it first.';
                                Modaler.dModal(header, body);
                            }
                            if (result.st == 238) {

                                    charges = parseFloat(result.amt);

                                $('#error_label').remove();
                                     $('#profile-selection').removeClass('active');
                                     $('#profile-selection').removeClass('show');
                                     $('#profiles').removeClass('active');
                                     $('#profiles').removeAttr('href', '#profile-selection');
                                     $('#messages-supp').addClass('active');
                                     $('#messages-supp').addClass('show');
                                     $('#charge-link').addClass('active');
                                     $('#charge-link').attr('href', 'messages-supp');
                                }

                        ActivityCheckCharges.checkCharges(result);

                    },
                    error: function () {
                        alert('something went wrong');
                    }
                });
            }


        });


        $('#sup_storage_button').on('click', function () {
            
            var datePattern = new RegExp("\\d{4}-\\d{2}-\\d{2}");
            var container_list = document.getElementById('container');
            var invoice_numb = document.getElementById('sup_invoice_number').value;

            var note = document.getElementById('sup_note').value;
            var s_pDateValue = document.getElementById('sup_upto_date').value;
            inputs = container_list.getElementsByTagName('input');
            var date = new Date();
            var dd = (date.getDate() < 10 ? '0' : '') + date.getDate();
            var MM = ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1);
            var yyyy = date.getFullYear();
            var  currentDate = yyyy + "-" + MM + "-" + dd;


            var list = [];
            for (input in inputs) {
                if (inputs[input].checked == true) {
                    list.push(inputs[input].value.split(" ")[0]);
                }
            }

            if (!datePattern.test(s_pDateValue)) {
                Modaler.dModal('ERROR', 'Wrong Paid-Up-To Date');
            }
            else {

                if (!(s_pDateValue >= currentDate)) {
                    Modaler.dModal('ERROR', 'Invalid Date');
                }
                else {

                    $.ajax({
                        url:"/api/supp_invoice/get_supp_charges",
                        type:"POST",
                        data:{
                            minc: invoice_numb,
                            pdat: s_pDateValue,
                            prof: SupplementaryInvoice.is_proforma ? 1 : 0,
                            cont:JSON.stringify(list)},
                        success: function (data) {

                            var result = $.parseJSON(data);
                            var header = "";
                            var body = "";
                            charges = parseFloat(result.amt);

                            ActivityCheckCharges.checkCharges(result);

                            if (result.st == 1600){
                                var flag = result.flag;
                                var result_number = flag.join(",  ");
                                header = 'Error';
                                body = 'Container  <a href="/user/container">' + result_number + '&nbsp' + '</a> ' +
                                    'has been flagged.';
                                Modaler.dModal(header, body);

                            }
                            if (result.st == 167){
                                var last_date = result.dd;
                                header = "Storage rent/extra activity not found";
                                body ="The paid up to date must be greater than or equal to "+ last_date;
                                Modaler.dModal(header, body);
                            }
                            if (result.st == 2021){



                                    $.ajax({
                                        url: "/api/supp_invoice/previewSupplementaryInvoice",
                                        type: "POST",
                                        data:{
                                            note: note,
                                            minc: invoice_numb,
                                            pdate: s_pDateValue,
                                            prof: SupplementaryInvoice.is_proforma ? 1 : 0,
                                            cntrs: JSON.stringify(list)
                                        },
                                        success:function(data){
                                            // console.log(data);
                                            let parsedData = $.parseJSON(data);
                                            console.log(parsedData);

                                            // ActivityCheckCharges.checkCharges(result);

                                            $('#preview-left .removable').remove();

                                            document.getElementById('company-name').innerHTML = parsedData.companyName;
                                            document.getElementById('company-address').innerHTML = parsedData.companyLocation;
                
                                            let contactString = parsedData.companyPhone + "  ||  " + parsedData.companyMail + "  ||  " 
                                                    + parsedData.companyWeb;
                                            document.getElementById('company-contacts').innerHTML = contactString;
                
                                            document.getElementById('invoice-date').innerHTML = parsedData.invoiceDate;
                                            document.getElementById('invoice-no').innerHTML = parsedData.invoiceNumber;
                                            document.getElementById('paid-up-to').innerHTML = parsedData.paidUpTo;
                                            document.getElementById('tin').innerHTML = parsedData.tin;
                                            
                                            if (parsedData.tradeType == 1 || parsedData.tradeType == 3) {
                                                document.getElementById('importer-td').innerHTML = parsedData.importerAddress;
                                                document.getElementById('agency-td').innerHTML = parsedData.agency;
                                                document.getElementById('release-instructions-td').innerHTML = parsedData.releaseInstructions;
                                                document.getElementById('customer-td').innerHTML = parsedData.customer;
                                                document.getElementById('main-invoice-td').innerHTML = parsedData.mainInvoice;
                
                                                document.getElementById('vessel-td').innerHTML = parsedData.vessel;
                                                document.getElementById('voyage-no-td').innerHTML = parsedData.voyageNumber;
                                                document.getElementById('arrival-date-td').innerHTML = parsedData.arrivalDate;
                                                document.getElementById('departure-date-td').innerHTML = parsedData.departureDate;
                                                document.getElementById('rotation-number-td').innerHTML = parsedData.rotationNumber;
                
                                                document.getElementById('bl-number-td').innerHTML = parsedData.bNumber;
                                                document.getElementById('boe-number-td').innerHTML = parsedData.boeNumber;
                                                document.getElementById('do-number-td').innerHTML = parsedData.doNumber;
                                                document.getElementById('release-date-td').innerHTML = '';
                                                document.getElementById('containers-td').innerHTML = parsedData.containers;
                                            } else if (parsedData.tradeType == 4) {
                                                document.getElementById('shipper-td').innerHTML = parsedData.shipper;
                                                document.getElementById('ship-line-td').innerHTML = parsedData.shippingLine;
                                                document.getElementById('exp-customer-td').innerHTML = parsedData.customer;
                                                document.getElementById('exp-main-invoice-td').innerHTML = parsedData.mainInvoice;
                
                                                document.getElementById('exp-vessel-td').innerHTML = parsedData.vessel;
                                                document.getElementById('booking-number-td').innerHTML = parsedData.bNumber;
                                                document.getElementById('booking-date-td').innerHTML = parsedData.bookingDate;
                                                document.getElementById('exp-activity-td').innerHTML = parsedData.voyageNumber;
                
                                                document.getElementById('exp-containers-td').innerHTML = parsedData.containers;
                
                                            } else if (parsedData.tradeType == 8) {
                                                document.getElementById('emp-shipper-td').innerHTML = parsedData.shipper;
                                                document.getElementById('emp-ship-line-td').innerHTML = parsedData.shippingLine;
                                                document.getElementById('emp-customer-td').innerHTML = parsedData.customer;
                                                document.getElementById('emp-main-invoice-td').innerHTML = parsedData.mainInvoice;
                
                                                document.getElementById('emp-vessel-td').innerHTML = parsedData.vessel;
                                                document.getElementById('emp-booking-number-td').innerHTML = parsedData.bNumber;
                                                document.getElementById('emp-booking-date-td').innerHTML = parsedData.bookingDate;
                                                document.getElementById('emp-activity-td').innerHTML = parsedData.voyageNumber;
                
                                                document.getElementById('emp-containers-td').innerHTML = parsedData.containers;
                
                                            }
                
                                            let midInfo; // = parsedData.tradeType == 1 ? document.querySelector('.mid-info.import') : document.querySelector('.mid-info.export');
                                            switch (parsedData.tradeType) {
                                                case 1:
                                                case 3:
                                                    midInfo = document.querySelector('.mid-info.import');
                                                    break;
                                                    
                                                case 4:
                                                    midInfo = document.querySelector('.mid-info.export');
                                                    break;
                
                                                case 8:
                                                    midInfo = document.querySelector('.mid-info.empty');
                                                    break;
                
                                                default:
                                                    midInfo = document.querySelector('.mid-info.import');
                                                    break;
                
                                            }
                                                            console.log(document.querySelector('div.business'));
                
                                            $(midInfo).css('position', 'static');
                                            let previewLeft = document.getElementById('preview-left');
                                            console.log(midInfo.cloneNode(true));
                                            previewLeft.insertBefore(midInfo.cloneNode(true), document.querySelector('div.business'));
                                            previewLeft.insertBefore(document.createElement('br'), document.querySelector('div.business'));
                
                                            let fragment = document.createDocumentFragment();
                
                                            parsedData.activities.forEach(activity => {
                                                let row = document.createElement('tr');
                                                $(row).attr('class', 'removable');
                
                                                let descriptionData = document.createElement('td');
                                                descriptionData.innerHTML = activity.description;
                
                                                let quantityData = document.createElement('td');
                                                quantityData.innerHTML = activity.qty;
                                                
                
                                                let costData = document.createElement('td');
                                                costData.setAttribute('class', 'table-money');
                                                costData.innerHTML = activity.cost;
                
                                                let totalCostData = document.createElement('td');
                                                totalCostData.setAttribute('class', 'table-money');
                                                totalCostData.innerHTML = activity.total_cost;
                
                                                row.append(descriptionData, quantityData, costData, totalCostData);
                                                fragment.append(row);
                                            });
                
                                            let secondFragment = document.createDocumentFragment();
                
                                            parsedData.taxDetails.forEach(taxDetail => {
                                                let emptyData = document.createElement('td');
                                                let row = document.createElement('tr');
                                                $(row).attr('class', 'removable');
                
                                                let taxName = document.createElement('th');
                                                taxName.setAttribute('colspan', '2');
                                                taxName.innerHTML = taxDetail.details;
                
                                                let taxAmount = document.createElement('td');
                                                taxAmount.setAttribute('class', 'table-money');
                                                taxAmount.innerHTML = taxDetail.amount;
                
                                                row.append(emptyData, taxName, taxAmount);
                                                secondFragment.append(row);
                
                                            });
                
                
                                            let mainTableBody = document.getElementById('main-table');
                                            console.log(mainTableBody);
                
                                            mainTableBody.insertBefore(fragment, (mainTableBody.rows)[0]);
                
                                            mainTableBody.insertBefore(secondFragment, (mainTableBody.rows)[mainTableBody.rows.length - 2]);
                
                                            document.getElementById('subtotal').innerHTML = parsedData.subtotal;
                
                                            document.getElementById('total-tax').innerHTML = parsedData.totalTax;
                                            document.getElementById('total-amount').innerHTML = parsedData.totalAmount;
                
                                            let thirdFragment = document.createDocumentFragment();
                                            let count = 0;
                
                                            parsedData.containerDetails.forEach(containerDetail => {
                                                let row = document.createElement('tr');
                                                $(row).attr('class', 'removable');
                
                                                let number = document.createElement('td');
                                                number.innerHTML = ++count;
                
                                                let containerNumber = document.createElement('td');
                                                containerNumber.innerHTML = containerDetail.number;
                
                                                let isoType = document.createElement('td');
                                                isoType.innerHTML = containerDetail.code;
                
                                                let containerType = document.createElement('td');
                                                containerType.innerHTML = containerDetail.containerType;
                
                                                let goodDescription = document.createElement('td');
                                                goodDescription.innerHTML = containerDetail.goods;
                
                                                row.append(number, containerNumber, isoType, containerType, goodDescription);
                                                thirdFragment.append(row);
                                            });
                
                                            document.getElementById('container-list').append(thirdFragment);
                
                                            midInfo.style.position = 'absolute';
                                       
                
                                                $('#messages-supp').removeClass('active');
                                                $('#messages-supp').removeClass('show');
                                                $('#charge-link').removeClass('active');
                                                $('#charge-link').removeAttr('href','messages-supp');
                                                $('#preview-left').addClass('active');
                                                $('#preview-left').addClass('show');
                                                $('#preview-link').addClass('active');
                                                $('#preview-link').attr('href', '#invoice-left');
                                                // $('#homes').removeAttr('href', '#home-left');
                                            // }

                                        },
                                        error:function () {
                                            alert('something went wrong');
                                        }
                                    });

                            }
                        },
                        error: function () {
                            $('#manualHeader').text('ERROR');
                            $('#manualAlert').text('Something Went Wrong');
                        }
                    });


                }
            }
        });

        $('#invoice_button').on('click', function () {
            
            var datePattern = new RegExp("\\d{4}-\\d{2}-\\d{2}");
            var container_list = document.getElementById('container');
            var invoice_numb = document.getElementById('sup_invoice_number').value;

            var note = document.getElementById('sup_note').value;
            var s_pDateValue = document.getElementById('sup_upto_date').value;
            inputs = container_list.getElementsByTagName('input');
            var date = new Date();
            var dd = (date.getDate() < 10 ? '0' : '') + date.getDate();
            var MM = ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1);
            var yyyy = date.getFullYear();
            var  currentDate = yyyy + "-" + MM + "-" + dd;

            var list = [];
            for (input in inputs) {
                if (inputs[input].checked == true) {
                    list.push(inputs[input].value.split(" ")[0]);
                }
            }
            

            $.ajax({
                url: "/api/supp_invoice/add_supp_invoice",
                type: "POST",
                data:{
                    note: note,
                    minc: invoice_numb,
                    pdate: s_pDateValue,
                    prof: SupplementaryInvoice.is_proforma ? 1 : 0,
                    cntrs: JSON.stringify(list)
                },
                success:function(data){
                    result = $.parseJSON(data);

                    ActivityCheckCharges.checkCharges(result);

                    if (result.st == 2035){
                        if (SupplementaryInvoice.is_proforma) {
                            if (result.ttyp == 1){
                                $('#invoice_link').html('<a href="/api/proforma_supp_import_invoice/show_import/' + result.sinv + '" target="_blank">View Import Invoice</a>');
                            }
                            if (result.ttyp == 3){
                                $('#invoice_link').html('<a href="/api/proforma_supp_import_invoice/show_import/' + result.sinv + '" target="_blank">View Transit Invoice</a>');
                            }
                            if (result.ttyp == 4){
                                $('#invoice_link').html('<a href="/api/proforma_supp_export_invoice/show_export/' + result.sinv + '" target="_blank">View Export Invoice</a>');
                            }
                        }
                        else {
                            if (result.ttyp == 1) {
                                $('#invoice_link').html('<a href="/api/supp_import_invoice/show_import/' + result.sinv + '" target="_blank">View Import Invoice</a>');
                            }
                            if (result.ttyp == 3) {
                                $('#invoice_link').html('<a href="/api/supp_transit_invoice/show_transit/' + result.sinv + '" target="_blank">View Transit Invoice</a>');
                            }
                            if (result.ttyp == 4) {
                                $('#invoice_link').html('<a href="/api/supp_export_invoice/show_export/' + result.sinv + '" target="_blank">View Export Invoice</a>');
                            }
                            if (result.ttyp == 8) {
                                $('#invoice_link').html('<a href="/api/supp_empty_invoice/show_export/' + result.sinv + '" target="_blank">View Empty Invoice</a>');
                            }
                        }

                        $('#preview-left').removeClass('active');
                        $('#preview-left').removeClass('show');
                        $('#preview-link').removeClass('active');
                        $('#preview-link').removeAttr('href','messages-supp');
                        $('#invoice-supp').addClass('active');
                        $('#invoice-supp').addClass('show');
                        $('#invoice-link').addClass('active');
                        $('#invoice-link').attr('href', '#invoice-left');
                        $('#homes').removeAttr('href', '#home-left');
                    }

                },
                error:function () {
                    alert('something went wrong');
                }
            });

        });
        
    }

}

var ActivityCheckCharges={
    checkCharges:function (result) {
        if (result.st == 1210){
            Modaler.dModal('Missing Depot Activity Charges', `There are no charges available for depot activity '${result.act}'
             with container length ${result.len} feet, load status ${result.stat} and goods type '${result.good}'.`);
            return false;
        }
        if (result.st == 1211){
            Modaler.dModal("No Exchange Rate Found", `No exchange rate is available between the following currency: ${result.base}, ${result.quote}`);
            return false;
        }
        if (result.st == 1212){
            Modaler.dModal("No Charges To Be Invoice", `There are no charges available to be invoice for the containers selected.`);
            return false;
        }
        return true;
    }
}

var Invoice = {
    recallInvoice:function(number){
        var header =  '';
        var body = '';
        $.ajax({
            url:"/api/invoice_status/recall_invoice",
            type:"POST",
            data:{
                invn: number
            },
             success: function (data) {
                var response = $.parseJSON(data);
                if(response.st == 1133){
                    header = "Recalled Error";
                    body = "Invoiced has not be assigned reason";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('invoice');
                }
                else if (response.st == 1134){
                    header = "Recalled Error";
                    body = "Container(s) have been invoiced";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('invoice');
                }
                else if (response.st == 260){
                    header = "Recalled";
                    body = "Invoice have been recalled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('invoice');
                }
             },
             error:function () {
                 alert("something went wrong");
             }
         });
    },
    addNote:function(number){
        note_editor.create({
            title: 'Add Note',
            buttons: 'Add',
        });
        var invoice = document.getElementById("invoice_number1");
        invoice.value = number;
    },
    cancelInvoice: function(number) {
        var invoice = number;

        var url = "/api/invoice_status/cancel";
        var header =  '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 121){
                    header = "CANCELLATION ERROR";
                    body = "Note not added to invoice for cancellation";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('invoice');
                }
                else if (response.st == 120){
                    header = "DEFERAL CANCELLATION ERROR";
                    body = "Deferral cancellation refused. Some containers have been gated out.";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('invoice');
                }
                else if (response.st == 240){
                    header = "DEFERRAL CANCELLED";
                    body = "Invoice Number "+response.numb+" deferral cancelled";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('invoice');
                }
                else if (response.st == 241){
                    header = "CANCELLED";
                    body = "Invoice Number "+response.numb+" cancelled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('invoice');
                }

            }
            else {
                header = document.getElementById('myModalLabel');
                header = "FAILED";

                body = document.getElementById('conditionTable');
                body.innerText = "Cancellation Failed!";
            }
        };
        request.send("data=" + invoice);


    },

    viewNote: function(id,number){
        var header = number;
        var body = "<div class=\"col-md-12\"><table id=\"view_notes\" class=\"display table-responsive\">" +
            "<thead><tr><th>Invoice Number </th><th>Note </th><th>Note Type </th><th>User </th></tr></thead>" +
            "</table></div>";

        CondModal.cModal(header, body);

        InvoiceNote.initTable(id);
    },

    iniTable: function () {
        note_editor = new $.fn.dataTable.Editor({
            fields:[
                {
                    label:"Number",
                    name:"number",
                    attr:{
                        class:"form-control",
                        id:"invoice_number1",
                        disabled: true
                    }
                },
                {
                    label:"Note Type",
                    name:"note_type",
                    type:"select",
                    options: [
                        {label: "CANCELLED", value: 1},
                        {label: "RECALLED", value: 2}
                    ],
                    attr:{
                        class:"form-control",
                        id:"note_type_id"
                    }
                },
                {
                    label:"Note",
                    name:"note",
                    type:"textarea",
                    attr:{
                        class:"form-control",
                        id:"note_id"
                    }
                }
            ]
        });

        note_editor.on("create", function () {
            var invoice_number = this.field('number');
            var note = this.field('note');
            var note_type = this.field('note_type');

           $.ajax({
              url:"/api/invoice/add_note",
              type:"POST",
              data:{
                  invn: invoice_number.val(),
                  note: note.val(),
                  ntype: note_type.val()
              },
               success: function (data) {
                  var response = $.parseJSON(data);
                  if (response.st == 122){
                    //   note.error("empty fields");
                      header = "Add Note Error";
                      body = "Cannot add empty field";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('invoice');
                  }
                  else if (response.st == 123){
                      header = "Add Note Error";
                      body = "Invoice must be unpaid";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('invoice');
                  }
                      else if(response.st == 260){
                        header = "Add Note Success";
                        body = "Note Added successful";
                        Modaler.dModal(header,body);
                        TableRfresh.freshTable('invoice');
                    }
               },
               error:function () {
                   alert("something went wrong");
               }
           });

        });

        


        editor = new $.fn.dataTable.Editor({
            ajax:"/api/invoice/table",
            table: "#invoice",
            fields: [
                {
                    label: "Trade Type:",
                    name: "invoice.trade_type",
                    type: "select",
                    attr: {
                        id: "trade_select",
                        class: "form-control",
                    },
                    options: [
                        { label: "IMPORT", value: 1 },
                        { label: "EXPORT", value: 4 },
                        { label: "TRANSIT", value: 3 },
                    ]
                }, {
                    label: "Number:",
                    name: "invoice.number",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "BL Number:",
                    name: "invoice.bl_number",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "DO Number:",
                    name: "invoice.do_number",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Bill Date:",
                    name: "invoice.bill_date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Due Date:",
                    name: "invoice.due_date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Cost:",
                    name: "invoice.cost",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Tax:",
                    name: "invoice.tax",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Tax Setting:",
                    name: "invoice.tax_setting",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Customer ID:",
                    name: "invoice.customer_id",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "User ID:",
                    name: "invoice.user_id",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Date:",
                    name: "invoice.date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Status:",
                    name: "invoice.status",
                    attr: {
                        class: "form-control"
                    }
                }
            ]
        });

        $('#invoice').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/invoice/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [15] },{ "searchable": false, "targets": 17 } ],
            order: [[ 15, 'desc' ]],
            columns: [
                {data: "trade_type.name", visible:false},
                {data: "invoice.number"},
                {data: "invoice.bl_number"},
                {data: "invoice.book_number", visible:false},
                {data: "invoice.do_number", visible:false},
                {data: "invoice.bill_date", visible:false},
                {data: "invoice.due_date"},
                {data: "invoice.cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "invoice.tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "customer.id"},
                {data: "tax_type.name"},
                {data: "note", visible:false},
                {data: "customer.name"},
                {data: "invoice.approved"},
                {data: "invoice.user_id", visible:false},
                {data: "invoice.date", visible:false},
                {data: "invoice.status", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.trade_type.name == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/import_invoice/show_import/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/transit_invoice/show_transit/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/export_invoice/show_export/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/empty_invoice/show_empty/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.invoice.status == 'UNPAID'  || data.invoice.status == 'DEFERRED'){
                            invoice += "<a href='#' onclick='Invoice.cancelInvoice(\"" + data.invoice.number + "\")' class='depot_cont'>Cancel</a><br/>";
                            invoice += "<a href='#' onclick='Invoice.addNote(\"" + data.invoice.number + "\")' class='depot_cont'>Add Note</a><br/>";
                        }
                        if(data.invn != null){
                            invoice += "<a href='#' onclick='Invoice.viewNote(" + data.invi + ",\"" + data.invoice.number +"\")' class='depot_cont'>View Note</a><br/>";
                        }
                        if(data.invoice.status == "CANCELLED" || data.invoice.status =="RECALLED"){
                            invoice += "<a href='#' onclick='Invoice.addNote(\"" + data.invoice.number + "\")' class='depot_cont'>Add Note</a><br/>";
                        }
                        if(data.invs == "recallable" && data.invoice.status != "RECALLED"){
                            invoice += "<a href='#' onclick='Invoice.recallInvoice(\"" + data.invoice.number + "\")' class='depot_cont'>Recall Invoice</a><br/>";
                        }


                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });

    }
}

var InvoicePayments = {

    viewPayments: function(number, id) {

        var header = document.getElementById('paymentHeader');
        header.innerText = number;

        var body = document.getElementById('paymentTable');
        body.innerHTML = "<table id=\"payment\" class=\"display table table-responsive\">" +
            "<thead><tr><th>Receipt Number </th><th>Paid</th><th>User</th><th>Date</th><th>Action</th></tr></thead>" +
            "</table>";
        Payment.iniTable(id);


    },

    addPayment: function(number) {
        $.ajax({
            url:"/api/depot_overview/get_invoice_cost",
            type:"POST",
            data:{cntr: number},
            success: function (data) {
                var result = $.parseJSON(data);
                $('#invoice_field').val(result.cntr);
                $('#amount_value').val(result.amt);
            },
            error: function () {
                alert('something went wrong');
            }
        });

        paymentEditor.create({
            title: 'Add Payment',
            buttons: 'Add',
        });


    },

    iniTable:function () {

        paymentEditor = new $.fn.dataTable.Editor( {
            ajax: "/api/payment/table",
            fields: [ {
                label: "Receipt Number:",
                name: "payment.receipt_number",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Invoice:",
                name: "payment.invoice_id",
                attr: {
                    id: "invoice_field",
                    class: "form-control",
                    disabled: true
                }
            }, {
                label: "Amount:",
                name: "payment.paid",
                attr: {
                    class: "form-control",
                    id: 'amount_value'
                }
            }, {
                label:"Payment Mode",
                name:"payment.mode",
                type:"select",
                options:[
                    {label:"Cash", value:1},
                    {label:"Cheque", value:2}
                ],
                attr:{
                    class:"form-control",
                    id:"paymenID"
                }
            },{
                label:"Bank",
                name:"payment.bank_name",
                attr:{
                    list:"banks",
                    class:"form-control",
                }
            },{
                label:"Cheque Number",
                name:"payment.bank_cheque_number",
                attr:{
                    class:"form-control",
                    id:"cheque_id"
                }
            },{
                label: "Outstanding:",
                name: "payment.outstanding",
                attr: {
                    class: "form-control"
                }
            },{
                label: "User",
                name:"payment.user_id",
                attr:{
                    class:"form-control"
                }
            },
                {
                    label: "Date:",
                    name: "payment.date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        paymentEditor.field('payment.bank_name').hide();
        paymentEditor.field('payment.bank_cheque_number').hide();

        paymentEditor.dependent('payment.mode', function (val) {
            if (val == 1) {
                paymentEditor.field('payment.bank_name').val('');
                paymentEditor.field('payment.bank_cheque_number').val('');
            }
            return val == 2 ?
                {   show:['payment.bank_name',"payment.bank_cheque_number"]} :
                {   hide: ['payment.bank_name',"payment.bank_cheque_number"]};
        });

        paymentEditor.field('payment.receipt_number').hide();
        paymentEditor.field('payment.outstanding').hide();
        paymentEditor.field('payment.user_id').hide();

        paymentEditor.on('submitSuccess', function () {
            TableRfresh.freshTable('invoice_payment');
        });

        $('#invoice_payment').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/invoice/payment_table",
                type: "POST",
                data: {
                    unap : 0,
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [15] },{ "searchable": false, "targets": 17 } ],
            order: [[ 15, 'desc' ]],
            columns: [
                {data: "trade_type.name", visible:false},
                {data: "invoice.number"},
                {data: "invoice.bl_number"},
                {data: "invoice.book_number", visible:false},
                {data: "invoice.do_number", visible:false},
                {data: "invoice.bill_date", visible:false},
                {data: "invoice.due_date"},
                {data: "invoice.cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "invoice.tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "customer.id"},
                {data: "tax_type.name"},
                {data: "invoice.note", visible:false},
                {data: "customer.name"},
                {data: "invoice.approved"},
                {data: "invoice.user_id", visible:false},
                {data: "invoice.date", visible:false},
                {data: "invoice.status", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.trade_type.name == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/import_invoice/show_import/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/export_invoice/show_export/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/empty_invoice/show_empty/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/transit_invoice/show_transit/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.invoice.approved == "YES") {

                            if (!(data.invoice.status == 'PAID' || data.invoice.status == 'CANCELLED' || data.invoice.status == 'EXPIRED')) {
                                invoice += "<a href='#' onclick='InvoicePayments.addPayment(\"" + data.invoice.number + "\")' >Add Payment</a><br/>";
                            }
                            if (data.invoice.id) {
                                invoice += "<a href='#' data-toggle=\"modal\" data-target=\"#modal-large\" onclick='InvoicePayments.viewPayments(\"" + data.invoice.number + "\", " + data.invoice.id + ")' class='depot_cont'>View Payments</a><br/>";
                            }

                        }

                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });

    }

}

var InvoiceDeferrals = {

    deferInvoice: function(number){

        deferPayment.create( {
            title: "Defer Invoice"+" "+number,
            buttons: {text:"Defer", action:function () {
                    var defer_date = $('#deferID').val();
                    var defer_note = $('#note').val();
                    $.ajax({
                        url:"/api/invoice/defer_invoice",
                        type:"POST",
                        data:{
                            defd: defer_date,
                            invn: number,
                            invt: 1,
                            defn: defer_note
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (result.st == 1117){
                                Modaler.dModal("UNAPPROVED", "Invoice is not approved");
                            }

                            if (result.st == 1125){
                                Modaler.dModal("INVALID DATE", "Deferral date must not be earlier than the current date.");
                            }

                            if (result.st == 2114){
                                Modaler.dModal("DEFERRAL SUCCESSFUL", "Invoice has been deferred to " + defer_date);
                            }
                            TableRfresh.freshTable('invoice_deferrals');
                        },
                        error:function () {
                            alert('something went wrong');
                        }
                    });
                    this.submit();
                }},
            focus: null
        } );
    },

    iniTable:function () {

        deferPayment = new $.fn.dataTable.Editor( {
            fields: [
                {
                    label: "Defer Invoice To::",
                    name: "deferral_date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD",
                    attr: {
                        id: "deferID",
                        class: "form-control"
                    }
                },
                {
                    label:'Note',
                    name:'deferral_note',
                    type:'textarea',
                    attr:{
                        class:"form-control",
                        id: "note"
                    }
                }]
        });

        $('#invoice_deferrals').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/invoice/table",
                type: "POST",
                data: {
                    unap : 0
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [15] },{ "searchable": false, "targets": 17 } ],
            order: [[ 15, 'desc' ]],
            columns: [
                {data: "trade_type.name", visible:false},
                {data: "invoice.number"},
                {data: "invoice.bl_number"},
                {data: "invoice.book_number", visible:false},
                {data: "invoice.do_number", visible:false},
                {data: "invoice.bill_date", visible:false},
                {data: "invoice.due_date"},
                {data: "invoice.cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "invoice.tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "customer.id",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax_type.name"},
                {data: "note", visible:false},
                {data: "customer.name"},
                {data: "invoice.approved"},
                {data: "invoice.user_id", visible:false},
                {data: "invoice.date", visible:false},
                {data: "invoice.status", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.trade_type.name == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/import_invoice/show_import/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/export_invoice/show_export/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/empty_invoice/show_empty/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/transit_invoice/show_transit/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        invoice += "<a href='#' onclick='InvoiceDeferrals.deferInvoice(\"" + data.invoice.number + "\")'>Defer</a>";



                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });

    }

}

var SupplementaryInvoiceDeferrals = {

    deferInvoice: function(number){

        deferPayment.create( {
            title: "Defer Invoice"+" "+number,
            buttons: {text:"Defer", action:function () {
                    var defer_date = $('#deferID').val();
                    var defer_note = $('#note').val();
                    $.ajax({
                        url:"/api/invoice/defer_invoice",
                        type:"POST",
                        data:{
                            defd: defer_date,
                            invn: number,
                            invt: 2,
                            defn: defer_note
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (result.st == 1117){
                                Modaler.dModal("UNAPPROVED", "Invoice is not approved");
                            }

                            if (result.st == 1125){
                                Modaler.dModal("INVALID DATE", "Deferral date must not be earlier than the current date.");
                            }

                            if (result.st == 2114){
                                Modaler.dModal("DEFERRAL SUCCESSFUL", "Invoice has been deferred to " + defer_date);
                            }
                            TableRfresh.freshTable('invoice');
                        },
                        error:function () {
                            alert('something went wrong');
                        }
                    });
                    this.submit();
                }},
            focus: null
        } );
    },

    iniTable:function () {

        deferPayment = new $.fn.dataTable.Editor( {
            fields: [
                {
                    label: "Defer Invoice To::",
                    name: "deferral_date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD",
                    attr: {
                        id: "deferID",
                        class: "form-control"
                    }
                },
                {
                    label:'Note',
                    name:'deferral_note',
                    type:'textarea',
                    attr:{
                        class:"form-control",
                        id: "note"
                    }
                }]
        });

        $('#supp_invoice_deferrals').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/supp_invoice/table",
                type: "POST",
                data: {
                    unap : 0
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [13] }, { "searchable": false, "targets": 16 }],
            order: [[ 14, 'desc' ]],
            columns: [
                {data: "ttyp", visible:false},
                {data: "spnum"},
                {data: "blnum"},
                {data: "dnum", visible:false},
                {data: "bdate", visible:false},
                {data: "ddate"},
                {data: "cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "cust",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "txnam"},
                {data: "note", visible:false},
                {data: "name"},
                {data: "appr"},
                {data: "uid", visible:false},
                {data: "date", visible:false},
                {data: "stat", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.ttyp == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/supp_import_invoice/show_import/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/supp_export_invoice/show_export/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/supp_empty_invoice/show_empty/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/supp_transit_invoice/show_transit/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        invoice += "<a href='#' onclick='SupplementaryInvoiceDeferrals.deferInvoice(\"" + data.spnum + "\")'>Defer</a>";



                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });

    }

}

var SupplementaryInvoicePayment = {

    addSupPayment: function(number) {
        $.ajax({
            url:"/api/depot_overview/get_sup_invoice_cost",
            type:"POST",
            data:{cntr: number},
            success: function (data) {
                var result = $.parseJSON(data);
                $('#invoice_field').val(result.cntr);
                $('#amount_value').val(result.amt);
            },
            error: function () {
                alert('something went wrong');
            }
        });

        paymentEditor.create({
            title: 'Add Supplementary Payment',
            buttons: 'Add',
        });
    },

    viewPayments: function(number, id) {
        var header = document.getElementById('sup_paymentHeader');
        header.innerText = number;

        var body = document.getElementById('sup_paymentTable');
        body.innerHTML = "<table id=\"sup_payment\" class=\"display table table-responsive\">" +
            "<thead><tr><th>Receipt Number </th><th>Paid</th><th>User</th><th>Date</th><th>Action</th></tr></thead>" +
            "</table>";

        SupPayment.iniTable(id);
    },

    iniTable:function () {

        paymentEditor = new $.fn.dataTable.Editor( {
            ajax: "/api/supp_payment/table",
            fields: [ {
                label: "Receipt Number:",
                name: "supplementary_payment.receipt_number",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Invoice:",
                name: "supplementary_payment.invoice_id",
                attr: {
                    id: "invoice_field",
                    class: "form-control",
                    disabled: true
                }
            }, {
                label: "Amount:",
                name: "supplementary_payment.paid",
                attr: {
                    class: "form-control",
                    id:"amount_value"
                }
            }, {
                label:"Payment Mode",
                name:"supplementary_payment.mode",
                type:"select",
                options:[
                    {label:"Cash", value:1},
                    {label:"Cheque", value:2}
                ],
                attr:{
                    class:"form-control"
                }
            },{
                label: "Banks:",
                name: "supplementary_payment.bank_name",
                attr: {
                    class: "form-control",
                    list:"sup_banks"
                }
            },{
                label: "Cheque:",
                name: "supplementary_payment.bank_cheque_number",
                attr: {
                    class: "form-control"
                }
            },{
                label: "Outstanding:",
                name: "supplementary_payment.outstanding",
                attr: {
                    class: "form-control"
                }
            },{
                label: "User",
                name:"supplementary_payment.user_id",
                attr:{
                    class:"form-control"
                }
            },
                {
                    label: "Date:",
                    name: "supplementary_payment.date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        paymentEditor.field('supplementary_payment.bank_name').hide();
        paymentEditor.field('supplementary_payment.bank_cheque_number').hide();

        paymentEditor.dependent('supplementary_payment.mode', function (val) {
            if (val == 1) {
                paymentEditor.field('supplementary_payment.bank_name').val('');
                paymentEditor.field('supplementary_payment.bank_cheque_number').val('');
            }
            return val == 2 ?
                {   show:['supplementary_payment.bank_name',"supplementary_payment.bank_cheque_number"]} :
                {   hide: ['supplementary_payment.bank_name',"supplementary_payment.bank_cheque_number"]};
        });

        paymentEditor.field('supplementary_payment.receipt_number').hide();
        paymentEditor.field('supplementary_payment.outstanding').hide();
        paymentEditor.field('supplementary_payment.user_id').hide();

        paymentEditor.on('submitSuccess', function () {
            TableRfresh.freshTable('supp_invoice_payment');
        });

        $('#supp_invoice_payment').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/supp_invoice/table",
                type: "POST",
                data: {
                    unap : 0,
                    payo: 1
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [13] }, { "searchable": false, "targets": 16 }],
            order: [[ 14, 'desc' ]],
            columns: [
                {data: "ttyp", visible:false},
                {data: "spnum"},
                {data: "blnum"},
                {data: "dnum", visible:false},
                {data: "bdate", visible:false},
                {data: "ddate"},
                {data: "cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "cust",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "txnam"},
                {data: "note", visible:false},
                {data: "name"},
                {data: "appr"},
                {data: "uid", visible:false},
                {data: "date", visible:false},
                {data: "stat", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.ttyp == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/supp_import_invoice/show_import/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/supp_export_invoice/show_export/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/supp_export_invoice/show_export/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/supp_transit_invoice/show_transit/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (!(data.stat == 'PAID' || data.stat == 'CANCELLED' || data.stat == 'EXPIRED')) {
                            invoice += "<a href='#' onclick='SupplementaryInvoicePayment.addSupPayment(\"" + data.spnum + "\")' >Add Payment</a><br/>";
                        }

                        if (data.supid) {
                            invoice += "<a href='#' data-toggle=\"modal\" data-target=\"#modal-large\" onclick='SupplementaryInvoicePayment.viewPayments(\"" + data.spnum + "\", " + data.supid + ")' class='depot_cont'>View Payments</a><br/>";
                        }

                        return invoice;
                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });

    }

}

var InvoiceApproval = {
    addNote:function(number){
        note_editor.create({
            title: 'Add Note',
            buttons: 'Add',
        });
        var invoice = document.getElementById("invoice_number1");
        invoice.value = number;
    },
    viewNote: function(id,number){
        var header = number;
        var body = "<div class=\"col-md-12\"><table id=\"view_notes\" class=\"display table-responsive\">" +
            "<thead><tr><th>Invoice Number </th><th>Note </th><th>Note Type </th><th>User </th></tr></thead>" +
            "</table></div>";

        CondModal.cModal(header, body);

        InvoiceNote.initTable(id);
    },
    cancelInvoice: function(number) {
        var invoice = number;

        var url = "/api/invoice_status/cancel";
        var header =  '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 121){
                    header = "CANCELLATION ERROR";
                    body = "Note not added to invoice for cancellation";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('invoice_approvals');
                }
                else if (response.st == 120){
                    header = "DEFERAL CANCELLATION ERROR";
                    body = "Deferral cancellation refused. Some containers have been gated out.";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('invoice_approvals');
                }
                else if (response.st == 240){
                    header = "DEFERRAL CANCELLED";
                    body = "Invoice Number "+response.numb+" deferral cancelled";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('invoice_approvals');
                }
                else if (response.st == 241){
                    header = "CANCELLED";
                    body = "Invoice Number "+response.numb+" cancelled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('invoice_approvals');
                }

            }
            else {
                header = document.getElementById('myModalLabel');
                header = "FAILED";

                body = document.getElementById('conditionTable');
                body.innerText = "Cancellation Failed!";
            }
        };
        request.send("data=" + invoice);
    },

    approveInvoice: function(number) {
        var invoice = number;

        var url = "/api/InvoiceStatus/approve";
        var header =  '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 242) {
                    header = "INVOICE APPROVED";
                    body = `Invoice ${response.number} has been approved`;
                    Modaler.dModal(header, body);
                    TableRfresh.freshTable('invoice_approvals');

                    return;
                }
            }

            header = document.getElementById('myModalLabel');
            header = "FAILED";

            body = document.getElementById('conditionTable');
            body.innerText = "Approval Failed!";
        };
        request.send("data=" + invoice);
    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/invoice/table",
            table: "#invoice_approvals",
            fields: [ ]
        });

        note_editor = new $.fn.dataTable.Editor({
            fields:[
                {
                    label:"Number",
                    name:"invoice.number",
                    attr:{
                        class:"form-control",
                        id:"invoice_number1",
                        disabled: true
                    }
                },
                {
                    label:"Note Type",
                    name:"note_type",
                    type:"select",
                    options: [
                        {label: "CANCELLED", value: 1},
                        {label: "RECALLED", value: 2}
                    ],
                    attr:{
                        class:"form-control",
                        id:"note_type_id"
                    }
                },
                {
                    label:"Note",
                    name:"note",
                    type:"textarea",
                    attr:{
                        class:"form-control",
                        id:"note_id"
                    }
                }
            ]
        });

        note_editor.on("create", function () {
            var invoice_number = $("#invoice_number1").val();
            var note = $("#note_id").val();
            var note_type = $("#note_type_id").val();
 
            $.ajax({
               url:"/api/invoice/add_note",
               type:"POST",
               data:{
                   invn: invoice_number,
                   note: note,
                   ntype: note_type
               },
                success: function (data) {
                   var response = $.parseJSON(data);
                   if (response.st == 122){
                       header = "Add Note Error";
                       body = "Cannot add empty field";
                       Modaler.dModal(header,body);
                       TableRfresh.freshTable('invoice_approvals');
                   }
                   else if (response.st == 123){
                       header = "Add Note Error";
                       body = "Invoice must be unpaid";
                       Modaler.dModal(header,body);
                       TableRfresh.freshTable('invoice_approvals');
                   }
                       else if(response.st == 260){
                         header = "Add Note Success";
                         body = "Note Added successful";
                         Modaler.dModal(header,body);
                         TableRfresh.freshTable('invoice_approvals');
                     }
                },
                error:function () {
                    alert("something went wrong");
                }
            });
 
         });


        $('#invoice_approvals').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/invoice/table",
                type: "POST",
                data: {
                    unap : 1
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [14] },{ "searchable": false, "targets": 16 } ],
            order: [[ 14, 'desc' ]],
            columns: [
                {data: "trade_type.name", visible:false},
                {data: "invoice.number"},
                {data: "invoice.bl_number"},
                {data: "invoice.book_number", visible:false},
                {data: "invoice.do_number", visible:false},
                {data: "invoice.bill_date", visible:false},
                {data: "invoice.due_date"},
                {data: "invoice.cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "invoice.tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "customer.id"},
                {data: "tax_type.name"},
                {data: "note", visible:false},
                {data: "customer.name"},
                {data: "invoice.user_id", visible:false},
                {data: "invoice.date", visible:false},
                {data: "invoice.status", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.trade_type.name == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/import_invoice/show_import/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/export_invoice/show_export/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/empty_invoice/show_empty/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/transit_invoice/show_transit/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }
                        

                        if (data.invoice.status == 'UNPAID' || data.invoice.status == 'DEFERRED' || data.invoice.status == 'RECALLED') {

                            invoice += "<a href='#' onclick='InvoiceApproval.approveInvoice(\"" + data.invoice.number + "\")' class='depot_cont'>Approve</a><br/>";
                            invoice += "<a href='#' onclick='InvoiceApproval.cancelInvoice(\"" + data.invoice.number + "\")' class='depot_cont'>Cancel</a><br/>";
                            invoice += "<a href='#' onclick='Invoice.addNote(\"" + data.invoice.number + "\")' class='depot_cont'>Add Note</a><br/>";
                        }
                        if(data.invn != null){
                            invoice += "<a href='#' onclick='InvoiceApproval.viewNote(" + data.invi + ",\"" + data.invoice.number +"\")' class='depot_cont'>View Note</a><br/>";
                        }

                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });

        ino_helper();

        function fetch_invoice_range(start_date='',end_date='',status=''){
            $('#invoice').DataTable({
                dom: "Bfrtip",
                ajax: {
                    url: "/api/InvoiceSearchTable",
                    type: "POST",
                    data:{
                        start_date: start_date,
                        end_date: end_date,
                        status: status
                    }
                },
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                serverSide: true,
                columnDefs: [ { type: 'date', 'targets': [13] },{ "searchable": false, "targets": 15 } ],
                order: [[ 13, 'desc' ]],
                columns: [
                    {data: "trade_type.name", visible:false},
                    {data: "invoice.number"},
                    {data: "invoice.bl_number"},
                    {data: "invoice.do_number", visible:false},
                    {data: "invoice.bill_date"},
                    {data: "invoice.due_date"},
                    {data: "invoice.cost",
                    render: $.fn.dataTable.render.number( ',', '.', 2 )},
                    {data: "invoice.tax",
                    render: $.fn.dataTable.render.number( ',', '.', 2 )},
                    {data: "customer.id"},
                    {data: "tax_type.name"},
                    {data: "invoice.note", visible:false},
                    {data: "customer.name"},
                    {data: "invoice.user_id", visible:false},
                    {data: "invoice.date", visible:false},
                    {data: "invoice.status", visible:false},
                    {data: "invoice.approved", visible:false},
                    {data: null,
                        render: function (data, type, row) {

                            var invoice = "";

                            if (data.trade_type.name == 'IMPORT') {
                                invoice += '<a class="view_act" href="/api/ImportIvoice/showImport/' + data.invoice.number + '" target="_blank">View</a><br>';
                            }

                            if (data.trade_type.name == 'EXPORT') {
                                invoice += '<a class="view_act" href="/api/ExportInvoice/showExport/' + data.invoice.number + '" target="_blank">View</a><br>';
                            }

                            invoice += "<a href='#' onclick='InvoiceApproval.approveInvoice(\"" + data.invoice.number + "\")' class='depot_cont'>Approve</a><br/>";

                            i
                            if (data.invoice.status == 'UNPAID'){
                                invoice += "<a href='#' onclick='InvoiceApproval.approveInvoice(\"" + data.invoice.number + "\")' class='depot_cont'>Approve</a><br/>";
                                invoice += "<a href='#' data-toggle=\"modal\" data-target=\"#modal-small\" onclick='InvoiceApproval.cancelInvoice(\"" + data.invoice.number + "\")' class='depot_cont'>Cancel</a><br/>";
                            }

                            return invoice;

                        }
                    }
                ],
                select: true,
                buttons: [
                    { extend: 'pageLength', className:"btn btn-primary"},
                    { extend: "colvis", className:"btn btn-primary"}
                ]
            });
        }


        function ino_helper(){

            $('#start_date, #end_date, #status').on('change', function () {

                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var status = $('#status').val();


                if (start_date != '' && end_date !='' && status != '' ){
                    $('#invoice').DataTable().destroy();
                    fetch_invoice_range(start_date,end_date,status);
                }
                if (status == '' && start_date != '' && end_date != ''){
                    $('#invoice').DataTable().destroy();
                    fetch_invoice_range(start_date,end_date,status);
                }
                if (status != '' && start_date == '' && end_date == ''){
                    $('#invoice').DataTable().destroy();
                    fetch_invoice_range(start_date,end_date,status);
                }

            });
        }

    }
}

var InvoiceWaiver = {
    addWaiver: function(number, currency, cost){
        var waiver = new $.fn.dataTable.Editor( {
            fields: [
                {
                    label:'Cost',
                    name:'cost',
                    def: currency + " " + cost,
                    attr:{
                        class:"form-control",
                        disabled: true
                    }
                },
                {
                    label: "Waiver Type",
                    name: "waiver_type",
                    type:  "radio",
                    options: [
                        { label: "Amount", value: 0 },
                        { label: "Percentage",  value: 1 }
                    ],
                    attr: {
                        id: "waiver_type",
                        class: "form-control"
                    }
                },
                {
                    label:'Waiver Value',
                    name:'waiver',
                    attr:{
                        class:"form-control",
                        id: "waiver",
                        type: "number",
                        min: "0",
                        value: 0
                    }
                },
                {
                    label:'Note',
                    name:'waiver_note',
                    type:'textarea',
                    attr:{
                        class:"form-control",
                        id: "waiver_note"
                    }
                }]
        });
        waiver.create( {
            title: "Add Waiver For Invoice "+number,
            buttons: {text:"Add", action:function () {
                    var waiver = $('#waiver').val();
                    var waiver_type =  $("input[type=radio][name='waiver_type']:checked").val();//$('#waiver_type').val();
                    var waiver_note = $('#waiver_note').val();
                    
                    if (waiver.trim() === "" || waiver < 0 || (waiver_type == 1 && waiver > 100))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Invalid Waiver Amount");

                        return;
                    }
                    if ((waiver_type == 1 && waiver > 100) || (waiver_type == 0 && waiver > cost))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Waiver exceeds Cost of Invoice");

                        return;
                    }
                    $.ajax({
                        url:"/api/invoice/add_waiver",
                        type:"POST",
                        data:{
                            wvrt: waiver_type,
                            invn: number,
                            invt: 1,
                            wvr: waiver,
                            wvrn: waiver_note
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (result.st == 1117){
                                Modaler.dModal("UNAPPROVED", "Invoice is not approved.");
                            }
                            if (result.st == 1122){
                                Modaler.dModal("INVALID WAIVER", "Waiver amount or percentage is less than 0.");
                            }
                            if (result.st == 1120){
                                Modaler.dModal("UNPAYABLE INVOICE", "Invoice is either paid, cancelled or expired.");
                            }
                            if (result.st == 1121){
                                Modaler.dModal("WAIVER ALREADY APPLIED", "Invoice already has a waiver.");
                            }
                            if (result.st == 1124){
                                Modaler.dModal("WAIVER EXCEEDS COST", "Resulting waiver exceeds the cost of the invoice.");
                            }
                            if (result.st == 1123){
                                Modaler.dModal("UNKNOW WAIVER TYPE", "Invoice is not approved");
                            }
                            if (result.st == 2113){
                                Modaler.dModal("WAIVER APPLIED", "Waiver has been successfully applied.");
                            }
                            TableRfresh.freshTable('invoice_waiver');
                        },
                        error:function () {
                            alert('something went wrong');
                        }
                    });
                    this.submit();
                }},
            focus: null
        } );
    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/invoice/table",
            table: "#invoice_approvals",
            fields: [ ]
        });


        $('#invoice_waiver').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/invoice/table",
                type: "POST",
                data: {
                    unap : 0
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [14] },{ "searchable": false, "targets": 16 } ],
            order: [[ 14, 'desc' ]],
            columns: [
                {data: "trade_type.name", visible:false},
                {data: "invoice.number"},
                {data: "invoice.bl_number"},
                {data: "invoice.book_number", visible:false},
                {data: "invoice.do_number", visible:false},
                {data: "invoice.bill_date", visible:false},
                {data: "invoice.due_date"},
                {data: "invoice.cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "invoice.tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "customer.id"},
                {data: "tax_type.name"},
                {data: "note", visible:false},
                {data: "customer.name"},
                {data: "invoice.user_id", visible:false},
                {data: "invoice.date", visible:false},
                {data: "invoice.status", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.trade_type.name == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/import_invoice/show_import/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/export_invoice/show_export/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/empty_invoice/show_empty/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/transit_invoice/show_transit/' + data.invoice.number + '" target="_blank">View</a><br>';
                        }

                        invoice += "<a href='#' onclick='InvoiceWaiver.addWaiver(\"" + data.invoice.number + "\", \""+ data.currency.code +"\", "+ data.invoice.cost + ")' class='depot_cont'>Add Waiver</a><br/>";

                        return invoice;
                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });
    }
}

var SupplementaryInvoiceWaiver = {

    addWaiver: function(number, currency, cost){
        var waiver = new $.fn.dataTable.Editor( {
            fields: [
                {
                    label:'Cost',
                    name:'cost',
                    def: currency + " " + cost,
                    attr:{
                        class:"form-control",
                        disabled: true
                    }
                },
                {
                    label: "Waiver Type",
                    name: "waiver_type",
                    type:  "radio",
                    options: [
                        { label: "Amount", value: 0 },
                        { label: "Percentage",  value: 1 }
                    ],
                    attr: {
                        id: "waiver_type",
                        class: "form-control"
                    }
                },
                {
                    label:'Waiver Value',
                    name:'waiver',
                    attr:{
                        class:"form-control",
                        id: "waiver",
                        type: "number",
                        min: "0",
                        value: 0
                    }
                },
                {
                    label:'Note',
                    name:'waiver_note',
                    type:'textarea',
                    attr:{
                        class:"form-control",
                        id: "waiver_note"
                    }
                }]
        });
        waiver.create( {
            title: "Add Waiver For Invoice "+number,
            buttons: {text:"Add", action:function () {
                    var waiver = $('#waiver').val();
                    var waiver_type =  $("input[type=radio][name='waiver_type']:checked").val();//$('#waiver_type').val();
                    var waiver_note = $('#waiver_note').val();

                    if (waiver.trim() === "" || waiver < 0 || (waiver_type == 1 && waiver > 100))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Invalid Waiver Amount");

                        return;
                    }
                    if ((waiver_type == 1 && waiver > 100) || (waiver_type == 0 && waiver > cost))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Waiver exceeds Cost of Invoice");

                        return;
                    }
                    $.ajax({
                        url:"/api/invoice/add_waiver",
                        type:"POST",
                        data:{
                            wvrt: waiver_type,
                            invn: number,
                            invt: 2,
                            wvr: waiver,
                            wvrn: waiver_note
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (result.st == 1117){
                                Modaler.dModal("UNAPPROVED", "Invoice is not approved.");
                            }
                            if (result.st == 1122){
                                Modaler.dModal("INVALID WAIVER", "Waiver amount or percentage is less than 0.");
                            }
                            if (result.st == 1120){
                                Modaler.dModal("UNPAYABLE INVOICE", "Invoice is either paid, cancelled or expired.");
                            }
                            if (result.st == 1121){
                                Modaler.dModal("WAIVER ALREADY APPLIED", "Invoice already has a waiver.");
                            }
                            if (result.st == 1124){
                                Modaler.dModal("WAIVER EXCEEDS COST", "Resulting waiver exceeds the cost of the invoice.");
                            }
                            if (result.st == 1123){
                                Modaler.dModal("UNKNOW WAIVER TYPE", "Invoice is not approved");
                            }
                            if (result.st == 2113){
                                Modaler.dModal("WAIVER APPLIED", "Waiver has been successfully applied.");
                            }
                            TableRfresh.freshTable('invoice_waiver');
                        },
                        error:function () {
                            alert('something went wrong');
                        }
                    });
                    this.submit();
                }},
            focus: null
        } );
    },

    iniTable:function () {

        editor = new $.fn.dataTable.Editor({
            ajax:"/api/supp_invoice/table",
            table: "#supplementary_invoice_waiver",
            fields: [ ]
        });

        $('#supplementary_invoice_waiver').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/supp_invoice/table",
                type: "POST",
                data: {
                    unap : 0
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [13] }, { "searchable": false, "targets": 16 }],
            order: [[ 14, 'desc' ]],
            columns: [
                {data: "ttyp", visible:false},
                {data: "spnum"},
                {data: "blnum"},
                {data: "dnum", visible:false},
                {data: "bdate", visible:false},
                {data: "ddate"},
                {data: "cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "cust",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "txnam"},
                {data: "note", visible:false},
                {data: "name"},
                {data: "appr"},
                {data: "uid", visible:false},
                {data: "date", visible:false},
                {data: "stat", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.ttyp == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/supp_import_invoice/show_import/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/supp_export_invoice/show_export/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/supp_empty_invoice/show_empty/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/supp_transit_invoice/show_transit/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        invoice += "<a href='#' onclick='SupplementaryInvoiceWaiver.addWaiver(\"" + data.spnum + "\", \""+ data.code +"\", "+ data.cost + ")' class='depot_cont'>Add Waiver</a><br/>";

                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"}
            ]
        });

    }

}

var SupplementaryInvoiceApproval = {
    addNote:function(number){
        note_editor.create({
            title: 'Add Note',
            buttons: 'Add',
        });
        var invoice = document.getElementById("invoice_number1");
        invoice.value = number;
    },
    viewNote: function(id,number){
        var header = number;
        var body = "<div class=\"col-md-12\"><table id=\"view_notes\" class=\"display table-responsive\">" +
            "<thead><tr><th>Invoice Number </th><th>Note </th><th>Note Type </th><th>User </th></tr></thead>" +
            "</table></div>";

        CondModal.cModal(header, body);

        SuppsInvoiceNote.initTable(id);
    },
    cancelInvoice: function(number) {
        var invoice = number;

        var url = "/api/supp_invoice_status/cancel";
        var header =  '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);
                if (response.st == 123){
                    header = "CANCELLATION ERROR";
                    body = "Note not added to invoice for cancellation";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('supp_invoice_approvals');
                }
                else if (response.st == 121){
                    header = "Deferred Error";
                    body = "Deferral refused, some containers have gated out.";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('supp_invoice_approvals');
                }
                else if (response.st == 250){
                    header = "DEFERRAL CANCELLED";
                    body = "Deferred Supplementary Invoice Number "+response.numb+"cancelled";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('supp_invoice_approvals');
                }
                else if (response.st == 251){
                    header = "CANCELLED";
                    body = "Supplementary Invoice Number"+response.numb+" cancelled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('supp_invoice_approvals');
                }
            }
            else {
                header = 'FAILED';
                body = 'Cancellation failed';
                Modaler.dModal(header,body);
            }
        };
        request.send("data=" + invoice);


    },

    approveInvoice: function(number) {
        var invoice = number;

        var url = "/api/supp_invoice_status/approve";
        var header =  '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function()  {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);

                if (response.st == 2122) {
                    header = "Approved";
                    body = "Invoice Number " +  invoice  + " approved";
                    Modaler.dModal(header,body);
                }
                else {
                    header = document.getElementById('myModalLabel');
                    header = "FAILED";

                    body = document.getElementById('conditionTable');
                    body.innerText = "Approval Failed!";
                }

                TableRfresh.freshTable('supp_invoice_approvals');
            }
            else {
                header = document.getElementById('myModalLabel');
                header = "FAILED";

                body = document.getElementById('conditionTable');
                body.innerText = "Approval Failed!";
            }
        };
        request.send("data=" + invoice);
    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/supp_invoice/table",
            table: "#supp_invoice",
            fields: []
        });

        note_editor = new $.fn.dataTable.Editor({
            fields:[
                {
                    label:"Number",
                    name:"supplementary_invoice.number",
                    attr:{
                        class:"form-control",
                        id:"invoice_number1",
                        disabled: true
                    }
                },
                {
                    label:"Note Type",
                    name:"note_type",
                    type:"select",
                    options: [
                        {label: "CANCELLED", value: 1},
                        {label: "RECALLED", value: 2}
                    ],
                    attr:{
                        class:"form-control",
                        id:"note_type_id"
                    }
                },
                {
                    label:"Note",
                    name:"note",
                    type:"textarea",
                    attr:{
                        class:"form-control",
                        id:"note_id"
                    }
                }
            ]
        });

        note_editor.on("create", function () {
           var invoice_number = $("#invoice_number1").val();
           var note = $("#note_id").val();
           var note_type = $("#note_type_id").val();

           $.ajax({
              url:"/api/supp_invoice/add_note",
              type:"POST",
              data:{
                  invn: invoice_number,
                  note: note,
                  ntype: note_type
              },
               success: function (data) {
                  var response = $.parseJSON(data);
                  if (response.st == 122){
                      header = "Add Note Error";
                      body = "Cannot add empty field";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('supp_invoice_approvals');
                  }
                  else if (response.st == 123){
                      header = "Add Note Error";
                      body = "Supplementary Invoice must be paid";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('supp_invoice_approvals');
                  }
                  else if(response.st == 260){
                    header = "Add Note Success";
                    body = "Note Added successful";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('supp_invoice_approvals');
                }
               },
               error:function () {
                   alert("something went wrong");
               }
           });

        });

        $('#supp_invoice_approvals').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/supp_invoice/table",
                type: "POST",
                data: {
                    unap : 1
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [13] }, { "searchable": false, "targets": 15 }],
            order: [[ 13, 'desc' ]],
            columns: [
                {data: "ttyp", visible:false},
                {data: "spnum"},
                {data: "blnum"},
                {data: "dnum", visible:false},
                {data: "bdate", visible:false},
                {data: "ddate"},
                {data: "cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "cust",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "txnam"},
                {data: "note", visible:false},
                {data: "name"},
                {data: "uid", visible:false},
                {data: "date", visible:false},
                {data: "stat", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.ttyp == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/supp_import_invoice/show_import/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/supp_export_invoice/show_export/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/supp_empty_invoice/show_empty/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/supp_transit_invoice/show_transit/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.stat == 'UNPAID' || data.stat == 'DEFERRED' || data.stat == 'RECALLED'){
                            invoice += "<a href='#' onclick='SupplementaryInvoiceApproval.approveInvoice(\"" + data.spnum + "\")' class='depot_cont'>Approve</a><br/>";
                            invoice += "<a href='#' onclick='SupplementaryInvoiceApproval.cancelInvoice(\"" + data.spnum + "\")' class='depot_cont'>Cancel</a><br/>";
                            invoice += "<a href='#' onclick='SupplementaryInvoiceApproval.addNote(\"" + data.spnum + "\")' class='depot_cont'>Add Note</a><br/>";
                        }

                        if(data.invn != null){
                            invoice += "<a href='#' onclick='SupplementaryInvoiceApproval.viewNote(" + data.invi + ",\"" + data.spnum +"\")' class='depot_cont'>View Note</a><br/>";
                        }

                        return invoice;
                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"}
            ]
        });

    }
}

var Payment = {

    iniTable: function (id) {

        $('#payment').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/payment/table",
                type: "POST",
                data: {
                    activity: id
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [3] },{ "searchable": false, "targets": 4 } ],
            order: [[ 3, 'desc' ]],
            columns: [
                {data: "payment.receipt_number"},
                {data: "payment.paid",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "user.first_name", visible:false},
                {data: "payment.date"},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";
                        if (data.invoice.trade_type == 1) {
                            invoice += '<a class="view_act" href="/api/ImportReceipt/showReceipt/' + data.payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        if (data.invoice.trade_type == 4) {
                            invoice += '<a class="view_act" href="/api/ExportReceipt/showReceipt/' + data.payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        if (data.invoice.trade_type == 8) {
                            invoice += '<a class="view_act" href="/api/ExportReceipt/showReceipt/' + data.payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        if (data.invoice.trade_type == 3) {
                            invoice += '<a class="view_act" href="/api/TransitReceipt/show_receipt/' + data.payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        return invoice;

                    }
                }
            ],
            buttons: []
        });



    }
}

var SuppInvoice = {
    recallInvoice:function(number){
        var header =  '';
        var body = '';
        $.ajax({
            url:"/api/supp_invoice_status/recall_invoice",
            type:"POST",
            data:{
                invn: number
            },
             success: function (data) {
                var response = $.parseJSON(data);
                if(response.st == 1133){
                    header = "Recalled Error";
                    body = "Invoiced has not be assigned reason";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('supp_invoice');
                }
                else if (response.st == 1134){
                    header = "Recalled Error";
                    body = "Container(s) have been invoiced";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('supp_invoice');
                }
                else if (response.st == 260){
                    header = "Recalled";
                    body = "Invoice have been recalled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('supp_invoice');
                }
             },
             error:function () {
                 alert("something went wrong");
             }
         });
    },
    addNote:function(number){
        note_editor.create({
            title: 'Add Note',
            buttons: 'Add',
        });
        var invoice = document.getElementById("invoice_number1");
        invoice.value = number;
    },

    viewNote: function(id,number){
        var header = number;
        var body = "<div class=\"col-md-12\"><table id=\"view_notes\" class=\"display table-responsive\">" +
            "<thead><tr><th>Invoice Number </th><th>Note </th><th>Note Type </th><th>User </th></tr></thead>" +
            "</table></div>";

        CondModal.cModal(header, body);

        SuppsInvoiceNote.initTable(id);
    },

    cancelInvoice: function(number) {
        var invoice = number;

        var url = "/api/supp_invoice_status/cancel";
        var header =  '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);
                if (response.st == 123){
                    header = "CANCELLATION ERROR";
                    body = "Note not added to invoice for cancellation";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('supp_invoice');
                }
                else if (response.st == 121){
                    header = "Deferred Error";
                    body = "Deferral refused, some containers have gated out.";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('supp_invoice');
                }
                else if (response.st == 250){
                    header = "DEFERRAL CANCELLED";
                    body = "Deferred Supplementary Invoice Number "+response.numb+"cancelled";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('supp_invoice');
                }
                else if (response.st == 251){
                    header = "CANCELLED";
                    body = "Supplementary Invoice Number"+response.numb+" cancelled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('supp_invoice');
                }
            }
            else {
                header = 'FAILED';
                body = 'Cancellation failed';
                Modaler.dModal(header,body);
            }
        };
        request.send("data=" + invoice);


    },

    addSupPayment: function(number) {
        $.ajax({
            url:"/api/depot_overview/get_sup_invoice_cost",
            type:"POST",
            data:{cntr: number},
            success: function (data) {
                var result = $.parseJSON(data);
                $('#invoice_field').val(result.cntr);
                $('#amount_value').val(result.amt);
            },
            error: function () {
                alert('something went wrong');
            }
        });

        paymentEditor.create({
            title: 'Add Supplementary Payment',
            buttons: 'Add',
        });


    },

    deferInvoice: function(number){

        deferPayment.create( {
            title: "Defer Invoice"+" "+number,
            buttons: {text:"Defer", action:function () {
                    var defer_date = $('#deferID').val();
                    var defer_note = $('#note').val();
                    $.ajax({
                        url:"/api/invoice/defer_invoice",
                        type:"POST",
                        data:{
                            defd: defer_date,
                            invn: number,
                            invt: 2,
                            defn: defer_note
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (result.st == 1117){
                                Modaler.dModal("UNAPPROVED", "Supplementary Invoice is not approved");
                            }
                            TableRfresh.freshTable('supp_invoice');
                        },
                        error:function () {
                            alert('something went wrong');
                        }
                    });
                    this.submit();
                }},
            focus: null
        } );
    },

    viewPayments: function(number, id) {

        var header = document.getElementById('sup_paymentHeader');
        header.innerText = number;

        var body = document.getElementById('sup_paymentTable');
        body.innerHTML = "<table id=\"sup_payment\" class=\"display table table-responsive\">" +
            "<thead><tr><th>Receipt Number </th><th>Paid</th><th>User</th><th>Date</th><th>Action</th></tr></thead>" +
            "</table>";


        SupPayment.iniTable(id);


    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/supp_invoice/table",
            table: "#supp_invoice",
            fields: []
        });

        note_editor = new $.fn.dataTable.Editor({
            fields:[
                {
                    label:"Number",
                    name:"supplementary_invoice.number",
                    attr:{
                        class:"form-control",
                        id:"invoice_number1",
                        disabled: true
                    }
                },
                {
                    label:"Note Type",
                    name:"note_type",
                    type:"select",
                    options: [
                        {label: "CANCELLED", value: 1},
                        {label: "RECALLED", value: 2}
                    ],
                    attr:{
                        class:"form-control",
                        id:"note_type_id"
                    }
                },
                {
                    label:"Note",
                    name:"note",
                    type:"textarea",
                    attr:{
                        class:"form-control",
                        id:"note_id"
                    }
                }
            ]
        });

        note_editor.on("create", function () {
           var invoice_number = $("#invoice_number1").val();
           var note = $("#note_id").val();
           var note_type = $("#note_type_id").val();

           $.ajax({
              url:"/api/supp_invoice/add_note",
              type:"POST",
              data:{
                  invn: invoice_number,
                  note: note,
                  ntype: note_type
              },
               success: function (data) {
                  var response = $.parseJSON(data);
                  if (response.st == 122){
                      header = "Add Note Error";
                      body = "Cannot add empty field";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('supp_invoice');
                  }
                  else if (response.st == 123){
                      header = "Add Note Error";
                      body = "Supplementary Invoice must be paid";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('supp_invoice');
                  }
                  else if(response.st == 260){
                    header = "Add Note Success";
                    body = "Note Added successful";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('supp_invoice');
                }
               },
               error:function () {
                   alert("something went wrong");
               }
           });

        });

        deferPayment = new $.fn.dataTable.Editor( {
            fields: [
                {
                    label: "Defer Invoice To:",
                    name: "deferral_date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD",
                    attr: {
                        id: "deferID",
                        class: "form-control"
                    }
                },
                {
                    label:'Note',
                    name:'deferral_note',
                    type:'textarea',
                    attr:{
                        class:"form-control",
                        id: "note"
                    }
                }]
        });

        paymentEditor = new $.fn.dataTable.Editor( {
            ajax: "/api/supp_payment/table",
            fields: [ {
                label: "Receipt Number:",
                name: "supplementary_payment.receipt_number",
                attr: {
                    class: "form-control"
                }
            }, {
                label: "Invoice:",
                name: "supplementary_payment.invoice_id",
                attr: {
                    id: "invoice_field",
                    class: "form-control",
                    disabled: true
                }
            }, {
                label: "Amount:",
                name: "supplementary_payment.paid",
                attr: {
                    class: "form-control",
                    id:"amount_value"
                }
            }, {
                label:"Payment Mode",
                name:"supplementary_payment.mode",
                type:"select",
                options:[
                    {label:"Cash", value:1},
                    {label:"Cheque", value:2}
                ],
                attr:{
                    class:"form-control"
                }
            },{
                label: "Banks:",
                name: "supplementary_payment.bank_name",
                attr: {
                    class: "form-control",
                    list:"sup_banks"
                }
            },{
                label: "Cheque:",
                name: "supplementary_payment.bank_cheque_number",
                attr: {
                    class: "form-control"
                }
            },{
                label: "Outstanding:",
                name: "supplementary_payment.outstanding",
                attr: {
                    class: "form-control"
                }
            },{
                label: "User",
                name:"supplementary_payment.user_id",
                attr:{
                    class:"form-control"
                }
            },
                {
                    label: "Date:",
                    name: "supplementary_payment.date",
                    type: "datetime",
                    def:  function () { return new Date(); },
                    format: "YYYY-MM-DD HH:mm",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        paymentEditor.field('supplementary_payment.bank_name').hide();
        paymentEditor.field('supplementary_payment.bank_cheque_number').hide();

        paymentEditor.dependent('supplementary_payment.mode', function (val) {
            if (val == 1) {
                paymentEditor.field('supplementary_payment.bank_name').val('');
                paymentEditor.field('supplementary_payment.bank_cheque_number').val('');
            }
            return val == 2 ?
                {   show:['supplementary_payment.bank_name',"supplementary_payment.bank_cheque_number"]} :
                {   hide: ['supplementary_payment.bank_name',"supplementary_payment.bank_cheque_number"]};
        });

        paymentEditor.field('supplementary_payment.receipt_number').hide();
        paymentEditor.field('supplementary_payment.outstanding').hide();
        paymentEditor.field('supplementary_payment.user_id').hide();

        paymentEditor.on('submitSuccess', function () {
            TableRfresh.freshTable('supp_invoice');
        });

        deferPayment.on('preSubmit', function (e, o, action) {
            if (action !== 'remove') {
                var date_defer = this.field('deferral_date');
                var date = new Date();
                var dd = (date.getDate() < 10 ? '0' : '') + date.getDate();
                var MM = ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1);
                var yyyy = date.getFullYear();
                var  currentDate = yyyy + "-" + MM + "-" + dd;

                if (currentDate > date_defer.val()) {
                    date_defer.error("Cannot choose past date");
                    return false;
                }
            }


        });

        $('#supp_invoice').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/supp_invoice/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [13] }, { "searchable": false, "targets": 16 }],
            order: [[ 14, 'desc' ]],
            columns: [
                {data: "ttyp", visible:false},
                {data: "spnum"},
                {data: "blnum"},
                {data: "dnum", visible:false},
                {data: "bdate", visible:false},
                {data: "ddate"},
                {data: "cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "cust",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "txnam"},
                {data: "note", visible:false},
                {data: "name"},
                {data: "appr"},
                {data: "uid", visible:false},
                {data: "date", visible:false},
                {data: "stat", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.ttyp == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/supp_import_invoice/show_import/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/supp_transit_invoice/show_transit/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/supp_export_invoice/show_export/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/supp_empty_invoice/show_empty/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.stat == 'UNPAID' || data.stat == 'DEFERRED'){
                            invoice += "<a href='#' onclick='SuppInvoice.cancelInvoice(\"" + data.spnum + "\")' class='depot_cont'>Cancel</a><br/>";
                            invoice += "<a href='#' onclick='SuppInvoice.addNote(\"" + data.spnum + "\")' class='depot_cont'>Add Note</a><br/>";
                        }

                        if(data.invn != null){
                            invoice += "<a href='#' onclick='SuppInvoice.viewNote(" + data.invi + ",\"" + data.spnum +"\")' class='depot_cont'>View Note</a><br/>";
                        }

                        if(data.stat == "CANCELLED" || data.stat == "RECALLED"){
                            invoice += "<a href='#' onclick='SuppInvoice.addNote(\"" + data.spnum + "\")' class='depot_cont'>Add Note</a><br/>";
                        }
                        if(data.invs == "recallable" && data.stat != "RECALLED"){
                            invoice += "<a href='#' onclick='SuppInvoice.recallInvoice(\"" + data.spnum + "\")' class='depot_cont'>Recall Invoice</a><br/>";
                        }

                    

                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"}
            ]
        });

    }
}

var ProformaInvoice = {
    recallInvoice:function(number){
        var header =  '';
        var body = '';
        $.ajax({
            url:"/api/invoice_status/recall_invoice",
            type:"POST",
            data:{
                invn: number,
                prof: 1
            },
             success: function (data) {
                var response = $.parseJSON(data);
                if(response.st == 1133){
                    header = "Recalled Error";
                    body = "Invoiced has not be assigned reason";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('proforma_invoice');
                }
                else if (response.st == 1134){
                    header = "Recalled Error";
                    body = "Container(s) have been invoiced";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('proforma_invoice');
                }
                else if (response.st == 260){
                    header = "Recalled";
                    body = "Invoice have been recalled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('proforma_invoice');
                }
             },
             error:function () {
                 alert("something went wrong");
             }
         });
    },

    cancelInvoice: function(number) {
        var invoice = number;

        var url = "/api/invoice_status/cancel";
        var header =  '';
        var body = '';

        var request = new XMLHttpRequest();
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                var response = JSON.parse(request.responseText);
                if (response.st == 121){
                    header = "CANCELLATION ERROR";
                    body = "Note not added to invoice for cancellation";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('proforma_invoice');
                }
                if (response.st == 120){
                    header = "Deferred Error";
                    body = "Deferral refused, some containers have gated out.";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('proforma_invoice');
                }
                else if (response.st == 240){
                    header = "DEFERRAL CANCELLED";
                    body = "Deferred Invoice Number "+response.numb+"cancelled";
                    Modaler.dModal(header,body)
                    TableRfresh.freshTable('proforma_invoice');
                }
                else if (response.st == 241){
                    header = "CANCELLED";
                    body = "Invoice Number "+response.numb+" cancelled";
                    Modaler.dModal(header,body);
                    TableRfresh.freshTable('proforma_invoice');
                }

            }
            else {
                header = document.getElementById('myModalLabel');
                header = "FAILED";

                body = document.getElementById('conditionTable');
                body.innerText = "Cancellation Failed!";
            }
        };
        request.send("data=" + invoice + "&prof=1");


    },
    addWaiver: function(number, currency, cost){
        var waiver = new $.fn.dataTable.Editor( {
            fields: [
                {
                    label:'Cost',
                    name:'cost',
                    def: currency + " " + cost,
                    attr:{
                        class:"form-control",
                        disabled: true
                    }
                },
                {
                    label: "Waiver Type",
                    name: "waiver_type",
                    type:  "radio",
                    options: [
                        { label: "Amount", value: 0 },
                        { label: "Percentage",  value: 1 }
                    ],
                    attr: {
                        id: "waiver_type",
                        class: "form-control"
                    }
                },
                {
                    label:'Waiver Value',
                    name:'waiver',
                    attr:{
                        class:"form-control",
                        id: "waiver",
                        type: "number",
                        min: "0",
                        value: 0
                    }
                },
                {
                    label:'Note',
                    name:'waiver_note',
                    type:'textarea',
                    attr:{
                        class:"form-control",
                        id: "waiver_note"
                    }
                }]
        });
        waiver.create( {
            title: "Add Waiver For Invoice "+number,
            buttons: {text:"Add", action:function () {
                    var waiver = $('#waiver').val();
                    var waiver_type =  $("input[type=radio][name='waiver_type']:checked").val();
                    var waiver_note = $('#waiver_note').val();

                    if (waiver.trim() === "" || waiver < 0 || (waiver_type == 1 && waiver > 100))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Invalid Waiver Amount");

                        return;
                    }
                    if ((waiver_type == 1 && waiver > 100) || (waiver_type == 0 && waiver > cost))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Waiver exceeds Cost of Invoice");

                        return;
                    }
                    $.ajax({
                        url:"/api/invoice/add_waiver",
                        type:"POST",
                        data:{
                            wvrt: waiver_type,
                            invn: number,
                            invt: 1,
                            wvr: waiver,
                            wvrn: waiver_note,
                            prof: 1
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (result.st == 1122){
                                Modaler.dModal("INVALID WAIVER", "Waiver amount or percentage is less than 0.");
                            }
                            if (result.st == 1121){
                                Modaler.dModal("WAIVER ALREADY APPLIED", "Invoice already has a waiver.");
                            }
                            if (result.st == 1124){
                                Modaler.dModal("WAIVER EXCEEDS COST", "Resulting waiver exceeds the cost of the invoice.");
                            }
                            if (result.st == 1123){
                                Modaler.dModal("UNKNOW WAIVER TYPE", "Invoice is not approved");
                            }
                            if (result.st == 2113){
                                Modaler.dModal("WAIVER APPLIED", "Waiver has been successfully applied.");
                            }
                            TableRfresh.freshTable('invoice_waiver');
                        },
                        error:function () {
                            alert('something went wrong');
                        }
                    });
                    this.submit();
                }},
            focus: null
        } );
    },
    addNote:function(number){
        note_editor.create({
            title: 'Add Note',
            buttons: 'Add',
        });
        var invoice = document.getElementById("invoice_number1");
        invoice.value = number;
    },

    viewNote: function(id,number){
        var header = number;
        var body = "<div class=\"col-md-12\"><table id=\"view_notes\" class=\"display table-responsive\">" +
            "<thead><tr><th>Invoice Number </th><th>Note </th><th>Note Type </th><th>User </th></tr></thead>" +
            "</table></div>";

        CondModal.cModal(header, body);

        ProformaInvoiceNote.initTable(id);
    },

    iniTable: function () {

        note_editor = new $.fn.dataTable.Editor({
            fields:[
                {
                    label:"Number",
                    name:"number",
                    attr:{
                        class:"form-control",
                        id:"invoice_number1",
                        disabled: true
                    }
                },
                {
                    label:"Note Type",
                    name:"note_type",
                    type:"select",
                    options: [
                        {label: "CANCELLED", value: 1},
                        {label: "RECALLED", value: 2}
                    ],
                    attr:{
                        class:"form-control",
                        id:"note_type_id"
                    }
                },
                {
                    label:"Note",
                    name:"note",
                    type:"textarea",
                    attr:{
                        class:"form-control",
                        id:"note_id"
                    }
                }
            ]
        });

        note_editor.on("create", function () {
            var invoice_number = this.field('number');
            var note = this.field('note');
            var note_type = this.field('note_type');

           $.ajax({
              url:"/api/proforma_invoice/add_note",
              type:"POST",
              data:{
                  invn: invoice_number.val(),
                  note: note.val(),
                  ntype: note_type.val()
              },
               success: function (data) {
                  var response = $.parseJSON(data);
                  if (response.st == 122){
                    //   note.error("empty fields");
                      header = "Add Note Error";
                      body = "Cannot add empty field";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('proforma_invoice');
                  }
                  else if (response.st == 123){
                      header = "Add Note Error";
                      body = "Invoice must be unpaid";
                      Modaler.dModal(header,body);
                      TableRfresh.freshTable('proforma_invoice');
                  }
                      else if(response.st == 260){
                        header = "Add Note Success";
                        body = "Note Added successful";
                        Modaler.dModal(header,body);
                        TableRfresh.freshTable('proforma_invoice');
                    }
               },
               error:function () {
                   alert("something went wrong");
               }
           });

        });

        $('#proforma_invoice').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/proforma_invoice/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [13] },{ "searchable": false, "targets": 14 } ],
            order: [[ 13, 'desc' ]],
            columns: [
                {data: "trade_type.name", visible:false},
                {data: "proforma_invoice.number"},
                {data: "proforma_invoice.bl_number"},
                {data: "proforma_invoice.book_number", visible:false},
                {data: "proforma_invoice.do_number", visible:false},
                {data: "proforma_invoice.bill_date", visible:false},
                {data: "proforma_invoice.due_date"},
                {data: "proforma_invoice.cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "proforma_invoice.tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax_type.name"},
                {data: "note", visible:false},
                {data: "customer.name"},
                {data: "proforma_invoice.user_id", visible:false},
                {data: "proforma_invoice.date", visible:false},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.trade_type.name == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/proforma_import_invoice/show_import/' + data.proforma_invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/proforma_transit_invoice/show_transit/' + data.proforma_invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/proforma_export_invoice/show_export/' + data.proforma_invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.trade_type.name == 'EMPTY') {
                            invoice += '<a class="view_act" href="/api/proforma_empty_invoice/show_empty/' + data.proforma_invoice.number + '" target="_blank">View</a><br>';
                        }

                        if (data.proforma_invoice.status == 'UNPAID'  || data.proforma_invoice.status == 'DEFERRED'){
                            invoice += "<a href='#' onclick='ProformaInvoice.cancelInvoice(\"" + data.proforma_invoice.number + "\")' class='depot_cont'>Cancel</a><br/>";
                            invoice += "<a href='#' onclick='ProformaInvoice.addNote(\"" + data.proforma_invoice.number + "\")' class='depot_cont'>Add Note</a><br/>";
                        }
                        if(data.invn != null){
                            invoice += "<a href='#' onclick='ProformaInvoice.viewNote(" + data.invi + ",\"" + data.proforma_invoice.number +"\")' class='depot_cont'>View Note</a><br/>";
                        }

                        if (data.proforma_invoice.status != 'CANCELLED'  && data.proforma_invoice.status != 'EXPIRED') {
                            invoice += "<a href='#' onclick='ProformaInvoice.addWaiver(\"" + data.proforma_invoice.number + "\", \"" + data.currency.code + "\", " + data.proforma_invoice.cost + ")' class='depot_cont'>Add Waiver</a><br/>";
                        }
                        if(data.proforma_invoice.status == "CANCELLED" || data.proforma_invoice.status =="RECALLED"){
                            invoice += "<a href='#' onclick='ProformaInvoice.addNote(\"" + data.proforma_invoice.number + "\")' class='depot_cont'>Add Note</a><br/>";
                        }
                        if(data.invs == "recallable" && data.proforma_invoice.status != "RECALLED"){
                            invoice += "<a href='#' onclick='ProformaInvoice.recallInvoice(\"" + data.proforma_invoice.number + "\")' class='depot_cont'>Recall Invoice</a><br/>";
                        }

                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });

    }
}

var ProfromaSuppInvoice = {
    addWaiver: function(number, currency, cost){
        var waiver = new $.fn.dataTable.Editor( {
            fields: [
                {
                    label:'Cost',
                    name:'cost',
                    def: currency + " " + cost,
                    attr:{
                        class:"form-control",
                        disabled: true
                    }
                },
                {
                    label: "Waiver Type",
                    name: "waiver_type",
                    type:  "radio",
                    options: [
                        { label: "Amount", value: 0 },
                        { label: "Percentage",  value: 1 }
                    ],
                    attr: {
                        id: "waiver_type",
                        class: "form-control"
                    }
                },
                {
                    label:'Waiver Value',
                    name:'waiver',
                    attr:{
                        class:"form-control",
                        id: "waiver",
                        type: "number",
                        min: "0",
                        value: 0
                    }
                },
                {
                    label:'Note',
                    name:'waiver_note',
                    type:'textarea',
                    attr:{
                        class:"form-control",
                        id: "waiver_note"
                    }
                }]
        });
        waiver.create( {
            title: "Add Waiver For Invoice "+number,
            buttons: {text:"Add", action:function () {
                    var waiver = $('#waiver').val();
                    var waiver_type =  $("input[type=radio][name='waiver_type']:checked").val();//$('#waiver_type').val();
                    var waiver_note = $('#waiver_note').val();

                    if (waiver.trim() === "" || waiver < 0 || (waiver_type == 1 && waiver > 100))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Invalid Waiver Amount");

                        return;
                    }
                    if ((waiver_type == 1 && waiver > 100) || (waiver_type == 0 && waiver > cost))
                    {
                        var amount = this.field( 'waiver' );

                        amount.error("Waiver exceeds Cost of Invoice");

                        return;
                    }
                    $.ajax({
                        url:"/api/invoice/add_waiver",
                        type:"POST",
                        data:{
                            wvrt: waiver_type,
                            invn: number,
                            invt: 2,
                            wvr: waiver,
                            wvrn: waiver_note,
                            prof: 1
                        },
                        success: function (data) {
                            var result = $.parseJSON(data);

                            if (result.st == 1122){
                                Modaler.dModal("INVALID WAIVER", "Waiver amount or percentage is less than 0.");
                            }
                            if (result.st == 1121){
                                Modaler.dModal("WAIVER ALREADY APPLIED", "Invoice already has a waiver.");
                            }
                            if (result.st == 1124){
                                Modaler.dModal("WAIVER EXCEEDS COST", "Resulting waiver exceeds the cost of the invoice.");
                            }
                            if (result.st == 1123){
                                Modaler.dModal("UNKNOW WAIVER TYPE", "Invoice is not approved");
                            }
                            if (result.st == 2113){
                                Modaler.dModal("WAIVER APPLIED", "Waiver has been successfully applied.");
                            }
                            TableRfresh.freshTable('invoice_waiver');
                        },
                        error:function () {
                            alert('something went wrong');
                        }
                    });
                    this.submit();
                }},
            focus: null
        } );
    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/proforma_supp_invoice/table",
            table: "#proforma_supp_invoice",
            fields: []
        });

        $('#proforma_supp_invoice').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/proforma_supp_invoice/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [12] }, { "searchable": false, "targets": 13 }],
            order: [[ 12, 'desc' ]],
            columns: [
                {data: "ttyp", visible:false},
                {data: "spnum"},
                {data: "blnum"},
                {data: "dnum", visible:false},
                {data: "bdate", visible:false},
                {data: "ddate"},
                {data: "cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "txnam"},
                {data: "note", visible:false},
                {data: "name"},
                {data: "uid"},
                {data: "date"},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";

                        if (data.ttyp == 'IMPORT') {
                            invoice += '<a class="view_act" href="/api/proforma_supp_import_invoice/show_import/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        if (data.ttyp == 'EXPORT') {
                            invoice += '<a class="view_act" href="/api/proforma_supp_export_invoice/show_export/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        // if (data.trade_type.name == 'EMPTY') {
                        //     invoice += '<a class="view_act" href="/api/proforma_empty_invoice/show_empty/' + data.proforma_invoice.number + '" target="_blank">View</a><br>';
                        // }

                        if (data.ttyp == 'TRANSIT') {
                            invoice += '<a class="view_act" href="/api/proforma_supp_transit_invoice/show_transit/' + data.spnum + '" target="_blank">View</a><br>';
                        }

                        invoice += "<a href='#' onclick='ProfromaSuppInvoice.addWaiver(\"" + data.spnum + "\", \""+ data.code +"\", "+ data.cost + ")' class='depot_cont'>Add Waiver</a><br/>";

                        return invoice;

                    }
                }
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"}
            ]
        });

    }
}

var SupPayment = {

    iniTable: function (id) {

        $('#sup_payment').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/supp_payment/table",
                type: "POST",
                data: {
                    activity: id
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [3] },{ "searchable": false, "targets": 4 } ],
            order: [[ 3, 'desc' ]],
            columns: [
                {data: "supplementary_payment.receipt_number"},
                {data: "supplementary_payment.paid",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "user.first_name", visible:false},
                {data: "supplementary_payment.date"},
                {data: null,
                    render: function (data, type, row) {

                        var invoice = "";
                        if (data.invoice.trade_type == 1) {
                            invoice += '<a class="view_act" href="/api/SuppImportReciept/showReceipt/' + data.supplementary_payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        if (data.invoice.trade_type == 4) {
                            invoice += '<a class="view_act" href="/api/SuppExportReciept/showReceipt/' + data.supplementary_payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        if (data.invoice.trade_type == 8) {
                            invoice += '<a class="view_act" href="/api/SuppExportReciept/showReceipt/' + data.supplementary_payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        if (data.invoice.trade_type == 3) {
                            invoice += '<a class="view_act" href="/api/SuppTransitReceipt/show_receipt/' + data.supplementary_payment.receipt_number + '" target="_blank">Receipt</a><br>';
                        }
                        return invoice;

                    }
                }
            ],
            buttons: []
        });

    }
}

var InvoiceReports = {    
    iniTable: function () {

        $('#invoice_reports').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/invoice_report/table",
                type: "POST",
                data: function(d){
                    d.stdt = $('#start_date').val();
                    d.eddt = $('#end_date').val();
                    d.pstat = $('#payment_status').val();
                    d.istat = $('#invoice_status').val();
                    d.trty = $('#trade_type').val();
                    d.tax = $('#tax_type').val();
                }
            },
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api();
                    $.ajax({
                        url:"/api/invoice_report/total_cost",
                        type:"POST",
                        async: false,
                        data: {
                            stdt : $('#start_date').val(),
                            eddt : $('#end_date').val(),
                            trty : $('#trade_type').val(),
                            tax : $('#tax_type').val(),
                            pstat : $('#payment_status').val(),
                            istat : $('#invoice_status').val()
                        },
                        success:function (data) {
                            var result = $.parseJSON(data);
                            if (result.st == 232) {

                                $(api.column(0).footer()).html(
                                    ''
                                );

                                $(api.column(1).footer()).html(
                                    ''
                                );

                                $(api.column(2).footer()).html(
                                    ''
                                );

                                $(api.column(3).footer()).html(
                                    ''
                                );

                                $(api.column(4).footer()).html(
                                    ''
                                );

                                $(api.column(5).footer()).html(
                                    ''
                                );

                                $(api.column(6).footer()).html(
                                    'GHS '+ result.hand
                                );
                                $(api.column(7).footer()).html(
                                    'GHS '+ result.transfer
                                );
                                $(api.column(8).footer()).html(
                                    'GHS '+ result.partst
                                );
                                $(api.column(9).footer()).html(
                                    'GHS '+ result.unstuff
                                );
                                $(api.column(10).footer()).html(
                                    'GHS '+ result.ancilar
                                );
                                $(api.column(11).footer()).html(
                                    'GHS '+ result.stor
                                );
                                $(api.column(12).footer()).html(
                                    'GHS '+ result.gtax
                                );
                                $(api.column(13).footer()).html(
                                    'GHS '+ result.covtax
                                );
                                $(api.column(14).footer()).html(
                                    'GHS '+ result.nhtax
                                );
                                $(api.column(15).footer()).html(
                                    'GHS '+ result.vat
                                );
                                $(api.column(16).footer()).html(
                                    'QTY: '+ result.qty
                                );


                                $(api.column(17).footer()).html(
                                    'GHS '+ result.total
                                );

                                $(api.column(18).footer()).html(
                                    result.wpct+'%'
                                );

                                $(api.column(19).footer()).html(
                                    'GHS '+result.wamt
                                );

                                $(api.column(20).footer()).html(
                                    ''
                                );

                                $( api.column( 21 ).footer() ).html(
                                   ''
                                );

                                $(api.column(22).footer()).html(
                                    ''
                                );

                                $(api.column(23).footer()).html(
                                    ''
                                );

                                $(api.column(24).footer()).html(
                                    ''
                                );

                                $(api.column(25).footer()).html(
                                    ''
                                );

                                $(api.column(26).footer()).html(
                                    ''
                                );

                                $(api.column(27).footer()).html(
                                    ''
                                );

                                $(api.column(28).footer()).html(
                                    ''
                                );

                            }
                        },
                        error:function () {
                            alert("something went wrong");
                        }
                    });
                },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [13] } ],
            order: [[ 13, 'desc' ]],
            columns: [
                {data: 'trty', visible:false},
                {data: "num"},
                {data: "blnum", visible:false},
                {data: "dnum", visible:false},
                {data: "bdate", visible:false},
                {data: "ddate", visible:false},
                {data: "hand",render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "transfer",render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "partst",render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "unstuff", render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "ancilar",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "stor",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "gtax",visible:false,
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "covtax",visible:false,
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "nhtax",visible:false,
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "vat",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "qty", visible:false},
                {data: "total",
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "wpct", visible:false},
                {data: "wamt",visible:false,
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "tax",visible:false,
                render: $.fn.dataTable.render.number( ',', '.', 2 )},
                {data: "note", visible:false},
                {data: "cust",visible:false},
                {data: "wvnam", visible:false},
                {data: "clnam", visible:false},
                {data: "dfnam", visible:false},
                {data: "fname", visible:false},
                {data: "date", visible:false},
                {data: "stat",visible:false}
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {

                                var src = $('#invoice_reports').DataTable().columns().dataSrc();
                                var visible = $('#invoice_reports').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#invoice_reports').DataTable().column(i).header().innerHTML);
                                    }
                                }

                                $.ajax({
                                    url:"/api/invoice_report/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        eddt : $('#end_date').val(),
                                        trty : $('#trade_type').val(),
                                        tax : $('#tax_type').val(),
                                        pstat : $('#payment_status').val(),
                                        istat : $('#invoice_status').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "xsl"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#invoice_reports').DataTable().columns().dataSrc();
                                var visible = $('#invoice_reports').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#invoice_reports').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/invoice_report/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        eddt : $('#end_date').val(),
                                        trty : $('#trade_type').val(),
                                        tax : $('#tax_type').val(),
                                        pstat : $('#payment_status').val(),
                                        istat : $('#invoice_status').val(),
                                          src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "pdf"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        }
                    ],
                    className: "btn btn-primary"
                }
            ]
        });

        $('#start_date, #end_date,#payment_status,#invoice_status,#trade_type,#tax_type').on('change', function () {

            $('#invoice_reports').DataTable().ajax.reload();
        });

    }


}

var DepotActivityCharges = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/depot_activity_charges/table",
            table: "#depot_activity_charges",
            fields: [
                {
                    label: "Trade Type:",
                    name: "charges_container_depot_activity.trade_type",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Container Length:",
                    name: "charges_container_depot_activity.container_length",
                    type: "select",
                    attr: {
                        class: "form-control"
                    },
                    options: [
                        { label: "20 Foot", value: 20 },
                        { label: "40 Foot", value: 40 },
                        { label: "45 Foot", value: 45 },
                    ]
                },
                {
                    label: "Load Status:",
                    name: "charges_container_depot_activity.load_status",
                    attr: {
                        class: "form-control"
                    },
                    type:"select",
                    options:[
                        {label: "FCL", value: "FCL"},
                        {label: "LCL", value: "LCL"},
                        {label: "ANY", value: "ANY"}
                    ],
                },
                {
                    label: "Goods:",
                    name: "charges_container_depot_activity.goods",
                    attr: {
                        class: "form-control"
                    },
                    type:"select",
                    options:[
                        {label: "General Goods", value: "General Goods"},
                        {label: "Engines/Spares Parts", value: "Engines/Spares Parts"},
                        {label: "Vehicle", value: "Vehicle"},
                        {label: "DG I", value: "DG I"},
                        {label: "DG II", value: "DG II"}
                    ],
                },
                {
                    label: "OOG Status:",
                    name: "charges_container_depot_activity.oog_status",
                    type: "select",
                    attr: {
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ],
                },
                {
                    label: "Activity:",
                    name: "charges_container_depot_activity.activity",
                    type:"select",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Full Status:",
                    name: "charges_container_depot_activity.full_status",
                    type: "select",
                    attr: {
                        class: "form-control"
                    },
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ],
                    def: "YES"
                },
                {
                    label: "Cost:",
                    name: "charges_container_depot_activity.cost",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Currency:",
                    name: "charges_container_depot_activity.currency",
                    attr: {
                        class: "form-control"
                    },
                    type: "select"
                },
                {
                    label: "Date:",
                    name: "charges_container_depot_activity.date",
                    type:"datetime",
                    def:function () { return new Date(); },
                    format:"YYYY-MM-DD HH:mm",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        $('#depot_activity_charges').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/depot_activity_charges/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [10] } ],
            order: [[ 10, 'desc' ]],
            columns: [
                {data: "charges_container_depot_activity.id"},
                {data: "trade_type.name"},
                {data: "charges_container_depot_activity.container_length"},
                {data: "charges_container_depot_activity.load_status"},
                {data: "charges_container_depot_activity.goods"},
                {data: "charges_container_depot_activity.oog_status"},
                {data: "depot_activity.name"},
                {data: "charges_container_depot_activity.full_status"},
                {data: "charges_container_depot_activity.cost"},
                {data: "currency.code"},
                {data: "charges_container_depot_activity.date",visible:false}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Depot Charges')
        });

    }
}

var UCL = {

    activityAlert: function (id, container) {

        $('#container_number').text(container);

        var header = container;
        var body = "<div class=\"col-md-12\"><table id=\"container_activity\" class=\"display table-responsive\">" +
            "<thead><tr><th>Activity </th><th>Note</th><th>User</th><th>Date</th><th>ID</th></tr></thead>" +
            "</table></div>";
        CondModal.cModal(header, body);

        UCL.iniActivityTable(id,container);
    },

    iniActivityTable: function (id,container) {

        addActivity = new $.fn.dataTable.Editor({
            ajax: "/api/depot_overview/activity_table",
            fields:[
                {
                    label:"Container:",
                    name:"container_log.container_id",
                    attr:{
                        class:"form-control",
                        id:"containerID",
                        disabled: true
                    },
                    def: id
                },
                {
                    label:"Activity",
                    name:"container_log.activity_id",
                    attr:{
                        class:"form-control",
                        list: "activity_list"
                    }
                },{
                    label:"Note:",
                    name:"container_log.note",
                    type:"textarea",
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "User:",
                    name: "container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label:"Date:",
                    name:"container_log.date",
                    type:"datetime",
                    def:function () { return new Date(); },
                    format:"YYYY-MM-DD HH:mm",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        actEditor = new $.fn.dataTable.Editor({
            ajax: "/api/depot_overview/activity_table",
            table: "#container_activity",
            fields: [
                {
                    label: "Container:",
                    name: "container_log.container_id",
                    attr: {
                        class: "form-control",
                        id: "containerID",
                        disabled: true
                    },
                    def: container
                },
                {
                    label: "Activity:",
                    name: "container_log.activity_id",
                    attr: {
                        class: "form-control",
                        list: "activity_list"
                    }
                }, {
                    label: "Note:",
                    name: "container_log.note",
                    type: "textarea",
                    attr: {
                        class: "form-control"
                    }
                }, {
                    label: "User:",
                    name: "container_log.user_id",
                    attr: {
                        class: "form-control"
                    }
                }]
        });

        actEditor.field('container_log.user_id').hide();
        actEditor.field('container_log.container_id').hide();
        addActivity.field('container_log.user_id').hide();
        addActivity.field('container_log.container_id').hide();

        addActivity.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        actEditor.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        addActivity.on('submitSuccess', function () {
            TableRfresh.freshTable('container_activity');
        });

        $('#container_activity').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/depot_overview/activity_table",
                type: "POST",
                data: {
                    ctid: id
                }
            },
            columnDefs: [ { type: 'date', 'targets': [3] } ],
            order: [[ 3, 'asc' ]],
            serverSide: true,
            columns: [
                { data: "name" },
                { data: "container_log.note" },
                { data: "container_log.user_id" },
                { data: "container_log.date" },
                { data: "container_log.activity_id", visible:false }
            ],
            select: true,
            buttons: [
                { extend: "create", editor:addActivity, className:"btn btn-primary"},
                { extend: "edit", editor: actEditor, className:"btn btn-primary" },
                { extend: "remove", editor: actEditor, className: "btn btn-primary"}
            ]
        });

        // actEditor.on('preRemove', function () {
        //    alert('ok');
        // });

    },

    moveToDepot:function(container_id){
        $.ajax({
           url:"/api/ucl/move_to_depot",
           type:"POST",
           data:{
               cnum:container_id
           },
            success: function (data) {
                if (data){
                    var result = JSON.parse(data);
                    var header;
                    var body;
                    if (result.st == 1510){
                        header = "UCL Error";
                        body = "User does not have permission to move container to UCL";
                        Modaler.dModal(header,body);
                    }
                    if (result.st == 2200){
                        header = "UCL Update";
                        body = "Container has been moved to Depot";
                        Modaler.dModal(header,body);
                        TableRfresh.freshTable('ucl_depot');
                    }
                }
            },
            error:function () {
                alert("something went wrong");
            }
        });
    },

    iniTable:function () {

        $.ajax({
           url:"/api/ucl/load_days",
           type:"POST",
           data:{},
            success:function (data) {
                if (data){
                    var result = JSON.parse(data);
                    if (result.st == 2300){
                        document.getElementById('ucl_days').value = result.days;
                        document.getElementById('20_ft_charge').value = result.ch20 != 0 ? result.ch20 : (result.ch20).toFixed(2);
                        document.getElementById('40_ft_charge').value = result.ch40 != 0 ? result.ch40 : (result.ch40).toFixed(2);
                        document.getElementById('45_ft_charge').value = result.ch45 != 0 ? result.ch45 : (result.ch45).toFixed(2);
                    }
                }
            },
            error:function(){
               alert("something went wrong");
            }
        });

        $('#ucl_setting').click(function () {

            var ucdys = document.getElementById('ucl_days').value;
            var ch20 = document.getElementById('20_ft_charge').value;
            var ch40 = document.getElementById('40_ft_charge').value;
            var ch45 = document.getElementById('45_ft_charge').value;

            $.ajax({
               url:"/api/ucl/update_ucl_days" ,
                type:"POST",
                data:{
                   days:ucdys,
                   ch20:ch20,
                   ch40:ch40,
                   ch45:ch45
                },
                success:function (data) {
                    if (data){
                        var result = JSON.parse(data);

                        var header, body;

                        if (result.st == 1500) {
                            header = "Invalid data";
                            body = "Only valid numbers are allowed";
                            Modaler.dModal(header,body);
                        }
                        if (result.st == 2300) {
                            header = "UCL Update";
                            body = "Ucl depot days updated successfully";
                            Modaler.dModal(header,body);
                        }
                    }
                },
                error:function () {
                    alert("something went wrong");
                }
            });

        });

        $('#ucl_depot').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/ucl/table",
                type: "POST",
                data:function(d){
                    d.trade_type = $('#trade_type').val();
                }
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [15] }, { "searchable": false, "targets": 13 } ],
            order: [[ 15, 'desc' ]],
            columns: [
                { data: "cnum" },
                { data: "code" },
                { data: "blnum" },
                { data: "bknum",visible:false },
                { data: "depot" },
                { data: "gate" },
                { data: "veh" },
                { data: "drv" },
                { data: "tknam" },
                { data: "cons" },
                { data: "ref", visible:false },
                { data: "cond", visible:false },
                {data: "note",visible:false },
                {data: null,
                    render: function (data, type, row) {

                        var gated_record = "";

                        gated_record  +=  '<a class="view_act" href="#" onclick="UCL.activityAlert(' + data.cid + ',' + '\'' + data.cnum + '\'' + ')">Manage Container</a><br>';

                        gated_record  +=  '<a class="view_act" href="#" onclick="UCL.moveToDepot(' + data.cid + ')">Move to Depot</a><br>';


                        return gated_record;

                    }
                },
                { data: "eir", visible:false },
                { data: "date", visible:false },
                { data: "pdate", visible: false },
                { data: "user", visible: false },
                { data: "spsl", visible:false}
            ],
            select: true,
            buttons: [
                { extend:"colvis", className:"btn btn-primary"}
            ]
        });

        $('#trade_type').on('change', function () {
            $('#ucl_depot').DataTable().ajax.reload();
        });
    }
}

var Tax = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/tax/table",
            table: "#tax",
            fields: [
                {
                    label:"Type",
                    type: "select",
                    name:"tax.type",
                    options:[
                        {label:"Generic", value:1}
                    ],
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label: "Label",
                    name: "tax.label",
                    attr:{
                        class:"form-control",
                        maxlength:20
                    }
                },
                {
                    label: "Rate (&#37)",
                    name: "tax.rate",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Tax', 'Tax In Use Cannot Be Deleted.');
                }
            }
        });

        $('#tax').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/tax/table",
                type: "POST"
            },
            serverSide: true,
            columns:[
                {data: "tax_type.name"},
                {data: "tax.label"},
                {data: "tax.rate"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Tax')
        });
    }
}

var StorageRentCharges = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/storage_rent_charges/table",
                type: "POST"
            },
            table: "#storage_rent_charges",
            fields: [
                {
                    label: "Trade Type:",
                    name: "charges_storage_rent_teu.trade_type",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Goods:",
                    name: "charges_storage_rent_teu.goods",
                    attr: {
                        class: "form-control"
                    },
                    type:"select",
                    options:[
                        {label: "General Goods", value: "General Goods"},
                        {label: "Engines/Spares Parts", value: "Engines/Spares Parts"},
                        {label: "Vehicle", value: "Vehicle"},
                        {label: "DG I", value: "DG I"},
                        {label: "DG II", value: "DG II"}
                    ],
                },
                {
                    label: "Full Status:",
                    name: "charges_storage_rent_teu.full_status",
                    attr: {
                        class: "form-control",
                    },
                    type: 'select',
                    options: [
                        {label: "NO", value: "NO"},
                        {label: "YES", value: "YES"}
                    ],
                    def: "YES"
                },
                {
                    label: "Free Days:",
                    name: "charges_storage_rent_teu.free_days",
                    attr: {
                        maxlength: 3,
                        class: "form-control"
                    },
                },
                {
                    label: "First Billable Days:",
                    name: "charges_storage_rent_teu.first_billable_days",
                    attr: {
                        maxlength: 3,
                        class: "form-control"
                    },
                },
                {
                    label: "First Billable Days Cost:",
                    name: "charges_storage_rent_teu.first_billable_days_cost",
                    attr: {
                        maxlength: 19,
                        class: "form-control"
                    },
                },
                {
                    label: "Second Billable Days:",
                    name: "charges_storage_rent_teu.second_billable_days",
                    attr: {
                        maxlength: 3,
                        class: "form-control"
                    },
                },
                {
                    label: "Second Billable Days Cost:",
                    name: "charges_storage_rent_teu.second_billable_days_cost",
                    attr: {
                        maxlength: 19,
                        class: "form-control"
                    },
                },
                {
                    label: "All Other Billable Days:",
                    name: "charges_storage_rent_teu.allother_billable_days_cost",
                    attr: {
                        maxlength: 19,
                        class: "form-control"
                    }
                },
                {
                    label: "Currency:",
                    name: "charges_storage_rent_teu.currency",
                    attr: {
                        class: "form-control"
                    },
                    type: "select"
                },
                {
                    label: "Date:",
                    name: "charges_storage_rent_teu.date",
                    type:"datetime",
                    def:function () { return new Date(); },
                    format:"YYYY-MM-DD HH:mm",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        $('#storage_rent_charges').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/storage_rent_charges/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [9] } ],
            order: [[ 9, 'desc' ]],
            columns: [
                {data: "trade_type.name"},
                {data: "charges_storage_rent_teu.goods"},
                {data: "charges_storage_rent_teu.full_status"},
                {data: "charges_storage_rent_teu.free_days"},
                {data: "charges_storage_rent_teu.first_billable_days"},
                {data: "charges_storage_rent_teu.first_billable_days_cost"},
                {data: "charges_storage_rent_teu.second_billable_days"},
                {data: "charges_storage_rent_teu.second_billable_days_cost"},
                {data: "charges_storage_rent_teu.allother_billable_days_cost"},
                {data: "currency.code"},
                {data: "charges_storage_rent_teu.date"}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Storage Charges')
        });

    }
}

var ContainerMonitoringCharges = {
    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/container_monitoring_charges/table",
                type: "POST"
            },
            table: "#container_monitoring_charges",
            fields: [
                {
                    label: "ID:",
                    name: "charges_container_monitoring.id",
                    type: "hidden",
                    attr: {
                        maxlength: 19,
                        class: "form-control"
                    }
                },
                {
                    label: "Trade Type:",
                    name: "charges_container_monitoring.trade_type",
                    type: "select",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Goods:",
                    name: "charges_container_monitoring.goods",
                    attr: {
                        class: "form-control"
                    },
                    type:"select",
                    options:[
                        {label: "General Goods", value: "General Goods"},
                        {label: "Engines/Spares Parts", value: "Engines/Spares Parts"},
                        {label: "Vehicle", value: "Vehicle"},
                        {label: "DG I", value: "DG I"},
                        {label: "DG II", value: "DG II"}
                    ],
                },
                {
                    label: "Cost Per Day:",
                    name: "charges_container_monitoring.cost_per_day",
                    attr: {
                        maxlength: 19,
                        class: "form-control"
                    }
                },
                {
                    label: "Currency:",
                    name: "charges_container_monitoring.currency",
                    attr: {
                        class: "form-control"
                    },
                    type: "select"
                },
                {
                    label: "Date:",
                    name: "charges_container_monitoring.date",
                    type:"datetime",
                    def:function () { return new Date(); },
                    format:"YYYY-MM-DD HH:mm",
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        $('#container_monitoring_charges').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/container_monitoring_charges/table",
                type: "POST"
            },
            serverSide: true,
            columnDefs: [ { type: 'date', 'targets': [3] } ],
            order: [[ 3, 'desc' ]],
            columns: [
                {data: "trade_type.name"},
                {data: "charges_container_monitoring.goods"},
                {data: "charges_container_monitoring.cost_per_day"},
                {data: "currency.code"},
                {data: "charges_container_monitoring.date", visible: false}
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Container Monitoring Charges')
        });

    }
}

var Dashboard={
    Overview:function () {
        $.ajax({
            url:"/api/dashboard/show_overview",
            type:"POST",
            success:function (data) {
                var result = $.parseJSON(data);

                $('#laden').text(result.lad);
                $('#depot_empty').text(result.dep);
                $('#export').text(result.exp);

                $('#laden_amount').text(result.ladamt);
                $('#export_amount').text(result.expamt);

            },
            error:function () {
                alert("something went wrong");
            }
        });

        $('#dashboard_activity').DataTable({
            dom: "Brtip",
            ajax: {
                url: "/api/dashboard/table",
                type: "POST"
            },
            serverSide: true,
            columns: [
                {data: "name"},
                {data: "ft20"},
                {data: "ft40"}
            ],
            select: true,
            buttons:[],
        });
    }
}

var StockReport = {
    viewLine: function(id, line) {

        var header = '<b>' + line + '</b>'

        var body = "<table id=\"line_report\" class=\"display table\">" +
            "<thead><tr><th>State</th><th>Container</th><th>ISO Code</th><th>Status</th><th>Stock Date</th>" +
            "<th>Days Spent</th><th>Stack</th><th>Position</th><th>Client</th><th>Condition</th></tr></thead>" +
            "</table>";

        Modaler.dModal(header, body, 'modal-xl');

        StockReport.iniLineTable(id);


    },

    iniLineTable: function(id) {

        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/stock_report/line_table",
            table: "#line_report",
            fields: []
        });

        $('#line_report').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/stock_report/line_table",
                type: "POST",
                data: function (d) {
                    d.slid = id;
                    d.trty = $('#trade_type').val();
                }
            },
            serverSide: true,
            order: [[ 1, 'desc' ]],
            scrollX:  true,
            scrollY:  "400px",
            scrollCollapse: true,
            columns: [
                {data: "flag"},
                {data: "num"},
                {data: "code"},
                {data: "gstat"},
                {data: "sdate"},
                {data: "days"},
                {data: "stack"},
                {data: "pos"},
                {data: "agnt"},
                {data: "cond"}
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function () {
                                var src = $('#line_report').DataTable().columns().dataSrc();
                                var visible = $('#line_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#line_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/stock_report/line_report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        slid : id,
                                        trty : $('#trade_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        rtyp:"xsl"
                                    },
                                    success:function(data){
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function(){
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function(){
                                var src = $('#line_report').DataTable().columns().dataSrc();
                                var visible = $('#line_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#line_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/stock_report/line_report",
                                    type:"POST",
                                    async: false,
                                    data:{
                                        slid : id,
                                        trty : $('#trade_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        rtyp:"pdf"
                                    },
                                    success:function(data){
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function(){
                                        alert("something went wrong");
                                    }
                                });

                            }
                        },
                    ],
                    className: "btn btn-primary"
                }
            ],
        });
    },

    iniTable: function () {

        $('#depot_report').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/stock_report/table",
                type: "POST",
                data: function (d) {
                    d.trty = $('#trade_type').val();
                }
            },
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();

                // Total over this page

                var sum = 0;
                var sum1 = 0;
                for(var rown=0; rown < data.length; rown++){
                    sum += data[rown]['22U1'] + data[rown]['22G1'] + data[rown]['22P1'];
                    sum1 += data[rown]['45G1'] + data[rown]['42U1'] + data[rown]['45R1']+data[rown]['42P1'];
                }

                twenty_footer = sum;
                $( api.column( 4 ).footer() ).html(
                    'Total 20FT('+twenty_footer +')'
                );

                forty_footer = sum1;
                $( api.column( 8 ).footer() ).html(
                    'Total 40FT('+forty_footer +')'
                );

                var total_containers = 0;
                for(var rowd=0; rowd < data.length; rowd++){
                    total_containers += data[rowd]['OTHER'];
                }

                container = total_containers + twenty_footer + forty_footer;
                $( api.column( 1 ).footer() ).html(
                    'Total number of containers('+container +')'
                );

            },
            serverSide: true,
            columnDefs: [{ "searchable": false, "targets": 11 } ],
            columns: [
                { data: "sname" },
                { data: "22G1" },
                { data: "22U1" },
                { data: "22P1" },
                { data: "45G1" },
                { data: "42U1" },
                { data: "45R1" },
                { data: "42P1" },
                { data: "OTHER" },
                { data: "TU", visible:false },
                { data: "TTEU", visible:false },
                { data: null,
                    render: function (data, type, row) {
                        return "<a href=\'#\' onclick=\'StockReport.viewLine(" + data.shipping_line.id + ", \"" + data.sname + "\")\'>View</a><br>";
                    }
                },
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function () {
                                var src = $('#depot_report').DataTable().columns().dataSrc();
                                var visible = $('#depot_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#depot_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/stock_report/report",
                                    type:"POST",
                                    async: false,
                                    data:{
                                        typ: $('#trade_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        rtyp:"xsl"
                                    },
                                    success:function(data){
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function(){
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function(){
                                var src = $('#depot_report').DataTable().columns().dataSrc();
                                var visible = $('#depot_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#depot_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/stock_report/report",
                                    type:"POST",
                                    async: false,
                                    data:{
                                        typ: $('#trade_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        rtyp:"pdf"
                                    } ,
                                    success:function(data){
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function(){
                                        alert("something went wrong");
                                    }
                                });

                            }
                        },
                    ],
                    className: "btn btn-primary"
                }
            ],
        });

        $(' #gate_status, #trade_type').on('change', function () {
            $('#depot_report').DataTable().ajax.reload();
        });

    }
}

var GateReport = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/gate_report/table",
            table: "#gate_report",
            fields: []
        });


        $('#gate_report').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/gate_report/table",
                type: "POST",
                data:function (d) {
                    d.stdt = $('#start_date').val();
                    d.eddt = $('#end_date').val();
                    d.gtst = $('#gate_status').val();
                    d.trty = $('#trade_type').val();
                }
            },
            serverSide: true,
            order: [[ 10, 'desc' ]],
            columns: [
                { data: "ctnum" },
                { data: "code"},
                { data: "vyref"},
                { data: "trname",visible:false},
                { data: "gtname" },
                { data: "dpname",visible:false },
                { data: "vhnum" },
                { data: "dvname" },
                { data: "tkname", visible:false },
                { data: "ship" },
                { data: "date"},
                { data: "icsl1", visible:false},
                { data: "icsl2", visible:false},
                { data: "seal1", visible:false},
                { data: "seal2", visible:false},
                { data: "spsl", visible:false},
                { data: "goods",visible:false},
                { data: "act"},
                { data: "cond"},
                { data: "note",visible:false},
                { data: "cons",visible:false},
                { data: "exref", visible:false },
                { data: "pdate", visible: false },
                { data: "user", visible: false },
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function () {
                                var src = $('#gate_report').DataTable().columns().dataSrc();
                                var visible = $('#gate_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#gate_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/gate_report/report",
                                    type:"POST",
                                    async: false,
                                    data:{
                                        sdat: $('#start_date').val(),
                                        edat: $('#end_date').val(),
                                        typ: $('#trade_type').val(),
                                        gate_st: $('#gate_status').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        rtyp:"xsl"
                                    },
                                    success:function(data){
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function(){
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function(){
                                var src = $('#gate_report').DataTable().columns().dataSrc();
                                var visible = $('#gate_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#gate_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/gate_report/report",
                                    type:"POST",
                                    async: false,
                                    data:{
                                        sdat: $('#start_date').val(),
                                        edat: $('#end_date').val(),
                                        typ: $('#trade_type').val(),
                                        gate_st: $('#gate_status').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        rtyp:"pdf"
                                    } ,
                                    success:function(data){
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function(){
                                        alert("something went wrong");
                                    }
                                });

                            }
                        },
                    ],
                    className: "btn btn-primary"
                }
            ],
        });



        $('#start_date, #end_date,#gate_status,#trade_type').on('change', function () {
            $('#gate_report').DataTable().ajax.reload();
        });


    }
}

var PaymentReport = {
    formatMoney: function(number) {
        return number.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
    },

    iniTable: function () {
        $('#payment_report').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/payment_report/table",
                type: "POST",
                data:function (d) {
                    d.stdt = $('#start_date').val();
                    d.endt = $('#end_date').val();
                    d.pymd = $('#payment_mode').val();
                    d.trty = $('#trade_type').val();
                    d.txty = $('#tax_type').val();
                }
            },
            serverSide: true,
            order: [[ 27, 'desc' ]],
            columns: [
                { data: "rcpn",visible:false },
                { data: "inv" },
                { data: "idate",visible:false },
                { data: "wpct", visible:false },
                { data: "wamt",visible:false, render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "tax_type", visible:false },
                { data: "hand", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "transfer", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "p_unstuff", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "unstuff", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "stor", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "ancilar", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "qty", visible:false},
                { data: "getF", render: $.fn.dataTable.render.number( ',', '.', 2 ), visible:false },
                { data: "vat", render: $.fn.dataTable.render.number( ',', '.', 2 ), visible:false },
                { data: "nhil", render: $.fn.dataTable.render.number( ',', '.', 2 ),visible:false },
                { data: "covid", render: $.fn.dataTable.render.number( ',', '.', 2 ),visible:false },
                { data: "tax", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "cost", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "paid", render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "cheq", visible:false },
                { data: "bank", visible:false},
                { data: "trade_type", visible:false },
                { data: "cust", visible:false },
                { data: "mode", visible:false},
                { data: "date",visible:false },
                { data: "TEU", visible:false},
                { data: "user", visible:false }
            ],
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;
                $.ajax({
                    url:"/api/payment_report/total",
                    type:"POST",
                    async: false,
                    data: {
                        stdt : $('#start_date').val(),
                        endt : $('#end_date').val(),
                        pymd : $('#payment_mode').val(),
                        trty : $('#trade_type').val(),
                        txty : $('#tax_type').val(),
                    },
                    success:function (data) {
                        var result = $.parseJSON(data);
                        if (result.st == 230) {

                            $(api.column(0).footer()).html(
                                ''
                            );

                            $(api.column(1).footer()).html(
                                ''
                            );

                            $(api.column(2).footer()).html(
                               ''
                            );

                            $(api.column(3).footer()).html(
                                result.wpct+'%'
                            );

                            $(api.column(4).footer()).html(
                                'GHS '+ result.wamt
                             );

                             $(api.column(5).footer()).html(
                                ''
                             );

                            $(api.column(6).footer()).html(
                                'GHS '+ result.hand
                            ); 

                            $(api.column(7).footer()).html(
                                'GHS '+ result.trans
                             ); 
                             $(api.column(8).footer()).html(
                                'GHS '+ result.partst
                             ); 
                             $(api.column(9).footer()).html(
                                'GHS '+ result.unstu
                             ); 

                             $(api.column(10).footer()).html(
                                'GHS '+ result.stor
                             ); 

                             $(api.column(11).footer()).html(
                                'GHS '+ result.ancilar
                             ); 
                       

                             $(api.column(12).footer()).html(
                                'QTY: '+ result.qty
                             ); 

                             $(api.column(13).footer()).html(
                                'GHS ' + result.getf
                            );

                            $(api.column(14).footer()).html(
                                'GHS ' + result.vat
                            );

                            $(api.column(15).footer()).html(
                                'GHS ' + result.nhil
                            );

                            $(api.column(16).footer()).html(
                                'GHS ' + result.covid
                            );

                            $(api.column(17).footer()).html(
                                'GHS ' + result.tax
                            ); 

                            $(api.column(18).footer()).html(
                                'GHS ' + result.cost
                            ); 

                            $(api.column(19).footer()).html(
                                'GHS ' + result.paid
                            );

                            $(api.column(20).footer()).html(
                                ''
                            );

                            $(api.column(21).footer()).html(
                                ''
                            );

                            $(api.column(22).footer()).html(
                                ''
                            );

                            $(api.column(23).footer()).html(
                                ''
                            );

                            $(api.column(24).footer()).html(
                                ''
                            );

                            $(api.column(25).footer()).html(
                                ''
                            );

                            $(api.column(26).footer()).html(
                                ''
                            );

                            $(api.column(27).footer()).html(
                                ''
                            );
                        }
                    },
                    error:function () {
                        alert("something went wrong");
                    }
                });
            },
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#payment_report').DataTable().columns().dataSrc();
                                var visible = $('#payment_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#payment_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/payment_report/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        endt : $('#end_date').val(),
                                        pymd : $('#payment_mode').val(),
                                        trty : $('#trade_type').val(),
                                        txty : $('#tax_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "xsl"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#payment_report').DataTable().columns().dataSrc();
                                var visible = $('#payment_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#payment_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/payment_report/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        endt : $('#end_date').val(),
                                        pymd : $('#payment_mode').val(),
                                        trty : $('#trade_type').val(),
                                        txty : $('#tax_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "pdf"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        }
                    ],
                    className: "btn btn-primary"
                }
            ],
        });

        document.getElementById('start_date').valueAsDate = new Date();
        document.getElementById('end_date').valueAsDate = new Date();

        $('#start_date, #end_date, #payment_mode, #trade_type, #tax_type').on('change', function () {
            $('#payment_report').DataTable().ajax.reload();
        });


    }
}

var SummaryRemittance = {
    formatMoney: function(number) {
        return number.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
    },

    iniTable: function () {
        $('#summary_remittance').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url: "/api/summary_remittance/table",
                type: "POST",
                data:function (d) {
                    d.stdt = $('#start_date').val();
                    d.endt = $('#end_date').val();
                    d.pymd = $('#payment_mode').val();
                    d.trty = $('#trade_type').val();
                    d.txty = $('#tax_type').val();
                }
            },
            
            serverSide: true,
            columns: [
                { data: "date" },
                { data: "number" },
                { data: "teu",visible:false },
                { data: "stripping",
                render: $.fn.dataTable.render.number( ',', '.', 2 ), visible:false },
                { data: "dd_cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 ), visible:false },
                { data: "handling_cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "storage_cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "transport_cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "waiver_amount",
                render: $.fn.dataTable.render.number( ',', '.', 2 ), visible:false},
                { data: "invoice_cost",
                render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "vat",
                render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "covid19", 
                render: $.fn.dataTable.render.number( ',', '.', 2 ),visible:false },
                { data: "wht",
                render: $.fn.dataTable.render.number( ',', '.', 2 ), visible:false},
                { data: "getfund",
                render: $.fn.dataTable.render.number( ',', '.', 2 ), visible:false},
                { data: "name", visible:false },
                { data: "paid",
                render: $.fn.dataTable.render.number( ',', '.', 2 ) },
                { data: "outstanding",
                render: $.fn.dataTable.render.number( ',', '.', 2 ),visible:false },
                { data: "shipline", visible:false },
                { data: "vessel" },
                { data: "consignee" },
            ],
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;
                $.ajax({
                    url:"/api/summary_remittance/total",
                    type:"POST",
                    async: false,
                    data: {
                        stdt : $('#start_date').val(),
                        endt : $('#end_date').val(),
                    
                    },
                    success:function (data) {
                        var result = $.parseJSON(data);
                    
                        if (result.st == 232) {
                            $(api.column(0).footer()).html(
                                ''
                            );

                            $(api.column(1).footer()).html(
                                ''
                            );

                            $(api.column(2).footer()).html(
                                ''
                            );

                            $(api.column(3).footer()).html(
                               'GHS '+SummaryRemittance.formatMoney(result.tstp) 
                            );

                            $(api.column(4).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.ttdd)
                            );

                            $(api.column(5).footer()).html(
                               'GHS '+ SummaryRemittance.formatMoney(result.thd) 
                            );

                            $(api.column(6).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.tstrg)
                            );

                            $(api.column(7).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.ttsp) 
                            );

                            $(api.column(8).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.ttwv)
                            );

                            $(api.column(9).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.tinv)
                            );

                            $(api.column(10).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.ttvt)
                            );

                            $(api.column(11).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.tcvd)
                            );

                            $(api.column(12).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.twht)
                            );

                            $(api.column(13).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.tgfd)
                            );

                            $(api.column(14).footer()).html(
                                ''
                            );

                            $(api.column(15).footer()).html(
                               'GHS ' +SummaryRemittance.formatMoney(result.ttpd)
                            );

                            $(api.column(16).footer()).html(
                                'GHS '+SummaryRemittance.formatMoney(result.outb)
                            );

                            $(api.column(17).footer()).html(
                                ''
                            );

                            $(api.column(18).footer()).html(
                                ''
                            );

                            $(api.column(19).footer()).html(
                                ''
                            );
                        }
                    },
                    error:function () {
                        alert("something went wrong");
                    }
                });
            },
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#summary_remittance').DataTable().columns().dataSrc();
                                var visible = $('#summary_remittance').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#summary_remittance').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/summary_remittance/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        endt : $('#end_date').val(),
                                        pymd : $('#payment_mode').val(),
                                        trty : $('#trade_type').val(),
                                        txty : $('#tax_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "xsl"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                    var src = $('#summary_remittance').DataTable().columns().dataSrc();
                                    var visible = $('#summary_remittance').DataTable().columns().visible();
                                    var visible_columns = [];
                                    var visible_headers = [];
    
                                    for (var i = 0; i < src.length; i++) {
                                        if (visible[i]) {
                                            visible_columns.push(src[i]);
                                            visible_headers.push($('#summary_remittance').DataTable().column(i).header().innerHTML);
                                        }
                                    }
                                    $.ajax({
                                    url:"/api/summary_remittance/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        endt : $('#end_date').val(),
                                        pymd : $('#payment_mode').val(),
                                        trty : $('#trade_type').val(),
                                        txty : $('#tax_type').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "pdf"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        }
                    ],
                    className: "btn btn-primary"
                }
            ],
        });

        document.getElementById('start_date').valueAsDate = new Date();
        document.getElementById('end_date').valueAsDate = new Date();

        $('#start_date, #end_date, #payment_mode, #trade_type, #tax_type').on('change', function () {
            $('#summary_remittance').DataTable().ajax.reload();
        });


    }
}

var VoyageReports = {

    ViewVoyage:function(id,voyage_name){

        var header = voyage_name;
        var body = "<table id=\"voyage_details\" class=\"display table responsive\">" +
            "<thead><tr><th>State </th><th>Container Number</th><th>ISO Type Code </th><th>Gate Status</th><th>Stock Date</th><th>Days Spent</th>" +
            "<th>Stack</th><th>Position</th><th>Client</th><th>Condition</th></tr></thead>" +
            "</table>";
        CondModal.cModal(header, body);

        VoyageDetails.iniTable(id);
    },

    iniTable:function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/voyage_report/table",
            table: "#voyage_report",
            fields: []
        });

        $('#voyage_report').DataTable( {
            responsive: true,
            dom: "Bfrtip",
            ajax: {
                url: "/api/voyage_report/table",
                type: "POST",
                data: function (d) {
                    d.stdt = $('#start_date').val();
                    d.eddt = $('#end_date').val();
                }
            },
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
                $.ajax({
                    url:"/api/voyage_report/total",
                    type:"POST",
                    async: false,
                    data: {
                        stdt : $('#start_date').val(),
                        eddt : $('#end_date').val(),
                    },
                    success:function (data) {
                        var result = $.parseJSON(data);
                        if (result.st == 231) {
                            $(api.column(0).footer()).html(
                                'Total number of Containers: ' + result.cntr
                            );

                            $(api.column(2).footer()).html(
                                'Total 20FT: ' + result.ft20
                            );

                            $(api.column(4).footer()).html(
                                'Total 40FT: ' + result.ft40
                            );



                            $(api.column(6).footer()).html(
                                'Total TEU: ' + result.teu
                            );
                        }
                    },
                    error:function () {
                        alert("something went wrong");
                    }
                });
            },
            serverSide: true,
            order: [[ 1, 'desc' ]],
            columnDefs: [{ "searchable": false, "targets": 6 } ],
            columns: [
                { data: 'vref'},
                { data: 'vnam'},
                { data: '20FT'},
                { data: '40FT'},
                { data: 'TEU'},
                { data: 'adat'},
                { data: null, render:function (data, type, row) {
                        return "<a href='#' onclick='VoyageReports.ViewVoyage(" + data.voyid + ", \"" + data.vref +"\")'>View</a>";
                    }}
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#voyage_report').DataTable().columns().dataSrc();
                                var visible = $('#voyage_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#voyage_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/voyage_report/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        eddt : $('#end_date').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "xsl"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#voyage_report').DataTable().columns().dataSrc();
                                var visible = $('#voyage_report').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#voyage_report').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/voyage_report/report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        stdt : $('#start_date').val(),
                                        eddt : $('#end_date').val(),
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "pdf"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        }
                    ],
                    className: "btn btn-primary"
                }
            ],
        });



        $('#start_date, #end_date').on('change', function () {
                $('#voyage_report').DataTable().ajax.reload();
        });


    }
}

var EditDriver = {
    initTable:function (id) {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/DriverEditTable",
            table:'#driver_edit',
            fields:[
                {
                    label:'License',
                    name:'license',
                    attr:{
                        class:"form-control"
                    }
                },
                {
                    label:'Driver Name',
                    name:'name',
                    attr:{
                        class:"form-control"
                    }
                }
            ]
        });

        editor.on('open', function () {
            $('.modal').removeAttr('tabindex');
        });

        $('#driver_edit').DataTable({
            dom: "Bfrtip",
            ajax: {
                url:"/api/DriverEditTable",
                type:"POST",
                data:{
                    let_pass_id: id
                }
            },
            serverSide: true,
            columns:[
                {data:"license"},
                {data:"name"}
            ],
            select:true,
            buttons: LetDriverEdit.permissionButtonBuilder(editor,'Let Pass Driver',)
        });
    }
}


var VoyageDetails = {
    iniTable:function (id) {

        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/voyage_report/details_table",
            table: "#voyage_details",
            fields: []
        });

        $('#voyage_details').DataTable({
            dom: "Bfrtip",
            ajax: {
                url: "/api/voyage_report/details_table",
                type: "POST",
                data:{
                    vyid: id
                }
            },
            serverSide: true,
            order: [[ 1, 'desc' ]],
            scrollX:  true,
            scrollY:  "400px",
            scrollCollapse: true,
            columns: [
                {data: "stat"},
                {data: "ctnr"},
                {data: "code"},
                {data: "gstat"},
                {data: "stdat"},
                {data: "days"},
                {data: "stak"},
                {data: "pstn"},
                {data: "anam"},
                {data: "cond"}
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                {
                    extend: 'collection',
                    text: 'Download',
                    buttons: [
                        {extend: 'excel', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#voyage_details').DataTable().columns().dataSrc();
                                var visible = $('#voyage_details').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#voyage_details').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/voyage_report/details_report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        vyid: id,
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "xsl"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        },
                        {extend: 'pdf', className: "btn btn-primary active",
                            action:function (e, dt, node, config) {
                                var src = $('#voyage_details').DataTable().columns().dataSrc();
                                var visible = $('#voyage_details').DataTable().columns().visible();
                                var visible_columns = [];
                                var visible_headers = [];

                                for (var i = 0; i < src.length; i++) {
                                    if (visible[i]) {
                                        visible_columns.push(src[i]);
                                        visible_headers.push($('#voyage_details').DataTable().column(i).header().innerHTML);
                                    }
                                }
                                $.ajax({
                                    url:"/api/voyage_report/details_report",
                                    type:"POST",
                                    async: false,
                                    data: {
                                        vyid: id,
                                        src: JSON.stringify(visible_columns),
                                        head: JSON.stringify(visible_headers),
                                        type : "pdf"
                                    },
                                    success:function (data) {
                                        data = JSON.parse(data);
                                        var file = data.file;
                                        Helpers.loadFile(file);
                                    },
                                    error:function () {
                                        alert("something went wrong");
                                    }
                                });
                            }
                        }
                    ],
                    className: "btn btn-primary"
                }
            ],
        });

    }
}

var UserGroups = {

    iniTable:function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/user_group/table",
            table: "#user_group",
            template: '#customForm',
            fields: [ {
                label: "Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }, {
                label: "Status:",
                name: "status",
                type:"select",
                options:[
                    {label:"Active",value:1},
                    {label:"Non-Active",value:0}
                ],
                def: 0,
                attr: {
                    class: "form-control"
                }
            },
                {
                    label:"Deleted",
                    name:"deleted",
                    attr:{
                        class:"form-control"
                    },
                    def:0
                }
            ]
        });

        editor.field('deleted').hide();

        $('#user_group').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/user_group/table",
                type:"POST"
            },
            serverSide: true,
            columnDefs: [ { "searchable": false, "targets": 3 } ],
            columns: [
                { data: "name" },
                { data: "status" , visible:false},
                { data:"deleted", visible:false},
                {data: null,
                    render:function () {
                        return "<a href='/user/access_permission'>Permissions</a>";
                    }}
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                { extend: "create", editor: editor, className:"btn btn-primary" },
                { extend: "edit", editor: editor, className:"btn btn-primary" }
            ]
        });
    }



}

var ContainerCodes = {
    iniTable: function() {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/container/type_codes_table",
            table: "#container_codes",
            fields: [ {
                label: "Code:",
                name: "code",
                attr: {
                    class: "form-control",
                    maxlength: 30
                }
            }, {
                label: "Description:",
                name: "description",
                attr: {
                    class: "form-control",
                    maxlength: 30
                }
            },
                {
                    label:"Length:",
                    name:"length",
                    attr:{
                        class:"form-control",
                        maxlength: 30
                    }
                },
                {
                    label:"Height:",
                    name:"height",
                    attr:{
                        class:"form-control",
                        maxlength: 30
                    }
                },
                {
                    label:"Group:",
                    name:"grp",
                    attr:{
                        class:"form-control",
                        maxlength: 30
                    }
                }
            ]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete ISO Type Code', 'ISO Type Codes In Use Cannot Be Deleted.');
                }
            }
        });

        $('#container_codes').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/container/type_codes_table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                { data: "code" },
                { data: "description" },
                { data:"length"},
                { data:"height"},
                { data:"grp"},
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Container Codes')
        });

    }

}


var UserAccount = {
    lock: function(lock){

        //var flag = $('#status_flag').attr('value');

        $.ajax({
            type:"POST",
            url:"/api/user/user_lock",
            data: {user_id: lock},
            success: function () {
                Modaler.dModal('LOCKED', 'User Account Locked', 'sm');
                TableRfresh.freshTable('user_account');
            },
            error: function () {
                // swal('', 'something went wrong');
                $('#statusHeader').text('ERROR');
                $('#containerStatus').text('Something Went Wrong');
            }
        });


    },
    unlock: function(unlock){

        $.ajax({
            type:"POST",
            url:"/api/user/user_unlock",
            data: {user_id: unlock},
            success: function () {
                Modaler.dModal('UNLOCKED', 'User Account Unlocked', 'sm');
                TableRfresh.freshTable('user_account');
            },
            error: function () {
                $('#statusHeader').text('ERROR');
                $('#containerStatus').text('Something Went Wrong');
            }
        });

    },
    reset: function(reset){
        $.ajax({
            type:"POST",
            url:"/api/user/user_reset",
            data: {user_id: reset},
            success: function () {
                Modaler.dModal('PASSWORD RESET', 'Account Password Has Been Reset', 'sm');
                TableRfresh.freshTable('user_account');
            },
            error: function () {
                // swal('', 'something went wrong');
                $('#statusHeader').text('ERROR');
                $('#containerStatus').text('Something Went Wrong');
            }
        });

    },
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/user/table",
            table: "#user_account",
            fields: [ {
                label: "First Name:",
                name: "user.first_name",
                attr: {
                    class: "form-control",
                    maxlength: 50,
                }
            }, {
                label: "Last Name:",
                name: "user.last_name",
                attr: {
                    class: "form-control",
                    maxlength: 50,
                }
            }, {
                label: "Phone:",
                name: "user.phone",
                attr: {
                    class: "form-control",
                    maxlength: 20,
                }
            }, {
                label: "Email:",
                name: "user.email",
                attr: {
                    class: "form-control",
                    maxlength: 80,
                }
            }, {
                label: "Password:",
                name: "user.password",
                attr: {
                    class: "form-control",
                    disabled: true
                }
            },
                {
                    label:"Group",
                    name:"user.grp",
                    type:"select",
                    attr:{
                        class: "form-control"
                    }
                },
                {
                    label: "Status:",
                    name: "user.status",
                    attr: {
                        class: "form-control",
                    },
                    def: 0
                }]
        });
        editor.field('user.status').hide();
        var pword = editor.field('user.password');
        pword.def('secret');
        editor.on('initEdit', function (e) {
            pword.hide();
            pword.disable();
        });

        $('#user_account').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/user/table",
                type:"POST"
            },
            serverSide: true,
            columnDefs: [{ "searchable": false, "targets": 6 } ],
            columns: [
                { data: "user.first_name" },
                { data: "user.last_name" },
                { data: "user.phone" },
                { data: "user.email" },
                { data: "user.status" , visible:false},
                { data: "user_group.name", visible:false},
                { data: null,
                    render: function (data, type, row) {
                        var gated_status = "";
                        if (data.user.status == 0){
                            gated_status = "<a href='#' id='status_flag' value='' onclick='UserAccount.lock(\""+data.id+"\")'>Lock</a><br>";
                        }
                        if (data.user.status == 1){
                            gated_status = "<a href='#' id='status_unflag' value='' onclick='UserAccount.unlock(\""+data.id+"\")'>Unlock</a><br>";
                        }

                        gated_status += "<a href='#' id='status_unflag' value='' onclick='UserAccount.reset(\""+data.user.id+"\")'>Reset Password</a>"

                        return gated_status;
                    }
                },
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'User')
        });
    }
};

var Bank = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/bank/table",
            table: "#banks",
            fields: [{
                label: "Bank Name:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }]
        });

        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Unable To Delete Bank', 'Bank In Use Cannot Be Deleted.');
                }
            }
        });

        $('#banks').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/bank/table",
                type:"POST"
            },
            serverSide: true,
            order: [[ 0, 'desc' ]],
            columns: [
                { data: "name" },
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
                { extend: "create", editor: editor, className:"btn btn-primary" },
                { extend: "edit", editor: editor, className:"btn btn-primary" }
            ]
        });
    }
};

var ExchangeRate = {
    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/exchange_rate/table",
            table: "#exchange_rate",
            fields: [ {
                label: "Base:",
                name: "base",
                attr: {
                    list:"base_list",
                    class: "form-control",
                    maxlength:10
                }
            }, {
                label: "Quote:",
                name: "quote",
                attr: {
                    list:"quote_list",
                    class: "form-control",
                    maxlength:10
                }
            },{
                label:"Buying",
                name:"buying",
                attr:{
                    class:"form-control"
                }
            },{
                label:"Selling",
                name:"selling",
                attr:{
                    class:"form-control"
                }
            },{
                label:"User",
                name:"user_id",
                attr:{
                    class:"form-control"
                }
            }]
        });

        editor.field('user_id').hide();

        editor.on('initEdit', function () {
            editor.field('base').disable();
            editor.field('quote').disable();
        });


        editor.on( 'submitComplete', function ( e, json, data, action ) {
            var status = json.cancelled;

            if (action === 'remove') {
                if (status.length > 0) {
                    Modaler.dModal('Exchange Rate Error', 'Exchange rate In Use Cannot Be Deleted.');
                }
            }
        });

        $('#exchange_rate').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/exchange_rate/table",
                type:"POST"
            },
            serverSide: true,
            columns: [
                { data: "base" },
                { data: "quote" },
                { data: "buying" },
                { data: "selling" },
                { data: "user_id" },
                { data: "date" },
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Exchange Rate')
        });
    }
};

var Setting = {
    iniTable: function() {
        $.ajax({
            type:"POST",
            url:"/api/user/user_init",
            success: function (data) {
                var details = JSON.parse(data);

                document.getElementById('firstName').value = details['first'];
                document.getElementById('lastName').value = details['last'];
                document.getElementById('phone').value = details['phone'];
                document.getElementById('email').value = details['email'];
            },
            error: function () {
                // swal('', 'something went wrong');
                $('#statusHeader').text('ERROR');
                $('#containerStatus').text('Something Went Wrong');
            }
        });

    },

    update: function() {
        var first_name = document.getElementById('firstName').value;
        var last_name = document.getElementById('lastName').value;
        var phone = document.getElementById('phone').value;

        $('.first_name_error').text("");
        $('.last_name_error').text('');


        $.ajax({
            type:"POST",
            url:"/api/user/user_update",
            data: {
                first:first_name,
                last:last_name,
                phone:phone
                },
            success: function (data) {
                var result = JSON.parse(data);

                if (result.st == 180){
                    if (first_name == ""){
                        $('.first_name_error').text('Field cannot be empty');
                    }
                    if (last_name == ""){
                        $('.last_name_error').text('Field cannot be empty');
                    }
                }
                if (result.st == 181){
                    $('.first_name_error').text("Field length has exceeded it limit");
                }
                if (result.st == 182){
                    $('.last_name_error').text("Field length has exceeded it limit");
                }
                if (result.st == 11){
                    $('.phone_error').text("Phone number is invalid");
                }
                else if (result.st == 22) {
                    Modaler.dModal('UPDATE SUCCESSFUL', 'Your Personal Details Have Been Updated', 'sm');
                }
            },
            error: function () {
                $('#statusHeader').text('ERROR');
                $('#containerStatus').text('Something Went Wrong');
            }
        });

    },

    change: function() {
        var oldpword = document.getElementById('oldPword').value;
        var newpword = document.getElementById('newPword').value;
        var confpword = document.getElementById('confPword').value;
        if (oldpword == '' || newpword == '' || confpword == '') {
            Modaler.dModal('ERROR: EMPTY FIELD', 'None Of The Fields Can Be Empty', 'sm');
        } else if (newpword != confpword) {
            Modaler.dModal('ERROR: CONFIRMATION MISMATCH', 'New Password And Confirmation Do Not Match', 'sm');
        }else {
            $.ajax({
                type:"POST",
                url:"/api/user/user_change",
                data: {
                    oldp: oldpword,
                    newp: newpword,
                },
                success: function (data) {
                    var result = $.parseJSON(data);
                    if (result.st == 291) {
                        Modaler.dModal("Password Updated Successfully", 'Password Changed Successfully', 'sm');
                    } else{
                        Modaler.dModal("Error" + ': WRONG PASSWORD', 'Invalid Input For Current Password', 'sm');
                    }
                },
                error: function () {
                    Modaler.dModal('ERROR', 'Something Went Wrong');
                }
            });
        }


    }

};

var Login = {
    login: function() {
        if (event != undefined) {
            event.preventDefault();
        }
        var email = document.getElementById('email').value;
        var passwd = document.getElementById('password').value;

        if (email == '') {
            document.getElementById('error_em').textContent = 'Empty Field';
        } else {
            document.getElementById('error_em').textContent = '';
        }
        if (passwd == '') {
            document.getElementById('error_pw').textContent = 'Empty Field';
        } else {
            document.getElementById('error_pw').textContent = ''
        }
        if (!(email == '' || passwd == '')) {
            $.ajax({
                type:"POST",
                url:"api/login",
                async: false,
                data: {
                    em: email,
                    pass: passwd,
                },
                success: function (data) {            
                    if (data == 'ERROR') {
                        Modaler.dModal(data, 'Wrong Email And Password Combination', 'sm');
                    }
                    else {
                        sessionStorage.setItem('permission',data);
                        window.location = "/user/dashboard";
                    }
                },
                error: function () {
                    Modaler.dModal('ERROR', 'Something Went Wrong');
                }
            });
        }
    }
};

var Logout={
    logout:function(){
        $.ajax({
            type:"POST",
            url:"/api/logout",
            success:function(data){
                var result = JSON.parse(data);
                if(result.st == 101){
                    sessionStorage.clear();
                    window.location.href="/";
                }
            },
            error:function(){
                alert("something went wrong");
            }
        });
    }
}

var GroupAccess={
    GroupsOverview:function () {

        $.ajax({
            url:"/api/user_group/get_objects",
            type:"POST",
            success: function (data) {
                var result = $.parseJSON(data);
                var system_object = result.sobj;
                var perms = result.perms;

                $('table tr td:first-child').each(function (index) {
                    $(this).attr('id', system_object[index]);
                });

                $('#user_group').val(result.uid);

                $('table tr').each(function () {

                    var menu = $(this).find('td:nth-child(1)').attr('id');
                    var col = $(this).find('td:nth-child(2) input');
                    var col2 = $(this).find('td:nth-child(3) input');
                    var col3 = $(this).find('td:nth-child(4) input');
                    var col4 = $(this).find('td:nth-child(5) input');


                    $.each(perms, function (index,value) {

                        if (index == menu){
                            col.val(value.read);
                            col2.val(value.create);
                            col3.val(value.update);
                            col4.val(value.delete);
                            $('td input[type="checkbox"][value=1]').prop('checked', true);
                        }

                    });
                });
            },
            error: function () {
                Modaler.dModal("Error","something went wrong");
            }
        });

        var checks = $('tr td input[type=checkbox]');
        checks.each(function (index) {
            $(this).attr({
                value: "0",
                id: "chkbox"+index
            });
        });

        checks.on('click', function () {
            var vals = $(this).is(':checked') ? 1 : 0;
            $(this).val(vals);
        });

        $('#user_group').on('change', function () {

            var group_id = $('#user_group').val();
            var chk = $('table tr input');

            if (group_id){
                chk.val(0);
                chk.prop("checked", false);
            }

            $.ajax({
                url:"/api/user_group/get_users_group",
                type:"POST",
                data:{gpid: group_id},
                success: function (data) {
                    var result = $.parseJSON(data);

                    $('table tbody tr').each(function () {

                        var menu = $(this).find('td:nth-child(1)').attr('id');
                        var col = $(this).find('td:nth-child(2) input');
                        var col2 = $(this).find('td:nth-child(3) input');
                        var col3 = $(this).find('td:nth-child(4) input');
                        var col4 = $(this).find('td:nth-child(5) input');


                        $.each(result, function (index,value) {

                            if (index == menu){
                                col.val(value.read);
                                col2.val(value.create);
                                col3.val(value.update);
                                col4.val(value.delete);
                                $('td input[type="checkbox"][value=1]').prop('checked', true);
                            }

                        });
                    });


                },
                error: function () {
                    alert("SOmething went wrong");
                }
            });

        });


        $('#add_permission').on('click',function () {
            var user = $('#user_group').val();
            var perms = [];
            var header;
            var body;

            if (user == ''){
                header = "Access Error";
                body = "A user must be selected";
                Modaler.dModal(header,body);
            }
            else{
                $('table tbody tr').each(function () {
                    var menu = $(this).find('td:nth-child(1)').attr('id');
                    var col = $(this).find('td:nth-child(2) input').val();
                    var col2 = $(this).find('td:nth-child(3) input').val();
                    var col3 = $(this).find('td:nth-child(4) input').val();
                    var col4 = $(this).find('td:nth-child(5) input').val();
                    var chkbox = menu + ","  + col + "," + col2 + ","+ col3 + "," + col4;
                    perms.push(chkbox);
                });


                $.ajax({
                    url:"/api/user_group/edit_permission",
                    type:"POST",
                    data:{
                        perms: JSON.stringify(perms),
                        gpid: user
                    },
                    success: function () {
                        header = "User permission";
                        body = "User permission successfully assigned";
                        Modaler.dModal(header,body)
                    },
                    error: function () {
                        alert("something went wrong");
                    }
                });
            }


        });

    }
}


var ContainerHistory={
    searchContainer: function (e) {
        e.preventDefault();
        search = $('#container_search').val();
        if (!search){
            Modaler.dModal("Empty Search Query", "Please enter a search query");
        }
        else {
            $.ajax({
                url:"/api/container_history/search",
                type:"POST",
                async:false,
                data: {
                    qury: search
                },
                success: function (data) {
                    var result = JSON.parse(data);
                    if (result.st == "140"){
                        Modaler.dModal("Empty Search Query", "Please enter a search query");
                    }
                    if (result.st == "141"){
                        Modaler.dModal("No results", "No container history results were found for the container " + search);
                    }
                    else if (result.st == "240"){
                        localStorage.setItem('search', search);
                        search = "";
                        window.location.href = window.location.origin + "/user/container_history";
                    }
                },
                error: function () {
                    alert("something went wrong");
                }
            });
        }
    },

    viewHistory: function(id) {

        var header = '<b>' + search + '</b>'

        var body = "<table id=\"container_history\" class=\"display table history-table\">" +
            "<thead><tr><th>ID</th><th>Activity</th><th>Note</th><th>User</th><th>Date</th></tr></thead></table>";

        Modaler.dModal(header, body, 'modal-xl');

        ContainerHistory.iniHistory(id);
    },

    iniHistory:function (id) {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/container_history/view_history",
            table: "#container_history",
            template: "#customForm",
            fields: []
        });

        $('#container_history').DataTable({
            dom: "Bfrtip",
            pageLength: 5,
            ajax: {
                url:"/api/container_history/view_history",
                type:"POST",
                data: {
                    cntr: id
                }
            },
            serverSide: true,
            order: [[ 4, 'desc' ]],
            columns: [
                {data: "id"},
                {data: "act"},
                {data: "note"},
                {data: "user"},
                {data: "date"}

            ],
            order: [[ 4, "asc" ]],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"}
            ]
        });
    },

    iniTable:function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/container_history/results",
            table: "#history",
            template: "#customForm",
            fields: []
        });


        $('#history').DataTable({
            dom: "Bfrtip",
            ajax: {
                url:"/api/container_history/results",
                type:"POST",
                data: {
                    cntr: search
                }
            },
            serverSide: true,
            columnDefs: [
                { "searchable": false, "targets": 6 }
            ],
            columns: [
                {data: "id"},
                {data: "num"},
                {data: "blnum"},
                {data: "bknum"},
                {data: "trty"},
                {data: "len"},
                {data: "gtin"},
                {data: "gtout"},
                {data: null,
                    render: function (data, type, row) {
                        return "<a href=\'#\' onclick=\'ContainerHistory.viewHistory(" + data.id + ")\'>View</a><br>";
                    }
                },

            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"}
            ]
        });
    }
}

var System={
    systemSetting:function () {

        $.ajax({
            url:"/api/system_information/show_system_info",
            type:"POST",
            success: function (data) {
                var result = $.parseJSON(data);



                $('#company_name').val(result.cnam);
                $('#company_tin').val(result.ctin);
                $('#company_location').val(result.cloc);
                $('#company_phone_1').val(result.cph1);
                $('#company_phone_2').val(result.cph2);
                $('#company_email').val(result.mail);
                $('#company_web').val(result.cweb);
                $('#logo').val(result.logo);
                $('#tax_type').val(result.ttyp);
                $('#prefix').val(result.prfx);
                $('#idn_seperator').val(result.idns);
                $('#sw_version').val(result.swvn);
            },
            error: function () {
                alert("something went wrong");
            }
        });
    },

    updateSystem:function (e) {

        e.preventDefault();

        var company_name = $('#company_name').val();
        var company_tin = $('#company_tin').val();
        var company_location = $('#company_location').val();
        var company_phone_1 = $('#company_phone_1').val();
        var company_phone_2 = $('#company_phone_2').val();
        var company_email = $('#company_email').val();
        var company_web = $('#company_web').val();
        var tax_type = $('#tax_type').val();
        var prefix = $('#prefix').val();
        var idn_seperator = $('#idn_seperator').val();
        var sw_version = $('#sw_version').val();
        var company_logo = $('#company_logo').prop('files')[0];
        var logo = $('#logo').val();


        $('.name_error').text('');
        $('.tin_error').text('');
        $('.location_error').text('');
        $('.phone1_error').text('');
        $('.phone2_error').text('');
        $('.email_error').text('');
        $('.web_error').text('');
        $('.prefix_error').text('');
        $('.idn_error').text('');
        $('.logo_error').text('');
        $('.sw_error').text('');
        $('.logo_error').text('');



        var form_data = new FormData();
        form_data.append('cnam',company_name);
        form_data.append('ctin',company_tin);
        form_data.append('cloc',company_location);
        form_data.append('cph1',company_phone_1);
        form_data.append('cph2',company_phone_2);
        form_data.append('mail',company_email);
        form_data.append('cweb',company_web);
        form_data.append('ttyp',tax_type);
        form_data.append('file',company_logo);
        form_data.append('logo',logo);
        form_data.append('prfx',prefix);
        form_data.append('idns',idn_seperator);
        form_data.append('swvn',sw_version);


        if (company_name == ''){
            $('.name_error').text('Company name is required');
        }
        if (company_tin == ''){
            $('.tin_error').text('Company TIN is required');
        }
        if (company_location == ''){
            $('.location_error').text('Company location is required');
        }
        if (company_phone_1 == ''){
            $('.phone1_error').text('Company phone 1 is required');
        }


        var pattern = /[-]/g;

        if (idn_seperator == ''){
            $('.idn_error').text('Idn separator is required');
        }
        else if (!(idn_seperator.match(pattern))){
            $('.idn_error').text('Cannot use this character for Idn separator');
        }



        $.ajax({
            url:"/api/system_information/update_system",
            type:"POST",
            cache: false,
            contentType: false,
            processData: false,
            data:form_data,
            success: function (data) {
                var result = JSON.parse(data);
                    var header;
                    var body;

                    if (result.st == 120){
                        $('.logo_error').text('Image must be in PNG format');
                    }
                    if (result.st == 121){
                        $('.logo_error').text(' The Image size is too large');
                    }

                    if (result.st == 215) {
                        header = "System Update";
                        body = 'System updated successfully';
                        Modaler.dModal(header,body);
                    }
            },
            error:function () {
                Modaler.dModal('Error','Something went wrong');
            }
        });


    }

}

var EmptyBooking = {

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/empty_booking/table",
            table: "#booking",
            // template: "#customForm",
            fields: [
                {
                    label: "Shipping Line:",
                    name: "booking.shipping_line_id",
                    attr: {
                        class: "form-control",
                        list: "lines",
                    }
                },
                {
                    label: "Booked by Party:",
                    name: "booking.customer_id",
                    attr: {
                        class: "form-control",
                        list: "customers",
                    },
                },
                {
                    label: "Size:",
                    name: "booking.size",
                    type: "select",
                    options: [
                        {label: "20-Footer", value: '20'},
                        {label: "40-Footer", value: '40'}
                    ],
                    attr: {
                        class: "form-control",
                        // maxlength: 100
                    }
                },
                {
                    label: "Quantity:",
                    name: "booking.quantity",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "ACT:",
                    name: "booking.act",
                    type: "select",
                    def: "N/A",
                    attr: {
                        class: "form-control"
                    }
                },
                {
                    label: "Booking Number:",
                    name: "booking.booking_number",
                    attr: {
                        class: "form-control"
                    }
                },
            ]
        });

        editor.on('submitComplete', function (e, json, data, action){
            if (action === 'remove') {
                var status = json.cancelled;
                if (status.length > 0) {
                    Modaler.dModal('Create Booking Deletion Error', 'Booking has been already invoiced. Cancel invoice first');
                }
            }
        });

        $("#booking").DataTable({
            dom: "Bfrtip",
            ajax: {
                url:"/api/empty_booking/table",
                type:"POST"
            },
            serverSide: true,
            order: [[ 6, 'desc' ]],
            columns: [
                {data: "booking.shipping_line_id"},
                {data: "booking.customer_id"},
                {data: "booking.size"},
                {data: "booking.quantity"},
                {data: "booking.act"},
                {data: "booking.booking_number"},
                { data: "booking.date", visible:false },
            ],
            select: true,
            buttons: Helpers.permissionButtonBuilder(editor,'Booking')
        });
    },

};

var Booking = {
    getEmptyList: function (line, size, booking) {
        let data;
        let request = new XMLHttpRequest();
        url = '/api/booking/get_containers';
        request.open("POST", url);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.status == 200) {
                let response = JSON.parse(request.responseText);
                data = response;
            }
        };
        request.send(`line=${line}&size=${size}&booking=${booking}`);


        return data;
    },

    populateDatalist: function (containers) {
        let datalist = document.getElementById('containers');
        datalist.innerHTML = "";
        let listFragment = document.createDocumentFragment();
        containers.forEach(container => {
            localStorage[container[1]] = container[0];

            let option = document.createElement('option');
            option.innerText = container[1];

            listFragment.appendChild(option);
        });

        datalist.appendChild(listFragment);
    },

    checkNew: function (checks) {

    },

    checkBoxes: function (val, checks) {
        if (!(val == '' || val == NaN || val % 1 != 0 || val < 1 || checks.length < 1)) {
            checks.forEach(check => {
                if (checks.indexOf(check) < val)
                    check.checked = true;
                else 
                    check.checked = false;
            });
        }
    },

    tagEditChecks: function (checks, editData, booking) {
        for (var i = 0; i < editData.length; i++) {
            if (!(editData[i][2] == null || editData[i][2] == '')) {
                checks[i].setAttribute('book', booking);
            }
        }
    },

    checkEditBoxes: function (checks, booking) {
        checks.forEach(check => {
            check.getAttribute('book') == booking ? check.checked = true : check.checked = false;
        });
    },

    doAjaxRequest: function (val, values, action, editor, callback) {   
        let request = new XMLHttpRequest();
        let url;
        switch (action) {
            case 'create':
                url = '/api/booking/get_containers';
                break;
            
            case 'edit':
                url = '/api/booking/get_edit_containers';
                break;

            default:
                url = '/api/booking/get_containers';
                break;
        }
        
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.status == 200) {
                let response = JSON.parse(request.responseText);

                let booking = values['booking.booking_number'];
                if (action == 'edit') {
                    response = response.filter(value => value[2] == null || value[2] == '' || value[2] == booking)
                }

                let options = [];

                response.forEach(container => {
                    let option = {};
                    option.value = container[0];
                    option.label = container[1];

                    options.push(option);
                });

                editor.field('container[].id').update(options);

                let $editorCheck = $(editor.field('container[].id').dom.container[0].childNodes[1]);
                let $editorChecks = $editorCheck.find("input[type='checkbox']");
                let checkArray = Array.from($editorChecks);

                switch (action) {
                    case 'create':
                        Booking.checkBoxes(values['booking.quantity'], checkArray);
                        break;

                    case 'edit':
                        Booking.tagEditChecks(checkArray, response, values['booking.booking_number']);
                        Booking.checkEditBoxes(checkArray, values['booking.booking_number']);
                        break;

                    default:
                        Booking.checkBoxes(values['booking.quantity'], checkArray);
                        break;
                }

                callback({});
            }
        };

        request.send(`line=${values['booking.shipping_line_id']}&size=${values['booking.size']}`);
    },

    assignBookings: function (e, o, action) {
        let booking = this.field('booking.booking_number').val();
        let containerIds = this.field('container[].id').val();

        let request = new XMLHttpRequest();
        let url = '/api/booking/assign_bookings';
        request.open("POST", url, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.status == 200) {
                let response = JSON.parse(request.responseText);

                console.log(response);
            }
        };

        request.send(`booking=${booking}&ids=${containerIds}`);

    },

    getDeleteIds: function (event, values) {

        let ids = [];

        values.forEach(value => {
            var containers = event.target.s.editFields[value].data.container;
            
            containers.forEach(container => {
                ids.push(container.id);
            });
        });

        return ids;
    },

    getRowContainers: function () {
        let promise = new Promise((resolve, reject) => {
            let request = new XMLHttpRequest();
            let url = '/api/booking/get_row_containers';
            request.open("POST", url, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.onload = function() {
                if (this.status >= 200 && this.status < 300) {
                    resolve(JSON.parse(this.responseText));
                } else {
                    reject(`${this.status}: ${this.statusText}`);
                }
            };
        });

        return promise.then((containers))
    },

    assignEvent: function (check) {
        check.addEventListener('change', function(e) {
            if (this.checked == false) {
                $(this).parent().remove();
            }
        }, false);
    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor({
            ajax:"/api/booking/table",
            table: "#booking",
            // template: "#customForm",
            fields: [
                {
                    label: "Shipping Line:",
                    name: "booking.shipping_line_id",
                    attr: {
                        class: "form-control",
                        list: "lines",
                        disabled: true
                    },
                },
                {
                    label: "Booked by Party:",
                    name: "booking.customer_id",
                    attr: {
                        class: "form-control",
                        list: "lines",
                        disabled: true
                    },
                },
                {
                    label: "Size:",
                    name: "booking.size",
                    type: "select",
                    options: [
                        {label: "20-Footer", value: '20'},
                        {label: "40-Footer", value: '40'}
                    ],
                    attr: {
                        class: "form-control",
                        disabled: true
                    }
                },
                {
                    label: "Quantity:",
                    name: "booking.quantity",
                    attr: {
                        class: "form-control",
                        disabled: true
                    }
                },
                {
                    label: "Booking Number:",
                    name: "booking.booking_number",
                    attr: {
                        class: "form-control",
                        disabled: true
                    }
                },
                {
                    label: "Container(s):",
                    name: "container[].id",
                    type: "checkbox",
                    attr: {
                        id: "container-list",
                        class: "form-control"
                    },
                },
                {
                    label: "Add Container from Empty:",
                    name: "booking.container",
                    attr: {
                        class: "form-control",
                        list: "containers",
                    }
                },
            ]
        });

        editor.field('container[].id').message('Uncheck Container to Remove');
        editor.field('booking.container').message('Press Space Key to Add Container');

        editor.on('initEdit', function (event, data, values, row) {
            // do ajax to fetch and populate empty containers datalist
            let containers = Booking.getEmptyList(values.booking.shipping_line_id, 
                values.booking.size, values.booking.booking_number);
            Booking.populateDatalist(containers.empty);

            let options = [];
            containers.row.forEach(container => {
                let option = {};
                option.value = container[0];
                option.label = container[1];

                options.push(option);
            });

            editor.field('container[].id').update(options);

            let $editorCheck = $(editor.field('container[].id').dom.container[0].childNodes[1]);
            let $editorChecks = $editorCheck.find("input[type='checkbox']");

            let checkArray = Array.from($editorChecks);

            checkArray.forEach(check => {
                check.checked = true;
                Booking.assignEvent(check);
            });
        });

        editor.on('open', function(event, data, values, row) {
            editor.field('booking.container').focus();
        });

        editor.field('booking.container').input().on('focus', function (e) {
            let $editorCheck = $(editor.field('container[].id').dom.container[0].childNodes[1]);
            let $editorChecks = $editorCheck.find("input[type='checkbox']");
        })

        editor.field('booking.container').input().on('keyup', function (e, d) {
            e.preventDefault();
            if (e.originalEvent.key == ' ') {
                let number = this.value.trim();
                let id = localStorage[number];
                if (typeof id == "undefined")
                    return;

                let option = {};
                option.value = id;
                option.label = number;
                editor.field('container[].id').update([option], true);
                this.value = '';

                let $editorCheck = $(editor.field('container[].id').dom.container[0].childNodes[1]);
                let $editorChecks = $editorCheck.find("input[type='checkbox']");
    
                let checkArray = Array.from($editorChecks);
    
                checkArray[checkArray.length - 1].checked = true;

                Booking.assignEvent(checkArray[checkArray.length - 1]);
    
            }
        });

        editor.on('remove', function (event, data, values, row) {

            let info = values;

            let ids = Booking.getDeleteIds(event, values);


            let request = new XMLHttpRequest();
            let url = '/api/booking/unbook_containers';
            request.open("POST", url, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.onload = function() {
                if (request.status == 200) {
                    let response = JSON.parse(request.responseText);

                    console.log(response);
                }
            };

            request.send(`ids=${ids}`);

        });

        editor.on('preEdit', function (event, data, values, row) {
            let booking = this.field('booking.booking_number').val();
            let containerIds = this.field('container[].id').val();
            let containers = Array.from(data.data[0].container);
            let ids = event.target.s.editData['container[].id'][row];


            let request = new XMLHttpRequest();
            let url = '/api/booking/assign_edit_bookings';
            request.open("POST", url, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.onload = function() {
                if (request.status == 200) {

                    console.log('Bookings assigned');
                } else {
                    console.log("Something went wrong");
                }
            };

            request.send(`booking=${booking}&ids=${containerIds}&rowids=${ids}`);
        });

        editor.on('submitComplete', function (e, json, data, action){
            if (action === 'remove') {
                var status = json.cancelled;
                if (status.length > 0) {
                    Modaler.dModal('Booking Deletion Error', 'Booking has been already invoiced. Cancel invoice first');
                }
            }
        });

        $("#booking").DataTable({
            dom: "Bfrtip",
            ajax: {
                url:"/api/booking/table",
                type:"POST"
            },
            serverSide: true,
            columnDefs: [
                {
                    "searchable": false,
                    "targets": 7,
                }
            ],
            order: [[ 6, 'desc' ]],
            columns: [
                {data: "booking.shipping_line_id"},
                {data: "booking.customer_id"},
                {data: "booking.size"},
                {data: "booking.quantity"},
                {data: "booking.act"},
                {data: "booking.booking_number"},
                { data: "booking.date", visible:false },
                {data: "container", render: "[, ].number"}
           ],
            select: true,
            buttons: (function () {
                var permission=JSON.parse(sessionStorage.getItem('permission'));
                let buttons = [];
                if (permission[system_object]['update'] == 1) {
                    buttons.push({extend: "edit", editor: editor, formTitle: "Add/Remove Containers", className: "btn btn-primary"});
                }
                if ((permission[system_object]['delete'] == 1)) {
                    buttons.push({extend: "remove", editor: editor, formTitle: "Delete ", className: "btn btn-primary"});
                }
                return buttons;
        
            }()),
        });
    }
}

var MoveToExport = {
    move: function(id) {

        let form = $('div.move-form');
        let title = $('div.move-title').clone();
        form.css('position', 'static');
        let newForm = form.clone();
        let $shownInput = newForm.find('input');
        let $shownTextArea = newForm.find('textarea');
        let $shownButton = newForm.find('button');
        $shownInput.attr('id', 'move-input');
        $shownTextArea.attr('id', 'move-text');
        $shownButton.attr('id', 'move-button');
        $('#move-input').focus();
        EventModal.dModal('Export Booking Number', newForm, 'sm');

        var modalBody = document.querySelector('div.modal-body');
        form.css('position', 'absolute');

        $shownButton.on('click', '', id, MoveToExport.addBooking);
        $shownInput.on('keypress', function (event) {
            if (event.key === "Enter") {
                event.preventDefault();

                document.getElementById('move-button').click();
            }
        });

        $shownInput.focus();
    },

    enterIsClick: function() {
        console.log('Enter is click');
        var input = document.getElementById('move-input');

        input.addEventListener('keyup', function (event) {
            if (event.key === "Enter") {
                event.preventDefault();

                document.getElementById('move-button').click();
            }
        })
    },

    moveCall: function (id, booking, content) {
        let request = new XMLHttpRequest();
        url = '/api/move_to_export/move';
        request.open("POST", url);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.status == 200) {
                TableRfresh.freshTable('move-to-export');
                $('div.modal').removeClass('show');
                $('div.modal-backdrop').remove();
                $('div.modal').remove();
                Modaler.dModal('MOVED', 'Empty Container Moved To Export Depot', 'sm')
            } else {
                alert('Something went wrong');
            }
        };
        request.send(`id=${id}&booking=${booking}&content=${content}`);
    },

    addBooking: function(id) {
        if ($('#move-input').val() == '') {
            return;
        }
        MoveToExport.moveCall(id.data, $('#move-input').val(), $('#move-text').val());
    },

    cancel: function(id) {
        MoveToExport.cancelCall(id);
    },

    cancelCall: function(id) {
        let request = new XMLHttpRequest();
        url = '/api/move_to_export/cancel';
        request.open("POST", url);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onload = function() {
            if (request.status == 200) {
                TableRfresh.freshTable('move-to-export');
                Modaler.dModal('CANCELED', 'Move of Empty Container To Export Depot Canceled', 'sm')
            } else {
                alert('Something went wrong');
            }
        };
        request.send(`id=${id}`);

    },

    iniTable: function () {
        editor = new $.fn.dataTable.Editor( {
            ajax: "/api/move_to_export/table",
            table: "#move-to-export",
            // template: '#customForm',
            fields: [ {
                label: "Container:",
                name: "number",
                attr: {
                    class: "form-control",
                    maxlength: 10
                }
            }, {
                label: "Shipping Line:",
                name: "name",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            }, {
                label: "Empty Booking:",
                name: "book_number",
                attr: {
                    class: "form-control",
                    maxlength: 100
                }
            },]
        });

        $('#move-to-export').DataTable( {
            dom: "Bfrtip",
            ajax: {
                url:"/api/move_to_export/table",
                type:"POST"
            },
            serverSide: true,
            columnDefs: [
                {
                    "searchable": false,
                    "targets": 3,
                }
            ],
            columns: [
                { data: "number" },
                { data: "name" },
                { data: "book_number" },
                {
                    data: null,
                    render: function (data, type, row) {
                        var emptyAction = "";

                        if (data.moved_to == null) {
                            emptyAction += "<a href='#' onclick='MoveToExport.move(" + data.id +")' class='check_cond'>Move</a><br/>";

                        } else {
                            emptyAction += "<a href='#' onclick='MoveToExport.cancel(" + data.moved_to +")' class='check_cond'>Cancel</a><br/>";
                        }

                        return emptyAction;
                    }
                },
            ],
            select: true,
            buttons: [
                { extend: "colvis", className:"btn btn-primary"},
            ]
        });
    }

}

var GateInHelper={
    permissionButtonBuilder:function (editor, expressEditor,formTitle) {
        var permission=JSON.parse(sessionStorage.getItem('permission'));
        var buttons=[{extend: "colvis", className: "btn btn-primary"}];
        if (permission[system_object]['create'] == 1) {
            buttons.push({extend: "create", editor: editor, formTitle: formTitle, className: "btn btn-primary", text: formTitle});
        }
        if (permission[system_object]['create'] == 1) {
            buttons.push({extend: "create", editor: expressEditor, formTitle: "Express Gate In", className: "btn btn-primary", text: "Express"});
        }
        if (permission[system_object]['create'] == 1) {
            buttons.push({extend: "create", editor: emptyEditor, formTitle: "Empty Gate In", className: "btn btn-primary", text: "Empty"});
        }
        if (permission[system_object]['update'] == 1) {
            buttons.push({extend: "edit", editor: editor, formTitle: "Edit "+formTitle, className: "btn btn-primary"});
        }
        if ((permission[system_object]['delete'] == 1)) {
            buttons.push({extend: "remove", editor: editor, formTitle: "Delete "+formTitle, className: "btn btn-primary"});
        }
        return buttons;
    }
}

var GateOutHelper={
    permissionButtonBuilder:function (editor,formTitle) {
        var permission=JSON.parse(sessionStorage.getItem('permission'));
        var buttons=[{extend: "colvis", className: "btn btn-primary"}];
        if (permission[system_object]['create'] == 1) {
            buttons.push({extend: "create", editor: editor, formTitle: formTitle, className: "btn btn-primary", text: formTitle});
        }
        if (permission[system_object]['update'] == 1) {
            buttons.push({extend: "edit", editor: editor, formTitle: "Edit "+formTitle, className: "btn btn-primary"});
        }
        if ((permission[system_object]['delete'] == 1)) {
            buttons.push({extend: "remove", editor: editor, formTitle: "Delete "+formTitle, className: "btn btn-primary"});
        }
        return buttons;
    }
}

var LetPassHelper={
    permissionButtonBuilder:function (editor,formTitle) {
        var permission=JSON.parse(sessionStorage.getItem('permission'));
        var buttons=[{extend: "colvis", className: "btn btn-primary"}];
        if (permission[system_object]['update'] == 1) {
            buttons.push({extend: "edit", editor: editor, formTitle: "Edit "+formTitle, className: "btn btn-primary"});
        }
        if ((permission[system_object]['delete'] == 1)) {
            buttons.push({extend: "remove", editor: editor, formTitle: "Delete "+formTitle, className: "btn btn-primary"});
        }
        return buttons;
    }
}

var LetDriverEdit={
    permissionButtonBuilder:function (editor,formTitle) {
        var permission=JSON.parse(sessionStorage.getItem('permission'));
        var buttons=[];
        if (permission[system_object]['update'] == 1) {
            buttons.push({extend: "edit", editor: editor, formTitle: "Edit "+formTitle, className: "btn btn-primary"});
        }
        return buttons;
    }
}

var TruckButtons={
    permissionButtonBuilder:function (editor,formTitle) {
        var permission=JSON.parse(sessionStorage.getItem('permission'));
        var buttons=[{extend: "colvis", className: "btn btn-primary"}];
        if (permission[system_object]['create'] == 1) {
            buttons.push({extend: "create", editor: editor, formTitle: "Add "+formTitle,text:"Truck Gate In", className: "btn btn-primary"});
        }
        if ((permission[system_object]['delete'] == 1)) {
            buttons.push({extend: "remove", editor: editor, formTitle: "Delete "+formTitle, className: "btn btn-primary"});
        }
        return buttons;
    }
}

var Helpers={
    permissionButtonBuilder:function (editor,formTitle) {
        var permission=JSON.parse(sessionStorage.getItem('permission'));
        var buttons=[{extend: "colvis", className: "btn btn-primary"}];
        if (permission[system_object]['create'] == 1) {
            buttons.push({extend: "create", editor: editor, formTitle: "Add "+formTitle, className: "btn btn-primary"});
        }
        if (permission[system_object]['update'] == 1) {
            buttons.push({extend: "edit", editor: editor, formTitle: "Edit "+formTitle, className: "btn btn-primary"});
        }
        if ((permission[system_object]['delete'] == 1)) {
            buttons.push({extend: "remove", editor: editor, formTitle: "Delete "+formTitle, className: "btn btn-primary"});
        }
        return buttons;
    },
    loadFile : function (file) {
        var request = new XMLHttpRequest();
        request.open('POST', "/report/" + file, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.responseType = 'blob';

        request.onload = function() {
            if(request.status === 200) {
                var blob = new Blob([request.response], { type: request.getResponseHeader('content-type') });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = file;
                link.click();
            }
        };

        request.send();
    }
}