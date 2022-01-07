function replaceAll(str, find, replace) {
    return str.replace(new RegExp(find, 'g'), replace);
}

var circleauth_plugin = {
    wp_roles: JSON.parse(wp_roles),
    domain_roles: JSON.parse(domain_roles),
    initialized: 0,

    validateTags: function (event) {
        console.log(event);

    },
    comboWpRoles: function (role) {
        var values = this.wp_roles;
        var select = document.createElement("select");
        select.className = "wp_roles";

        for (const val of values) {
            var option = document.createElement("option");
            option.value = val;
            if (val.toLowerCase() == role.toLowerCase()) {
                option.selected = true;
            }

            option.text = val.charAt(0).toUpperCase() + val.slice(1);
            select.appendChild(option);
        }

        $(select).on("change", function () {
            circleauth_plugin.updateData();
        });

        return select;

    },
    bindEvents: function () {

        $(".wp_roles").on("change", function () {
            circleauth_plugin.updateData();
        });

        $(".wp_roles").on("focus", function () {
            circleauth_plugin.initialized = 1;
        });


        $(".tags").each(function () {
            tagify = new Tagify(this, {
                delimiters: " ", // add new tags when a comma or a space character is entered
                blacklist: ["fuck", "shit", "pussy"],
                callbacks: {
                    "change": (e) => circleauth_plugin.updateData()
                },
                originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(','),

            });
        });

        $(".icon-img").on("click", function () {

            var obj = this;

            $.confirm({
                title: 'Confirm!',
                content: 'Click on "Delete" to confirm the deletion. ',
                useBootstrap: true,
                buttons: {
                    delete: function () {

                        $(obj).closest("tr").remove();
                        circleauth_plugin.updateData();
                    },
                    cancel: function () { }
                }
            });


        });

    },
    addTagify: function (elm) {

        tagify = new Tagify(elm, {
            delimiters: " ", // add new tags when a comma or a space character is entered
            blacklist: ["fuck", "shit", "pussy"],
            callbacks: {
                "change": (e) => circleauth_plugin.updateData()
            },
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(','),
        });
    },
    createInputTable: function (obj) {

        let table = $('<table id="domain_roles" >').addClass('domain-table-list');
        let nr_rows = (this.domain_roles) ? (Object.keys(this.domain_roles).length) : 0;

        //add a blank row
        if (nr_rows == 0) {
            this.domain_roles = JSON.parse('[{"role":"subscriber","domains":"All"}]');
            nr_rows = 1;
        }

        //add rows
        for (i = 0; i < nr_rows; i++) {
            let row = $('<tr>').addClass('bar');

            //add cells
            let domains = replaceAll(this.domain_roles[i].domains, ",", " ");
            let td1 = $('<td><input class="domains_input tags" type="text" value="' + domains + '">');
            row.append(td1);

            let td2 = $('<td>');
            let combo = this.comboWpRoles(this.domain_roles[i].role);
            td2.append(combo);
            row.append(td2);

            let td3 = $('<td><img class="icon-img" src="' + remove_icon + '" />');
            row.append(td3);

            table.append(row);
        }
        $('#' + obj).append(table);

    },
    addTableRow: function () {
        let microtime = (Date.now() % 1000) / 1000;
        let table = $("#domain_roles");
        let row = $('<tr>').addClass('bar');
        let td1 = $('<td><input id="inp_' + microtime + '" class="domains_input tags" type="text" value="">');
        row.append(td1);

        let td2 = $('<td>');
        let combo = this.comboWpRoles("");
        td2.append(combo);
        row.append(td2);

        let td3 = $('<td><img class="icon-img" src="' + remove_icon + '" />');
        row.append(td3);
        $(table).append(row);
        //add tags
        var ipt = document.getElementById("inp_" + microtime);
        this.addTagify(ipt);
        ipt.focus();

        setTimeout(function () {
            $("#inp_" + microtime).focus();
            ipt.focus();
            console.log('focus');
        }, 1200);


    },

    updateData: function () {
        let domains = "";
        let role = "";

        let jsonData = [];

        $('#domain_roles tr').each(function (index, tr) {
            domains = ($(this).find("td:eq(0) input[type='text']").val());
            role = ($(this).find("td:eq(1) select").val());

            item = {}
            item["role"] = role;
            item["domains"] = domains;

            jsonData.push(item);

        });

        $("#user_roles").val(JSON.stringify(jsonData));
    }
}

function createComboRoles(obj_id, role) {
    var values = JSON.parse(wp_roles);

    var select = document.createElement("select");
    for (const val of values) {
        var option = document.createElement("option");
        option.value = val;
        if (val.toLowerCase() == role.toLowerCase()) {
            option.selected = true;
        }

        option.text = val.charAt(0).toUpperCase() + val.slice(1);
        select.appendChild(option);
    }
    document.getElementById(obj_id).appendChild(select);
}


function add_listeners(obj) {
    $(obj).on("click", function (event) {

        var idx = $(obj).attr('idx');
        if (confirm('Are you sure to remove the role?')) {
            $(obj).closest("tr").remove();
        } else {
            return false;
        }
    });
}

$(document).ready(function () {

    //circleauth_plugin.comboWpRoles("sell",'Contributor');
    circleauth_plugin.createInputTable("sell");
    circleauth_plugin.bindEvents();


});


function add_user_role() {
    circleauth_plugin.addTableRow();
}