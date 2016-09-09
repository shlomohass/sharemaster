<?php

Trace::add_step(__FILE__,"Loading Sub Page: admin -> users");

?>
<h2>Users Managment</h2>

<table id="user-grid" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered display" width="100%">
    <thead>
        <tr>
            <th>User Name</th>
            <th>Seen</th>
            <th>Created</th>
            <th>last_seen</th>
            <th>email</th>
        </tr>
    </thead>
</table>
<script type="text/javascript" language="javascript" >
    var dataTable = $('#user-grid').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax":{
            url : "index.php",
            type: "post",
            data: function ( d ) {
                    return $.extend( {}, d, {
                            req:"api",
                            token:$("#pagetoken").val(),
                            type:"tableuser"
                        } ); 
            },
            error: function(err, ms){  // error handling
                console.log("error",err);
            }
        }
    } );
</script>