let reserveConf = document.getElementById('reserve-conf');
config = {'datatables': {}};
if (reserveConf) {
    config = JSON.parse(reserveConf.getAttribute('data-conf'));
}

$('#datatable').DataTable(config.datatables);
