<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-content">
    <div class="content-inner">
        <div style="padding:20px 24px;background:#fff;border-bottom:1px solid #dee2e6;">
            <h2 style="margin:0;font-size:20px;">Gestionar Partners</h2>
        </div>
        <section id="partners-section">
            <div class="container-fluid" style="padding:20px;">

                <!-- FORM AGREGAR PARTNER -->
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-body">
                        <h5 class="card-title">Agregar Nuevo Partner</h5>
                        <form id="formAddPartner">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Nombre</label>
                                    <input type="text" id="partner-name" class="form-control" placeholder="Apellido, Nombre" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>DNI</label>
                                    <input type="text" id="partner-dni" class="form-control" placeholder="12345678" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Género</label>
                                    <select id="partner-gender" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="M">Caballero (M)</option>
                                        <option value="F">Dama (F)</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Email (opcional)</label>
                                    <input type="email" id="partner-email" class="form-control" placeholder="correo@example.com">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" id="btn-add-partner">
                                <i class="fas fa-plus"></i> Agregar Partner
                            </button>
                        </form>
                    </div>
                </div>

                <!-- BÚSQUEDA -->
                <div class="admin-filtros-row" style="margin-bottom:20px;">
                    <div class="admin-filtro-buscar">
                        <input type="text" id="buscar-partner" class="form-control" placeholder="Buscar por nombre o DNI...">
                    </div>
                    <div class="admin-filtro-gen">
                        <select id="filtro-gender-partners" class="form-control">
                            <option value="">Todos</option>
                            <option value="M">Caballeros</option>
                            <option value="F">Damas</option>
                        </select>
                    </div>
                    <div class="admin-filtro-contador">
                        <span class="text-muted small" id="contador-partners"></span>
                    </div>
                </div>

                <!-- TABLA PARTNERS -->
                <table class="table table-hover table-sm" id="partnersTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Email</th>
                            <th>Género</th>
                            <th width="80"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($partners)): ?>
                            <?php foreach($partners as $p): ?>
                            <tr class="partner-row"
                                data-nombre="<?=strtolower($p->name)?>"
                                data-dni="<?=$p->dni?>"
                                data-gender="<?=$p->gender?>">
                                <td style="text-transform:capitalize;"><?=strtolower($p->name)?></td>
                                <td><?=$p->dni?></td>
                                <td><?=$p->email?></td>
                                <td><?=$p->gender == 'M' ? '<span class="badge badge-primary">M</span>' : '<span class="badge badge-danger">F</span>'?></td>
                                <td nowrap>
                                    <button class="btn btn-xs btn-warning btn-editar-partner"
                                        data-id="<?=$p->id?>"
                                        data-name="<?=$p->name?>"
                                        data-dni="<?=$p->dni?>"
                                        data-email="<?=$p->email?>"
                                        data-gender="<?=$p->gender?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-xs btn-danger btn-eliminar-partner" data-id="<?=$p->id?>" data-name="<?=strtolower($p->name)?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay partners cargados.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<!-- MODAL EDITAR PARTNER -->
<div class="modal fade" id="modalEditPartner" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Partner</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="formEditPartner">
                    <input type="hidden" id="edit-partner-id">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" id="edit-partner-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" id="edit-partner-dni" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Género</label>
                        <select id="edit-partner-gender" class="form-control" required>
                            <option value="M">Caballero (M)</option>
                            <option value="F">Dama (F)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="edit-partner-email" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-save-partner">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
const adminurl = '<?=base_url('admin')?>';
const token = '<?=$token?>';

