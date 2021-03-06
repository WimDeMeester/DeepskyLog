<script type="text/javascript" src="lib/javascript/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="lib/javascript/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="styles/dataTables.bootstrap.css">

<script>
	var datatablesConfig = {
    	"language": {
    	    "search": 		  "<?=_("Search")?>",
    	    "lengthMenu":	  "_MENU_",
    	    "info":           "_START_ - _END_ (_TOTAL_)",
    	    "infoEmpty":      "",
    	    "loadingRecords": '<img src="/img/loading.gif">&nbsp;<?=_("Please wait a moment while loading data...")?>',
    	    "emptyTable":     "<?=_("Sorry, no observations found!")?>",
    	    "zeroRecords":    "<?=_("Sorry, no observations found!")?>",
    	    "paginate": {
    	        "next":       "<?=_("Next")?>",
    	        "previous":   "<?=_("Previous")?>" }   	    
   		 },   		 
   		"stateSave": true,
   		"stateLoadParams": function (settings, data) {
   		    data.search.search = "";
   		 	data.start = 0;
   		},
   		"order": [[1, 'desc']], 
        "lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "<?=_('show all')?>"]]

    };

</script>