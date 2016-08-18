<?php

namespace Modulos\Academico\Http\Controllers;

use Modulos\Academico\Repositories\CentroRepository;
use Modulos\Academico\Repositories\DepartamentoRepository;
use Modulos\Academico\Repositories\ProfessorRepository;
use Modulos\Seguranca\Providers\ActionButton\Facades\ActionButton;
use Modulos\Seguranca\Providers\ActionButton\TButton;
use Modulos\Core\Http\Controller\BaseController;
use Modulos\Academico\Http\Requests\DepartamentoRequest;
use Illuminate\Http\Request;

class DepartamentosController extends BaseController
{
    protected $departamentoRepository;
    protected $professorRepository;
    protected $centroRepository;

    public function __construct(DepartamentoRepository $departamento, ProfessorRepository $professor, CentroRepository $centro)
    {
        $this->departamentoRepository = $departamento;
        $this->professorRepository = $professor;
        $this->centroRepository = $centro;
    }

    public function getIndex(Request $request)
    {
        $btnNovo = new TButton();
        $btnNovo->setName('Novo')->setAction('/academico/departamentos/create')->setIcon('fa fa-plus')->setStyle('btn bg-olive');

        $actionButtons[] = $btnNovo;

        $tableData = $this->departamentoRepository->paginateRequest($request->all());

        $tabela = $tableData->columns(array(
            'dep_id' => '#',
            'dep_nome' => 'Departamento',
            'dep_action' => 'Ações'
        ))
            ->modifyCell('dep_action', function () {
                return array('style' => 'width: 140px;');
            })
            ->means('dep_action', 'dep_id')
            ->modify('dep_action', function ($id) {
                return ActionButton::grid([
                    'type' => 'SELECT',
                    'config' => [
                        'classButton' => 'btn-default',
                        'label' => 'Selecione'
                    ],
                    'buttons' => [
                        [
                            'classButton' => '',
                            'icon' => 'fa fa-pencil',
                            'action' => '/academico/departamentos/edit/' . $id,
                            'label' => 'Editar',
                            'method' => 'get'
                        ],
                        [
                            'classButton' => 'btn-delete text-red',
                            'icon' => 'fa fa-trash',
                            'action' => '/academico/departamentos/delete',
                            'id' => $id,
                            'label' => 'Excluir',
                            'method' => 'post'
                        ]
                    ]
                ]);
            })
            ->sortable(array('dep_id', 'dep_nome'));

        $paginacao = $tableData->appends($request->except('page'));

        return view('Academico::departamentos.index', ['tabela' => $tabela, 'paginacao' => $paginacao, 'actionButton' => $actionButtons]);
    }

    public function getCreate()
    {
        return view('Academico::departamentos.create');
    }

    public function postCreate(DepartamentoRequest $request)
    {
        try {
            $departamento = $this->departamentoRepository->create($request->all());

            if (!$departamento) {
                flash()->error('Erro ao tentar salvar.');

                return redirect()->back()->withInput($request->all());
            }

            flash()->success('Departamento criado com sucesso.');

            return redirect('/academico/departamentos');
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            } else {
                flash()->success('Erro ao tentar salvar. Caso o problema persista, entre em contato com o suporte.');

                return redirect()->back();
            }
        }
    }

    public function getEdit($moduloId)
    {
        $modulo = $this->departamentoRepository->find($moduloId);

        if (!$modulo) {
            flash()->error('Módulo não existe.');

            return redirect()->back();
        }

        return view('Seguranca::modulos.edit', compact('modulo'));
    }

    public function putEdit($id, ModuloRequest $request)
    {
        try {
            $modulo = $this->departamentoRepository->find($id);

            if (!$modulo) {
                flash()->error('Módulo não existe.');

                return redirect('/seguranca/modulos');
            }

            $requestData = $request->only($this->departamentoRepository->getFillableModelFields());

            if (!$this->departamentoRepository->update($requestData, $modulo->mod_id, 'mod_id')) {
                flash()->error('Erro ao tentar salvar.');

                return redirect()->back()->withInput($request->all());
            }

            flash()->success('Módulo atualizado com sucesso.');

            return redirect('/seguranca/modulos');
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            } else {
                flash()->success('Erro ao tentar salvar. Caso o problema persista, entre em contato com o suporte.');

                return redirect()->back();
            }
        }
    }

    public function postDelete(Request $request)
    {
        try {
            $moduloId = $request->get('id');

            if ($this->departamentoRepository->delete($moduloId)) {
                flash()->success('Módulo excluído com sucesso.');
            } else {
                flash()->error('Erro ao tentar excluir o módulo');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            } else {
                flash()->success('Erro ao tentar salvar. Caso o problema persista, entre em contato com o suporte.');

                return redirect()->back();
            }
        }
    }
}
