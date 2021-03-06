<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Disciplinas Ofertadas</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                @if($ofertas->count())
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Disciplina</th>
                            <th width="10%">Carga Horária</th>
                            <th width="5%">Créditos</th>
                            <th width="12%">Tipo de Avaliação</th>
                            <th width="12%">Tipo de Disciplina</th>
                            <th width="5%">Vagas</th>
                            <th>Professor</th>
                            <th width="10%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ofertas as $oferta)
                            <tr>
                                <td>{{ $oferta->moduloDisciplina->disciplina->dis_nome }}</td>
                                <td>{{ $oferta->moduloDisciplina->disciplina->dis_carga_horaria }} horas</td>
                                <td>{{ $oferta->moduloDisciplina->disciplina->dis_creditos }}</td>
                                <td>{{ $oferta->ofd_tipo_avaliacao }}</td>
                                <td>{{ $oferta->moduloDisciplina->mdc_tipo_disciplina }}</td>
                                <td>{{ $oferta->ofd_quant_matriculados }}/<strong>{{ $oferta->ofd_qtd_vagas }}</strong></td>
                                <td>{{ $oferta->professor->pessoa->pes_nome }}</td>
                                <td>
                                    {!! ActionButton::grid([
                                                    'type' => 'SELECT',
                                                    'config' => [
                                                        'classButton' => 'btn-default',
                                                        'label' => 'Selecione'
                                                    ],
                                                    'buttons' => [
                                                        [
                                                            'classButton' => '',
                                                            'icon' => 'fa fa-pencil',
                                                            'route' => 'academico.ofertasdisciplinas.edit',
                                                            'parameters' => ['id' => $oferta->ofd_id],
                                                            'label' => 'Editar',
                                                            'method' => 'get'
                                                        ],
                                                        [
                                                            'classButton' => 'btn-delete text-red',
                                                            'icon' => 'fa fa-trash',
                                                            'route' => 'academico.ofertasdisciplinas.delete',
                                                            'id' => $oferta->ofd_id,
                                                            'label' => 'Excluir',
                                                            'method' => 'post'
                                                        ]
                                                    ]
                                                ]);
                                    !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <p>Não há disciplinas ofertadas para o período requerido</p>
                @endif
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>