<script>
    @if(session()->exists('success'))
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '',
            confirmButtonColor: '#385670',
        })
    @endif
    @if(session()->exists('errors'))
        Swal.fire({
            icon: 'warning',
            title: 'Algo salió, intentelo mas tarde',
            html: '{!! implode('', $errors->all('<div>:message</div><br/>')) !!}',
            confirmButtonColor: '#385670',
        })
    @endif

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function removeItem(route){
        Swal.fire({
        icon: 'info',
        title: 'Advertencia',
        text: '¿Estás seguro de eliminar el registro?',
        showCancelButton: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: '#385670'
        }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "DELETE",
                url: route,
            }).done(function( msg ) {
                location.reload();
            }).fail(function( msg){
                
            });
        }else if(result.isDenied) {

        }
        });
    }
    function loading(){
        Swal.fire({
        title: 'Cargando!',
        html: 'Por favor espere',
        timerProgressBar: true,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        },
        willClose: () => {
        }
        }).then((result) => {
        })
    }
</script>