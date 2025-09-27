@push('scripts')
    <script>
        function billDatas(reservationData) {
            return {
                formData : {
                    tarif : reservationData.tarif,
                    majoration: reservationData.majoration,
                    complement: reservationData.complement,
                    comment_facture: reservationData.comment_facture,
                    reservation: reservationData.id
                },
                submission() {
                    @this.call('editReservation', this.formData)
                }
            }
        }
    </script>
@endpush