let reserveConfReservation = document.getElementById('reserve-conf');
reservationConfig = {'datatables': {}};
if (reserveConfReservation) {
    reservationConfig = JSON.parse(reserveConfReservation.getAttribute('data-conf'));
}

$('#datatable').DataTable(reservationConfig.datatables);
