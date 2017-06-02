<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Artisan;
use Modulos\Integracao\Repositories\MapeamentoNotasRepository;

class MapeamentoNotasRepositoryTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    protected $repo;

    protected $configuracoesCurso = [
        "media_min_aprovacao" => "7.0",
        "media_min_final" => "5.0",
        "media_min_aprovacao_final" => "5.0",
        "modo_recuperacao" => "substituir_media_final",
        "conceitos_aprovacao" => '["Bom","Muito Bom","Excelente"]',
    ];

    public function createApplication()
    {
        putenv('DB_CONNECTION=sqlite_testing');

        $app = require __DIR__ . '/../../../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        Artisan::call('modulos:migrate');

        $this->repo = $this->app->make(MapeamentoNotasRepository::class);
    }

    public function testAllWithEmptyDatabase()
    {
        $response = $this->repo->all();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals(0, $response->count());
    }

    public function testIfAlunoAprovadoConceito()
    {
        $data = [
            'mof_conceito' => 'Bom'
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso, 'conceitual');

        $this->assertEquals($result['mof_situacao_matricula'], 'aprovado_media');
    }

    public function testIfAlunoReprovadoConceito()
    {
        $data = [
            'mof_conceito' => 'Insuficiente'
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso, 'conceitual');

        $this->assertEquals($result['mof_situacao_matricula'], 'reprovado_media');
    }

    public function testIfAlunoAprovadoMedia()
    {
        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 7.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 7.0);
        $this->assertEquals($result['mof_situacao_matricula'], 'aprovado_media');
    }

    public function testIfAlunoReprovadoMedia()
    {
        $data = [
            'mof_nota1' => 4.0,
            'mof_nota2' => 5.0,
            'mof_nota3' => 3.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 4.0);
        $this->assertEquals($result['mof_situacao_matricula'], 'reprovado_media');
    }

    public function testIfAlunoIsAprovadoFinalWithoutRecuperacaoSubstituirMedia()
    {
        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 6.0,
            'mof_final' => 6.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 6.4);
        $this->assertEquals($result['mof_situacao_matricula'], 'aprovado_final');
    }

    public function testIfAlunoIsReprovadoFinalWithoutRecuperacaoSubstituirMedia()
    {
        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 6.0,
            'mof_final' => 3.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 4.9);
        $this->assertEquals($result['mof_situacao_matricula'], 'reprovado_final');
    }

    /* Testes com Modo de Recuperação Substituir Media Final */

    public function testIfAlunoAprovadoMediaWithRecuperacaoSubstituirMedia()
    {
        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 6.0,
            'mof_recuperacao' => 7.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 7.0);
        $this->assertEquals($result['mof_situacao_matricula'], 'aprovado_media');
    }

    public function testIfAlunoReprovadoMediaWithRecuperacaoSubstituirMedia()
    {
        $data = [
            'mof_nota1' => 5.0,
            'mof_nota2' => 5.0,
            'mof_nota3' => 4.0,
            'mof_recuperacao' => 4.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 4.7);
        $this->assertEquals($result['mof_situacao_matricula'], 'reprovado_media');
    }

    public function testIfAlunoIsAprovadoFinalWithRecuperacaoSubstituirMedia()
    {
        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 6.0,
            'mof_recuperacao' => 5.0,
            'mof_final' => 5.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 5.9);
        $this->assertEquals($result['mof_situacao_matricula'], 'aprovado_final');
    }

    public function testIfAlunoIsReprovadoFinalWithRecuperacaoSubstituirMedia()
    {
        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 6.0,
            'mof_recuperacao' => 5.0,
            'mof_final' => 3.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 4.9);
        $this->assertEquals($result['mof_situacao_matricula'], 'reprovado_final');
    }

    /* Testes com Modo de Recuperação Substituir Menor Nota */

    public function testIfAlunoAprovadoMediaWithRecuperacaoSubstituirMenorNota()
    {
        $this->configuracoesCurso['modo_recuperacao'] = 'substituir_menor_nota';

        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 6.0,
            'mof_recuperacao' => 7.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 7.0);
        $this->assertEquals($result['mof_situacao_matricula'], 'aprovado_media');
    }

    public function testIfAlunoReprovadoMediaWithRecuperacaoSubstituirMenorNota()
    {
        $this->configuracoesCurso['modo_recuperacao'] = 'substituir_menor_nota';

        $data = [
            'mof_nota1' => 5.0,
            'mof_nota2' => 5.0,
            'mof_nota3' => 4.0,
            'mof_recuperacao' => 6.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 5.3);
        $this->assertEquals($result['mof_situacao_matricula'], 'reprovado_media');
    }

    public function testIfAlunoIsAprovadoFinalWithRecuperacaoSubstituirMenorNota()
    {
        $this->configuracoesCurso['modo_recuperacao'] = 'substituir_menor_nota';

        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 5.0,
            'mof_recuperacao' => 6.0,
            'mof_final' => 5.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 5.9);
        $this->assertEquals($result['mof_situacao_matricula'], 'aprovado_final');
    }

    public function testIfAlunoIsReprovadoFinalWithRecuperacaoSubstituirMenorNota()
    {
        $this->configuracoesCurso['modo_recuperacao'] = 'substituir_menor_nota';

        $data = [
            'mof_nota1' => 7.0,
            'mof_nota2' => 7.0,
            'mof_nota3' => 5.0,
            'mof_recuperacao' => 6.0,
            'mof_final' => 3.0
        ];

        $result = $this->repo->calcularMedia($data, $this->configuracoesCurso);

        $this->assertEquals($result['mof_mediafinal'], 4.9);
        $this->assertEquals($result['mof_situacao_matricula'], 'reprovado_final');
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }
}
