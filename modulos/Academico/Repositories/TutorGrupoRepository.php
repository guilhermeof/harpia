<?php

namespace Modulos\Academico\Repositories;

use Modulos\Core\Repository\BaseRepository;
use Modulos\Academico\Models\TutorGrupo;
use DB;

class TutorGrupoRepository extends BaseRepository
{
    public function __construct(TutorGrupo $tutorgrupo)
    {
        $this->model = $tutorgrupo;
    }

    public function paginateRequestByGrupo($grupoid, array $requestParameters = null)
    {
        $sort = array();
        if (!empty($requestParameters['field']) and !empty($requestParameters['sort'])) {
            $sort = [
                'field' => $requestParameters['field'],
                'sort' => $requestParameters['sort']
            ];
            return $this->model->where('ttg_grp_id', '=', $grupoid)->where('ttg_data_fim', '=', null)
                ->orderBy($sort['field'], $sort['sort'])
                ->paginate(15);
        }
        return $this->model->where('ttg_grp_id', '=', $grupoid)->where('ttg_data_fim', '=', null)->paginate(15);
    }

    public function verifyTutorExists($grupoId)
    {
        $result_presencial = $this->model
            ->where('ttg_tipo_tutoria', 'presencial')
            ->where('ttg_grp_id', '=', $grupoId)
            ->get();

        $result_distancia = $this->model
            ->where('ttg_tipo_tutoria', 'distancia')
            ->where('ttg_grp_id', '=', $grupoId)
            ->get();

        if ($result_distancia->isEmpty() || $result_presencial->isEmpty()) {
            return true;
        }
    }

    public function getTiposTutoria($grupoId)
    {
        $distancia = $this->model
            ->where('ttg_tipo_tutoria', 'distancia')
            ->where('ttg_data_fim', null)
            ->where('ttg_grp_id', $grupoId)
            ->count();
        $presencial = $this->model
            ->where('ttg_tipo_tutoria', 'presencial')
            ->where('ttg_grp_id', $grupoId)
            ->where('ttg_data_fim', null)->count();
        $returnArray = [];

        if ($distancia<2) {
            $returnArray['distancia'] = 'A Distancia';
        }

        if ($presencial<2) {
            $returnArray['presencial'] = 'Presencial';
        }

        return $returnArray;
    }


    public function getTipoTutoria($tutorId, $grupoId)
    {
        $result = $this->model
            ->where('ttg_grp_id', '=', $grupoId)
            ->where('ttg_tut_id', '=', $tutorId)
            ->pluck('ttg_tipo_tutoria')->toArray();

        return array_pop($result);
    }
}