$(function() {
    // Agregar partner
    $('#formAddPartner').on('submit', function(e) {
        e.preventDefault();
        var name = $('#partner-name').val().trim();
        var dni = $('#partner-dni').val().trim();
        var gender = $('#partner-gender').val();
        var email = $('#partner-email').val().trim();

        if (!name || !dni || !gender) {
            alert('Completa los campos requeridos');
            return false;
        }

        $('#btn-add-partner').prop('disabled', true).addClass('loading');

        $.ajax({
            url: adminurl + '/addPartnerQuick',
            type: 'POST',
            data: {
                name: name,
                dni: dni,
                gender: gender,
                email: email
            },
            headers: {'X-Auth-Token': token},
            success: function(res) {
                if(res.action) {
                    alert('Partner agregado correctamente');
                    $('#formAddPartner')[0].reset();
                    location.reload();
                } else {
                    alert('Error: ' + (res.msg || 'Error desconocido'));
                }
            },
            error: function() {
                alert('Error al agregar partner');
            },
            complete: function() {
                $('#btn-add-partner').prop('disabled', false).removeClass('loading');
            }
        });
    });

    // Editar partner
    $(document).on('click', '.btn-editar-partner', function() {
        var d = $(this).data();
        $('#edit-partner-id').val(d.id);
        $('#edit-partner-name').val(d.name);
        $('#edit-partner-dni').val(d.dni);
        $('#edit-partner-gender').val(d.gender);
        $('#edit-partner-email').val(d.email);
        $('#modalEditPartner').modal('show');
    });

    // Guardar cambios
    $('#btn-save-partner').on('click', function() {
        var id = $('#edit-partner-id').val();
        var name = $('#edit-partner-name').val().trim();
        var dni = $('#edit-partner-dni').val().trim();
        var gender = $('#edit-partner-gender').val();
        var email = $('#edit-partner-email').val().trim();

        if (!name || !dni || !gender) {
            alert('Completa los campos requeridos');
            return;
        }

        $(this).prop('disabled', true).addClass('loading');

        $.ajax({
            url: adminurl + '/editPartner',
            type: 'POST',
            data: {
                id: id,
                name: name,
                dni: dni,
                gender: gender,
                email: email
            },
            headers: {'X-Auth-Token': token},
            success: function(res) {
                if(res.action) {
                    alert('Partner actualizado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (res.msg || 'Error desconocido'));
                }
            },
            error: function() {
                alert('Error al actualizar partner');
            },
            complete: function() {
                $('#btn-save-partner').prop('disabled', false).removeClass('loading');
            }
        });
    });

    // Eliminar partner
    $(document).on('click', '.btn-eliminar-partner', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        if (!confirm('¿Eliminar a ' + name + '?')) return;

        $.ajax({
            url: adminurl + '/deletePartner',
            type: 'POST',
            data: { id: id },
            headers: {'X-Auth-Token': token},
            success: function(res) {
                if(res.action) location.reload();
                else alert('Error al eliminar');
            }
        });
    });

    // Inicializar tabla
    var partnersTable = $('#partnersTable').DataTable({
        dom: 'ltp',
        order: [[ 0, "asc" ]],
        pageLength: -1
    });

    // Filtrado
    function filtrar() {
        var buscar = $('#buscar-partner').val().toLowerCase();
        var gender = $('#filtro-gender-partners').val();

        partnersTable.columns(0).search('').columns(1).search('').columns(2).search('').columns(3).search('').draw();

        var visible = 0;
        partnersTable.rows().every(function() {
            var data = this.data();
            var nombre = $(data[0]).text().toLowerCase();
            var dni = $(data[1]).text();
            var gen = this.node().getAttribute('data-gender');

            var ok = (!buscar || nombre.indexOf(buscar) >= 0 || dni.indexOf(buscar) >= 0) &&
                     (!gender || gen === gender);

            if(ok) {
                visible++;
                $(this.node()).show();
            } else {
                $(this.node()).hide();
            }
        });

        $('#contador-partners').text(visible + ' partner' + (visible !== 1 ? 's' : ''));
    }

    $('#buscar-partner, #filtro-gender-partners').on('input change', filtrar);
    filtrar();
});
</script>

<style>
.partner-row:hover {
    background-color: #f9f9f9;
}
</style>
